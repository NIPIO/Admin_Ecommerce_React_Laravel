<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Compras;
use App\Models\ComprasDetalle;
use App\Models\CtaCte;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Repositories\IndexRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\MovimientosRepository;

class ComprasController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
    }

    public function index() {
        $proveedor = request()->get('proveedor');
        $producto = request()->get('producto');

        $compras = $this->indexRepository->indexCompras($proveedor, $producto);

        return response()->json(['error' => false, 'allCompras' => Compras::all(), 'comprasFiltro' => $compras->get()]);
    }

    
    public function nuevaCompra(Request $request) {
        $req = $request->all();
        try {
            DB::beginTransaction();

            $compra = Compras::create([
                'proveedor_id' => $req['proveedor'],
                'cantidad' => array_sum(array_column($req['productos'], 'cantidad')),
                'precio_total' => 0,
                'activo' => 1,
            ]);
            
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
                Productos::whereId($compraDetalleRow['producto'])->increment('en_transito', $compraDetalleRow['cantidad']);
            }

            Compras::whereId($compra->id)->update([
                "precio_total" => $totalPrecioCompra,
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['status' => 200]);
    }

    public function confirmarCompra (Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        try {
            DB::beginTransaction();

            //El saldo abonado en la compra.
            $compra = Compras::whereId($req['id']);
            $proveedor = CtaCte::where('proveedor_id', $compra->first()->proveedor_id)->first();

            //Si no abonó exacto y no tiene cta cte no dejo seguir.
            $compraUp = $compra->first();

            if ($req['diferencia'] <> 0) {
                if (is_null($proveedor)) {
                    return response()->json(['error' => true, 'data' => 'Corrobore que el proveedor tenga una cuenta corriente abierta']);
                } else {
        
                    //Actualizo la cuenta corriente con el proveedor
                    $proveedor = CtaCte::where('proveedor_id', $compraUp->proveedor_id)->first();
        
                    $saldoProveedor = $proveedor->saldo;
                    CtaCte::where('proveedor_id', $compraUp->proveedor_id)->update([
                        'saldo' => $saldoProveedor + $req['diferencia']
                    ]);
                }
            }

            //Actialzo la compra
            $compra->update([
                'precio_abonado' => $req['pago'],
                'confirmada' => true,
                'fecha_compra' => Carbon::now()->format('Y-m-d'),
            ]);


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
                $prod = Productos::whereId($value['producto_id']);
                $prod->decrement('en_transito', $value['cantidad']);
                $prod->increment('stock', $value['cantidad']);
            }

            $this->movimientosRepository->guardarMovimiento(
                'compras', 'CONFIRMACION', $usuario, $compraUp->id, null, null, $req['diferencia']
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }
    
    public function editarCompra(Request $request) {
        $req = $request->all();

        try {
            DB::beginTransaction();
            
            $totalPrecioCompra = 0;
            $cantidad = 0;
            foreach ($req['filas'] as $fila) {
                $compraDetalle = null;
                
                // // La fila puede venir de 2 formas: 1, el producto como array (no fue editado esa fila), 2 el producto como int (fue editado y elegido otro)
                // // Formateo entonces los datos para trabajarlos.
                if (isset($fila['id'])) {
                    $compraDetalle = $fila;
                } else {
                    $productoDetalle = Productos::whereId($fila['producto'])->first()->toArray();
                    $fila['producto'] = $productoDetalle;
                    $compraDetalle = $fila;
                }

                if (isset($compraDetalle['compra_id'])) {
                    //Fila de la compra inicial entonces reemplazo campos quizá editó producto, precio o cantidad
                    $cargarRow = ComprasDetalle::whereId($compraDetalle['id']);

                    $productoAnterior = $cargarRow->first()->toArray();

                    $cargarRow->update([
                        'producto_id' => $compraDetalle['producto_id'],
                        'precio' => $compraDetalle['precio'],
                        'cantidad' => $compraDetalle['cantidad'],
                    ]);

                    Productos::whereId($compraDetalle['producto_id'])->decrement('en_transito', $productoAnterior['cantidad']);
                    Productos::whereId($compraDetalle['producto_id'])->increment('en_transito', $compraDetalle['cantidad']);
                
                } else {
                    // Es una fila agregada por edición
                    ComprasDetalle::create([
                        'compra_id' => $req['id'],
                        'producto_id' => $compraDetalle['producto']['id'],
                        'precio' => $compraDetalle['precio'],
                        'cantidad' => $compraDetalle['cantidad'],
                    ]);

                    Productos::whereId($compraDetalle['producto']['id'])->increment('en_transito', $compraDetalle['cantidad']);
                }

                $totalPrecioCompra += $compraDetalle['precio'] * $compraDetalle['cantidad'];
                $cantidad += $compraDetalle['cantidad'];
            }

            Compras::whereId($req['id'])->update([
                'proveedor_id' => $req['proveedor'],
                'precio_total' => $totalPrecioCompra,
                'cantidad' => $cantidad,
            ]);


            $this->movimientosRepository->guardarMovimiento(
                'compras', 'MODIFICACION', $req['usuario'], $req['id'], null, null, null, null
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
    }

    public function borrarCompra(int $id) {

        try {
            DB::beginTransaction();

            ComprasDetalle::where('compra_id', $id)->delete();
            Compras::find($id)->delete();

            DB::commit();
    
            return response()->json(['error' => false]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
    }

    public function getCompra (int $id) {
        return response()->json(['error' => false, 'compra' => Compras::whereId($id)->with(['detalleCompra', 'detalleCompra.producto'])->get()->toArray()]);
    }
}
