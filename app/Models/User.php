<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'fecha_registro',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'fecha_registro' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    //Equivalente a User.IsInRole("Root")
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    //Equivalente a [Authorize(Roles = "Root,Administrador")]
    public function isAdmin(): bool
    {
        return in_array($this->role, ['Root', 'Administrador']);
    }

}