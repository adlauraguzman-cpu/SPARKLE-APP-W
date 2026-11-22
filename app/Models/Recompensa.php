<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recompensa extends Model
{
    use HasFactory;

    protected $table = 'recompensas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'puntos_requeridos',
        'descuento_valor',
        'vigencia',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'descuento_valor' => 'decimal:2',
            'vigencia' => 'date',
            'activa' => 'boolean',
        ];
    }
}
