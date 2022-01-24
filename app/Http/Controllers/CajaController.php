<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    private $movimientosController;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
    }

    public function index() {
        $tipoMovimiento = request()->get('tipo_movimiento');
        $fechas = request()->get('fechas');
        $caja = Caja::orderBy('id', 'DESC')->with('usuario');

        if ($tipoMovimiento) {
            $tipoMovimiento->whereId((int) $tipoMovimiento);
        }
    
        if ($fechas) {
            $caja->whereBetween('created_at', [Carbon::parse(substr($fechas[0], 1, -1))->format('Y-m-d'), Carbon::parse(substr($fechas[1], 1, -1))->format('Y-m-d')]);
        }
        
        return response()->json(['error' => false, 'allCaja' => Caja::all(), 'cajaFiltro' => $caja->get(), 'datosIniciales' => [
            [
                'label' => 'Ventas',
                'value' => '$' . Caja::whereTipoMovimiento('VENTA')->sum('importe')
            ], 
            [
                'label' => 'Compras',
                'value' => '$' . Caja::whereTipoMovimiento('COMPRA')->sum('importe')
            ],
            [
                'label' => 'Gastos',
                'value' => '$' . Caja::whereTipoMovimiento('EGRESO')->sum('importe')
            ],
            [
                'label' => 'Ingresos',
                'value' => '$' . Caja::whereTipoMovimiento('INGRESO')->sum('importe')
            ],
        ]]);
    }

    public function nuevaCaja(Request $request) {

        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {

            $caja = new Caja();
            $caja->tipo_movimiento = strtoupper($req['tipoMovimiento']);
            $caja->importe = $req['tipoMovimiento'] === 'Egreso' ? - $req['importe'] : $req['importe'];
            $caja->usuario = $usuario;
            $caja->observacion = $req['observacion'] ?? null;
            $caja->save();
                        
            $this->movimientosController->guardarMovimiento(
                'caja', strtoupper($req['tipoMovimiento']), $usuario, $caja->id, null, null, null
            );

       } catch (\Exception $th) {
            throw new \Exception($th->getMessage());
        }
        return response()->json(['status' => 200]);
    }

}
