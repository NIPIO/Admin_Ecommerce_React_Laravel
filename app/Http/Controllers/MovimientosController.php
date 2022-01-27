<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Movimientos;
use App\Repositories\IndexRepository;
use Carbon\Carbon;

class MovimientosController extends Controller
{

    private $indexRepository;

    public function __construct(IndexRepository $indexRepository)
    {
        $this->indexRepository = $indexRepository;    
    }

    public function index(ProductosController $productos, VentasController $ventas) {

        $usuario = request()->get('usuario');
        $tipoMovi = request()->get('tipoMovimiento');
        $fechas = request()->get('fechas');
        $seccion = request()->get('seccion');
        
        $movimientos = $this->indexRepository->indexMovimientos($usuario, $tipoMovi, $fechas, $seccion);

        $clientes = Clientes::orderBy('id', 'DESC')->first();
        $ventas = $ventas->getVentasConfirmadas();
        $productos = $productos->getStock();

        return response()->json(['error' => false, 'movimientosFiltro' => $movimientos->get(), 'datosIniciales' => [
            [
                'label' => 'Ventas confirmadas',
                'value' => $ventas
            ], 
            [
                'label' => 'Clientes',
                'value' => $clientes ? $clientes->id : 0
            ],
            [
                'label' => 'Stock',
                'value' => $productos ? $productos[0] : 0
            ],
            [
                'label' => 'Stock reservado',
                'value' => $productos ? $productos[1] : 0
            ],
            [
                'label' => 'En transito',
                'value' => $productos ? $productos[2] : 0
            ],
            [
                'label' => 'En transito reservado',
                'value' => $productos ? $productos[3] : 0
            ],
        ]]);
   }
}

