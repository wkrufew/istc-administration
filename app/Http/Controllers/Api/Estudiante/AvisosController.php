<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDocente;
use App\Models\Aviso;
use App\Models\AvisoLectura;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvisosController extends Controller
{
    private function getAsignacionIds(int $userId): \Illuminate\Support\Collection
    {
        $periodoActivo = Periodo::periodoActivoGlobal();
        if (! $periodoActivo) return collect();

        return AsignacionDocente::where('periodo_id', $periodoActivo->id)
            ->whereExists(function ($query) use ($userId, $periodoActivo) {
                $query->select(DB::raw(1))
                    ->from('detalle_matriculas')
                    ->join('matriculas', 'matriculas.id', '=', 'detalle_matriculas.matricula_id')
                    ->whereColumn('detalle_matriculas.materia_id', 'asignacion_docentes.materia_id')
                    ->whereColumn('detalle_matriculas.paralelo_id', 'asignacion_docentes.paralelo_id')
                    ->where('matriculas.user_id', $userId)
                    ->where('matriculas.periodo_id', $periodoActivo->id);
            })
            ->pluck('id');
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $ids    = $this->getAsignacionIds($userId);

        if ($ids->isEmpty()) {
            return response()->json([
                'success' => true,
                'data'    => ['hoy' => [], 'proximo' => [], 'pasado' => [], 'total_no_leidos' => 0, 'total_pasados' => 0],
            ]);
        }

        $withRelations = ['asignacionDocente.materia', 'asignacionDocente.paralelo', 'asignacionDocente.docente'];
        $limitePasados = (int) $request->get('limite_pasados', 10);

        $futuros = Aviso::with($withRelations)
            ->whereIn('asignacion_docente_id', $ids)
            ->withExists(['lecturas as leido' => fn($q) => $q->where('user_id', $userId)])
            ->where('fecha_aviso', '>=', now()->startOfDay())
            ->orderBy('fecha_aviso')
            ->get();

        $pasados = Aviso::with($withRelations)
            ->whereIn('asignacion_docente_id', $ids)
            ->withExists(['lecturas as leido' => fn($q) => $q->where('user_id', $userId)])
            ->where('fecha_aviso', '<', now()->startOfDay())
            ->orderBy('fecha_aviso', 'desc')
            ->limit($limitePasados)
            ->get();

        $formatAviso = fn($a) => [
            'id'          => $a->id,
            'titulo'      => $a->titulo,
            'descripcion' => $a->descripcion,
            'tipo'        => $a->tipo,
            'tipo_label'  => Aviso::tipoLabel($a->tipo),
            'fecha_aviso' => $a->fecha_aviso->format('d/m/Y'),
            'tiempo'      => $a->tiempo_faltante,
            'estado'      => $a->estado,
            'leido'       => (bool) $a->leido,
            'materia'     => $a->asignacionDocente?->materia?->name,
            'paralelo'    => $a->asignacionDocente?->paralelo?->code,
            'docente'     => $a->asignacionDocente?->docente?->name,
        ];

        $totalNoLeidos = Aviso::whereIn('asignacion_docente_id', $ids)
            ->where('fecha_aviso', '>=', now()->startOfDay())
            ->whereDoesntHave('lecturas', fn($q) => $q->where('user_id', $userId))
            ->count();

        $totalPasados = Aviso::whereIn('asignacion_docente_id', $ids)
            ->where('fecha_aviso', '<', now()->startOfDay())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'hoy'             => $futuros->filter(fn($a) => $a->estado === 'hoy')->values()->map($formatAviso),
                'proximo'         => $futuros->filter(fn($a) => $a->estado === 'proximo')->values()->map($formatAviso),
                'pasado'          => $pasados->map($formatAviso),
                'total_no_leidos' => $totalNoLeidos,
                'total_pasados'   => $totalPasados,
            ],
        ]);
    }

    public function marcarLeido(Request $request, int $id)
    {
        $userId = $request->user()->id;
        $ids    = $this->getAsignacionIds($userId);

        // Verificar que el aviso pertenece al período del estudiante
        $aviso = Aviso::whereIn('asignacion_docente_id', $ids)->findOrFail($id);

        AvisoLectura::firstOrCreate(
            ['aviso_id' => $aviso->id, 'user_id' => $userId],
            ['leido_at' => now()]
        );

        return response()->json(['success' => true, 'message' => 'Aviso marcado como leído.']);
    }

    public function marcarTodosLeidos(Request $request)
    {
        $userId = $request->user()->id;
        $ids    = $this->getAsignacionIds($userId);

        if ($ids->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Sin avisos para marcar.']);
        }

        $avisoIds = Aviso::whereIn('asignacion_docente_id', $ids)
            ->where('fecha_aviso', '>=', now()->startOfDay())
            ->pluck('id');

        foreach ($avisoIds as $avisoId) {
            AvisoLectura::firstOrCreate(
                ['aviso_id' => $avisoId, 'user_id' => $userId],
                ['leido_at' => now()]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Todos los avisos marcados como leídos.',
            'data'    => ['marcados' => $avisoIds->count()],
        ]);
    }
}
