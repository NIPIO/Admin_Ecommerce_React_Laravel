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

    static function agregarComision($venta) {
        $comision = $venta['tipo_venta'] === 'Minorista' ? $venta['precio_total'] * 0.01 : ($venta['precio_total'] - $venta['costo']) * 0.1;
        Vendedores::whereId($venta['vendedor_id'])->increment('comision', $comision);
    }

}
