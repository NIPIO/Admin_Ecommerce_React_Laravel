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

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function toggleEstado() {
        try {
            $req = request()->all();

            $tabla = null; 
            switch ($req['tabla']) {
                case 'Producto':
                    $tabla = Productos::whereId($req['id']);
                    break;
                case 'Cliente':
                    $tabla = Clientes::whereId($req['id']);
                    break;
                case 'Compra':
                    $tabla = Compras::whereId($req['id']);
                    break;
                case 'Marca':
                    $tabla = Marcas::whereId($req['id']);
                    break;
                case 'Proveedor':
                    $tabla = Proveedores::whereId($req['id']);
                    break;
                case 'Vendedor':
                    $tabla = Vendedores::whereId($req['id']);
                    break;
                case 'Venta':
                    $tabla = Ventas::whereId($req['id']);
                    break;
                case 'Cuenta':
                    $tabla = CtaCte::whereId($req['id']);
                    break;
                default:
                    break;
            };

            $tabla->update([
                'activo' => $req['estado'] === 1 ? 0 : 1
            ]);
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
      
        return response()->json(['error' => false]);
    }

}

