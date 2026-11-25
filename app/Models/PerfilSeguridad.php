<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfilSeguridad extends Model
{
    use HasFactory;

    protected $table = 'perfiles_seguridad';

    protected $fillable = [
        'user_id',
        'alergias',
        'mascotas_en_casa',
        'tipo_mascotas',
        'ninos_en_casa',
        'embarazo_o_lactancia',
    ];

    protected function casts(): array
    {
        return [
            'alergias' => 'array',
            'mascotas_en_casa' => 'boolean',
            'tipo_mascotas' => 'array',
            'ninos_en_casa' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
