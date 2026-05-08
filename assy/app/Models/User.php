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
        'role_assypart',
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

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role_assypart === 'admin';
    }

    public function isShopfloor(): bool
    {
        return $this->role_assypart === 'tech_shopfloor';
    }

    public function isWorkshop(): bool
    {
        return $this->role_assypart === 'tech_workshop';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function assypartRoleLabel(): string
    {
        return match($this->role_assypart) {
            'admin'          => 'Admin',
            'tech_shopfloor' => 'Tech Shopfloor',
            'tech_workshop'  => 'Tech Workshop',
            default          => '-',
        };
    }
}
