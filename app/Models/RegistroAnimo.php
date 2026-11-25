<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroAnimo extends Model
{
    use HasFactory;

    protected $table = 'registros_animo';

    protected $fillable = [
        'user_id',
        'fecha_hora',
        'emocion',
        'energia',
        'nota',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'energia' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
