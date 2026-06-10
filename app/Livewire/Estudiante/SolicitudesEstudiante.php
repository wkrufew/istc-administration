<?php

namespace App\Livewire\Estudiante;

use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.estudiantil')]
class SolicitudesEstudiante extends Component
{
    use WithPagination, WithFileUploads;

    public bool   $showForm        = false;
    public ?int   $tipoSolicitudId = null;
    public string $descripcion     = '';
    public $documento              = null;

    public string $filtroEstado = '';

    public function updatedFiltroEstado(): void { $this->resetPage(); }
    public function updatedTipoSolicitudId(): void
    {
        unset($this->tipoSeleccionado);
        $this->documento = null;
    }

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
        // find() en lugar de findOrFail() — la validación atrapa el caso null/inválido
        $tipo = TipoSolicitud::find($this->tipoSolicitudId);

        $rules = [
            'tipoSolicitudId' => 'required|exists:tipos_solicitudes,id',
            'descripcion'     => 'required|string|min:10|max:1000',
        ];

        if ($tipo?->requiere_documento) {
            $rules['documento'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        $this->validate($rules, [
            'tipoSolicitudId.required' => 'Seleccione el tipo de solicitud.',
            'descripcion.required'     => 'Describa el motivo de su solicitud.',
            'descripcion.min'          => 'La descripción debe tener al menos 10 caracteres.',
            'documento.required'       => 'Este tipo de solicitud requiere adjuntar un documento.',
            'documento.mimes'          => 'El documento debe ser PDF, JPG o PNG.',
            'documento.max'            => 'El documento no puede superar los 5 MB.',
        ]);

        $documentoPath = null;
        if ($tipo?->requiere_documento && $this->documento) {
            $user     = Auth::user();
            $slug     = Str::slug($user->name);
            $ext      = $this->documento->getClientOriginalExtension();
            $filename = "{$slug}-solicitud-" . time() . ".{$ext}";
            $documentoPath = $this->documento->storeAs(
                'solicitudes-documentos/' . $user->id,
                $filename,
                'public'
            );
        }

        Solicitud::create([
            'estudiante_id'    => Auth::id(),
            'tipo_solicitud_id'=> $tipo->id,
            'descripcion'      => trim($this->descripcion),
            'documento_path'   => $documentoPath,
            'estado'           => 'pendiente',
            'precio_aplicado'  => $tipo->precio,
        ]);

        $this->reset(['tipoSolicitudId', 'descripcion', 'showForm', 'documento']);
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
