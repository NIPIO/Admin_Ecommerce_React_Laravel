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

            // Chequeo si cambió el stock y me fijo que no sea menor que el reservado
            if ($req['stock'] <> $producto->first()->toArray()['stock']) {
                if($req['stock'] < $producto->first()->toArray()['stock_reservado']) {
                    return response()->json(['error' => true, 'data' => 'Hay ' . $producto->first()->toArray()['stock_reservado'] . ' ' . $producto->first()->toArray()['nombre'] . ' reservados, no podés poner menos cantidad en stock o editá las reservas.']);
                }
            }

            $this->guardarMovimiento($req, $producto->first()->toArray(), $usuario);
            $this->productosRepository->updateGeneralProducto($producto, $req);
    
            DB::commit();
            
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
       
        return response()->json(['error' => false]);
    }
            
 
    private function guardarMovimiento($req, $prod, $usuario) {
        if($req['marca'] !== $prod['marca']) {
            $this->movimientosRepository->guardarMovimiento(
                'productos', 'MODIFICACION', $usuario, $req['id'], $prod['marca'], $req['marca'], null, 'marca'
            );
        }
        if($req['stock'] !== $prod['stock']) {
            $this->movimientosRepository->guardarMovimiento(
                'productos', 'MODIFICACION', $usuario, $req['id'], $prod['stock'], $req['stock'], null, 'stock'
            );
        }
        if($req['costo'] !== $prod['costo']) {
            $this->movimientosRepository->guardarMovimiento(
                'productos', 'MODIFICACION', $usuario, $req['id'], $prod['costo'], $req['costo'], null, 'costo'
            );
        }
    }
}
