@extends('pdf.reporte-carrera-materia.layout')

@section('report-title')
    REPORTE DE DISTRIBUCIÓN ACADÉMICA POR CARRERA &nbsp;·&nbsp; {{ strtoupper($data['carrera']->name) }}
    &nbsp;·&nbsp; PERÍODO: {{ $data['periodo']->code }}
@endsection

@section('content')
@php
    $carrera = $data['carrera'];
    $periodo = $data['periodo'];
    $pivot   = $data['periodo_pivot'] ?? null;

    // Paralelos únicos ya calculados en el backend
    $totalParalelos = $data['total_paralelos'] ?? 0;
@endphp

{{-- ── TARJETA DE INFORMACIÓN GENERAL ─────────────────────────────────── --}}
<div class="card no-break">
    <div class="card-header">
        Información General &nbsp;·&nbsp; {{ $carrera->name }}
    </div>
    <div class="card-body">
        <table style="width:100%;">
            <tr>
                <td style="width:48%; vertical-align:top; padding-right:10px;">
                    <table class="info-grid">
                        <tr>
                            <td class="info-label">Carrera:</td>
                            <td class="info-value" style="font-weight:bold; font-size:9px;">{{ $carrera->name }}</td>
                        </tr>
                        @if($carrera->code)
                        <tr>
                            <td class="info-label">Código:</td>
                            <td class="info-value">{{ $carrera->code }}</td>
                        </tr>
                        @endif
                        @if($carrera->description)
                        <tr>
                            <td class="info-label">Descripción:</td>
                            <td class="info-value">{{ $carrera->description }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info-label">Duración:</td>
                            <td class="info-value">{{ $carrera->duracion_semestres }} semestres</td>
                        </tr>
                        <tr>
                            <td class="info-label">Costo/crédito:</td>
                            <td class="info-value">$ {{ number_format($carrera->costo_credito, 2) }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width:27%; vertical-align:top; border-left:1px solid #e2e8f0; padding:0 10px;">
                    <table class="info-grid">
                        <tr>
                            <td class="info-label">Cohorte:</td>
                            <td class="info-value" style="font-weight:bold;">{{ $periodo->code }}</td>
                        </tr>
                        @if($periodo->description)
                        <tr>
                            <td class="info-label">Nombre:</td>
                            <td class="info-value">{{ $periodo->description }}</td>
                        </tr>
                        @endif
                        @if($periodo->fecha_inicio)
                        <tr>
                            <td class="info-label">Inicio de cohorte:</td>
                            <td class="info-value">{{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Fin de cohorte:</td>
                            <td class="info-value">{{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
                <td style="width:25%; vertical-align:top; border-left:1px solid #e2e8f0; padding-left:10px;">
                    <div style="font-size:7.5px; font-weight:bold; color:#14451a; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">
                        Fechas de Período
                    </div>
                    @if($pivot)
                    <table class="info-grid">
                        @if($pivot->fecha_inicio)
                        <tr>
                            <td class="info-label">Inicio:</td>
                            <td class="info-value" style="font-weight:bold; color:#14451a;">
                                {{ \Carbon\Carbon::parse($pivot->fecha_inicio)->format('d/m/Y') }}
                            </td>
                        </tr>
                        @endif
                        @if($pivot->fecha_fin)
                        <tr>
                            <td class="info-label">Fin:</td>
                            <td class="info-value" style="font-weight:bold; color:#14451a;">
                                {{ \Carbon\Carbon::parse($pivot->fecha_fin)->format('d/m/Y') }}
                            </td>
                        </tr>
                        @endif
                        @if($pivot->is_current)
                        <tr>
                            <td colspan="2">
                                <span class="badge-outline-green" style="font-size:7px;">Período activo</span>
                            </td>
                        </tr>
                        @endif
                    </table>
                    @else
                    <span style="font-size:7.5px; color:#94a3b8; font-style:italic;">Sin datos de período</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

{{-- ── ESTADÍSTICAS RESUMEN ─────────────────────────────────────────────── --}}
<div class="stats-wrapper no-break">
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-box stat-box-blue">
                    <div class="stat-number stat-number-blue">{{ $data['total_materias'] }}</div>
                    <div class="stat-label">Materias</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-green">
                    <div class="stat-number stat-number-green">{{ $data['total_estudiantes'] }}</div>
                    <div class="stat-label">Estudiantes</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-purple">
                    <div class="stat-number stat-number-purple">{{ $totalParalelos }}</div>
                    <div class="stat-label">Paralelos activos</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-teal">
                    <div class="stat-number stat-number-teal">{{ $data['total_horas_semana'] }}</div>
                    <div class="stat-label">Horas / Semana</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-amber">
                    <div class="stat-number stat-number-amber">{{ count($data['semestres']) }}</div>
                    <div class="stat-label">Semestres activos</div>
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ── DETALLE POR SEMESTRE ─────────────────────────────────────────────── --}}
@foreach ($data['semestres'] as $sem)
<div class="card">
    <div class="card-header" style="padding: 5px 10px;">
        <table style="width:100%;">
            <tr>
                <td style="color:#fff; font-size:9.5px; font-weight:bold; vertical-align:middle;">
                    {{ strtoupper($sem['semestre']->name) }}
                </td>
                <td style="text-align:right; vertical-align:middle;">
                    <span class="badge badge-teal">{{ $sem['total_materias'] }} mat.</span>
                    &nbsp;
                    <span class="badge" style="background-color:#4ade80; color:#14451a;">{{ $sem['total_horas_semana'] }} h/sem</span>
                </td>
            </tr>
        </table>
    </div>
    <div class="card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:23%;">Materia</th>
                    <th style="width:7%; text-align:center;">Tipo</th>
                    <th style="width:5%; text-align:center;">Créd.</th>
                    <th style="width:20%;">Docente</th>
                    <th style="width:7%; text-align:center;">Paralelo</th>
                    <th style="width:8%; text-align:center;">Inscritos</th>
                    <th style="width:6%; text-align:center;">Cupo</th>
                    <th style="width:14%; text-align:center;">Horario</th>
                    <th style="width:5%; text-align:center;">H/Sem</th>
                    <th style="width:5%; text-align:center;">Prom.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sem['materias'] as $mat)
                    @if ($mat['sin_asignacion'])
                        <tr>
                            <td style="font-weight:bold; color:#14451a;">{{ $mat['materia']->name }}</td>
                            <td colspan="9" style="color:#94a3b8; font-style:italic; text-align:center;">
                                Sin asignación docente en este período
                            </td>
                        </tr>
                    @else
                        @foreach ($mat['paralelos'] as $pidx => $par)
                            <tr>
                                @if ($pidx === 0)
                                    <td rowspan="{{ count($mat['paralelos']) }}"
                                        style="font-weight:bold; color:#14451a; border-right:2px solid #86efac; vertical-align:top; padding-top:5px;">
                                        {{ $mat['materia']->name }}
                                        @if($mat['materia']->code)
                                            <br><span style="font-weight:normal; color:#94a3b8; font-size:7px;">{{ $mat['materia']->code }}</span>
                                        @endif
                                    </td>
                                @endif
                                <td style="text-align:center;">
                                    @if($mat['materia']->tipo)
                                        <span class="badge badge-gray" style="font-size:7px;">{{ $mat['materia']->tipo }}</span>
                                    @endif
                                </td>
                                <td style="text-align:center; font-weight:bold; color:#1d4ed8;">
                                    {{ number_format($mat['materia']->credits, 1) }}
                                </td>
                                <td>{{ $par['docente']->name ?? '—' }}</td>
                                <td style="text-align:center; font-weight:bold; letter-spacing:0.3px;">
                                    {{ $par['paralelo']->code ?? $par['paralelo']->name ?? '—' }}
                                </td>
                                <td style="text-align:center;">
                                    <span style="font-weight:bold;">{{ $par['estudiantes'] }}</span>
                                </td>
                                <td style="text-align:center; color:#64748b;">
                                    {{ $par['cupo_maximo'] ?? '—' }}
                                </td>
                                <td style="text-align:center; line-height:1.6;">
                                    @if($par['horarios']->isNotEmpty())
                                        @foreach($par['horarios']->groupBy('dia_semana') as $dia => $hrs)
                                            <span style="font-size:7px; white-space:nowrap; display:block;">
                                                <span style="font-weight:bold;">{{ substr($dia, 0, 3) }}</span>
                                                {{ \Carbon\Carbon::parse($hrs->first()->hora_inicio)->format('H:i') }}–{{ \Carbon\Carbon::parse($hrs->first()->hora_fin)->format('H:i') }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td style="text-align:center; color:#0f766e; font-weight:bold;">
                                    {{ $par['horas_semana'] }}
                                </td>
                                <td style="text-align:center;">
                                    @if ($par['promedio_grupo'] !== null)
                                        @if ($par['promedio_grupo'] >= 7)
                                            <span class="badge-outline-green">{{ $par['promedio_grupo'] }}</span>
                                        @elseif ($par['promedio_grupo'] >= 4)
                                            <span class="badge-outline-amber">{{ $par['promedio_grupo'] }}</span>
                                        @else
                                            <span class="badge-outline-red">{{ $par['promedio_grupo'] }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

{{-- ── TOTALES GENERALES ────────────────────────────────────────────────── --}}
<div class="card no-break" style="margin-top:4px;">
    <div class="card-header-light" style="font-size:9px;">
        Resumen Global del Período &nbsp;·&nbsp; {{ $periodo->code }}
        @if($pivot && $pivot->fecha_inicio)
            &nbsp;·&nbsp;
            Cohorte: {{ \Carbon\Carbon::parse($pivot->fecha_inicio)->format('d/m/Y') }}
            – {{ \Carbon\Carbon::parse($pivot->fecha_fin)->format('d/m/Y') }}
        @endif
    </div>
    <div class="card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:40%; background-color:#334155;">Indicador</th>
                    <th style="width:20%; background-color:#334155; text-align:center;">Valor</th>
                    <th style="width:40%; background-color:#334155;">Detalle</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bold">Total de materias en oferta</td>
                    <td class="text-center bold" style="color:#1d4ed8; font-size:11px;">{{ $data['total_materias'] }}</td>
                    <td>Materias con al menos un paralelo activo en el período</td>
                </tr>
                <tr>
                    <td class="bold">Total de estudiantes matriculados</td>
                    <td class="text-center bold" style="color:#15803d; font-size:11px;">{{ $data['total_estudiantes'] }}</td>
                    <td>Estudiantes en estado Habilitada por carrera y período</td>
                </tr>
                <tr>
                    <td class="bold">Total de paralelos activos</td>
                    <td class="text-center bold" style="color:#7c3aed; font-size:11px;">{{ $totalParalelos }}</td>
                    <td>Paralelos con asignación docente en el período</td>
                </tr>
                <tr>
                    <td class="bold">Total de horas semanales</td>
                    <td class="text-center bold" style="color:#0f766e; font-size:11px;">{{ $data['total_horas_semana'] }}</td>
                    <td>Suma de horas semanales por materia y paralelo</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
