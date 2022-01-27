<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Movimientos;

class MovimientosRepository implements RepositoryInterface 
{
    public function guardarMovimiento(string $tabla, string $tipoMov,  $vendedor = null, int $campo, $estadoViejo = null, $estadoNuevo = null, $saldo = null,  $campo_modificado = null) {
        Movimientos::create([
            'tabla' => $tabla,
            'tipo_movimiento' => $tipoMov,
            'usuario' => $vendedor,
            'item_id' => $campo,
            'estado_viejo' => $estadoViejo,
            'estado_nuevo' => $estadoNuevo,
            //SOLO PARA COMPRAS Y VENTAS CON DIFERENCIA DE PAGO.
            'diferencia' => $saldo, 
            //SOLO PARA EDICIONES (NO ESTADO)
            'campo_modificado' => $campo_modificado, 
        ]);
    }
}