<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDocente;
use App\Models\Aviso;
use App\Models\DetalleMatricula;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user   = $request->user();
        $userId = $user->id;

        $ultimaMatricula  = Matricula::with(['carrera', 'periodo'])
            ->where('user_id', $userId)
            ->latest()
            ->first();

        $periodoActual = $ultimaMatricula?->carrera?->periodoActual();
        $periodoId     = $periodoActual?->id;

        // ── Deuda período actual ──────────────────────────────────────────
        $deudaActual = 0;
        if ($periodoId) {
            $deudaActual = ObligacionesFinanciera::where('user_id', $userId)
                ->where('periodo_id', $periodoId)
                ->whereIn('estado', ['Pendiente', 'Parcial', 'Vencido'])
                ->withSum(['pagos as pagado' => fn($q) => $q->where('estado', Pago::ESTADO_APROBADO)], 'monto')
                ->get()
                ->sum(fn($ob) => max(0, $ob->monto_final - ($ob->pagado ?? 0)));
        }

        // ── Materias inscritas ────────────────────────────────────────────
        $materiasInscritas = 0;
        if ($periodoId && $ultimaMatricula) {
            $materiasInscritas = DetalleMatricula::where('matricula_id', $ultimaMatricula->id)
                ->where('estado', 'Inscrito')
                ->count();
        }

        // ── Avisos no leídos ──────────────────────────────────────────────
        $avisosSinLeer  = 0;
        $avisosRecientes = [];
        $periodoActivo  = Periodo::periodoActivoGlobal();

        if ($periodoActivo) {
            $asignacionIds = AsignacionDocente::where('periodo_id', $periodoActivo->id)
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

            if ($asignacionIds->isNotEmpty()) {
                $avisosSinLeer = Aviso::whereIn('asignacion_docente_id', $asignacionIds)
                    ->where('fecha_aviso', '>=', now()->startOfDay())
                    ->whereDoesntHave('lecturas', fn($q) => $q->where('user_id', $userId))
                    ->count();

                $avisosRecientes = Aviso::with(['asignacionDocente.materia'])
                    ->whereIn('asignacion_docente_id', $asignacionIds)
                    ->withExists(['lecturas as leido' => fn($q) => $q->where('user_id', $userId)])
                    ->where('fecha_aviso', '>=', now()->startOfDay())
                    ->orderBy('fecha_aviso')
                    ->limit(3)
                    ->get()
                    ->map(fn($a) => [
                        'id'          => $a->id,
                        'titulo'      => $a->titulo,
                        'tipo'        => $a->tipo,
                        'tipo_label'  => Aviso::tipoLabel($a->tipo),
                        'materia'     => $a->asignacionDocente?->materia?->name,
                        'fecha_aviso' => $a->fecha_aviso->format('d/m/Y'),
                        'tiempo'      => $a->tiempo_faltante,
                        'estado'      => $a->estado,
                        'leido'       => (bool) $a->leido,
                    ])
                    ->values();
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'estudiante' => [
                    'nombre'     => $user->name,
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
                    'cedula'     => $user->cedula,
                    'email'      => $user->email,
                ],
                'periodo_actual' => $periodoActual ? [
                    'id'          => $periodoActual->id,
                    'descripcion' => $periodoActual->description,
                    'fecha_inicio'=> $periodoActual->fecha_inicio?->format('d/m/Y'),
                    'fecha_fin'   => $periodoActual->fecha_fin?->format('d/m/Y'),
                ] : null,
                'carrera' => $ultimaMatricula?->carrera ? [
                    'id'     => $ultimaMatricula->carrera->id,
                    'nombre' => $ultimaMatricula->carrera->name,
                    'codigo' => $ultimaMatricula->carrera->code,
                ] : null,
                'materias_inscritas' => $materiasInscritas,
                'deuda_actual'       => round((float) $deudaActual, 2),
                'avisos_sin_leer'    => $avisosSinLeer,
                'avisos_recientes'   => $avisosRecientes,
            ],
        ]);
    }
}
