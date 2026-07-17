<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaNoLectivo extends Model
{
    protected $table = 'dias_no_lectivos';

    protected $fillable = [
        'periodo_id',
        'fecha',
        'nombre',
        'tipo',
        'alcance',
        'horario_id',
        'creado_por_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por_id');
    }

    // -------------------------------------------------------
    // Scopes de uso frecuente
    // -------------------------------------------------------

    public function scopeParaFecha($query, string $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    public function scopeGlobales($query)
    {
        return $query->where('alcance', 'global');
    }

    public function scopeParaHorario($query, int $horarioId)
    {
        return $query->where('alcance', 'horario')->where('horario_id', $horarioId);
    }

    /**
     * Devuelve todos los días no lectivos (globales + del horario) para una fecha.
     */
    public static function afectanA(string $fecha, ?int $horarioId = null)
    {
        return static::where('fecha', $fecha)
            ->where(function ($q) use ($horarioId) {
                $q->where('alcance', 'global');
                if ($horarioId) {
                    $q->orWhere(fn($s) => $s->where('alcance', 'horario')->where('horario_id', $horarioId));
                }
            });
    }
}
