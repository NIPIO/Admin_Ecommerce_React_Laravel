<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index() {
        // $rol = request()->get('rol');
        // $permiso = request()->get('permiso');
        $roles = Roles::orderBy('id', 'DESC')->with(['permisos']);

        // if ($rol) {
        //     $roles->where('id', (int) $rol);
        // } elseif ($permiso) {
        //     $roles->where('permiso_id', (int) $permiso);
        // }
        
        return response()->json(['error' => false, 'allRoles' => Roles::all(), 'rolesFiltro' => $roles->get()]);
    }

//     public function editarRol(Request $req) {
//         $rol = $req->all();
// dd($rol);
//     }

    public function nuevoRol(Request $request) {
        $req = $request->all();

        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un rol con ese nombre']);
            }

            $rol = new Roles();
            $rol->nombre = $req['nombre'];
            $rol->descripcion = $req['descripcion'];
            $rol->save();
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
        

        return response()->json(['status' => 200]);
    }

    public function chequearSiExiste($nombre) {
        return count(Roles::where('nombre', $nombre)->get()->toArray()) > 0;
    }
}
