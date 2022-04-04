<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CtaCte;
use App\Models\Productos;
use App\Models\Vendedores;
use App\Models\Ventas;
use App\Models\VentasDetalle;
use App\Repositories\CajaRepository;
use App\Repositories\CuentasRepository;
use App\Repositories\IndexRepository;
use App\Repositories\MovimientosRepository;
use App\Repositories\ProductosRepository;
use App\Repositories\VendedoresRepository;
use App\Repositories\VentasDetalleRepository;
use App\Repositories\VentasRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class VentasController extends Controller
{
    private $ventasRepository;
    private $ventasDetalleRepository;
    private $movimientosRepository;
    private $indexRepository;
    private $cuentaRepository;
    private $cajaRepository;
    private $productosRepository;

    public function __construct(ProductosRepository $productosRepository, IndexRepository $indexRepository, CuentasRepository $cuentaRepository, CajaRepository $cajaRepository, MovimientosRepository $movimientosRepository, VentasRepository $ventasRepository, VentasDetalleRepository $ventasDetalleRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
        $this->ventasRepository = $ventasRepository;    
        $this->ventasDetalleRepository = $ventasDetalleRepository;    
        $this->cuentaRepository = $cuentaRepository;    
        $this->cajaRepository = $cajaRepository;    
        $this->productosRepository = $productosRepository;    
    }

    public function index() {
        $req = request()->all(); 
        $ventas = $this->indexRepository->indexVentas($req);
        return response()->json(['error' => false, 'allVentas' => Ventas::all(), 'ventasFiltro' => $ventas->get()]);
    }

    public function nuevaVenta(Request $request) {
        $req = $request->all();

        try {
            DB::beginTransaction();

            // Creo la reserva con precio y costo 0.
            $venta = $this->ventasRepository->setVenta($req);

            $totalPrecioVenta = 0;
            $totalCosto = 0;
            // Recorro los items para grabar detalle de la reserva, costo total y precio final.
            foreach ($req['filas'] as $ventaDetalleRow) {
                // 1- Incremento el costo y precio final.
                $totalPrecioVenta += $ventaDetalleRow['cantidad'] * $ventaDetalleRow['precioUnitario'];
                $totalCosto += $ventaDetalleRow['cantidad'] * $this->productosRepository->getProducto($ventaDetalleRow)['costo'];
                // 2-Chequeo disponibilidad.
                $this->productosRepository->chequearDisponibilidadStock($ventaDetalleRow, false);
                // 3- Incremento el stock reservado.
                $this->productosRepository->incrementar($ventaDetalleRow, 'stock_reservado');
                // 4- Guardo cada row de la reserva
                $this->ventasDetalleRepository->setVentaDetalle($venta, $ventaDetalleRow);
            }

            // Actualizo la reserva.
            $this->ventasRepository->updatePrecioVenta($venta, $totalPrecioVenta, $totalCosto);

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }

    public function confirmarVenta (Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            DB::beginTransaction();

            //Obtengo la reserva
            $venta = $this->ventasRepository->getVenta($req['id']);
            $compradorId = $venta->first()->proveedor_id;

            if ($req['diferencia'] <> 0) {
                // Si tiene diferencia la compra, actualizo el estado de la cuenta corriente.
                $compradorCtaCte = $this->cuentaRepository->getCuenta($compradorId);

                if (is_null($compradorCtaCte)) {
                    return response()->json(['error' => true, 'data' => 'Corrobore que el cliente tenga una cuenta corriente abierta']);
                } else {
                    //Actualizo la cuenta corriente con el proveedor
                    $this->cuentaRepository->updateSaldoCuenta($compradorCtaCte, $req['diferencia'], $req['diferencia'] < 0 ? 'increment' : 'decrement' );
                }
            }
            $this->ventasRepository->confirmarVenta($venta);

            //Grabo la comision del vendedor
            VendedoresRepository::agregarComision($venta->toArray());

            //grabo el ingreso en la caja
            $this->cajaRepository->setCaja($usuario, [
                'tipoMovimiento' => 'VENTA',
                'tipoCaja' => $req['tipoCaja'],
                'item_id' => $req['id'],
                'importe' => $req['pago'],
            ]);

            //Por ultimo resto de reservado y del stock los productos vendidos.
            $ventaDetalle = $this->ventasDetalleRepository->getVentaDetalleByVentaId($req['id']);

            foreach ($ventaDetalle as $value) {
                $prod = Productos::whereId($value['producto_id']);
                $prod->decrement('stock_reservado', $value['cantidad']);
                $prod->decrement('stock', $value['cantidad']);
            }

            
            $this->movimientosRepository->guardarMovimiento(
                'ventas', 'CONFIRMACION', $usuario, $req['id'], null, null, $req['diferencia'], null
            );

            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }

    public function editarVenta(Request $request) {
        $req = $request->all();

        try {
            DB::beginTransaction();
            
            $totalPrecioVenta = 0;
            $cantidad = 0;
            $totalCosto = 0;

            foreach ($req['filas'] as $fila) {
                $ventaDetalle = null;
                // La fila puede venir de 2 formas: 1, el producto como array (no fue editado esa fila), 2 el producto como int (fue editado y elegido otro)
                // Formateo entonces los datos para trabajarlos.
                if (isset($fila['id'])) {
                    $ventaDetalle = $fila;
                } else {
                    $productoDetalle = Productos::whereId($fila['producto'])->first()->toArray();
                    $fila['producto'] = $productoDetalle;
                    $ventaDetalle = $fila;
                }
                // Chequeo disponibilidad stock
                $this->productosRepository->chequearDisponibilidadStock($ventaDetalle, true);

                if (isset($ventaDetalle['venta_id'])) {
                    //Fila de la compra inicial entonces reemplazo campos quizá editó producto, precio o cantidad
                    $cargarRow = VentasDetalle::whereId($ventaDetalle['id']);

                    $productoAnterior = $cargarRow->first()->toArray();

                    $cargarRow->update([
                        'producto_id' => $ventaDetalle['producto']['id'],
                        'precio' => $ventaDetalle['precio'],
                        'cantidad' => $ventaDetalle['cantidad'],
                    ]);

                    Productos::whereId($ventaDetalle['producto']['id'])->decrement('stock_reservado', $productoAnterior['cantidad']);
                    Productos::whereId($ventaDetalle['producto']['id'])->increment('stock_reservado', $ventaDetalle['cantidad']);
                
                } else {
                    //Es una fila agregada por edición
                    VentasDetalle::create([
                        'venta_id' => $req['id'],
                        'producto_id' => $ventaDetalle['producto']['id'],
                        'precio' => $ventaDetalle['precio'],
                        'cantidad' => $ventaDetalle['cantidad'],
                    ]);

                    Productos::whereId($ventaDetalle['producto']['id'])->increment('stock_reservado', $ventaDetalle['cantidad']);
                }

                $totalPrecioVenta += $ventaDetalle['precio'] * $ventaDetalle['cantidad'];
                $totalCosto += $ventaDetalle['cantidad'] * $this->productosRepository->getProducto($ventaDetalle['producto']['id'])['costo'];
                $cantidad += $ventaDetalle['cantidad'];
            }

            Ventas::whereId($req['id'])->update([
                'cliente_id' => $req['cliente'],
                'vendedor_id' => $req['vendedor'],
                'precio_total' => $totalPrecioVenta,
                'tipo_venta' => $req['tipoVenta'],
                'cantidad' => $cantidad,
                'costo' => $totalCosto,
                'utilidad' => $totalPrecioVenta - $totalCosto,
                'vendedor_comision' => $req['tipoVenta'] === 'Minorista' ? $totalPrecioVenta * 0.01 : ($totalPrecioVenta - $totalCosto) * 0.1
            ]);

            $this->movimientosRepository->guardarMovimiento(
                'ventas', 'MODIFICACION', $req['usuario'], $req['id'], null, null, null, null
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
    }

    public function borrarVenta(int $id) {

        try {
            DB::beginTransaction();

            VentasDetalle::where('venta_id', $id)->delete();
            Ventas::find($id)->delete();
            
            DB::commit();
    
            return response()->json(['error' => false]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
    }

    public function getVenta (int $id) {
        return response()->json(['error' => false, 'venta' => Ventas::whereId($id)->with(['detalleVenta', 'detalleVenta.producto'])->get()->toArray()]);
    }
}
