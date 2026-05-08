@extends('pdf.reporte-carrera-materia.layout')

@section('report-title')
    ACTA CONSOLIDADA DE CALIFICACIONES
    &nbsp;·&nbsp; {{ strtoupper($estudiante->name) }}
    &nbsp;·&nbsp; {{ strtoupper($acta['carrera']->name ?? '') }}
@endsection

@section('content')
@php
    $carrera       = $acta['carrera'];
    $matricula     = $acta['matricula'];
    $pivot         = $acta['periodo_pivot'] ?? null;
    $semestres     = $acta['semestres'];
    $promedioMalla = $acta['promedio_malla'];
    $titulacion    = $acta['titulacion'];
    $practica      = $acta['practica'];
    $comunitaria   = $acta['comunitaria'] ?? null;
    $intentos      = $acta['intentos'];
    $mallaCompleta = $acta['malla_completa'];

    $semConDatos     = collect($semestres)->where('tiene_datos', true)->count();
    $totalSem        = count($semestres);
    $totalAprobadas  = collect($semestres)->sum('aprobadas');
    $totalReprobadas = collect($semestres)->sum('reprobadas');
    $totalMaterias   = collect($semestres)->sum('total_materias');
    $codigoDoc       = 'AC-' . str_pad($estudiante->id, 4, '0', STR_PAD_LEFT) . '-' . now()->format('Ymd');
@endphp

{{-- ── I. DATOS DEL ESTUDIANTE ──────────────────────────────────────────── --}}
<div class="card no-break">
    <div class="card-header">
        <table style="width:100%">
            <tr>
                <td style="color:#fff; font-size:9.5px; font-weight:bold; vertical-align:middle;">
                    I. Datos del Estudiante &nbsp;·&nbsp; Cód.: {{ $codigoDoc }}
                </td>
                <td style="text-align:right; vertical-align:middle;">
                    @if($mallaCompleta)
                        <span class="badge-outline-green" style="font-size:7px;">✓ Malla Completa</span>
                    @else
                        <span class="badge badge-blue" style="font-size:7px; color:#fff;">En Curso</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="card-body">
        <table style="width:100%">
            <tr>
                {{-- Datos personales --}}
                <td style="width:34%; vertical-align:top; padding-right:10px;">
                    <table class="info-grid">
                        <tr>
                            <td class="info-label">Nombres:</td>
                            <td class="info-value" style="font-weight:bold; font-size:9px;">{{ $estudiante->name }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Cédula:</td>
                            <td class="info-value">{{ $estudiante->cedula ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">N° Matrícula:</td>
                            <td class="info-value">{{ $estudiante->matricula_numero ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Correo:</td>
                            <td class="info-value" style="font-size:7.5px;">{{ $estudiante->email }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Teléfono:</td>
                            <td class="info-value">{{ $estudiante->telefono ?? $estudiante->phone ?? '—' }}</td>
                        </tr>
                    </table>
                </td>
                {{-- Datos académicos --}}
                <td style="width:33%; vertical-align:top; border-left:1px solid #e2e8f0; padding:0 10px;">
                    <table class="info-grid">
                        <tr>
                            <td class="info-label">Carrera:</td>
                            <td class="info-value" style="font-weight:bold; font-size:8.5px;">{{ $carrera->name }}</td>
                        </tr>
                        @if($carrera->code)
                        <tr>
                            <td class="info-label">Código:</td>
                            <td class="info-value">{{ $carrera->code }}</td>
                        </tr>
                        @endif
                        @if($carrera->modalidad)
                        <tr>
                            <td class="info-label">Modalidad:</td>
                            <td class="info-value">{{ $carrera->modalidad }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info-label">Duración:</td>
                            <td class="info-value">{{ $carrera->duracion_semestres ?? '—' }} semestres</td>
                        </tr>
                        <tr>
                            <td class="info-label">Estado:</td>
                            <td class="info-value">
                                <span class="badge-outline-green" style="font-size:7px;">{{ $matricula->estado }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">Avance malla:</td>
                            <td class="info-value">{{ $semConDatos }}/{{ $totalSem }} semestres cursados</td>
                        </tr>
                    </table>
                </td>
                {{-- Cohorte y Período --}}
                <td style="width:33%; vertical-align:top; border-left:1px solid #e2e8f0; padding-left:10px;">
                    <div style="font-size:7.5px; font-weight:bold; color:#14451a; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">
                        Cohorte &amp; Período Académico
                    </div>
                    <table class="info-grid">
                        <tr>
                            <td class="info-label">Cohorte:</td>
                            <td class="info-value" style="font-weight:bold;">{{ $matricula->periodo?->code ?? '—' }}</td>
                        </tr>
                        @if($matricula->periodo?->description)
                        <tr>
                            <td class="info-label">Nombre:</td>
                            <td class="info-value">{{ $matricula->periodo->description }}</td>
                        </tr>
                        @endif
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
                        @elseif($matricula->periodo?->fecha_inicio)
                        <tr>
                            <td class="info-label">Período inicial:</td>
                            <td class="info-value">{{ \Carbon\Carbon::parse($matricula->periodo->fecha_inicio)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Período final:</td>
                            <td class="info-value">{{ \Carbon\Carbon::parse($matricula->periodo->fecha_fin)->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                        @if($pivot?->is_current)
                        <tr>
                            <td colspan="2">
                                <span class="badge-outline-green" style="font-size:7px;">Período activo</span>
                            </td>
                        </tr>
                        @endif
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
                    <div class="stat-number stat-number-blue">{{ $totalMaterias }}</div>
                    <div class="stat-label">Total Materias</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-green">
                    <div class="stat-number stat-number-green">{{ $totalAprobadas }}</div>
                    <div class="stat-label">Aprobadas</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-red">
                    <div class="stat-number stat-number-red">{{ $totalReprobadas }}</div>
                    <div class="stat-label">Reprobadas</div>
                </div>
            </td>
            <td>
                <div class="stat-box stat-box-teal">
                    <div class="stat-number stat-number-teal">{{ $semConDatos }}/{{ $totalSem }}</div>
                    <div class="stat-label">Semestres Cursados</div>
                </div>
            </td>
            <td>
                <div class="stat-box {{ $promedioMalla !== null && $promedioMalla >= 7 ? 'stat-box-green' : 'stat-box-amber' }}">
                    <div class="stat-number {{ $promedioMalla !== null && $promedioMalla >= 7 ? 'stat-number-green' : 'stat-number-amber' }}">
                        {{ $promedioMalla !== null ? number_format($promedioMalla, 2) : '—' }}
                    </div>
                    <div class="stat-label">Promedio Malla</div>
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ── II. CALIFICACIONES POR SEMESTRE ─────────────────────────────────── --}}
<div class="seccion-titulo">II. Registro de Calificaciones por Semestre</div>
<div style="margin-bottom:6px;"></div>

@foreach ($semestres as $sem)
<div class="card" style="margin-bottom:6px;">

    {{-- Encabezado del semestre --}}
    <div class="card-header" style="padding:5px 10px;">
        <table style="width:100%;">
            <tr>
                <td style="color:#fff; font-size:9px; font-weight:bold; vertical-align:middle;">
                    {{ strtoupper($sem['semestre_nombre']) }}
                </td>
                <td style="text-align:right; vertical-align:middle;">
                    <span class="badge badge-teal">{{ $sem['total_materias'] }} materias</span>
                    &nbsp;
                    <span class="badge badge-green">{{ $sem['aprobadas'] }} aprob.</span>
                    &nbsp;
                    <span class="badge badge-red">{{ $sem['reprobadas'] }} reprob.</span>
                    @if($sem['promedio'] !== null)
                    &nbsp;
                    <span class="badge" style="background-color:{{ $sem['promedio'] >= 7 ? '#15803d' : '#b91c1c' }}; color:#fff;">
                        Prom. {{ number_format($sem['promedio'], 2) }}
                    </span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Tabla de calificaciones --}}
    <div style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:22%; text-align:left;">Materia</th>
                    <th style="width:4%; text-align:center;">Crd.</th>
                    <th style="width:5%; text-align:center;">Paralelo</th>
                    <th style="width:7%; text-align:center;">Cohorte</th>
                    <th style="width:4%; text-align:center;">Ins.1</th>
                    <th style="width:4%; text-align:center;">Ins.2</th>
                    <th style="width:4%; text-align:center;">Ins.3</th>
                    <th style="width:4%; text-align:center;">Ins.4</th>
                    <th style="width:4%; text-align:center;">Ins.5</th>
                    <th style="width:5%; text-align:center;">Prom.</th>
                    <th style="width:6%; text-align:center;">Ex. Parc.</th>
                    <th style="width:6%; text-align:center;">Ex. Final</th>
                    <th style="width:6%; text-align:center;">Suspenso</th>
                    <th style="width:7%; text-align:center;">Nota Final</th>
                    <th style="width:7%; text-align:center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sem['materias'] as $m)
                <tr>
                    {{-- Materia --}}
                    <td>
                        <span style="font-size:8px; font-weight:600; color:#1e293b; display:block;">{{ $m['materia_nombre'] }}</span>
                        @if($m['materia_code'])
                            <span style="font-size:6.5px; color:#94a3b8;">{{ $m['materia_code'] }}</span>
                        @endif
                        @if($m['es_arrastre'] ?? false)
                            &nbsp;<span class="badge-outline-amber" style="font-size:6.5px;">Arrastre</span>
                        @endif
                    </td>

                    {{-- Créditos --}}
                    <td class="text-center" style="font-weight:bold; color:#1d4ed8;">
                        {{ $m['creditos'] ?? '—' }}
                    </td>

                    {{-- Paralelo --}}
                    <td class="text-center" style="color:#475569; font-size:7.5px;">
                        {{ $m['paralelo'] ?? '—' }}
                    </td>

                    {{-- Cohorte (período del detalle de matrícula) --}}
                    <td class="text-center" style="color:#475569; font-size:7px;">
                        {{ $m['periodo'] ?? '—' }}
                    </td>

                    {{-- Insumos 1–5 --}}
                    @foreach (['insumo1','insumo2','insumo3','insumo4','insumo5'] as $ins)
                    <td class="text-center" style="background-color:#f8fafc;">
                        @if($m[$ins] !== null)
                            <span class="{{ $m[$ins] >= 7 ? 'nota-verde' : 'nota-rojo' }}" style="font-size:7.5px;">
                                {{ number_format($m[$ins], 1) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    @endforeach

                    {{-- Promedio insumos --}}
                    <td class="text-center">
                        @if($m['promedio_insumos'] !== null)
                            <span class="{{ $m['promedio_insumos'] >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                {{ number_format($m['promedio_insumos'], 2) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Examen parcial --}}
                    <td class="text-center">
                        @if($m['examen_parcial'] !== null)
                            <span class="{{ $m['examen_parcial'] >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                {{ number_format($m['examen_parcial'], 2) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Examen final --}}
                    <td class="text-center">
                        @if($m['examen_final'] !== null)
                            <span class="{{ $m['examen_final'] >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                {{ number_format($m['examen_final'], 2) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Nota suspenso --}}
                    <td class="text-center">
                        @if($m['nota_suspenso'] !== null)
                            <span class="nota-amber">{{ number_format($m['nota_suspenso'], 2) }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Nota final --}}
                    <td class="text-center">
                        @if($m['nota_final'] !== null)
                            @php $nf = floatval($m['nota_final']); @endphp
                            <span style="font-size:10px;" class="{{ $nf >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                {{ number_format($nf, 2) }}
                            </span>
                        @else
                            <span class="text-muted small">Pend.</span>
                        @endif
                    </td>

                    {{-- Estado --}}
                    <td class="text-center">
                        @php
                            $estado = $m['estado_final'] ?? null;
                            $badgeClass = match($estado) {
                                'Aprobado'  => 'badge-outline-green',
                                'Reprobado' => 'badge-outline-red',
                                default     => 'badge-outline-gray',
                            };
                        @endphp
                        <span class="{{ $badgeClass }}" style="font-size:6.5px;">
                            {{ $estado ?? 'Pendiente' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

{{-- ── III. PROCESO DE TITULACIÓN Y EGRESO ─────────────────────────────── --}}
<div class="seccion-titulo">III. Proceso de Titulación y Egreso</div>
<div style="margin-bottom:6px;"></div>

<table style="width:100%; border-collapse:separate; border-spacing:5px 0;">
    <tr>
        {{-- Panel izquierdo: Resumen --}}
        <td style="width:60%; vertical-align:top;">
            <div class="card" style="margin-bottom:0;">
                <div class="card-header-light">Resumen de Egreso</div>
                <div class="card-body">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="text-align:center; padding:6px 4px; border-right:1px solid #e2e8f0; width:33%;">
                                <div style="font-size:18px; font-weight:bold; display:block;"
                                    class="{{ $promedioMalla === null ? 'nota-gris' : ($promedioMalla >= 7 ? 'nota-verde' : 'nota-rojo') }}">
                                    {{ $promedioMalla !== null ? number_format($promedioMalla, 2) : '—' }}
                                </div>
                                <div style="font-size:7px; color:#64748b; text-transform:uppercase;">Promedio Malla</div>
                            </td>
                            <td style="text-align:center; padding:6px 4px; border-right:1px solid #e2e8f0; width:33%;">
                                @if($titulacion)
                                    <div style="font-size:18px; font-weight:bold; display:block;"
                                        class="{{ $titulacion->nota_final_egreso >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                        {{ number_format($titulacion->nota_final_egreso, 2) }}
                                    </div>
                                    <div style="font-size:7px; color:#64748b; text-transform:uppercase;">Nota Titulación</div>
                                    @if($titulacion->tipo_titulacion_label ?? null)
                                        <div style="font-size:7px; color:#94a3b8; margin-top:2px;">{{ $titulacion->tipo_titulacion_label }}</div>
                                    @endif
                                @else
                                    <div style="font-size:18px; font-weight:bold;" class="nota-gris">—</div>
                                    <div style="font-size:7px; color:#64748b; text-transform:uppercase;">Titulación Pendiente</div>
                                @endif
                            </td>
                            <td style="text-align:center; padding:6px 4px; width:34%;">
                                @if($titulacion?->estado === 'Aprobado')
                                    <div style="font-size:14px; font-weight:bold;" class="nota-verde">EGRESADO</div>
                                @elseif($titulacion?->estado === 'Reprobado')
                                    <div style="font-size:14px; font-weight:bold;" class="nota-rojo">Reprobado</div>
                                @elseif($mallaCompleta)
                                    <div style="font-size:13px; font-weight:bold;" class="nota-amber">En Titulación</div>
                                @else
                                    <div style="font-size:13px; font-weight:bold; color:#1d4ed8;">En Curso</div>
                                @endif
                                <div style="font-size:7px; color:#64748b; text-transform:uppercase;">Estado Final</div>
                            </td>
                        </tr>
                    </table>

                    {{-- Prácticas preprofesionales --}}
                    @if($practica)
                    <div style="margin-top:8px; padding-top:6px; border-top:1px solid #e2e8f0;">
                        <div style="font-size:7px; font-weight:bold; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">
                            Prácticas Preprofesionales
                        </div>
                        <table style="width:100%;">
                            <tr>
                                <td style="width:45%; font-size:8px;">
                                    <span style="color:#64748b;">Empresa: </span>
                                    <strong>{{ $practica->empresa ?? '—' }}</strong>
                                </td>
                                <td style="width:15%; font-size:8px; text-align:center;">
                                    <span style="color:#64748b;">Horas: </span>
                                    <strong>{{ $practica->total_horas ?? '—' }}</strong>
                                </td>
                                <td style="width:15%; font-size:8px; text-align:center;">
                                    <span style="color:#64748b;">Nota: </span>
                                    <strong>{{ $practica->nota ? number_format($practica->nota, 2) : '—' }}</strong>
                                </td>
                                <td style="width:25%; text-align:right;">
                                    <span class="{{ $practica->estado === 'Completada' ? 'badge-outline-green' : 'badge-outline-gray' }}" style="font-size:7px;">
                                        {{ $practica->estado ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    @endif

                    {{-- Prácticas comunitarias --}}
                    @if($comunitaria)
                    <div style="margin-top:6px; padding-top:6px; border-top:1px dashed #e2e8f0;">
                        <div style="font-size:7px; font-weight:bold; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">
                            Prácticas Comunitarias
                        </div>
                        <table style="width:100%;">
                            <tr>
                                <td style="width:45%; font-size:8px;">
                                    <span style="color:#64748b;">Organización: </span>
                                    <strong>{{ $comunitaria->empresa ?? '—' }}</strong>
                                </td>
                                <td style="width:15%; font-size:8px; text-align:center;">
                                    <span style="color:#64748b;">Horas: </span>
                                    <strong>{{ $comunitaria->total_horas ?? '—' }}</strong>
                                </td>
                                <td style="width:15%; font-size:8px; text-align:center;">
                                    <span style="color:#64748b;">Nota: </span>
                                    <strong>{{ $comunitaria->nota ? number_format($comunitaria->nota, 2) : '—' }}</strong>
                                </td>
                                <td style="width:25%; text-align:right;">
                                    <span class="{{ $comunitaria->estado === 'Completada' ? 'badge-outline-green' : 'badge-outline-gray' }}" style="font-size:7px;">
                                        {{ $comunitaria->estado ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    @endif

                    {{-- Tribunal --}}
                    @if($titulacion?->presidente_tribunal)
                    <div style="margin-top:8px; padding-top:6px; border-top:1px solid #e2e8f0;">
                        <div style="font-size:7px; font-weight:bold; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">
                            Tribunal Evaluador
                        </div>
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td style="text-align:center; padding:4px 6px; border-right:1px solid #e2e8f0;">
                                    <div style="font-size:7px; color:#64748b; text-transform:uppercase;">Presidente</div>
                                    <div style="font-size:8.5px; font-weight:bold; color:#1e293b;">{{ $titulacion->presidente_tribunal }}</div>
                                </td>
                                @if($titulacion->miembro_tribunal_1)
                                <td style="text-align:center; padding:4px 6px; border-right:1px solid #e2e8f0;">
                                    <div style="font-size:7px; color:#64748b; text-transform:uppercase;">Miembro 1</div>
                                    <div style="font-size:8.5px; font-weight:bold; color:#1e293b;">{{ $titulacion->miembro_tribunal_1 }}</div>
                                </td>
                                @endif
                                @if($titulacion->miembro_tribunal_2)
                                <td style="text-align:center; padding:4px 6px;">
                                    <div style="font-size:7px; color:#64748b; text-transform:uppercase;">Miembro 2</div>
                                    <div style="font-size:8.5px; font-weight:bold; color:#1e293b;">{{ $titulacion->miembro_tribunal_2 }}</div>
                                </td>
                                @endif
                            </tr>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </td>

        {{-- Panel derecho: Historial de intentos --}}
        <td style="width:40%; vertical-align:top; padding-left:5px;">
            <div class="card" style="margin-bottom:0;">
                <div class="card-header-light">Historial de Intentos — Titulación</div>
                <div style="padding:0;">
                    @if($intentos->count() > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:8%; text-align:center;">#</th>
                                <th style="width:35%; text-align:left;">Tipo</th>
                                <th style="width:20%; text-align:center;">Fecha</th>
                                <th style="width:18%; text-align:center;">Nota</th>
                                <th style="width:19%; text-align:center;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($intentos as $intento)
                            <tr>
                                <td class="text-center">
                                    <span style="font-weight:bold; font-size:10px;"
                                        class="{{ $intento->estado === 'Aprobado' ? 'nota-verde' : ($intento->estado === 'Reprobado' ? 'nota-rojo' : 'nota-amber') }}">
                                        {{ $intento->numero_intento }}
                                    </span>
                                </td>
                                <td style="font-size:7.5px; color:#475569;">
                                    {{ $intento->tipo_titulacion_label ?? '—' }}
                                </td>
                                <td class="text-center" style="font-size:7.5px; color:#64748b;">
                                    {{ $intento->fecha_evaluacion?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="text-center">
                                    <span style="font-weight:bold; font-size:9px;"
                                        class="{{ $intento->nota_final_egreso >= 7 ? 'nota-verde' : ($intento->nota_final_egreso ? 'nota-rojo' : 'nota-gris') }}">
                                        {{ $intento->nota_final_egreso ? number_format($intento->nota_final_egreso, 2) : '—' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="{{ $intento->estado === 'Aprobado' ? 'badge-outline-green' : ($intento->estado === 'Reprobado' ? 'badge-outline-red' : 'badge-outline-gray') }}"
                                        style="font-size:6.5px;">
                                        {{ $intento->estado }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div style="padding:16px; text-align:center; color:#94a3b8; font-size:8px; font-style:italic;">
                        Sin intentos de titulación registrados
                    </div>
                    @endif
                </div>
            </div>
        </td>
    </tr>
</table>
@endsection

{{-- ── FIRMAS PERSONALIZADAS PARA EL ACTA ─────────────────────────────── --}}
@section('firmas')
@php $carreraFirma = $acta['carrera'] ?? null; @endphp
<div class="signatures-section">
    <div class="signatures-title">Certifican la veracidad del presente documento</div>
    <table class="signatures-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-line-area">
                    <div class="sig-name">{{ \App\Services\SettingService::get('documentos.secretario', '—') }}</div>
                    <div class="sig-role">Secretario/a Académico/a</div>
                </div>
            </td>
            <td class="sig-cell">
                <div class="sig-line-area">
                    <div class="sig-name">{{ \App\Services\SettingService::get('documentos.coordinador', '—') }}</div>
                    <div class="sig-role">
                        Coordinación de Carrera
                        @if($carreraFirma) &nbsp;·&nbsp; {{ $carreraFirma->name }} @endif
                    </div>
                </div>
            </td>
            <td class="sig-cell">
                <div class="sig-line-area">
                    <div class="sig-name">{{ \App\Services\SettingService::get('documentos.rector', '—') }}</div>
                    <div class="sig-role">Rector / Vicerrector</div>
                </div>
            </td>
        </tr>
    </table>
</div>
@endsection
