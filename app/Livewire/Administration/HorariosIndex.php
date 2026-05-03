<?php

namespace App\Livewire\Administration;

use App\Models\Horario;
use App\Models\Periodo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class HorariosIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $filtroPeriodoId = null;
    public string $filtroDia = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFiltroPeriodoId(): void
    {
        $this->resetPage();
    }
    public function updatingFiltroDia(): void
    {
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        Horario::findOrFail($id)->delete();
        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Horario eliminado correctamente.']);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $q = Horario::with([
            'materia.semestre.carrera',
            'paralelo',
            'periodo',
            'asignacionDocente.docente',
        ]);

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $q->where(function ($query) use ($term) {
                $query->whereHas('materia', fn($m) => $m->where('name', 'like', $term))
                    ->orWhereHas('paralelo', fn($p) => $p->where('name', 'like', $term))
                    ->orWhere('aula', 'like', $term)
                    ->orWhereHas('asignacionDocente.docente', fn($d) => $d->where('name', 'like', $term));
            });
        }

        if ($this->filtroPeriodoId) {
            $q->where('periodo_id', $this->filtroPeriodoId);
        }

        if ($this->filtroDia !== '') {
            $q->where('dia_semana', $this->filtroDia);
        }

        $q->orderByRaw("FIELD(dia_semana,'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado')")
            ->orderBy('hora_inicio');

        return view('livewire.administration.horarios-index', [
            'horarios' => $q->paginate(10),
            'periodos' => Periodo::orderByDesc('fecha_inicio')->get(),
            'dias'     => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        ]);
    }
}
