<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;

class CamposEditadosRepository implements RepositoryInterface 
{

    public function buscarCamposEditados($tipo, $req) {
        $tipo = $tipo->first();

        if (isset($tipo->nombre) && ($tipo->nombre !== $req['nombre'])) {
            return ['nombre', $tipo->nombre];
        }
        if (isset($tipo->email) && ($tipo->email !== $req['email'])) {
            return ['email', $tipo->email];
        }
        if (isset($tipo->telefono) && ($tipo->telefono !== $req['telefono'])) {
            return ['telefono', $tipo->telefono];
        }
        if (isset($tipo->marca) && ($tipo->marca !== $req['marca'])) {
            return ['marca', $tipo->marca];
        }
        if (isset($tipo->stock) && ($tipo->stock !== $req['stock'])) {
            return ['stock', $tipo->stock];
        }
        if (isset($tipo->precio) && ($tipo->precio !== $req['precio'])) {
            return ['precio', $tipo->precio];
        }
        if (isset($tipo->rol_id) && ($tipo->rol_id !== $req['rol_id'])) {
            return ['rol', $tipo->rol_id];
        }
    }

}