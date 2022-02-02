<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CtaCte;
use App\Models\Productos;
use App\Models\Ventas;
use App\Models\VentasDetalle;
use App\Repositories\IndexRepository;
use App\Repositories\MovimientosRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class VentasController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
    }

    public function index() {
        $cliente = request()->get('cliente');
        $vendedor = request()->get('vendedor');
        $fechas = request()->get('fechas');
        $producto = request()->get('producto');

        $ventas = $this->indexRepository->indexVentas($cliente, $vendedor, $producto, $fechas);

        return response()->json(['error' => false, 'allVentas' => Ventas::all(), 'ventasFiltro' => $ventas->get()]);
    }

    public function nuevaVenta(Request $request) {
        $req = $request->all();

        try {
            DB::beginTransaction();

            $venta = Ventas::create([
                'cliente_id' => $req['cliente'],
                'vendedor_id' => $req['vendedor'],
                'cantidad' => array_sum(array_column($req['filas'], 'cantidad')),
                'precio_total' => 0,
                'activo' => 1,
                'fecha_venta' => Carbon::now()->format('Y-m-d'),
            ]);
            
            $totalPrecioVenta = 0;
            foreach ($req['filas'] as $ventaDetalleRow) {

                //el total es para sumar cantidad producto por precio prodcuto. Al final cuando grabo en compra sumo todo de todods. Despues elimino este campo no lo preciso
                $totalPrecioVenta += $ventaDetalleRow['cantidad'] * $ventaDetalleRow['precioUnitario'];

                //pongo en stock en transito las nuevas compras
                $productoAEditar = Productos::whereId($ventaDetalleRow['producto']);
                $prod = $productoAEditar->first()->toArray();
                if ($prod['stock'] - $prod['stock_reservado'] - (int) $ventaDetalleRow['cantidad'] < 0 ) {
                    return response()->json([
                        'error' => true, 
                        'data' => 'No hay stock suficiente para el ' . $prod['nombre'] . '. Stock disponible: ' . ($prod['stock'] - $prod['stock_reservado'])
                    ]); 
                }

                Productos::whereId($ventaDetalleRow['producto'])->increment('stock_reservado', $ventaDetalleRow['cantidad']);
      
                VentasDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $ventaDetalleRow['producto'],
                    'cantidad' => $ventaDetalleRow['cantidad'],
                    'precio' => $ventaDetalleRow['precioUnitario']
                ]);

            }

            Ventas::whereId($venta->id)->update([
                "precio_total" => $totalPrecioVenta,
                "vendedor_comision" => ($totalPrecioVenta * 0.01)
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['status' => 200]);
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
                'precio_abonado' => $req['pago'],
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

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
    }
    public function getVentasConfirmadas() {
        return Ventas::where('confirmada', true)->get()->count();
    }

    public function getVenta (int $id) {
        return response()->json(['error' => false, 'venta' => Ventas::whereId($id)->with(['detalleVenta', 'detalleVenta.producto'])->get()->toArray()]);
    }
}
