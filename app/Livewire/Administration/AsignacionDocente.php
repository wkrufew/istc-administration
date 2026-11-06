<?php

namespace App\Livewire\Administration;

use App\Models\AsignacionDocente as AsignacionDocenteModel;
use App\Models\MateriaPeriodoParalelo;
use App\Models\Periodo;
use App\Models\User;
use App\Services\AsignacionDocenteService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AsignacionDocente extends Component
{
    public User $docente;

    // Paso 1 del formulario: período y carrera juntos ("periodoId:carreraId"); las materias
    // que se ofrecen son solo las de esa carrera
    public string $periodoCarrera = '';
    public ?int $periodoId  = null;
    public ?int $carreraId  = null;
    public ?int $materiaId  = null;
    public ?int $paraleloId = null;

    // Filtro de la tabla de asignaciones: "periodoId:carreraId" ('' = todas). La carrera es la
    // de la materia asignada (materia → semestre → carrera), no las del período general
    public string $filtro = '';

    public function mount(User $docente): void
    {
        $this->docente = $docente;

        // Preseleccionar el periodo activo más reciente
        $periodoActivo = Periodo::whereExists(fn($q) =>
            $q->selectRaw(1)
              ->from('carrera_periodo')
              ->whereColumn('carrera_periodo.periodo_id', 'periodos.id')
              ->where('carrera_periodo.is_current', true)
        )->orderByDesc('fecha_inicio')->first();

        if ($periodoActivo) {
            // Si en ese período hay una sola carrera vigente, el paso 1 queda elegido
            $carrerasVigentes = $periodoActivo->carreras()->wherePivot('is_current', true)->pluck('carreras.id');
            if ($carrerasVigentes->count() === 1) {
                $this->periodoCarrera = $periodoActivo->id . ':' . $carrerasVigentes->first();
                $this->updatedPeriodoCarrera();
            }

            // La tabla abre en ese período si el docente tiene ahí materias de una sola carrera;
            // si son de varias, abre en "Todas" para no esconder ninguna
            $delPeriodo = $this->opcionesFiltro()->where('periodo_id', $periodoActivo->id);
            if ($delPeriodo->count() === 1) {
                $this->filtro = $delPeriodo->first()['valor'];
            }
        }
    }

    // =========================================================================
    // ACTUALIZACIÓN EN CASCADA
    // =========================================================================

    public function updatedPeriodoCarrera(): void
    {
        [$periodo, $carrera] = array_map('intval', explode(':', $this->periodoCarrera) + [1 => 0]);
        $this->periodoId  = $periodo ?: null;
        $this->carreraId  = $carrera ?: null;
        $this->materiaId  = null;
        $this->paraleloId = null;
    }

    public function updatedMateriaId(): void
    {
        $this->paraleloId = null;
    }

    // Llamado desde el combobox Alpine cuando el usuario selecciona una materia
    public function seleccionarMateria(?int $id): void
    {
        $this->materiaId  = $id;
        $this->paraleloId = null;
    }

    // =========================================================================
    // ASIGNAR
    // =========================================================================

    public function asignar(): void
    {
        $this->validate([
            'periodoId'  => 'required|exists:periodos,id',
            'carreraId'  => 'required|exists:carreras,id',
            'materiaId'  => 'required|exists:materias,id',
            'paraleloId' => 'required|exists:paralelos,id',
        ], [
            'periodoId.required'  => 'Selecciona un período.',
            'carreraId.required'  => 'Selecciona el período y la carrera.',
            'materiaId.required'  => 'Selecciona una materia.',
            'paraleloId.required' => 'Selecciona un paralelo.',
        ]);

        // La materia debe ser de la carrera elegida
        $deLaCarrera = \App\Models\Materia::whereKey($this->materiaId)
            ->whereHas('semestre', fn($s) => $s->where('carrera_id', $this->carreraId))
            ->exists();
        if (! $deLaCarrera) {
            $this->addError('general', 'La materia elegida no pertenece a la carrera seleccionada.');
            return;
        }

        // Verificar que la combinación existe en MPP
        $mppExiste = MateriaPeriodoParalelo::where('periodo_id', $this->periodoId)
            ->where('materia_id', $this->materiaId)
            ->where('paralelo_id', $this->paraleloId)
            ->where('is_active', true)
            ->exists();

        if (! $mppExiste) {
            $this->addError('general', 'Esta combinación de materia/paralelo no está configurada en el módulo Materia-Período-Paralelo.');
            return;
        }

        // Verificar si la materia/paralelo/período ya tiene cualquier docente asignado
        $yaAsignado = AsignacionDocenteModel::with('docente')
            ->where('materia_id',  $this->materiaId)
            ->where('paralelo_id', $this->paraleloId)
            ->where('periodo_id',  $this->periodoId)
            ->first();

        if ($yaAsignado) {
            if ($yaAsignado->docente_id === $this->docente->id) {
                $this->addError('general', 'Este docente ya tiene esta materia asignada en el período y paralelo seleccionados.');
            } else {
                $nombre = $yaAsignado->docente?->name ?? 'otro docente';
                $this->addError('general', "Esta materia ya está asignada al docente {$nombre} en el período y paralelo seleccionados.");
            }
            return;
        }

        AsignacionDocenteModel::create([
            'docente_id'  => $this->docente->id,
            'materia_id'  => $this->materiaId,
            'paralelo_id' => $this->paraleloId,
            'periodo_id'  => $this->periodoId,
        ]);

        $this->materiaId  = null;
        $this->paraleloId = null;
        $this->resetErrorBag();
        // La tabla pasa al período y carrera de la materia recién asignada
        $this->filtro = $this->periodoId . ':' . $this->carreraId;

        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Asignación registrada correctamente.']);
    }

    // =========================================================================
    // ELIMINAR
    // =========================================================================

    public function eliminar(int $asignacionId): void
    {
        $asignacion = AsignacionDocenteModel::where('id', $asignacionId)
            ->where('docente_id', $this->docente->id)
            ->firstOrFail();

        try {
            AsignacionDocenteService::eliminar($asignacion);
        } catch (\DomainException $e) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => $e->getMessage()]);
            return;
        }

        // Si era la última de ese período y carrera, el filtro vuelve a "Todas"
        if ($this->filtro !== '' && ! $this->opcionesFiltro()->contains('valor', $this->filtro)) {
            $this->filtro = '';
        }

        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Asignación eliminada.']);
    }

    // =========================================================================
    // CAMBIAR DOCENTE (reemplazo a mitad de período)
    // =========================================================================

    public ?int $cambioAsignacionId = null;
    public $cambioDocenteId = '';
    public string $cambioMotivo = '';

    public function abrirCambio(int $asignacionId): void
    {
        $this->resetErrorBag(['cambioDocenteId', 'cambioMotivo']);
        $this->cambioAsignacionId = $this->docente->asignacionesDocente()->whereKey($asignacionId)->value('id');
        $this->cambioDocenteId    = '';
        $this->cambioMotivo       = '';
    }

    public function cerrarCambio(): void
    {
        $this->reset(['cambioAsignacionId', 'cambioDocenteId', 'cambioMotivo']);
        $this->resetErrorBag(['cambioDocenteId', 'cambioMotivo']);
    }

    public function confirmarCambio(): void
    {
        $this->validate([
            'cambioDocenteId' => 'required|integer',
            'cambioMotivo'    => 'required|string|min:5|max:500',
        ], [
            'cambioDocenteId.required' => 'Elige el docente que tomará la materia.',
            'cambioMotivo.required'    => 'Indica el motivo del cambio.',
            'cambioMotivo.min'         => 'Describe el motivo con un poco más de detalle.',
        ]);

        $asignacion = $this->docente->asignacionesDocente()->whereKey($this->cambioAsignacionId)->firstOrFail();

        try {
            $notas = AsignacionDocenteService::cambiarDocente($asignacion, (int) $this->cambioDocenteId, $this->cambioMotivo);
        } catch (\DomainException $e) {
            $this->addError('cambioDocenteId', $e->getMessage());
            return;
        }

        $nombre = User::find($this->cambioDocenteId)?->name;
        $this->cerrarCambio();

        // La asignación ya no es de este docente: si era la última de la opción del filtro, vuelve a "Todas"
        if ($this->filtro !== '' && ! $this->opcionesFiltro()->contains('valor', $this->filtro)) {
            $this->filtro = '';
        }

        $this->dispatch('toast', [
            'tipo'    => 'success',
            'mensaje' => "La materia pasó a {$nombre}" . ($notas ? " con {$notas} nota" . ($notas > 1 ? 's' : '') : '') . '.',
        ]);
    }

    // =========================================================================
    // DATA HELPERS
    // =========================================================================

    private function getPeriodos()
    {
        return Periodo::whereExists(fn($q) =>
            $q->selectRaw(1)
              ->from('carrera_periodo')
              ->whereColumn('carrera_periodo.periodo_id', 'periodos.id')
              ->where('carrera_periodo.is_current', true)
        )
        ->with(['carreras' => fn($q) => $q->wherePivot('is_current', true)->orderBy('name')])
        ->orderByDesc('fecha_inicio')
        ->get();
    }

    private function getMaterias()
    {
        if (! $this->periodoId || ! $this->carreraId) return collect();

        return MateriaPeriodoParalelo::with('materia')
            ->where('periodo_id', $this->periodoId)
            ->where('is_active', true)
            ->whereHas('materia.semestre', fn($s) => $s->where('carrera_id', $this->carreraId))
            ->get()
            ->pluck('materia')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    private function getParalelos()
    {
        if (! $this->periodoId || ! $this->materiaId) return collect();

        return MateriaPeriodoParalelo::with('paralelo')
            ->where('periodo_id', $this->periodoId)
            ->where('materia_id', $this->materiaId)
            ->where('is_active', true)
            ->get()
            ->pluck('paralelo')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    private function getAsignaciones()
    {
        $q = $this->docente
            ->asignacionesDocente()
            ->with(['materia', 'paralelo', 'periodo.carreras']);

        if ($this->filtro !== '') {
            [$periodoId, $carreraId] = array_map('intval', explode(':', $this->filtro) + [1 => 0]);
            $q->where('periodo_id', $periodoId)
              ->whereHas('materia', fn($m) => $carreraId
                  ? $m->whereHas('semestre', fn($s) => $s->where('carrera_id', $carreraId))
                  : $m->whereDoesntHave('semestre', fn($s) => $s->whereNotNull('carrera_id')));
        }

        // Para cada fila: si está vigente para su carrera y si se puede eliminar (y qué se borraría)
        return $q->orderByDesc('created_at')->get()->each(function ($a) {
            $impacto = AsignacionDocenteService::impacto($a);
            $a->setAttribute('es_vigente', AsignacionDocenteService::esVigente($a));
            $a->setAttribute('motivo_bloqueo', AsignacionDocenteService::motivoNoEliminar($a, $impacto));
            $a->setAttribute('aviso_eliminar', AsignacionDocenteService::avisoEliminar($impacto));
        });
    }

    /**
     * Opciones del filtro: solo los períodos y carreras donde ESTE docente tiene materias
     * asignadas (vigentes y pasados), una opción por cada carrera, del período más reciente
     * al más antiguo.
     */
    private function opcionesFiltro()
    {
        return $this->docente->asignacionesDocente()
            ->with(['periodo:id,code,fecha_inicio', 'materia:id,semestre_id', 'materia.semestre:id,carrera_id', 'materia.semestre.carrera:id,name'])
            ->get()
            ->filter(fn($a) => $a->periodo)
            ->map(function ($a) {
                $carrera = $a->materia?->semestre?->carrera;
                return [
                    'valor'        => $a->periodo_id . ':' . (int) $carrera?->id,
                    'periodo_id'   => $a->periodo_id,
                    'etiqueta'     => $a->periodo->code . ' — ' . ($carrera?->name ?? 'Sin carrera'),
                    'fecha_inicio' => (string) $a->periodo->fecha_inicio,
                    'carrera'      => (string) $carrera?->name,
                ];
            })
            ->unique('valor')
            ->sortBy([['fecha_inicio', 'desc'], ['carrera', 'asc']])
            ->values();
    }

    /** Datos del modal "Cambiar docente" (null si está cerrado). */
    private function datosCambio(): ?array
    {
        if (! $this->cambioAsignacionId) return null;

        $a = AsignacionDocenteModel::with(['materia', 'paralelo', 'periodo'])->find($this->cambioAsignacionId);
        if (! $a) return null;

        return [
            'asignacion' => $a,
            'notas'      => AsignacionDocenteService::notasDelCurso($a),
            'impacto'    => AsignacionDocenteService::impacto($a),
            'docentes'   => \App\Support\PerfilUsuario::docentes()->where('is_active', true)
                ->where('id', '!=', $this->docente->id)->orderBy('name')->get(['id', 'name', 'cedula']),
        ];
    }

    private function checkYaExiste(): ?AsignacionDocenteModel
    {
        if (! $this->periodoId || ! $this->materiaId || ! $this->paraleloId) return null;

        return AsignacionDocenteModel::with('docente')
            ->where('materia_id',  $this->materiaId)
            ->where('paralelo_id', $this->paraleloId)
            ->where('periodo_id',  $this->periodoId)
            ->first();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.administration.asignacion-docente', [
            'periodos'    => $this->getPeriodos(),
            'materias'    => $this->getMaterias(),
            'paralelos'   => $this->getParalelos(),
            'asignaciones' => $this->getAsignaciones(),
            'opcionesFiltro' => $this->opcionesFiltro(),
            'cambio'         => $this->datosCambio(),
            'yaExiste'    => $this->checkYaExiste(),
        ]);
    }
}
