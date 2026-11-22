<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'rol',
        'autoriza_datos',
        'fecha_autoriza_datos',
        'puntos_disponibles',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'fecha_autoriza_datos' => 'datetime',
            'autoriza_datos' => 'boolean',
            'activo' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'administrador';
    }

    public function isColaborador(): bool
    {
        return $this->rol === 'colaborador';
    }

    public function isCliente(): bool
    {
        return $this->rol === 'cliente';
    }

    public function canManageInventory(): bool
    {
        return in_array($this->rol, ['administrador', 'colaborador']);
    }

    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function movimientosPuntos(): HasMany
    {
        return $this->hasMany(MovimientoPuntos::class);
    }

    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }

    public function carritoItems(): HasMany
    {
        return $this->hasMany(CarritoItem::class);
    }
}
