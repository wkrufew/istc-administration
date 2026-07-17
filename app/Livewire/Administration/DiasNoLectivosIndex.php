<?php

namespace App\Livewire\Administration;

use App\Models\DiaNoLectivo;
use App\Models\Horario;
use App\Models\Periodo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class DiasNoLectivosIndex extends Component
{
    // ── Filtros ──────────────────────────────────────────────
    public string|int $periodo_filtro = '';

    // ── Modal ────────────────────────────────────────────────
    public bool   $mostrarModal  = false;
    public ?int   $editandoId    = null;

    // ── Campos del formulario ────────────────────────────────
    public string|int $periodo_id = '';
    public string     $fecha      = '';
    public string     $nombre     = '';
    public string     $tipo       = 'feriado';
    public string     $alcance    = 'global';
    public string|int $horario_id = '';

    // ── Cascada horario ──────────────────────────────────────
    public string|int $filtro_materia  = '';
    public string|int $filtro_paralelo = '';


    // =========================================================
    // MOUNT
    // =========================================================
    public function mount(): void
    {
        $activo = Periodo::periodoActivoGlobal();
        $this->periodo_filtro = $activo?->id ?? '';
        $this->periodo_id     = $activo?->id ?? '';
        $this->fecha          = Carbon::today()->toDateString();
    }

    // =========================================================
    // LISTA
    // =========================================================
    #[Computed]
    public function diasNoLectivos()
    {
        return DiaNoLectivo::with(['horario.materia', 'horario.paralelo', 'creadoPor'])
            ->when($this->periodo_filtro, fn($q) => $q->where('periodo_id', $this->periodo_filtro))
            ->orderBy('fecha')
            ->get();
    }

    // =========================================================
    // MODAL — ABRIR / CERRAR
    // =========================================================
    public function abrirCrear(): void
    {
        try {
            $this->resetFormulario();
            $this->mostrarModal = true;
        } catch (\Throwable $e) {
            $this->dispatch('swal', [
                'toast' => true,
                'icon'  => 'error',
                'title' => 'Error al abrir el formulario: ' . $e->getMessage(),
            ]);
        }
    }

    public function abrirEditar(int $id): void
    {
        try {
            $d = DiaNoLectivo::with('horario')->findOrFail($id);
        } catch (\Throwable $e) {
            $this->dispatch('swal', ['toast' => true, 'icon' => 'error', 'title' => 'El registro ya no existe.']);
            return;
        }

        $this->resetFormulario();

        $this->editandoId  = $id;
        $this->periodo_id  = $d->periodo_id;
        $this->fecha       = $d->fecha->toDateString();
        $this->nombre      = $d->nombre;
        $this->tipo        = $d->tipo;
        $this->alcance     = $d->alcance;
        $this->horario_id  = $d->horario_id ?? '';

        if ($d->horario_id && $d->horario) {
            $this->filtro_materia  = $d->horario->materia_id  ?? '';
            $this->filtro_paralelo = $d->horario->paralelo_id ?? '';
        }

        $this->mostrarModal = true;
    }

    public function cerrarModal(): void
    {
        $this->mostrarModal = false;
        $this->resetFormulario();
    }

    // =========================================================
    // GUARDAR
    // =========================================================
    public function guardar(): void
    {
        $this->validate([
            'periodo_id' => 'required|exists:periodos,id',
            'fecha'      => 'required|date',
            'nombre'     => 'required|string|max:255',
            'tipo'       => 'required|in:feriado,suspension',
            'alcance'    => 'required|in:global,horario',
            'horario_id' => $this->alcance === 'horario' ? 'required|exists:horarios,id' : 'nullable',
        ], [
            'horario_id.required' => 'Debes seleccionar un horario cuando el alcance es específico.',
        ]);

        $datos = [
            'periodo_id'    => $this->periodo_id,
            'fecha'         => $this->fecha,
            'nombre'        => $this->nombre,
            'tipo'          => $this->tipo,
            'alcance'       => $this->alcance,
            'horario_id'    => $this->alcance === 'horario' ? $this->horario_id : null,
            'creado_por_id' => Auth::id(),
        ];

        if ($this->editandoId) {
            DiaNoLectivo::findOrFail($this->editandoId)->update($datos);
            $msg = 'Día no lectivo actualizado correctamente.';
        } else {
            DiaNoLectivo::create($datos);
            $msg = 'Día no lectivo registrado correctamente.';
        }

        $this->cerrarModal();
        unset($this->diasNoLectivos);
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => $msg]);
    }

    // =========================================================
    // ELIMINAR
    // =========================================================
    public function eliminar(int $id): void
    {
        DiaNoLectivo::findOrFail($id)->delete();
        unset($this->diasNoLectivos);
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => 'Día no lectivo eliminado.']);
    }

    // =========================================================
    // REACTIVO — limpiar horario si cambia alcance
    // =========================================================
    public function updatedPeriodoId(): void
    {
        $this->filtro_materia  = '';
        $this->filtro_paralelo = '';
        $this->horario_id      = '';
        unset($this->materiasDisponibles, $this->paralelosDisponibles, $this->horariosDisponibles);
    }

    public function updatedAlcance(): void
    {
        $this->horario_id     = '';
        $this->filtro_materia  = '';
        $this->filtro_paralelo = '';
    }

    public function updatedFiltroMateria(): void
    {
        $this->filtro_paralelo = '';
        $this->horario_id      = '';
    }

    public function updatedFiltroParalelo(): void
    {
        $this->horario_id = '';
    }

    // =========================================================
    // DATOS AUXILIARES
    // =========================================================
    #[Computed]
    public function periodos()
    {
        return Periodo::orderByDesc('fecha_inicio')->get(['id', 'code', 'description']);
    }

    #[Computed]
    public function materiasDisponibles()
    {
        if (! $this->periodo_id) return collect();

        return Horario::with('materia:id,name')
            ->where('periodo_id', $this->periodo_id)
            ->where('is_active', true)
            ->get()
            ->pluck('materia')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    #[Computed]
    public function paralelosDisponibles()
    {
        if (! $this->filtro_materia) return collect();

        return Horario::with('paralelo:id,name,code')
            ->where('periodo_id', $this->periodo_id)
            ->where('materia_id', $this->filtro_materia)
            ->where('is_active', true)
            ->get()
            ->pluck('paralelo')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    #[Computed]
    public function horariosDisponibles()
    {
        if (! $this->filtro_materia || ! $this->filtro_paralelo) return collect();

        return Horario::with(['materia:id,name', 'paralelo:id,name,code'])
            ->where('periodo_id',  $this->periodo_id)
            ->where('materia_id',  $this->filtro_materia)
            ->where('paralelo_id', $this->filtro_paralelo)
            ->where('is_active', true)
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    // =========================================================
    // HELPERS
    // =========================================================
    private function resetFormulario(): void
    {
        $activo = Periodo::periodoActivoGlobal();

        $this->editandoId     = null;
        $this->periodo_id     = $activo?->id ?? '';
        $this->fecha          = Carbon::today()->toDateString();
        $this->nombre         = '';
        $this->tipo           = 'feriado';
        $this->alcance        = 'global';
        $this->horario_id     = '';
        $this->filtro_materia  = '';
        $this->filtro_paralelo = '';

        $this->resetValidation();
        unset($this->materiasDisponibles, $this->paralelosDisponibles, $this->horariosDisponibles);
    }

    // =========================================================
    // RENDER
    // =========================================================
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.dias-no-lectivos-index');
    }
}
