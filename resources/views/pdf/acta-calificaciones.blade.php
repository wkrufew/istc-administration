<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

@page { margin: 10mm 8mm 14mm 8mm; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 7.5px;
    color: #0f172a;
    background: #fff;
}

/* ── CABECERA ─── */
.header-wrap { width: 100%; border-bottom: 3px solid #14451a; margin-bottom: 5px; }
.header-table { width: 100%; border-collapse: collapse; }
.logo-cell { width: 56px; vertical-align: middle; text-align: center; padding-right: 8px; }
.logo-img  { width: 50px; height: 50px; object-fit: contain; }
.inst-cell { vertical-align: middle; padding: 2px 8px; border-left: 1px solid #d1fae5; border-right: 1px solid #d1fae5; }
.inst-name { font-size: 13px; font-weight: bold; color: #14451a; letter-spacing: 0.3px; line-height: 1.3; }
.inst-sub  { font-size: 6.5px; color: #4b7c54; margin-top: 1px; line-height: 1.6; }
.doc-badge-cell { width: 170px; vertical-align: middle; text-align: right; padding-left: 8px; }
.badge-label { font-size: 6px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; }
.badge-value { font-size: 8px; font-weight: bold; color: #14451a; }

/* ── ACTA TITLE ─── */
.acta-title {
    text-align: center;
    font-size: 10px;
    font-weight: bold;
    color: #fff;
    background: #14451a;
    padding: 4px 0;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

/* ── INFO GRID ─── */
.info-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
.info-table td { padding: 2px 4px; font-size: 7px; }
.info-label {
    font-weight: bold;
    color: #334155;
    width: 80px;
    white-space: nowrap;
    background: #f0fdf4;
    border: 1px solid #d1fae5;
    padding: 2px 5px;
}
.info-value {
    color: #1e293b;
    border: 1px solid #e2e8f0;
    padding: 2px 5px;
}
.info-value-bold { font-weight: bold; color: #14451a; }

/* ── TABLA CALIFICACIONES ─── */
.grades-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin-bottom: 8px;
}

/* Group headers */
.gh {
    text-align: center;
    font-size: 6px;
    font-weight: bold;
    padding: 3px 1px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #fff;
}
.gh-main  { background: #14451a; }
.gh-ins   { background: #1d6332; }
.gh-ep    { background: #1e5f8a; }
.gh-ef    { background: #7c3aed; }
.gh-result{ background: #92400e; }
.gh-est   { background: #374151; }

/* Column headers (row 2) */
.ch {
    text-align: center;
    font-size: 5.5px;
    font-weight: bold;
    padding: 2px 1px;
    border: 1px solid #fff;
    line-height: 1.25;
    vertical-align: bottom;
    color: #fff;
}
.ch-main  { background: #166534; }
.ch-ins   { background: #15803d; }
.ch-ep    { background: #1d4ed8; }
.ch-ef    { background: #6d28d9; }
.ch-result{ background: #b45309; }
.ch-est   { background: #4b5563; }

/* Data cells */
.dc {
    text-align: center;
    font-size: 7px;
    padding: 2px 1px;
    border: 1px solid #e2e8f0;
    vertical-align: middle;
}
.dc-left { text-align: left; padding: 2px 3px; }
.dc-num  { font-size: 6.5px; color: #6b7280; }

/* Row alternation */
.row-even { background: #f8fafc; }
.row-odd  { background: #fff; }
.row-borrador { background: #fefce8; }

/* Value formatting */
.val-ok  { color: #15803d; font-weight: bold; }
.val-bad { color: #dc2626; font-weight: bold; }
.val-med { color: #b45309; font-weight: bold; }
.val-emp { color: #9ca3af; }
.val-bold{ font-weight: bold; }
.badge-arr { font-size: 5.5px; background: #fef3c7; color: #92400e; border-radius: 3px; padding: 0px 2px; }
.badge-bor { font-size: 5.5px; background: #fef9c3; color: #a16207; border-radius: 3px; padding: 0px 2px; }

/* Estado chips */
.estado-ap  { color: #15803d; font-weight: bold; font-size: 6.5px; }
.estado-re  { color: #dc2626; font-weight: bold; font-size: 6.5px; }
.estado-in  { color: #b45309; font-weight: bold; font-size: 6.5px; }
.estado-pe  { color: #6b7280; font-size: 6.5px; }

/* ── FIRMA ─── */
.firma-wrap {
    margin-top: 14px;
    width: 100%;
}
.firma-table { width: 100%; border-collapse: collapse; }
.firma-left  { width: 60%; vertical-align: bottom; padding-right: 20px; }
.firma-right { width: 40%; vertical-align: bottom; }

.firma-box {
    border-top: 1.5px solid #1e293b;
    padding-top: 5px;
    text-align: center;
}
.firma-titulo { font-size: 7px; font-weight: bold; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; }
.firma-sub    { font-size: 6.5px; color: #475569; margin-top: 2px; }
.firma-line   { border-top: 1px dashed #94a3b8; margin: 5px auto; width: 80%; }

.legend-box {
    font-size: 6px;
    color: #64748b;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 4px 6px;
    line-height: 1.7;
}
.legend-box b { color: #334155; }

/* Column widths */
.w-num    { width: 18px; }
.w-nom    { width: 108px; }
.w-ins    { width: 23px; }
.w-pi     { width: 27px; }
.w-ex     { width: 22px; }
.w-pct    { width: 26px; }
.w-base   { width: 27px; }
.w-susp   { width: 22px; }
.w-psusp  { width: 26px; }
.w-fin    { width: 27px; }
.w-est    { width: 40px; }
</style>
</head>
<body>

{{-- ══ CABECERA INSTITUCIONAL ══ --}}
<div class="header-wrap">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if ($logo64)
                    <img src="{{ $logo64 }}" class="logo-img">
                @else
                    <div style="width:50px;height:50px;background:#f0fdf4;border-radius:6px;
                                border:1px solid #d1fae5;display:flex;align-items:center;justify-content:center;">
                        <span style="font-size:20px;color:#14451a;">🎓</span>
                    </div>
                @endif
            </td>
            <td class="inst-cell">
                <div class="inst-name">{{ strtoupper($instituto['nombre_largo']) }}</div>
                <div class="inst-sub">
                    @if ($instituto['ruc'])      RUC: {{ $instituto['ruc'] }} &nbsp;·&nbsp; @endif
                    @if ($instituto['senescyt']) SENESCYT: {{ $instituto['senescyt'] }} &nbsp;·&nbsp; @endif
                    @if ($instituto['direccion']) {{ $instituto['direccion'] }} @endif
                    @if ($instituto['telefono']) &nbsp;·&nbsp; Tel: {{ $instituto['telefono'] }} @endif
                </div>
            </td>
            <td class="doc-badge-cell">
                <div class="badge-label">Período académico</div>
                <div class="badge-value">{{ $periodo->code }}</div>
                @if ($periodo->fecha_inicio)
                    <div class="badge-label" style="margin-top:2px;">Vigencia</div>
                    <div style="font-size:6.5px;color:#374151;">
                        {{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d/m/Y') }}
                        — {{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('d/m/Y') }}
                    </div>
                @endif
                <div style="font-size:5.5px;color:#9ca3af;margin-top:2px;">
                    Emitido: {{ now()->format('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ TÍTULO DEL ACTA ══ --}}
<div class="acta-title">ACTA DE CALIFICACIONES</div>

{{-- ══ DATOS DEL MÓDULO ══ --}}
<table class="info-table">
    <tr>
        <td class="info-label">ASIGNATURA</td>
        <td class="info-value info-value-bold" style="width:200px;">
            {{ $materia->name }}
            @if ($materia->code) <span style="font-weight:normal;color:#6b7280;">({{ $materia->code }})</span> @endif
        </td>
        <td class="info-label">DOCENTE</td>
        <td class="info-value info-value-bold" style="width:160px;">{{ $docente->name }}</td>
        <td class="info-label">SEMESTRE</td>
        <td class="info-value" style="width:80px;">{{ $materia->semestre?->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="info-label">CARRERA</td>
        <td class="info-value">{{ $materia->semestre?->carrera?->name ?? '—' }}</td>
        <td class="info-label">PARALELO</td>
        <td class="info-value">{{ $paralelo->name }} ({{ $paralelo->code }})</td>
        <td class="info-label">FECHA</td>
        <td class="info-value">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</td>
    </tr>
    <tr>
        <td class="info-label">FÓRMULA</td>
        <td class="info-value" colspan="5">
            @if ($nuevo_calculo)
                <b>Nuevo cálculo:</b> {{ $pct_pi }}% Promedio Insumos + {{ $pct_ep }}% Examen Parcial + {{ $pct_ef }}% Examen Final
            @else
                <b>Cálculo anterior:</b> {{ $pct_pi }}% Promedio Insumos + {{ $pct_ep }}% Examen Parcial + {{ $pct_ef }}% Examen Final
            @endif
            &nbsp;·&nbsp; Nota mínima de aprobación: <b>{{ number_format($nota_minima, 2) }}</b>
            &nbsp;·&nbsp; Zona de suspenso: <b>{{ number_format($nota_minima - 3, 2) }} – {{ number_format($nota_minima - 0.01, 2) }}</b>
        </td>
    </tr>
</table>

{{-- ══ TABLA DE CALIFICACIONES ══ --}}
@php
    $totalEst     = $filas->count();
    $totalAprobados  = $filas->where('estado', 'Aprobado')->count();
    $totalReprobados = $filas->where('estado', 'Reprobado')->count();
    $totalPendientes = $filas->where('sin_notas', true)->count();
    $hayBorradores   = $filas->where('es_borrador', true)->count() > 0;
@endphp

<table class="grades-table">
    {{-- Row 1: Grupos --}}
    <thead>
    <tr>
        <th class="gh gh-main w-num"  rowspan="2">#</th>
        <th class="gh gh-main w-nom"  rowspan="2">NÓMINA</th>
        {{-- INSUMOS --}}
        <th class="gh gh-ins" colspan="7">INSUMOS ({{ $pct_pi }}%)</th>
        {{-- EXAMEN PARCIAL --}}
        <th class="gh gh-ep"  colspan="2">PARCIAL</th>
        {{-- EXAMEN FINAL --}}
        <th class="gh gh-ef"  colspan="2">FINAL</th>
        {{-- RESULTADO --}}
        <th class="gh gh-result" colspan="4">RESULTADO</th>
        {{-- ESTADO --}}
        <th class="gh gh-est w-est" rowspan="2">ESTADO</th>
    </tr>
    {{-- Row 2: Columnas individuales --}}
    <tr>
        {{-- Insumos --}}
        <th class="ch ch-ins w-ins">ASIST.</th>
        <th class="ch ch-ins w-ins">ACT.<br>AUTÓN.</th>
        <th class="ch ch-ins w-ins">ACT.<br>PRÁCT.</th>
        <th class="ch ch-ins w-ins">ACT.<br>DOC.</th>
        <th class="ch ch-ins w-ins">ÉTICA</th>
        <th class="ch ch-ins w-pi">PROM.<br>INSUM.</th>
        <th class="ch ch-ins w-pct">{{ $pct_pi }}%<br>PI</th>
        {{-- Examen Parcial --}}
        <th class="ch ch-ep w-ex">EX.PAR.<br>/10</th>
        <th class="ch ch-ep w-pct">{{ $pct_ep }}%<br>EP</th>
        {{-- Examen Final --}}
        <th class="ch ch-ef w-ex">EX.FIN.<br>/10</th>
        <th class="ch ch-ef w-pct">{{ $pct_ef }}%<br>EF</th>
        {{-- Resultado --}}
        <th class="ch ch-result w-base">PROM.<br>BASE</th>
        <th class="ch ch-result w-susp">SUSP.<br>/10</th>
        <th class="ch ch-result w-psusp">+SUSP.<br>PTOS</th>
        <th class="ch ch-result w-fin">PROM.<br>FINAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($filas as $f)
        @php
            $rowClass = $f['es_borrador'] ? 'row-borrador' : ($loop->even ? 'row-even' : 'row-odd');

            $estadoClass = match($f['estado']) {
                'Aprobado'   => 'estado-ap',
                'Reprobado'  => 'estado-re',
                'Incompleto' => 'estado-in',
                default      => 'estado-pe',
            };

            $notaFinClass = '';
            if ($f['nota_fin'] !== null) {
                $notaFinClass = $f['nota_fin'] >= $nota_minima ? 'val-ok' : ($f['nota_fin'] >= $nota_minima - 3 ? 'val-med' : 'val-bad');
            }

            $fmt = fn($v) => $v !== null ? number_format($v, 2) : '<span class="val-emp">—</span>';
        @endphp
        <tr class="{{ $rowClass }}">
            <td class="dc dc-num">{{ $f['num'] }}</td>
            <td class="dc dc-left">
                <span style="font-size:7px;font-weight:bold;">{{ $f['nombre'] }}</span><br>
                <span style="font-size:6px;color:#64748b;">{{ $f['cedula'] }}</span>
                @if ($f['tipo'] === 'Arrastre')
                    &nbsp;<span class="badge-arr">ARR.</span>
                @endif
                @if ($f['es_borrador'])
                    &nbsp;<span class="badge-bor">BORRDR.</span>
                @endif
            </td>
            {{-- Insumos --}}
            <td class="dc">{!! $fmt($f['i1']) !!}</td>
            <td class="dc">{!! $fmt($f['i2']) !!}</td>
            <td class="dc">{!! $fmt($f['i3']) !!}</td>
            <td class="dc">{!! $fmt($f['i4']) !!}</td>
            <td class="dc">{!! $fmt($f['i5']) !!}</td>
            <td class="dc val-bold">{!! $fmt($f['prom_ins']) !!}</td>
            <td class="dc" style="background:#eff6ff;">{!! $fmt($f['col_pi']) !!}</td>
            {{-- Parcial --}}
            <td class="dc">{!! $fmt($f['ep']) !!}</td>
            <td class="dc" style="background:#eff6ff;">{!! $fmt($f['col_ep']) !!}</td>
            {{-- Final --}}
            <td class="dc">{!! $fmt($f['ef']) !!}</td>
            <td class="dc" style="background:#eff6ff;">{!! $fmt($f['col_ef']) !!}</td>
            {{-- Resultado --}}
            <td class="dc val-bold">{!! $fmt($f['nota_base']) !!}</td>
            <td class="dc">{!! $fmt($f['nota_susp']) !!}</td>
            <td class="dc" style="color:#7c3aed;">{!! $fmt($f['pct_susp']) !!}</td>
            <td class="dc {{ $notaFinClass }}" style="font-size:8px;">
                {!! $fmt($f['nota_fin']) !!}
            </td>
            <td class="dc {{ $estadoClass }}">{{ $f['estado'] }}</td>
        </tr>
    @endforeach
    </tbody>
    {{-- Totales --}}
    <tfoot>
    <tr style="background:#f0fdf4;">
        <td class="dc" colspan="2" style="text-align:right;font-weight:bold;font-size:7px;color:#14451a;">
            TOTALES: {{ $totalEst }} estudiantes
        </td>
        <td class="dc" colspan="9"></td>
        <td class="dc" colspan="6" style="text-align:center;">
            <span class="val-ok">Aprobados: {{ $totalAprobados }}</span>
            &nbsp;·&nbsp;
            <span class="val-bad">Reprobados: {{ $totalReprobados }}</span>
            @if ($totalPendientes > 0)
                &nbsp;·&nbsp;<span class="val-emp">Sin notas: {{ $totalPendientes }}</span>
            @endif
        </td>
        <td class="dc"></td>
    </tr>
    </tfoot>
</table>

{{-- ══ LEYENDA + FIRMA ══ --}}
<table class="firma-table">
    <tr>
        {{-- Leyenda izquierda --}}
        <td class="firma-left">
            <div class="legend-box">
                <b>Leyenda:</b>
                ARR. = Materia en arrastre &nbsp;·&nbsp;
                BORRDR. = Calificación en borrador (no publicada) &nbsp;·&nbsp;
                — = Sin nota registrada<br>
                ASIST. = Asistencia (Insumo 1) &nbsp;·&nbsp;
                ACT.AUTÓN. = Actividades Autónomas (I2) &nbsp;·&nbsp;
                ACT.PRÁCT. = Actividades Prácticas (I3)<br>
                ACT.DOC. = Actividades con el Docente (I4) &nbsp;·&nbsp;
                ÉTICA = Ética (I5) &nbsp;·&nbsp;
                EX.PAR. = Examen Parcial &nbsp;·&nbsp;
                EX.FIN. = Examen Final<br>
                SUSP. = Nota de Suspenso &nbsp;·&nbsp;
                +SUSP.PTOS = Puntos adicionales otorgados por suspenso
                @if ($hayBorradores)
                    <br><b style="color:#a16207;">⚠ Este acta contiene calificaciones en borrador. Las notas pueden cambiar hasta ser publicadas.</b>
                @endif
            </div>
        </td>

        {{-- Firma derecha --}}
        <td class="firma-right">
            <div style="height: 28px;"></div>
            <div class="firma-box">
                <div class="firma-titulo">Firma del Docente Responsable</div>
                <div class="firma-sub">{{ $docente->name }}</div>
                @if (isset($docente->cedula) && $docente->cedula)
                    <div class="firma-sub">C.C.: {{ $docente->cedula }}</div>
                @endif
                <div class="firma-line"></div>
                @if ($instituto['rector'])
                    <div style="margin-top:8px; padding-top:5px; border-top:1px solid #e2e8f0;">
                        <div class="firma-titulo">Rector / Director Académico</div>
                        <div class="firma-sub">{{ $instituto['rector'] }}</div>
                        <div class="firma-line"></div>
                    </div>
                @endif
            </div>
        </td>
    </tr>
</table>

</body>
</html>
