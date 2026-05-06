<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssyPart extends Model
{
    protected $table = 'assy_parts';

    protected $fillable = [
        'part_id',
        'category',
        'part_name',
        'part_detail',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
