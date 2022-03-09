<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Caja;

class CajaRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function nuevaCaja($usuario, $req) {
        return Caja::create([
            'tipo_movimiento' => strtoupper($req['tipoMovimiento']),
            'importe' => $req['tipoMovimiento'] === 'Egreso' ? - $req['importe'] : $req['importe'],
            'usuario' => $usuario,
            'observacion' => $req['observacion'] ?? null,
            'item_id' => isset($req['item_id']) ? $req['item_id'] : null,
        ]);
    }
}
