<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Roles;

class RolesRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function setRol($req) {
        return Roles::create([
            'nombre' => $req['nombre'],
            'descripcion' => $req['descripcion'],
        ]);
    }
}
