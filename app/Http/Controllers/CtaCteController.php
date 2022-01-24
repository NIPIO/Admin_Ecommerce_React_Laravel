<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\CtaCte;
use App\Models\Productos;
use App\Models\Proveedores;
use Illuminate\Http\Request;

class CtaCteController extends Controller
{
    private $movimientosController;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
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
        $usuario = $req['usuario'];
        $req = $req['data'];
        
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

                        
            $this->movimientosController->guardarMovimiento(
                'cuentas_corrientes', 'ALTA', $usuario, $cuenta->id, null, null, null
            );

        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }

        return response()->json(['status' => 200]);
    }

    public function editarCuenta(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
                $cuenta = CtaCte::whereId($req['id']);
            
                $cambios = $this->buscarCamposEditados($cuenta, $req);

                $cuenta->update([
                    "saldo" => $req['saldo'],
                    ($req['esCliente'] ? "cliente_id" : "proveedor_id") => $req['proveedor']
                ]);
                if ($cambios) { //EDITÓ ALGÚN CAMPO
                    foreach ($cambios as $cambio) {
                        $this->movimientosController->guardarMovimiento(
                            'cuentas_corrientes', 'MODIFICACION', $usuario, $req['id'], $cambio[1], $cambio[2], $cambio[3], $cambio[0] === 'saldo' ? 'saldo' : 'responsable'
                        );
                    }
                }

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
                
    private function buscarCamposEditados($cuenta, $req) {
        $cuenta = $cuenta->first();
        $campos = [];
        if ($req['esCliente'] ? $cuenta->cliente_id : $cuenta->proveedor_id !== $req['proveedor']) {
            //tabla, dato anterior, dato posterior, diferencia
            array_push($campos, ['proveedor', $req['esCliente'] ? $cuenta->cliente_id : $cuenta->proveedor_id, $req['proveedor'] , null]);
        }
        if ($cuenta->saldo !== $req['saldo']) {
            //tabla, dato anterior, dato posterior, diferencia
            array_push($campos, ['saldo', $cuenta->saldo, $req['saldo'], $req['saldo'] - $cuenta->saldo]);
        }
        return $campos;
    }

}
