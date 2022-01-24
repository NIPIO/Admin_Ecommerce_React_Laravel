<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    private $movimientosController;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
    }

    public function index() {
        $cliente = request()->get('cliente');
        $clientes = Clientes::orderBy('id', 'DESC');

        if ($cliente) {
            $clientes->whereId((int) $cliente);
        }
        
        return response()->json(['error' => false, 'allClientes' => Clientes::all(), 'clientesFiltro' => $clientes->get()]);
    }

    public function nuevoCliente(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            $cliente = new Clientes();
            $cliente->nombre = $req['nombre'];
            $cliente->telefono = isset($req['telefono']) ? $req['telefono'] : null;
            $cliente->email = isset($req['email']) ? $req['email'] : null;
            $cliente->save();

            $this->movimientosController->guardarMovimiento(
                'clientes', 'ALTA', $usuario, $cliente->id, null, null, null
            );

        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }

        return response()->json(['status' => 200]);
    }


    public function editarCliente(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            $cliente = Clientes::whereId($req['id']);
            
            $cambios = $this->buscarCamposEditados($cliente, $req);
            
            $cliente->update([
                "nombre" => $req['nombre'],
                "email" => $req['email'],
                "telefono" => $req['telefono'],
            ]);

            if ($cambios) { //EDITÓ ALGÚN CAMPO
                $this->movimientosController->guardarMovimiento(
                    'clientes', 'MODIFICACION', $usuario, $req['id'], $cambios[1], $req[$cambios[0]], null, $cambios[0]
                );
            }
        
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
        
        
        return response()->json(['error' => false]);
    }
    
    private function buscarCamposEditados($cliente, $req) {
        $cliente = $cliente->first();

        if ($cliente->nombre !== $req['nombre']) {
            return ['nombre', $cliente->nombre];
        }
        if ($cliente->email !== $req['email']) {
            return ['email', $cliente->email];
        }
        if ($cliente->telefono !== $req['telefono']) {
            return ['telefono', $cliente->telefono];
        }
    }
}
