<?php

namespace App\Livewire\Docente;

use App\Models\AsignacionDocente;
use App\Models\Aviso;
use App\Models\Periodo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Traits\WithAuthorization;

class AvisosDocente extends Component
{
    use WithAuthorization;
    // ── Filtro de materia ─────────────────────────────────────────────────────
    public ?int $asignacionId = null;

    // ── Paginación pasados ────────────────────────────────────────────────────
    public int  $limitePasados          = 10;
    public bool $seccionPasadosAbierta  = false;

    // ── Formulario nuevo aviso ────────────────────────────────────────────────
    public bool $mostrarFormulario = false;

    #[Validate('required|string|max:200')]
    public string $titulo = '';

    #[Validate('required|string|max:2000')]
    public string $descripcion = '';

    #[Validate('required|in:examen,tarea,evaluacion,general')]
    public string $tipo = 'general';

    #[Validate('required|date|after_or_equal:today')]
    public string $fechaAviso = '';

    public function mount(): void
    {
        $this->fechaAviso = now()->format('Y-m-d');

        // Seleccionar la primera asignación del periodo activo por defecto
        $primera = $this->asignaciones->first();
        if ($primera) {
            $this->asignacionId = $primera->id;
        }
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function periodoActivo(): ?Periodo
    {
        return Periodo::periodoActivoGlobal();
    }

    #[Computed]
    public function asignaciones()
    {
        if (! $this->periodoActivo) {
            return collect();
        }

        return AsignacionDocente::with(['materia', 'paralelo'])
            ->where('docente_id', Auth::id())
            ->where('periodo_id', $this->periodoActivo->id)
            ->get();
    }

    private function baseAvisosQuery()
    {
        $query = Aviso::with(['asignacionDocente.materia', 'asignacionDocente.paralelo'])
            ->whereHas('asignacionDocente', fn($q) => $q->where('docente_id', Auth::id()));

        if ($this->asignacionId) {
            $query->where('asignacion_docente_id', $this->asignacionId);
        }

        return $query;
    }

    #[Computed]
    public function avisos(): array
    {
        $futuros = $this->baseAvisosQuery()
            ->where('fecha_aviso', '>=', now()->startOfDay())
            ->orderBy('fecha_aviso', 'asc')
            ->get();

        $pasados = $this->baseAvisosQuery()
            ->where('fecha_aviso', '<', now()->startOfDay())
            ->orderBy('fecha_aviso', 'desc')
            ->limit($this->limitePasados)
            ->get();

        return [
            'hoy'     => $futuros->filter(fn($a) => $a->estado === 'hoy'),
            'proximo' => $futuros->filter(fn($a) => $a->estado === 'proximo'),
            'pasado'  => $pasados,
        ];
    }

    #[Computed]
    public function totalPasados(): int
    {
        return $this->baseAvisosQuery()
            ->where('fecha_aviso', '<', now()->startOfDay())
            ->count();
    }

    // ── Acciones ──────────────────────────────────────────────────────────────

    public function guardar(): void
    {
        if ($this->sinPermiso('acceso_docencia')) return;

        $this->validate();

        if (! $this->asignacionId) {
            $this->addError('asignacionId', 'Selecciona una materia.');
            return;
        }

        // Sanear HTML antes de persistir
        $descripcionLimpia = strip_tags(
            $this->descripcion,
            '<p><br><strong><b><em><i><u><ul><ol><li><a>'
        );
        // Evitar links javascript:
        $descripcionLimpia = preg_replace(
            '/href\s*=\s*["\']javascript:[^"\']*["\']/i',
            'href="#"',
            $descripcionLimpia
        );

        if (trim(strip_tags($descripcionLimpia)) === '') {
            $this->addError('descripcion', 'La descripción es obligatoria.');
            return;
        }

        Aviso::create([
            'asignacion_docente_id' => $this->asignacionId,
            'titulo'                => $this->titulo,
            'descripcion'           => $descripcionLimpia,
            'tipo'                  => $this->tipo,
            'fecha_aviso'           => $this->fechaAviso,
        ]);

        $this->reset('titulo', 'descripcion', 'tipo');
        $this->fechaAviso        = now()->format('Y-m-d');
        $this->mostrarFormulario = false;

        unset($this->avisos);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Aviso publicado',
            'toast' => true,
            'timer' => 2000,
        ]);
    }

    public function eliminar(int $id): void
    {
        if ($this->sinPermiso('acceso_docencia')) return;

        $aviso = Aviso::whereHas('asignacionDocente', fn($q) =>
            $q->where('docente_id', Auth::id())
        )->findOrFail($id);

        $aviso->delete();

        unset($this->avisos);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Aviso eliminado',
            'toast' => true,
            'timer' => 1500,
        ]);
    }

    public function toggleSeccionPasados(): void
    {
        $this->seccionPasadosAbierta = !$this->seccionPasadosAbierta;
    }

    public function cargarMasPasados(): void
    {
        $this->limitePasados += 10;
        $this->seccionPasadosAbierta = true;
        unset($this->avisos);
    }

    public function updatedAsignacionId(): void
    {
        $this->limitePasados          = 10;
        $this->seccionPasadosAbierta  = false;
        unset($this->avisos);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.docente.avisos-docente');
    }
}
