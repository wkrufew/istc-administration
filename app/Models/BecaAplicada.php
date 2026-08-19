<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BecaAplicada extends Model
{
    protected $table = 'becas_aplicadas';

    protected $fillable = [
        'user_id',
        'tipo_beca_id',
        'porcentaje_aplicado',
        'porcentaje_discapacidad',
        'documento_path',
        'observacion',
        'fecha_asignacion',
        'fecha_ultima_revision',
        'is_active',
        'asignado_por',
    ];

    protected $casts = [
        'porcentaje_aplicado'    => 'decimal:2',
        'porcentaje_discapacidad' => 'integer',
        'fecha_asignacion'       => 'date',
        'fecha_ultima_revision'  => 'date',
        'is_active'              => 'boolean',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tipoBeca(): BelongsTo
    {
        return $this->belongsTo(TipoBeca::class, 'tipo_beca_id');
    }

    public function asignadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_por');
    }

    public function getDocumentoUrlAttribute(): ?string
    {
        return $this->documento_path
            ? Storage::disk('public')->url($this->documento_path)
            : null;
    }
}
