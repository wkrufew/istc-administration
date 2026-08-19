<?php

namespace App\Livewire\Administration;

use App\Models\AuditoriaAnulacionMatricula;
use App\Models\Convalidacion;
use App\Models\DetalleMatricula;
use App\Models\Matricula;
use App\Models\MateriasArrastrada;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use App\Models\User;
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

                // 1. Snapshot antes de borrar
                $snapshot = $this->buildSnapshot($matricula);

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
