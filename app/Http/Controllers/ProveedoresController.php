<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use Illuminate\Http\Request;

class ProveedoresController extends Controller
{
    public function __construct()
    {
    }

    public function index() {
        $proveedores = Proveedores::orderBy('id', 'DESC');
        return response()->json(['error' => false, 'allProveedores' => $proveedores->get()]);
    }

    public function nuevoProveedor(Request $request) {
        $req = $request->all();

        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un proveedor con ese nombre']);
            }

            $proveedor = new Proveedores();
            $proveedor->nombre = $req['nombre'];
            $proveedor->save();
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
        

        return response()->json(['status' => 200]);
    }

    
    public function editarProveedor(Request $request) {
        $req = $request->all();

        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un proveedor con ese nombre']);
            }
    
            Proveedores::whereId($req['id'])->update([
                "nombre" => $req['nombre'],
            ]);
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }

        
        return response()->json(['error' => false]);
    }

    public function chequearSiExiste($nombre) {
        return count(Proveedores::where('nombre', $nombre)->get()->toArray()) > 0;
    }

    
   

}
