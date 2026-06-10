<?php

namespace App\Livewire\Administration;

use App\Models\TipoSolicitud;
use App\Traits\WithAuthorization;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class TiposSolicitudesGestion extends Component
{
    use WithPagination, WithAuthorization;

    public string $search = '';

    public bool $showModal = false;
    public ?int  $tipoId   = null;

    public string $nombre            = '';
    public string $descripcion       = '';
    public float  $precio            = 0.00;
    public bool   $requiereDocumento = true;
    public string $tipoCertificado   = '';
    public bool   $notificaDocente   = false;
    public bool   $isActive          = true;

    public function mount(): void
    {
        $this->requierePermiso('gestionar_tipos_solicitudes');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function tipos()
    {
        return TipoSolicitud::when(
            $this->search,
            fn($q) => $q->where('nombre', 'like', '%' . $this->search . '%')
        )
            ->orderBy('nombre')
            ->paginate(15);
    }

    public function abrirModalCrear(): void
    {
        $this->reset(['tipoId', 'nombre', 'descripcion']);
        $this->precio            = 0.00;
        $this->requiereDocumento = true;
        $this->tipoCertificado   = '';
        $this->notificaDocente   = false;
        $this->isActive          = true;
        $this->showModal         = true;
    }

    public function abrirModalEditar(int $id): void
    {
        $tipo = TipoSolicitud::findOrFail($id);
        $this->tipoId            = $tipo->id;
        $this->nombre            = $tipo->nombre;
        $this->descripcion       = $tipo->descripcion ?? '';
        $this->precio            = (float) $tipo->precio;
        $this->requiereDocumento = $tipo->requiere_documento;
        $this->tipoCertificado   = $tipo->tipo_certificado ?? '';
        $this->notificaDocente   = $tipo->notifica_docente;
        $this->isActive          = $tipo->is_active;
        $this->showModal         = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->reset(['tipoId', 'nombre', 'descripcion', 'precio', 'requiereDocumento', 'tipoCertificado', 'notificaDocente', 'isActive']);
        $this->precio = 0.00;
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_tipos_solicitudes')) return;

        $this->validate([
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'precio'      => 'required|numeric|min:0',
        ]);

        $data = [
            'nombre'             => trim($this->nombre),
            'descripcion'        => trim($this->descripcion) ?: null,
            'precio'             => $this->precio,
            'requiere_documento' => $this->requiereDocumento,
            'tipo_certificado'   => $this->tipoCertificado ?: null,
            'notifica_docente'   => $this->notificaDocente,
            'is_active'          => $this->isActive,
        ];

        if ($this->tipoId) {
            TipoSolicitud::findOrFail($this->tipoId)->update($data);
            $msg = 'Tipo de solicitud actualizado correctamente.';
        } else {
            TipoSolicitud::create($data);
            $msg = 'Tipo de solicitud creado correctamente.';
        }

        unset($this->tipos);
        $this->cerrarModal();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => $msg]);
    }

    public function toggleActivo(int $id): void
    {
        if ($this->sinPermiso('gestionar_tipos_solicitudes')) return;

        $tipo = TipoSolicitud::findOrFail($id);
        $tipo->update(['is_active' => ! $tipo->is_active]);
        unset($this->tipos);

        $label = $tipo->is_active ? 'activado' : 'desactivado';
        $this->dispatch('swal', [
            'toast' => true,
            'icon'  => 'success',
            'title' => "Tipo {$label}.",
        ]);
    }

    public function render()
    {
        return view('livewire.administration.tipos-solicitudes-gestion', [
            'tipos' => $this->tipos,
        ]);
    }
}
