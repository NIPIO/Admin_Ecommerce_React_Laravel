<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Compras;
use App\Models\ComprasDetalle;
use App\Models\CtaCte;
use App\Models\Productos;
use App\Models\Proveedores;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComprasController extends Controller
{
    private $movimientosController;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
    }

    public function index() {
        $proveedor = request()->get('proveedor');
        $producto = request()->get('producto');

        $compras = Compras::orderBy('id', 'DESC')->with(['proveedor', 'producto']);

        if ($proveedor) {
            $compras->where('proveedor_id', (int) $proveedor);
        }
        if ($producto) {
            $compras->whereHas('detalleCompra', function($innerQuery) use ($producto) {
                $innerQuery->where('producto_id', (int) $producto);
            });
        }
        
        return response()->json(['error' => false, 'allCompras' => Compras::all(), 'comprasFiltro' => $compras->get()]);
    }

    
    public function nuevaCompra(Request $request) {
        $req = $request->all();
        DB::beginTransaction();
        try {

            $compra = Compras::create([
                'proveedor_id' => $req['proveedor'],
                'cantidad' => array_sum(array_column($req['productos'], 'cantidad')),
                'precio_total' => 0,
                'activo' => 1,
                'fecha_compra' => Carbon::now()->format('Y-m-d'),
            ]);
            
            DB::commit();
            $totalPrecioCompra = 0;
            foreach ($req['productos'] as $compraDetalleRow) {

                ComprasDetalle::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $compraDetalleRow['producto'],
                    'cantidad' => $compraDetalleRow['cantidad'],
                    'precio' => $compraDetalleRow['precioUnitario']
                ]);

                //el total es para sumar cantidad producto por precio prodcuto. Al final cuando grabo en compra sumo todo de todods. Despues elimino este campo no lo preciso
                $totalPrecioCompra += $compraDetalleRow['cantidad'] * $compraDetalleRow['precioUnitario'];

                //pongo en stock en transito las nuevas compras
                $productoAEditar = Productos::whereId($compraDetalleRow['producto'])->first()->toArray();

                $productoAEditar = Productos::whereId($compraDetalleRow['producto'])->update([
                        "en_transito" => $productoAEditar['en_transito'] + $compraDetalleRow['cantidad']
                ]);;
                
                DB::commit();

            }

            Compras::whereId($compra->id)->update([
                "precio_total" => $totalPrecioCompra,
            ]);

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['status' => 200]);
    }


    public function getCompra (int $id) {
        return response()->json(['error' => false, 'compra' => Compras::whereId($id)->with(['detalleCompra', 'detalleCompra.producto'])->get()->toArray()]);
    }

    public function confirmarCompra (Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        DB::beginTransaction();
        try {
            //El saldo abonado en la compra.
            $compra = Compras::whereId($req['id']);
            $proveedor = CtaCte::where('proveedor_id', $compra->first()->proveedor_id)->first();
            
            if (is_null($proveedor)) {
                return response()->json(['error' => true, 'data' => 'Corrobore que el proveedor tenga una cuenta corriente abierta']);
            }

            $compra->update([
                'precio_abonado' => $req['pago'],
                'confirmada' => true,
            ]);
            DB::commit();

            //Actualizo la cuenta corriente con el proveedor
            $compra = $compra->first();
            $proveedor = CtaCte::where('proveedor_id', $compra->proveedor_id)->first();

            $saldoProveedor = $proveedor->saldo;
            CtaCte::where('proveedor_id', $compra->first()->proveedor_id)->update([
                'saldo' => $saldoProveedor + $req['diferencia']
            ]);

            DB::commit();

            //grabo el ingreso en la caja
            Caja::create([
                'tipo_movimiento' => 'COMPRA',
                'item_id' => $req['id'],
                'importe' => - $req['pago'],
                'usuario' => $usuario
            ]);
            
            //Por ultimo paso el stock de la compra en tranisto a stock
            $compraDetalle = ComprasDetalle::whereCompraId($req['id'])->get()->toArray();

            foreach ($compraDetalle as $value) {
                //Obtengo los productos y actualizo su stock
                $producto = Productos::whereId($value['producto_id'])->first();

                Productos::whereId($value['producto_id'])->update([
                    'en_transito' => $producto->en_transito - $value['cantidad'],
                    'stock' => $producto->stock + $value['cantidad']
                ]);
                
                DB::commit();
            }

            $this->movimientosController->guardarMovimiento(
                'compras', 'CONFIRMACION', $usuario, $compra->id, null, null, $req['diferencia']
            );

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }
    
    public function actualizarStock($compra, $quiereInactivar) {

        $productosDeLaCompra = ComprasDetalle::whereCompraId($compra->id)->get()->toArray();

        foreach ($productosDeLaCompra as $productoCompra) {
            $producto = Productos::whereId($productoCompra['producto_id']);
            $quiereInactivar ? $producto->decrement('en_transito', $productoCompra['cantidad']) : $producto->increment('en_transito', $productoCompra['cantidad']);
        }
    }
}
