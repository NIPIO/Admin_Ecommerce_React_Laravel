<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\CtaCte;

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

    public function updateCuenta($cuenta, $req) {
        $cuenta->update([
            "saldo" => $req['saldo'],
            ($req['esCliente'] ? "cliente_id" : "proveedor_id") => $req['proveedor']
        ]);
    }

    public function updateSaldoCuenta($cuenta, $diferencia) {
        $cuenta->increment('saldo', $diferencia);
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
}
