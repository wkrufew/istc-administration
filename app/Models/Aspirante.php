<?php

namespace App\Models;

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
        'pago_monto' => 'decimal:2',
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

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
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
