<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentasCorrientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentas_corrientes', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('proveedor_id')->nullable();
            $table->integer('cliente_id')->nullable();
            $table->integer('saldo')->nullable();
            $table->char('tipo_cuenta', 50);
            $table->boolean('activo')->default(1);
            $table->timestamps();
            
            $table->foreign('cliente_id', 'FK_cuentas_corrientes_clientes')->references('id')->on('clientes');
            $table->foreign('proveedor_id', 'FK_cuentas_corrientes_proveedores')->references('id')->on('proveedores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentas_corrientes');
    }
}
