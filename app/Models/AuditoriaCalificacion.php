<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaCalificacion extends Model
{
    protected $table = 'auditoria_calificacions';

    protected $fillable = [
        'campo_modificado',
        'valor_anterior',
        'valor_nuevo',
        'motivo_modificacion',
        'calificacion_id',
        'docente_id',
        'fecha_modificacion'
    ];

    protected $casts = [
        'fecha_modificacion' => 'datetime',
    ];

    /**
     * Calificación auditada
     */
    public function calificacion()
    {
        return $this->belongsTo(Calificacion::class);
    }

    /**
     * Docente que realizó la modificación
     */
    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }
}
