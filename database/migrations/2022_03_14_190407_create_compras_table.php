<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('proveedor_id');
            $table->integer('cantidad')->nullable();
            $table->double('costo')->default(0);
            $table->date('fecha_compra')->nullable();
            $table->tinyText('tipo_caja');
            $table->boolean('activo')->default(1);
            $table->boolean('confirmada')->default(0);
            $table->timestamps();
            
            $table->foreign('proveedor_id', 'FK_compras_proveedores')->references('id')->on('proveedores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compras');
    }
}
