<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProveedoresController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
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
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un proveedor con ese nombre']);
            }

            DB::beginTransaction();

            $proveedor = Proveedores::create(['nombre' => $req['nombre']]);
                        
            $this->movimientosRepository->guardarMovimiento(
                'proveedores', 'ALTA', $usuario, $proveedor->id, null, null, null
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
        
        return response()->json(['status' => 200]);
    }

    public function editarProveedor(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un proveedor con ese nombre']);
            }
    
            DB::beginTransaction();

            $proveedor = Proveedores::whereId($req['id']);
            $pNombre = $proveedor->first()->toArray()['nombre'];
            $proveedor->update([
                "nombre" => $req['nombre'],
            ]);

            if ($pNombre !== $req['nombre']) { 
                $this->movimientosRepository->guardarMovimiento(
                    'proveedores', 'MODIFICACION', $usuario, $req['id'], $pNombre, $req['nombre'], null, 'nombre'
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

    public function chequearSiExiste($nombre) {
        return count(Proveedores::where('nombre', $nombre)->get()->toArray()) > 0;
    }
}
