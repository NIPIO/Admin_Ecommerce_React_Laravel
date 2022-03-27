<?php

namespace Database\Seeders;

use App\Models\Clientes;
use App\Models\FormatoDato;
use App\Models\FormatoPlantilla;
use App\Models\Marcas;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Roles;
use App\Models\SeparadorPlantilla;
use App\Models\Vendedores;
use Illuminate\Database\Seeder;

class InitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'id' => 1,
                'nombre' => 'Admin',
                'descripcion' => 'Acceso a todos los items del menu',
            ],
            [
                'id' => 2,
                'nombre' => 'Vendedor',
                'descripcion' => "Acceso a todo menos 'Roles y permisos', 'Caja', 'Compras' y 'Movimientos'",
            ],
            [
                'id' => 3,
                'nombre' => 'Otro',
                'descripcion' => 'Customizar',
            ],

        ];

        foreach ($items as $item) {
            Roles::create($item);
        }


        Vendedores::create([
            'id' => 1,
            'nombre' => 'harry',
            'usuario' => 'harry',
            'email' => 'dielectronics@gmail.com',
            'password' => 'rivercapo',
            'telefono' => '44339966',
            'comision' => 0,
            'rol_id' => 1,
            'activo' => 1
        ]);

        Marcas::create([
            'id' => 1,
            'nombre' => 'Samsung',
            'activo' => 1,
        ]);

        Productos::create([
            'id' => 1,
            'nombre' => 'J7',
            'marca' => 1,
            'costo' => 0,
            'stock' => 0,
            'stock_reservado' => 0,
            'en_transito' => 0,
            'en_transito_reservado' => 0,
            'activo' => 1
        ]);

        Proveedores::create([
            'id' => 1,
            'nombre' => 'Proveedor 1',
            'activo' => 1,
        ]);


        Clientes::create([
            'id' => 1,
            'nombre' => 'Clientes 1',
            'activo' => 1,
        ]);
    }
}
