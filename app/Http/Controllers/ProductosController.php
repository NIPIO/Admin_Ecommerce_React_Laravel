<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Json;
use App\Models\Marcas;
use App\Models\Productos;
use App\Repositories\CamposEditadosRepository;
use App\Repositories\IndexRepository;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductosController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
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

            $producto = Productos::where('nombre', $req['nombre'])->get();
            
            if (count($producto) > 0) {
                return response()->json(['error' => true, 'data' => 'Existe un producto con ese nombre']);
            }
     
            DB::beginTransaction();

            $producto = Productos::create([
                'nombre' => $req['nombre'],
                'marca' => $req['marca'],
                'precio' => $req['precio'],
                'costo' => $req['costo'],
                'stock' => $req['stock'],
                'stock_reservado' => 0,
                'en_transito_reservado' => 0,
            ]);
    
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
            if ($cambios[0] === 'stock') {
                $productoEnCuestion = $producto->first()->toArray();

                //Si bajó la cantidad de stock me fijo que no se pase de 0 si hay reservas
                if($req['stock'] < $productoEnCuestion['stock_reservado']) {
                    return response()->json(['error' => true, 'data' => 'Hay ' . $productoEnCuestion['stock_reservado'] . ' ' . $productoEnCuestion['nombre'] . ' reservados, no podés poner menos cantidad en stock o editá las reservas.']);
                }
            }
            $producto->update([
                "nombre" => $req['nombre'],
                "marca" => $req['marca'],
                "stock" => $req['stock'],
                "precio" => $req['precio'],
            ]);

            if ($cambios) { //EDITÓ ALGÚN CAMPO
                $this->movimientosRepository->guardarMovimiento(
                    'productos', 'MODIFICACION', $usuario, $req['id'], $cambios[1], $req[$cambios[0]], null, $cambios[0]
                );
            }

            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
       
        return response()->json(['error' => false]);
    }
            
 
}
