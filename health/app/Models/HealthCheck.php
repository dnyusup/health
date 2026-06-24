<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthCheck extends Model
{
    protected $table = 'mtnhealth_health_checks';

    protected $fillable = [
        'user_id',
        'weight',
        'systolic',
        'diastolic',
        'oxygen_saturation',
        'body_temperature',
        'checked_at',
        'notes',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'weight' => 'decimal:2',
        'oxygen_saturation' => 'decimal:2',
        'body_temperature' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBloodPressureAttribute(): string
    {
        if ($this->systolic && $this->diastolic) {
            return "{$this->systolic}/{$this->diastolic} mmHg";
        }
        return '-';
    }

    public function getBloodPressureStatus(): string
    {
        if (!$this->systolic || !$this->diastolic) return 'unknown';
        if ($this->systolic < 120 && $this->diastolic < 80) return 'normal';
        if ($this->systolic < 130 && $this->diastolic < 80) return 'elevated';
        if ($this->systolic < 140 || $this->diastolic < 90) return 'high1';
        return 'high2';
    }

    public function getOxygenStatus(): string
    {
        if (!$this->oxygen_saturation) return 'unknown';
        if ($this->oxygen_saturation >= 95) return 'normal';
        if ($this->oxygen_saturation >= 90) return 'low';
        return 'critical';
    }

    public function getTemperatureStatus(): string
    {
        if (!$this->body_temperature) return 'unknown';
        if ($this->body_temperature < 36.1) return 'low';
        if ($this->body_temperature <= 37.5) return 'normal';
        if ($this->body_temperature <= 38.5) return 'fever';
        return 'high_fever';
    }
}
