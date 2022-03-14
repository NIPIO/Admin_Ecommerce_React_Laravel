<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\CtaCte;
use App\Models\Movimientos;

class CuentasRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function getCuenta($id) {
        return CtaCte::where('proveedor_id', $id)->first();
    }

    public function setCuenta($req) {
        return CtaCte::create([
            'proveedor_id' => $req['tipoCuenta'] === 'p' ? $req['proveedor'] : null,
            'cliente_id' => $req['tipoCuenta'] === 'c' ? $req['proveedor'] : null,
            'saldo' => $req['saldo'],
            'tipo_cuenta' => $req['tipoCuenta'],
        ]);
    }

    public function updateSaldoCuenta($cuenta, $diferencia, $movimiento) {
        $cuenta->$movimiento('saldo', $diferencia);
    }
  
    public function existeCuenta($id, $tipoCuenta) {
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

    public function getHistorial($id) {
        return Movimientos::where([
            'tabla' => 'cuentas_corrientes',
            'item_id' => (int) $id,
        ])->whereIn('tipo_movimiento', ['ALTA', 'PAGO', 'COBRO'])->orderBy('id', 'DESC')->with('usuario')->get()->toArray();
    }
}
