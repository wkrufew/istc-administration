<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Periodo;
use Illuminate\Http\Request;
use App\Models\ObligacionesFinanciera;
use Illuminate\Support\Facades\DB;

class PeriodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $periodos = Periodo::all();
        return view('administracion.periodos.index', compact('periodos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $periodo = new Periodo(['is_active' => true]); // Objeto vacío
        return view('administracion.periodos.create', compact('periodo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:periodos,code',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_matricula' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_pago' => 'required|date|after_or_equal:fecha_inicio',
            'is_current' => 'nullable|boolean',
        ]);

        Periodo::create($request->all());
        return redirect()->route('administracion.administrativa.periodos.index')->with('success', 'Periodo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Periodo $periodo)
    {
        return view('administracion.periodos.edit', compact('periodo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Periodo $periodo)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:periodos,code,' . $periodo->id,
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_matricula' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_pago' => 'required|date|after_or_equal:fecha_inicio',
            'is_current' => 'nullable|boolean',
        ]);

        $periodo->update($request->all());
        return redirect()->route('administracion.administrativa.periodos.index')->with('success', 'Periodo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Periodo $periodo)
    {
        $periodo->delete();
        return redirect()->route('administracion.administrativa.periodos.index')->with('success', 'Periodo eliminado.');
    }

    public function cerrar(Periodo $periodo)
    {
        // Verificar que no esté ya cerrado
        if (! $periodo->is_current) {
            return redirect()->back()->with('error', 'Este periodo ya no es el periodo actual.');
        }

        // Verificar que exista un periodo siguiente al cual arrastrar
        $periodoSiguiente = Periodo::where('id', '!=', $periodo->id)
            ->where('is_current', false)
            ->where('fecha_inicio', '>', $periodo->fecha_fin)
            ->orderBy('fecha_inicio')
            ->first();

        if (! $periodoSiguiente) {
            return redirect()->back()->with('error', 'No existe un periodo siguiente. Cree el próximo periodo antes de cerrar este.');
        }

        DB::beginTransaction();
        try {
            // Buscar obligaciones COLEGIATURA de este periodo con saldo pendiente
            $obligacionesPendientes = ObligacionesFinanciera::where('periodo_id', $periodo->id)
                ->where('tipo', 'COLEGIATURA')
                ->where('estado', '!=', 'Pagado')
                ->with('estudiante.matriculas.carrera')
                ->get();

            foreach ($obligacionesPendientes as $obligacion) {
                $saldoPendiente = $obligacion->saldo;

                if ($saldoPendiente <= 0) continue;

                // Obtener costo del nuevo semestre desde la carrera del estudiante
                $matricula = $obligacion->matricula;
                $carrera   = $matricula->carrera;
                $semestres = $carrera->duracion_semestres > 0 ? $carrera->duracion_semestres : 1;
                $nuevaColegiatura = round($carrera->costo_carrera / $semestres, 2);

                // Crear nueva obligación en el periodo siguiente
                // sumando el saldo pendiente del periodo actual
                ObligacionesFinanciera::create([
                    'user_id'          => $obligacion->user_id,
                    'periodo_id'       => $periodoSiguiente->id,
                    'matricula_id'     => $obligacion->matricula_id,
                    'tipo'             => 'COLEGIATURA',
                    'monto_original'   => $nuevaColegiatura + $saldoPendiente,
                    'descuento'        => 0,
                    'monto_final'      => $nuevaColegiatura + $saldoPendiente,
                    'estado'           => 'Pendiente',
                    'fecha_vencimiento' => $periodoSiguiente->fecha_limite_pago,
                    'descripcion'      => "Colegiatura periodo {$periodoSiguiente->code}"
                        . " + \${$saldoPendiente} pendientes del periodo {$periodo->code}",
                ]);

                // Marcar la obligación anterior como Vencido
                $obligacion->update(['estado' => 'Vencido']);
            }

            // Cerrar el periodo actual
            $periodo->update(['is_current' => false]);

            // Activar el siguiente
            $periodoSiguiente->update(['is_current' => true]);

            DB::commit();

            return redirect()->route('administracion.administrativa.periodos.index')
                ->with('success', "Periodo {$periodo->code} cerrado. Saldos pendientes arrastrados al periodo {$periodoSiguiente->code}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al cerrar el periodo: ' . $e->getMessage());
        }
    }
}
