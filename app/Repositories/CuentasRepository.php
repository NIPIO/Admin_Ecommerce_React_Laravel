<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Compras;
use App\Models\CtaCte;
use App\Models\CtaCteMovimientos;
use App\Models\Movimientos;
use App\Models\Ventas;

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

    public function getHistorial(int $cuentaId) {
        //necesito también el proveedor/cliente para buscar movimientos de compras/ventas que haya tenido o no deudas o saldo a favor
        $proveedorId = CtaCte::whereId($cuentaId)->first()->toArray()['proveedor_id'];
      
        //OBTENGO LOS ID DE LAS COMPRAS QUE HIZO.
        $compras = Compras::where([
            'proveedor_id' => $proveedorId])->get('id')->toArray();

        $comprasIds = [];
        foreach ($compras as $key => $value) {
            array_push($comprasIds, $value['id']);
        }
        //OBTENGO: 1- ALTA DE CUENTA. 2- PAGOS Y COBROS GENERADOS. 3- COMPRAS.
        $movimientos = Movimientos::where([
            'tabla' => 'cuentas_corrientes',
            'item_id' => (int) $cuentaId,
        ])->whereIn('tipo_movimiento', ['PAGO', 'COBRO'])->orderBy('updated_at', 'DESC')->get(['diferencia', 'tipo_movimiento', 'tabla', 'updated_at'])->toArray();

        $movimientos2 = Movimientos::where([
            'tabla' => 'compras',
            'tipo_movimiento' => 'CONFIRMACION',
        ])
        ->whereIn('item_id', $comprasIds)->orderBy('updated_at', 'DESC')->get(['diferencia', 'tipo_movimiento', 'tabla', 'updated_at'])->toArray();
        
        return array_merge($movimientos, $movimientos2);
    }
}
