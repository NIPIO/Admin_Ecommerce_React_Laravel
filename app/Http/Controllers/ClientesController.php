<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Repositories\CamposEditadosRepository;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientesController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
    }

    public function index() {
        $cliente = request()->get('cliente');

        $clientes = $this->indexRepository->indexClientes($cliente);
        
        return response()->json(['error' => false, 'allClientes' => Clientes::all(), 'clientesFiltro' => $clientes->get()]);
    }

    public function nuevoCliente(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            DB::beginTransaction();

            $cliente = Clientes::create([
                'nombre' => $req['nombre'],
                'telefono' => isset($req['telefono']) ? $req['telefono'] : null,
                'email' => isset($req['email']) ? $req['email'] : null,
            ]);
            
            $this->movimientosRepository->guardarMovimiento(
                'clientes', 'ALTA', $usuario, $cliente->id, null, null, null
            );

            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['status' => 200]);
    }


    public function editarCliente(Request $request, CamposEditadosRepository $camposEditadosRepository) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            DB::beginTransaction();

            $cliente = Clientes::whereId($req['id']);
            
            $cambios = $camposEditadosRepository->buscarCamposEditados($cliente, $req);
            
            $cliente->update([
                "nombre" => $req['nombre'],
                "email" => $req['email'],
                "telefono" => $req['telefono'],
            ]);

            if ($cambios) { //EDITÓ ALGÚN CAMPO
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
