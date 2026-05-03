<?php

namespace App\Livewire\Administration;

use App\Models\MateriaPeriodoParalelo;
use App\Models\Periodo;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MateriaPeriodoParalelosIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $filtroPeriodoId = null;

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingFiltroPeriodoId(): void { $this->resetPage(); }

    public function eliminar(int $id): void
    {
        MateriaPeriodoParalelo::findOrFail($id)->delete();
        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Módulo eliminado correctamente.']);
    }

    public function render()
    {
        $q = MateriaPeriodoParalelo::with(['materia.semestre.carrera', 'paralelo', 'periodo']);

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $q->where(function ($query) use ($term) {
                $query->whereHas('materia', fn ($m) => $m->where('name', 'like', $term))
                      ->orWhereHas('paralelo', fn ($p) => $p->where('name', 'like', $term))
                      ->orWhereHas('periodo', fn ($pe) => $pe->where('code', 'like', $term)
                                                              ->orWhere('description', 'like', $term));
            });
        }

        if ($this->filtroPeriodoId) {
            $q->where('periodo_id', $this->filtroPeriodoId);
        }

        $q->orderByDesc('created_at');

        return view('livewire.administration.materia-periodo-paralelos-index', [
            'registros' => $q->paginate(10),
            'periodos'  => Periodo::orderByDesc('fecha_inicio')->get(),
        ]);
    }
}
