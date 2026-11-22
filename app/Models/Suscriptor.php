<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscriptor extends Model
{
    use HasFactory;

    protected $table = 'suscriptores';

    protected $fillable = [
        'nombre',
        'correo',
        'autoriza_datos',
    ];

    protected function casts(): array
    {
        return [
            'autoriza_datos' => 'boolean',
        ];
    }
}
