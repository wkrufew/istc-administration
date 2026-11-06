<?php

namespace App\Livewire\Administration;

use App\Models\AsignacionDocente;
use App\Models\Asistencia;
use App\Models\AuditoriaGeneral;
use App\Models\Calificacion;
use App\Models\DetalleMatricula;
use App\Models\Materia;
use App\Models\MateriaPeriodoParalelo;
use App\Models\Matricula;
use App\Models\User;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Vista de solo lectura de las matrículas de un estudiante, con tres correcciones puntuales
 * que nunca rehacen la matrícula (las notas y asistencias siguen ligadas a su registro):
 * cambiar de paralelo, agregar una materia y quitar una materia que aún no tiene notas.
 * Solo en matrículas del período en curso de su carrera; cada cambio queda en la auditoría.
 */
class VerMatricula extends Component
{
    use WithAuthorization;

    public const MAX_MATERIAS = 8;

    public bool  $mostrar      = false;
    public ?int  $estudianteId = null;
    public ?int  $matriculaId  = null;

    // Acción en curso: 'paralelo' | 'agregar' | 'quitar'
    public ?string $accion          = null;
    public ?int    $detalleId       = null;
    public ?int    $nuevoParaleloId = null;
    public ?int    $nuevaMateriaId  = null;
    public string  $motivo          = '';

    #[On('abrir-ver-matricula')]
    public function abrir(int $estudianteId, ?int $matriculaId = null): void
    {
        if ($this->sinPermiso('gestionar_matriculas')) return;

        $this->reset(['accion', 'detalleId', 'nuevoParaleloId', 'nuevaMateriaId', 'motivo']);
        $this->resetErrorBag();
        $this->estudianteId = $estudianteId;
        $this->matriculaId  = $matriculaId
            ?? Matricula::where('user_id', $estudianteId)->latest('fecha_matricula')->latest('id')->value('id');
        $this->mostrar = true;
        unset($this->estudiante, $this->matriculas, $this->matricula);
        $this->dispatch('modal-opened');
    }

    public function cerrar(): void
    {
        $this->reset();
        $this->resetErrorBag();
        $this->dispatch('modal-closed');
    }

    public function updatedMatriculaId(): void
    {
        $this->cancelarAccion();
        unset($this->matricula);
    }

    // =========================================================================
    // DATOS
    // =========================================================================

    #[Computed]
    public function estudiante(): ?User
    {
        return $this->estudianteId ? User::find($this->estudianteId) : null;
    }

    #[Computed]
    public function matriculas()
    {
        return Matricula::where('user_id', $this->estudianteId)
            ->with('periodo:id,code')
            ->orderByDesc('fecha_matricula')
            ->orderByDesc('id')
            ->get(['id', 'code', 'tipo', 'estado', 'periodo_id', 'fecha_matricula']);
    }

    #[Computed]
    public function matricula(): ?Matricula
    {
        if (! $this->matriculaId) return null;

        return Matricula::where('user_id', $this->estudianteId)
            ->with([
                'periodo', 'carrera',
                'detalles' => fn($q) => $q->withCount(['calificaciones', 'asistencias'])
                    ->with(['materia.semestre', 'paralelo', 'calificacion']),
                'obligacionesFinancieras' => fn($q) => $q->with(['pagos', 'becaAplicada.tipoBeca', 'convenioAplicado.tipoConvenio'])
                    ->orderBy('tipo')->orderBy('fecha_vencimiento'),
            ])
            ->find($this->matriculaId);
    }

    /** Docente de cada materia+paralelo de la matrícula, en una sola consulta. */
    #[Computed]
    public function docentes(): array
    {
        $m = $this->matricula;
        if (! $m) return [];

        return AsignacionDocente::with('docente:id,name')
            ->where('periodo_id', $m->periodo_id)
            ->whereIn('materia_id', $m->detalles->pluck('materia_id'))
            ->get()
            ->mapWithKeys(fn($a) => [$a->materia_id . '_' . $a->paralelo_id => $a->docente?->name])
            ->all();
    }

    /** Las correcciones solo aplican a una matrícula vigente del período en curso de su carrera. */
    #[Computed]
    public function editable(): bool
    {
        $m = $this->matricula;
        if (! $m || ! auth()->user()?->can('editar_matriculas')) return false;

        return $this->esEditable($m);
    }

    private function esEditable(Matricula $m): bool
    {
        return $m->tipo !== Matricula::TIPO_VALIDACION
            && ! in_array($m->estado, ['Retirada', 'Cancelada'], true)
            && DB::table('carrera_periodo')
                ->where('carrera_id', $m->carrera_id)
                ->where('periodo_id', $m->periodo_id)
                ->where('is_current', true)
                ->where('is_active', true) // mismo criterio que Carrera::periodoActual()
                ->exists();
    }

    /** Paralelos ofertados para la materia del detalle elegido (sin el actual), con su cupo. */
    #[Computed]
    public function paralelosDisponibles(): array
    {
        $materiaId = match ($this->accion) {
            'paralelo' => $this->matricula?->detalles->firstWhere('id', $this->detalleId)?->materia_id,
            'agregar'  => $this->nuevaMateriaId,
            default    => null,
        };
        if (! $materiaId || ! $this->matricula) return [];

        $actual = $this->accion === 'paralelo'
            ? $this->matricula->detalles->firstWhere('id', $this->detalleId)?->paralelo_id
            : null;

        return MateriaPeriodoParalelo::with('paralelo')
            ->where('periodo_id', $this->matricula->periodo_id)
            ->where('materia_id', $materiaId)
            ->where('is_active', true)
            ->get()
            ->reject(fn($mpp) => $mpp->paralelo_id === $actual)
            ->map(fn($mpp) => [
                'id'         => $mpp->paralelo_id,
                'name'       => $mpp->paralelo?->name ?? '—',
                'disponible' => max(0, $mpp->cupo_maximo - $this->cupoUsado($materiaId, $mpp->paralelo_id, $this->matricula->periodo_id)),
            ])
            ->values()
            ->all();
    }

    /**
     * Materias que se pueden agregar: de su carrera, no aprobadas, no inscritas ya en esta
     * matrícula, sin arrastre pendiente (el arrastre tiene su propio cobro), con prerrequisitos
     * cumplidos y ofertadas en el período.
     */
    #[Computed]
    public function materiasParaAgregar(): array
    {
        $m = $this->matricula;
        if (! $m || $this->accion !== 'agregar') return [];

        $aprobadas = DB::table('calificacions')
            ->join('detalle_matriculas', 'calificacions.detalle_matricula_id', '=', 'detalle_matriculas.id')
            ->where('detalle_matriculas.user_id', $m->user_id)
            ->whereNull('detalle_matriculas.deleted_at')
            ->where('calificacions.estado_final', 'Aprobado')
            ->pluck('detalle_matriculas.materia_id')
            ->all();

        $enArrastre = DB::table('materias_arrastradas')
            ->where('user_id', $m->user_id)
            ->whereIn('estado', ['Arrastrada', 'Inscrita'])
            ->pluck('materia_id')
            ->all();

        $ofertadas = MateriaPeriodoParalelo::where('periodo_id', $m->periodo_id)
            ->where('is_active', true)
            ->pluck('materia_id')
            ->unique()
            ->all();

        $excluir = array_merge($aprobadas, $enArrastre, $m->detalles->pluck('materia_id')->all());

        return Materia::with('semestre:id,name,order')
            ->whereHas('semestre', fn($q) => $q->where('carrera_id', $m->carrera_id))
            ->whereIn('id', $ofertadas)
            ->whereNotIn('id', $excluir)
            ->get()
            ->filter(fn($materia) => $this->prerrequisitosCumplidos($materia->id, $aprobadas))
            ->sortBy(fn($materia) => [$materia->semestre?->order, $materia->name])
            ->map(fn($materia) => [
                'id'       => $materia->id,
                'name'     => $materia->name,
                'semestre' => $materia->semestre?->name,
            ])
            ->values()
            ->all();
    }

    // =========================================================================
    // ACCIONES
    // =========================================================================

    public function iniciarAccion(string $accion, ?int $detalleId = null): void
    {
        if ($this->sinPermiso('editar_matriculas')) return;
        if (! in_array($accion, ['paralelo', 'agregar', 'quitar'], true)) return;

        $this->reset(['nuevoParaleloId', 'nuevaMateriaId', 'motivo']);
        $this->resetErrorBag();
        $this->accion    = $accion;
        $this->detalleId = $detalleId;
        unset($this->paralelosDisponibles, $this->materiasParaAgregar);
    }

    public function cancelarAccion(): void
    {
        $this->reset(['accion', 'detalleId', 'nuevoParaleloId', 'nuevaMateriaId', 'motivo']);
        $this->resetErrorBag();
    }

    public function updatedNuevaMateriaId(): void
    {
        $this->nuevoParaleloId = null;
        unset($this->paralelosDisponibles);
    }

    public function guardarCambioParalelo(): void
    {
        if ($this->sinPermiso('editar_matriculas')) return;
        if (! $this->validarMotivo()) return;
        $this->validate(['nuevoParaleloId' => 'required|integer'], ['nuevoParaleloId.required' => 'Elige el paralelo nuevo.']);

        $resultado = DB::transaction(function () {
            $detalle = DetalleMatricula::with('matricula')->lockForUpdate()->find($this->detalleId);
            $error   = $this->errorDetalle($detalle);
            if ($error) return $error;

            $matricula = $detalle->matricula;
            if ((int) $detalle->paralelo_id === (int) $this->nuevoParaleloId) return 'Ya está en ese paralelo.';

            $error = $this->errorCupo($detalle->materia_id, (int) $this->nuevoParaleloId, $matricula->periodo_id);
            if ($error) return $error;

            $anterior = $detalle->paralelo_id;
            $detalle->update(['paralelo_id' => $this->nuevoParaleloId]);

            // Las notas en curso pasan al docente del paralelo nuevo: solo quien registró una
            // calificación puede modificarla. Las asistencias tomadas quedan como historial.
            $docenteNuevo = AsignacionDocente::where('materia_id', $detalle->materia_id)
                ->where('paralelo_id', $this->nuevoParaleloId)
                ->where('periodo_id', $matricula->periodo_id)
                ->value('docente_id');
            if ($docenteNuevo) {
                Calificacion::where('detalle_matricula_id', $detalle->id)->update(['docente_id' => $docenteNuevo]);
            }

            $this->auditar($detalle, 'updated', ['paralelo_id' => $anterior], [
                'paralelo_id' => (int) $this->nuevoParaleloId,
                'motivo'      => 'Cambio de paralelo: ' . trim($this->motivo),
            ]);

            return null;
        });

        $this->terminar($resultado, 'Paralelo cambiado. Las notas y asistencias se conservan.');
    }

    public function guardarAgregarMateria(): void
    {
        if ($this->sinPermiso('editar_matriculas')) return;
        if (! $this->validarMotivo()) return;
        $this->validate([
            'nuevaMateriaId'  => 'required|integer',
            'nuevoParaleloId' => 'required|integer',
        ], [
            'nuevaMateriaId.required'  => 'Elige la materia.',
            'nuevoParaleloId.required' => 'Elige el paralelo.',
        ]);

        // La materia debe seguir siendo elegible (no se confía en la lista mostrada)
        unset($this->materiasParaAgregar);
        if (! collect($this->materiasParaAgregar)->contains('id', (int) $this->nuevaMateriaId)) {
            $this->terminar('Esa materia ya no se puede agregar (aprobada, inscrita, en arrastre o sin prerrequisitos).', '');
            return;
        }

        $resultado = DB::transaction(function () {
            $matricula = Matricula::with('carrera')->lockForUpdate()->find($this->matriculaId);
            if (! $matricula || ! $this->esEditable($matricula)) return 'Esta matrícula ya no se puede modificar.';

            $activas = DetalleMatricula::where('matricula_id', $matricula->id)->where('estado', '!=', 'Retirado')->count();
            if ($activas >= self::MAX_MATERIAS) return 'La matrícula ya tiene ' . self::MAX_MATERIAS . ' materias, el máximo permitido.';

            $error = $this->errorCupo((int) $this->nuevaMateriaId, (int) $this->nuevoParaleloId, $matricula->periodo_id);
            if ($error) return $error;

            $materia = Materia::find($this->nuevaMateriaId);
            $detalle = DetalleMatricula::create([
                'asignacion'    => now(),
                'code'          => $matricula->code . '-' . $materia->code,
                'tipo'          => 'Normal',
                'estado'        => 'Inscrito',
                'costo_materia' => (($materia->horas_teoricas + $materia->horas_practicas) / 48) * $matricula->carrera->costo_credito,
                'es_repeticion' => false,
                'matricula_id'  => $matricula->id,
                'materia_id'    => $materia->id,
                'paralelo_id'   => $this->nuevoParaleloId,
                'user_id'       => $matricula->user_id,
            ]);

            $this->auditar($detalle, 'created', [], [
                'materia_id'  => $materia->id,
                'paralelo_id' => (int) $this->nuevoParaleloId,
                'motivo'      => 'Materia agregada: ' . trim($this->motivo),
            ]);

            return null;
        });

        $this->terminar($resultado, 'Materia agregada a la matrícula.');
    }

    public function guardarQuitarMateria(): void
    {
        if ($this->sinPermiso('editar_matriculas')) return;
        if (! $this->validarMotivo()) return;

        $resultado = DB::transaction(function () {
            $detalle = DetalleMatricula::with('matricula')->lockForUpdate()->find($this->detalleId);
            $error   = $this->errorDetalle($detalle);
            if ($error) return $error;

            if ($detalle->tipo === 'Arrastre') {
                return 'Un arrastre no se quita desde aquí: tiene su propio cobro. Consulta con el administrador.';
            }
            if (Calificacion::where('detalle_matricula_id', $detalle->id)->exists()
                || Asistencia::where('detalle_matricula_id', $detalle->id)->exists()) {
                return 'Esta materia ya tiene notas o asistencias registradas; no se puede quitar.';
            }
            $restantes = DetalleMatricula::where('matricula_id', $detalle->matricula_id)
                ->where('id', '!=', $detalle->id)
                ->where('estado', '!=', 'Retirado')
                ->count();
            if ($restantes === 0) return 'Es la única materia de la matrícula; no se puede quitar.';

            $this->auditar($detalle, 'deleted', [
                'materia_id'  => $detalle->materia_id,
                'paralelo_id' => $detalle->paralelo_id,
            ], ['motivo' => 'Materia quitada: ' . trim($this->motivo)]);

            // Sin notas ni asistencias no queda nada ligado al registro
            $detalle->forceDelete();

            return null;
        });

        $this->terminar($resultado, 'Materia quitada de la matrícula.');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function validarMotivo(): bool
    {
        $this->resetErrorBag('motivo');
        if (mb_strlen(trim($this->motivo)) < 5) {
            $this->addError('motivo', 'Indica el motivo (mínimo 5 caracteres). Queda en la auditoría.');
            return false;
        }
        return true;
    }

    /** El detalle debe ser de la matrícula abierta, seguir inscrito y la matrícula ser editable. */
    private function errorDetalle(?DetalleMatricula $detalle): ?string
    {
        if (! $detalle || (int) $detalle->matricula_id !== (int) $this->matriculaId) return 'La materia ya no forma parte de esta matrícula.';
        if ($detalle->estado === 'Retirado') return 'La materia está retirada.';
        if (! $detalle->matricula || ! $this->esEditable($detalle->matricula)) return 'Esta matrícula ya no se puede modificar.';
        return null;
    }

    private function errorCupo(int $materiaId, int $paraleloId, int $periodoId): ?string
    {
        $mpp = MateriaPeriodoParalelo::where('materia_id', $materiaId)
            ->where('paralelo_id', $paraleloId)
            ->where('periodo_id', $periodoId)
            ->where('is_active', true)
            ->first();
        if (! $mpp) return 'Ese paralelo no está ofertado para la materia en este período.';

        return $this->cupoUsado($materiaId, $paraleloId, $periodoId) >= $mpp->cupo_maximo
            ? 'Ese paralelo ya no tiene cupo disponible.'
            : null;
    }

    /** Mismo criterio de cupo que el wizard de matrícula. */
    private function cupoUsado(int $materiaId, int $paraleloId, int $periodoId): int
    {
        return DB::table('detalle_matriculas as dm')
            ->join('matriculas as m', 'dm.matricula_id', '=', 'm.id')
            ->where('m.periodo_id', $periodoId)
            ->where('m.estado', '!=', 'Cancelada')
            ->where('dm.estado', '!=', 'Retirado')
            ->whereNull('dm.deleted_at')
            ->where('dm.materia_id', $materiaId)
            ->where('dm.paralelo_id', $paraleloId)
            ->count();
    }

    private function prerrequisitosCumplidos(int $materiaId, array $aprobadas): bool
    {
        $prerrequisitos = DB::table('prerequisitos')
            ->where('materia_id', $materiaId)
            ->where('es_obligatorio', true)
            ->pluck('prerequisito_id')
            ->all();

        return empty(array_diff($prerrequisitos, $aprobadas));
    }

    /** DetalleMatricula no tiene observer de auditoría: se registra a mano con el motivo. */
    private function auditar(DetalleMatricula $detalle, string $evento, array $antes, array $despues): void
    {
        AuditoriaGeneral::create([
            'auditable_type'  => DetalleMatricula::class,
            'auditable_id'    => $detalle->id,
            'evento'          => $evento,
            'valores_antes'   => $antes ?: null,
            'valores_despues' => $despues + ['matricula' => $detalle->matricula?->code ?? $this->matricula?->code],
            'user_id'         => auth()->id(),
            'ip_address'      => request()?->ip(),
        ]);
    }

    private function terminar(?string $error, string $exito): void
    {
        if ($error) {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'No se pudo aplicar', 'text' => $error]);
            unset($this->materiasParaAgregar, $this->paralelosDisponibles);
            return;
        }

        $this->cancelarAccion();
        unset($this->matricula, $this->docentes, $this->paralelosDisponibles, $this->materiasParaAgregar);
        $this->dispatch('swal', ['icon' => 'success', 'title' => $exito, 'timer' => 2500]);
        $this->dispatch('matricula-modificada');
    }

    public function render()
    {
        return view('livewire.administration.ver-matricula');
    }
}
