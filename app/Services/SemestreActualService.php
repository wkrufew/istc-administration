<?php

namespace App\Services;

use App\Models\Carrera;
use App\Models\MateriasArrastrada;
use App\Models\Semestre;
use Illuminate\Support\Facades\DB;

class SemestreActualService
{
    /**
     * Determines which semester to show in the enrollment wizard for a student.
     *
     * A semester is considered "complete" when every one of its materias is either:
     *   - Approved (calificacion.estado_final = 'Aprobado'), OR
     *   - In active arrastre (MateriasArrastrada.estado = 'Arrastrada')
     *
     * Returns null in two safe-fallback cases:
     *   a) The student has prior enrollment records but no calificaciones or arrastres
     *      (was enrolled at a higher semester without grade history in the system).
     *   b) All semesters are already complete.
     * In both cases the caller should show all materias (current behavior).
     */
    public function obtenerSemestreActual(int $userId, int $carreraId): ?Semestre
    {
        $carrera = Carrera::with([
            'semestres' => fn($q) => $q->orderBy('order'),
            'semestres.materias',
        ])->find($carreraId);

        if (! $carrera || $carrera->semestres->isEmpty()) return null;

        $aprobadas = DB::table('calificacions')
            ->join('detalle_matriculas', 'calificacions.detalle_matricula_id', '=', 'detalle_matriculas.id')
            ->join('matriculas', 'detalle_matriculas.matricula_id', '=', 'matriculas.id')
            ->where('matriculas.user_id', $userId)
            ->where('matriculas.carrera_id', $carreraId)
            ->where('calificacions.estado_final', 'Aprobado')
            ->pluck('detalle_matriculas.materia_id')
            ->unique()
            ->all();

        // Scope arrastres to this carrera's materias only, so a student's arrastre in
        // another carrera doesn't interfere with the válvula de escape logic below.
        $carreraMateriaIds = $carrera->semestres
            ->flatMap(fn($s) => $s->materias->pluck('id'))
            ->all();

        $enArrastre = MateriasArrastrada::where('user_id', $userId)
            ->where('estado', 'Arrastrada')
            ->whereIn('materia_id', $carreraMateriaIds)
            ->pluck('materia_id')
            ->all();

        // Válvula de escape: student has prior detalle_matriculas for this carrera
        // but zero calificaciones/arrastres — likely enrolled at a higher semester
        // without grade history. Return null so the wizard shows everything.
        if (empty($aprobadas) && empty($enArrastre)) {
            $tieneHistorial = DB::table('detalle_matriculas')
                ->join('matriculas', 'detalle_matriculas.matricula_id', '=', 'matriculas.id')
                ->where('matriculas.user_id', $userId)
                ->where('matriculas.carrera_id', $carreraId)
                ->whereNull('detalle_matriculas.deleted_at')
                ->whereNull('matriculas.deleted_at')
                ->exists();

            if ($tieneHistorial) {
                return null;
            }
            // No history whatsoever → genuinely new student → fall through to S1
        }

        foreach ($carrera->semestres as $semestre) {
            $ids = $semestre->materias->pluck('id')->all();
            if (empty($ids)) continue;

            $contabilizadas = count(array_filter(
                $ids,
                fn($id) => in_array($id, $aprobadas) || in_array($id, $enArrastre)
            ));

            if ($contabilizadas < count($ids)) {
                return $semestre;
            }
        }

        return null; // All semesters complete
    }
}
