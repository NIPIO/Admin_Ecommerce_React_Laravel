<?php

namespace App\Http\Controllers;

use App\Models\Permisos;
use Illuminate\Http\Request;

class PermisosController extends Controller
{
    public function index() {
        return response()->json(['error' => false, 'allPermisos' => Permisos::all(), 'permisosFiltro' => Permisos::orderBy('id', 'DESC')->get()]);
    }

}
