<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroCambio extends Model
{
    use HasFactory;

    protected $table = 'registros_cambios';

    protected $fillable = [
        'tipo_entidad',
        'entidad_id',
        'user_id',
        'accion',
        'fecha',
        'comentario',
        'detalles',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'detalles' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
