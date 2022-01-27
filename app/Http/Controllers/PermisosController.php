<?php

namespace App\Http\Controllers;

use App\Models\Permisos;
use Illuminate\Http\Request;
use App\Repositories\MovimientosRepository;

class PermisosController extends Controller
{
    private $movimientosRepository;

    public function __construct(MovimientosRepository $movimientosRepository)
    {
        $this->movimientosRepository = $movimientosRepository;    
    }
    
    public function index() {
        return response()->json(['error' => false, 'allPermisos' => Permisos::all(), 'permisosFiltro' => Permisos::orderBy('id', 'DESC')->get()]);
    }
}
