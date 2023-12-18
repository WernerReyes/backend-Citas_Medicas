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
        Schema::create('medical_appointment_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status');
            $table->string('medical_status')->nullable();
            $table->string('description');
            $table->uuid('user_id');
            $table->uuid('medical_appointment_id');
            $table->uuid('payment_id')->nullable();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->foreign('medical_appointment_id')->references('id')->on('medical_appointments');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_appointment_history');
    }
};
