<?php

namespace App\Livewire\Administration;

use App\Models\AsignacionDocente;
use App\Models\Horario;
use App\Models\Periodo;
use Livewire\Component;

class HorariosForm extends Component
{
    public ?int $horarioId = null;

    public ?int $periodoId    = null;
    public ?int $asignacionId = null;

    public string $diaSemana      = 'Lunes';
    public string $horaInicio     = '';
    public string $horaFin        = '';
    public string $aula           = '';
    public string $modalidadClase = 'Presencial';

    public function mount(?int $horarioId = null): void
    {
        if ($horarioId) {
            $this->horarioId = $horarioId;
            $h = Horario::findOrFail($horarioId);
            $this->periodoId      = $h->periodo_id;
            $this->asignacionId   = $h->asignacion_docente_id;
            $this->diaSemana      = $h->dia_semana;
            $this->horaInicio     = $h->hora_inicio?->format('H:i') ?? '';
            $this->horaFin        = $h->hora_fin?->format('H:i') ?? '';
            $this->aula           = $h->aula ?? '';
            $this->modalidadClase = $h->modalidad_clase;
            return;
        }

        $activo = Periodo::whereExists(fn ($q) =>
            $q->selectRaw(1)->from('carrera_periodo')
              ->whereColumn('carrera_periodo.periodo_id', 'periodos.id')
              ->where('carrera_periodo.is_current', true)
        )->orderByDesc('fecha_inicio')->first();

        if ($activo) {
            $this->periodoId = $activo->id;
        }
    }

    public function updatedPeriodoId(): void
    {
        $this->asignacionId = null;
    }

    public function seleccionarAsignacion(?int $id): void
    {
        $this->asignacionId = $id;
    }

    public function guardar(): void
    {
        $this->validate([
            'periodoId'      => 'required|exists:periodos,id',
            'asignacionId'   => 'required|exists:asignacion_docentes,id',
            'diaSemana'      => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'horaInicio'     => 'required|date_format:H:i',
            'horaFin'        => 'required|date_format:H:i',
            'aula'           => 'nullable|string|max:45',
            'modalidadClase' => 'required|in:Presencial,Virtual,Híbrida,Semipresencial',
        ], [
            'periodoId.required'      => 'Selecciona un período.',
            'asignacionId.required'   => 'Selecciona una asignación docente.',
            'diaSemana.required'      => 'Selecciona un día.',
            'horaInicio.required'     => 'Ingresa la hora de inicio.',
            'horaFin.required'        => 'Ingresa la hora de fin.',
            'modalidadClase.required' => 'Selecciona una modalidad.',
        ]);

        if ($this->horaInicio && $this->horaFin && $this->horaFin <= $this->horaInicio) {
            $this->addError('horaFin', 'La hora de fin debe ser mayor a la hora de inicio.');
            return;
        }

        if ($conflicto = $this->detectConflicto()) {
            $this->addError('conflicto', $conflicto);
            return;
        }

        $asignacion = AsignacionDocente::findOrFail($this->asignacionId);

        $data = [
            'asignacion_docente_id' => $this->asignacionId,
            'materia_id'            => $asignacion->materia_id,
            'paralelo_id'           => $asignacion->paralelo_id,
            'periodo_id'            => $asignacion->periodo_id,
            'dia_semana'            => $this->diaSemana,
            'hora_inicio'           => $this->horaInicio,
            'hora_fin'              => $this->horaFin,
            'aula'                  => $this->aula ?: null,
            'modalidad_clase'       => $this->modalidadClase,
        ];

        if ($this->horarioId) {
            Horario::findOrFail($this->horarioId)->update($data);
            $msg = 'Horario actualizado correctamente.';
        } else {
            Horario::create($data);
            $msg = 'Horario creado correctamente.';
        }

        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => $msg]);
        $this->redirect(route('administracion.administrativa.horarios.index'));
    }

    private function detectConflicto(): ?string
    {
        if (! $this->asignacionId || ! $this->diaSemana || ! $this->horaInicio || ! $this->horaFin) {
            return null;
        }

        $asignacion = AsignacionDocente::find($this->asignacionId);
        if (! $asignacion) return null;

        $base = Horario::where('periodo_id', $this->periodoId)
            ->where('dia_semana', $this->diaSemana)
            ->where('hora_inicio', '<', $this->horaFin)
            ->where('hora_fin', '>', $this->horaInicio);

        if ($this->horarioId) {
            $base = $base->where('id', '!=', $this->horarioId);
        }

        if ((clone $base)
            ->whereHas('asignacionDocente', fn ($q) => $q->where('docente_id', $asignacion->docente_id))
            ->exists()
        ) {
            return 'El docente ya tiene un horario en ese día y rango de horas.';
        }

        if ((clone $base)->where('paralelo_id', $asignacion->paralelo_id)->exists()) {
            return 'El paralelo ya tiene un horario en ese día y rango de horas.';
        }

        return null;
    }

    public function render()
    {
        $asignaciones = $this->periodoId
            ? AsignacionDocente::with(['docente', 'materia', 'paralelo'])
                ->where('periodo_id', $this->periodoId)
                ->get()
                ->sortBy(fn ($a) => ($a->materia->name ?? '') . ($a->docente->name ?? ''))
                ->values()
            : collect();

        $asignacionActual = $this->asignacionId
            ? AsignacionDocente::with(['docente', 'materia', 'paralelo', 'periodo'])->find($this->asignacionId)
            : null;

        $conflicto = ($this->horaInicio && $this->horaFin && $this->asignacionId && $this->diaSemana)
            ? $this->detectConflicto()
            : null;

        return view('livewire.administration.horarios-form', [
            'periodos'         => Periodo::orderByDesc('fecha_inicio')->get(),
            'asignaciones'     => $asignaciones,
            'asignacionActual' => $asignacionActual,
            'conflicto'        => $conflicto,
            'dias'             => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            'modalidades'      => ['Presencial', 'Virtual', 'Híbrida', 'Semipresencial'],
        ]);
    }
}
