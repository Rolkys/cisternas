<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
            'password' => 'hashed',
            'fecha_registro' => 'datetime',
        ];
    }

    // Métodos de verificación de roles
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'Administrador' || $this->isRoot();
    }

    public function isRoot(): bool
    {
        return $this->role === 'Root';
    }

    public function isUser(): bool
    {
        return $this->role === 'user' || $this->role === 'Usuario';
    }

    // 👇 ESTE ES EL NUEVO MÉTODO 👇
    public function isOperario(): bool
    {
        return $this->role === 'operario' || $this->role === 'Operario';
    }

    // Permisos de visualización
    public function canView(): bool
    {
        return $this->isUser() || $this->isOperario() || $this->isAdmin() || $this->isRoot();
    }

    // Permisos de creación
    public function canCreate(): bool
    {
        return $this->isAdmin() || $this->isRoot() || $this->isUser();
    }

    // Permisos de eliminación
    public function canDelete(): bool
    {
        return $this->isAdmin() || $this->isRoot();
    }
}