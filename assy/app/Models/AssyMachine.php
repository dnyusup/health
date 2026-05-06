<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssyMachine extends Model
{
    protected $table = 'assy_machines';

    protected $fillable = [
        'mach_number',
        'mach_type',
        'mach_area',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
