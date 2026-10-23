<?php

namespace App\Livewire\Administration;

use App\Models\BecaAplicada;
use App\Models\Carrera;
use App\Models\ConvenioAplicado;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Periodo;
use App\Services\BeneficiosService;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PeriodosIndex extends Component
{
    use WithPagination, WithAuthorization;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $expandedId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->expandedId = null;
    }

    public function toggle(int $periodoId): void
    {
        $this->expandedId = $this->expandedId === $periodoId ? null : $periodoId;
    }

    public function cerrar(int $periodoId, int $carreraId): void
    {
        if ($this->sinPermiso('gestionar_periodos')) return;

        $periodo = Periodo::find($periodoId);
        if (! $periodo) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Periodo no encontrado.']);
            return;
        }

        $cpActual = DB::table('carrera_periodo')
            ->where('carrera_id', $carreraId)
            ->where('periodo_id', $periodo->id)
            ->where('is_current', true)
            ->first();

        if (! $cpActual) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Este periodo no está activo para la carrera seleccionada.']);
            return;
        }

        $cpSiguiente = DB::table('carrera_periodo as cp')
            ->join('periodos as p', 'p.id', '=', 'cp.periodo_id')
            ->where('cp.carrera_id', $carreraId)
            ->where('cp.periodo_id', '!=', $periodo->id)
            ->where('cp.is_current', false)
            ->where('p.fecha_inicio', '>', $periodo->fecha_fin)
            ->orderBy('p.fecha_inicio')
            ->select('cp.*', 'p.code as p_code')
            ->first();

        if (! $cpSiguiente) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'No existe un periodo siguiente vinculado a esta carrera. Cree el próximo periodo y vincúlelo primero.']);
            return;
        }

        DB::beginTransaction();
        try {
            // Solo obligaciones de ESTA carrera: el mismo período puede seguir activo en otras.
            // Las impagas quedan en su período como Vencido (siguen siendo pagables); no se
            // trasladan ni se generan cobros nuevos: la colegiatura siguiente la crea la matrícula.
            $obligacionesPendientes = ObligacionesFinanciera::where('periodo_id', $periodo->id)
                ->whereNotIn('estado', ['Pagado', 'Invalidado'])
                ->where(fn($q) => $q
                    ->whereHas('matricula', fn($m) => $m->where('carrera_id', $carreraId))
                    ->orWhere(fn($sinMatricula) => $sinMatricula
                        ->whereNull('matricula_id')
                        ->whereIn('user_id', Matricula::where('periodo_id', $periodo->id)
                            ->where('carrera_id', $carreraId)
                            ->select('user_id'))
                    )
                )
                ->get();

            $matriculadosEnPeriodo = Matricula::where('periodo_id', $periodo->id)
                ->where('carrera_id', $carreraId)
                ->where('tipo', '!=', Matricula::TIPO_VALIDACION)
                ->select('user_id');

            // Las becas se renuevan manualmente cada período: al cerrar se desactivan TODAS las de
            // los estudiantes de esta carrera matriculados en el período, hayan pagado o no.
            $becasADesactivar = BecaAplicada::where('is_active', true)
                ->whereIn('user_id', $matriculadosEnPeriodo)
                ->get();

            // Convenios semestrales usados en este período (los permanentes siguen vigentes; los
            // semestrales aún sin usar, p. ej. asignados a mitad de período, se conservan)
            $conveniosADesactivar = ConvenioAplicado::where('is_active', true)
                ->where('periodo_id', $periodo->id)
                ->whereHas('tipoConvenio', fn($t) => $t->where('tipo_alcance', 'semestral'))
                ->whereIn('user_id', $matriculadosEnPeriodo)
                ->get();

            // Marcar obligaciones impagas como Vencido
            $numVencidas = 0;
            foreach ($obligacionesPendientes as $obligacion) {
                if ($obligacion->estado === 'Vencido' || $obligacion->saldo <= 0) continue;
                $obligacion->update(['estado' => 'Vencido']);
                $numVencidas++;
            }

            // Desactivar becas y convenios semestrales (quedan en el historial con motivo)
            $detalle = "Período {$periodo->code}";
            $becasADesactivar->each(fn($beca) => BeneficiosService::desactivar($beca, 'cierre_periodo', $detalle));
            $conveniosADesactivar->each(fn($conv) => BeneficiosService::desactivar($conv, 'cierre_periodo', $detalle));

            DB::table('carrera_periodo')
                ->where('carrera_id', $carreraId)
                ->where('periodo_id', $periodo->id)
                ->update(['is_current' => false]);

            DB::table('carrera_periodo')
                ->where('id', $cpSiguiente->id)
                ->update(['is_current' => true]);

            DB::commit();

            $carreraObj  = Carrera::find($carreraId);
            $numBecas     = $becasADesactivar->count();
            $numConvenios = $conveniosADesactivar->count();
            $sufijoBecas  = ($numBecas > 0 ? " · {$numBecas} beca(s) desactivada(s)" : '')
                . ($numConvenios > 0 ? " · {$numConvenios} convenio(s) semestral(es) desactivado(s)" : '');

            $this->dispatch('toast', [
                'tipo'    => 'success',
                'mensaje' => "Período {$periodo->code} cerrado para {$carreraObj->name}. {$numVencidas} obligación(es) marcada(s) como Vencido{$sufijoBecas}. Siguiente: {$cpSiguiente->p_code}.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Error al cerrar el periodo: ' . $e->getMessage()]);
        }
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $periodos = Periodo::with(['carreras'])
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%' . trim($this->search) . '%';
                $q->where('code', 'like', $term)
                  ->orWhere('description', 'like', $term);
            })
            ->orderByDesc('fecha_inicio')
            ->paginate(10);

        return view('livewire.administration.periodos-index', compact('periodos'));
    }
}
