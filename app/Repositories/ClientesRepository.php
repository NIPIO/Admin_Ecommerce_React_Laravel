<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Clientes;

class ClientesRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function getClientes() {
        return Clientes::all();
    }

    public function setCliente($req) {
        return Clientes::create([
            'nombre' => $req['nombre'],
            'telefono' => isset($req['telefono']) ? $req['telefono'] : null,
            'email' => isset($req['email']) ? $req['email'] : null,
        ]);
    }

    public function updateCliente($cliente, $req) {
        return $cliente->update([
            "nombre" => $req['nombre'],
            "email" => $req['email'],
            "telefono" => $req['telefono'],
        ]);
    }
}
