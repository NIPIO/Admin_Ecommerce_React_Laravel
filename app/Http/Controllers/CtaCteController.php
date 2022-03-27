<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\CtaCte;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Repositories\CamposEditadosRepository;
use App\Repositories\CuentasRepository;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CtaCteController extends Controller
{
    private $movimientosRepository;
    private $cuentasRepository;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository, CuentasRepository $cuentasRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->cuentasRepository = $cuentasRepository;    
        $this->indexRepository = $indexRepository;    
    }

    public function index() {
        $req = request()->all();
        $cuentas = $this->indexRepository->indexCuentas($req);

        return response()->json(['error' => false, 'allCuentas' => CtaCte::all(), 'cuentasFiltro' => $cuentas->get(), 'datosIniciales' => [
            [
                'label' => 'Sdo. Proveedores',
                'value' => '$' . number_format(CtaCte::whereTipoCuenta('p')->sum('saldo'),0,",",".")
            ], 
            [
                'label' => 'Sdo. Clientes',
                'value' => '$' . number_format(CtaCte::whereTipoCuenta('c')->sum('saldo'),0,",",".")
            ],
        ]]);
    }

    public function nuevaCtaCte(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            DB::beginTransaction();

            //Puede ser proveedor o cliente que necesita abrir una cuenta
            if($this->cuentasRepository->existeCuenta($req['proveedor'], $req['tipoCuenta'])){
                return response()->json(['error' => true, 'data' => 'Esa persona ya tiene una cuenta']);
            }
            
            $cuenta = $this->cuentasRepository->setCuenta($req);
            $this->movimientosRepository->guardarMovimiento('cuentas_corrientes', 'ALTA', $usuario, $cuenta->id, null, null, null);

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }

    //Metodo que impacta pagos o cobros sobre una cuenta corriente
    public function editarCuenta(Request $request, CamposEditadosRepository $camposEditadosRepository) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            DB::beginTransaction();

            // 1- Actualizo los datos
            $cuenta = CtaCte::whereId($req['id']);
            $this->cuentasRepository->updateSaldoCuenta($cuenta, $req['cantidad'], $req['tipoMovimiento'] === 'pago' ? 'increment' : 'decrement');
           
            // 2- Grabo movimiento
            $this->movimientosRepository->guardarMovimiento(
                'cuentas_corrientes', strtoupper($req['tipoMovimiento']), $usuario, $req['id'], null, null, $req['cantidad'], null
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
       
        return response()->json(['error' => false]);
    }

    public function verDetalleCuenta($idCuenta) {
        return $this->cuentasRepository->getHistorial($idCuenta);
    }
}
