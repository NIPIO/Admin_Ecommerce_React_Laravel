<?php

namespace App\Http\Controllers;

use App\Models\Compras;
use App\Models\ComprasDetalle;
use App\Models\Productos;
use App\Repositories\CajaRepository;
use App\Repositories\ComprasRepository;
use App\Repositories\ComprasDetalleRepository;
use App\Repositories\CuentasRepository;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\MovimientosRepository;
use App\Repositories\ProductosRepository;

class ComprasController extends Controller
{
    private $movimientosRepository;
    private $comprasRepository;
    private $indexRepository;
    private $comprasDetalleRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository, ComprasRepository $comprasRepository, ComprasDetalleRepository $comprasDetalleRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
        $this->comprasDetalleRepository = $comprasDetalleRepository;    
        $this->comprasRepository = $comprasRepository;    
    }

    public function index() {
        $compras = $this->indexRepository->indexCompras(request()->get('proveedor'), request()->get('producto'));
        return response()->json(['error' => false, 'allCompras' => Compras::all(), 'comprasFiltro' => $compras->get()]);
    }

    public function nuevaCompra(Request $request, ProductosRepository $productosRepository) {
        $req = $request->all();
        try {
            DB::beginTransaction();

            $compra = $this->comprasRepository->setCompra($req);
            
            $totalPrecioCompra = 0;
            foreach ($req['productos'] as $compraDetalleRow) {
                // 1- Cargo cada row de la compra (uno o varios productos).
                $this->comprasDetalleRepository->setCompraDetalle($compra, $compraDetalleRow);
                // 2- Incremento el precio final.
                $totalPrecioCompra += $compraDetalleRow['cantidad'] * $compraDetalleRow['costo'];
                // 3- Incremento el transito de cada producto.
                $productosRepository->incrementar($compraDetalleRow, 'en_transito');
            }

            $this->comprasRepository->updatePrecioCompra($compra, $totalPrecioCompra);

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }

    public function confirmarCompra(Request $request, CuentasRepository $cuentasRepository, CajaRepository $cajaRepository) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            DB::beginTransaction();

            //Datos iniciales: compra y proveedor.
            $compra = $this->comprasRepository->getCompra($req['id']);
            $cuenta = $cuentasRepository->getCuenta($compra->proveedor_id);

            //Si no abonó exacto y no tiene cta cte no dejo seguir.
            if ($req['diferencia'] <> 0) {
                if (is_null($cuenta)) {
                    return response()->json(['error' => true, 'data' => 'Corrobore que el proveedor tenga una cuenta corriente abierta']);
                } else {
                    //Actualizo la cuenta corriente con el proveedor
                    $cuentasRepository->updateSaldoCuenta($cuenta, $req['diferencia'], 'increment');
                }
            }

            //1- Actualizo la compra
            $this->comprasRepository->confirmarCompra($compra,  $req);
            
            //2- Grabo el movimiento en la caja
            $cajaRepository->setCaja($usuario, [
                'tipoMovimiento' => 'COMPRA', 
                'tipoCaja' => $req['tipoCaja'], 
                'importe' => - $req['pago'],
                'item_id' => $req['id'],
            ]);

            //3- Paso el stock de la compra en transito a stock
            $this->comprasDetalleRepository->movimientoStockConfirmacionCompra($req['id']);
            
            //4- Guardo el movimiento.
            $this->movimientosRepository->guardarMovimiento(
                'compras', 'CONFIRMACION', $usuario, $compra->id, null, null, $req['diferencia']
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
                        'costo' => $compraDetalle['costo'],
                        'cantidad' => $compraDetalle['cantidad'],
                    ]);

                    Productos::whereId($compraDetalle['producto_id'])->decrement('en_transito', $productoAnterior['cantidad']);
                    Productos::whereId($compraDetalle['producto_id'])->increment('en_transito', $compraDetalle['cantidad']);
                
                } else {
                    // Es una fila agregada por edición
                    ComprasDetalle::create([
                        'compra_id' => $req['id'],
                        'producto_id' => $compraDetalle['producto']['id'],
                        'costo' => $compraDetalle['costo'],
                        'cantidad' => $compraDetalle['cantidad'],
                    ]);

                    Productos::whereId($compraDetalle['producto']['id'])->increment('en_transito', $compraDetalle['cantidad']);
                }

                $totalPrecioCompra += $compraDetalle['costo'] * $compraDetalle['cantidad'];
                $cantidad += $compraDetalle['cantidad'];
            }

            Compras::whereId($req['id'])->update([
                'proveedor_id' => $req['proveedor'],
                'costo' => $totalPrecioCompra,
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

            $this->comprasRepository->deleteCompra($id);
            
            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }

    public function verCompra(int $id) {
        return response()->json(['error' => false, 'compra' => Compras::whereId($id)->with(['detalleCompra', 'detalleCompra.producto'])->get()->toArray()]);
    }
}
