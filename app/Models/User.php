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

    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_registro' => 'datetime',
    ];

    // Métodos de verificación de roles
    /**
     * Indica si el usuario tiene permisos de administrador.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'Administrador' || $this->isRoot();
    }

    /**
     * Indica si el usuario tiene rol Root.
     */
    public function isRoot(): bool
    {
        return $this->role === 'Root';
    }

    /**
     * Indica si el usuario tiene rol Usuario.
     */
    public function isUser(): bool
    {
        return $this->role === 'user' || $this->role === 'Usuario';
    }

    // 👇 ESTE ES EL NUEVO MÉTODO 👇
    /**
     * Indica si el usuario tiene rol Operario.
     */
    public function isOperario(): bool
    {
        return $this->role === 'operario' || $this->role === 'Operario';
    }

    // Permisos de visualización
    /**
     * Indica si el usuario puede visualizar datos en la aplicacion.
     */
    public function canView(): bool
    {
        return $this->isUser() || $this->isOperario() || $this->isAdmin() || $this->isRoot();
    }

    // Permisos de creación
    /**
     * Indica si el usuario puede crear registros.
     */
    public function canCreate(): bool
    {
        return $this->isAdmin() || $this->isRoot() || $this->isUser();
    }

    // Permisos de eliminación
    /**
     * Indica si el usuario puede eliminar registros.
     */
    public function canDelete(): bool
    {
        return $this->isAdmin() || $this->isRoot();
    }
}
