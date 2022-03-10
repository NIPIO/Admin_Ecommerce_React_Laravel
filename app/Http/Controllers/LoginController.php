<?php

namespace App\Http\Controllers;

use App\Models\Vendedores;
use App\Repositories\LoginRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use App\Repositories\VendedoresRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    private $movimientosRepository;
    private $vendedoresRepository;
    private $loginRepository;

    public function __construct(MovimientosRepository $movimientosRepository, VendedoresRepository $vendedoresRepository, LoginRepository $loginRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->loginRepository = $loginRepository;    
        $this->vendedoresRepository = $vendedoresRepository;    
    }
    
    public function login(Request $request) {
        $req = $request->all();
        try {
            $usuario = $this->loginRepository->setLogin($req);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'data' => 'No existe el usuario o la contraseña es otra']);
        }

        return response()->json(['error' => false, 'data' => $usuario]);
    }


    public function registro(Request $request) {

        $req = $request->all();
        try {
            DB::beginTransaction();

            if ($this->loginRepository->checkUsuarioExistente($req)) {
                return response()->json(['error' => true, 'data' => 'Ya existe ese usuario']);
            }

            $nuevoUsuario = $this->vendedoresRepository->setVendedor($req);

            $this->movimientosRepository->guardarMovimiento(
                'vendedores', 'ALTA', null, $nuevoUsuario->id, null, null, null
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false, 'data' => $nuevoUsuario]);
    }
}
