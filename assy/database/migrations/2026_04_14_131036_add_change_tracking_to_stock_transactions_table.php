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
        Schema::table('stock_transactions', function (Blueprint $table) {
            // Add tracking columns
            $table->foreignId('changed_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at')->nullable()->after('changed_by');
        });
        
        // Change status enum to include 'confirmed'
        DB::statement("ALTER TABLE stock_transactions MODIFY COLUMN status ENUM('new', 'changed', 'confirmed') DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status enum
        DB::statement("ALTER TABLE stock_transactions MODIFY COLUMN status ENUM('new', 'changed') DEFAULT 'new'");
        
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['changed_by']);
            $table->dropColumn(['changed_by', 'changed_at']);
        });
    }
};
