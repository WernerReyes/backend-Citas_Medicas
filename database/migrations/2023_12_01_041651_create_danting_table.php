<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('danting', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('estado')->default('pendiente');
            $table->date('fecha_cambio_estado');
            $table->uuid('pago_id');
            $table->uuid('cita_id');

            $table->foreign('cita_id')->references('id')->on('medical_appointments');
            $table->foreign('pago_id')->references('id')->on('payments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danting');
    }
};
