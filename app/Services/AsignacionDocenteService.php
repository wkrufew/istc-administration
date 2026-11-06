<?php

namespace App\Services;

use App\Models\AsignacionDocente;
use App\Models\AuditoriaGeneral;
use Illuminate\Support\Facades\DB;

/**
 * Reglas para eliminar la asignación de una materia a un docente.
 *
 * Al eliminar una asignación la base de datos borra en cadena sus horarios y, con ellos, las
 * asistencias, los registros de clase no dictada y los avisos. Por eso:
 *  - si el período ya terminó para la carrera de la materia, no se elimina (queda como historial);
 *  - si tiene asistencias o notas registradas, tampoco (se perderían);
 *  - en los demás casos se puede, avisando qué se borra, y queda en auditoría.
 *
 * "Vigente" se mira en el período de la carrera de la materia (carrera_periodo), no en el
 * período general, que comparten todas las carreras.
 */
class AsignacionDocenteService
{
    public static function carreraId(AsignacionDocente $a): ?int
    {
        $a->loadMissing('materia.semestre');
        return $a->materia?->semestre?->carrera_id;
    }

    public static function esVigente(AsignacionDocente $a): bool
    {
        $carreraId = self::carreraId($a);
        if (! $carreraId) return false;

        return DB::table('carrera_periodo')
            ->where('carrera_id', $carreraId)
            ->where('periodo_id', $a->periodo_id)
            ->where('is_current', true)
            ->exists();
    }

    /** Lo que depende de la asignación y se borraría (o se perdería) al eliminarla. */
    public static function impacto(AsignacionDocente $a): array
    {
        $horarios = DB::table('horarios')->where('asignacion_docente_id', $a->id)->pluck('id');
        $detalles = self::detallesDelCurso($a);

        return [
            'horarios'           => $horarios->count(),
            'asistencias'        => $horarios->isEmpty() ? 0 : DB::table('asistencias')->whereIn('horario_id', $horarios)->count(),
            'clases_no_dictadas' => $horarios->isEmpty() ? 0 : DB::table('dias_no_lectivos')->whereIn('horario_id', $horarios)->count(),
            'avisos'             => DB::table('avisos')->where('asignacion_docente_id', $a->id)->count(),
            'notas'              => $detalles->isEmpty() ? 0 : DB::table('calificacions')->whereIn('detalle_matricula_id', $detalles)->whereNull('deleted_at')->count(),
            'contrato'           => (bool) self::contratoVigente($a),
        ];
    }

    /** Contrato vigente de la asignación en el expediente de su docente. */
    public static function contratoVigente(AsignacionDocente $a): ?\App\Models\ExpedienteDocumento
    {
        return \App\Models\ExpedienteDocumento::vigentes()
            ->where('tipo', 'contrato')
            ->where('asignacion_docente_id', $a->id)
            ->where('user_id', $a->docente_id)
            ->latest('id')
            ->first();
    }

    /** Por qué no se puede eliminar (null = se puede). */
    public static function motivoNoEliminar(AsignacionDocente $a, ?array $impacto = null): ?string
    {
        if (! self::esVigente($a)) {
            return 'Es de un período que ya terminó para su carrera: queda como historial y no se puede eliminar.';
        }

        $impacto ??= self::impacto($a);
        $registrado = array_filter([
            $impacto['asistencias'] ? $impacto['asistencias'] . ' asistencia' . ($impacto['asistencias'] > 1 ? 's' : '') : null,
            $impacto['notas'] ? $impacto['notas'] . ' nota' . ($impacto['notas'] > 1 ? 's' : '') : null,
        ]);
        if ($registrado) {
            return 'Ya tiene ' . implode(' y ', $registrado) . ' registrada' . (array_sum([$impacto['asistencias'], $impacto['notas']]) > 1 ? 's' : '')
                . ': eliminarla las haría perder.';
        }

        return null;
    }

    /** Qué se borra junto con la asignación, para el aviso de confirmación. */
    public static function avisoEliminar(array $impacto): string
    {
        $partes = array_filter([
            $impacto['horarios'] ? $impacto['horarios'] . ' horario' . ($impacto['horarios'] > 1 ? 's' : '') : null,
            $impacto['clases_no_dictadas'] ? $impacto['clases_no_dictadas'] . ' registro' . ($impacto['clases_no_dictadas'] > 1 ? 's' : '') . ' de clase no dictada' : null,
            $impacto['avisos'] ? $impacto['avisos'] . ' aviso' . ($impacto['avisos'] > 1 ? 's' : '') . ' a estudiantes' : null,
        ]);

        $texto = $partes ? 'También se eliminarán: ' . implode(', ', $partes) . '.' : 'No tiene horarios ni avisos asociados.';
        if ($impacto['contrato']) $texto .= ' El contrato subido queda en el historial del expediente del docente.';

        return $texto;
    }

    /** Detalles de matrícula del curso (materia + paralelo + período) de la asignación. */
    private static function detallesDelCurso(AsignacionDocente $a)
    {
        return DB::table('detalle_matriculas as d')
            ->join('matriculas as m', 'm.id', '=', 'd.matricula_id')
            ->where('d.materia_id', $a->materia_id)
            ->where('d.paralelo_id', $a->paralelo_id)
            ->where('m.periodo_id', $a->periodo_id)
            ->whereNull('d.deleted_at')
            ->pluck('d.id');
    }

    /** Notas del curso que pasarían al nuevo docente. */
    public static function notasDelCurso(AsignacionDocente $a): int
    {
        $detalles = self::detallesDelCurso($a);
        return $detalles->isEmpty() ? 0
            : DB::table('calificacions')->whereIn('detalle_matricula_id', $detalles)->whereNull('deleted_at')->count();
    }

    /**
     * Pasa la asignación a otro docente (reemplazo a mitad de período). Se conservan horarios,
     * asistencias y avisos. Las notas del curso pasan al nuevo docente para que pueda verlas y
     * seguir calificando (la pantalla de calificar carga las notas del docente conectado), como ya
     * se hace al cambiar a un estudiante de paralelo. El docente anterior queda en auditoría.
     *
     * El contrato subido es del docente anterior y se queda en SU expediente (ligado a la
     * asignación como historial); el nuevo docente necesitará el suyo.
     *
     * @return int notas que pasaron al nuevo docente
     * @throws \DomainException si no se puede cambiar
     */
    public static function cambiarDocente(AsignacionDocente $a, int $nuevoDocenteId, string $motivo): int
    {
        return DB::transaction(function () use ($a, $nuevoDocenteId, $motivo) {
            $a = AsignacionDocente::with(['materia.semestre', 'paralelo', 'periodo', 'docente'])->lockForUpdate()->findOrFail($a->id);

            if (! self::esVigente($a)) {
                throw new \DomainException('Es de un período que ya terminó para su carrera: el docente ya no se puede cambiar.');
            }
            if ($nuevoDocenteId === (int) $a->docente_id) {
                throw new \DomainException('Elige un docente distinto al actual.');
            }
            $nuevo = \App\Support\PerfilUsuario::docentes()->where('is_active', true)->find($nuevoDocenteId);
            if (! $nuevo) {
                throw new \DomainException('El docente elegido no existe o no está activo.');
            }
            if (trim($motivo) === '') {
                throw new \DomainException('Indica el motivo del cambio.');
            }

            $anterior = $a->docente;
            $contrato = self::contratoVigente($a)?->archivo_path ?? $a->file_contrato;

            $detalles = self::detallesDelCurso($a);
            $notas = $detalles->isEmpty() ? 0 : DB::table('calificacions')
                ->whereIn('detalle_matricula_id', $detalles)
                ->whereNull('deleted_at')
                ->update(['docente_id' => $nuevo->id, 'updated_at' => now()]);

            // file_contrato (columna anterior al expediente) se limpia para que no aparezca como del nuevo docente
            $a->update(['docente_id' => $nuevo->id, 'file_contrato' => null]);

            AuditoriaGeneral::create([
                'auditable_type'  => AsignacionDocente::class,
                'auditable_id'    => $a->id,
                'evento'          => 'updated',
                'valores_antes'   => ['docente' => $anterior?->name, 'contrato' => $contrato],
                'valores_despues' => [
                    'docente'            => $nuevo->name,
                    'materia'            => $a->materia?->name,
                    'paralelo'           => $a->paralelo?->name,
                    'periodo'            => $a->periodo?->code,
                    'notas_transferidas' => $notas,
                    'motivo'             => trim($motivo),
                ],
                'user_id'         => auth()->id(),
                'ip_address'      => request()?->ip(),
            ]);

            return $notas;
        });
    }

    /** @throws \DomainException si no se puede eliminar */
    public static function eliminar(AsignacionDocente $a): void
    {
        DB::transaction(function () use ($a) {
            $a = AsignacionDocente::with(['materia', 'paralelo', 'periodo', 'docente'])->lockForUpdate()->findOrFail($a->id);
            $impacto = self::impacto($a);

            if ($motivo = self::motivoNoEliminar($a, $impacto)) {
                throw new \DomainException($motivo);
            }

            // AsignacionDocente no tiene observer de auditoría: se registra a mano
            AuditoriaGeneral::create([
                'auditable_type'  => AsignacionDocente::class,
                'auditable_id'    => $a->id,
                'evento'          => 'deleted',
                'valores_antes'   => [
                    'docente'  => $a->docente?->name,
                    'materia'  => $a->materia?->name,
                    'paralelo' => $a->paralelo?->name,
                    'periodo'  => $a->periodo?->code,
                    'horarios' => $impacto['horarios'],
                    'avisos'   => $impacto['avisos'],
                    'contrato' => $a->file_contrato,
                ],
                'valores_despues' => null,
                'user_id'         => auth()->id(),
                'ip_address'      => request()?->ip(),
            ]);

            $a->delete();
        });
    }
}
