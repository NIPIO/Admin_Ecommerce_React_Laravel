<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CajaController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
    }
    
    public function index() {
        $tipoMovimiento = request()->get('tipoMovimiento');
        $fechas = request()->get('fechas');

        $caja = $this->indexRepository->indexCaja($tipoMovimiento, $fechas);
        return response()->json(['error' => false, 'allCaja' => Caja::all(), 'cajaFiltro' => $caja->get(), 'datosIniciales' => [
            [
                'label' => 'Ventas',
                'value' => '$' . number_format(Caja::whereTipoMovimiento('VENTA')->sum('importe'),0,",",".")
            ], 
            [
                'label' => 'Compras',
                'value' => '$' . number_format(Caja::whereTipoMovimiento('COMPRA')->sum('importe'),0,",",".")
            ],
            [
                'label' => 'Gastos',
                'value' => '$' . number_format(Caja::whereTipoMovimiento('EGRESO')->sum('importe'),0,",",".")
            ],
            [
                'label' => 'Ingresos',
                'value' => '$' . number_format(Caja::whereTipoMovimiento('INGRESO')->sum('importe'),0,",",".")
            ],
        ]]);
    }

    public function nuevaCaja(Request $request) {

        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            DB::beginTransaction();

            $caja = Caja::create([
                'tipo_movimiento' => strtoupper($req['tipoMovimiento']),
                'importe' => $req['tipoMovimiento'] === 'Egreso' ? - $req['importe'] : $req['importe'],
                'usuario' => $usuario,
                'observacion' => $req['observacion'] ?? null,
            ]);
                        
            $this->movimientosRepository->guardarMovimiento(
                'caja', strtoupper($req['tipoMovimiento']), $usuario, $caja->id, null, null, null
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['status' => 200]);
    }

}
