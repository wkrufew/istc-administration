@extends('pdf.reporte-carrera-materia.layout')

@section('content')
    <div class="card">
        <div class="card-header">
            Reporte por Materia · {{ $data['periodo']->code }}
        </div>
        <div class="card-body">
            <strong>{{ $data['materia']->name }}</strong>

            <table class="stats">
                <tr>
                    <td class="stat-box">
                        <div class="stat-number">{{ $data['total_inscritos'] }}</div>
                        <div class="stat-label">Inscritos</div>
                    </td>
                    <td class="stat-box">
                        <div class="stat-number">{{ $data['total_aprobados'] }}</div>
                        <div class="stat-label">Aprobados</div>
                    </td>
                    <td>
                        <div class="stat-number">{{ $data['total_reprobados'] }}</div>
                        <div class="stat-label">Reprobados</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @foreach ($data['paralelos'] as $par)
        <div class="card">
            <div class="card-header">
                Paralelo {{ $par['paralelo']->name }}
                <span class="badge badge-morado">{{ $par['docente']->name ?? 'Sin docente' }}</span>
            </div>

            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Estudiante</th>
                            <th>Nota</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($par['estudiantes'] as $i => $est)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $est['nombre'] }}</td>
                                <td>{{ $est['nota_final'] ?? '-' }}</td>
                                <td>
                                    <span
                                        class="badge
                            {{ $est['estado_final'] == 'Aprobado'
                                ? 'badge-verde'
                                : ($est['estado_final'] == 'Reprobado'
                                    ? 'badge-naranja'
                                    : 'badge-morado') }}">
                                        {{ $est['estado_final'] ?? 'Pendiente' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
