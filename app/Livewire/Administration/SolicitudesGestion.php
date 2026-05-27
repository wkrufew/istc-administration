<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarNotificacionSolicitudListaJob;
use App\Jobs\EnviarNotificacionSolicitudPagadaJob;
use App\Jobs\EnviarNotificacionSolicitudRechazadaJob;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\ObligacionesFinanciera;
use App\Models\Periodo;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class SolicitudesGestion extends Component
{
    use WithPagination, WithAuthorization;

    public string $search       = '';
    public string $filtroEstado = '';
    public string $filtroTipo   = '';

    // Modal aprobar / rechazar
    public bool    $showModal  = false;
    public ?int    $solicitudId = null;
    public string  $accion     = '';  // aprobar | rechazar
    public string  $notas      = '';

    // Modal avanzar (pagada → en_proceso → lista → entregada)
    public bool   $showModalAvanzar = false;
    public ?int   $avanzarId        = null;
    public string $notasAvance      = '';

    public function mount(): void
    {
        $this->requierePermiso('gestionar_solicitudes');
    }

    public function updatedSearch(): void      { $this->resetPage(); }
    public function updatedFiltroEstado(): void { $this->resetPage(); }
    public function updatedFiltroTipo(): void   { $this->resetPage(); }

    #[Computed]
    public function tiposSolicitudes()
    {
        return TipoSolicitud::orderBy('nombre')->get();
    }

    #[Computed]
    public function solicitudes()
    {
        return Solicitud::with(['estudiante', 'tipoSolicitud', 'obligacion', 'procesadoPor'])
            ->when($this->search, fn($q) => $q->whereHas('estudiante', fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('cedula', 'like', '%' . $this->search . '%')
            ))
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroTipo,   fn($q) => $q->where('tipo_solicitud_id', $this->filtroTipo))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function conteos()
    {
        return [
            'pendientes'  => Solicitud::where('estado', 'pendiente')->count(),
            'en_proceso'  => Solicitud::whereIn('estado', ['aprobada', 'pendiente_pago', 'pagada', 'en_proceso'])->count(),
            'total_hoy'   => Solicitud::whereDate('created_at', today())->count(),
        ];
    }

    // ─── Aprobar / Rechazar ───────────────────────────────────────────────────

    public function abrirGestion(int $id, string $accion): void
    {
        $this->solicitudId = $id;
        $this->accion      = $accion;
        $this->notas       = '';
        $this->showModal   = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal   = false;
        $this->solicitudId = null;
        $this->accion      = '';
        $this->notas       = '';
    }

    public function procesarAccion(): void
    {
        $permiso = $this->accion === 'aprobar' ? 'aprobar_solicitudes' : 'rechazar_solicitudes';
        if ($this->sinPermiso($permiso)) return;

        $solicitud = Solicitud::with('tipoSolicitud')->findOrFail($this->solicitudId);

        $accionRealizada = $this->accion;
        $solicitudId     = $solicitud->id;

        DB::beginTransaction();
        try {
            match ($this->accion) {
                'aprobar'  => $this->aprobar($solicitud),
                'rechazar' => $this->rechazar($solicitud),
                default    => throw new \Exception('Acción no válida.'),
            };
            DB::commit();
            unset($this->solicitudes, $this->conteos);
            $this->cerrarModal();
            $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => 'Solicitud actualizada.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['toast' => true, 'icon' => 'error', 'title' => $e->getMessage()]);
            return;
        }

        if ($accionRealizada === 'rechazar') {
            try {
                EnviarNotificacionSolicitudRechazadaJob::dispatch($solicitudId);
            } catch (\Throwable $e) {
                Log::warning('SolicitudesGestion: email rechazo falló', ['id' => $solicitudId, 'error' => $e->getMessage()]);
            }
        }
    }

    private function aprobar(Solicitud $solicitud): void
    {
        $nuevoEstado = $solicitud->precio_aplicado > 0 ? 'pendiente_pago' : 'en_proceso';

        $solicitud->update([
            'estado'        => $nuevoEstado,
            'notas_admin'   => $this->notas ?: null,
            'procesado_por' => Auth::id(),
        ]);

        if ($solicitud->precio_aplicado > 0) {
            $periodo = Periodo::periodoActivoGlobal();
            ObligacionesFinanciera::create([
                'user_id'          => $solicitud->estudiante_id,
                'periodo_id'       => $periodo?->id,
                'tipo'             => 'CERTIFICADO',
                'monto_original'   => $solicitud->precio_aplicado,
                'descuento'        => 0,
                'monto_final'      => $solicitud->precio_aplicado,
                'estado'           => 'Pendiente',
                'fecha_vencimiento'=> now()->addDays(15)->toDateString(),
                'descripcion'      => 'Solicitud: ' . $solicitud->tipoSolicitud->nombre,
                'solicitud_id'     => $solicitud->id,
            ]);
        }
    }

    private function rechazar(Solicitud $solicitud): void
    {
        $this->validate(
            ['notas' => 'required|string|min:5'],
            ['notas.required' => 'El motivo del rechazo es obligatorio.', 'notas.min' => 'Ingrese al menos 5 caracteres.']
        );

        $solicitud->update([
            'estado'        => 'rechazada',
            'notas_admin'   => $this->notas,
            'procesado_por' => Auth::id(),
        ]);
    }

    // ─── Avanzar estado (pagada → entregada) ─────────────────────────────────

    public function abrirAvanzar(int $id): void
    {
        $this->avanzarId     = $id;
        $this->notasAvance   = '';
        $this->showModalAvanzar = true;
    }

    public function cerrarAvanzar(): void
    {
        $this->showModalAvanzar = false;
        $this->avanzarId        = null;
        $this->notasAvance      = '';
    }

    public function confirmarAvance(): void
    {
        if ($this->sinPermiso('avanzar_solicitudes')) return;

        $flujo = [
            'pendiente_pago' => 'pagada',
            'pagada'         => 'en_proceso',
            'en_proceso'     => 'entregada',
        ];

        $solicitud = Solicitud::findOrFail($this->avanzarId);
        $siguiente = $flujo[$solicitud->estado] ?? null;

        if (! $siguiente) {
            $this->dispatch('swal', ['toast' => true, 'icon' => 'warning', 'title' => 'No hay siguiente estado.']);
            return;
        }

        $solicitud->update([
            'estado'        => $siguiente,
            'notas_admin'   => $this->notasAvance ?: $solicitud->notas_admin,
            'procesado_por' => Auth::id(),
        ]);

        unset($this->solicitudes, $this->conteos);
        $this->cerrarAvanzar();
        $this->dispatch('swal', [
            'toast' => true, 'icon' => 'success',
            'title' => 'Estado actualizado a: ' . Solicitud::ESTADOS[$siguiente],
        ]);

        if ($siguiente === 'pagada') {
            try {
                EnviarNotificacionSolicitudPagadaJob::dispatch($solicitud->id);
            } catch (\Throwable $e) {
                Log::warning('SolicitudesGestion: email pago confirmado falló', ['id' => $solicitud->id, 'error' => $e->getMessage()]);
            }
        } elseif ($siguiente === 'entregada') {
            try {
                EnviarNotificacionSolicitudListaJob::dispatch($solicitud->id);
            } catch (\Throwable $e) {
                Log::warning('SolicitudesGestion: email documento enviado falló', ['id' => $solicitud->id, 'error' => $e->getMessage()]);
            }
        }
    }

    public function render()
    {
        return view('livewire.administration.solicitudes-gestion', [
            'solicitudes' => $this->solicitudes,
        ]);
    }
}
