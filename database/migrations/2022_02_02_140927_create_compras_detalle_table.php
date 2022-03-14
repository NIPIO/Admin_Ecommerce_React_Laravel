<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras_detalle', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('compra_id');
            $table->integer('producto_id');
            $table->double('costo')->nullable();
            $table->integer('cantidad')->nullable();
            $table->timestamps();
            
            $table->foreign('compra_id', 'FK_compras_detalle_compras')->references('id')->on('compras');
            $table->foreign('producto_id', 'FK_compras_detalle_productos')->references('id')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compras_detalle');
    }
}
