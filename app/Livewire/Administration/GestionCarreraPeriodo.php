<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\CarreraPeriodo;
use App\Models\Periodo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class GestionCarreraPeriodo extends Component
{
    public bool    $isOpen    = false;
    public ?int    $periodoId = null;

    // --- Vincular form ---
    public ?int    $nuevaCarreraId            = null;
    public bool    $nuevaIsCurrent            = false;
    public bool    $nuevaIsActive             = true;
    public ?string $nuevaFechaInicio          = null;
    public ?string $nuevaFechaFin             = null;
    public ?string $nuevaFechaLimiteMatricula = null;
    public ?string $nuevaFechaLimitePago      = null;
    public bool    $mostrarFormVincular       = false;

    // --- Editar form ---
    public ?int    $editandoId               = null;
    public bool    $editIsCurrent            = false;
    public bool    $editIsActive             = true;
    public ?string $editFechaInicio          = null;
    public ?string $editFechaFin             = null;
    public ?string $editFechaLimiteMatricula = null;
    public ?string $editFechaLimitePago      = null;

    // -------------------------------------------------------
    // Event listener
    // -------------------------------------------------------

    #[On('openGestionCarreras')]
    public function abrir(int $periodoId): void
    {
        $this->periodoId           = $periodoId;
        $this->isOpen              = true;
        $this->mostrarFormVincular = false;
        $this->editandoId          = null;
        $this->resetVincularForm();
    }

    public function cerrar(): void
    {
        $this->isOpen              = false;
        $this->periodoId           = null;
        $this->editandoId          = null;
        $this->mostrarFormVincular = false;
    }

    // -------------------------------------------------------
    // Data helpers
    // -------------------------------------------------------

    protected function getPeriodo(): ?Periodo
    {
        return $this->periodoId ? Periodo::find($this->periodoId) : null;
    }

    protected function getCarrerasVinculadas()
    {
        return $this->periodoId
            ? (Periodo::find($this->periodoId)?->carreras ?? collect())
            : collect();
    }

    protected function getCarrerasDisponibles()
    {
        if (! $this->periodoId) {
            return collect();
        }
        $vinculadasIds = Periodo::find($this->periodoId)?->carreras()->pluck('carreras.id') ?? collect();

        return Carrera::where('is_active', true)
            ->whereNotIn('id', $vinculadasIds)
            ->orderBy('name')
            ->get();
    }

    // -------------------------------------------------------
    // Vincular
    // -------------------------------------------------------

    public function abrirFormVincular(): void
    {
        $this->mostrarFormVincular = true;
        $this->resetVincularForm();
    }

    public function cerrarFormVincular(): void
    {
        $this->mostrarFormVincular = false;
        $this->resetErrorBag();
    }

    public function vincular(): void
    {
        $this->validate([
            'nuevaCarreraId' => 'required|integer|exists:carreras,id',
        ], [
            'nuevaCarreraId.required' => 'Selecciona una carrera.',
        ]);

        $periodo = $this->getPeriodo();
        abort_unless($periodo !== null, 404);

        DB::transaction(function () use ($periodo) {
            if ($this->nuevaIsCurrent) {
                DB::table('carrera_periodo')
                    ->where('carrera_id', $this->nuevaCarreraId)
                    ->update(['is_current' => false]);
            }

            $periodo->carreras()->attach($this->nuevaCarreraId, [
                'is_current'             => $this->nuevaIsCurrent,
                'is_active'              => $this->nuevaIsActive,
                'fecha_inicio'           => $this->nuevaFechaInicio ?: null,
                'fecha_fin'              => $this->nuevaFechaFin ?: null,
                'fecha_limite_matricula' => $this->nuevaFechaLimiteMatricula ?: null,
                'fecha_limite_pago'      => $this->nuevaFechaLimitePago ?: null,
            ]);
        });

        $this->mostrarFormVincular = false;
        $this->resetVincularForm();
        session()->flash('gestion_success', 'Carrera vinculada correctamente.');
    }

    // -------------------------------------------------------
    // Editar
    // -------------------------------------------------------

    public function editar(int $cpId): void
    {
        $cp = CarreraPeriodo::where('id', $cpId)->firstOrFail();
        abort_unless((int) $cp->periodo_id === $this->periodoId, 403);

        $this->editandoId              = $cpId;
        $this->editIsCurrent           = (bool) $cp->is_current;
        $this->editIsActive            = (bool) $cp->is_active;
        $this->editFechaInicio         = $cp->fecha_inicio?->format('Y-m-d');
        $this->editFechaFin            = $cp->fecha_fin?->format('Y-m-d');
        $this->editFechaLimiteMatricula = $cp->fecha_limite_matricula?->format('Y-m-d');
        $this->editFechaLimitePago     = $cp->fecha_limite_pago?->format('Y-m-d');
    }

    public function guardarEdicion(): void
    {
        $cp = CarreraPeriodo::where('id', $this->editandoId)->firstOrFail();
        abort_unless((int) $cp->periodo_id === $this->periodoId, 403);

        DB::transaction(function () use ($cp) {
            if ($this->editIsCurrent) {
                DB::table('carrera_periodo')
                    ->where('carrera_id', $cp->carrera_id)
                    ->where('id', '!=', $cp->id)
                    ->update(['is_current' => false]);
            }

            DB::table('carrera_periodo')->where('id', $cp->id)->update([
                'is_current'             => $this->editIsCurrent,
                'is_active'              => $this->editIsActive,
                'fecha_inicio'           => $this->editFechaInicio ?: null,
                'fecha_fin'              => $this->editFechaFin ?: null,
                'fecha_limite_matricula' => $this->editFechaLimiteMatricula ?: null,
                'fecha_limite_pago'      => $this->editFechaLimitePago ?: null,
            ]);
        });

        $this->editandoId = null;
        session()->flash('gestion_success', 'Registro actualizado correctamente.');
    }

    public function cancelarEdicion(): void
    {
        $this->editandoId = null;
        $this->resetErrorBag();
    }

    // -------------------------------------------------------
    // Desvincular
    // -------------------------------------------------------

    public function desvincular(int $cpId): void
    {
        $cp = CarreraPeriodo::where('id', $cpId)->firstOrFail();
        abort_unless((int) $cp->periodo_id === $this->periodoId, 403);

        DB::table('carrera_periodo')->where('id', $cpId)->delete();
        session()->flash('gestion_success', 'Carrera desvinculada.');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    protected function resetVincularForm(): void
    {
        $this->nuevaCarreraId            = null;
        $this->nuevaIsCurrent            = false;
        $this->nuevaIsActive             = true;
        $this->nuevaFechaInicio          = null;
        $this->nuevaFechaFin             = null;
        $this->nuevaFechaLimiteMatricula = null;
        $this->nuevaFechaLimitePago      = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.administration.gestion-carrera-periodo', [
            'periodo'             => $this->getPeriodo(),
            'carrerasVinculadas'  => $this->getCarrerasVinculadas(),
            'carrerasDisponibles' => $this->getCarrerasDisponibles(),
        ]);
    }
}
