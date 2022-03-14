<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Repositories\CajaRepository;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CajaController extends Controller
{
    private $movimientosRepository;
    private $cajaRepository;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository, CajaRepository $cajaRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
        $this->cajaRepository = $cajaRepository;    
    }
    
    public function index() {
        $caja = $this->indexRepository->indexCaja(request()->get('tipoMovimiento'), request()->get('fechas'));

        return response()->json(['error' => false, 'allCaja' => Caja::all(), 'cajaFiltro' => $caja->get(), 'datosIniciales' => [
            [
                'label' => 'Efectivo',
                'value' => '$' . number_format(Caja::whereTipoMovimiento('VENTA')->sum('importe'),0,",",".")
            ], 
            [
                'label' => 'Ingresos',
                'value' => '$' . number_format(Caja::whereTipoMovimiento('INGRESO')->sum('importe'),0,",",".")
            ],
            [
                'label' => 'Gastos',
                'value' => '$' . number_format(Caja::whereTipoMovimiento('EGRESO')->sum('importe'),0,",",".")
            ],
           
        ]]);
    }

    public function nuevaCaja(Request $request) {

        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            DB::beginTransaction();

            $caja = $this->cajaRepository->setCaja($usuario, $req);
            $this->movimientosRepository->guardarMovimiento('caja', strtoupper($req['tipoMovimiento']), $usuario, $caja->id, null, null, null);

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }

}
