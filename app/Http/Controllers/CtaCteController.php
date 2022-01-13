<?php

namespace App\Http\Controllers;

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

        $cuentas = CtaCte::orderBy('id', 'DESC')->with(['proveedor']);

        if ($proveedor) {
            $cuentas->where('proveedor_id', (int) $proveedor);
        }
        
        return response()->json(['error' => false, 'allCuentas' => CtaCte::all(), 'cuentasFiltro' => $cuentas->get()]);
    }


    public function nuevaCtaCte(Request $request) {
        $req = $request->all();
        try {
            $proveedor = Proveedores::whereId($req['proveedor'])->first()->toArray();

                
                if($this->chequearSiExiste($proveedor['id'])){
                        return response()->json(['error' => true, 'data' => 'Ese proveedor ya tiene una cuenta']);
                }

                $venta = new CtaCte();
                $venta->proveedor_id = $proveedor['id'];
                $venta->saldo = $req['saldo'];
                $venta->save();
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

    public function chequearSiExiste($proveedorId) {
        return count(CtaCte::where('proveedor_id', $proveedorId)->get()->toArray()) > 0;
    }
}
