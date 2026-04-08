<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $fillable = [
        'fecha',
        'estado',
        'hora_entrada',
        'observaciones',
        'detalle_matricula_id',
        'horario_id',
        'docente_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_entrada' => 'datetime:H:i:s',
    ];

    /**
     * Detalle de matrícula (estudiante en la materia)
     */
    public function detalleMatricula()
    {
        return $this->belongsTo(DetalleMatricula::class);
    }

    /**
     * Estudiante (a través de detalle_matricula)
     */
    public function estudiante()
    {
        return $this->hasOneThrough(
            User::class,
            DetalleMatricula::class,
            'id',
            'id',
            'detalle_matricula_id',
            'user_id'
        );
    }

    /**
     * Horario de la clase
     */
    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    /**
     * Docente que registra la asistencia
     */
    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * Materia (a través del horario)
     */
    public function materia()
    {
        return $this->hasOneThrough(
            Materia::class,
            Horario::class,
            'id',
            'id',
            'horario_id',
            'materia_id'
        );
    }

    /**
     * Scope para asistencias por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    /**
     * Scope para asistencias de un estudiante
     */
    public function scopePorEstudiante($query, $estudianteId)
    {
        return $query->whereHas('detalleMatricula', function ($q) use ($estudianteId) {
            $q->where('user_id', $estudianteId);
        });
    }

    /**
     * Scope para asistencias por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Verificar si es tardanza
     */
    public function esTardanza(): bool
    {
        return $this->estado === 'Tardanza';
    }

    /**
     * Verificar si está presente (Presente o Tardanza)
     */
    public function estaPresente(): bool
    {
        return in_array($this->estado, ['Presente', 'Tardanza']);
    }
}
