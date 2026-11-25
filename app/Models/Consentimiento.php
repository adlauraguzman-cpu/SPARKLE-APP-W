<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consentimiento extends Model
{
    use HasFactory;

    protected $table = 'consentimientos';

    protected $fillable = [
        'user_id',
        'tipo',
        'aceptado',
        'version_del_texto',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'aceptado' => 'boolean',
            'fecha' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
