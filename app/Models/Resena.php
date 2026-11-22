<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resena extends Model
{
    use HasFactory;

    protected $table = 'resenas';

    protected $fillable = [
        'user_id',
        'producto_id',
        'pedido_id',
        'calificacion',
        'comentario',
        'foto',
        'autoriza_publicacion',
        'estado', // pendiente, publicada
    ];

    protected function casts(): array
    {
        return [
            'autoriza_publicacion' => 'boolean',
            'calificacion' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
