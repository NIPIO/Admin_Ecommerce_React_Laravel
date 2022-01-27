<?php

namespace App\Http\Controllers;

use App\Models\Vendedores;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    private $movimientosRepository;

    public function __construct(MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
    }
    
    public function login(Request $request) {
        $datos = $request->all();
        try {
            $usuario = Vendedores::where('usuario', $datos['usuario'])->where('password', $datos['password'])->firstOrFail();
        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'data' => 'No existe el usuario o la contraseña es otra']);
        }

        return response()->json(['error' => false, 'data' => $usuario]);
    }


    public function registro(Request $request) {

        $datos = $request->all();
        try {
            DB::beginTransaction();

            $usuario = Vendedores::where('usuario', $datos['usuario'])->get();

            if (count($usuario)) {
                return response()->json(['status' => 400, 'data' => 'Ya existe ese usuario']);
            }

            $nuevoUsuario = Vendedores::create([
                'usuario' => $datos['usuario'],
                'password' => $datos['password'],
                'nombre' => $datos['nombre'],
                'rol_id' => 2,
            ]);

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
