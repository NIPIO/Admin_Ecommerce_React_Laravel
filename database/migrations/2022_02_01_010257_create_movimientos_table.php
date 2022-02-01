<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('tabla', 50);
            $table->string('tipo_movimiento', 50)->nullable();
            $table->integer('item_id');
            $table->integer('usuario')->nullable();
            $table->string('estado_viejo', 50)->nullable();
            $table->string('estado_nuevo', 50)->nullable();
            $table->integer('diferencia')->nullable();
            $table->string('campo_modificado', 50)->nullable();
            $table->timestamps();
            
            $table->foreign('usuario', 'FK_movimientos_vendedores')->references('id')->on('vendedores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientos');
    }
}
