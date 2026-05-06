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
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->string('material_code')->unique();
            $table->string('bin_location')->nullable();
            $table->string('old_material_no')->nullable();
            $table->string('description');
            $table->decimal('stock', 15, 2)->default(0);
            $table->string('unit')->default('PC');
            $table->decimal('min_stock', 15, 2)->default(0);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spareparts');
    }
};
