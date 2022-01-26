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
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ToggleController extends Controller
{
    private $movimientosController;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
    }

    public function toggleEstado(VentasController $ventasController, ComprasController $comprasController) {
        try {
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

            $tabla->update([
                'activo' => $req['estado'] === 1 ? 0 : 1
            ]);

            $this->movimientosController->guardarMovimiento(
                $req['tabla'], 'ESTADO', $req['usuario'], $req['id'], $req['estado'] === 1 ? 'activo' : 'inactivo', $req['estado'] === 0 ? 'activo' : 'inactivo'
            );

            if ($req['tabla'] === 'compras') {
                $comprasController->actualizarStock($tabla->first(), $req['estado']);
            } elseif ($req['tabla'] === 'ventas') {
                $ventasController->actualizarStock($tabla->first(), $req['estado']);
            }

        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());
        }
      
        return response()->json(['error' => false]);
    }

}

