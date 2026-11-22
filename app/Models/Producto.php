<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'slug',
        'tipo',
        'aroma',
        'descripcion',
        'precio',
        'foto',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    public function inventario(): HasOne
    {
        return $this->hasOne(Inventario::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }

    public function avisosStock(): HasMany
    {
        return $this->hasMany(AvisoStock::class);
    }

    public function pedidoItems(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function getEnStockAttribute(): bool
    {
        return $this->inventario && $this->inventario->stock_disponible > 0;
    }

    public function getStockDisponibleAttribute(): int
    {
        return $this->inventario ? $this->inventario->stock_disponible : 0;
    }
}
