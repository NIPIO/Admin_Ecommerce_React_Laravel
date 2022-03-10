<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use App\Repositories\ComunRepository;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use App\Repositories\ProveedoresRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProveedoresController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;
    private $proveedoresRepository;
    private $comunRepository;

    public function __construct(IndexRepository $indexRepository, ProveedoresRepository $proveedoresRepository, MovimientosRepository $movimientosRepository, ComunRepository $comunRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
        $this->proveedoresRepository = $proveedoresRepository;    
        $this->comunRepository = $comunRepository;    
    }

    public function index() {
        $proveedor = request()->get('proveedor');
        $proveedores = $this->indexRepository->indexProveedores($proveedor);
        return response()->json(['error' => false, 'allProveedores' => Proveedores::all(), 'proveedoresFiltro' => $proveedores->get()]);
    }

    public function nuevoProveedor(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            if($this->comunRepository->chequearSiExiste('proveedor', $req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un proveedor con ese nombre']);
            }

            DB::beginTransaction();

            $proveedor = $this->proveedoresRepository->setProveedor($req);
                        
            $this->movimientosRepository->guardarMovimiento(
                'proveedores', 'ALTA', $usuario, $proveedor->id, null, null, null
            );

            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
        
        return response()->json(['error' => false]);
    }

    public function editarProveedor(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            if($this->comunRepository->chequearSiExiste('proveedor', $req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un proveedor con ese nombre']);
            }
    
            DB::beginTransaction();

            $proveedor = $this->proveedoresRepository->updateProveedor($req);

            if ($proveedor['nombreAnterior'] !== $req['nombre']) { 
                $this->movimientosRepository->guardarMovimiento(
                    'proveedores', 'MODIFICACION', $usuario, $req['id'], $proveedor['nombreAnterior'], $req['nombre'], null, 'nombre'
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
