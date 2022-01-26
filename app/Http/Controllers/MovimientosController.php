<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Movimientos;
use Carbon\Carbon;

class MovimientosController extends Controller
{

    public function index(ProductosController $productos, VentasController $ventas) {

        $usuario = request()->get('usuario');
        $tipoMovi = request()->get('tipoMovimiento');
        $fechas = request()->get('fechas');
        $seccion = request()->get('seccion');
        $movimientos = Movimientos::orderBy('id', 'DESC')->with('usuario');
        

        if ($usuario) {
            $movimientos->whereUsuario((int) $usuario);
        }

        if ($tipoMovi) {
            $movimientos->whereTipoMovimiento($tipoMovi);
        }

        if ($seccion) {
            $movimientos->whereTabla($seccion);
        }
        
        if ($fechas) {
            $movimientos->whereBetween('created_at', [Carbon::parse(substr($fechas[0], 1, -1))->format('Y-m-d'), Carbon::parse(substr($fechas[1], 1, -1))->format('Y-m-d')]);
        }

        $clientes = Clientes::orderBy('id', 'DESC')->first();
        $ventas = $ventas->getVentasConfirmadas();
        $productos = $productos->getStock();

        return response()->json(['error' => false, 'movimientosFiltro' => $movimientos->get(), 'datosIniciales' => [
            [
                'label' => 'Ventas confirmadas',
                'value' => $ventas
            ], 
            [
                'label' => 'Clientes',
                'value' => $clientes ? $clientes->id : 0
            ],
            [
                'label' => 'Stock',
                'value' => $productos ? $productos[0] : 0
            ],
            [
                'label' => 'Stock reservado',
                'value' => $productos ? $productos[1] : 0
            ],
            [
                'label' => 'En transito',
                'value' => $productos ? $productos[2] : 0
            ],
            [
                'label' => 'En transito reservado',
                'value' => $productos ? $productos[3] : 0
            ],
        ]]);
   }

   //Las compras y ventas que se cargan NO se guardan, solamente cuando se CONFIRMAN.
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

