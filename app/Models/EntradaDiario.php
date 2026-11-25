<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntradaDiario extends Model
{
    use HasFactory;

    protected $table = 'entradas_diario';

    protected $fillable = [
        'user_id',
        'formula_id',
        'rutina_id',
        'fecha',
        'emocion_antes',
        'emocion_despues',
        'energia_antes',
        'energia_despues',
        'calificacion',
        'nota',
        'etiquetas',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'energia_antes' => 'integer',
            'energia_despues' => 'integer',
            'calificacion' => 'integer',
            'etiquetas' => 'array',
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

    public function rutina(): BelongsTo
    {
        return $this->belongsTo(Rutina::class);
    }
}
