<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Retiro extends Model
{
    protected $fillable = [
        'matricula_id',
        'user_id',
        'fecha_retiro',
        'motivo',
        'documento_path',
        'recargo_cobrado',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_retiro'    => 'date',
        'recargo_cobrado' => 'boolean',
    ];

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
