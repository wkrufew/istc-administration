<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\Cohorte;
use App\Traits\WithAuthorization;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Layout;

class GestionCohortes extends Component
{
    use WithAuthorization;

    // ── Filtros ────────────────────────────────────────────────────────────────
    public string $buscar           = '';
    public string $filtroEstado     = '';
    public string $filtroCarrera    = '';

    // ── Modal ─────────────────────────────────────────────────────────────────
    public bool $showModal      = false;
    public bool $isEditing      = false;
    public ?int $editingId      = null;

    // ── Formulario ─────────────────────────────────────────────────────────────
    public string $nombre                    = '';
    public ?int   $carrera_id               = null;
    public string $fecha_inicio_matriculacion = '';
    public string $fecha_inicio_clases        = '';
    public string $estado                    = 'abierto';
    public string $descripcion               = '';

    protected function rules(): array
    {
        return [
            'nombre'                     => 'required|string|max:255',
            'carrera_id'                 => 'required|exists:carreras,id',
            'fecha_inicio_matriculacion' => 'nullable|date',
            'fecha_inicio_clases'        => 'nullable|date',
            'estado'                     => 'required|in:abierto,cerrado',
            'descripcion'                => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'nombre.required'   => 'El nombre es obligatorio.',
        'carrera_id.required' => 'Selecciona una carrera.',
        'carrera_id.exists' => 'La carrera seleccionada no existe.',
    ];

    public function mount(): void
    {
        $this->requierePermiso('gestionar_cohortes');
    }

    #[Computed]
    public function carreras()
    {
        return Carrera::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function cohortes()
    {
        return Cohorte::with(['carrera', 'creadoPor'])
            ->withCount('aspirantes')
            ->when($this->buscar, fn ($q) => $q->where('nombre', 'like', "%{$this->buscar}%"))
            ->when($this->filtroEstado, fn ($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroCarrera, fn ($q) => $q->where('carrera_id', $this->filtroCarrera))
            ->orderByDesc('created_at')
            ->get();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.gestion-cohortes', [
            'cohortes' => $this->cohortes,
            'carreras' => $this->carreras,
        ]);
    }

    // ── Crear ─────────────────────────────────────────────────────────────────

    public function abrirCrear(): void
    {
        if ($this->sinPermiso('gestionar_cohortes')) return;
        $this->resetForm();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_cohortes')) return;

        $data = $this->validate();

        if ($this->isEditing && $this->editingId) {
            $cohorte = Cohorte::findOrFail($this->editingId);
            $cohorte->update($data);
            $mensaje = 'Cohorte actualizada correctamente.';
        } else {
            $data['creado_por'] = auth()->id();
            Cohorte::create($data);
            $mensaje = 'Cohorte creada correctamente.';
        }

        $this->showModal = false;
        $this->resetForm();
        unset($this->cohortes);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Guardado',
            'text'  => $mensaje,
            'timer' => 3000,
        ]);
    }

    // ── Editar ─────────────────────────────────────────────────────────────────

    public function editar(int $id): void
    {
        if ($this->sinPermiso('gestionar_cohortes')) return;

        $cohorte = Cohorte::findOrFail($id);
        $this->editingId                   = $id;
        $this->nombre                      = $cohorte->nombre;
        $this->carrera_id                  = $cohorte->carrera_id;
        $this->fecha_inicio_matriculacion  = $cohorte->fecha_inicio_matriculacion?->format('Y-m-d') ?? '';
        $this->fecha_inicio_clases         = $cohorte->fecha_inicio_clases?->format('Y-m-d') ?? '';
        $this->estado                      = $cohorte->estado;
        $this->descripcion                 = $cohorte->descripcion ?? '';
        $this->isEditing                   = true;
        $this->showModal                   = true;
    }

    // ── Toggle estado ──────────────────────────────────────────────────────────

    public function toggleEstado(int $id): void
    {
        if ($this->sinPermiso('gestionar_cohortes')) return;

        $cohorte = Cohorte::findOrFail($id);
        $nuevoEstado = $cohorte->estado === 'abierto' ? 'cerrado' : 'abierto';
        $cohorte->update(['estado' => $nuevoEstado]);
        unset($this->cohortes);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Estado actualizado',
            'text'  => "Cohorte marcada como {$nuevoEstado}.",
            'timer' => 2500,
        ]);
    }

    // ── Eliminar ──────────────────────────────────────────────────────────────

    public function eliminar(int $id): void
    {
        if ($this->sinPermiso('gestionar_cohortes')) return;

        $cohorte = Cohorte::withCount('aspirantes')->findOrFail($id);

        if ($cohorte->aspirantes_count > 0) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'No se puede eliminar',
                'text'  => 'La cohorte tiene aspirantes registrados. Ciérrala en lugar de eliminarla.',
                'timer' => 4000,
            ]);
            return;
        }

        $cohorte->delete();
        unset($this->cohortes);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Eliminada',
            'text'  => 'La cohorte fue eliminada.',
            'timer' => 2500,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->nombre                    = '';
        $this->carrera_id               = null;
        $this->fecha_inicio_matriculacion = '';
        $this->fecha_inicio_clases        = '';
        $this->estado                    = 'abierto';
        $this->descripcion               = '';
        $this->resetValidation();
    }
}
