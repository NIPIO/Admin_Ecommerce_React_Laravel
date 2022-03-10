<?php


namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Vendedores;

class LoginRepository implements RepositoryInterface
{
   
    public function __construct() {}

    public function setLogin($req) {
        return Vendedores::where('usuario', $req['usuario'])->where('password', $req['password'])->firstOrFail();
    }

    public function checkUsuarioExistente($req) {
        return Vendedores::where('usuario', $req['usuario'])->first();
    }


}
