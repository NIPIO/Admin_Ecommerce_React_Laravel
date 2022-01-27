<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Compras;
use App\Models\CtaCte;
use App\Models\Marcas;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Vendedores;
use App\Models\Ventas;
use App\Repositories\StockRepository;
use App\Repositories\MovimientosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ToggleController extends Controller
{
    private $movimientosRepository;
    private $stockRepository;

    public function __construct(MovimientosRepository $movimientosRepository, StockRepository $stockRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
        $this->stockRepository = $stockRepository;    
    }

    public function toggleEstado() {
        try {

            DB::beginTransaction();

            $req = request()->all();
            $tabla = null; 
            switch ($req['tabla']) {
                case 'productos':
                    $tabla = Productos::whereId($req['id']);
                    break;
                case 'clientes':
                    $tabla = Clientes::whereId($req['id']);
                    break;
                case 'compras':
                    $tabla = Compras::whereId($req['id']);
                    break;
                case 'marcas':
                    $tabla = Marcas::whereId($req['id']);
                    break;
                case 'proveedores':
                    $tabla = Proveedores::whereId($req['id']);
                    break;
                case 'vendedores':
                    $tabla = Vendedores::whereId($req['id']);
                    break;
                case 'ventas':
                    $tabla = Ventas::whereId($req['id']);
                    break;
                case 'cuentas_corrientes':
                    $tabla = CtaCte::whereId($req['id']);
                    break;
                default:
                    break;
            };

            //Si es compra o venta: 1- no puede cancelar/activar si está confirmada. 2- tengo que chequear que haya stock cuando reactiva venta
            if ($req['tabla'] === 'ventas' || $req['tabla'] === 'compras') {
                $respuesta = $this->administrarSiEsCompraVenta($req, $tabla);

                if ($respuesta === 'Confirmada') return response()->json(['error' => true, 'data' => 'No podes modificar un item confirmado']);
                if ($respuesta === 'Sin stock') return response()->json(['error' => true, 'data' => 'Ya no hay suficiente stock de algún producto de esta venta para reactivarla']); 
            }

            $tabla->update([
                'activo' => $req['estado'] === 1 ? 0 : 1
            ]);

            $this->movimientosRepository->guardarMovimiento(
                $req['tabla'], 'ESTADO', $req['usuario'], $req['id'], $req['estado'] === 1 ? 'activo' : 'inactivo', $req['estado'] === 0 ? 'activo' : 'inactivo'
            );

            DB::commit();

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
            return response()->json(['error' => true, 'data' => $e->getMessage()]);
        }
      
        return response()->json(['error' => false]);
    }

    private function administrarSiEsCompraVenta($req, $tabla) {
        if ($tabla->first()->confirmada === 1) return 'Confirmada'; 

        if ($req['tabla'] === 'compras') {
            return $this->stockRepository->actualizarStockCompras($tabla->first(), $req['estado']);
        } elseif ($req['tabla'] === 'ventas') {
            return $this->stockRepository->actualizarStockVentas($tabla->first(), $req['estado']);
        }

    }

}

