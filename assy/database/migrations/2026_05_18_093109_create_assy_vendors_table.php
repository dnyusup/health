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
        Schema::create('assy_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_id', 50)->unique();
            $table->string('vendor_name', 150);
            $table->string('pic_vendor', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telp', 50)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assy_vendors');
    }
};
