<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use App\Repositories\CamposEditadosRepository;
use App\Repositories\ComunRepository;
use App\Repositories\IndexRepository;
use App\Repositories\MovimientosRepository;
use App\Repositories\ProductosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductosController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;
    private $comunRepository;
    private $productosRepository;

    public function __construct(ComunRepository $comunRepository, IndexRepository $indexRepository, MovimientosRepository $movimientosRepository, ProductosRepository $productosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
        $this->comunRepository = $comunRepository;    
        $this->productosRepository = $productosRepository;    
    }

    public function index() {
        $producto = request()->get('producto');
        $marca = request()->get('marca');

        $productos = $this->indexRepository->indexProductos($producto, $marca);

        return response()->json(['error' => false, 'allProductos' => Productos::all(), 'productosFiltro' => $productos->get()]);
    }

    public function nuevoProducto(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            if($this->comunRepository->chequearSiExiste('producto', $req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un producto con ese nombre']);
            }

            DB::beginTransaction();

            $producto = $this->productosRepository->setProducto($req);
    
            $this->movimientosRepository->guardarMovimiento(
                'productos', 'ALTA', $usuario, $producto->id, null, null, null
            );

            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
        
        return response()->json(['error' => false]);
    }

    public function editarProducto(Request $request, CamposEditadosRepository $camposEditadosRepository) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            $producto = Productos::whereId($req['id']);

            $cambios = $camposEditadosRepository->buscarCamposEditados($producto, $req);
            //Se revisa el editar stock de un producto que tenga ventas reservadas para que no se sobrepase.
            if ($cambios) {
                if ($cambios[0] === 'stock') {
                    $productoEnCuestion = $producto->first()->toArray();
    
                    //Si bajó la cantidad de stock me fijo que no se pase de 0 si hay reservas
                    if($req['stock'] < $productoEnCuestion['stock_reservado']) {
                        return response()->json(['error' => true, 'data' => 'Hay ' . $productoEnCuestion['stock_reservado'] . ' ' . $productoEnCuestion['nombre'] . ' reservados, no podés poner menos cantidad en stock o editá las reservas.']);
                    }
                }
    
                $this->productosRepository->updateGeneralProducto($producto, $req);
    
                if ($cambios) { //EDITÓ ALGÚN CAMPO
                    $this->movimientosRepository->guardarMovimiento(
                        'productos', 'MODIFICACION', $usuario, $req['id'], $cambios[1], $req[$cambios[0]], null, $cambios[0]
                    );
                }
    
                DB::commit();
            }
            
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
       
        return response()->json(['error' => false]);
    }
            
 
}
