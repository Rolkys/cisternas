<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cisternas', function (Blueprint $table) {
            $table->id('IdCisterna');
            $table->integer('OF');
            $table->integer('NumeroCisterna');
            $table->string('Origen')->nullable();
            $table->string('Destino')->nullable();
            $table->string('Matricula')->nullable();
            $table->string('MatriculaCisterna')->nullable();
            $table->string('Conductor');
            $table->string('Telefono')->nullable();
            $table->string('Transporte')->nullable();
            $table->dateTime('FechaFabricacionHuelva')->nullable();
            $table->dateTime('HoraSalida')->nullable();
            $table->dateTime('FechaEntradaMG')->nullable();
            $table->dateTime('HoraLlegadaEstimada')->nullable();
            $table->dateTime('FechaConsumoMG')->nullable();
            $table->dateTime('HoraEstimadaConsumoL1')->nullable();
            $table->dateTime('HoraEstimadaConsumoL2')->nullable();
            $table->dateTime('HoraRealConsumoL1')->nullable();
            $table->dateTime('HoraRealConsumoL2')->nullable();
            $table->boolean('GlobalGAP')->nullable();
            $table->boolean('FDA')->nullable();
            $table->text('Observaciones')->nullable();
            $table->text('Incidencias')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cisternas');
    }
};