<?php

namespace App\Livewire\Administration;

use App\Models\Materia;
use App\Models\MateriaPeriodoParalelo;
use App\Models\Paralelo;
use App\Models\Periodo;
use Livewire\Component;

class MppForm extends Component
{
    public ?int $mppId = null;

    public ?int $periodoId   = null;
    public ?int $materiaId   = null;
    public ?int $paraleloId  = null;
    public int  $cupoMaximo  = 30;
    public string $fechaInicio = '';
    public string $fechaFin    = '';
    public bool $isActive      = true;

    public function mount(?int $mppId = null): void
    {
        if ($mppId) {
            $this->mppId = $mppId;
            $mpp = MateriaPeriodoParalelo::findOrFail($mppId);
            $this->periodoId   = $mpp->periodo_id;
            $this->materiaId   = $mpp->materia_id;
            $this->paraleloId  = $mpp->paralelo_id;
            $this->cupoMaximo  = $mpp->cupo_maximo;
            $this->fechaInicio = $mpp->fecha_inicio->format('Y-m-d');
            $this->fechaFin    = $mpp->fecha_fin->format('Y-m-d');
            $this->isActive    = $mpp->is_active;
            return;
        }

        $activo = Periodo::whereExists(fn ($q) =>
            $q->selectRaw(1)->from('carrera_periodo')
              ->whereColumn('carrera_periodo.periodo_id', 'periodos.id')
              ->where('carrera_periodo.is_current', true)
        )->orderByDesc('fecha_inicio')->first();

        if ($activo) {
            $this->periodoId   = $activo->id;
            $this->fechaInicio = $activo->fecha_inicio->format('Y-m-d');
            $this->fechaFin    = $activo->fecha_fin->format('Y-m-d');
        }
    }

    public function updatedParaleloId(): void
    {
        $paralelo = Paralelo::find($this->paraleloId);
        if ($paralelo) {
            $this->cupoMaximo = $paralelo->cupo_maximo;
        }
    }

    public function updatedPeriodoId(): void
    {
        $this->materiaId  = null;
        $this->paraleloId = null;

        $periodo = Periodo::find($this->periodoId);
        if ($periodo) {
            $this->fechaInicio = $periodo->fecha_inicio->format('Y-m-d');
            $this->fechaFin    = $periodo->fecha_fin->format('Y-m-d');
        } else {
            $this->fechaInicio = '';
            $this->fechaFin    = '';
        }
    }

    public function seleccionarMateria(?int $id): void
    {
        $this->materiaId = $id;
    }

    public function guardar(): void
    {
        $this->validate([
            'periodoId'   => 'required|exists:periodos,id',
            'materiaId'   => 'required|exists:materias,id',
            'paraleloId'  => 'required|exists:paralelos,id',
            'cupoMaximo'  => 'required|integer|min:1|max:500',
            'fechaInicio' => 'required|date',
            'fechaFin'    => 'required|date',
        ], [
            'periodoId.required'   => 'Selecciona un período.',
            'materiaId.required'   => 'Selecciona una materia.',
            'paraleloId.required'  => 'Selecciona un paralelo.',
            'cupoMaximo.required'  => 'Ingresa el cupo máximo.',
            'cupoMaximo.min'       => 'El cupo mínimo es 1.',
            'fechaInicio.required' => 'Ingresa la fecha de inicio.',
            'fechaFin.required'    => 'Ingresa la fecha de fin.',
        ]);

        if ($this->fechaFin <= $this->fechaInicio) {
            $this->addError('fechaFin', 'La fecha de fin debe ser posterior a la fecha de inicio.');
            return;
        }

        $periodo = Periodo::findOrFail($this->periodoId);
        $inicio  = \Carbon\Carbon::parse($this->fechaInicio);
        $fin     = \Carbon\Carbon::parse($this->fechaFin);

        if ($inicio->lt($periodo->fecha_inicio) || $fin->gt($periodo->fecha_fin)) {
            $this->addError('fechaInicio',
                'Las fechas del módulo deben estar dentro del período: '
                . $periodo->fecha_inicio->format('d/m/Y') . ' — '
                . $periodo->fecha_fin->format('d/m/Y') . '.'
            );
            return;
        }

        $dup = MateriaPeriodoParalelo::where([
            'materia_id'  => $this->materiaId,
            'periodo_id'  => $this->periodoId,
            'paralelo_id' => $this->paraleloId,
        ]);
        if ($this->mppId) {
            $dup->where('id', '!=', $this->mppId);
        }
        if ($dup->exists()) {
            $this->addError('materiaId', 'Ya existe un módulo para esa Materia en ese Período y Paralelo.');
            return;
        }

        $data = [
            'materia_id'  => $this->materiaId,
            'periodo_id'  => $this->periodoId,
            'paralelo_id' => $this->paraleloId,
            'cupo_maximo' => $this->cupoMaximo,
            'fecha_inicio'=> $this->fechaInicio,
            'fecha_fin'   => $this->fechaFin,
            'is_active'   => $this->isActive,
        ];

        if ($this->mppId) {
            MateriaPeriodoParalelo::findOrFail($this->mppId)->update($data);
            $msg = 'Módulo actualizado correctamente.';
        } else {
            MateriaPeriodoParalelo::create($data);
            $msg = 'Módulo creado correctamente.';
        }

        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => $msg]);
        $this->redirect(route('administracion.administrativa.materia_periodo_paralelo.index'));
    }

    public function render()
    {
        $periodos        = Periodo::orderByDesc('fecha_inicio')->get();
        $periodoCurrent  = $this->periodoId ? Periodo::find($this->periodoId) : null;
        $paralelos       = Paralelo::orderBy('name')->get();

        $periodoId = $this->periodoId;
        $materias  = $periodoId
            ? Materia::with('semestre.carrera')
                ->whereHas('semestre', function ($q) use ($periodoId) {
                    $q->whereHas('carrera', function ($q2) use ($periodoId) {
                        $q2->whereExists(function ($sq) use ($periodoId) {
                            $sq->selectRaw(1)
                               ->from('carrera_periodo')
                               ->whereColumn('carrera_periodo.carrera_id', 'carreras.id')
                               ->where('carrera_periodo.periodo_id', $periodoId);
                        });
                    });
                })
                ->orderBy('name')
                ->get()
                ->sortBy(fn ($m) => ($m->semestre->carrera->name ?? '') . ($m->semestre->name ?? '') . $m->name)
                ->values()
            : collect();

        $materiaActual = $this->materiaId
            ? Materia::with('semestre.carrera')->find($this->materiaId)
            : null;

        return view('livewire.administration.mpp-form', [
            'periodos'       => $periodos,
            'periodoCurrent' => $periodoCurrent,
            'materias'       => $materias,
            'paralelos'      => $paralelos,
            'materiaActual'  => $materiaActual,
        ]);
    }
}
