<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Marcas;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Roles;
use App\Models\Vendedores;

class ComunRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function chequearSiExiste($type, $nombre) {
        switch ($type) {
            case 'vendedor':
                return count(Vendedores::where('nombre', $nombre)->get()->toArray()) > 0;
                break;
            case 'marca':
                return count(Marcas::where('nombre', $nombre)->get()->toArray()) > 0;
                break;
            case 'proveedor':
                return count(Proveedores::where('nombre', $nombre)->get()->toArray()) > 0;
                break;
            case 'producto':
                return count(Productos::where('nombre', $nombre)->get()->toArray()) > 0;
                break;
            case 'rol':
                return count(Roles::where('nombre', $nombre)->get()->toArray()) > 0;
                break;
            default:
                break;
        }
    }
}
