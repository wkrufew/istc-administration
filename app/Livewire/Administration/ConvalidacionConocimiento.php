<?php

namespace App\Livewire\Administration;

use App\Models\Calificacion;
use App\Models\Carrera;
use App\Models\Convalidacion;
use App\Models\ConvalidacionDetalle;
use App\Models\DetalleMatricula;
use App\Models\Materia;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

class ConvalidacionConocimiento extends Component
{
    use WithFileUploads;

    public int $paso       = 1;
    public int $totalPasos = 3;

    public int $userId;

    // Paso 1
    public ?int  $carreraId    = null;
    public string $observaciones = '';
    public $documento           = null;

    // Paso 2
    public array $seleccionadas = [];

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function estudiante(): ?User
    {
        return User::select('id', 'name', 'email', 'cedula', 'phone')->find($this->userId);
    }

    #[Computed]
    public function carreras()
    {
        return Carrera::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
    }

    #[Computed]
    public function semestres()
    {
        if (! $this->carreraId) return collect();

        return Semestre::with([
            'materias' => fn($q) => $q->where('is_active', true)->orderBy('name'),
        ])
            ->where('carrera_id', $this->carreraId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    #[Computed]
    public function historial()
    {
        return Convalidacion::with([
            'carrera:id,name',
            'periodo:id,code',
            'registradoPor:id,name',
            'detalles' => fn($q) => $q->with([
                'materia' => fn($mq) => $mq->with('semestre:id,name,order'),
            ])->orderBy('estado'),
        ])
            ->where('user_id', $this->userId)
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function materiasAprobadas(): array
    {
        return DetalleMatricula::where('user_id', $this->userId)
            ->whereHas('calificaciones', fn($q) => $q->where('estado_final', 'Aprobado'))
            ->pluck('materia_id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    // ── Watchers ──────────────────────────────────────────────────────────────

    public function updatedCarreraId(): void
    {
        $this->seleccionadas = [];
        unset($this->semestres, $this->materiasAprobadas);
    }

    // ── Helpers para la vista ─────────────────────────────────────────────────

    public function toggleMateria(int $materiaId): void
    {
        $key = (string) $materiaId;
        if (isset($this->seleccionadas[$key])) {
            unset($this->seleccionadas[$key]);
        } else {
            $this->seleccionadas[$key] = ['nota' => ''];
        }
    }

    public function estadoMateria(int $materiaId, float $notaMinima): string
    {
        $nota = floatval($this->seleccionadas[(string) $materiaId]['nota'] ?? 0);
        if ($nota <= 0) return 'Pendiente';
        return $nota >= $notaMinima ? 'Aprobado' : 'Reprobado';
    }

    public function semestreCompleto(Semestre $semestre): bool
    {
        if ($semestre->materias->isEmpty()) return false;

        foreach ($semestre->materias as $materia) {
            $key = (string) $materia->id;
            if (! isset($this->seleccionadas[$key])) return false;
            $nota = floatval($this->seleccionadas[$key]['nota'] ?? 0);
            if ($nota <= 0 || $nota < floatval($materia->nota_minima_aprobacion ?? 7.00)) return false;
        }
        return true;
    }

    public function resumen(): array
    {
        $aprobadas  = 0;
        $reprobadas = 0;

        foreach ($this->seleccionadas as $materiaId => $datos) {
            $materia = Materia::find((int) $materiaId);
            $nota    = floatval($datos['nota'] ?? 0);
            $notaMin = floatval($materia?->nota_minima_aprobacion ?? 7.00);
            $nota >= $notaMin ? $aprobadas++ : $reprobadas++;
        }

        return [
            'total'      => count($this->seleccionadas),
            'aprobadas'  => $aprobadas,
            'reprobadas' => $reprobadas,
        ];
    }

    public function semestresExonerados(): int
    {
        return $this->semestres->filter(fn($s) => $this->semestreCompleto($s))->count();
    }

    // ── Navegación entre pasos ────────────────────────────────────────────────

    public function siguientePaso(): void
    {
        if ($this->paso === 1) {
            $this->validate(
                ['carreraId' => 'required|exists:carreras,id'],
                ['carreraId.required' => 'Debes seleccionar una carrera.']
            );
        }

        if ($this->paso === 2) {
            if (empty($this->seleccionadas)) {
                $this->addError('seleccionadas', 'Debes seleccionar al menos una materia.');
                return;
            }
            foreach ($this->seleccionadas as $datos) {
                $nota = $datos['nota'] ?? '';
                if ($nota === '' || $nota === null) {
                    $this->addError('seleccionadas', 'Todas las materias seleccionadas deben tener nota.');
                    return;
                }
                if (floatval($nota) < 0 || floatval($nota) > 10) {
                    $this->addError('seleccionadas', 'Las notas deben estar entre 0 y 10.');
                    return;
                }
            }
        }

        $this->paso++;
    }

    public function anteriorPaso(): void
    {
        $this->paso = max(1, $this->paso - 1);
    }

    // ── Confirmación ──────────────────────────────────────────────────────────

    public function confirmar(): void
    {
        if (empty($this->seleccionadas)) return;

        // Guard: ninguna materia debe tener ya una calificación aprobada
        $materiasIds = array_map('intval', array_keys($this->seleccionadas));
        $conflicto   = DetalleMatricula::where('user_id', $this->userId)
            ->whereIn('materia_id', $materiasIds)
            ->whereHas('calificaciones', fn($q) => $q->where('estado_final', 'Aprobado'))
            ->exists();

        if ($conflicto) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Conflicto de calificaciones',
                'text'  => 'Una o más materias seleccionadas ya tienen calificaciones aprobadas registradas para este estudiante.',
            ]);
            return;
        }

        $periodo = Periodo::periodoActivoGlobal();
        if (! $periodo) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Sin período activo',
                'text'  => 'No hay un período académico activo. Activa uno antes de continuar.',
            ]);
            return;
        }

        DB::beginTransaction();
        try {

            $documentoPath = null;
            if ($this->documento) {
                $documentoPath = $this->documento->store('convalidaciones', 'public');
            }

            // 1 — Convalidacion
            $convalidacion = Convalidacion::create([
                'user_id'        => $this->userId,
                'carrera_id'     => $this->carreraId,
                'periodo_id'     => $periodo->id,
                'registrado_por' => Auth::id(),
                'documento_path' => $documentoPath,
                'observaciones'  => $this->observaciones ?: null,
                'estado'         => 'Confirmada',
            ]);

            // 2 — Matrícula tipo Validacion (estado Completada: no interfiere con el dashboard del estudiante)
            $ultimaId      = Matricula::max('id') ?? 0;
            $matriculaCode = 'ISTC-VAL-' . str_pad($ultimaId + 1, 5, '0', STR_PAD_LEFT);

            $matricula = Matricula::create([
                'code'            => $matriculaCode,
                'tipo'            => Matricula::TIPO_VALIDACION,
                'estado'          => 'Completada',
                'fecha_matricula' => now()->toDateString(),
                'periodo_id'      => $periodo->id,
                'carrera_id'      => $this->carreraId,
                'user_id'         => $this->userId,
                'observaciones'   => 'Generada automáticamente — Validación de Conocimientos.',
            ]);

            // 3 — DetalleMatricula + Calificacion + ConvalidacionDetalle por materia
            foreach ($this->seleccionadas as $materiaId => $datos) {
                $materia = Materia::find((int) $materiaId);
                if (! $materia) continue;

                $nota    = round(floatval($datos['nota'] ?? 0), 2);
                $notaMin = floatval($materia->nota_minima_aprobacion ?? 7.00);
                $estado  = $nota >= $notaMin ? 'Aprobado' : 'Reprobado';

                $detalle = DetalleMatricula::create([
                    'code'          => $matriculaCode . '-' . ($materia->code ?? $materiaId),
                    'tipo'          => 'Validacion',
                    'estado'        => 'Finalizado',
                    'costo_materia' => 0,
                    'es_repeticion' => false,
                    'matricula_id'  => $matricula->id,
                    'materia_id'    => (int) $materiaId,
                    'paralelo_id'   => null,
                    'user_id'       => $this->userId,
                ]);

                Calificacion::create([
                    'nota_final'           => $nota,
                    'estado_final'         => $estado,
                    'docente_id'           => Auth::id(),
                    'detalle_matricula_id' => $detalle->id,
                    'es_borrador'          => false,
                    'es_arrastre'          => false,
                    'numero_intento'       => 1,
                ]);

                ConvalidacionDetalle::create([
                    'convalidacion_id'     => $convalidacion->id,
                    'materia_id'           => (int) $materiaId,
                    'nota'                 => $nota,
                    'estado'               => $estado,
                    'detalle_matricula_id' => $detalle->id,
                ]);
            }

            // 4 — Vincular matrícula a la convalidación
            $convalidacion->update(['matricula_id' => $matricula->id]);

            DB::commit();

            unset($this->semestres, $this->historial, $this->materiasAprobadas);

            $this->dispatch('swal', [
                'icon'             => 'success',
                'title'            => '¡Validación registrada!',
                'text'             => 'Las calificaciones y la matrícula de validación han sido generadas. Procede a matricular al estudiante en el módulo de Matriculación para generar las obligaciones financieras.',
                'timer'            => 6000,
                'timerProgressBar' => true,
            ]);

            $this->finalizarWizard();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ConvalidacionConocimiento::confirmar — ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Error al confirmar',
                'text'  => 'Ocurrió un error inesperado. Revisa el log del sistema.',
            ]);
        }
    }

    private function finalizarWizard(): void
    {
        $this->paso          = 1;
        $this->carreraId     = null;
        $this->seleccionadas = [];
        $this->observaciones = '';
        $this->documento     = null;
        unset($this->semestres, $this->historial, $this->materiasAprobadas);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.convalidacion-conocimiento');
    }
}
