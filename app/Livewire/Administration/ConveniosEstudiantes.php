<?php

namespace App\Livewire\Administration;

use App\Models\ConvenioAplicado;
use App\Models\TipoConvenio;
use App\Models\User;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class ConveniosEstudiantes extends Component
{
    use WithPagination, WithAuthorization, WithFileUploads;

    public string $search = '';

    // Modal asignar / editar
    public bool   $showModal     = false;
    public ?int   $convenioId    = null;
    public ?int   $estudianteId  = null;

    // Campos del formulario
    public ?int   $tipoConvenioId      = null;
    public string $porcentajeAplicado  = '';
    public string $motivo              = '';
    public        $documento           = null;
    public string $observacion         = '';
    public string $fechaInicio         = '';
    public string $fechaFin            = '';

    // Modal revocar
    public bool   $showRevocarModal   = false;
    public ?int   $convenioRevocarId  = null;
    public string $motivoRevocacion   = '';

    public function mount(): void
    {
        $this->requierePermiso('gestionar_convenios');
        $this->fechaInicio = now()->toDateString();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function estudiantes()
    {
        return User::role('Estudiante')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('cedula', 'like', '%' . $this->search . '%')
                       ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['conveniosAplicados' => fn($q) => $q->where('is_active', true)
                ->where(fn($q2) => $q2->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
                ->with('tipoConvenio')
            ])
            ->orderBy('name')
            ->paginate(20);
    }

    #[Computed]
    public function tiposConvenio()
    {
        return TipoConvenio::where('is_active', true)->orderBy('nombre')->get();
    }

    public function updatedTipoConvenioId(): void
    {
        $tipo = TipoConvenio::find($this->tipoConvenioId);
        if ($tipo) {
            $this->porcentajeAplicado = (string) $tipo->porcentaje_defecto;
            if ($tipo->tipo_alcance === 'semestral') {
                $this->fechaFin = '';
            }
        } else {
            $this->porcentajeAplicado = '';
        }
    }

    public function abrirModalAsignar(int $estudianteId): void
    {
        $this->reset(['convenioId', 'tipoConvenioId', 'porcentajeAplicado', 'motivo', 'documento', 'observacion', 'fechaFin', 'motivoRevocacion']);
        $this->estudianteId = $estudianteId;
        $this->fechaInicio  = now()->toDateString();
        $this->showModal    = true;
    }

    public function abrirModalEditar(int $convenioId): void
    {
        $convenio = ConvenioAplicado::with('tipoConvenio')->findOrFail($convenioId);
        $this->convenioId         = $convenio->id;
        $this->estudianteId       = $convenio->user_id;
        $this->tipoConvenioId     = $convenio->tipo_convenio_id;
        $this->porcentajeAplicado = (string) $convenio->porcentaje_aplicado;
        $this->motivo             = $convenio->motivo ?? '';
        $this->observacion        = $convenio->observacion ?? '';
        $this->fechaInicio        = $convenio->fecha_inicio->toDateString();
        $this->fechaFin           = $convenio->fecha_fin?->toDateString() ?? '';
        $this->showModal          = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->reset(['convenioId', 'estudianteId', 'tipoConvenioId', 'porcentajeAplicado', 'motivo', 'documento', 'observacion', 'fechaFin']);
        $this->fechaInicio = now()->toDateString();
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_convenios')) return;

        $this->validate([
            'estudianteId'        => 'required|exists:users,id',
            'tipoConvenioId'      => 'required|exists:tipos_convenio,id',
            'porcentajeAplicado'  => 'required|numeric|min:0|max:100',
            'motivo'              => 'nullable|string|max:500',
            'documento'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'observacion'         => 'nullable|string|max:500',
            'fechaInicio'         => 'required|date',
            'fechaFin'            => 'nullable|date|after_or_equal:fechaInicio',
        ]);

        if (!$this->convenioId) {
            $yaActivo = ConvenioAplicado::where('user_id', $this->estudianteId)
                ->where('is_active', true)
                ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
                ->exists();

            if ($yaActivo) {
                $this->addError('tipoConvenioId', 'Este estudiante ya tiene un convenio activo. Revoca el actual antes de asignar otro.');
                return;
            }
        }

        $documentoPath = null;
        if ($this->documento) {
            $documentoPath = $this->documento->store('convenios/documentos', 'public');
        }

        $data = [
            'user_id'             => $this->estudianteId,
            'tipo_convenio_id'    => $this->tipoConvenioId,
            'porcentaje_aplicado' => $this->porcentajeAplicado,
            'motivo'              => trim($this->motivo) ?: null,
            'observacion'         => trim($this->observacion) ?: null,
            'fecha_inicio'        => $this->fechaInicio,
            'fecha_fin'           => $this->fechaFin ?: null,
            'is_active'           => true,
            'registrado_por'      => Auth::id(),
        ];

        if ($documentoPath) {
            $data['documento_path'] = $documentoPath;
        }

        if ($this->convenioId) {
            ConvenioAplicado::findOrFail($this->convenioId)->update($data);
            $msg = 'Convenio actualizado correctamente.';
        } else {
            ConvenioAplicado::create($data);
            $msg = 'Convenio asignado correctamente.';
        }

        unset($this->estudiantes);
        $this->cerrarModal();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => $msg]);
    }

    public function abrirRevocar(int $convenioId): void
    {
        $this->convenioRevocarId = $convenioId;
        $this->motivoRevocacion  = '';
        $this->showRevocarModal  = true;
    }

    public function cerrarRevocar(): void
    {
        $this->showRevocarModal = false;
        $this->reset(['convenioRevocarId', 'motivoRevocacion']);
    }

    public function revocar(): void
    {
        if ($this->sinPermiso('gestionar_convenios')) return;

        $convenio = ConvenioAplicado::findOrFail($this->convenioRevocarId);
        $convenio->update([
            'is_active'  => false,
            'observacion' => $convenio->observacion
                ? $convenio->observacion . ' | REVOCADO: ' . trim($this->motivoRevocacion)
                : 'REVOCADO: ' . trim($this->motivoRevocacion),
        ]);

        unset($this->estudiantes);
        $this->cerrarRevocar();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => 'Convenio revocado.']);
    }

    public function render()
    {
        return view('livewire.administration.convenios-estudiantes', [
            'estudiantes'   => $this->estudiantes,
            'tiposConvenio' => $this->tiposConvenio,
        ]);
    }
}
