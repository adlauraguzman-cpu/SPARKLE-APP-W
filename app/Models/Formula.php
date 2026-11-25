<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formula extends Model
{
    use HasFactory;

    protected $table = 'formulas';

    protected $fillable = [
        'sesion_id',
        'user_id',
        'nombre',
        'version_de_reglas',
        'explicacion',
        'ritual_sugerido',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'ritual_sugerido' => 'array',
        ];
    }

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function componentes(): HasMany
    {
        return $this->hasMany(ComponenteFormula::class);
    }

    public function rutinas(): HasMany
    {
        return $this->hasMany(Rutina::class);
    }

    public function entradasDiario(): HasMany
    {
        return $this->hasMany(EntradaDiario::class);
    }
}
