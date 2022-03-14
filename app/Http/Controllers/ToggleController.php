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

            $validacion = $this->validarCambioEstado($req, $tabla);
            
            // Capturo la validación y devuelvo el mensaje al front.
            if ($validacion['error']) {
                switch ($validacion['tipo']) {
                    case 'Stock':
                        return response()->json(['error' => true, 'data' => 'No podes modificar un producto/marca que tenga stock disponible']);
                    case 'Reserva':
                        return response()->json(['error' => true, 'data' => 'No podes modificar un producto/marca que tenga alguna reserva activa']);
                    case 'Confirmada':
                        return response()->json(['error' => true, 'data' => 'No podes modificar un item confirmado']);
                    default:
                        break;
                }
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

    private function validarCambioEstado($req, $tabla) {
        $respuesta = ['error' => false, 'tipo' => null];

        //Si es compra o venta: 1- no puede cancelar/activar si está confirmada. 2- tengo que chequear que haya stock cuando reactiva venta
        if ($req['tabla'] === 'ventas' || $req['tabla'] === 'compras') {
            $respuesta = $this->administrarSiEsCompraVenta($req, $tabla);
        }
        //Si desactiva una marca o un producto, tiene que tener sí o sí 0 en stock, 0 reservado, 0 en transito
        if ($req['tabla'] === 'productos' || $req['tabla'] === 'marcas') {
            // Solamente necesito validar si quiere desactivar, si quiere activar debería estar todo en 0.
            if($req['estado'] === 1) {
                $respuesta = $this->validarSiProductoMarcaTieneStock($req);
            }
        }

        return $respuesta;
    }

    private function validarSiProductoMarcaTieneStock($req) {
            $productosDeEsaMarca = Productos::where($req['tabla'] === 'productos' ? 'id' : 'marca', '=', $req['id'])->get()->toArray();

            // Verifico que haya productos de esa marca (puede estar creada la marca pero sin producto)
            if (count($productosDeEsaMarca)) {
                if (array_sum(array_column($productosDeEsaMarca, 'stock'))) {
                    return ['error' => true, 'tipo' => 'Stock'];
                } 
                if (array_sum(array_column($productosDeEsaMarca, 'stock_reservado')) + 
                array_sum(array_column($productosDeEsaMarca, 'en_transito')) + 
                array_sum(array_column($productosDeEsaMarca, 'en_transito_reservado')) > 0) {
                    return ['error' => true, 'tipo' => 'Reserva'];
                }
            }   
            return ['error' => false, 'tipo' => null];
    }

    private function administrarSiEsCompraVenta($req, $tabla) {
        if ($tabla->first()->confirmada === 1) return ['error' => true, 'tipo' => 'Confirmada'];

        if ($req['tabla'] === 'compras') {
            return $this->stockRepository->actualizarStockCompras($tabla->first(), $req['estado']);
        } elseif ($req['tabla'] === 'ventas') {
            return $this->stockRepository->actualizarStockVentas($tabla->first(), $req['estado']);
        }

    }


}

