@extends('pdf.reporte-carrera-materia.layout')

@section('report-title')
    REPORTE DE RENDIMIENTO ACADÉMICO POR MATERIA &nbsp;·&nbsp; {{ strtoupper($data['materia']->name) }}
    &nbsp;·&nbsp; PERÍODO: {{ $data['periodo']->code }}
@endsection

@section('content')
@php
    $materia = $data['materia'];
    $periodo = $data['periodo'];
    $pivot   = $data['periodo_pivot'] ?? null;
    $carrera = $materia->semestre?->carrera;

    $pctAprobacion = $data['total_inscritos'] > 0
        ? round(($data['total_aprobados'] / $data['total_inscritos']) * 100, 1)
        : null;

    $docentes = collect($data['paralelos'])->pluck('docente')->filter()->unique('id');
@endphp

{{-- ── TARJETA DE INFORMACIÓN GENERAL ─────────────────────────────────── --}}
<div class="card no-break">
    <div class="card-header">
        Información de la Materia
    </div>
    <div class="card-body">
        <table style="width:100%;">
            <tr>
                {{-- Datos de la materia --}}
                <td style="width:55%; vertical-align:top; padding-right:10px;">
                    <table class="info-grid">
                        <tr>
                            <td class="info-label">Materia:</td>
                            <td class="info-value" style="font-weight:bold; font-size:9px;">{{ $materia->name }}</td>
                        </tr>
                        @if($materia->code)
                        <tr>
                            <td class="info-label">Código:</td>
                            <td class="info-value">{{ $materia->code }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info-label">Créditos:</td>
                            <td class="info-value">
                                <span style="font-weight:bold; color:#1d4ed8;">{{ number_format($materia->credits, 1) }}</span>
                                &nbsp;
                                <span style="color:#64748b; font-size:7.5px;">
                                    ({{ $materia->horas_teoricas ?? 0 }}h teóricas + {{ $materia->horas_practicas ?? 0 }}h prácticas)
                                </span>
                            </td>
                        </tr>
                        @if($materia->tipo)
                        <tr>
                            <td class="info-label">Tipo:</td>
                            <td class="info-value">
                                <span class="badge badge-gray" style="font-size:7.5px;">{{ $materia->tipo }}</span>
                            </td>
                        </tr>
                        @endif
                        @if($carrera)
                        <tr>
                            <td class="info-label">Carrera:</td>
                            <td class="info-value">{{ $carrera->name }}</td>
                        </tr>
                        @endif
                        @if($materia->semestre)
                        <tr>
                            <td class="info-label">Semestre:</td>
                            <td class="info-value">{{ $materia->semestre->name }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
                {{-- Datos del período --}}
                <td style="width:45%; vertical-align:top; border-left:1px solid #e2e8f0; padding-left:10px;">
                    <table class="info-grid">
                        <tr>
                            <td class="info-label">Cohorte:</td>
                            <td class="info-value" style="font-weight:bold;">{{ $periodo->code }}</td>
                        </tr>
                        @if($periodo->description)
                        <tr>
                            <td class="info-label">Descripción:</td>
                            <td class="info-value">{{ $periodo->description }}</td>
                        </tr>
                        @endif
                        {{-- Fechas de cohorte (desde carrera_periodo pivot) --}}
                        @if($pivot && $pivot->fecha_inicio)
                        <tr>
                            <td class="info-label" style="color:#14451a;">Período inicial:</td>
                            <td class="info-value" style="font-weight:bold; color:#14451a;">
                                {{ \Carbon\Carbon::parse($pivot->fecha_inicio)->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label" style="color:#14451a;">Período final:</td>
                            <td class="info-value" style="font-weight:bold; color:#14451a;">
                                {{ \Carbon\Carbon::parse($pivot->fecha_fin)->format('d/m/Y') }}
                            </td>
                        </tr>
                        @elseif($periodo->fecha_inicio)
                        <tr>
                            <td class="info-label">Período inicial:</td>
                            <td class="info-value">{{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Período final:</td>
                            <td class="info-value">{{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info-label">Paralelos:</td>
                            <td class="info-value">{{ count($data['paralelos']) }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Docente(s):</td>
                            <td class="info-value">
                                @foreach($docentes as $doc)
                                    {{ $doc->name }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
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
                    <div class="stat-number stat-number-blue">{{ $data['total_inscritos'] }}</div>
                    <div class="stat-label">Inscritos</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-green">
                    <div class="stat-number stat-number-green">{{ $data['total_aprobados'] }}</div>
                    <div class="stat-label">Aprobados</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-red">
                    <div class="stat-number stat-number-red">{{ $data['total_reprobados'] }}</div>
                    <div class="stat-label">Reprobados</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-purple">
                    @if ($data['promedio_general'] !== null)
                        <div class="stat-number stat-number-purple">{{ number_format($data['promedio_general'], 2) }}</div>
                    @else
                        <div class="stat-number stat-number-purple">—</div>
                    @endif
                    <div class="stat-label">Promedio General</div>
                </div>
            </td>
            <td>
                <div class="stat-box {{ $pctAprobacion !== null && $pctAprobacion >= 70 ? 'stat-box-green' : 'stat-box-amber' }}">
                    @if ($pctAprobacion !== null)
                        <div class="stat-number {{ $pctAprobacion >= 70 ? 'stat-number-green' : 'stat-number-amber' }}">
                            {{ $pctAprobacion }}%
                        </div>
                    @else
                        <div class="stat-number stat-number-amber">—</div>
                    @endif
                    <div class="stat-label">% Aprobación</div>
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ── DETALLE POR PARALELO ─────────────────────────────────────────────── --}}
@if ($data['sin_asignacion'])
    <div class="card">
        <div class="card-body" style="text-align:center; color:#94a3b8; font-style:italic; padding:16px;">
            Esta materia no tiene asignación docente en el período seleccionado.
        </div>
    </div>
@else
    @foreach ($data['paralelos'] as $par)
    @php
        $pctPar = count($par['estudiantes']) > 0
            ? round(($par['aprobados'] / count($par['estudiantes'])) * 100, 1)
            : null;
    @endphp
    <div class="card">
        {{-- Encabezado del paralelo --}}
        <div class="card-header">
            <table style="width:100%;">
                <tr>
                    <td style="color:#fff; font-size:9.5px; font-weight:bold; vertical-align:middle;">
                        Paralelo: {{ $par['paralelo']->code ?? $par['paralelo']->name ?? '—' }}
                        &nbsp;·&nbsp;
                        Docente: {{ $par['docente']->name ?? 'Sin asignar' }}
                    </td>
                    <td style="text-align:right; vertical-align:middle;">
                        <span class="badge badge-teal">{{ $par['inscritos'] }} inscritos</span>
                        &nbsp;
                        <span class="badge badge-green">{{ $par['aprobados'] }} aprobados</span>
                        &nbsp;
                        <span class="badge badge-red">{{ $par['reprobados'] }} reprobados</span>
                        @if($pctPar !== null)
                        &nbsp;
                        <span class="badge {{ $pctPar >= 70 ? 'badge-green' : 'badge-amber' }}">{{ $pctPar }}%</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Horario del paralelo --}}
        @if($par['horarios']->isNotEmpty())
        <div style="background-color:#f8fafc; border-bottom:1px solid #e2e8f0; padding:4px 10px;">
            <span style="font-size:7.5px; color:#64748b; font-weight:bold;">HORARIO:&nbsp;</span>
            @foreach($par['horarios']->groupBy('dia_semana') as $dia => $horas)
                <span style="font-size:7.5px; color:#1e293b;">
                    {{ $dia }}
                    {{ \Carbon\Carbon::parse($horas->first()->hora_inicio)->format('H:i') }}
                    –
                    {{ \Carbon\Carbon::parse($horas->first()->hora_fin)->format('H:i') }}
                </span>
                @if(!$loop->last)&nbsp;&nbsp;·&nbsp;&nbsp;@endif
            @endforeach
        </div>
        @endif

        {{-- Tabla de estudiantes --}}
        @if(count($par['estudiantes']) > 0)
        <div style="padding: 0;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:3%; text-align:center;">#</th>
                        <th style="width:24%;">Estudiante</th>
                        <th style="width:10%;">Cédula</th>
                        <th style="width:6%; text-align:center;">Tipo</th>
                        <th style="width:8%; text-align:center;">Insumos</th>
                        <th style="width:7%; text-align:center;">E. Parcial</th>
                        <th style="width:7%; text-align:center;">E. Final</th>
                        <th style="width:7%; text-align:center;">Suspenso</th>
                        <th style="width:9%; text-align:center;">Nota Final</th>
                        <th style="width:9%; text-align:center;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($par['estudiantes'] as $i => $est)
                    <tr>
                        <td style="text-align:center; color:#94a3b8;">{{ $i + 1 }}</td>
                        <td style="font-weight:bold; color:#1e293b;">{{ $est['nombre'] }}</td>
                        <td style="font-size:7.5px; color:#475569;">{{ $est['cedula'] }}</td>
                        <td style="text-align:center;">
                            @if($est['tipo'] === 'Arrastre')
                                <span class="badge-outline-amber" style="font-size:7px;">Arrastre</span>
                            @else
                                <span class="badge-outline-gray" style="font-size:7px;">Normal</span>
                            @endif
                        </td>
                        <td style="text-align:center; color:#475569;">
                            {{ $est['promedio_insumos'] !== null ? number_format($est['promedio_insumos'], 2) : '—' }}
                        </td>
                        <td style="text-align:center; color:#475569;">
                            {{ $est['examen_parcial'] !== null ? number_format($est['examen_parcial'], 2) : '—' }}
                        </td>
                        <td style="text-align:center; color:#475569;">
                            {{ $est['examen_final'] !== null ? number_format($est['examen_final'], 2) : '—' }}
                        </td>
                        <td style="text-align:center; color:#7c3aed;">
                            {{ $est['nota_suspenso'] !== null ? number_format($est['nota_suspenso'], 2) : '—' }}
                        </td>
                        <td style="text-align:center;">
                            @if ($est['nota_final'] !== null)
                                @php $nf = floatval($est['nota_final']); @endphp
                                <span style="font-size:10px; font-weight:bold;
                                    color: {{ $nf >= 7 ? '#15803d' : ($nf >= 4 ? '#b45309' : '#b91c1c') }};">
                                    {{ number_format($nf, 2) }}
                                </span>
                            @else
                                <span style="color:#94a3b8;">Pendiente</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if ($est['estado_final'] === 'Aprobado')
                                <span class="badge-outline-green">Aprobado</span>
                            @elseif ($est['estado_final'] === 'Reprobado')
                                <span class="badge-outline-red">Reprobado</span>
                            @elseif ($est['estado_final'] === 'Incompleto')
                                <span class="badge-outline-amber">Incompleto</span>
                            @else
                                <span class="badge-outline-gray">{{ $est['estado_final'] ?? 'Pendiente' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right; color:#14451a;">
                            Subtotal paralelo: <strong>{{ $par['inscritos'] }}</strong>
                        </td>
                        <td colspan="4" style="text-align:center; color:#64748b; font-style:italic; font-size:7.5px;">
                            @if($par['promedio'] !== null)
                                Promedio: {{ number_format($par['promedio'], 2) }}
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span class="badge-outline-green">{{ $par['aprobados'] }} apr.</span>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge-outline-red">{{ $par['reprobados'] }} rep.</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="card-body" style="text-align:center; color:#94a3b8; font-style:italic;">
            No hay estudiantes habilitados en este paralelo.
        </div>
        @endif
    </div>
    @endforeach
@endif

{{-- ── CUADRO RESUMEN FINAL ─────────────────────────────────────────────── --}}
@if(!$data['sin_asignacion'] && $data['total_inscritos'] > 0)
<div class="card no-break">
    <div class="card-header-light">
        Cuadro Resumen &nbsp;·&nbsp; {{ $materia->name }} &nbsp;·&nbsp; {{ $periodo->code }}
    </div>
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:20%; background-color:#334155;">Paralelo</th>
                    <th style="width:25%; background-color:#334155;">Docente</th>
                    <th style="width:11%; text-align:center; background-color:#334155;">Inscritos</th>
                    <th style="width:11%; text-align:center; background-color:#334155;">Aprobados</th>
                    <th style="width:11%; text-align:center; background-color:#334155;">Reprobados</th>
                    <th style="width:11%; text-align:center; background-color:#334155;">Promedio</th>
                    <th style="width:11%; text-align:center; background-color:#334155;">% Aprob.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['paralelos'] as $par)
                @php
                    $pPct = count($par['estudiantes']) > 0
                        ? round(($par['aprobados'] / count($par['estudiantes'])) * 100, 1)
                        : null;
                @endphp
                <tr>
                    <td style="font-weight:bold;">{{ $par['paralelo']->code ?? $par['paralelo']->name ?? '—' }}</td>
                    <td>{{ $par['docente']->name ?? '—' }}</td>
                    <td style="text-align:center; font-weight:bold; color:#1d4ed8;">{{ $par['inscritos'] }}</td>
                    <td style="text-align:center; font-weight:bold; color:#15803d;">{{ $par['aprobados'] }}</td>
                    <td style="text-align:center; font-weight:bold; color:#b91c1c;">{{ $par['reprobados'] }}</td>
                    <td style="text-align:center; font-weight:bold; color:#7c3aed;">
                        {{ $par['promedio'] !== null ? number_format($par['promedio'], 2) : '—' }}
                    </td>
                    <td style="text-align:center;">
                        @if ($pPct !== null)
                            <span class="{{ $pPct >= 70 ? 'badge-outline-green' : 'badge-outline-red' }}">
                                {{ $pPct }}%
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align:right; color:#14451a;">TOTAL GENERAL:</td>
                    <td style="text-align:center; font-size:10px; color:#1d4ed8;">{{ $data['total_inscritos'] }}</td>
                    <td style="text-align:center; font-size:10px; color:#15803d;">{{ $data['total_aprobados'] }}</td>
                    <td style="text-align:center; font-size:10px; color:#b91c1c;">{{ $data['total_reprobados'] }}</td>
                    <td style="text-align:center; font-size:10px; color:#7c3aed;">
                        {{ $data['promedio_general'] !== null ? number_format($data['promedio_general'], 2) : '—' }}
                    </td>
                    <td style="text-align:center;">
                        @if($pctAprobacion !== null)
                            <span style="font-size:10px; font-weight:bold;
                                color:{{ $pctAprobacion >= 70 ? '#15803d' : '#b91c1c' }};">
                                {{ $pctAprobacion }}%
                            </span>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

@endsection
