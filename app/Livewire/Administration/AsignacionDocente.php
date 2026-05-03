<?php

namespace App\Livewire\Administration;

use App\Models\AsignacionDocente as AsignacionDocenteModel;
use App\Models\MateriaPeriodoParalelo;
use App\Models\Periodo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AsignacionDocente extends Component
{
    public User $docente;

    public ?int $periodoId  = null;
    public ?int $materiaId  = null;
    public ?int $paraleloId = null;

    // Filtro de la tabla de asignaciones
    public ?int $filtroPeriodoId = null;

    public function mount(User $docente): void
    {
        $this->docente = $docente;

        // Preseleccionar el periodo activo más reciente
        $periodoActivo = Periodo::whereExists(fn($q) =>
            $q->selectRaw(1)
              ->from('carrera_periodo')
              ->whereColumn('carrera_periodo.periodo_id', 'periodos.id')
              ->where('carrera_periodo.is_current', true)
        )->orderByDesc('fecha_inicio')->first();

        if ($periodoActivo) {
            $this->periodoId      = $periodoActivo->id;
            $this->filtroPeriodoId = $periodoActivo->id;
        }
    }

    // =========================================================================
    // ACTUALIZACIÓN EN CASCADA
    // =========================================================================

    public function updatedPeriodoId(): void
    {
        $this->materiaId  = null;
        $this->paraleloId = null;
    }

    public function updatedMateriaId(): void
    {
        $this->paraleloId = null;
    }

    // Llamado desde el combobox Alpine cuando el usuario selecciona una materia
    public function seleccionarMateria(?int $id): void
    {
        $this->materiaId  = $id;
        $this->paraleloId = null;
    }

    // =========================================================================
    // ASIGNAR
    // =========================================================================

    public function asignar(): void
    {
        $this->validate([
            'periodoId'  => 'required|exists:periodos,id',
            'materiaId'  => 'required|exists:materias,id',
            'paraleloId' => 'required|exists:paralelos,id',
        ], [
            'periodoId.required'  => 'Selecciona un período.',
            'materiaId.required'  => 'Selecciona una materia.',
            'paraleloId.required' => 'Selecciona un paralelo.',
        ]);

        // Verificar que la combinación existe en MPP
        $mppExiste = MateriaPeriodoParalelo::where('periodo_id', $this->periodoId)
            ->where('materia_id', $this->materiaId)
            ->where('paralelo_id', $this->paraleloId)
            ->where('is_active', true)
            ->exists();

        if (! $mppExiste) {
            $this->addError('general', 'Esta combinación de materia/paralelo no está configurada en el módulo Materia-Período-Paralelo.');
            return;
        }

        // Verificar duplicado
        $duplicado = AsignacionDocenteModel::where([
            'docente_id'  => $this->docente->id,
            'materia_id'  => $this->materiaId,
            'paralelo_id' => $this->paraleloId,
            'periodo_id'  => $this->periodoId,
        ])->exists();

        if ($duplicado) {
            $this->addError('general', 'El docente ya tiene esta materia asignada en ese período y paralelo.');
            return;
        }

        AsignacionDocenteModel::create([
            'docente_id'  => $this->docente->id,
            'materia_id'  => $this->materiaId,
            'paralelo_id' => $this->paraleloId,
            'periodo_id'  => $this->periodoId,
        ]);

        $this->materiaId  = null;
        $this->paraleloId = null;
        $this->resetErrorBag();
        $this->filtroPeriodoId = $this->periodoId;

        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Asignación registrada correctamente.']);
    }

    // =========================================================================
    // ELIMINAR
    // =========================================================================

    public function eliminar(int $asignacionId): void
    {
        $asignacion = AsignacionDocenteModel::where('id', $asignacionId)
            ->where('docente_id', $this->docente->id)
            ->firstOrFail();

        $asignacion->delete();

        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Asignación eliminada.']);
    }

    // =========================================================================
    // DATA HELPERS
    // =========================================================================

    private function getPeriodos()
    {
        return Periodo::whereExists(fn($q) =>
            $q->selectRaw(1)
              ->from('carrera_periodo')
              ->whereColumn('carrera_periodo.periodo_id', 'periodos.id')
              ->where('carrera_periodo.is_current', true)
        )->orderByDesc('fecha_inicio')->get();
    }

    private function getMaterias()
    {
        if (! $this->periodoId) return collect();

        return MateriaPeriodoParalelo::with('materia')
            ->where('periodo_id', $this->periodoId)
            ->where('is_active', true)
            ->get()
            ->pluck('materia')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    private function getParalelos()
    {
        if (! $this->periodoId || ! $this->materiaId) return collect();

        return MateriaPeriodoParalelo::with('paralelo')
            ->where('periodo_id', $this->periodoId)
            ->where('materia_id', $this->materiaId)
            ->where('is_active', true)
            ->get()
            ->pluck('paralelo')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    private function getAsignaciones()
    {
        $q = $this->docente
            ->asignacionesDocente()
            ->with(['materia', 'paralelo', 'periodo.carreras']);

        if ($this->filtroPeriodoId) {
            $q->where('periodo_id', $this->filtroPeriodoId);
        }

        return $q->orderByDesc('created_at')->get();
    }

    private function checkYaExiste(): bool
    {
        if (! $this->periodoId || ! $this->materiaId || ! $this->paraleloId) return false;

        return AsignacionDocenteModel::where([
            'docente_id'  => $this->docente->id,
            'materia_id'  => $this->materiaId,
            'paralelo_id' => $this->paraleloId,
            'periodo_id'  => $this->periodoId,
        ])->exists();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.administration.asignacion-docente', [
            'periodos'    => $this->getPeriodos(),
            'materias'    => $this->getMaterias(),
            'paralelos'   => $this->getParalelos(),
            'asignaciones' => $this->getAsignaciones(),
            'yaExiste'    => $this->checkYaExiste(),
        ]);
    }
}
