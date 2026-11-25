<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sesion extends Model
{
    use HasFactory;

    protected $table = 'sesiones';

    protected $fillable = [
        'user_id',
        'espacio_id',
        'animo_id',
        'objetivo',
        'momento',
        'intensidad',
        'es_invitado',
    ];

    protected function casts(): array
    {
        return [
            'es_invitado' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function espacio(): BelongsTo
    {
        return $this->belongsTo(Espacio::class);
    }

    public function registroAnimo(): BelongsTo
    {
        return $this->belongsTo(RegistroAnimo::class, 'animo_id');
    }

    public function formula(): HasOne
    {
        return $this->hasOne(Formula::class);
    }
}
