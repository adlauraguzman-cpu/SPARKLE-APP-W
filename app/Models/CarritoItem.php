<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarritoItem extends Model
{
    use HasFactory;

    protected $table = 'carrito_items';

    protected $fillable = [
        'user_id',
        'session_id',
        'producto_id',
        'cantidad',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) ($this->producto ? $this->producto->precio * $this->cantidad : 0);
    }
}
