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
        Schema::create('doctors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('dni')->unique();
            $table->string('correo')->unique();
            $table->string('password');
            $table->string('direccion')->nullable();
            $table->string('telefono');
            $table->string('img')->nullable();
            $table->unsignedBigInteger('rol_id')->default(3);
            $table->uuid('especialidad_id');

            $table->foreign('rol_id')->references('id')->on('rols');
            $table->foreign('especialidad_id')->references('id')->on('specialties');
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
