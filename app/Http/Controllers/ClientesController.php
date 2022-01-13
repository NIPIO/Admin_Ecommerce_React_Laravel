<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function __construct()
    {
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
        try {
            $cliente = new Clientes();
            $cliente->nombre = $req['nombre'];
            $cliente->telefono = isset($req['telefono']) ? $req['telefono'] : null;
            $cliente->email = isset($req['email']) ? $req['email'] : null;
            $cliente->save();
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
       

        return response()->json(['status' => 200]);
    }


    public function editarCliente(Request $request) {
        $req = $request->all();

        try {
            Clientes::whereId($req['id'])->update([
                "nombre" => $req['nombre'],
                "email" => $req['email'],
                "telefono" => $req['telefono'],
            ]);
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
        
        
        
        return response()->json(['error' => false]);
    }
    
}
