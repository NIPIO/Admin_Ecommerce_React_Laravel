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

