<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';

    protected $fillable = [
        'producto_id',
        'stock_disponible',
        'stock_reservado',
        'umbral_minimo',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function getEstaBajoMinimoAttribute(): bool
    {
        return $this->stock_disponible <= $this->umbral_minimo;
    }
}
