<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('tipo_movimiento', 50);
            $table->string('tipo_caja', 10);
            $table->integer('item_id')->nullable();
            $table->double('importe')->default(0);
            $table->integer('usuario')->nullable();
            $table->string('observacion', 250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caja');
    }
}
