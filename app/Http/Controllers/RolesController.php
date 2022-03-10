<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use App\Repositories\ComunRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use App\Repositories\RolesRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    private $movimientosRepository;
    private $rolesRepository;
    private $comunRepository;

    public function __construct(MovimientosRepository $movimientosRepository, ComunRepository $comunRepository, RolesRepository $rolesRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->rolesRepository = $rolesRepository;    
        $this->comunRepository = $comunRepository;    
    }
    
    public function index() {
        return response()->json(['error' => false, 'allRoles' => Roles::all(), 'rolesFiltro' => Roles::orderBy('id', 'DESC')->with(['permisos'])->get()]);
    }

    public function nuevoRol(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            if($this->comunRepository->chequearSiExiste('rol', $req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un rol con ese nombre']);
            }

            DB::beginTransaction();

            $rol = $this->rolesRepository->setRol($req);
            
            $this->movimientosRepository->guardarMovimiento(
                'roles', 'ALTA', $usuario, $rol->id, null, null, null
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }
}
