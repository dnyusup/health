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
        Schema::create('assy_work_orders', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_bongkar');
            $table->string('order_number', 50)->nullable();
            $table->string('order_type', 10); // ZSPM, ZSBM
            $table->foreignId('machine_id')->nullable()->constrained('assy_machines')->nullOnDelete();
            $table->string('mach_number', 50); // snapshot
            $table->string('mach_type', 100);  // snapshot
            $table->string('pos', 50)->nullable();
            $table->string('part_id', 50)->nullable();
            $table->string('part_name', 255)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('part_detail', 255)->nullable();
            $table->string('kerusakan', 255)->nullable();
            $table->foreignId('pic_bongkar')->nullable()->constrained('users')->nullOnDelete();
            $table->string('remark', 500)->nullable();
            $table->string('status', 20)->default('Open');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assy_work_orders');
    }
};
