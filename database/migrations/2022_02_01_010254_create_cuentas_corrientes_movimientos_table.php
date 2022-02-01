<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentasCorrientesMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentas_corrientes_movimientos', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('proveedor_id');
            $table->integer('cantidad_pago')->nullable();
            $table->integer('cuenta_corriente_id')->nullable();
            $table->integer('fecha')->nullable();
            $table->integer('productos')->nullable();
            
            $table->foreign('proveedor_id', 'FK_cuentas_corrientes_moviemientos_proveedores')->references('id')->on('proveedores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentas_corrientes_movimientos');
    }
}
