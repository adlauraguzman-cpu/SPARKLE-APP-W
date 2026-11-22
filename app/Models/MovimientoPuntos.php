<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoPuntos extends Model
{
    use HasFactory;

    protected $table = 'movimientos_puntos';

    protected $fillable = [
        'user_id',
        'pedido_id',
        'tipo', // ganado, canjeado, ajuste
        'puntos',
        'estado', // pendiente, acreditado
        'motivo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
