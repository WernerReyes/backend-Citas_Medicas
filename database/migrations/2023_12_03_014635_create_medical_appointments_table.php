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
        Schema::create('medical_appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fecha');
            $table->string('sede');
            $table->string('estado')->default('pendiente');
            $table->string('estado_medico')->nullable();
            // $table->unsignedBigInteger('rate_appointment_id')->default(1);
            $table->uuid('paciente_id');
            $table->uuid('doctor_id');
            $table->uuid('schedule_id');
            $table->boolean('activo')->default(true);

            $table->foreign('paciente_id')->references('id')->on('users');
            $table->foreign('doctor_id')->references('id')->on('doctors');
            $table->foreign('schedule_id')->references('id')->on('medical_schedules');
            // $table->foreign('rate_appointment_id')->references('id')->on('rates_appointments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_appointments');
    }
};
