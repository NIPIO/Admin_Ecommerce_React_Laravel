<?php

namespace App\Http\Controllers;

use App\Models\Vendedores;
use Illuminate\Http\Request;

class VendedoresController extends Controller
{
    private $movimientosController;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
    }
    public function index() {
        $vendedor = request()->get('vendedor');
        $vendedores = Vendedores::orderBy('id', 'DESC')->with(['rol']);

        if ($vendedor) {
            $vendedores->whereId((int) $vendedor);
        }
        
        return response()->json(['error' => false, 'allVendedores' => Vendedores::all(), 'vendedoresFiltro' => $vendedores->get()]);
    }
        
    public function editarVendedor(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            $vendedor = Vendedores::whereId($req['id']);
            
            $cambios = $this->buscarCamposEditados($vendedor, $req);
            $vendedor->update([
                "nombre" => $req['nombre'],
                "email" => $req['email'],
                "telefono" => $req['telefono'],
                "rol_id" => $req['rol'],
            ]);

            if ($cambios) { //EDITÓ ALGÚN CAMPO
                $this->movimientosController->guardarMovimiento(
                    'vendedores', 'MODIFICACION', $usuario, $req['id'], $cambios[1], $req[$cambios[0]], null, $cambios[0]
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
        if ($cliente->rol_id !== $req['rol']) {
            return ['rol', $cliente->rol_id];
        }
    }
}
