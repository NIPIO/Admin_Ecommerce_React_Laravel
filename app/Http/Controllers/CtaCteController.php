<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\CtaCte;
use App\Models\Productos;
use App\Models\Proveedores;
use Illuminate\Http\Request;

class CtaCteController extends Controller
{
    public function __construct()
    {
    }

    public function index() {
        $proveedor = request()->get('proveedor');
        $cliente = request()->get('cliente');
        $cuentas = CtaCte::orderBy('id', 'DESC')->with(['proveedor','cliente']);

        if ($proveedor) {
            $cuentas->where('proveedor_id', (int) $proveedor);
        } elseif ($cliente) {
            $cuentas->where('cliente_id', (int) $cliente);
        }
        
        return response()->json(['error' => false, 'allCuentas' => CtaCte::all(), 'cuentasFiltro' => $cuentas->get()]);
    }


    public function nuevaCtaCte(Request $request) {
        $req = $request->all();
        try {
            //PUede ser proveedor o cliente que necesita abrir una cuenta
            if($this->chequearSiExiste($req['id'],$req['tipoCuenta'])){
                return response()->json(['error' => true, 'data' => 'Esa persona ya tiene una cuenta']);
            }

            $cuenta = new CtaCte();
            if ($req['tipoCuenta'] === 'p') {
                $cuenta->proveedor_id = $req['id'];
                $cuenta->cliente_id = null;
            } else {
                $cuenta->cliente_id = $req['id'];
                $cuenta->proveedor_id = null;
            }

            $cuenta->saldo = $req['saldo'];
            $cuenta->tipo_cuenta = $req['tipoCuenta'];
            $cuenta->save();


        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
        

        return response()->json(['status' => 200]);
    }

    public function editarCuenta(Request $request) {
        $req = $request->all();
        try {
            CtaCte::whereId($req['id'])->update([
                "saldo" => $req['saldo'],
            ]);
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
       
        return response()->json(['error' => false]);
    }

    public function chequearSiExiste($id, $tipoCuenta) {
        if ($tipoCuenta === 'p') {
            return count(CtaCte::where([
                'proveedor_id' => $id,
                'tipo_cuenta' => $tipoCuenta,
            ])->get()->toArray()) > 0;
        } else {
            return count(CtaCte::where([
                'cliente_id' => $id,
                'tipo_cuenta' => $tipoCuenta,
            ])->get()->toArray()) > 0;
        }
     
     
    }
}
