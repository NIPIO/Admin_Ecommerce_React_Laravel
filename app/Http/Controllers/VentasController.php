<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CtaCte;
use App\Models\Productos;
use App\Models\Ventas;
use App\Models\VentasDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class VentasController extends Controller
{
    private $movimientosController;
    public $fecha;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->fecha = Carbon::now()->format('Y-m-d');
        $this->movimientosController = $movimientosController;    
    }

    public function index() {
        $cliente = request()->get('cliente');
        $vendedor = request()->get('vendedor');
        $fechas = request()->get('fechas');
        $producto = request()->get('producto');

        $ventas = Ventas::orderBy('id', 'DESC')->with(['cliente', 'vendedor']);
        if ($cliente) {
            $ventas->whereClienteId((int) $cliente);
        }
        if ($vendedor) {
            $ventas->whereVendedorId((int) $vendedor);
        }
        if ($producto) {
            $ventas->whereHas('detalleVenta', function($innerQuery) use ($producto) {
                $innerQuery->where('producto_id', (int) $producto);
            });
        }
        if ($fechas) {
            $ventas->whereBetween('fecha_venta', [Carbon::parse(substr($fechas[0], 1, -1))->format('Y-m-d'), Carbon::parse(substr($fechas[1], 1, -1))->format('Y-m-d')]);
        } else {
            $ventas->whereDate('fecha_venta', '<=' ,$this->fecha);
        }

        return response()->json(['error' => false, 'allVentas' => Ventas::all(), 'ventasFiltro' => $ventas->get()]);
    }

    public function nuevaVenta(Request $request) {
        $req = $request->all();
        $req = $req['data'];
        // $usuario = $req['usuario'];
        DB::beginTransaction();
        try {

            $venta = Ventas::create([
                'cliente_id' => $req['cliente'],
                'vendedor_id' => $req['vendedor'],
                'cantidad' => array_sum(array_column($req['productos'], 'cantidad')),
                'precio_total' => 0,
                'fecha_venta' => Carbon::now()->format('Y-m-d'),
            ]);
            
            DB::commit();
            $totalPrecioVenta = 0;
            foreach ($req['productos'] as $ventaDetalleRow) {

                VentasDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $ventaDetalleRow['producto'],
                    'cantidad' => $ventaDetalleRow['cantidad'],
                    'precio' => $ventaDetalleRow['precioUnitario']
                ]);

                //el total es para sumar cantidad producto por precio prodcuto. Al final cuando grabo en compra sumo todo de todods. Despues elimino este campo no lo preciso
                $totalPrecioVenta += $ventaDetalleRow['cantidad'] * $ventaDetalleRow['precioUnitario'];

                //pongo en stock en transito las nuevas compras
                $productoAEditar = Productos::whereId($ventaDetalleRow['producto'])->first()->toArray();

                $productoAEditar = Productos::whereId($ventaDetalleRow['producto'])->update([
                        "stock_reservado" => $productoAEditar['stock_reservado'] + $ventaDetalleRow['cantidad']
                ]);;
                
                DB::commit();
    
            }

            Ventas::whereId($venta->id)->update([
                "precio_total" => $totalPrecioVenta,
                "vendedor_comision" => ($totalPrecioVenta * 0.01)
            ]);

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['status' => 200]);
    }

    public function getVenta (int $id) {
        return response()->json(['error' => false, 'venta' => Ventas::whereId($id)->with(['detalleVenta', 'detalleVenta.producto'])->get()->toArray()]);
    }

    public function confirmarVenta (Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        DB::beginTransaction();
        try {
            //El saldo abonado en la compra.
            $venta = Ventas::whereId($req['id']);
            $cliente = CtaCte::where('proveedor_id', $venta->first()->proveedor_id)->first();

            if ($req['diferencia'] <> 0) {
                if (is_null($cliente)) {
                    return response()->json(['error' => true, 'data' => 'Corrobore que el cliente tenga una cuenta corriente abierta']);
                } else {
                    //Actualizo la cuenta corriente con el proveedor
                    $cliente = CtaCte::where('proveedor_id', $venta->first()->proveedor_id)->first();
    
                    $saldoProveedor = $cliente->saldo;
                    CtaCte::where('proveedor_id', $venta->first()->proveedor_id)->update([
                        'saldo' => $saldoProveedor + $req['diferencia']
                    ]);
    
                    DB::commit();
                }
            }

            $venta->update([
                'precio_abonado' => $req['pago'],
                'confirmada' => true,
            ]);
            DB::commit();

            //grabo el egreso en la caja
            Caja::create([
                'tipo_movimiento' => 'VENTA',
                'item_id' => $req['id'],
                'importe' => $req['pago'],
                'usuario' => $usuario
            ]);

            //Por ultimo paso el stock de la compra en tranisto a stock
            $ventaDetalle = VentasDetalle::whereVentaId($req['id'])->get()->toArray();

            foreach ($ventaDetalle as $value) {
                //Obtengo los productos y actualizo su stock
                $producto = Productos::whereId($value['producto_id'])->first();

                Productos::whereId($value['producto_id'])->update([
                    'stock_reservado' => $producto->stock_reservado - $value['cantidad'],
                    'stock' => $producto->stock - $value['cantidad']
                ]);
                
                DB::commit();
            }
            $this->movimientosController->guardarMovimiento(
                'ventas', 'CONFIRMACION', $usuario, $req['id'], null, null, $req['diferencia'], null
            );

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }

        return response()->json(['error' => false]);
    }

    public function getVentasConfirmadas() {
        return Ventas::where('confirmada', true)->get()->count();
    }
}
