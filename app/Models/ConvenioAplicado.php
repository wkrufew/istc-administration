<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConvenioAplicado extends Model
{
    protected $table = 'convenios_aplicados';

    protected $fillable = [
        'user_id',
        'tipo_convenio_id',
        'periodo_id',
        'porcentaje_aplicado',
        'motivo',
        'documento_path',
        'observacion',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
        'registrado_por',
        'desactivado_at',
        'desactivado_por',
        'motivo_desactivacion',
        'detalle_desactivacion',
    ];

    protected $casts = [
        'porcentaje_aplicado' => 'decimal:2',
        'fecha_inicio'        => 'date',
        'fecha_fin'           => 'date',
        'is_active'           => 'boolean',
        'requiere_documento'  => 'boolean',
        'desactivado_at'      => 'datetime',
    ];

    public function desactivadoPor()
    {
        return $this->belongsTo(User::class, 'desactivado_por');
    }

    public function obligaciones()
    {
        return $this->hasMany(ObligacionesFinanciera::class, 'convenio_aplicado_id');
    }

    public function esSemestral(): bool
    {
        return $this->tipoConvenio?->tipo_alcance === 'semestral';
    }

    /**
     * Estado de un semestral ya usado: 'en_uso' mientras el período donde se aplicó sea el
     * vigente (o uno futuro) de su carrera; 'cumplido' cuando la carrera ya pasó a un período
     * posterior. null si no es semestral o aún no se ha usado.
     */
    public function estadoUsoSemestral(): ?string
    {
        if (! $this->periodo_id || ! $this->esSemestral()) return null;

        $cp = DB::table('matriculas as m')
            ->join('carrera_periodo as cp', fn($j) => $j->on('cp.carrera_id', '=', 'm.carrera_id')->on('cp.periodo_id', '=', 'm.periodo_id'))
            ->where('m.user_id', $this->user_id)
            ->where('m.periodo_id', $this->periodo_id)
            ->select('cp.carrera_id', 'cp.fecha_inicio', 'cp.is_current')
            ->first();

        if (! $cp) return 'cumplido';
        if ($cp->is_current) return 'en_uso';

        $inicioVigente = DB::table('carrera_periodo')
            ->where('carrera_id', $cp->carrera_id)
            ->where('is_current', true)
            ->value('fecha_inicio');

        return ($inicioVigente && $inicioVigente > $cp->fecha_inicio) ? 'cumplido' : 'en_uso';
    }

    /**
     * Convenios que se pueden aplicar en una matrícula del período dado: activos, vigentes por
     * fecha y, si son semestrales, sin período asignado aún o asignados a ese mismo período
     * (un semestral ya usado en otro período no vuelve a aplicar aunque no se haya cerrado).
     */
    public function scopeAplicablesEnPeriodo($query, int $userId, ?int $periodoId)
    {
        return $query->where('user_id', $userId)
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
            ->where(fn($q) => $q
                ->whereHas('tipoConvenio', fn($t) => $t->where('tipo_alcance', '!=', 'semestral'))
                ->orWhereNull('periodo_id')
                ->when($periodoId, fn($q2) => $q2->orWhere('periodo_id', $periodoId))
            );
    }

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tipoConvenio()
    {
        return $this->belongsTo(TipoConvenio::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
