<?php

namespace App\Http\Controllers;

use App\Models\Marcas;
use App\Repositories\ComunRepository;
use App\Repositories\IndexRepository;
use App\Repositories\MarcasRepository;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarcasController extends Controller
{
    public $marcas;
    private $movimientosRepository;
    private $marcasRepository;
    private $indexRepository;
    private $comunRepository;

    public function __construct(IndexRepository $indexRepository, MovimientosRepository $movimientosRepository, MarcasRepository $marcasRepository, ComunRepository $comunRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->marcasRepository = $marcasRepository;    
        $this->indexRepository = $indexRepository;    
        $this->comunRepository = $comunRepository;    
    }

    public function index() {
        $marca = request()->get('marca');

        $this->marcas = $this->indexRepository->indexMarcas($marca);

        foreach (['stock', 'en_transito'] as $tipo) {
            $this->marcas = $this->marcasRepository->getStockPorMarca($this->marcas, $tipo, $marca);
        }
        
        return response()->json(['error' => false, 'allMarcas' => Marcas::all(), 'marcasFiltro' => $this->marcas]);
    }

    public function nuevaMarca(Request $request) {

        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            if($this->comunRepository->chequearSiExiste('marca', $req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe una marca con ese nombre']);
            }

            DB::beginTransaction();
            
            $marca = $this->marcasRepository->setMarca($req);

            $this->movimientosRepository->guardarMovimiento(
                'marcas', 'ALTA', $usuario, $marca->id, null, null, null
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }

    public function editarMarca(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            if($this->comunRepository->chequearSiExiste('marca', $req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe una marca con ese nombre']);
            }
            
            $marca = $this->marcasRepository->updateMarca($req);

            if ($marca['nombreAnterior'] !== $req['nombre']) { 
                $this->movimientosRepository->guardarMovimiento(
                    'marcas', 'MODIFICACION', $usuario, $req['id'], $marca['nombreAnterior'], $req['nombre'], null, 'nombre'
                );
            }

        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }

        return response()->json(['error' => false]);
    }
}
