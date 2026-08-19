<?php

namespace App\Livewire\Administration;

use App\Models\TipoBeca;
use App\Traits\WithAuthorization;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class TiposBecaGestion extends Component
{
    use WithPagination, WithAuthorization;

    public string $search   = '';
    public bool   $showModal = false;
    public ?int   $tipoId   = null;

    public string $nombre               = '';
    public string $categoria            = '';
    public string $porcentaje_descuento = '';
    public string $descripcion          = '';
    public bool   $isActive             = true;

    const CATEGORIAS = [
        'Excelencia Academica'     => 'Excelencia Académica',
        'Socioeconomica'           => 'Socioeconómica',
        'Discapacidad'             => 'Discapacidad',
        'Deportista'               => 'Deportista',
        'Artistica'                => 'Artística',
        'Pueblos y Nacionalidades' => 'Pueblos y Nacionalidades',
        'Migrante Retornado'       => 'Migrante Retornado',
        'Emergente'                => 'Emergente',
    ];

    public function mount(): void
    {
        $this->requierePermiso('gestionar_becas');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function tipos()
    {
        return TipoBeca::when(
            $this->search,
            fn($q) => $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('categoria', 'like', '%' . $this->search . '%')
        )
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->paginate(20);
    }

    public function abrirModalCrear(): void
    {
        $this->reset(['tipoId', 'nombre', 'categoria', 'porcentaje_descuento', 'descripcion']);
        $this->isActive  = true;
        $this->showModal = true;
    }

    public function abrirModalEditar(int $id): void
    {
        $tipo = TipoBeca::findOrFail($id);
        $this->tipoId               = $tipo->id;
        $this->nombre               = $tipo->nombre;
        $this->categoria            = $tipo->categoria;
        $this->porcentaje_descuento = (string) $tipo->porcentaje_descuento;
        $this->descripcion          = $tipo->descripcion ?? '';
        $this->isActive             = $tipo->is_active;
        $this->showModal            = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->reset(['tipoId', 'nombre', 'categoria', 'porcentaje_descuento', 'descripcion', 'isActive']);
        $this->isActive = true;
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_becas')) return;

        $this->validate([
            'nombre'               => 'required|string|max:100',
            'categoria'            => 'required|in:' . implode(',', array_keys(self::CATEGORIAS)),
            'porcentaje_descuento' => 'required|numeric|min:0|max:100',
            'descripcion'          => 'nullable|string|max:500',
        ]);

        $data = [
            'nombre'               => trim($this->nombre),
            'categoria'            => $this->categoria,
            'porcentaje_descuento' => $this->porcentaje_descuento,
            'descripcion'          => trim($this->descripcion) ?: null,
            'is_active'            => $this->isActive,
        ];

        if ($this->tipoId) {
            TipoBeca::findOrFail($this->tipoId)->update($data);
            $msg = 'Tipo de beca actualizado.';
        } else {
            TipoBeca::create($data);
            $msg = 'Tipo de beca creado.';
        }

        unset($this->tipos);
        $this->cerrarModal();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => $msg]);
    }

    public function toggleActivo(int $id): void
    {
        if ($this->sinPermiso('gestionar_becas')) return;

        $tipo = TipoBeca::findOrFail($id);
        $tipo->update(['is_active' => !$tipo->is_active]);
        unset($this->tipos);

        $label = ! $tipo->is_active ? 'activado' : 'desactivado';
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => "Tipo {$label}."]);
    }

    public function render()
    {
        return view('livewire.administration.tipos-beca-gestion', [
            'tipos' => $this->tipos,
        ]);
    }
}
