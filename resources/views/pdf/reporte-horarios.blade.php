<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

@page { margin: 12mm 10mm 14mm 10mm; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 8px;
    color: #1e293b;
    background: #fff;
}

/* ── CABECERA INSTITUCIONAL ─── */
.inst-banner {
    background-color: #14451a;
    padding: 7px 10px;
    width: 100%;
}
.inst-banner table { width: 100%; border-collapse: collapse; }
.inst-name  { font-size: 11px; font-weight: bold; color: #fff; line-height: 1.3; }
.inst-detail{ font-size: 6.5px; color: #86efac; margin-top: 2px; line-height: 1.5; }
.inst-badge-label { font-size: 6px; color: #86efac; text-transform: uppercase; letter-spacing: 0.4px; }
.inst-badge-value { font-size: 8px; font-weight: bold; color: #fff; }
.inst-accent-bar { background-color: #22c55e; height: 3px; }

.report-title-bar {
    background-color: #f0fdf4;
    border-bottom: 2px solid #86efac;
    padding: 5px 10px;
}
.report-title {
    font-size: 8.5px;
    font-weight: bold;
    color: #14451a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ── STATS ROW ─── */
.stats-table { width: 100%; border-collapse: collapse; margin: 6px 0; }
.stat-cell {
    text-align: center;
    padding: 5px 4px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background: #f8fafc;
    width: 16.6%;
}
.stat-num  { font-size: 14px; font-weight: bold; }
.stat-label{ font-size: 6px; color: #64748b; margin-top: 1px; }
.stat-blue  { color: #2563eb; }
.stat-indigo{ color: #4f46e5; }
.stat-purple{ color: #7c3aed; }
.stat-green { color: #059669; }
.stat-teal  { color: #0f766e; }
.stat-red   { color: #dc2626; }
.stat-ok    { color: #16a34a; }

/* ── CONFLICTOS ─── */
.conflict-box {
    border: 1px solid #fca5a5;
    background: #fff5f5;
    border-radius: 6px;
    padding: 5px 8px;
    margin-bottom: 7px;
}
.conflict-title { font-size: 7.5px; font-weight: bold; color: #b91c1c; margin-bottom: 3px; }
.conflict-row   { font-size: 6.5px; color: #7f1d1d; margin-bottom: 2px; }
.badge-tipo {
    display: inline-block;
    font-size: 6px;
    font-weight: bold;
    text-transform: uppercase;
    padding: 1px 4px;
    border-radius: 3px;
    margin-right: 3px;
}
.badge-aula    { background: #fef3c7; color: #92400e; }
.badge-docente { background: #fee2e2; color: #991b1b; }
.badge-paralelo{ background: #ffedd5; color: #9a3412; }

.ok-box {
    border: 1px solid #bbf7d0;
    background: #f0fdf4;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 7px;
    color: #15803d;
    font-weight: bold;
    margin-bottom: 7px;
}

/* ── SEPARADOR CARRERA ─── */
.carrera-sep {
    font-size: 7.5px;
    font-weight: bold;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border-bottom: 2px solid #cbd5e1;
    padding-bottom: 3px;
    margin: 10px 0 5px 0;
}

/* ── SEMESTRE HEADER ─── */
.semestre-header {
    background-color: #1e293b;
    color: #fff;
    font-size: 7.5px;
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 5px 5px 0 0;
    width: 100%;
}
.semestre-sub { font-size: 6px; color: #94a3b8; margin-left: 6px; }

/* ── GRILLA DÍAS ─── */
.grid-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    border: 1px solid #e2e8f0;
    border-top: none;
    border-radius: 0 0 5px 5px;
    margin-bottom: 8px;
}
.day-header-cell {
    font-size: 6.5px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 4px 4px 3px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    color: #475569;
    width: 16.6%;
}
.day-header-cell.empty-day { color: #cbd5e1; }
.day-cell {
    vertical-align: top;
    padding: 4px;
    border-right: 1px solid #f1f5f9;
    background: #fff;
}
.day-cell.empty-cell { background: #fafafa; }

/* ── CARD DE CLASE ─── */
.clase-card {
    border: 1px solid #e2e8f0;
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 3px;
    background: #fff;
}
.clase-card.conflicto { border-color: #fca5a5; background: #fff5f5; }
.clase-inner { display: table; width: 100%; }
.clase-bar   { display: table-cell; width: 3px; background: #6b7280; vertical-align: top; }
.clase-body  { display: table-cell; padding: 3px 3px 3px 3px; vertical-align: top; }

.clase-hora     { font-size: 6.5px; font-weight: bold; color: #374151; }
.clase-materia  { font-size: 6.5px; font-weight: bold; color: #111827; margin-top: 1px; line-height: 1.3; }
.clase-code     { font-size: 5.5px; color: #9ca3af; font-family: monospace; }
.clase-paralelo {
    display: inline-block;
    font-size: 5.5px;
    font-weight: bold;
    color: #fff;
    padding: 1px 3px;
    border-radius: 3px;
    margin-top: 1px;
}
.clase-docente  { font-size: 5.5px; color: #6b7280; margin-top: 1px; line-height: 1.3; }
.clase-aula     { font-size: 5.5px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 2px; margin-top: 2px; }
.conflict-badge {
    display: inline-block;
    font-size: 5.5px;
    font-weight: bold;
    color: #fff;
    background: #ef4444;
    padding: 1px 3px;
    border-radius: 3px;
    margin-bottom: 1px;
}
.libre-text { font-size: 6px; color: #d1d5db; text-align: center; padding: 6px 0; }
</style>
</head>
<body>

{{-- ══ CABECERA INSTITUCIONAL ══ --}}
<div class="inst-banner">
    <table>
        <tr>
            <td style="vertical-align: middle; padding-right: 8px; width: 50px;">
                @php
                    $logoPath = public_path('images/logo.png');
                    $logo64   = file_exists($logoPath)
                        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                        : null;
                @endphp
                @if ($logo64)
                    <img src="{{ $logo64 }}" style="width:40px; height:40px; object-fit:contain;">
                @endif
            </td>
            <td style="vertical-align: middle;">
                <div class="inst-name">INSTITUTO SUPERIOR TECNOLÓGICO DEL CANTÓN CUMANDÁ</div>
                <div class="inst-detail">Sistema de Gestión Académica &nbsp;·&nbsp; Reporte de Horarios</div>
            </td>
            <td style="vertical-align: middle; text-align: right; width: 160px;">
                <div class="inst-badge-label">Período</div>
                <div class="inst-badge-value">{{ $periodo?->code ?? '—' }}</div>
                @if ($carrera)
                    <div class="inst-badge-label" style="margin-top:3px;">Carrera</div>
                    <div class="inst-badge-value" style="font-size:7px;">{{ $carrera->name }}</div>
                @endif
                @if ($materia)
                    <div class="inst-badge-label" style="margin-top:3px;">Materia</div>
                    <div class="inst-badge-value" style="font-size:7px;">{{ $materia->name }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>
<div class="inst-accent-bar"></div>

<div class="report-title-bar">
    <span class="report-title">Reporte de Horarios &nbsp;·&nbsp; Período {{ $periodo?->code }}</span>
    @if ($periodo?->description)
        <span style="font-size:7px; color:#4b5563; margin-left:6px;">{{ $periodo->description }}</span>
    @endif
    <span style="float:right; font-size:6.5px; color:#6b7280;">
        Generado: {{ now()->format('d/m/Y H:i') }}
    </span>
</div>

{{-- ══ ESTADÍSTICAS ══ --}}
<table class="stats-table" style="margin-top:6px; margin-bottom:6px;">
    <tr>
        <td class="stat-cell">
            <div class="stat-num stat-blue">{{ $stats['total_clases'] }}</div>
            <div class="stat-label">Clases / semana</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            <div class="stat-num stat-indigo">{{ $stats['docentes_unicos'] }}</div>
            <div class="stat-label">Docentes</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            <div class="stat-num stat-purple">{{ $stats['paralelos_unicos'] }}</div>
            <div class="stat-label">Paralelos</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            <div class="stat-num stat-green">{{ $stats['horas_semana'] }}h</div>
            <div class="stat-label">Horas / semana</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            <div class="stat-num stat-teal">{{ $stats['total_creditos'] }}</div>
            <div class="stat-label">Créditos totales</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            @if ($stats['total_conflictos'] > 0)
                <div class="stat-num stat-red">{{ $stats['total_conflictos'] }}</div>
                <div class="stat-label">Conflictos</div>
            @else
                <div class="stat-num stat-ok">✓</div>
                <div class="stat-label">Sin conflictos</div>
            @endif
        </td>
    </tr>
</table>

{{-- ══ CONFLICTOS ══ --}}
@if (count($conflictos) > 0)
    <div class="conflict-box">
        <div class="conflict-title">⚠ {{ count($conflictos) }} conflicto(s) detectado(s)</div>
        @foreach ($conflictos as $conf)
            @php
                $badgeClass = ['aula' => 'badge-aula', 'docente' => 'badge-docente', 'paralelo' => 'badge-paralelo'][$conf['tipo']] ?? 'badge-aula';
                $tipoLabel  = ['aula' => 'AULA', 'docente' => 'DOC.', 'paralelo' => 'PAR.'][$conf['tipo']] ?? $conf['tipo'];
            @endphp
            <div class="conflict-row">
                <span class="badge-tipo {{ $badgeClass }}">{{ $tipoLabel }}</span>
                <strong>{{ $conf['dia'] }}</strong> · {{ $conf['descripcion'] }} ·
                {{ $conf['horario_a']['materia'] }} ({{ $conf['horario_a']['hora_ini'] }}–{{ $conf['horario_a']['hora_fin'] }})
                <strong>vs</strong>
                {{ $conf['horario_b']['materia'] }} ({{ $conf['horario_b']['hora_ini'] }}–{{ $conf['horario_b']['hora_fin'] }})
            </div>
        @endforeach
    </div>
@else
    <div class="ok-box">✓ Sin conflictos — todos los horarios son compatibles en este período</div>
@endif

{{-- ══ GRILLA POR CARRERA → SEMESTRE ══ --}}
@php
    $paleta = ['#2563eb','#16a34a','#dc2626','#7c3aed','#ea580c','#0891b2','#0f766e','#ca8a04'];
    $conflictIds = collect($conflictos)
        ->flatMap(fn($c) => [$c['horario_a']['id'], $c['horario_b']['id']])
        ->unique()->flip()->toArray();
@endphp

@foreach ($grilla as $carreraData)
    <div class="carrera-sep">{{ $carreraData['nombre'] }}</div>

    @foreach ($carreraData['semestres'] as $semestre)
        @php
            $totalCl = collect($semestre['dias'])->sum(fn($c) => count($c));
            $diasAct = collect($semestre['dias'])->filter(fn($c) => count($c) > 0)->count();
        @endphp
        <div class="semestre-header">
            {{ $semestre['nombre'] }}
            <span class="semestre-sub">{{ $totalCl }} clase(s) · {{ $diasAct }} día(s)</span>
        </div>

        <table class="grid-table">
            {{-- Cabecera días --}}
            <thead>
            <tr>
                @foreach ($dias as $dia)
                    @php $clasesDia = $semestre['dias'][$dia] ?? []; @endphp
                    <th class="day-header-cell {{ count($clasesDia) === 0 ? 'empty-day' : '' }}">
                        {{ $dia }}
                        @if (count($clasesDia) > 0)
                            <br><span style="font-weight:normal; color:#94a3b8; font-size:5.5px;">{{ count($clasesDia) }} clase(s)</span>
                        @endif
                    </th>
                @endforeach
            </tr>
            </thead>
            {{-- Cards --}}
            <tbody>
            <tr>
                @foreach ($dias as $dia)
                    @php $clases = $semestre['dias'][$dia] ?? []; @endphp
                    <td class="day-cell {{ count($clases) === 0 ? 'empty-cell' : '' }}">
                        @if (count($clases) === 0)
                            <div class="libre-text">Libre</div>
                        @else
                            @foreach ($clases as $clase)
                                @php $color = $paleta[($clase['materia_id'] ?? ($clase['id'] ?? 0)) % count($paleta)]; @endphp
                                <div class="clase-card {{ $clase['conflicto'] ? 'conflicto' : '' }}">
                                    <div class="clase-inner">
                                        <div class="clase-bar" style="background-color: {{ $clase['color'] ?? $color }}"></div>
                                        <div class="clase-body">
                                            @if ($clase['conflicto'])
                                                <span class="conflict-badge">⚠ Conflicto</span><br>
                                            @endif
                                            <div class="clase-hora">{{ $clase['hora_inicio'] }} – {{ $clase['hora_fin'] }}</div>
                                            <div class="clase-materia">{{ $clase['materia'] }}</div>
                                            @if ($clase['materia_code'])
                                                <div class="clase-code">{{ $clase['materia_code'] }}</div>
                                            @endif
                                            @if ($clase['paralelo'])
                                                <span class="clase-paralelo" style="background-color: {{ $clase['color'] ?? $color }}">
                                                    {{ $clase['paralelo'] }}
                                                </span>
                                            @endif
                                            @if ($clase['docente'])
                                                <div class="clase-docente">{{ $clase['docente'] }}</div>
                                            @endif
                                            @if ($clase['aula'])
                                                <div class="clase-aula">Aula: {{ $clase['aula'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
    @endforeach
@endforeach

</body>
</html>
