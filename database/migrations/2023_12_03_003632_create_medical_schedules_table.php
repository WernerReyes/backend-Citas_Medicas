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
        Schema::create('medical_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->uuid('doctor_id');
            $table->boolean('disponible')->default(true);
            $table->boolean('activo')->default(true);

            $table->foreign('doctor_id')->references('id')->on('doctors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_schedules');
    }
};
