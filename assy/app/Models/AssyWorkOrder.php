<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssyWorkOrder extends Model
{
    protected $table = 'assy_work_orders';

    protected $fillable = [
        'tanggal_bongkar',
        'order_number',
        'order_type',
        'machine_id',
        'mach_number',
        'mach_type',
        'pos',
        'part_id',
        'part_name',
        'category',
        'part_detail',
        'kerusakan',
        'pic_bongkar',
        'remark',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_bongkar' => 'date',
    ];

    public function machine()
    {
        return $this->belongsTo(AssyMachine::class, 'machine_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_bongkar');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
