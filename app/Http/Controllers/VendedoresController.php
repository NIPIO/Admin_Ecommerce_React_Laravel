<?php

namespace App\Http\Controllers;

use App\Models\Vendedores;
use Illuminate\Http\Request;

class VendedoresController extends Controller
{
    public function __construct()
    {
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
        try {
            Vendedores::whereId($req['id'])->update([
                "nombre" => $req['nombre'],
                "email" => $req['email'],
                "telefono" => $req['telefono'],
                "rol_id" => $req['rol'],
            ]);
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }

        
        return response()->json(['error' => false]);
    }
}
