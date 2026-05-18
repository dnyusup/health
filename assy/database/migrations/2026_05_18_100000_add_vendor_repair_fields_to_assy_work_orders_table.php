<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assy_work_orders', function (Blueprint $table) {
            $table->boolean('repair_by_vendor')->default(false)->after('remark_assembling');
            $table->foreignId('repair_vendor_id')->nullable()->constrained('assy_vendors')->nullOnDelete()->after('repair_by_vendor');
            $table->string('po_number', 100)->nullable()->after('repair_vendor_id');
        });
    }

    public function down(): void
    {
        Schema::table('assy_work_orders', function (Blueprint $table) {
            $table->dropForeign(['repair_vendor_id']);
            $table->dropColumn(['repair_by_vendor', 'repair_vendor_id', 'po_number']);
        });
    }
};
