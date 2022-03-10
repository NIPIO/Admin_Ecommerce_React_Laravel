<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Marcas;
use App\Models\Productos;
use App\Models\Vendedores;

class MarcasRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function getMarca($id) {
        return Marcas::whereId($id)->first();
    }
    
    public function getStockPorMarca($marcas, $tipoStock, $marcaFiltro) {
        if ($marcaFiltro) {
            $productosDeEsaMarca = Productos::where('marca', '=', (int)$marcaFiltro)->get()->toArray();
            $marcas[0]->$tipoStock = array_sum(array_column($productosDeEsaMarca, $tipoStock));
        } else {
            foreach ($marcas as $marca) {
                $productosDeEsaMarca = Productos::where('marca', '=', $marca->id)->get()->toArray();
                $marcas[$marca->id - 1]->$tipoStock = array_sum(array_column($productosDeEsaMarca, $tipoStock));
            }
        }
        return $marcas;
    }

    public function setMarca($req) {
        return Marcas::create(['nombre' => $req['nombre']]);
    }

    public function updateMarca($req) {
        $marca = $this->getMarca($req['id']);

        $nombreAnterior = $marca->nombre;

        $marca->update([
            "nombre" => $req['nombre'],
        ]);

        return ['nombreAnterior' => $nombreAnterior, 'marca' => $marca];
    }

}
