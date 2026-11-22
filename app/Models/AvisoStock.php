<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvisoStock extends Model
{
    use HasFactory;

    protected $table = 'avisos_stock';

    protected $fillable = [
        'correo',
        'producto_id',
        'notificado',
    ];

    protected function casts(): array
    {
        return [
            'notificado' => 'boolean',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
