<?php

namespace App\Livewire\Administration;

use App\Models\MateriaPeriodoParalelo;
use Livewire\Component;
use Livewire\WithPagination;

class MateriaPeriodoParalelosIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $search = trim($this->search);

        $query = MateriaPeriodoParalelo::query()
            ->with(['materia', 'paralelo', 'periodo'])
            ->latest();

        if ($search !== '') {

            $searchLower = mb_strtolower($search);

            $query->where(function ($q) use ($search, $searchLower) {

                // Buscar por Materia
                $q->whereHas('materia', function ($materiaQuery) use ($search) {
                    $materiaQuery->where('nombre', 'LIKE', '%' . $search . '%');
                })

                    // Buscar por Paralelo
                    ->orWhereHas('paralelo', function ($paraleloQuery) use ($search) {
                        $paraleloQuery->where('nombre', 'LIKE', '%' . $search . '%');
                    })

                    // Buscar por Periodo (código)
                    ->orWhereHas('periodo', function ($periodoQuery) use ($search) {
                        $periodoQuery->where('codigo', 'LIKE', '%' . $search . '%');
                    })

                    // Buscar por fechas
                    ->orWhere('fecha_inicio', 'LIKE', '%' . $search . '%')
                    ->orWhere('fecha_fin', 'LIKE', '%' . $search . '%');

                // Buscar por estado (texto)
                if ($searchLower === 'activo') {
                    $q->orWhere('is_active', true);
                }

                if ($searchLower === 'inactivo') {
                    $q->orWhere('is_active', false);
                }
            });
        }

        $registros = $query->paginate(10);

        return view('livewire.administration.materia-periodo-paralelos-index', [
            'registros' => $registros
        ]);
    }

    /*       return view('livewire.administration.materia-periodo-paralelos-index', compact('registros'));
    } */

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
