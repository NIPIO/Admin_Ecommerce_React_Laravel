<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\ComprasDetalle;
use App\Models\Productos;
use App\Models\VentasDetalle;

class StockRepository implements RepositoryInterface 
{

    public function actualizarStockVentas($venta, $quiereInactivar) {
        $productosDeLaVenta = VentasDetalle::whereVentaId($venta->id)->get()->toArray();

        foreach ($productosDeLaVenta as $productoVenta) {
            $producto = Productos::whereId($productoVenta['producto_id']);
            //Si reactiva la venta, chequeo que haya stock disponible para reactivarla.
            if (!$quiereInactivar && $producto->first()->stock - $producto->first()->stock_reservado - $productoVenta['cantidad'] < 0) {
                return ['error' => true, 'tipo' => 'Stock'];
            }

            $quiereInactivar ? $producto->decrement('stock_reservado', $productoVenta['cantidad']) : $producto->increment('stock_reservado', $productoVenta['cantidad']);
        }

        return ['error' => false, 'tipo' => null];
    }

    public function actualizarStockCompras($compra, $quiereInactivar) {

        $productosDeLaCompra = ComprasDetalle::whereCompraId($compra->id)->get()->toArray();

        foreach ($productosDeLaCompra as $productoCompra) {
            $producto = Productos::whereId($productoCompra['producto_id']);
            $quiereInactivar ? $producto->decrement('en_transito', $productoCompra['cantidad']) : $producto->increment('en_transito', $productoCompra['cantidad']);
        }

        return ['error' => false, 'tipo' => null];
    }

}