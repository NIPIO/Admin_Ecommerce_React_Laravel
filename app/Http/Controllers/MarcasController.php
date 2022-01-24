<?php

namespace App\Http\Controllers;

use App\Models\Marcas;
use App\Models\Productos;
use Illuminate\Http\Request;

class MarcasController extends Controller
{
    public $marcas;
    private $movimientosController;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
    }

    public function index() {
        $marca = request()->get('marca');
        $this->marcas = Marcas::orderBy('id', 'ASC');

        if ($marca) {
            $this->marcas->whereId((int) $marca);
        }
        
        $this->marcas = $this->marcas->get();
        $this->getStock('stock', !is_null($marca));
        $this->getStock('en_transito', !is_null($marca));
        
        return response()->json(['error' => false, 'allMarcas' => Marcas::all(), 'marcasFiltro' => $this->marcas]);
    }

    
    public function nuevaMarca(Request $request) {

        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe una marca con ese nombre']);
            }

            $marca = new Marcas();
            $marca->nombre = $req['nombre'];
            $marca->save();

                        
            $this->movimientosController->guardarMovimiento(
                'marcas', 'ALTA', $usuario, $marca->id, null, null, null
            );

       } catch (\Exception $th) {
            throw new \Exception($th->getMessage());
        }
        return response()->json(['status' => 200]);
    }

    public function getStock($tipo, $esFiltro) {
        try {

            foreach ($this->marcas as $marca) {
                $productosDeEsaMarca = Productos::where('marca', '=', $marca->id)->get();
    
                $cantidad = 0;
                foreach ($productosDeEsaMarca as $producto) {
                   $cantidad += $producto->$tipo;
                }

                if ($esFiltro) {
                    $this->marcas[0]->$tipo = $cantidad;

                } else {
                    $this->marcas[$marca->id - 1]->$tipo = $cantidad;
                }
            }
       } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
    }

    public function editarMarca(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe una marca con ese nombre']);
            }
    
            $marca = Marcas::whereId($req['id']);

            $mNombre = $marca->first()->toArray()['nombre'];

            $marca->update([
                "nombre" => $req['nombre'],
            ]);

            if ($mNombre !== $req['nombre']) { 
                $this->movimientosController->guardarMovimiento(
                    'marcas', 'MODIFICACION', $usuario, $req['id'], $mNombre, $req['nombre'], null, 'nombre'
                );
            }

        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }

        return response()->json(['error' => false]);
    }

    public function chequearSiExiste($nombre) {
        return count(Marcas::where('nombre', $nombre)->get()->toArray()) > 0;
    }

}
