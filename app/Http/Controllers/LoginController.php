<?php

namespace App\Http\Controllers;

use App\Models\Vendedores;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $movimientosController;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
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
            $usuario = Vendedores::where('usuario', $datos['usuario'])->get();

            if (count($usuario)) {
                return response()->json(['status' => 400, 'data' => 'Ya existe ese usuario']);
            }

            $nuevoUsuario = new Vendedores();
            $nuevoUsuario->usuario = $datos['usuario'];
            $nuevoUsuario->password = $datos['password'];
            $nuevoUsuario->nombre = $datos['nombre'];
            $nuevoUsuario->rol_id = 2;

            $nuevoUsuario->save();

            $this->movimientosController->guardarMovimiento(
                'vendedores', 'ALTA', null, $nuevoUsuario->id, null, null, null
            );

            return response()->json(['error' => false, 'data' => $nuevoUsuario]);

        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'data' => 'Ocurrió un error, intentá de nuevo con otro usuario.']);
        }

    }
}
