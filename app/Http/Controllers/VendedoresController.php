<?php

namespace App\Http\Controllers;

use App\Models\Vendedores;
use App\Repositories\CamposEditadosRepository;
use App\Repositories\ComunRepository;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use App\Repositories\VendedoresRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendedoresController extends Controller
{
    private $movimientosRepository;
    private $indexRepository;
    private $vendedoresRepository;
    private $comunRepository;

    public function __construct(IndexRepository $indexRepository, VendedoresRepository $vendedoresRepository, MovimientosRepository $movimientosRepository, ComunRepository $comunRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->indexRepository = $indexRepository;    
        $this->vendedoresRepository = $vendedoresRepository;    
        $this->comunRepository = $comunRepository;    
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
            if($this->comunRepository->chequearSiExiste('vendedor', $req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe un vendedor con ese nombre']);
            }

            DB::beginTransaction();

            $vendedor = $this->vendedoresRepository->updateVendedor($req);
            
            $cambios = $camposEditadosRepository->buscarCamposEditados($vendedor, $req);

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
