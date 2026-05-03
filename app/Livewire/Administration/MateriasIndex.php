<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Semestre;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class MateriasIndex extends Component
{
    use WithPagination;

    // ── Filtros del listado ─────────────────────────────────────────────
    public string $search = '';
    public string $tipoFilter = '';
    public string $carreraFilter = '';

    // ── Estado del modal ────────────────────────────────────────────────
    public bool $modalAbierto = false;
    public ?int $editandoId = null;
    public string $tabActiva = 'datos'; // 'datos' | 'prerequisitos'

    // ── Campos del formulario ───────────────────────────────────────────
    public string $name = '';
    public string $code = '';
    public string $description = '';
    public string $credits = '';
    public string $horas_teoricas = '';
    public string $horas_practicas = '';
    public string $nota_minima_aprobacion = '7.00';
    public string $tipo = 'Obligatoria';
    public string $semestre_id = '';
    public bool   $is_active = true;

    // Carrera auxiliar para filtrar el select de semestres en el form
    public string $carreraIdForm = '';

    // Cuando true, el usuario editó créditos manualmente → no recalcular
    public bool $creditosOverride = false;

    // ── Reset de paginación al cambiar filtros ──────────────────────────
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedTipoFilter(): void
    {
        $this->resetPage();
    }
    public function updatedCarreraFilter(): void
    {
        $this->resetPage();
    }

    // ── Auto-cálculo de créditos ────────────────────────────────────────
    public function updatedHorasTeoricas(): void
    {
        if (! $this->creditosOverride) {
            $this->calcularCreditos();
        }
    }

    public function updatedHorasPracticas(): void
    {
        if (! $this->creditosOverride) {
            $this->calcularCreditos();
        }
    }

    // El usuario escribió directamente en el campo créditos → marcar override
    public function updatedCredits(): void
    {
        $this->creditosOverride = true;
    }

    // Botón "↺ Recalcular" en la vista: limpia override y recalcula
    public function resetCreditos(): void
    {
        $this->creditosOverride = false;
        $this->calcularCreditos();
    }

    private function calcularCreditos(): void
    {
        $t = (int) $this->horas_teoricas;
        $p = (int) $this->horas_practicas;
        if ($t + $p > 0) {
            $this->credits = number_format(($t + $p) / 48, 2);
        }
    }

    // Al cambiar la carrera en el form, vaciar semestre seleccionado
    public function updatedCarreraIdForm(): void
    {
        $this->semestre_id = '';
    }

    // ── Computed properties ─────────────────────────────────────────────
    #[Computed]
    public function carreras()
    {
        return Carrera::orderBy('name')->get();
    }

    #[Computed]
    public function semestresForm()
    {
        if (! $this->carreraIdForm) {
            return collect();
        }
        return Semestre::where('carrera_id', $this->carreraIdForm)
            ->orderBy('order')
            ->get();
    }

    #[Computed]
    public function materias()
    {
        return Materia::with('semestre.carrera')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%");
            }))
            ->when($this->tipoFilter, fn($q) => $q->where('tipo', $this->tipoFilter))
            ->when($this->carreraFilter, fn($q) => $q->whereHas(
                'semestre',
                fn($q) => $q->where('carrera_id', $this->carreraFilter)
            ))
            ->orderBy('semestre_id')
            ->orderBy('name')
            ->paginate(15);
    }

    // ── Acciones del modal ──────────────────────────────────────────────
    public function abrirModalCrear(): void
    {
        $this->resetForm();
        $this->editandoId = null;
        $this->tabActiva  = 'datos';
        $this->modalAbierto = true;
        $this->dispatch('modal-scroll-lock');
    }

    public function abrirModalEditar(int $id): void
    {
        $materia = Materia::with('semestre')->findOrFail($id);
        $this->resetForm();

        $this->editandoId = $id;
        $this->tabActiva  = 'datos';

        $this->name                    = $materia->name;
        $this->code                    = $materia->code;
        $this->description             = $materia->description ?? '';
        $this->credits                 = (string) $materia->credits;
        $this->horas_teoricas          = (string) $materia->horas_teoricas;
        $this->horas_practicas         = (string) $materia->horas_practicas;
        $this->nota_minima_aprobacion  = (string) $materia->nota_minima_aprobacion;
        $this->tipo                    = $materia->tipo;
        $this->semestre_id             = (string) $materia->semestre_id;
        $this->is_active               = $materia->is_active;
        $this->creditosOverride        = true; // respetar créditos guardados

        if ($materia->semestre) {
            $this->carreraIdForm = (string) $materia->semestre->carrera_id;
        }

        $this->modalAbierto = true;
        $this->dispatch('modal-scroll-lock');
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->resetForm();
        $this->dispatch('modal-scroll-unlock');
    }

    // ── Guardar (crear o actualizar) ────────────────────────────────────
    public function guardar(): void
    {
        $uniqueRule = 'unique:materias,code' . ($this->editandoId ? ",{$this->editandoId}" : '');

        $data = $this->validate([
            'name'                   => 'required|string|max:255',
            'code'                   => "required|string|max:50|{$uniqueRule}",
            'description'            => 'nullable|string|max:1000',
            'credits'                => 'required|numeric|min:0|max:20',
            'horas_teoricas'         => 'required|integer|min:0',
            'horas_practicas'        => 'required|integer|min:0',
            'nota_minima_aprobacion' => 'required|numeric|min:0|max:10',
            'tipo'                   => 'required|in:Obligatoria,Electiva,Nivelacion',
            'semestre_id'            => 'required|exists:semestres,id',
        ]);

        $data['is_active'] = $this->is_active;

        if ($this->editandoId) {
            Materia::findOrFail($this->editandoId)->update($data);
            $this->dispatch('flash', type: 'success', message: 'Materia actualizada correctamente.');
        } else {
            Materia::create($data);
            $this->dispatch('flash', type: 'success', message: 'Materia creada correctamente.');
        }

        $this->cerrarModal();
    }

    // ── Eliminar ────────────────────────────────────────────────────────
    public function eliminar(int $id): void
    {
        Materia::findOrFail($id)->delete();
        $this->dispatch('flash', type: 'success', message: 'Materia eliminada correctamente.');
    }

    // ── Reset del formulario ────────────────────────────────────────────
    private function resetForm(): void
    {
        $this->name                   = '';
        $this->code                   = '';
        $this->description            = '';
        $this->credits                = '';
        $this->horas_teoricas         = '';
        $this->horas_practicas        = '';
        $this->nota_minima_aprobacion = '7.00';
        $this->tipo                   = 'Obligatoria';
        $this->semestre_id            = '';
        $this->is_active              = true;
        $this->carreraIdForm          = '';
        $this->creditosOverride       = false;
        $this->resetValidation();
    }

    #[Layout('layouts.admin')]
    public function render(): \Illuminate\View\View
    {
        return view('livewire.administration.materias-index');
    }
}
