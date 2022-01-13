<?php

namespace App\Http\Controllers;

use App\Models\Marcas;
use App\Models\Productos;
use Illuminate\Http\Request;

class MarcasController extends Controller
{

    public $marcas;

    public function __construct()
    {
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

        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe una marca con ese nombre']);
            }

            $marca = new Marcas();
            $marca->nombre = $req['nombre'];
            $marca->save();
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

    public function borrarMarca(int $id) {

        try {
            $marca = Marcas::whereId($id)->first();
            $marca->update(['activo' => $marca['activo'] === 0 ? 1 : 0]);
       } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
     
        return response()->json(['error' => false]);
    }

    public function editarMarca(Request $request) {
        $req = $request->all();

        try {
            if($this->chequearSiExiste($req['nombre'])){
                return response()->json(['error' => true, 'data' => 'Existe una marca con ese nombre']);
            }
    
            Marcas::whereId($req['id'])->update([
                "nombre" => $req['nombre'],
            ]);
       } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }

        return response()->json(['error' => false]);
    }

    public function chequearSiExiste($nombre) {
        return count(Marcas::where('nombre', $nombre)->get()->toArray()) > 0;
    }

}
