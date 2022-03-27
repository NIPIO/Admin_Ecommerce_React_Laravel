<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendedores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre')->unique('nombre');
            $table->string('email')->nullable();
            $table->string('usuario', 50)->unique('usuario');
            $table->string('password', 50);
            $table->string('telefono')->nullable();
            $table->double('comision')->default(0);
            $table->integer('rol_id')->nullable();
            $table->timestamps();
            $table->boolean('activo')->default(1);
            
            $table->foreign('rol_id', 'FK_vendedores_roles')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendedores');
    }
}
