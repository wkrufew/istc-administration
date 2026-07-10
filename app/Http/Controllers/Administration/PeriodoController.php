<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CarreraPeriodo;
use App\Models\Periodo;
use Illuminate\Http\Request;
use App\Models\ObligacionesFinanciera;
use Illuminate\Support\Facades\DB;

class PeriodoController extends Controller
{
    public function index()
    {
        $periodos = Periodo::with(['carreras'])->orderByDesc('fecha_inicio')->get();
        return view('administracion.periodos.index', compact('periodos'));
    }

    public function create()
    {
        $periodo = new Periodo();
        return view('administracion.periodos.create', compact('periodo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description'            => 'required|string|max:255',
            'code'                   => 'required|string|max:255|unique:periodos,code',
            'fecha_inicio'           => 'required|date',
            'fecha_fin'              => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_matricula' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_pago'      => 'required|date|after_or_equal:fecha_inicio',
        ]);

        Periodo::create(array_merge(
            $request->only([
                'description', 'code',
                'fecha_inicio', 'fecha_fin',
                'fecha_limite_matricula', 'fecha_limite_pago',
            ]),
            ['nuevo_calculo' => $request->boolean('nuevo_calculo', true)]
        ));

        return redirect()
            ->route('administracion.administrativa.periodos.index')
            ->with('success', 'Periodo creado. Ahora vincúlalo a las carreras correspondientes desde el módulo de Materia-Período-Paralelo.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Periodo $periodo)
    {
        return view('administracion.periodos.edit', compact('periodo'));
    }

    public function update(Request $request, Periodo $periodo)
    {
        $request->validate([
            'description'            => 'required|string|max:255',
            'code'                   => 'required|string|max:255|unique:periodos,code,' . $periodo->id,
            'fecha_inicio'           => 'required|date',
            'fecha_fin'              => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_matricula' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_pago'      => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $periodo->update(array_merge(
            $request->only([
                'description', 'code',
                'fecha_inicio', 'fecha_fin',
                'fecha_limite_matricula', 'fecha_limite_pago',
            ]),
            ['nuevo_calculo' => $request->boolean('nuevo_calculo', true)]
        ));

        return redirect()
            ->route('administracion.administrativa.periodos.index')
            ->with('success', 'Periodo actualizado correctamente.');
    }

    public function destroy(Periodo $periodo)
    {
        $periodo->delete();
        return redirect()
            ->route('administracion.administrativa.periodos.index')
            ->with('success', 'Periodo eliminado.');
    }

    /**
     * Cierra el período para una carrera específica y activa el siguiente.
     * Arrastra las obligaciones COLEGIATURA pendientes al nuevo período.
     */
    public function cerrar(Request $request, Periodo $periodo)
    {
        $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
        ]);

        $carreraId = (int) $request->carrera_id;

        // El pivot debe tener is_current = true para esta carrera
        $cpActual = DB::table('carrera_periodo')
            ->where('carrera_id', $carreraId)
            ->where('periodo_id', $periodo->id)
            ->where('is_current', true)
            ->first();

        if (! $cpActual) {
            return redirect()->back()
                ->with('error', 'Este periodo no está activo para la carrera seleccionada.');
        }

        // Buscar el siguiente período vinculado a la misma carrera
        $cpSiguiente = DB::table('carrera_periodo as cp')
            ->join('periodos as p', 'p.id', '=', 'cp.periodo_id')
            ->where('cp.carrera_id', $carreraId)
            ->where('cp.periodo_id', '!=', $periodo->id)
            ->where('cp.is_current', false)
            ->where('p.fecha_inicio', '>', $periodo->fecha_fin)
            ->orderBy('p.fecha_inicio')
            ->select(
                'cp.*',
                'p.code as p_code',
                'p.fecha_limite_pago as p_fecha_limite_pago'
            )
            ->first();

        if (! $cpSiguiente) {
            return redirect()->back()
                ->with('error', 'No existe un periodo siguiente vinculado a esta carrera. Cree el próximo periodo y vincúlelo primero.');
        }

        DB::beginTransaction();
        try {
            // Fecha límite de pago efectiva del período siguiente
            $fechaLimitePago = $cpSiguiente->fecha_limite_pago ?? $cpSiguiente->p_fecha_limite_pago;
            $codeSiguiente   = $cpSiguiente->p_code;

            // Transferir obligaciones COLEGIATURA pendientes de este período
            $obligacionesPendientes = ObligacionesFinanciera::where('periodo_id', $periodo->id)
                ->where('tipo', 'COLEGIATURA')
                ->where('estado', '!=', 'Pagado')
                ->with('matricula.carrera')
                ->get();

            foreach ($obligacionesPendientes as $obligacion) {
                $saldoPendiente = $obligacion->saldo;
                if ($saldoPendiente <= 0) continue;

                $matricula      = $obligacion->matricula;
                $carrera        = $matricula?->carrera;
                if (! $carrera) continue;

                $semestres        = $carrera->duracion_semestres > 0 ? $carrera->duracion_semestres : 1;
                $nuevaColegiatura = round($carrera->costo_carrera / $semestres, 2);

                ObligacionesFinanciera::create([
                    'user_id'           => $obligacion->user_id,
                    'periodo_id'        => $cpSiguiente->periodo_id,
                    'matricula_id'      => $obligacion->matricula_id,
                    'tipo'              => 'COLEGIATURA',
                    'monto_original'    => $nuevaColegiatura + $saldoPendiente,
                    'descuento'         => 0,
                    'monto_final'       => $nuevaColegiatura + $saldoPendiente,
                    'estado'            => 'Pendiente',
                    'fecha_vencimiento' => $fechaLimitePago,
                    'descripcion'       => "Colegiatura {$codeSiguiente} + \${$saldoPendiente} pendiente de {$periodo->code}",
                ]);

                $obligacion->update(['estado' => 'Vencido']);
            }

            // Desactivar período actual para esta carrera
            DB::table('carrera_periodo')
                ->where('carrera_id', $carreraId)
                ->where('periodo_id', $periodo->id)
                ->update(['is_current' => false]);

            // Activar el siguiente período para esta carrera
            DB::table('carrera_periodo')
                ->where('id', $cpSiguiente->id)
                ->update(['is_current' => true]);

            DB::commit();

            $carrera = Carrera::find($carreraId);
            return redirect()
                ->route('administracion.administrativa.periodos.index')
                ->with('success', "Periodo {$periodo->code} cerrado para {$carrera->name}. Saldos arrastrados al período {$codeSiguiente}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al cerrar el periodo: ' . $e->getMessage());
        }
    }
}
