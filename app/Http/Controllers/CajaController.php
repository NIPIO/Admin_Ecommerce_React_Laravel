<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    private $movimientosController;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
        $this->indexRepository = $indexRepository;    
    }
    
    public function index() {
        $tipoMovimiento = request()->get('tipoMovimiento');
        $fechas = request()->get('fechas');

        $caja = $this->indexRepository->indexCaja($tipoMovimiento, $fechas);

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
