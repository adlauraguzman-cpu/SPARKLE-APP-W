<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'numero_pedido',
        'user_id',
        'direccion_id',
        'direccion_envio',
        'ciudad_envio',
        'telefono_contacto',
        'estado', // pagado, en_preparacion, enviado, entregado, cancelado
        'subtotal',
        'costo_envio',
        'total',
        'puntos_ganados',
        'puntos_canjeados',
        'descuento',
        'metodo_pago', // tarjeta, pse, billetera
        'estado_pago', // aprobado, rechazado, pendiente
        'referencia_pasarela',
        'transportadora',
        'guia_transporte',
        'fecha_estimada_entrega',
        'acepta_terminos',
        'solicitud_cambio_devolucion',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'costo_envio' => 'decimal:2',
            'total' => 'decimal:2',
            'descuento' => 'decimal:2',
            'fecha_estimada_entrega' => 'datetime',
            'acepta_terminos' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function movimientoPuntos(): HasOne
    {
        return $this->hasOne(MovimientoPuntos::class);
    }

    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }
}
