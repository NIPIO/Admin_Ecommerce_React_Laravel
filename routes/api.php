<?php

use App\Http\Controllers\CajaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\ToggleController;
use App\Http\Controllers\CtaCteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\VendedoresController;
use App\Http\Controllers\MarcasController;
use App\Http\Controllers\MovimientosController;
use App\Http\Controllers\PermisosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\VentasController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/', [ProductosController::class, 'index']);

Route::prefix('')->group(function () {
    Route::get('productos', [ProductosController::class, 'index']);
    Route::post('producto', [ProductosController::class, 'nuevoProducto']);
    Route::put('producto/{id}', [ProductosController::class, 'editarProducto']);

    Route::get('ventas', [VentasController::class, 'index']);
    Route::get('venta/{id}', [VentasController::class, 'getVenta']);
    Route::post('venta', [VentasController::class, 'nuevaVenta']);
    Route::put('venta/{id}', [VentasController::class, 'editarVenta']);
    Route::post('confirmarVenta', [VentasController::class, 'confirmarVenta']);
    Route::delete('venta/{id}', [VentasController::class, 'borrarVenta']);

    Route::get('compras', [ComprasController::class, 'index']);
    Route::get('compra/{id}', [ComprasController::class, 'verCompra']);
    Route::post('compra', [ComprasController::class, 'nuevaCompra']);
    Route::put('compra/{id}', [ComprasController::class, 'editarCompra']);
    Route::post('confirmarCompra', [ComprasController::class, 'confirmarCompra']);
    Route::delete('compra/{id}', [ComprasController::class, 'borrarCompra']);
    
    Route::get('marcas', [MarcasController::class, 'index']);
    Route::post('marca', [MarcasController::class, 'nuevaMarca']);
    Route::put('marca/{id}', [MarcasController::class, 'editarMarca']);
    
    Route::get('proveedores', [ProveedoresController::class, 'index']);
    Route::post('proveedor', [ProveedoresController::class, 'nuevoProveedor']);
    Route::put('proveedor/{id}', [ProveedoresController::class, 'editarProveedor']);

    
    Route::get('vendedores', [VendedoresController::class, 'index']);
    Route::put('vendedor/{id}', [VendedoresController::class, 'editarVendedor']);
    
    Route::get('clientes', [ClientesController::class, 'index']);
    Route::post('cliente', [ClientesController::class, 'nuevoCliente']);
    Route::put('cliente/{id}', [ClientesController::class, 'editarCliente']);
    
    Route::get('cuentas-corrientes', [CtaCteController::class, 'index']);
    Route::post('cuentas-corrientes', [CtaCteController::class, 'nuevaCtaCte']);
    Route::put('cuentas-corrientes/{id}', [CtaCteController::class, 'editarCuenta']);
    Route::get('cuentas-corrientes/{id}', [CtaCteController::class, 'verDetalleCuenta']);

    Route::get('roles', [RolesController::class, 'index']);
    Route::get('permisos', [PermisosController::class, 'index']);
    Route::post('roles', [RolesController::class, 'nuevoRol']);

    Route::get('caja', [CajaController::class, 'index']);
    Route::post('caja', [CajaController::class, 'nuevaCaja']);

    Route::get('movimientos', [MovimientosController::class, 'index']);

    Route::post('login', [LoginController::class, 'login']);
    Route::post('registro', [LoginController::class, 'registro']);

    Route::patch('toggleEstado/{id}', [ToggleController::class, 'toggleEstado']);
    




});