<?php

namespace App\Livewire\Administration;

use App\Models\BecaAplicada;
use App\Models\TipoBeca;
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
class BecasEstudiantes extends Component
{
    use WithPagination, WithAuthorization, WithFileUploads;

    public string $search = '';

    // Modal asignar
    public bool   $showModal   = false;
    public ?int   $becaId      = null;
    public ?int   $estudianteId = null;

    // Campos del formulario
    public ?int   $tipoBecaId             = null;
    public string $porcentajeAplicado     = '';
    public ?int   $porcentajeDiscapacidad = null;
    public        $documento              = null;
    public string $observacion            = '';
    public string $fechaAsignacion        = '';

    // Modal revocar
    public bool   $showRevocarModal = false;
    public ?int   $becaRevocarId    = null;
    public string $motivo           = '';

    public function mount(): void
    {
        $this->requierePermiso('gestionar_becas');
        $this->fechaAsignacion = now()->toDateString();
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
            ->with(['becasAplicadas' => fn($q) => $q->where('is_active', true)->with('tipoBeca')])
            ->orderBy('name')
            ->paginate(20);
    }

    #[Computed]
    public function tiposBeca()
    {
        return TipoBeca::where('is_active', true)->orderBy('categoria')->orderBy('nombre')->get();
    }

    public function updatedTipoBecaId(): void
    {
        $tipo = TipoBeca::find($this->tipoBecaId);
        if ($tipo) {
            $this->porcentajeAplicado = (string) $tipo->porcentaje_descuento;
            if ($tipo->categoria !== 'Discapacidad') {
                $this->porcentajeDiscapacidad = null;
            }
        } else {
            $this->porcentajeAplicado     = '';
            $this->porcentajeDiscapacidad = null;
        }
    }

    public function updatedPorcentajeDiscapacidad(): void
    {
        $pct = (int) $this->porcentajeDiscapacidad;
        if ($pct >= 75) {
            $this->porcentajeAplicado = '100';
        } elseif ($pct >= 50) {
            $this->porcentajeAplicado = '75';
        } elseif ($pct >= 35) {
            $this->porcentajeAplicado = '50';
        }
    }

    public function abrirModalAsignar(int $estudianteId): void
    {
        $this->reset(['becaId', 'tipoBecaId', 'porcentajeAplicado', 'porcentajeDiscapacidad', 'documento', 'observacion', 'motivo']);
        $this->estudianteId    = $estudianteId;
        $this->fechaAsignacion = now()->toDateString();
        $this->showModal       = true;
    }

    public function abrirModalEditar(int $becaId): void
    {
        $beca = BecaAplicada::with('tipoBeca')->findOrFail($becaId);
        $this->becaId                 = $beca->id;
        $this->estudianteId           = $beca->user_id;
        $this->tipoBecaId             = $beca->tipo_beca_id;
        $this->porcentajeAplicado     = (string) $beca->porcentaje_aplicado;
        $this->porcentajeDiscapacidad = $beca->porcentaje_discapacidad;
        $this->observacion            = $beca->observacion ?? '';
        $this->fechaAsignacion        = $beca->fecha_asignacion->toDateString();
        $this->showModal              = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->reset(['becaId', 'estudianteId', 'tipoBecaId', 'porcentajeAplicado', 'porcentajeDiscapacidad', 'documento', 'observacion']);
        $this->fechaAsignacion = now()->toDateString();
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_becas')) return;

        $tipo = TipoBeca::find($this->tipoBecaId);

        $this->validate([
            'estudianteId'          => 'required|exists:users,id',
            'tipoBecaId'            => 'required|exists:tipos_beca,id',
            'porcentajeAplicado'    => 'required|numeric|min:0|max:100',
            'porcentajeDiscapacidad' => $tipo && $tipo->categoria === 'Discapacidad'
                                        ? 'required|integer|min:35|max:100'
                                        : 'nullable|integer',
            'documento'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'observacion'           => 'nullable|string|max:500',
            'fechaAsignacion'       => 'required|date',
        ]);

        // Si es nueva asignación, verificar que no tenga beca activa
        if (!$this->becaId) {
            $yaActiva = BecaAplicada::where('user_id', $this->estudianteId)
                ->where('is_active', true)
                ->exists();

            if ($yaActiva) {
                $this->addError('tipoBecaId', 'Este estudiante ya tiene una beca activa. Revoca la actual antes de asignar otra.');
                return;
            }
        }

        $documentoPath = null;
        if ($this->documento) {
            $documentoPath = $this->documento->store('becas/documentos', 'public');
        }

        $data = [
            'user_id'                => $this->estudianteId,
            'tipo_beca_id'           => $this->tipoBecaId,
            'porcentaje_aplicado'    => $this->porcentajeAplicado,
            'porcentaje_discapacidad' => $this->porcentajeDiscapacidad,
            'observacion'            => trim($this->observacion) ?: null,
            'fecha_asignacion'       => $this->fechaAsignacion,
            'fecha_ultima_revision'  => now()->toDateString(),
            'is_active'              => true,
            'asignado_por'           => Auth::id(),
        ];

        if ($documentoPath) {
            $data['documento_path'] = $documentoPath;
        }

        if ($this->becaId) {
            BecaAplicada::findOrFail($this->becaId)->update($data);
            $msg = 'Beca actualizada correctamente.';
        } else {
            BecaAplicada::create($data);
            $msg = 'Beca asignada correctamente.';
        }

        unset($this->estudiantes);
        $this->cerrarModal();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => $msg]);
    }

    public function abrirRevocar(int $becaId): void
    {
        $this->becaRevocarId    = $becaId;
        $this->motivo           = '';
        $this->showRevocarModal = true;
    }

    public function cerrarRevocar(): void
    {
        $this->showRevocarModal = false;
        $this->reset(['becaRevocarId', 'motivo']);
    }

    public function revocar(): void
    {
        if ($this->sinPermiso('gestionar_becas')) return;

        $beca = BecaAplicada::findOrFail($this->becaRevocarId);
        $beca->update([
            'is_active'             => false,
            'fecha_ultima_revision' => now()->toDateString(),
            'observacion'           => $beca->observacion
                ? $beca->observacion . ' | REVOCADA: ' . trim($this->motivo)
                : 'REVOCADA: ' . trim($this->motivo),
        ]);

        unset($this->estudiantes);
        $this->cerrarRevocar();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => 'Beca revocada.']);
    }

    public function render()
    {
        return view('livewire.administration.becas-estudiantes', [
            'estudiantes' => $this->estudiantes,
            'tiposBeca'   => $this->tiposBeca,
        ]);
    }
}
