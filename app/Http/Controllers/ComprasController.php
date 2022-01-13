<?php

namespace App\Http\Controllers;

use App\Models\Compras;
use App\Models\Productos;
use App\Models\Proveedores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComprasController extends Controller
{
    public function __construct()
    {
    }

    public function index() {
        $compras = Compras::orderBy('id', 'DESC')->with(['proveedor', 'producto'])->get();

        return response()->json(['error' => false, 'data' => $compras]);
    }

    
    public function nuevaCompra(Request $request) {
        $req = $request->all();
        $proveedor = Proveedores::where('nombre', $req['proveedor'])->first();

        DB::beginTransaction();
        try {
            foreach ($req['rowsProductos'] as $productoCompra) {
                $venta = Compras::create([
                'proveedor_id' => $proveedor['id'],
                'producto_id' => $productoCompra['producto'] + 1,
                'cantidad' => $productoCompra['cantidad'],
                'precio_unidad' => $productoCompra['precioUnitario'],
                'precio_total' => $productoCompra['precioUnitario'] * $productoCompra['cantidad'],
                ]);
                
                $venta->save();

                $producto = Productos::find($productoCompra['producto'] + 1);
                $producto->update(['stock_reservado' => $producto->stock_reservado + $productoCompra['cantidad']]);

                $producto->save();
                DB::commit();

            }


        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['status' => 200]);
    }
}
