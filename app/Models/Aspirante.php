<?php

namespace App\Models;

use App\Services\CalculoMatriculaService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aspirante extends Model
{
    use SoftDeletes;

    const ESTADOS = [
        'pendiente'   => 'Pendiente',
        'proceso'     => 'Proceso',
        'verificacion'=> 'Verificación',
        'aprobado'    => 'Aprobado',
        'rechazado'   => 'Rechazado',
        'matriculado' => 'Matriculado',
    ];

    const DOC_ESTADOS  = ['pendiente', 'aprobado', 'rechazado'];
    const PAGO_ESTADOS = ['pendiente', 'verificado', 'rechazado'];

    protected $fillable = [
        'user_id',
        'cohorte_id',
        'carrera_id',
        'tipo_proceso',
        'estado',
        'motivo_rechazo',
        'observacion_general',

        'cedula_path',
        'cedula_estado',
        'cedula_observacion',

        'bachiller_path',
        'bachiller_estado',
        'bachiller_observacion',

        'habilitante_path',
        'habilitante_estado',
        'habilitante_observacion',

        'pago_monto',
        'pago_metodo',
        'pago_verificado_at',
        'pago_aplicado_id',
        'pago_comprobante_path',
        'pago_estado',
        'pago_observacion',

        'hoja_vida_path',
        'hoja_vida_estado',
        'hoja_vida_observacion',

        'cert_laborales_path',
        'cert_laborales_estado',
        'cert_laborales_observacion',

        'cert_cursos_path',
        'cert_cursos_estado',
        'cert_cursos_observacion',

        'mecanizado_iess_path',

        'registrado_por',
    ];

    protected $casts = [
        'pago_monto'         => 'decimal:2',
        'pago_verificado_at' => 'datetime',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohorte(): BelongsTo
    {
        return $this->belongsTo(Cohorte::class);
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function pagoAplicado(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'pago_aplicado_id');
    }

    /** Matrícula de un semestre regular de su carrera: lo que paga en admisión. */
    public function valorMatriculaAdmision(): float
    {
        if (! $this->carrera) return 0;

        return CalculoMatriculaService::matriculaPorSemestre(
            $this->carrera,
            CalculoMatriculaService::semestresCarrera($this->carrera)
        );
    }

    /**
     * Lo que el aspirante debe conocer antes de pagar, compartido por el portal y el correo:
     * valor de matrícula, tipo de proceso, convenio y, en validación, cómo cambia el valor
     * según los semestres que exonere.
     */
    public function resumenPagoAdmision(): array
    {
        $carrera  = $this->carrera;
        $valor    = $this->valorMatriculaAdmision();
        $convenio = ConvenioAplicado::where('user_id', $this->user_id)
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
            ->with('tipoConvenio:id,nombre')
            ->latest('id')
            ->first();

        return [
            'valor_matricula'  => $valor,
            'es_validacion'    => $this->esValidacionConocimientos(),
            'total_matricula'  => $carrera ? CalculoMatriculaService::matriculaTotalCarrera($carrera) : 0,
            'semestres'        => $carrera ? CalculoMatriculaService::semestresCarrera($carrera) : 0,
            'convenio'         => $convenio ? [
                'nombre'     => $convenio->tipoConvenio?->nombre ?? 'Convenio',
                'porcentaje' => (float) $convenio->porcentaje_aplicado,
            ] : null,
            'escenarios'       => ($carrera && $this->esValidacionConocimientos())
                ? CalculoMatriculaService::escenariosValidacion($carrera, $valor)
                : [],
        ];
    }

    /**
     * Pago de matrícula verificado en admisión (con monto confirmado por secretaría)
     * que aún no se aplicó a una matrícula. Las verificaciones anteriores al registro de
     * monto/método no tienen pago_verificado_at y siguen el cobro normal.
     */
    public function tienePagoAdmisionPorAplicar(): bool
    {
        return $this->pago_estado === 'verificado'
            && $this->pago_verificado_at !== null
            && (float) $this->pago_monto > 0
            && ! $this->pago_aplicado_id;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function tieneHabilitante(): bool
    {
        return ! empty($this->habilitante_path);
    }

    public function bachillerRequerido(): bool
    {
        return ! $this->tieneHabilitante();
    }

    /**
     * El aspirante puede enviar para revisión cuando:
     * - Tiene la cédula subida
     * - Tiene bachiller O habilitante subido
     * - Tiene comprobante de pago subido
     */
    public function esValidacionConocimientos(): bool
    {
        return $this->tipo_proceso === 'validacion_conocimientos';
    }

    public function puedeEnviarRevision(): bool
    {
        $tieneBachillerOHabilitante = ! empty($this->bachiller_path) || ! empty($this->habilitante_path);

        $docsBase = ! empty($this->cedula_path)
            && $tieneBachillerOHabilitante
            && ! empty($this->pago_comprobante_path);

        if ($this->esValidacionConocimientos()) {
            return $docsBase
                && ! empty($this->hoja_vida_path)
                && ! empty($this->cert_laborales_path)
                && ! empty($this->cert_cursos_path);
        }

        return $docsBase;
    }

    public function estadoLabel(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }
}
