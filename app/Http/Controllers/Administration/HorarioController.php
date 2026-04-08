<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDocente;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Paralelo;
use App\Models\Periodo;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        //validar por dia de la semana y ordenar por hora de inicio
        $horarios = Horario::with(['materia', 'paralelo', 'periodo'])->orderBy('materia_id')->paginate(10);
        return view('administracion.horarios.index', compact('horarios'));
    }

    public function create()
    {
        return view('administracion.horarios.create', [
            'materias' => Materia::all(),
            'paralelos' => Paralelo::all(),
            'periodos' => Periodo::all(),
            'asignaciones' => AsignacionDocente::all(),
            'horario' => new Horario()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'aula' => 'nullable|string|max:45',
            'modalidad_clase' => 'required|in:Presencial,Virtual,Híbrida,Semipresencial',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'materia_id' => 'required|exists:materias,id',
            'paralelo_id' => 'required|exists:paralelos,id',
            'periodo_id' => 'required|exists:periodos,id',
            'asignacion_docente_id' => 'required|exists:asignacion_docentes,id',
        ]);

        Horario::create($data);
        return redirect()->route('administracion.administrativa.horarios.index')->with('success', 'Horario creado correctamente.');
    }

    public function edit(Horario $horario)
    {
        return view('administracion.horarios.edit', [
            'horario' => $horario,
            'materias' => Materia::all(),
            'paralelos' => Paralelo::all(),
            'periodos' => Periodo::all(),
            'asignaciones' => AsignacionDocente::all(),
        ]);
    }

    public function update(Request $request, Horario $horario)
    {
        $data = $request->validate([
            'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'aula' => 'nullable|string|max:45',
            'modalidad_clase' => 'required|in:Presencial,Virtual,Híbrida,Semipresencial',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'materia_id' => 'required|exists:materias,id',
            'paralelo_id' => 'required|exists:paralelos,id',
            'periodo_id' => 'required|exists:periodos,id',
            'asignacion_docente_id' => 'required|exists:asignacion_docentes,id',
        ]);

        $horario->update($data);
        return redirect()->route('administracion.administrativa.horarios.index')->with('success', 'Horario actualizado.');
    }

    public function destroy(Horario $horario)
    {
        $horario->delete();
        return redirect()->route('administracion.administrativa.horarios.index')->with('success', 'Horario eliminado.');
    }
}
