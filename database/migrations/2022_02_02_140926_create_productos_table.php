<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre');
            $table->integer('marca')->default(0);
            $table->double('precio');
            $table->double('costo');
            $table->integer('stock');
            $table->integer('stock_reservado')->default(0);
            $table->integer('en_transito')->default(0);
            $table->integer('en_transito_reservado')->default(0);
            $table->timestamps();
            $table->boolean('activo')->default(1);
            
            $table->foreign('marca', 'FK_productos_marcas')->references('id')->on('marcas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
}
