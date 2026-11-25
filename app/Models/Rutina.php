<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rutina extends Model
{
    use HasFactory;

    protected $table = 'rutinas';

    protected $fillable = [
        'user_id',
        'formula_id',
        'espacio_id',
        'nombre',
        'dias',
        'hora',
        'duracion_minutos',
        'canal_recordatorio',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'dias' => 'array',
            'duracion_minutos' => 'integer',
            'activa' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    public function espacio(): BelongsTo
    {
        return $this->belongsTo(Espacio::class);
    }

    public function recordatorios(): HasMany
    {
        return $this->hasMany(Recordatorio::class);
    }

    public function entradasDiario(): HasMany
    {
        return $this->hasMany(EntradaDiario::class);
    }
}
