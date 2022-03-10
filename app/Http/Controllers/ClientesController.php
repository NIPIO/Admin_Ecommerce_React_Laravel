<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Repositories\CamposEditadosRepository;
use App\Repositories\ClientesRepository;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientesController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;
    private $clientesRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository, ClientesRepository $clientesRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
        $this->clientesRepository = $clientesRepository;    
    }

    // GET: Devuelve los clientes y los clientes filtrados.
    public function index() {
        $cliente = request()->get('cliente');
        $clientes = $this->indexRepository->indexClientes($cliente);
        return response()->json(['error' => false, 'allClientes' => Clientes::all(), 'clientesFiltro' => $clientes->get()]);
    }

    // POST: Carga cliente nuevo y guarda el movimiento.
    public function nuevoCliente(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            DB::beginTransaction();

            $cliente = $this->clientesRepository->setCliente($req);
            $this->movimientosRepository->guardarMovimiento('clientes', 'ALTA', $usuario, $cliente->id, null, null, null);

            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }


    // PUT: edita cliente, se fija qué cambió y guarda el movimiento.
    public function editarCliente(Request $request, CamposEditadosRepository $camposEditadosRepository) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            DB::beginTransaction();

            $cliente = Clientes::whereId($req['id']);
            $cambios = $camposEditadosRepository->buscarCamposEditados($cliente, $req);
            $this->clientesRepository->updateCliente($cliente, $req);

            //EDITÓ ALGÚN CAMPO
            if ($cambios) { 
                $this->movimientosRepository->guardarMovimiento(
                    'clientes', 'MODIFICACION', $usuario, $req['id'], $cambios[1], $req[$cambios[0]], null, $cambios[0]
                );
            }
        
            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
        
        return response()->json(['error' => false]);
    }
    
}
