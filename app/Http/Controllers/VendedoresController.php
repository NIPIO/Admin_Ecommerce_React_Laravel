<?php

namespace App\Http\Controllers;

use App\Models\Vendedores;
use App\Repositories\CamposEditadosRepository;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendedoresController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
    }

    public function index() {
        $vendedor = request()->get('vendedor');

        $vendedores = $this->indexRepository->indexVendedores($vendedor);
        
        return response()->json(['error' => false, 'allVendedores' => Vendedores::all(), 'vendedoresFiltro' => $vendedores->get()]);
    }
        
    public function editarVendedor(Request $request, CamposEditadosRepository $camposEditadosRepository) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            DB::beginTransaction();

            $vendedor = Vendedores::whereId($req['id']);
            
            $cambios = $camposEditadosRepository->buscarCamposEditados($vendedor, $req);

            $vendedor->update([
                "nombre" => $req['nombre'],
                "email" => $req['email'],
                "telefono" => $req['telefono'],
                "rol_id" => $req['rol'],
            ]);

            if ($cambios) { //EDITÓ ALGÚN CAMPO
                $this->movimientosRepository->guardarMovimiento(
                    'vendedores', 'MODIFICACION', $usuario, $req['id'], $cambios[1], $req[$cambios[0]], null, $cambios[0]
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
