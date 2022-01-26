<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;

class ProveedoresController extends Controller
{
    private $movimientosController;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
        $this->indexRepository = $indexRepository;    
    }

    public function index() {
        $proveedor = request()->get('proveedor');

        $proveedores = $this->indexRepository->indexProveedores($proveedor);
        
        return response()->json(['error' => false, 'allProveedores' => Proveedores::all(), 'proveedoresFiltro' => $proveedores->get()]);
    }


    public function nuevoProveedor(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un proveedor con ese nombre']);
            }

            $proveedor = new Proveedores();
            $proveedor->nombre = $req['nombre'];
            $proveedor->save();

                        
            $this->movimientosController->guardarMovimiento(
                'proveedores', 'ALTA', $usuario, $proveedor->id, null, null, null
            );


        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
        

        return response()->json(['status' => 200]);
    }

    public function editarProveedor(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un proveedor con ese nombre']);
            }
    
            $proveedor = Proveedores::whereId($req['id']);

            $pNombre = $proveedor->first()->toArray()['nombre'];

            $proveedor->update([
                "nombre" => $req['nombre'],
            ]);

            if ($pNombre !== $req['nombre']) { 
                $this->movimientosController->guardarMovimiento(
                    'proveedores', 'MODIFICACION', $usuario, $req['id'], $pNombre, $req['nombre'], null, 'nombre'
                );
            }

        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }

        
        return response()->json(['error' => false]);
    }

    public function chequearSiExiste($nombre) {
        return count(Proveedores::where('nombre', $nombre)->get()->toArray()) > 0;
    }
}
