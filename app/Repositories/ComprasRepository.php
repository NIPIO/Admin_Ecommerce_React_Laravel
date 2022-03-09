<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Compras;
use App\Models\ComprasDetalle;
use App\Models\Productos;
use Carbon\Carbon;

class ComprasRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function getCompra($id) {
        return Compras::whereId($id)->first();
    }

    public function setCompra($req) {
        return Compras::create([
            'proveedor_id' => $req['proveedor'],
            'cantidad' => array_sum(array_column($req['productos'], 'cantidad')),
            'precio_total' => 0,
            'activo' => 1,
        ]);
    }

    public function updatePrecioCompra($compra, $totalPrecioCompra) {
        Compras::whereId($compra->id)->update([
            "precio_total" => $totalPrecioCompra,
        ]);
    }

    public function deleteCompra($id) {
        ComprasDetalle::where('compra_id', $id)->delete();
        Compras::find($id)->delete();
    }

    public function confirmarCompra($compra, $pago) {
        $compra->update([
            'precio_abonado' => $pago,
            'confirmada' => true,
            'fecha_compra' => Carbon::now()->format('Y-m-d'),
        ]);
    }

}


class ComprasDetalleRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function setCompraDetalle($compra, $compraDetalleRow) {
        return ComprasDetalle::create([
            'compra_id' => $compra->id,
            'producto_id' => $compraDetalleRow['producto'],
            'cantidad' => $compraDetalleRow['cantidad'],
            'precio' => $compraDetalleRow['precioUnitario']
        ]);
    }

    public function movimientoStockConfirmacionCompra($id) {

        $compraDetalle = ComprasDetalle::whereCompraId($id)->get()->toArray();

        foreach ($compraDetalle as $value) {
            //Obtengo los productos y actualizo su stock
            $prod = Productos::whereId($value['producto_id']);
            $prod->decrement('en_transito', $value['cantidad']);
            $prod->increment('stock', $value['cantidad']);
        }
       
    }

}
