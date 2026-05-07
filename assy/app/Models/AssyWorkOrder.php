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
        // assembling fields
        'tanggal_assembling',
        'action_assembling',
        'pic_assembling',
        'remark_assembling',
        'foto_kerusakan',
        'repaired_by',
        'repaired_at',
    ];

    protected $casts = [
        'tanggal_bongkar'    => 'date',
        'tanggal_assembling' => 'date',
        'pic_assembling'     => 'array',
        'repaired_at'        => 'datetime',
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

    public function repairedBy()
    {
        return $this->belongsTo(User::class, 'repaired_by');
    }
}
