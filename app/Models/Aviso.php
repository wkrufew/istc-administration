<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aviso extends Model
{
    protected $fillable = [
        'asignacion_docente_id',
        'titulo',
        'descripcion',
        'tipo',
        'fecha_aviso',
    ];

    protected $casts = [
        'fecha_aviso' => 'date',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function asignacionDocente(): BelongsTo
    {
        return $this->belongsTo(AsignacionDocente::class);
    }

    public function lecturas(): HasMany
    {
        return $this->hasMany(AvisoLectura::class);
    }

    // ── Estado calculado por fecha ────────────────────────────────────────────

    public function getEstadoAttribute(): string
    {
        $hoy        = now()->startOfDay();
        $fechaAviso = Carbon::parse($this->fecha_aviso)->startOfDay();

        if ($fechaAviso->lt($hoy)) return 'pasado';
        if ($fechaAviso->gt($hoy)) return 'proximo';
        return 'hoy';
    }

    public function getTiempoFaltanteAttribute(): string
    {
        $diff = (int) now()->startOfDay()
            ->diffInDays(Carbon::parse($this->fecha_aviso)->startOfDay(), false);

        if ($diff < -1) return 'Hace ' . abs($diff) . ' días';
        if ($diff === -1) return 'Ayer';
        if ($diff === 0)  return 'Hoy';
        if ($diff === 1)  return 'Mañana';
        return "En {$diff} días";
    }

    // ── Helpers de presentación ───────────────────────────────────────────────

    public static function tipoColor(string $tipo): string
    {
        return match ($tipo) {
            'examen'     => 'bg-red-100 text-red-700 border-red-200',
            'tarea'      => 'bg-blue-100 text-blue-700 border-blue-200',
            'evaluacion' => 'bg-violet-100 text-violet-700 border-violet-200',
            'general'    => 'bg-slate-100 text-slate-600 border-slate-200',
            default      => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    public static function tipoLabel(string $tipo): string
    {
        return match ($tipo) {
            'examen'     => 'Examen',
            'tarea'      => 'Tarea',
            'evaluacion' => 'Evaluación',
            'general'    => 'General',
            default      => ucfirst($tipo),
        };
    }

    public static function tipoIcono(string $tipo): string
    {
        return match ($tipo) {
            'examen'     => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            'tarea'      => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            'evaluacion' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            default      => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
        };
    }
}
