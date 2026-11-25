<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'zona_horaria',
        'activo',
        'fecha_registro',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'fecha_registro' => 'datetime',
        ];
    }

    public function isUsuario(): bool
    {
        return $this->rol === 'usuario';
    }

    public function isEspecialista(): bool
    {
        return $this->rol === 'especialista';
    }

    public function isAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function consentimientos(): HasMany
    {
        return $this->hasMany(Consentimiento::class);
    }

    public function perfilSensorial(): HasOne
    {
        return $this->hasOne(PerfilSensorial::class);
    }

    public function perfilSeguridad(): HasOne
    {
        return $this->hasOne(PerfilSeguridad::class);
    }

    public function espacios(): HasMany
    {
        return $this->hasMany(Espacio::class);
    }

    public function registrosAnimo(): HasMany
    {
        return $this->hasMany(RegistroAnimo::class);
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class);
    }

    public function formulas(): HasMany
    {
        return $this->hasMany(Formula::class);
    }

    public function rutinas(): HasMany
    {
        return $this->hasMany(Rutina::class);
    }

    public function entradasDiario(): HasMany
    {
        return $this->hasMany(EntradaDiario::class);
    }

    public function registrosCambios(): HasMany
    {
        return $this->hasMany(RegistroCambio::class);
    }
}
