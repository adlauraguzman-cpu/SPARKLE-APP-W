<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Espacio extends Model
{
    use HasFactory;

    protected $table = 'espacios';

    protected $fillable = [
        'user_id',
        'nombre',
        'tipo',
        'tamano',
        'ventilado',
    ];

    protected function casts(): array
    {
        return [
            'ventilado' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rutinas(): HasMany
    {
        return $this->hasMany(Rutina::class);
    }
}
