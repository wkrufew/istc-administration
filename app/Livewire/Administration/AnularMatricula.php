<?php

namespace App\Livewire\Administration;

use App\Models\Aspirante;
use App\Models\Asistencia;
use App\Models\AuditoriaAnulacionMatricula;
use App\Models\Calificacion;
use App\Models\Convalidacion;
use App\Models\ConvenioAplicado;
use App\Models\DetalleMatricula;
use App\Models\Matricula;
use App\Models\MateriasArrastrada;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use App\Models\Retiro;
use App\Models\User;
use App\Services\BeneficiosService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

class AnularMatricula extends Component
{
    public bool $mostrar = false;
    public int  $paso    = 1;
    public bool $procesando = false;

    public ?int   $estudianteId = null;
    public ?array $estudiante   = null;
    public array  $matriculas   = [];

    public ?int    $matriculaSeleccionadaId = null;
    public ?array  $matriculaSeleccionada   = null;
    public string  $codigoIngresado         = '';

    // ── Abrir modal ───────────────────────────────────────────────────────────

    #[On('abrir-anulacion-matricula')]
    public function abrir(int $estudianteId): void
    {
        if (! $this->esAdministrador()) return;

        $user = User::findOrFail($estudianteId);

        $this->estudianteId = $estudianteId;
        $this->estudiante   = [
            'id'     => $user->id,
            'nombre' => $user->name,
            'cedula' => $user->cedula ?? '-',
        ];

        $this->matriculas = Matricula::where('user_id', $estudianteId)
            ->with(['periodo', 'carrera', 'detalles', 'obligacionesFinancieras', 'pagos'])
            ->latest()
            ->get()
            ->map(fn ($m) => [
                'id'               => $m->id,
                'code'             => $m->code,
                'tipo'             => $m->tipo,
                'estado'           => $m->estado,
                'fecha_matricula'  => $m->fecha_matricula?->format('d/m/Y'),
                'periodo_code'     => $m->periodo?->code ?? '-',
                'periodo_desc'     => $m->periodo?->description ?? '',
                'carrera_nombre'   => $m->carrera?->name ?? '-',
                'total_materias'   => $m->detalles->count(),
                'total_obligaciones' => $m->obligacionesFinancieras->count(),
                'tiene_pagos'      => $m->pagos->count() > 0,
                'total_pagado'     => $m->pagos->sum('monto'),
                'bloqueo'          => $this->motivoBloqueo($m),
            ])
            ->toArray();

        $this->resetConfirmacion();
        $this->paso    = 1;
        $this->mostrar = true;
        $this->dispatch('modal-opened');
    }

    // ── Seleccionar matrícula ─────────────────────────────────────────────────

    public function seleccionarMatricula(int $matriculaId): void
    {
        if (! $this->esAdministrador()) return;

        $m = Matricula::with(['periodo', 'carrera', 'detalles.materia', 'detalles.paralelo', 'obligacionesFinancieras', 'pagos'])
            ->findOrFail($matriculaId);

        abort_if($m->user_id !== $this->estudianteId, 403);

        $this->matriculaSeleccionadaId = $m->id;
        $this->matriculaSeleccionada   = [
            'id'             => $m->id,
            'code'           => $m->code,
            'tipo'           => $m->tipo,
            'estado'         => $m->estado,
            'fecha_matricula' => $m->fecha_matricula?->format('d/m/Y'),
            'periodo_code'   => $m->periodo?->code ?? '-',
            'periodo_desc'   => $m->periodo?->description ?? '',
            'carrera_nombre' => $m->carrera?->name ?? '-',
            'materias'       => $m->detalles->map(fn ($d) => [
                'nombre'  => $d->materia?->name ?? 'Sin nombre',
                'tipo'    => $d->tipo,
                'paralelo' => $d->paralelo?->codigo ?? $d->paralelo?->name ?? '-',
            ])->toArray(),
            'obligaciones'   => $m->obligacionesFinancieras->map(fn ($o) => [
                'tipo'   => $o->tipo,
                'monto'  => (float) $o->monto_final,
                'estado' => $o->estado,
            ])->toArray(),
            'pagos_count'  => $m->pagos->count(),
            'total_pagado' => (float) $m->pagos->sum('monto'),
            'bloqueo'      => $this->motivoBloqueo($m),
        ];

        $this->codigoIngresado = '';
        $this->resetErrorBag();
        $this->paso = 2;
    }

    // ── Volver al paso 1 ──────────────────────────────────────────────────────

    public function volverPaso1(): void
    {
        $this->paso = 1;
        $this->resetConfirmacion();
    }

    // ── Cerrar modal ──────────────────────────────────────────────────────────

    public function cerrar(): void
    {
        $this->mostrar      = false;
        $this->estudianteId = null;
        $this->estudiante   = null;
        $this->matriculas   = [];
        $this->resetConfirmacion();
        $this->dispatch('modal-closed');
    }

    // ── Anular con cascada ────────────────────────────────────────────────────

    public function anular(): void
    {
        if (! $this->esAdministrador()) return;

        $codigoEsperado = $this->matriculaSeleccionada['code'] ?? '';

        if ($this->codigoIngresado !== $codigoEsperado) {
            $this->addError('codigo', 'El código ingresado no coincide.');
            return;
        }

        $this->procesando          = true;
        $matriculaId               = $this->matriculaSeleccionadaId;
        $codigoAnulado             = $codigoEsperado;
        $adminId                   = Auth::id();

        try {
            DB::transaction(function () use ($matriculaId, $adminId) {
                $matricula = Matricula::with([
                    'periodo',
                    'carrera',
                    'estudiante',
                    'detalles.materia',
                    'detalles.paralelo',
                    'obligacionesFinancieras.pagos',
                ])->findOrFail($matriculaId);

                abort_if($matricula->user_id !== $this->estudianteId, 403);

                // Con notas, asistencias o algo construido encima ya no es un error de registro
                if ($bloqueo = $this->motivoBloqueo($matricula)) {
                    throw new \DomainException($bloqueo);
                }

                // 1. Snapshot antes de borrar
                $snapshot = $this->buildSnapshot($matricula);

                // 1b. Lo que esta matrícula consumió vuelve a quedar pendiente de aplicar/cobrar:
                //     el pago verificado en admisión y el reintegro de un retiro anterior
                $pagosIds = $matricula->obligacionesFinancieras->flatMap(fn($o) => $o->pagos->pluck('id'));
                Aspirante::whereIn('pago_aplicado_id', $pagosIds)->get()->each(function ($aspirante) use ($matricula) {
                    $sigueMatriculado = Matricula::where('user_id', $matricula->user_id)
                        ->where('id', '!=', $matricula->id)
                        ->where('tipo', '!=', Matricula::TIPO_VALIDACION)
                        ->exists();
                    $aspirante->update([
                        'pago_aplicado_id' => null,
                        'estado'           => $sigueMatriculado ? $aspirante->estado : 'aprobado',
                    ]);
                });

                $matricula->obligacionesFinancieras->where('tipo', 'REINTEGRO')->pluck('retiro_id')->filter()->unique()
                    ->each(fn($retiroId) => Retiro::find($retiroId)?->update(['recargo_cobrado' => false]));

                // 2. Archivos físicos + pagos
                foreach ($matricula->obligacionesFinancieras as $obligacion) {
                    foreach ($obligacion->pagos as $pago) {
                        if ($pago->comprobante_path) {
                            Storage::disk('public')->delete($pago->comprobante_path);
                        }
                        $pago->delete();
                    }
                }

                // 3. Obligaciones financieras
                $matricula->obligacionesFinancieras()->delete();

                // 3b. Beneficios: una matrícula del período en curso que se anula nunca se usó, así
                //     que sus becas y convenios terminan (quedan en el historial). Se conservan si
                //     el estudiante sigue teniendo obligaciones vigentes de otra matrícula en un
                //     período en curso, o si la anulada es de un período pasado (no tiene que ver
                //     con los beneficios que tenga ahora).
                $esDelPeriodoEnCurso = DB::table('carrera_periodo')
                    ->where('carrera_id', $matricula->carrera_id)
                    ->where('periodo_id', $matricula->periodo_id)
                    ->where('is_current', true)
                    ->where('is_active', true)
                    ->exists();

                $sigueEnCurso = ObligacionesFinanciera::where('user_id', $matricula->user_id)
                    ->where('matricula_id', '!=', $matricula->id)
                    ->where('estado', '!=', 'Invalidado')
                    ->whereExists(fn($q) => $q->selectRaw(1)
                        ->from('matriculas as m')
                        ->join('carrera_periodo as cp', 'cp.carrera_id', '=', 'm.carrera_id')
                        ->whereColumn('m.id', 'obligaciones_financieras.matricula_id')
                        ->whereColumn('cp.periodo_id', 'obligaciones_financieras.periodo_id')
                        ->where('cp.is_current', true)
                        ->where('cp.is_active', true))
                    ->exists();

                if ($esDelPeriodoEnCurso && ! $sigueEnCurso) {
                    BeneficiosService::desactivarTodos($matricula->user_id, 'anulacion_matricula', "Matrícula {$matricula->code}");
                } else {
                    // Se conservan: un convenio semestral marcado con este período solo se libera
                    // si ninguna otra obligación vigente de ese período lo usa
                    ConvenioAplicado::where('user_id', $matricula->user_id)
                        ->where('periodo_id', $matricula->periodo_id)
                        ->where('is_active', true)
                        ->whereHas('tipoConvenio', fn($t) => $t->where('tipo_alcance', 'semestral'))
                        ->get()
                        ->reject(fn($convenio) => ObligacionesFinanciera::where('convenio_aplicado_id', $convenio->id)
                            ->where('periodo_id', $matricula->periodo_id)
                            ->where('estado', '!=', 'Invalidado')
                            ->exists())
                        ->each(fn($convenio) => $convenio->update(['periodo_id' => null]));
                }

                // 4. Convalidación (si existe)
                $convalidacion = Convalidacion::where('matricula_id', $matriculaId)->first();
                if ($convalidacion) {
                    $convalidacion->detalles()->delete();
                    $convalidacion->delete();
                }

                // 5. Revertir arrastres que pasaron a 'Inscrita' con esta matrícula
                $detallesArrastre = $matricula->detalles->where('tipo', 'Arrastre');
                foreach ($detallesArrastre as $detalle) {
                    MateriasArrastrada::where('user_id', $matricula->user_id)
                        ->where('materia_id', $detalle->materia_id)
                        ->where('estado', 'Inscrita')
                        ->update(['estado' => 'Arrastrada']);
                }

                // 6. Detalles de matrícula (forceDelete, incluyendo soft-deleted)
                DetalleMatricula::withTrashed()->where('matricula_id', $matriculaId)->forceDelete();

                // 7. Matrícula (forceDelete)
                $matricula->forceDelete();

                // 8. Registro de auditoría
                AuditoriaAnulacionMatricula::create([
                    'matricula_code'    => $snapshot['code'],
                    'matricula_tipo'    => $snapshot['tipo'],
                    'estudiante_id'     => $matricula->user_id,
                    'estudiante_nombre' => $matricula->estudiante?->name ?? 'Desconocido',
                    'periodo_code'      => $matricula->periodo?->code ?? '-',
                    'carrera_nombre'    => $matricula->carrera?->name ?? '-',
                    'detalle'           => $snapshot,
                    'eliminado_por'     => $adminId,
                ]);
            });

            $this->cerrar();
            $this->dispatch('matricula-anulada');
            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => "Matrícula {$codigoAnulado} anulada correctamente.",
                'timer' => 3000,
            ]);
        } catch (\DomainException $e) {
            $this->procesando = false;
            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'No se puede anular',
                'text'  => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            $this->procesando = false;
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Error al anular la matrícula.',
                'text'  => 'Contacte al administrador del sistema.',
                'timer' => 4000,
            ]);
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function esAdministrador(): bool
    {
        if (auth()->user()?->hasAnyRole(['Administrador', 'Super Admin'])) return true;

        $this->dispatch('swal', ['icon' => 'error', 'title' => 'Acceso denegado', 'text' => 'Solo un administrador puede anular matrículas.']);
        return false;
    }

    /**
     * Por qué no se puede anular (null = se puede). Una matrícula regular con notas o asistencias
     * ya se usó: se corrige desde "Ver matrícula". La de validación de conocimientos guarda las
     * notas convalidadas: no se anula si ya se cursaron semestres regulares después de ella o si
     * su saldo tiene pagos (aprobados o en revisión).
     */
    private function motivoBloqueo(Matricula $matricula): ?string
    {
        if ($matricula->tipo === Matricula::TIPO_VALIDACION) {
            $posteriores = Matricula::where('user_id', $matricula->user_id)
                ->where('tipo', '!=', Matricula::TIPO_VALIDACION)
                ->where('id', '>', $matricula->id)
                ->exists();
            if ($posteriores) {
                return 'El estudiante ya tiene matrículas regulares posteriores a esta validación de conocimientos; anularla borraría las notas convalidadas en las que se basan.';
            }

            $conPagos = Pago::whereHas('obligacion', fn($q) => $q->where('matricula_id', $matricula->id))
                ->whereIn('estado', [Pago::ESTADO_APROBADO, Pago::ESTADO_PENDIENTE, Pago::ESTADO_PROCESANDO])
                ->exists();
            return $conPagos
                ? 'El saldo de esta validación de conocimientos ya tiene pagos registrados (aprobados o en revisión).'
                : null;
        }

        $detalles = DetalleMatricula::withTrashed()->where('matricula_id', $matricula->id)->pluck('id');
        if ($detalles->isEmpty()) return null;

        $conRegistros = Calificacion::whereIn('detalle_matricula_id', $detalles)->exists()
            || Asistencia::whereIn('detalle_matricula_id', $detalles)->exists();

        return $conRegistros
            ? 'Esta matrícula ya tiene notas o asistencias registradas. Usa "Ver matrícula" para cambiar paralelo, agregar o quitar materias.'
            : null;
    }

    private function resetConfirmacion(): void
    {
        $this->matriculaSeleccionadaId = null;
        $this->matriculaSeleccionada   = null;
        $this->codigoIngresado         = '';
        $this->procesando              = false;
        $this->resetErrorBag();
    }

    private function buildSnapshot(Matricula $matricula): array
    {
        return [
            'code'           => $matricula->code,
            'tipo'           => $matricula->tipo,
            'estado'         => $matricula->estado,
            'fecha_matricula' => $matricula->fecha_matricula?->format('Y-m-d'),
            'materias'       => $matricula->detalles->map(fn ($d) => [
                'nombre'  => $d->materia?->name,
                'tipo'    => $d->tipo,
                'paralelo' => $d->paralelo?->codigo ?? $d->paralelo?->name,
            ])->toArray(),
            'obligaciones'   => $matricula->obligacionesFinancieras->map(fn ($o) => [
                'tipo'   => $o->tipo,
                'monto'  => (float) $o->monto_final,
                'estado' => $o->estado,
            ])->toArray(),
            'pagos'          => $matricula->obligacionesFinancieras
                ->flatMap(fn ($o) => $o->pagos->map(fn ($p) => [
                    'comprobante' => $p->numero_comprobante,
                    'monto'       => (float) $p->monto,
                    'metodo'      => $p->metodo_pago,
                    'archivo'     => $p->comprobante_path,
                ]))
                ->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.administration.anular-matricula');
    }
}
