<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'permissions' => 'array',
    ];

    public function eggProductions()
    {
        return $this->hasMany(EggProduction::class);
    }

    public function feedConsumptions()
    {
        return $this->hasMany(FeedConsumption::class);
    }

    public function mortalities()
    {
        return $this->hasMany(Mortality::class);
    }

    public function permissionsList()
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Helper untuk memeriksa hak akses fitur/menu user
     */
    public function canAccess(string $permissionKey): bool
    {
        return \App\Services\PermissionService::canAccess($this, $permissionKey);
    }
}
