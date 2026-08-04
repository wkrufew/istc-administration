<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

@page { size: A4 landscape; margin: 0; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 9px;
    color: #1e293b;
    background: #fff;
    margin: 0;
    padding: 0;
}

.page-wrap {
    padding: 10mm 10mm 12mm 10mm;
}

/* ── CABECERA INSTITUCIONAL ─── */
.header-wrap {
    width: 100%;
    border-bottom: 3px solid #14451a;
    margin-bottom: 6px;
}
.header-table { width: 100%; border-collapse: collapse; }
.logo-cell {
    width: 60px;
    vertical-align: middle;
    text-align: center;
    padding-right: 10px;
}
.logo-img { width: 52px; height: 52px; object-fit: contain; }
.inst-cell {
    vertical-align: middle;
    padding: 4px 12px;
    border-left: 1px solid #d1fae5;
    border-right: 1px solid #d1fae5;
}
.inst-name { font-size: 13px; font-weight: bold; color: #14451a; letter-spacing: 0.3px; line-height: 1.3; }
.inst-sub  { font-size: 8px; color: #4b7c54; margin-top: 2px; line-height: 1.6; }
.badge-cell { width: 190px; vertical-align: middle; text-align: right; padding-left: 10px; }
.badge-label { font-size: 7px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; }
.badge-value { font-size: 10px; font-weight: bold; color: #14451a; }

/* ── BARRA DE TÍTULO ─── */
.title-bar {
    background: #f0fdf4;
    border-bottom: 2px solid #86efac;
    padding: 5px 0;
    margin-bottom: 7px;
    width: 100%;
}
.title-bar table { width: 100%; border-collapse: collapse; }
.title-text {
    font-size: 10px;
    font-weight: bold;
    color: #14451a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.title-right { font-size: 8px; color: #6b7280; text-align: right; }

/* ── STATS ─── */
.stats-table { width: 100%; border-collapse: separate; border-spacing: 4px 0; margin-bottom: 7px; }
.stat-cell {
    text-align: center;
    padding: 6px 4px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background: #f8fafc;
    width: 16.6%;
}
.stat-num   { font-size: 16px; font-weight: bold; }
.stat-label { font-size: 7px; color: #64748b; margin-top: 2px; }
.c-blue   { color: #2563eb; }
.c-indigo { color: #4f46e5; }
.c-purple { color: #7c3aed; }
.c-green  { color: #059669; }
.c-teal   { color: #0f766e; }
.c-red    { color: #dc2626; }
.c-ok     { color: #16a34a; }

/* ── CONFLICTOS / OK ─── */
.conflict-box {
    border: 1px solid #fca5a5;
    background: #fff5f5;
    border-radius: 6px;
    padding: 6px 10px;
    margin-bottom: 7px;
}
.conflict-title { font-size: 9px; font-weight: bold; color: #b91c1c; margin-bottom: 3px; }
.conflict-row   { font-size: 8px; color: #7f1d1d; margin-bottom: 2px; line-height: 1.5; }
.badge-tipo {
    display: inline-block; font-size: 7px; font-weight: bold;
    text-transform: uppercase; padding: 1px 4px;
    border-radius: 3px; margin-right: 3px;
}
.badge-aula     { background: #fef3c7; color: #92400e; }
.badge-docente  { background: #fee2e2; color: #991b1b; }
.badge-paralelo { background: #ffedd5; color: #9a3412; }
.ok-box {
    border: 1px solid #bbf7d0; background: #f0fdf4;
    border-radius: 6px; padding: 5px 10px;
    font-size: 8.5px; color: #15803d; font-weight: bold; margin-bottom: 7px;
}

/* ── SEPARADOR CARRERA ─── */
.carrera-sep {
    font-size: 9px; font-weight: bold; color: #334155;
    text-transform: uppercase; letter-spacing: 0.8px;
    border-bottom: 2px solid #cbd5e1;
    padding-bottom: 3px; margin: 10px 0 5px 0;
}

/* ── SEMESTRE ─── */
.semestre-header {
    background-color: #1e293b; color: #fff;
    font-size: 9px; font-weight: bold;
    padding: 5px 10px;
    border-radius: 5px 5px 0 0;
}
.semestre-sub { font-size: 7.5px; color: #94a3b8; margin-left: 8px; }

/* ── TABLA DE GRILLA ─── */
.grid-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    border: 1px solid #e2e8f0;
    border-top: none;
    margin-bottom: 8px;
}
.day-header-cell {
    font-size: 8px; font-weight: bold;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 6px 5px 5px;
    background: #374151; color: #fff;
    border-right: 1px solid #4b5563;
    width: 20%;
}
.day-header-cell.empty-day { background: #f8fafc; color: #cbd5e1; }
.day-count { font-size: 7px; color: #9ca3af; font-weight: normal; display: block; margin-top: 1px; }
.day-cell {
    vertical-align: top;
    padding: 5px;
    border-right: 1px solid #f1f5f9;
    background: #fff;
    width: 20%;
}
.day-cell.empty-cell { background: #fafafa; }
.libre-text { font-size: 8px; color: #d1d5db; text-align: center; padding: 10px 0; }

/* ── TARJETA DE CLASE ─── */
.clase-card {
    border: 1px solid #e2e8f0;
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 4px;
    background: #fff;
}
.clase-card.conflicto { border-color: #fca5a5; background: #fff5f5; }
.clase-inner { display: table; width: 100%; }
.clase-bar   { display: table-cell; width: 4px; vertical-align: top; }
.clase-body  { display: table-cell; padding: 4px 5px 4px 4px; vertical-align: top; }

/* Fila hora + modalidad */
.hora-modal-row { display: table; width: 100%; margin-bottom: 2px; }
.hora-cell  { display: table-cell; vertical-align: middle; }
.modal-cell { display: table-cell; vertical-align: middle; text-align: right; width: 42px; }
.clase-hora {
    font-size: 8px; font-weight: bold; color: #374151;
}
.clase-materia {
    font-size: 8px; font-weight: bold; color: #111827;
    margin-top: 2px; line-height: 1.3;
}
.clase-code { font-size: 7px; color: #9ca3af; font-family: monospace; }
/* Docente */
.clase-docente-wrap { margin-top: 3px; }
.clase-avatar {
    display: inline-block;
    width: 13px; height: 13px;
    border-radius: 7px;
    text-align: center;
    font-size: 7px; font-weight: bold; color: #fff;
    line-height: 13px;
    vertical-align: middle;
    overflow: hidden;
}
.clase-docente {
    display: inline;
    font-size: 7px; color: #6b7280;
    vertical-align: middle;
    margin-left: 3px;
    line-height: 1.3;
}
.clase-footer {
    display: table; width: 100%;
    border-top: 1px solid #f3f4f6;
    margin-top: 3px; padding-top: 3px;
}
.clase-aula          { display: table-cell; font-size: 7px; color: #9ca3af; }
.clase-paralelo-text { display: table-cell; font-size: 7px; font-weight: bold; color: #6b7280; text-align: right; }
.badge-modal {
    display: inline-block; font-size: 6.5px; font-weight: bold;
    color: #fff; padding: 1px 4px; border-radius: 2px;
}
.conflict-badge {
    display: inline-block; font-size: 7px; font-weight: bold;
    color: #fff; background: #ef4444;
    padding: 1px 4px; border-radius: 3px; margin-bottom: 2px;
}
</style>
</head>
<body>
<div class="page-wrap">

{{-- ══ CABECERA ══ --}}
<div class="header-wrap">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if ($logo64)
                    <img src="{{ $logo64 }}" class="logo-img">
                @else
                    <div style="width:48px;height:48px;background:#f0fdf4;border-radius:6px;
                                border:1px solid #d1fae5;text-align:center;line-height:48px;
                                font-size:20px;">🎓</div>
                @endif
            </td>
            <td class="inst-cell">
                <div class="inst-name">{{ strtoupper($instituto['nombre_largo']) }}</div>
                <div class="inst-sub">
                    Sistema de Gestión Académica &nbsp;·&nbsp; Reporte de Horarios
                    @if ($instituto['ruc'])      &nbsp;·&nbsp; RUC: {{ $instituto['ruc'] }} @endif
                    @if ($instituto['senescyt']) &nbsp;·&nbsp; SENESCYT: {{ $instituto['senescyt'] }} @endif
                </div>
            </td>
            <td class="badge-cell">
                <div class="badge-label">Período académico</div>
                <div class="badge-value">{{ $periodo?->code ?? '—' }}</div>
                @if ($carrera)
                    <div class="badge-label" style="margin-top:3px;">Carrera</div>
                    <div class="badge-value" style="font-size:7px;">{{ $carrera->name }}</div>
                @endif
                @if ($materia)
                    <div class="badge-label" style="margin-top:3px;">Materia</div>
                    <div class="badge-value" style="font-size:7px;">{{ $materia->name }}</div>
                @endif
                <div class="badge-label" style="margin-top:3px;">Generado</div>
                <div style="font-size:6.5px;color:#374151;">{{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ BARRA DE TÍTULO ══ --}}
<div class="title-bar">
    <table>
        <tr>
            <td style="vertical-align:middle; padding: 0 2px;">
                <span class="title-text">Reporte de Horarios · Período {{ $periodo?->code }}</span>
                @if ($periodo?->description)
                    <span style="font-size:7px;color:#4b5563;margin-left:6px;">{{ $periodo->description }}</span>
                @endif
            </td>
            <td style="vertical-align:middle; text-align:right; white-space:nowrap; padding: 0 2px; width:120px;">
                <span style="font-size:6.5px;color:#6b7280;">{{ now()->format('d/m/Y H:i') }}</span>
            </td>
        </tr>
    </table>
</div>

{{-- ══ ESTADÍSTICAS ══ --}}
<table class="stats-table">
    <tr>
        <td class="stat-cell">
            <div class="stat-num c-blue">{{ $stats['total_clases'] }}</div>
            <div class="stat-label">Clases / semana</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            <div class="stat-num c-indigo">{{ $stats['docentes_unicos'] }}</div>
            <div class="stat-label">Docentes</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            <div class="stat-num c-purple">{{ $stats['paralelos_unicos'] }}</div>
            <div class="stat-label">Paralelos</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            <div class="stat-num c-green">{{ $stats['horas_semana'] }}h</div>
            <div class="stat-label">Horas / semana</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            <div class="stat-num c-teal">{{ $stats['total_creditos'] }}</div>
            <div class="stat-label">Créditos totales</div>
        </td>
        <td style="width:4px;"></td>
        <td class="stat-cell">
            @if ($stats['total_conflictos'] > 0)
                <div class="stat-num c-red">{{ $stats['total_conflictos'] }}</div>
                <div class="stat-label">Conflictos</div>
            @else
                <div class="stat-num c-ok">✓</div>
                <div class="stat-label">Sin conflictos</div>
            @endif
        </td>
    </tr>
</table>

{{-- ══ PANEL CONFLICTOS ══ --}}
@if (count($conflictos) > 0)
    <div class="conflict-box">
        <div class="conflict-title">⚠ {{ count($conflictos) }} conflicto(s) detectado(s)</div>
        @foreach ($conflictos as $conf)
            @php
                $bClass = ['aula' => 'badge-aula', 'docente' => 'badge-docente', 'paralelo' => 'badge-paralelo'][$conf['tipo']] ?? 'badge-aula';
                $bLabel = ['aula' => 'AULA', 'docente' => 'DOC.', 'paralelo' => 'PAR.'][$conf['tipo']] ?? $conf['tipo'];
            @endphp
            <div class="conflict-row">
                <span class="badge-tipo {{ $bClass }}">{{ $bLabel }}</span>
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
@foreach ($grilla as $carreraData)
    <div class="carrera-sep">{{ $carreraData['nombre'] }}</div>

    @foreach ($carreraData['semestres'] as $semestre)
        @php
            $totalCl = collect($semestre['dias'])->sum(fn($c) => count($c));
            $diasAct = collect($semestre['dias'])->filter(fn($c) => count($c) > 0)->count();
        @endphp
        <div class="semestre-header">
            {{ $semestre['nombre'] }}
            <span class="semestre-sub">{{ $totalCl }} clase(s) · {{ $diasAct }} día(s) activo(s)</span>
        </div>

        <table class="grid-table">
            <thead>
            <tr>
                @foreach ($dias as $dia)
                    @php $clsDia = $semestre['dias'][$dia] ?? []; @endphp
                    <th class="day-header-cell {{ count($clsDia) === 0 ? 'empty-day' : '' }}">
                        {{ $dia }}
                        @if (count($clsDia) > 0)
                            <span class="day-count">{{ count($clsDia) }} clase(s)</span>
                        @endif
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
                @foreach ($dias as $dia)
                    @php $clases = $semestre['dias'][$dia] ?? []; @endphp
                    <td class="day-cell {{ count($clases) === 0 ? 'empty-cell' : '' }}">
                        @if (count($clases) === 0)
                            <div class="libre-text">— Libre —</div>
                        @else
                            @foreach ($clases as $clase)
                                @php
                                    $color = $clase['color'] ?? '#6b7280';
                                    $modalColor = [
                                        'Presencial'     => '#16a34a',
                                        'Virtual'        => '#2563eb',
                                        'Híbrida'        => '#7c3aed',
                                        'Semipresencial' => '#ea580c',
                                    ][$clase['modalidad']] ?? '#6b7280';
                                    $modalLabel = [
                                        'Presencial'     => 'Presencial',
                                        'Virtual'        => 'Virtual',
                                        'Híbrida'        => 'Híbrida',
                                        'Semipresencial' => 'Semi',
                                    ][$clase['modalidad']] ?? ($clase['modalidad'] ?? '');
                                @endphp
                                <div class="clase-card {{ $clase['conflicto'] ? 'conflicto' : '' }}">
                                    <div class="clase-inner">
                                        <div class="clase-bar" style="background-color: {{ $color }}"></div>
                                        <div class="clase-body">
                                            @if ($clase['conflicto'])
                                                <span class="conflict-badge">⚠ Conflicto</span><br>
                                            @endif

                                            {{-- Hora (izq) + Modalidad (der) en una sola línea --}}
                                            <div class="hora-modal-row">
                                                <div class="hora-cell">
                                                    <span class="clase-hora">{{ $clase['hora_inicio'] }} – {{ $clase['hora_fin'] }}</span>
                                                </div>
                                                @if ($modalLabel)
                                                    <div class="modal-cell">
                                                        <span class="badge-modal" style="background-color: {{ $modalColor }}">{{ $modalLabel }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="clase-materia">{{ $clase['materia'] }}</div>
                                            @if ($clase['materia_code'])
                                                <div class="clase-code">{{ $clase['materia_code'] }}</div>
                                            @endif

                                            {{-- Docente con avatar circular --}}
                                            @if ($clase['docente'])
                                                <div class="clase-docente-wrap">
                                                    <span class="clase-avatar" style="background-color: {{ $color }}">{{ strtoupper(substr($clase['docente'], 0, 1)) }}</span>
                                                    <span class="clase-docente">{{ $clase['docente'] }}</span>
                                                </div>
                                            @endif

                                            @if ($clase['aula'] || $clase['paralelo'])
                                                <div class="clase-footer">
                                                    <div class="clase-aula">{{ $clase['aula'] ? 'Aula ' . $clase['aula'] : '' }}</div>
                                                    <div class="clase-paralelo-text">{{ $clase['paralelo'] ?? '' }}</div>
                                                </div>
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

</div>{{-- .page-wrap --}}
</body>
</html>
