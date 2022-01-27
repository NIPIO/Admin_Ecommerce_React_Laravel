<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    private $movimientosRepository;

    public function __construct(MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
    }
    
    public function index() {
        return response()->json(['error' => false, 'allRoles' => Roles::all(), 'rolesFiltro' => Roles::orderBy('id', 'DESC')->with(['permisos'])->get()]);
    }

    public function nuevoRol(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un rol con ese nombre']);
            }

            DB::beginTransaction();

            $rol = Roles::create([
                'nombre' => $req['nombre'],
                'descripcion' => $req['descripcion'],
            ]);

            $this->movimientosRepository->guardarMovimiento(
                'roles', 'ALTA', $usuario, $req['id'], $rol->id, null, null, null
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['status' => 200]);
    }

    public function chequearSiExiste($nombre) {
        return count(Roles::where('nombre', $nombre)->get()->toArray()) > 0;
    }
}
