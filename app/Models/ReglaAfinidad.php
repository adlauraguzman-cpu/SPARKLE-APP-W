<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReglaAfinidad extends Model
{
    use HasFactory;

    protected $table = 'reglas_afinidad';

    protected $fillable = [
        'aroma_id',
        'variable',
        'valor',
        'peso',
        'version',
        'estado',
        'creado_por',
        'aprobado_por',
    ];

    protected function casts(): array
    {
        return [
            'peso' => 'integer',
        ];
    }

    public function aroma(): BelongsTo
    {
        return $this->belongsTo(Aroma::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
