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
        return response()->json(['error' => false, 'allCuentas' => CtaCte::all(), 'cuentasFiltro' => $cuentas->get()]);
    }

    public function nuevaCtaCte(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            DB::beginTransaction();

            //Puede ser proveedor o cliente que necesita abrir una cuenta
            if($this->chequearSiExiste($req['proveedor'], $req['tipoCuenta'])){
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

        return response()->json(['status' => 200]);
    }

    public function editarCuenta(Request $request, CamposEditadosRepository $camposEditadosRepository) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            DB::beginTransaction();

            // 1- Actualizo los datos
            $cuenta = CtaCte::whereId($req['id']);
            $this->cuentasRepository->updateCuenta($cuenta, $req);
           
            // 2- Busco los cambios y grabo el movimiento
            $cambios = $this->buscarCamposEditados($cuenta, $req);
            if ($cambios) {
                foreach ($cambios as $cambio) {
                    $this->movimientosRepository->guardarMovimiento(
                        'cuentas_corrientes', 'MODIFICACION', $usuario, $req['id'], $cambio[1], $cambio[2], $cambio[3], $cambio[0] === 'saldo' ? 'saldo' : 'responsable'
                    );
                }
            }

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
       
        return response()->json(['error' => false]);
    }

    public function chequearSiExiste($id, $tipoCuenta) {
        return $this->cuentasRepository->existeCuenta($id, $tipoCuenta);
    }
                
    private function buscarCamposEditados($cuenta, $req) {
        $cuenta = $cuenta->first();
        $campos = [];
        if ($req['esCliente'] ? $cuenta->cliente_id : $cuenta->proveedor_id !== $req['proveedor']) {
            //tabla, dato anterior, dato posterior, diferencia
            array_push($campos, ['proveedor', $req['esCliente'] ? $cuenta->cliente_id : $cuenta->proveedor_id, $req['proveedor'] , null]);
        }
        if ($cuenta->saldo !== $req['saldo']) {
            //tabla, dato anterior, dato posterior, diferencia
            array_push($campos, ['saldo', $cuenta->saldo, $req['saldo'], $req['saldo'] - $cuenta->saldo]);
        }
        return $campos;
    }

}
