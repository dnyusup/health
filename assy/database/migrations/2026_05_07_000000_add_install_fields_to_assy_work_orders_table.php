<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assy_work_orders', function (Blueprint $table) {
            $table->date('tanggal_pasang')->nullable()->after('repaired_at');
            $table->unsignedBigInteger('install_machine_id')->nullable()->after('tanggal_pasang');
            $table->string('install_mach_number')->nullable()->after('install_machine_id');
            $table->string('install_mach_type')->nullable()->after('install_mach_number');
            $table->string('install_pos')->nullable()->after('install_mach_type');
            $table->json('pic_pasang')->nullable()->after('install_pos');
            $table->text('remark_pemasangan')->nullable()->after('pic_pasang');
            $table->unsignedBigInteger('installed_by')->nullable()->after('remark_pemasangan');
            $table->timestamp('installed_at')->nullable()->after('installed_by');
        });
    }

    public function down(): void
    {
        Schema::table('assy_work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_pasang',
                'install_machine_id',
                'install_mach_number',
                'install_mach_type',
                'install_pos',
                'pic_pasang',
                'remark_pemasangan',
                'installed_by',
                'installed_at',
            ]);
        });
    }
};
