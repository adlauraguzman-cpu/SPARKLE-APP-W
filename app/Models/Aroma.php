<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aroma extends Model
{
    use HasFactory;

    protected $table = 'aromas';

    protected $fillable = [
        'nombre',
        'familia',
        'notas',
        'descripcion',
        'advertencias_seguridad',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'advertencias_seguridad' => 'array',
            'activo' => 'boolean',
        ];
    }

    public function reglasAfinidad(): HasMany
    {
        return $this->hasMany(ReglaAfinidad::class);
    }

    public function componentesFormula(): HasMany
    {
        return $this->hasMany(ComponenteFormula::class);
    }
}
