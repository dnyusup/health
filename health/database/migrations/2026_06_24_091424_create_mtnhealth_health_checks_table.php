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
        Schema::create('mtnhealth_health_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('weight', 5, 2)->nullable()->comment('Berat Badan (kg)');
            $table->unsignedSmallInteger('systolic')->nullable()->comment('Tensi Darah Sistolik (mmHg)');
            $table->unsignedSmallInteger('diastolic')->nullable()->comment('Tensi Darah Diastolik (mmHg)');
            $table->decimal('oxygen_saturation', 5, 2)->nullable()->comment('Saturasi Oksigen SpO2 (%)');
            $table->decimal('body_temperature', 4, 2)->nullable()->comment('Temperatur Badan (°C)');
            $table->dateTime('checked_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtnhealth_health_checks');
    }
};
