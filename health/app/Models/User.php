<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'user_id',
        'email',
        'password',
        'role',
        'role_mtnhealth',
        'supervisor_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role_mtnhealth === 'admin';
    }

    public function getRoleLabel(): string
    {
        return match($this->role_mtnhealth) {
            'admin'      => 'Admin',
            'supervisor' => 'Supervisor',
            'user'       => 'User',
            default      => 'User',
        };
    }

    public function isSupervisor(): bool
    {
        return $this->role_mtnhealth === 'supervisor' || $this->subordinates()->exists();
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function supervisor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function subordinates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }
}
