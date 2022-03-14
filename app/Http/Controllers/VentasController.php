<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CtaCte;
use App\Models\Productos;
use App\Models\Vendedores;
use App\Models\Ventas;
use App\Models\VentasDetalle;
use App\Repositories\IndexRepository;
use App\Repositories\MovimientosRepository;
use App\Repositories\ProductosRepository;
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

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository, VentasRepository $ventasRepository, VentasDetalleRepository $ventasDetalleRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
        $this->ventasRepository = $ventasRepository;    
        $this->ventasDetalleRepository = $ventasDetalleRepository;    
    }

    public function index() {
        $req = request()->all(); 
        $ventas = $this->indexRepository->indexVentas($req);
        return response()->json(['error' => false, 'allVentas' => Ventas::all(), 'ventasFiltro' => $ventas->get()]);
    }

    public function nuevaVenta(Request $request, ProductosRepository $productosRepository) {
        $req = $request->all();

        try {
            DB::beginTransaction();

            $venta = $this->ventasRepository->setVenta($req);

            $totalPrecioVenta = 0;
            $totalCosto = 0;
            foreach ($req['filas'] as $ventaDetalleRow) {
                // 1- Incremento el precio final.
                $totalPrecioVenta += $ventaDetalleRow['cantidad'] * $ventaDetalleRow['precioUnitario'];
                $totalCosto += $ventaDetalleRow['cantidad'] * $productosRepository->getProducto($ventaDetalleRow)['costo'];
                // 2-Chequeo disponibilidad.
                $productosRepository->chequearDisponibilidadStock($ventaDetalleRow);
                // 3- Incremento el stock reservado.
                $productosRepository->incrementar($ventaDetalleRow, 'stock_reservado');
                // 4- Guardo cada row de la venta
                $this->ventasDetalleRepository->setVentaDetalle($venta, $ventaDetalleRow);
            }

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

            //El saldo abonado en la compra.
            $venta = Ventas::whereId($req['id']);
            $cliente = CtaCte::where('proveedor_id', $venta->first()->proveedor_id)->first();

            if ($req['diferencia'] <> 0) {
                if (is_null($cliente)) {
                    return response()->json(['error' => true, 'data' => 'Corrobore que el cliente tenga una cuenta corriente abierta']);
                } else {
                    //Actualizo la cuenta corriente con el proveedor
                    $cliente = CtaCte::where('proveedor_id', $venta->first()->proveedor_id)->first();
    
                    $saldoProveedor = $cliente->saldo;
                    CtaCte::where('proveedor_id', $venta->first()->proveedor_id)->update([
                        'saldo' => $saldoProveedor + $req['diferencia']
                    ]);
                }
            }

            $venta->update([
                'costo' => $req['pago'],
                'confirmada' => true,
            ]);

            //grabo el egreso en la caja
            Caja::create([
                'tipo_movimiento' => 'VENTA',
                'item_id' => $req['id'],
                'importe' => $req['pago'],
                'usuario' => $usuario
            ]);

            //Por ultimo paso el stock de la compra en tranisto a stock
            $ventaDetalle = VentasDetalle::whereVentaId($req['id'])->get()->toArray();

            foreach ($ventaDetalle as $value) {
                //Obtengo los productos y actualizo su stock
                $prod = Productos::whereId($value['producto_id']);
                $prod->decrement('stock_reservado', $value['cantidad']);
                $prod->decrement('stock', $value['cantidad']);
            }
            
            //Grabo la comision del vendedor
            Vendedores::whereId($venta->first()->vendedor_id)->increment('comision', $venta->first()->toArray()['vendedor_comision']);
            
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
            // $req['usuario']
            
            $totalPrecioVenta = 0;
            $cantidad = 0;
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

                if (isset($ventaDetalle['venta_id'])) {
                    //Fila de la compra inicial entonces reemplazo campos quizá editó producto, precio o cantidad
                    $cargarRow = VentasDetalle::whereId($ventaDetalle['id']);

                    $productoAnterior = $cargarRow->first()->toArray();

                    $cargarRow->update([
                        'producto_id' => $ventaDetalle['producto_id'],
                        'precio' => $ventaDetalle['precio'],
                        'cantidad' => $ventaDetalle['cantidad'],
                    ]);

                    Productos::whereId($ventaDetalle['producto_id'])->decrement('stock_reservado', $productoAnterior['cantidad']);
                    Productos::whereId($ventaDetalle['producto_id'])->increment('stock_reservado', $ventaDetalle['cantidad']);
                
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
                $cantidad += $ventaDetalle['cantidad'];
            }

            Ventas::whereId($req['id'])->update([
                'cliente_id' => $req['cliente'],
                'vendedor_id' => $req['vendedor'],
                'precio_total' => $totalPrecioVenta,
                'cantidad' => $cantidad,
                'vendedor_comision' => $totalPrecioVenta * 0.01
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
