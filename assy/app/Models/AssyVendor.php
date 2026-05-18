<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssyVendor extends Model
{
    protected $table = 'assy_vendors';

    protected $fillable = [
        'vendor_id',
        'vendor_name',
        'pic_vendor',
        'email',
        'telp',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
