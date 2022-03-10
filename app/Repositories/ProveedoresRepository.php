<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Proveedores;

class ProveedoresRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function getProveedor($id) {
        return Proveedores::whereId($id)->first();
    }

    public function setProveedor($req) {
        return Proveedores::create([
            'nombre' => $req['nombre'],
        ]);
    }

    public function updateProveedor($req) {
        $proveedor = $this->getProveedor($req['id']);

        $nombreAnterior = $proveedor->nombre;

        $proveedor->update([
            "nombre" => $req['nombre'],
        ]);

        return ['nombreAnterior' => $nombreAnterior, 'proveedor' => $proveedor];
    }
}

