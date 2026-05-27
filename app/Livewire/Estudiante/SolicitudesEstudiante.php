<?php

namespace App\Livewire\Estudiante;

use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.estudiantil')]
class SolicitudesEstudiante extends Component
{
    use WithPagination;

    public bool   $showForm       = false;
    public ?int   $tipoSolicitudId = null;
    public string $descripcion    = '';

    public string $filtroEstado = '';

    public function updatedFiltroEstado(): void { $this->resetPage(); }
    public function updatedTipoSolicitudId(): void { unset($this->tipoSeleccionado); }

    #[Computed]
    public function tipos()
    {
        return TipoSolicitud::where('is_active', true)->orderBy('nombre')->get();
    }

    #[Computed]
    public function tipoSeleccionado(): ?TipoSolicitud
    {
        if (! $this->tipoSolicitudId) return null;
        return TipoSolicitud::find($this->tipoSolicitudId);
    }

    #[Computed]
    public function solicitudes()
    {
        return Solicitud::with(['tipoSolicitud', 'obligacion'])
            ->where('estudiante_id', Auth::id())
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->latest()
            ->paginate(10);
    }

    public function enviarSolicitud(): void
    {
        $this->validate([
            'tipoSolicitudId' => 'required|exists:tipos_solicitudes,id',
            'descripcion'     => 'required|string|min:10|max:1000',
        ], [
            'tipoSolicitudId.required' => 'Seleccione el tipo de solicitud.',
            'descripcion.required'     => 'Describa el motivo de su solicitud.',
            'descripcion.min'          => 'La descripción debe tener al menos 10 caracteres.',
        ]);

        $tipo = TipoSolicitud::findOrFail($this->tipoSolicitudId);

        Solicitud::create([
            'estudiante_id'    => Auth::id(),
            'tipo_solicitud_id'=> $tipo->id,
            'descripcion'      => trim($this->descripcion),
            'estado'           => 'pendiente',
            'precio_aplicado'  => $tipo->precio,
        ]);

        $this->reset(['tipoSolicitudId', 'descripcion', 'showForm']);
        unset($this->solicitudes, $this->tipoSeleccionado);

        $this->dispatch('solicitud-enviada');
    }

    public function render()
    {
        return view('livewire.estudiante.solicitudes-estudiante', [
            'tipos'      => $this->tipos,
            'solicitudes'=> $this->solicitudes,
        ]);
    }
}
