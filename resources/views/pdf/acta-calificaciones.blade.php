<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

@page { size: A4 landscape; margin: 0; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 8.5px;
    color: #0f172a;
    background: #fff;
    margin: 0; padding: 0;
}

.page-wrap { padding: 5mm; }

/* ── CABECERA ─── */
.header-wrap { width: 100%; border-bottom: 3px solid #14451a; margin-bottom: 5px; }
.header-table { width: 100%; border-collapse: collapse; }
.logo-cell { width: 58px; vertical-align: middle; text-align: center; padding-right: 8px; }
.logo-img  { width: 52px; height: 52px; object-fit: contain; }
.inst-cell { vertical-align: middle; padding: 3px 10px; border-left: 1px solid #d1fae5; border-right: 1px solid #d1fae5; }
.inst-name { font-size: 14px; font-weight: bold; color: #14451a; letter-spacing: 0.3px; line-height: 1.3; }
.inst-sub  { font-size: 7.5px; color: #4b7c54; margin-top: 2px; line-height: 1.6; }
.doc-badge-cell { width: 175px; vertical-align: middle; text-align: right; padding-left: 8px; }
.badge-label { font-size: 7px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; }
.badge-value { font-size: 9px; font-weight: bold; color: #14451a; }

/* ── ACTA TITLE ─── */
.acta-title {
    text-align: center; font-size: 11px; font-weight: bold;
    color: #fff; background: #14451a;
    padding: 5px 0; letter-spacing: 1px; margin-bottom: 5px;
}

/* ── INFO GRID ─── */
.info-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
.info-table td { padding: 3px 5px; font-size: 8px; }
.info-label {
    font-weight: bold; color: #334155; width: 80px;
    white-space: nowrap; background: #f0fdf4;
    border: 1px solid #d1fae5; padding: 3px 6px;
}
.info-value { color: #1e293b; border: 1px solid #e2e8f0; padding: 3px 6px; }
.info-value-bold { font-weight: bold; color: #14451a; }

/* ── TABLA CALIFICACIONES ─── */
.grades-table {
    width: 100%; border-collapse: collapse;
    table-layout: fixed; margin-bottom: 8px;
}

/* Group headers */
.gh {
    text-align: center; font-size: 7px; font-weight: bold;
    padding: 4px 1px; text-transform: uppercase;
    letter-spacing: 0.3px; color: #fff;
}
.gh-main  { background: #14451a; }
.gh-ins   { background: #1d6332; }
.gh-ep    { background: #1e5f8a; }
.gh-ef    { background: #7c3aed; }
.gh-result{ background: #92400e; }
.gh-est   { background: #374151; }

/* Column headers */
.ch {
    text-align: center; font-size: 6.5px; font-weight: bold;
    padding: 3px 1px; border: 1px solid #fff;
    line-height: 1.3; vertical-align: bottom; color: #fff;
}
.ch-sub { font-size: 5.5px; font-weight: normal; opacity: 0.85; display: block; margin-top: 1px; }
.ch-main  { background: #166534; }
.ch-ins   { background: #15803d; }
.ch-ep    { background: #1d4ed8; }
.ch-ef    { background: #6d28d9; }
.ch-result{ background: #b45309; }
.ch-est   { background: #4b5563; }

/* Data cells */
.dc {
    text-align: center; font-size: 8px;
    padding: 2px 1px; border: 1px solid #e2e8f0;
    vertical-align: middle;
}
.dc-left { text-align: left; padding: 3px 4px; }
.dc-num  { font-size: 7.5px; color: #6b7280; }

/* Sub-valor (% aplicado, debajo del número) */
.val-main { font-size: 8.5px; font-weight: bold; line-height: 1.2; }
.val-sub  { font-size: 6.5px; line-height: 1.2; margin-top: 1px; }
.sub-ins  { color: #15803d; }
.sub-ep   { color: #1d4ed8; }
.sub-ef   { color: #6d28d9; }
.sub-susp { color: #b45309; }

/* Row alternation */
.row-even    { background: #f8fafc; }
.row-odd     { background: #fff; }
.row-borrador{ background: #fefce8; }

/* Value formatting */
.val-ok  { color: #15803d; font-weight: bold; }
.val-bad { color: #dc2626; font-weight: bold; }
.val-med { color: #b45309; font-weight: bold; }
.val-emp { color: #9ca3af; }
.val-bold{ font-weight: bold; }
.badge-arr { font-size: 6.5px; background: #fef3c7; color: #92400e; border-radius: 3px; padding: 0px 2px; }
.badge-bor { font-size: 6.5px; background: #fef9c3; color: #a16207; border-radius: 3px; padding: 0px 2px; }

/* Estado chips */
.estado-ap { color: #15803d; font-weight: bold; font-size: 7.5px; }
.estado-re { color: #dc2626; font-weight: bold; font-size: 7.5px; }
.estado-in { color: #b45309; font-weight: bold; font-size: 7.5px; }
.estado-pe { color: #6b7280; font-size: 7.5px; }

/* ── FIRMA ─── */
.firma-table  { width: 100%; border-collapse: collapse; margin-top: 10px; }
.firma-cell   { width: 50%; vertical-align: bottom; padding: 0 20px; text-align: center; }
.firma-space  { height: 4cm; }
.firma-line   { border-top: 1.5px solid #1e293b; margin: 0 auto; width: 80%; }
.firma-titulo { font-size: 8px; font-weight: bold; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 5px; }
.firma-sub    { font-size: 7.5px; color: #475569; margin-top: 2px; }
.legend-box b { color: #334155; }

/* ── ANCHOS DE COLUMNA (14 cols) ─── */
/* 2% + 20% + 5×5% + 8% + 8% + 8% + 7% + 7% + 8% + 7% = 100% */
.w-num  { width: 2%; }
.w-nom  { width: 20%; }
.w-ins  { width: 5%; }
.w-pi   { width: 8%; }   /* PROM.INSUM (con col_pi abajo) */
.w-ex   { width: 8%; }   /* EX.PAR / EX.FIN (con % abajo) */
.w-base { width: 7%; }
.w-susp { width: 7%; }   /* SUSP./10 (con pct abajo) */
.w-fin  { width: 8%; }
.w-est  { width: 7%; }
</style>
</head>
<body>
<div class="page-wrap">

{{-- ══ CABECERA INSTITUCIONAL ══ --}}
<div class="header-wrap">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if ($logo64)
                    <img src="{{ $logo64 }}" class="logo-img">
                @else
                    <div style="width:52px;height:52px;background:#f0fdf4;border-radius:6px;
                                border:1px solid #d1fae5;text-align:center;line-height:52px;font-size:22px;">🎓</div>
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
                    <div style="font-size:7px;color:#374151;">
                        {{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d/m/Y') }}
                        — {{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('d/m/Y') }}
                    </div>
                @endif
                <div style="font-size:6.5px;color:#9ca3af;margin-top:2px;">Emitido: {{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ TÍTULO ══ --}}
<div class="acta-title">ACTA DE CALIFICACIONES</div>

{{-- ══ DATOS DEL MÓDULO ══ --}}
@php
    $creditos = round(($materia->horas_teoricas + $materia->horas_practicas) / 48, 2);
@endphp
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
        <td class="info-label">CRÉDITOS</td>
        <td class="info-value info-value-bold">
            {{ $creditos }} créditos &nbsp;·&nbsp;
            <span style="font-weight:normal;">{{ $materia->horas_teoricas }}h teóricas + {{ $materia->horas_practicas }}h prácticas</span>
        </td>
        <td class="info-label">FÓRMULA</td>
        <td class="info-value" colspan="3">
            {{ $pct_pi }}% Insumos + {{ $pct_ep }}% Parcial + {{ $pct_ef }}% Final
            &nbsp;·&nbsp; Nota mín.: <b>{{ number_format($nota_minima, 2) }}</b>
            &nbsp;·&nbsp; Zona susp.: <b>{{ number_format($nota_minima - 3, 2) }}–{{ number_format($nota_minima - 0.01, 2) }}</b>
        </td>
    </tr>
</table>

{{-- ══ TABLA DE CALIFICACIONES ══ --}}
{{-- 14 columnas: # NOM i1 i2 i3 i4 i5 prom_ins ep ef nota_base nota_susp nota_fin estado --}}
@php
    $totalEst        = $filas->count();
    $totalAprobados  = $filas->where('estado', 'Aprobado')->count();
    $totalReprobados = $filas->where('estado', 'Reprobado')->count();
    $totalPendientes = $filas->where('sin_notas', true)->count();
    $hayBorradores   = $filas->where('es_borrador', true)->count() > 0;
@endphp

<table class="grades-table">
    <thead>
    {{-- Fila 1: grupos --}}
    <tr>
        <th class="gh gh-main w-num"  rowspan="2">#</th>
        <th class="gh gh-main w-nom"  rowspan="2">NÓMINA</th>
        <th class="gh gh-ins" colspan="6">INSUMOS ({{ $pct_pi }}%)</th>
        <th class="gh gh-ep"  colspan="1">PARCIAL</th>
        <th class="gh gh-ef"  colspan="1">FINAL</th>
        <th class="gh gh-result" colspan="3">RESULTADO</th>
        <th class="gh gh-est w-est" rowspan="2">ESTADO</th>
    </tr>
    {{-- Fila 2: columnas individuales --}}
    <tr>
        <th class="ch ch-ins w-ins">ASIST.</th>
        <th class="ch ch-ins w-ins">ACT.<br>AUTÓN.</th>
        <th class="ch ch-ins w-ins">ACT.<br>PRÁCT.</th>
        <th class="ch ch-ins w-ins">ACT.<br>DOC.</th>
        <th class="ch ch-ins w-ins">ÉTICA</th>
        <th class="ch ch-ins w-pi">PROM.<br>INSUM.<span class="ch-sub">({{ $pct_pi }}% PI)</span></th>
        <th class="ch ch-ep w-ex">EX.PAR.<br>/10<span class="ch-sub">({{ $pct_ep }}% EP)</span></th>
        <th class="ch ch-ef w-ex">EX.FIN.<br>/10<span class="ch-sub">({{ $pct_ef }}% EF)</span></th>
        <th class="ch ch-result w-base">PROM.<br>BASE</th>
        <th class="ch ch-result w-susp">SUSP.<br>/10<span class="ch-sub">(ptos extra)</span></th>
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
            {{-- # --}}
            <td class="dc dc-num">{{ $f['num'] }}</td>
            {{-- NÓMINA --}}
            <td class="dc dc-left">
                <span style="font-size:8.5px;font-weight:bold;">{{ $f['nombre'] }}</span><br>
                <span style="font-size:7px;color:#64748b;">{{ $f['cedula'] }}</span>
                @if ($f['tipo'] === 'Arrastre') &nbsp;<span class="badge-arr">ARR.</span> @endif
                @if ($f['es_borrador'])          &nbsp;<span class="badge-bor">BORRDR.</span> @endif
            </td>
            {{-- Insumos i1–i5 --}}
            <td class="dc">{!! $fmt($f['i1']) !!}</td>
            <td class="dc">{!! $fmt($f['i2']) !!}</td>
            <td class="dc">{!! $fmt($f['i3']) !!}</td>
            <td class="dc">{!! $fmt($f['i4']) !!}</td>
            <td class="dc">{!! $fmt($f['i5']) !!}</td>
            {{-- PROM.INSUM + col_pi abajo --}}
            <td class="dc">
                <div class="val-main val-bold">{!! $fmt($f['prom_ins']) !!}</div>
                @if ($f['col_pi'] !== null)
                    <div class="val-sub sub-ins">= {{ number_format($f['col_pi'], 2) }}</div>
                @endif
            </td>
            {{-- EX.PAR + col_ep abajo --}}
            <td class="dc">
                <div class="val-main">{!! $fmt($f['ep']) !!}</div>
                @if ($f['col_ep'] !== null)
                    <div class="val-sub sub-ep">= {{ number_format($f['col_ep'], 2) }}</div>
                @endif
            </td>
            {{-- EX.FIN + col_ef abajo --}}
            <td class="dc">
                <div class="val-main">{!! $fmt($f['ef']) !!}</div>
                @if ($f['col_ef'] !== null)
                    <div class="val-sub sub-ef">= {{ number_format($f['col_ef'], 2) }}</div>
                @endif
            </td>
            {{-- PROM.BASE --}}
            <td class="dc val-bold">{!! $fmt($f['nota_base']) !!}</td>
            {{-- SUSP./10 + pct_susp abajo --}}
            <td class="dc">
                <div class="val-main">{!! $fmt($f['nota_susp']) !!}</div>
                @if ($f['pct_susp'] !== null)
                    <div class="val-sub sub-susp">+{{ number_format($f['pct_susp'], 2) }}</div>
                @endif
            </td>
            {{-- PROM.FINAL --}}
            <td class="dc {{ $notaFinClass }}" style="font-size:9.5px;">
                {!! $fmt($f['nota_fin']) !!}
            </td>
            {{-- ESTADO --}}
            <td class="dc {{ $estadoClass }}">{{ $f['estado'] }}</td>
        </tr>
    @endforeach
    </tbody>
    {{-- Totales (14 cols: 2+5+1+1+1+1+1+1+1 = 14) --}}
    <tfoot>
    <tr style="background:#f0fdf4;">
        <td class="dc" colspan="2" style="text-align:right;font-weight:bold;font-size:8px;color:#14451a;">
            TOTALES: {{ $totalEst }} estudiantes
        </td>
        <td class="dc" colspan="5"></td>
        <td class="dc" colspan="6" style="text-align:center;font-size:8px;">
            <span class="val-ok">✓ Aprobados: {{ $totalAprobados }}</span>
            &nbsp;·&nbsp;
            <span class="val-bad">✗ Reprobados: {{ $totalReprobados }}</span>
            @if ($totalPendientes > 0)
                &nbsp;·&nbsp;<span class="val-emp">Sin notas: {{ $totalPendientes }}</span>
            @endif
        </td>
        <td class="dc"></td>
    </tr>
    </tfoot>
</table>

{{-- ══ FIRMAS ══ --}}
@if ($hayBorradores)
    <div style="font-size:7px;color:#a16207;background:#fef9c3;border:1px solid #fde68a;
                border-radius:4px;padding:4px 7px;margin-bottom:6px;">
        ⚠ Este acta contiene calificaciones en borrador. Las notas pueden cambiar hasta ser publicadas.
    </div>
@endif
<table class="firma-table">
    <tr>
        {{-- Secretaria Académica --}}
        <td class="firma-cell">
            <div class="firma-space"></div>
            <div class="firma-line"></div>
            <div class="firma-titulo">Secretario/a Académico/a</div>
            @if ($instituto['secretario'])
                <div class="firma-sub">{{ $instituto['secretario'] }}</div>
            @endif
        </td>
        {{-- Docente --}}
        <td class="firma-cell">
            <div class="firma-space"></div>
            <div class="firma-line"></div>
            <div class="firma-titulo">Docente Responsable</div>
            <div class="firma-sub">{{ $docente->name }}</div>
            @if (isset($docente->cedula) && $docente->cedula)
                <div class="firma-sub">C.C.: {{ $docente->cedula }}</div>
            @endif
        </td>
    </tr>
</table>

</div>{{-- .page-wrap --}}
</body>
</html>
