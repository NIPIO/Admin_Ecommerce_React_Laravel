<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Productos;
use App\Models\Vendedores;

class VendedoresRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function getVendedor($id) {
        return Vendedores::whereId($id)->first();
    }
    
    public function setVendedor($req) {
        return Vendedores::create([
            'usuario' => $req['usuario'],
            'password' => $req['password'],
            'nombre' => $req['nombre'],
            'rol_id' => 2,
        ]);
    }

    public function updateVendedor($req) {
        $vendedor = $this->getVendedor($req['id']);

        $vendedor->update([
            "nombre" => $req['nombre'],
            "email" => $req['email'],
            "telefono" => $req['telefono'],
            "rol_id" => $req['rol'],
        ]);

        return $vendedor;
    }

}
