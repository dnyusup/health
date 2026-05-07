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
        Schema::table('assy_work_orders', function (Blueprint $table) {
            $table->date('tanggal_assembling')->nullable()->after('remark');
            $table->text('action_assembling')->nullable()->after('tanggal_assembling');
            $table->json('pic_assembling')->nullable()->after('action_assembling');
            $table->text('remark_assembling')->nullable()->after('pic_assembling');
            $table->string('foto_kerusakan')->nullable()->after('remark_assembling');
            $table->foreignId('repaired_by')->nullable()->constrained('users')->nullOnDelete()->after('foto_kerusakan');
            $table->timestamp('repaired_at')->nullable()->after('repaired_by');
        });
    }

    public function down(): void
    {
        Schema::table('assy_work_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('repaired_by');
            $table->dropColumn([
                'tanggal_assembling', 'action_assembling', 'pic_assembling',
                'remark_assembling', 'foto_kerusakan', 'repaired_at',
            ]);
        });
    }
};
