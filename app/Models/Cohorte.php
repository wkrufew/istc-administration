<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cohorte extends Model
{
    protected $fillable = [
        'nombre',
        'fecha_inicio_matriculacion',
        'fecha_inicio_clases',
        'estado',
        'descripcion',
        'creado_por',
    ];

    protected $casts = [
        'fecha_inicio_matriculacion' => 'date',
        'fecha_inicio_clases'        => 'date',
    ];

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function aspirantes(): HasMany
    {
        return $this->hasMany(Aspirante::class);
    }

    public function estaAbierto(): bool
    {
        return $this->estado === 'abierto';
    }
}
