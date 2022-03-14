<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Productos;

class ProductosRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function getProducto($id) {
        return Productos::whereId($id)->first();
    }

    public function setProducto($req) {
        return Productos::create([
            'nombre' => $req['nombre'],
            'marca' => $req['marca'],
            'costo' => $req['costo'],
            'stock' => $req['stock'],
            'stock_reservado' => 0,
            'en_transito_reservado' => 0,
        ]);
    }
    
    public function updateGeneralProducto($producto, $req) {
        return $producto->update([
            "nombre" => $req['nombre'],
            "marca" => $req['marca'],
            "stock" => $req['stock'],
            "costo" => $req['costo'],
        ]);
    }
    public function updateProducto($id, $campo, $valor) {
        Productos::whereId($id)->update([
            $campo => $valor,
        ]);
    }
    
    public function getStockTotal() {
        return [Productos::sum('stock'), Productos::sum('stock_reservado'), Productos::sum('en_transito'), Productos::sum('en_transito_reservado')];
    }

    public function incrementar($compraDetalleRow, $tipo) {
        Productos::whereId($compraDetalleRow['producto'])->increment($tipo, $compraDetalleRow['cantidad']);
    }

    public function chequearDisponibilidadStock($ventaDetalleRow) {
        // Obtengo el producto.
        $prod = $this->getProducto($ventaDetalleRow['producto'])->toArray();

        //Me fijo que no nos sobrepasemos de stock con la nueva venta.
        if ($prod['stock'] - $prod['stock_reservado'] - (int) $ventaDetalleRow['cantidad'] < 0 ) {
            return response()->json([
                'error' => true, 
                'data' => 'No hay stock suficiente para el ' . $prod['nombre'] . '. Stock disponible: ' . ($prod['stock'] - $prod['stock_reservado'])
            ]); 
        }
    }
}

