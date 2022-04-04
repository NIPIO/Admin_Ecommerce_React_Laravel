<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('cliente_id');
            $table->integer('vendedor_id');
            $table->double('precio_total')->nullable();
            $table->double('costo')->default(0);
            $table->double('utilidad')->default(0);
            $table->tinyText('tipo_venta');
            $table->double('vendedor_comision')->nullable();
            $table->integer('cantidad');
            $table->date('fecha_venta')->nullable();
            $table->tinyInteger('activo')->nullable();
            $table->boolean('confirmada')->default(0);
            $table->timestamps();
            
            $table->foreign('cliente_id', 'FK_ventas_clientes')->references('id')->on('clientes');
            $table->foreign('vendedor_id', 'FK_ventas_vendedores')->references('id')->on('vendedores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
}
