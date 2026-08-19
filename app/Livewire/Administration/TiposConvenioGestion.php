<?php

namespace App\Livewire\Administration;

use App\Models\TipoConvenio;
use App\Traits\WithAuthorization;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class TiposConvenioGestion extends Component
{
    use WithPagination, WithAuthorization;

    public string $search    = '';
    public bool   $showModal = false;
    public ?int   $tipoId    = null;

    public string $nombre             = '';
    public string $descripcion        = '';
    public string $porcentaje_defecto = '';
    public string $tipo_alcance       = 'anual';
    public bool   $requiere_documento = false;
    public bool   $isActive           = true;

    public function mount(): void
    {
        $this->requierePermiso('gestionar_convenios');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function tipos()
    {
        return TipoConvenio::when(
            $this->search,
            fn ($q) => $q->where('nombre', 'like', '%' . $this->search . '%')
                         ->orWhere('descripcion', 'like', '%' . $this->search . '%')
        )
            ->orderBy('nombre')
            ->paginate(20);
    }

    public function abrirModalCrear(): void
    {
        $this->reset(['tipoId', 'nombre', 'descripcion', 'porcentaje_defecto', 'tipo_alcance', 'requiere_documento']);
        $this->tipo_alcance = 'anual';
        $this->isActive     = true;
        $this->showModal    = true;
    }

    public function abrirModalEditar(int $id): void
    {
        $tipo = TipoConvenio::findOrFail($id);
        $this->tipoId             = $tipo->id;
        $this->nombre             = $tipo->nombre;
        $this->descripcion        = $tipo->descripcion ?? '';
        $this->porcentaje_defecto = (string) $tipo->porcentaje_defecto;
        $this->tipo_alcance       = $tipo->tipo_alcance;
        $this->requiere_documento = $tipo->requiere_documento;
        $this->isActive           = $tipo->is_active;
        $this->showModal          = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->reset(['tipoId', 'nombre', 'descripcion', 'porcentaje_defecto', 'tipo_alcance', 'requiere_documento']);
        $this->tipo_alcance = 'anual';
        $this->isActive     = true;
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_convenios')) return;

        $this->validate([
            'nombre'             => 'required|string|max:100',
            'descripcion'        => 'nullable|string|max:500',
            'porcentaje_defecto' => 'required|numeric|min:0|max:100',
            'tipo_alcance'       => 'required|in:anual,semestral',
        ]);

        $data = [
            'nombre'             => trim($this->nombre),
            'descripcion'        => trim($this->descripcion) ?: null,
            'porcentaje_defecto' => $this->porcentaje_defecto,
            'tipo_alcance'       => $this->tipo_alcance,
            'requiere_documento' => $this->requiere_documento,
            'is_active'          => $this->isActive,
        ];

        if ($this->tipoId) {
            TipoConvenio::findOrFail($this->tipoId)->update($data);
            $msg = 'Tipo de convenio actualizado.';
        } else {
            TipoConvenio::create($data);
            $msg = 'Tipo de convenio creado.';
        }

        unset($this->tipos);
        $this->cerrarModal();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => $msg]);
    }

    public function toggleActivo(int $id): void
    {
        if ($this->sinPermiso('gestionar_convenios')) return;

        $tipo = TipoConvenio::findOrFail($id);
        $tipo->update(['is_active' => ! $tipo->is_active]);
        unset($this->tipos);

        $label = ! $tipo->is_active ? 'activado' : 'desactivado';
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => "Tipo {$label}."]);
    }

    public function render()
    {
        return view('livewire.administration.tipos-convenio-gestion', [
            'tipos' => $this->tipos,
        ]);
    }
}
