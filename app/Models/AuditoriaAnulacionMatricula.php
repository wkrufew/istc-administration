<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaAnulacionMatricula extends Model
{
    const UPDATED_AT = null;

    protected $table = 'auditoria_anulaciones_matricula';

    protected $fillable = [
        'matricula_code',
        'matricula_tipo',
        'estudiante_id',
        'estudiante_nombre',
        'periodo_code',
        'carrera_nombre',
        'detalle',
        'eliminado_por',
    ];

    protected $casts = [
        'detalle' => 'array',
    ];

    public function eliminadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'eliminado_por');
    }
}
