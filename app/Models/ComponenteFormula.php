<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComponenteFormula extends Model
{
    use HasFactory;

    protected $table = 'componentes_formula';

    protected $fillable = [
        'formula_id',
        'aroma_id',
        'porcentaje',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje' => 'integer',
        ];
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    public function aroma(): BelongsTo
    {
        return $this->belongsTo(Aroma::class);
    }
}
