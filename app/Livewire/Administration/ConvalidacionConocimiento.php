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

    // Estudiante recibido por ruta
    public int $userId;

    // Paso 1
    public ?int  $carreraId    = null;
    public string $observaciones = '';
    public $documento           = null;

    // Paso 2 — ['materia_id' => ['nota' => '']]
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
            'detalles' => fn($q) => $q->with('materia:id,name,code,semestre_id')
                                       ->orderBy('estado'),
        ])
            ->where('user_id', $this->userId)
            ->orderByDesc('created_at')
            ->get();
    }

    // ── Watchers ──────────────────────────────────────────────────────────────

    public function updatedCarreraId(): void
    {
        $this->seleccionadas = [];
        unset($this->semestres);
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
        $aprobadas = 0;
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

        DB::beginTransaction();
        try {
            $periodo = Periodo::periodoActivoGlobal();
            if (! $periodo) {
                $this->dispatch('swal', [
                    'icon'  => 'error',
                    'title' => 'Sin período activo',
                    'text'  => 'No hay un período académico activo. Activa uno antes de continuar.',
                ]);
                return;
            }

            // Guardar documento
            $documentoPath = null;
            if ($this->documento) {
                $documentoPath = $this->documento->store('convalidaciones', 'public');
            }

            // 1 — Crear Convalidacion
            $convalidacion = Convalidacion::create([
                'user_id'        => $this->userId,
                'carrera_id'     => $this->carreraId,
                'periodo_id'     => $periodo->id,
                'registrado_por' => Auth::id(),
                'documento_path' => $documentoPath,
                'observaciones'  => $this->observaciones ?: null,
                'estado'         => 'Confirmada',
            ]);

            // 2 — Crear Matrícula tipo Validacion
            $ultimaId     = Matricula::max('id') ?? 0;
            $matriculaCode = 'ISTC-VAL-' . str_pad($ultimaId + 1, 5, '0', STR_PAD_LEFT);

            $matricula = Matricula::create([
                'code'            => $matriculaCode,
                'tipo'            => Matricula::TIPO_VALIDACION,
                'estado'          => 'Habilitada',
                'fecha_matricula' => now()->toDateString(),
                'periodo_id'      => $periodo->id,
                'carrera_id'      => $this->carreraId,
                'user_id'         => $this->userId,
                'observaciones'   => 'Generada automáticamente — Validación de Conocimientos.',
            ]);

            // 3 — Por cada materia: DetalleMatricula + Calificacion + ConvalidacionDetalle
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

            // Resetear estado
            $this->paso          = 1;
            $this->carreraId     = null;
            $this->seleccionadas = [];
            $this->observaciones = '';
            $this->documento     = null;
            unset($this->semestres, $this->historial);

            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => '¡Convalidación registrada!',
                'text'  => 'Las calificaciones se generaron. El estudiante puede proceder a matricularse normalmente.',
                'timer' => 4000,
            ]);

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

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.convalidacion-conocimiento');
    }
}
