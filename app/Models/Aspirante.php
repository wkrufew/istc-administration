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

    /**
     * Cambios de estado que secretaría puede hacer a mano desde la gestión de admisión.
     * Pasar a 'verificacion' lo hace el aspirante al enviar sus documentos y a 'matriculado'
     * su primera matrícula; un aprobado o matriculado ya no cambia desde aquí.
     */
    const TRANSICIONES = [
        'pendiente'    => ['proceso' => 'Poner en proceso', 'rechazado' => 'Rechazar'],
        'proceso'      => ['rechazado' => 'Rechazar'],
        'verificacion' => ['aprobado' => 'Aprobar', 'rechazado' => 'Rechazar'],
        'aprobado'     => [],
    ];

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
        'pago_fecha',
        'pago_verificado_at',
        'pago_aplicado_id',
        'credenciales_enviadas_at',
        'credenciales_enviadas_por',
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
        'pago_fecha'               => 'date',
        'pago_verificado_at'       => 'datetime',
        'credenciales_enviadas_at' => 'datetime',
    ];

    /** Estados en los que el aspirante tiene acceso al portal de admisión (credenciales vigentes). */
    const ESTADOS_CON_ACCESO = ['proceso', 'verificacion', 'aprobado'];

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

    public function credencialesEnviadasPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'credenciales_enviadas_por');
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
        return $this->pagoConfirmado() && ! $this->pago_aplicado_id;
    }

    /**
     * Secretaría confirmó monto y método. Un pago "verificado" sin pago_verificado_at es de
     * antes de registrar monto/método: el monto que tenga lo escribió el aspirante, no está confirmado.
     */
    public function pagoConfirmado(): bool
    {
        return $this->pago_estado === 'verificado'
            && $this->pago_verificado_at !== null
            && (float) $this->pago_monto > 0;
    }

    /** Verificado antes de que se registrara monto y método (hay que completarlo en Aspirantes). */
    public function pagoVerificadoSinMonto(): bool
    {
        return $this->pago_estado === 'verificado' && ! $this->pagoConfirmado();
    }

    /** El pago de admisión se puede registrar o corregir: aspirante en Verificación o Aprobado y pago aún no aplicado a una matrícula. */
    public function pagoEditable(): bool
    {
        return in_array($this->estado, ['verificacion', 'aprobado'], true) && ! $this->pago_aplicado_id;
    }

    /** Pago confirmado por secretaría y, si ya se usó, la matrícula a la que se aplicó. Null si no está confirmado. */
    public function resumenPago(): ?array
    {
        if (! $this->pagoConfirmado()) return null;

        $pago = $this->pago_aplicado_id ? \App\Models\Pago::with('obligacion.matricula')->find($this->pago_aplicado_id) : null;

        return [
            'monto'      => (float) $this->pago_monto,
            'metodo'     => $this->pago_metodo,
            'fecha_pago' => ($this->pago_fecha ?? $this->pago_verificado_at)?->format('d/m/Y'),
            'verificado' => $this->pago_verificado_at?->format('d/m/Y'),
            'matricula'  => $pago?->obligacion?->matricula?->code,
            'aplicado'   => (bool) $this->pago_aplicado_id,
        ];
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

    /** Por qué no se le pueden (re)enviar credenciales (null = se puede). */
    public function motivoNoReenvioCredenciales(): ?string
    {
        if (! in_array($this->estado, self::ESTADOS_CON_ACCESO, true)) {
            return match ($this->estado) {
                'pendiente'   => 'Aún no tiene acceso al portal: ponlo en proceso y recibirá sus credenciales.',
                'matriculado' => 'Ya es estudiante: sus credenciales se reenvían desde Usuarios.',
                default       => 'Un aspirante rechazado no tiene acceso al portal.',
            };
        }
        if (! $this->user?->cedula) return 'No tiene cédula registrada (la contraseña se restablece a la cédula).';
        if (! $this->user?->email)  return 'No tiene correo registrado.';

        return null;
    }

    /** Segundos que faltan para poder volver a enviarle credenciales (0 = ya se puede). */
    public function segundosParaReenvio(): int
    {
        return \App\Services\CredencialesService::segundosDesde($this->credenciales_enviadas_at);
    }

    public function puedeCambiarA(string $estado): bool
    {
        return array_key_exists($estado, self::TRANSICIONES[$this->estado] ?? []);
    }

    /** Campos obligatorios de la ficha (catálogo único en App\Support\FichaPersona). */
    public function camposFichaRequeridos(): array
    {
        return \App\Support\FichaPersona::camposRequeridos($this->user);
    }

    /**
     * Por qué aún no se puede aprobar (vacío = se puede): ficha completa, documentos
     * requeridos aprobados (pago verificado) y ninguno subido que esté rechazado.
     */
    public function razonesNoPuedeAprobar(): array
    {
        $u = $this->user;
        $fichaFaltantes = collect($this->camposFichaRequeridos())->filter(fn($c) => empty($u?->$c))->count();

        $docsReqAprobados = $this->cedula_estado === 'aprobado'
            && ($this->bachiller_estado === 'aprobado' || $this->habilitante_estado === 'aprobado')
            && $this->pago_estado === 'verificado';

        $documentos = ['cedula' => 'cedula_path', 'bachiller' => 'bachiller_path', 'habilitante' => 'habilitante_path', 'pago' => 'pago_comprobante_path'];
        if ($this->esValidacionConocimientos()) {
            $docsReqAprobados = $docsReqAprobados
                && $this->hoja_vida_estado      === 'aprobado'
                && $this->cert_laborales_estado === 'aprobado'
                && $this->cert_cursos_estado    === 'aprobado';
            $documentos += ['hoja_vida' => 'hoja_vida_path', 'cert_laborales' => 'cert_laborales_path', 'cert_cursos' => 'cert_cursos_path'];
        }

        $hayDocRechazado = collect($documentos)
            ->contains(fn($path, $campo) => $this->$path && $this->{"{$campo}_estado"} === 'rechazado');

        $razones = [];
        if ($fichaFaltantes > 0)
            $razones[] = "Ficha incompleta ({$fichaFaltantes} campo" . ($fichaFaltantes > 1 ? 's' : '') . ')';
        if ($hayDocRechazado)
            $razones[] = 'Hay documentos rechazados sin corregir';
        elseif (! $docsReqAprobados)
            $razones[] = 'Documentos requeridos aún no están todos aprobados';

        // Verificado antes de registrar monto y método: sin monto, la matrícula no podría aplicarlo
        if ($this->pagoVerificadoSinMonto())
            $razones[] = 'Registra el monto del pago de matrícula';

        return $razones;
    }
}
