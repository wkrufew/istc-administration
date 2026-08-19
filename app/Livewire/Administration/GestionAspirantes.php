<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarEmailBienvenidaAspirante;
use App\Jobs\NotificarEstadoAspirante;
use App\Jobs\NotificarObservacionDocumentoAspirante;
use App\Models\Aspirante;
use App\Models\Cohorte;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class GestionAspirantes extends Component
{
    use WithAuthorization, WithPagination;

    // ── Filtros (sincronizados con la URL) ────────────────────────────────
    #[Url(as: 'q')]
    public string $buscar       = '';

    #[Url(as: 'cohorte')]
    public string $filtroCohorte = '';

    #[Url(as: 'estado')]
    public string $filtroEstado = '';

    // ── Modal detalle / acción ────────────────────────────────────────────
    public bool    $showDetalle    = false;
    public ?int    $aspiranteId    = null;
    public string  $motivoRechazo  = '';
    public bool    $showRechazar   = false;

    // ── Modal cambiar estado ──────────────────────────────────────────────
    public bool    $showCambiarEstado = false;
    public string  $nuevoEstado       = '';
    public string  $observacion       = '';

    public function mount(): void
    {
        $this->requierePermiso('gestionar_aspirantes');
    }

    public function updatedBuscar(): void       { $this->resetPage(); }
    public function updatedFiltroCohorte(): void { $this->resetPage(); }
    public function updatedFiltroEstado(): void  { $this->resetPage(); }

    #[Computed]
    public function cohortes()
    {
        return Cohorte::with('carrera')->orderByDesc('created_at')->get(['id', 'nombre', 'carrera_id', 'estado']);
    }

    #[Computed]
    public function aspirantes()
    {
        return Aspirante::with(['user', 'cohorte.carrera', 'registradoPor'])
            ->when($this->buscar, function ($q) {
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%{$this->buscar}%")
                      ->orWhere('cedula', 'like', "%{$this->buscar}%")
                      ->orWhere('email', 'like', "%{$this->buscar}%")
                );
            })
            ->when($this->filtroCohorte, fn ($q) => $q->where('cohorte_id', $this->filtroCohorte))
            ->when($this->filtroEstado, fn ($q) => $q->where('estado', $this->filtroEstado))
            ->orderByDesc('created_at')
            ->paginate(25);
    }

    #[Computed]
    public function aspiranteSeleccionado(): ?Aspirante
    {
        if (! $this->aspiranteId) return null;
        return Aspirante::with(['user', 'cohorte.carrera', 'registradoPor'])->find($this->aspiranteId);
    }

    #[Computed]
    public function verificacionCount(): int
    {
        return Aspirante::where('estado', 'verificacion')->count();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.gestion-aspirantes', [
            'aspirantes'        => $this->aspirantes,
            'cohortes'          => $this->cohortes,
            'aspiranteDetalle'  => $this->aspiranteSeleccionado,
            'verificacionCount' => $this->verificacionCount,
        ]);
    }

    // ── Ver detalle ───────────────────────────────────────────────────────

    public function verDetalle(int $id): void
    {
        $this->aspiranteId   = $id;
        $this->showDetalle   = true;
        $this->showRechazar  = false;
        $this->showCambiarEstado = false;
        unset($this->aspiranteSeleccionado);
    }

    public function cerrarDetalle(): void
    {
        $this->showDetalle   = false;
        $this->aspiranteId   = null;
        $this->showRechazar  = false;
        $this->showCambiarEstado = false;
        $this->motivoRechazo = '';
        $this->observacion   = '';
        $this->nuevoEstado   = '';
        unset($this->aspiranteSeleccionado);
    }

    // ── Cambiar estado ─────────────────────────────────────────────────────

    public function abrirCambiarEstado(string $estado): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;
        $this->nuevoEstado   = $estado;
        $this->observacion   = '';
        $this->motivoRechazo = '';
        $this->showCambiarEstado = true;
        $this->showRechazar  = ($estado === 'rechazado');
    }

    public function confirmarCambioEstado(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        if (! array_key_exists($this->nuevoEstado, Aspirante::ESTADOS)) {
            return;
        }

        $aspirante = Aspirante::with('user')->findOrFail($this->aspiranteId);
        $estadoActual = $aspirante->estado;

        if ($this->nuevoEstado === 'rechazado') {
            $this->validate(['motivoRechazo' => 'required|string|max:500'],
                ['motivoRechazo.required' => 'Indica el motivo del rechazo.']);
            $aspirante->update([
                'estado'         => 'rechazado',
                'motivo_rechazo' => $this->motivoRechazo,
            ]);
        } else {
            $aspirante->update([
                'estado'               => $this->nuevoEstado,
                'observacion_general'  => $this->observacion ?: $aspirante->observacion_general,
            ]);
        }

        if ($this->nuevoEstado === 'proceso') {
            $password = Str::random(10);
            $aspirante->user->update(['password' => $password]);
            EnviarEmailBienvenidaAspirante::dispatch($aspirante->user_id, $password);
        } elseif (in_array($this->nuevoEstado, ['aprobado', 'rechazado', 'matriculado'])) {
            NotificarEstadoAspirante::dispatch($aspirante->id, $this->nuevoEstado);
        }

        unset($this->aspirantes, $this->aspiranteSeleccionado);
        $this->showCambiarEstado = false;
        $this->showRechazar = false;
        $this->motivoRechazo = '';
        $this->observacion   = '';

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Estado actualizado',
            'text'  => 'Aspirante pasó de ' . (Aspirante::ESTADOS[$estadoActual] ?? $estadoActual) . ' a ' . (Aspirante::ESTADOS[$this->nuevoEstado] ?? $this->nuevoEstado) . '.',
            'timer' => 3000,
        ]);

        $this->nuevoEstado = '';
        // Refrescar detalle
        $this->aspiranteId = $aspirante->id;
        unset($this->aspiranteSeleccionado);
    }

    // ── Eliminar aspirante (soft delete) ─────────────────────────────────

    public function eliminar(int $id): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $aspirante = Aspirante::findOrFail($id);
        $aspirante->delete();

        unset($this->aspirantes);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Aspirante eliminado',
            'text'  => 'Movido a la papelera. Puedes restaurarlo desde allí.',
            'timer' => 3000,
        ]);
    }

    // ── Actualizar estado de documento ────────────────────────────────────

    public function actualizarDocumento(string $campo, string $nuevoEstado, string $observacion = ''): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $camposValidos = ['cedula', 'bachiller', 'habilitante', 'pago', 'hoja_vida', 'cert_laborales', 'cert_cursos'];
        if (! in_array($campo, $camposValidos, true)) return;

        $estadosPermitidos = $campo === 'pago' ? Aspirante::PAGO_ESTADOS : Aspirante::DOC_ESTADOS;
        if (! in_array($nuevoEstado, $estadosPermitidos, true)) return;

        $aspirante = Aspirante::findOrFail($this->aspiranteId);

        $data = ["{$campo}_estado" => $nuevoEstado];
        if ($observacion !== '') {
            $data["{$campo}_observacion"] = $observacion;
        } elseif ($nuevoEstado !== 'rechazado') {
            // Limpiar la observación previa si se aprueba/verifica
            $data["{$campo}_observacion"] = null;
        }
        $aspirante->update($data);

        // Enviar correo al estudiante cuando se rechaza con observación
        if ($nuevoEstado === 'rechazado' && trim($observacion) !== '') {
            $labelDocumentos = [
                'cedula'        => 'Cédula de identidad y papeleta de votación',
                'bachiller'     => 'Título de bachiller',
                'habilitante'   => 'Documento habilitante',
                'pago'          => 'Comprobante de pago de matrícula',
                'hoja_vida'     => 'Hoja de vida',
                'cert_laborales'=> 'Certificados laborales',
                'cert_cursos'   => 'Certificados de cursos o capacitaciones',
            ];
            NotificarObservacionDocumentoAspirante::dispatch(
                $aspirante->id,
                $labelDocumentos[$campo] ?? $campo,
                trim($observacion),
            );
        }

        unset($this->aspiranteSeleccionado);

        $icono = match($nuevoEstado) {
            'aprobado', 'verificado' => 'success',
            'rechazado'              => 'warning',
            default                  => 'success',
        };

        $this->dispatch('swal', [
            'icon'  => $icono,
            'title' => $nuevoEstado === 'rechazado' ? 'Documento rechazado' : 'Documento aprobado',
            'text'  => $nuevoEstado === 'rechazado' && trim($observacion) !== '' ? 'Se notificó al estudiante por correo.' : '',
            'timer' => 3000,
        ]);
    }
}
