<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Productos;
use App\Models\Vendedores;
use App\Models\Ventas;
use App\Models\VentasDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class VentasController extends Controller
{
    public $fecha;

    public function __construct()
    {
        $this->fecha = Carbon::now()->format('Y-m-d');
    }

    public function index() {
        $cliente = request()->get('cliente');
        $vendedor = request()->get('vendedor');
        $fechas = request()->get('fechas');

        $ventas = Ventas::orderBy('id', 'DESC')->with(['cliente', 'vendedor']);
        if ($cliente) {
            $ventas->whereClienteId((int) $cliente);
        }
        if ($vendedor) {
            $ventas->whereVendedorId((int) $vendedor);
        }
        if ($fechas) {
            $ventas->whereBetween('fecha_venta', [Carbon::parse(substr($fechas[0], 1, -1))->format('Y-m-d'), Carbon::parse(substr($fechas[1], 1, -1))->format('Y-m-d')]);
        } else {
            $ventas->whereDate('fecha_venta', '<=' ,$this->fecha);
        }
        
        return response()->json(['error' => false, 'allVentas' => Ventas::all(), 'ventasFiltro' => $ventas->get()]);
    }



    public function ventasByFilter(Request $request) {
        $filtros = $request->all();
        
        try {
            $ventas = Ventas::orderBy('id', 'DESC')->with(['cliente', 'producto', 'vendedor'])
            ->whereBetween('fecha_venta', [$filtros['desde'], $filtros['hasta']]);
            
            if (isset($filtros['cliente'])) {
                $cliente = Clientes::where('nombre', $filtros['cliente'])->first();
                $ventas->where('cliente_id', $cliente->id);
            }
    
            if (isset($filtros['vendedor'])) {
                $vendedor = Vendedores::where('nombre', $filtros['vendedor'])->first();
                $ventas->where('vendedor_id', $vendedor->id);
            }
    
            if (isset($filtros['producto'])) {
                $producto = Productos::where('nombre', $filtros['producto'])->first();
                $ventas->where('producto_id', $producto->id);
            }
    
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());;
        }
        
        return response()->json(['error' => false, 'data' => $ventas->get()]);
    }

    public function nuevaVenta(Request $request) {
        $req = $request->all();

        $cliente = Clientes::where('nombre', $req['cliente'])->first();
        $vendedor = Vendedores::where('nombre', $req['vendedor'])->first();
        
        DB::beginTransaction();
        try {

            $precio_final = 0;
            foreach ($req['rowsProductos'] as $productoVenta) {
                $precio_final += $productoVenta['cantidad'] * $productoVenta['precioUnitario'];
            }

            $venta = Ventas::create([
                'cliente_id' => $cliente['id'],
                'vendedor_id' => $vendedor['id'],
                'cantidad' => array_sum(array_column($req['rowsProductos'], 'cantidad')),
                'precio_total' => $precio_final, 
                'vendedor_comision' => $precio_final * 0.01 ,
                'fecha_venta' => $this->fecha,
            ]);
            
            $venta->save();

            foreach ($req['rowsProductos'] as $productoVenta) {
                $producto = Productos::where('nombre', $productoVenta['nombre'])->first();

                $venta = VentasDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto['id'],
                    'cantidad' => $productoVenta['cantidad'],
                    'precio_unidad' => $productoVenta['precioUnitario'],
                ]);
                
                $venta->save();

                $producto->update(['stock_reservado' => $producto->stock_reservado + $productoVenta['cantidad']]);

                $producto->save();
                DB::commit();

            }

        } catch (\Exception $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            throw new \Exception($th->getMessage());;
        }

        return response()->json(['error' => false]);
    }

    public function getVenta(int $id) {

        try {
            $venta = VentasDetalle::orderBy('id', 'DESC')->with(['producto', 'venta.cliente', 'venta.vendedor', 'venta'])->where('venta_id', $id)->get();
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
        return response()->json(['error' => false, 'data' => $venta]);

    }

    public function editarVenta(Request $request) {
        dd($request);
    }
}
