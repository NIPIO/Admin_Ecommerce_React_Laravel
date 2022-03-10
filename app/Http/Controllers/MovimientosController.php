<?php

namespace App\Http\Controllers;

use App\Repositories\ClientesRepository;
use App\Repositories\IndexRepository;
use App\Repositories\ProductosRepository;
use App\Repositories\VentasRepository;

class MovimientosController extends Controller
{

    private $indexRepository;
    private $clientesRepository;
    private $ventasRepository;
    private $productosRepository;

    public function __construct(IndexRepository $indexRepository, ClientesRepository $clientesRepository, ProductosRepository $productosRepository, VentasRepository $ventasRepository)
    {
        $this->indexRepository = $indexRepository;    
        $this->clientesRepository = $clientesRepository;    
        $this->ventasRepository = $ventasRepository;    
        $this->productosRepository = $productosRepository;    
    }

    public function index() {

        $req = request()->all();
        
        $movimientos = $this->indexRepository->indexMovimientos($req);
        return response()->json(['error' => false, 'movimientosFiltro' => $movimientos->get(), 'datosIniciales' => [
            [
                'label' => 'Ventas confirmadas',
                'value' => count($this->ventasRepository->getVentasConfirmadas())
            ], 
            [
                'label' => 'Clientes',
                'value' => count($this->clientesRepository->getClientes())
            ],
            [
                'label' => 'Stock',
                'value' => $this->productosRepository->getStockTotal()[0]
            ],
            [
                'label' => 'Stock reservado',
                'value' => $this->productosRepository->getStockTotal()[1]
            ],
            [
                'label' => 'En transito',
                'value' => $this->productosRepository->getStockTotal()[2]
            ],
            [
                'label' => 'En transito reservado',
                'value' => $this->productosRepository->getStockTotal()[3]
            ],
        ]]);
   }
}

