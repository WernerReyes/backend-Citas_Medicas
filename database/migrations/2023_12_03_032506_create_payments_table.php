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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tipo_pago')->default('tarjeta');
            $table->uuid('medical_appointment_id');
            $table->unsignedBigInteger('rate_appointment_id')->default(1);

            $table->foreign('medical_appointment_id')->references('id')->on('medical_appointments');
            $table->foreign('rate_appointment_id')->references('id')->on('rates_appointments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
