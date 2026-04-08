@extends('pdf.reporte-carrera-materia.layout')

@section('content')
    {{-- HEADER REPORTE --}}
    <div class="card">
        <div class="card-header">
            Reporte por Carrera · {{ $data['periodo']->code }}
        </div>
        <div class="card-body">
            <strong>{{ $data['carrera']->name }}</strong>

            <table class="stats">
                <tr>
                    <td class="stat-box">
                        <div class="stat-number">{{ $data['total_materias'] }}</div>
                        <div class="stat-label">Materias</div>
                    </td>
                    <td class="stat-box">
                        <div class="stat-number">{{ $data['total_estudiantes'] }}</div>
                        <div class="stat-label">Estudiantes</div>
                    </td>
                    <td>
                        <div class="stat-number">{{ $data['total_horas_semana'] }}</div>
                        <div class="stat-label">Horas/Sem</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- SEMESTRES --}}
    @foreach ($data['semestres'] as $sem)
        <div class="card">
            <div class="card-header">
                {{ $sem['semestre']->name }}
                <span class="badge badge-naranja">{{ $sem['total_materias'] }} materias</span>
                <span class="badge badge-verde">{{ $sem['total_estudiantes'] }} estudiantes</span>
            </div>

            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Docente</th>
                            <th>Paralelo</th>
                            <th>Estudiantes</th>
                            <th>Horas</th>
                            <th>Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sem['materias'] as $mat)
                            @foreach ($mat['paralelos'] as $par)
                                <tr>
                                    <td>{{ $mat['materia']->name }}</td>
                                    <td>{{ $par['docente']->name ?? '—' }}</td>
                                    <td>{{ $par['paralelo']->name ?? '—' }}</td>
                                    <td>{{ $par['estudiantes'] }}</td>
                                    <td>{{ $par['horas_semana'] }}</td>
                                    <td>
                                        @if ($par['promedio_grupo'])
                                            <span
                                                class="badge {{ $par['promedio_grupo'] >= 7 ? 'badge-verde' : 'badge-naranja' }}">
                                                {{ $par['promedio_grupo'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
