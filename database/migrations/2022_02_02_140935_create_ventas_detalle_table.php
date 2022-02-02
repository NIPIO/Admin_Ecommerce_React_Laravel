<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas_detalle', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('venta_id');
            $table->integer('producto_id');
            $table->double('precio')->nullable();
            $table->integer('cantidad')->nullable();
            $table->timestamps();
            
            $table->foreign('producto_id', 'FK_ventas_detalle_productos')->references('id')->on('productos');
            $table->foreign('venta_id', 'FK_ventas_detalle_ventas')->references('id')->on('ventas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas_detalle');
    }
}
