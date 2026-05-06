<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum to include 'canceled'
        DB::statement("ALTER TABLE stock_transactions MODIFY COLUMN status ENUM('new', 'changed', 'confirmed', 'canceled') DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (without 'canceled')
        DB::statement("ALTER TABLE stock_transactions MODIFY COLUMN status ENUM('new', 'changed', 'confirmed') DEFAULT 'new'");
    }
};
