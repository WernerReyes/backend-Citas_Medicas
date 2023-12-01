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
            $table->date('fecha');
            $table->string('sede');
            $table->string('estado')->default('pendiente');
            $table->string('descripcion')->nullable();
            $table->uuid('doctor_id');
            $table->uuid('user_id');

            $table->foreign('doctor_id')->references('id')->on('doctors');
            $table->foreign('user_id')->references('id')->on('users');
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
