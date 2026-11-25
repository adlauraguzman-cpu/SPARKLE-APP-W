<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfilSensorial extends Model
{
    use HasFactory;

    protected $table = 'perfiles_sensoriales';

    protected $fillable = [
        'user_id',
        'familias_preferidas',
        'familias_evitadas',
        'intensidad_preferida',
    ];

    protected function casts(): array
    {
        return [
            'familias_preferidas' => 'array',
            'familias_evitadas' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
