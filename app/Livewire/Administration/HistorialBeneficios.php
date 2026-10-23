<?php

namespace App\Livewire\Administration;

use App\Models\BecaAplicada;
use App\Models\ConvenioAplicado;
use App\Models\ObligacionesFinanciera;
use App\Models\User;
use App\Services\BeneficiosService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Historial de becas y convenios de un estudiante (activos e inactivos), con en qué
 * períodos se usaron y cuándo, quién y por qué se desactivaron. Se abre desde becas y convenios.
 */
class HistorialBeneficios extends Component
{
    public bool $abierto = false;
    public ?int $userId  = null;

    #[On('ver-historial-beneficios')]
    public function abrir(int $userId): void
    {
        $usuario = auth()->user();
        if (! $usuario || ! ($usuario->can('gestionar_becas') || $usuario->can('gestionar_convenios'))) return;

        $this->userId  = $userId;
        $this->abierto = true;
        unset($this->estudiante, $this->registros);
    }

    public function cerrar(): void
    {
        $this->abierto = false;
        $this->userId  = null;
    }

    #[Computed]
    public function estudiante(): ?User
    {
        return $this->userId ? User::select('id', 'name', 'cedula')->find($this->userId) : null;
    }

    #[Computed]
    public function registros(): Collection
    {
        if (! $this->userId) return collect();

        $becas = BecaAplicada::with(['tipoBeca:id,nombre', 'desactivadoPor:id,name'])
            ->where('user_id', $this->userId)->get();
        $convenios = ConvenioAplicado::with(['tipoConvenio:id,nombre,tipo_alcance', 'desactivadoPor:id,name', 'periodo:id,code'])
            ->where('user_id', $this->userId)->get();

        // Períodos en los que cada beneficio descontó cuotas
        $periodosBeca = $this->periodosDeUso('beca_aplicada_id', $becas->pluck('id'));
        $periodosConv = $this->periodosDeUso('convenio_aplicado_id', $convenios->pluck('id'));

        $filas = $becas->map(fn($b) => [
            'clase'      => 'Beca',
            'nombre'     => $b->tipoBeca?->nombre ?? '—',
            'alcance'    => null,
            'porcentaje' => (float) $b->porcentaje_aplicado,
            'desde'      => $b->fecha_asignacion,
            'activo'     => $b->is_active,
            'periodos'   => $periodosBeca[$b->id] ?? [],
            ...$this->datosDesactivacion($b),
        ])->concat($convenios->map(fn($c) => [
            'clase'      => 'Convenio',
            'nombre'     => $c->tipoConvenio?->nombre ?? '—',
            'alcance'    => $c->tipoConvenio?->tipo_alcance === 'semestral' ? 'Semestral' : 'Permanente',
            'porcentaje' => (float) $c->porcentaje_aplicado,
            'desde'      => $c->fecha_inicio,
            'activo'     => $c->is_active,
            'periodos'   => array_values(array_unique(array_filter([...($periodosConv[$c->id] ?? []), $c->periodo?->code]))),
            ...$this->datosDesactivacion($c),
        ]));

        return $filas->sortByDesc(fn($f) => $f['desde']?->timestamp ?? 0)->values();
    }

    private function datosDesactivacion(BecaAplicada|ConvenioAplicado $beneficio): array
    {
        if ($beneficio->is_active) {
            return ['hasta' => null, 'motivo' => null, 'detalle' => null, 'por' => null];
        }

        // Desactivados antes de existir el historial: solo queda la observación
        $sinRegistro = ! $beneficio->motivo_desactivacion;

        return [
            'hasta'   => $beneficio->desactivado_at,
            'motivo'  => $sinRegistro ? 'Sin registro (anterior al historial)' : BeneficiosService::etiquetaMotivo($beneficio->motivo_desactivacion),
            'detalle' => $sinRegistro ? $beneficio->observacion : $beneficio->detalle_desactivacion,
            'por'     => $beneficio->desactivadoPor?->name,
        ];
    }

    /** @return array<int, array<int, string>> id del beneficio => códigos de período */
    private function periodosDeUso(string $columna, Collection $ids): array
    {
        if ($ids->isEmpty()) return [];

        return ObligacionesFinanciera::whereIn($columna, $ids)
            ->with('periodo:id,code')
            ->get([$columna, 'periodo_id'])
            ->groupBy($columna)
            ->map(fn($obs) => $obs->pluck('periodo.code')->filter()->unique()->values()->all())
            ->all();
    }

    public function render()
    {
        return view('livewire.administration.historial-beneficios', [
            'estudiante' => $this->estudiante,
            'registros'  => $this->registros,
        ]);
    }
}
