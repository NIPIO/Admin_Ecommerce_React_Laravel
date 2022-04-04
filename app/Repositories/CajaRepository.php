<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Caja;

class CajaRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function setCaja($usuario, $req) {
        return Caja::create([
            'tipo_movimiento' => strtoupper($req['tipoMovimiento']),
            'tipo_caja' => $req['tipoCaja'],
            'importe' => in_array($req['tipoMovimiento'], ['Egreso','pago', 'Gasto']) ? - $req['importe'] : $req['importe'],
            'usuario' => $usuario,
            'observacion' => isset($req['observacion']) ? $req['observacion'] : null,
            'item_id' => isset($req['item_id']) ? $req['item_id'] : null,
        ]);
    }
}
