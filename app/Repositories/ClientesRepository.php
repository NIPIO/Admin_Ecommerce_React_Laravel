<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Clientes;

class ClientesRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function nuevoCliente($req) {
        return Clientes::create([
            'nombre' => $req['nombre'],
            'telefono' => isset($req['telefono']) ? $req['telefono'] : null,
            'email' => isset($req['email']) ? $req['email'] : null,
        ]);
    }

    public function editarCliente($cliente, $req) {
        return $cliente->update([
            "nombre" => $req['nombre'],
            "email" => $req['email'],
            "telefono" => $req['telefono'],
        ]);
    }
}
