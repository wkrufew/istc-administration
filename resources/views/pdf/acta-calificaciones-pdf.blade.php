<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Acta de Calificaciones - {{ $estudiante->name }}</title>
    <style>
        @page {
            margin: 10mm 13mm 10mm 13mm;
        }

        html,
        body {
            /* margin: 0;
            padding: 0; */
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a2e;
            background: #ffffff;
        }

        * {
            padding: 0;
            box-sizing: border-box;
            /* SIN margin: 0 aquí */
        }

        /* body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a2e;
            background: #ffffff;
            margin: 0;
        } */

        /* ================================================================
           HEADER INSTITUCIONAL
           ================================================================ */
        .header {
            width: 100%;
            border-bottom: 3px solid #1a3a6b;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-inner {
            width: 100%;
        }

        .header-logo-cell {
            width: 80px;
            vertical-align: middle;
            text-align: center;
        }

        .header-logo-cell img {
            width: 68px;
            height: auto;
        }

        .header-title-cell {
            vertical-align: middle;
            text-align: center;
            padding: 0 10px;
        }

        .header-right-cell {
            width: 80px;
            vertical-align: middle;
            text-align: right;
        }

        .inst-nombre {
            font-size: 13px;
            font-weight: bold;
            color: #1a3a6b;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .inst-subtitulo {
            font-size: 9px;
            color: #4a5568;
            margin-top: 2px;
        }

        .doc-titulo {
            font-size: 11px;
            font-weight: bold;
            color: #1a3a6b;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-subtitulo {
            font-size: 8px;
            color: #718096;
            margin-top: 2px;
        }

        .codigo-doc {
            font-size: 7.5px;
            color: #718096;
            text-align: right;
        }

        /* ================================================================
           SECCIÓN DE DATOS DEL ESTUDIANTE
           ================================================================ */
        .seccion-titulo {
            background-color: #1a3a6b;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            padding: 4px 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 0;
        }

        .datos-estudiante {
            width: 100%;
            border: 1px solid #cbd5e0;
            border-top: none;
            margin-bottom: 12px;
        }

        .datos-estudiante td {
            padding: 5px 8px;
            border-right: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .datos-estudiante td:last-child {
            border-right: none;
        }

        .dato-label {
            font-size: 7px;
            color: #718096;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        .dato-valor {
            font-size: 9px;
            color: #1a202c;
            font-weight: bold;
        }

        /* ================================================================
           STATS RESUMEN
           ================================================================ */
        .stats-row {
            width: 100%;
            margin-bottom: 12px;
            border: 1px solid #cbd5e0;
        }

        .stats-row td {
            text-align: center;
            padding: 6px 4px;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .stats-row td:last-child {
            border-right: none;
        }

        .stat-numero {
            font-size: 14px;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            font-size: 7px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stat-azul {
            color: #2b6cb0;
        }

        .stat-verde {
            color: #276749;
        }

        .stat-rojo {
            color: #c53030;
        }

        .stat-gris {
            color: #4a5568;
        }

        .stat-amber {
            color: #b7791f;
        }

        .bg-azul-claro {
            background-color: #ebf8ff;
        }

        .bg-verde-claro {
            background-color: #f0fff4;
        }

        .bg-rojo-claro {
            background-color: #fff5f5;
        }

        .bg-gris-claro {
            background-color: #f7fafc;
        }

        .bg-amber-claro {
            background-color: #fffbeb;
        }

        /* ================================================================
           TABLAS DE CALIFICACIONES POR SEMESTRE
           ================================================================ */
        .semestre-bloque {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .semestre-header {
            width: 100%;
        }

        .semestre-header td {
            padding: 5px 8px;
            vertical-align: middle;
        }

        .semestre-titulo-cell {
            background-color: #2d5299;
        }

        .semestre-titulo {
            font-size: 9px;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .semestre-meta-cell {
            background-color: #3a63a8;
            text-align: right;
            width: 250px;
        }

        .semestre-meta {
            font-size: 7.5px;
            color: #bee3f8;
        }

        .semestre-promedio-cell {
            background-color: #1a3a6b;
            text-align: center;
            width: 80px;
        }

        .semestre-promedio-valor {
            font-size: 13px;
            font-weight: bold;
            display: block;
        }

        .semestre-promedio-label {
            font-size: 6.5px;
            color: #bee3f8;
            text-transform: uppercase;
        }

        /* Tabla de materias */
        .tabla-materias {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e0;
            border-top: none;
            font-size: 8px;
        }

        .tabla-materias thead tr {
            background-color: #edf2f7;
        }

        .tabla-materias thead th {
            padding: 4px 5px;
            text-align: center;
            font-size: 7px;
            font-weight: bold;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-right: 1px solid #cbd5e0;
            border-bottom: 1px solid #cbd5e0;
        }

        .tabla-materias thead th.th-left {
            text-align: left;
        }

        .tabla-materias tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .tabla-materias tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .tabla-materias tbody tr:hover {
            background-color: #ebf8ff;
        }

        .tabla-materias tbody td {
            padding: 4px 5px;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .td-materia-nombre {
            font-size: 8px;
            font-weight: 600;
            color: #1a202c;
        }

        .td-materia-code {
            font-size: 6.5px;
            color: #718096;
        }

        .td-center {
            text-align: center;
        }

        .td-nota {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        /* Badges de estado */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-aprobado {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .badge-reprobado {
            background-color: #fed7d7;
            color: #742a2a;
        }

        .badge-suspenso {
            background-color: #feebc8;
            color: #7b341e;
        }

        .badge-pendiente {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        .badge-arrastre {
            background-color: #e9d8fd;
            color: #44337a;
        }

        .nota-verde {
            color: #276749;
        }

        .nota-rojo {
            color: #c53030;
        }

        .nota-gris {
            color: #a0aec0;
        }

        .nota-amber {
            color: #b7791f;
        }

        .sin-dato {
            color: #cbd5e0;
            font-size: 7.5px;
        }

        .col-insumo {
            background-color: #f7fafc;
            font-size: 8px;
            text-align: center;
        }

        /* ================================================================
           SECCIÓN TITULACIÓN
           ================================================================ */
        .titulacion-grid {
            width: 100%;
            margin-bottom: 12px;
        }

        .titulacion-grid td {
            vertical-align: top;
            padding-right: 8px;
        }

        .titulacion-grid td:last-child {
            padding-right: 0;
        }

        .card-titulacion {
            border: 1px solid #cbd5e0;
            margin-bottom: 0;
        }

        .card-titulo {
            background-color: #2d3748;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            padding: 4px 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-body {
            padding: 8px;
        }

        .egreso-stats {
            width: 100%;
        }

        .egreso-stats td {
            text-align: center;
            padding: 6px 4px;
            border-right: 1px solid #e2e8f0;
        }

        .egreso-stats td:last-child {
            border-right: none;
        }

        .egreso-numero {
            font-size: 18px;
            font-weight: bold;
            display: block;
        }

        .egreso-label {
            font-size: 7px;
            color: #718096;
            text-transform: uppercase;
        }

        /* Tribunal */
        .tribunal-row {
            width: 100%;
            margin-top: 8px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }

        .tribunal-row td {
            padding: 4px 6px;
            border-right: 1px solid #e2e8f0;
            text-align: center;
        }

        .tribunal-row td:last-child {
            border-right: none;
        }

        .tribunal-cargo {
            font-size: 7px;
            color: #718096;
            text-transform: uppercase;
        }

        .tribunal-nombre {
            font-size: 8.5px;
            font-weight: bold;
            color: #1a202c;
        }

        /* Historial intentos */
        .intentos-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .intentos-table thead th {
            background-color: #edf2f7;
            padding: 4px 6px;
            text-align: center;
            font-size: 7px;
            font-weight: bold;
            color: #4a5568;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e0;
            border-right: 1px solid #cbd5e0;
        }

        .intentos-table tbody td {
            padding: 4px 6px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }

        .intento-numero {
            font-weight: bold;
            font-size: 10px;
        }

        /* ================================================================
           FIRMA Y PIE DE PÁGINA
           ================================================================ */
        .seccion-firmas {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .firmas-table {
            width: 100%;
        }

        .firmas-table td {
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }

        .linea-firma {
            border-top: 1px solid #1a3a6b;
            padding-top: 4px;
            margin-top: 30px;
        }

        .firma-nombre {
            font-size: 8.5px;
            font-weight: bold;
            color: #1a202c;
        }

        .firma-cargo {
            font-size: 7.5px;
            color: #718096;
        }

        .footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            width: 100%;
        }

        .footer td {
            font-size: 7px;
            color: #a0aec0;
            vertical-align: middle;
        }

        .footer-derecha {
            text-align: right;
        }

        /* ================================================================
           ESTADO MALLA
           ================================================================ */
        .estado-malla-aprobado {
            background-color: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            display: inline-block;
        }

        .estado-malla-curso {
            background-color: #bee3f8;
            color: #1a365d;
            border: 1px solid #90cdf4;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            display: inline-block;
        }

        /* Separador de sección */
        .page-break {
            page-break-after: always;
        }

        .barra-progreso-outer {
            width: 100%;
            background-color: #e2e8f0;
            border-radius: 3px;
            height: 6px;
            margin-top: 3px;
        }

        .barra-progreso-inner {
            height: 6px;
            border-radius: 3px;
            background-color: #3182ce;
        }
    </style>
</head>

<body>

    @php
        $carrera = $acta['carrera'];
        $matricula = $acta['matricula'];
        $semestres = $acta['semestres'];
        $promedioMalla = $acta['promedio_malla'];
        $titulacion = $acta['titulacion'];
        $practica = $acta['practica'];
        $intentos = $acta['intentos'];
        $mallaCompleta = $acta['malla_completa'];

        $semConDatos = collect($semestres)->where('tiene_datos', true)->count();
        $totalSem = count($semestres);
        $pct = $totalSem > 0 ? round(($semConDatos / $totalSem) * 100) : 0;
        $totalAprobadas = collect($semestres)->sum('aprobadas');
        $totalReprobadas = collect($semestres)->sum('reprobadas');
        $totalMaterias = collect($semestres)->sum('total_materias');

        $logoPath = public_path('imagenes/icono.webp');
        $logoData = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $fechaGeneracion = now()->format('d/m/Y H:i');
        $codigoDoc = 'AC-' . str_pad($estudiante->id, 4, '0', STR_PAD_LEFT) . '-' . now()->format('Ymd');
    @endphp

    {{-- ================================================================
     ENCABEZADO INSTITUCIONAL
     ================================================================ --}}
    <div class="header">
        <table class="header-inner">
            <tr>
                <td class="header-logo-cell">
                    @if ($logoData)
                        <img src="{{ $logoData }}" alt="Logo ISTC">
                    @endif
                </td>
                <td class="header-title-cell">
                    <div class="inst-nombre">Instituto Superior Tecnológico Cumandá</div>
                    <div class="inst-subtitulo">Acreditado por el SENESCYT — RPC-SO-04-No.000037-2012</div>
                    <div class="doc-titulo">Acta Consolidada de Calificaciones</div>
                    <div class="doc-subtitulo">Registro Académico Oficial</div>
                </td>
                <td class="header-right-cell">
                    <div class="codigo-doc">
                        <strong>Cód:</strong> {{ $codigoDoc }}<br>
                        <strong>Fecha:</strong> {{ $fechaGeneracion }}<br><br>
                        @if ($mallaCompleta)
                            <span class="estado-malla-aprobado">✓ Malla Completa</span>
                        @else
                            <span class="estado-malla-curso">En Curso</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ================================================================
     DATOS DEL ESTUDIANTE
     ================================================================ --}}
    <div class="seccion-titulo">I. Datos del Estudiante</div>
    <table class="datos-estudiante">
        <tr>
            <td style="width:22%">
                <span class="dato-label">Apellidos y Nombres</span>
                <span class="dato-valor">{{ $estudiante->name }}</span>
            </td>
            <td style="width:12%">
                <span class="dato-label">Cédula de Identidad</span>
                <span class="dato-valor">{{ $estudiante->cedula ?? '—' }}</span>
            </td>
            <td style="width:12%">
                <span class="dato-label">N° Matrícula</span>
                <span class="dato-valor">{{ $estudiante->matricula_numero ?? '—' }}</span>
            </td>
            <td style="width:20%">
                <span class="dato-label">Correo Electrónico</span>
                <span class="dato-valor" style="font-size:8px">{{ $estudiante->email }}</span>
            </td>
            <td style="width:12%">
                <span class="dato-label">Teléfono</span>
                <span class="dato-valor">{{ $estudiante->telefono ?? ($estudiante->phone ?? '—') }}</span>
            </td>
            <td style="width:22%">
                <span class="dato-label">Carrera</span>
                <span class="dato-valor" style="font-size:7.5px">{{ $carrera->name }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="dato-label">Código Carrera</span>
                <span class="dato-valor">{{ $carrera->code ?? '—' }}</span>
            </td>
            <td>
                <span class="dato-label">Modalidad</span>
                <span class="dato-valor">{{ $carrera->modalidad ?? '—' }}</span>
            </td>
            <td>
                <span class="dato-label">Duración</span>
                <span class="dato-valor">{{ $carrera->duracion_semestres ?? '—' }} semestres</span>
            </td>
            <td>
                <span class="dato-label">Período Activo</span>
                <span class="dato-valor">{{ $matricula->periodo?->code ?? '—' }}</span>
            </td>
            <td>
                <span class="dato-label">Estado Matrícula</span>
                <span class="dato-valor">{{ $matricula->estado ?? '—' }}</span>
            </td>
            <td>
                <span class="dato-label">Progreso de Malla</span>
                <span class="dato-valor">{{ $semConDatos }}/{{ $totalSem }} semestres cursados</span>
                <div class="barra-progreso-outer">
                    <div class="barra-progreso-inner" style="width:{{ $pct }}%"></div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ================================================================
     STATS RESUMEN
     ================================================================ --}}
    <table class="stats-row">
        <tr>
            <td class="bg-azul-claro" style="width:20%">
                <span class="stat-numero stat-azul">{{ $totalMaterias }}</span>
                <span class="stat-label">Total materias</span>
            </td>
            <td class="bg-verde-claro" style="width:20%">
                <span class="stat-numero stat-verde">{{ $totalAprobadas }}</span>
                <span class="stat-label">Aprobadas</span>
            </td>
            <td class="bg-rojo-claro" style="width:20%">
                <span class="stat-numero stat-rojo">{{ $totalReprobadas }}</span>
                <span class="stat-label">Reprobadas</span>
            </td>
            <td class="bg-gris-claro" style="width:20%">
                <span class="stat-numero stat-gris">{{ $semConDatos }}/{{ $totalSem }}</span>
                <span class="stat-label">Semestres cursados</span>
            </td>
            <td class="bg-amber-claro" style="width:20%">
                <span class="stat-numero stat-amber">
                    {{ $promedioMalla !== null ? number_format($promedioMalla, 2) : '—' }}
                </span>
                <span class="stat-label">Promedio malla</span>
            </td>
        </tr>
    </table>

    {{-- ================================================================
     CALIFICACIONES POR SEMESTRE
     ================================================================ --}}
    <div class="seccion-titulo">II. Registro de Calificaciones por Semestre</div>
    <div style="margin-top: 10px"></div>

    @foreach ($semestres as $semestre)
        <div class="semestre-bloque">

            {{-- Header semestre --}}
            <table class="semestre-header" style="border-collapse:collapse; margin-bottom:0">
                <tr>
                    <td class="semestre-titulo-cell" style="padding:5px 8px">
                        <span class="semestre-titulo">{{ $semestre['semestre_nombre'] }}</span>
                    </td>
                    <td class="semestre-meta-cell" style="padding:5px 8px">
                        <span class="semestre-meta">
                            {{ $semestre['aprobadas'] }} aprobadas &nbsp;|&nbsp;
                            {{ $semestre['reprobadas'] }} reprobadas &nbsp;|&nbsp;
                            {{ $semestre['total_materias'] }} materias
                        </span>
                    </td>
                    <td class="semestre-promedio-cell" style="padding:5px 8px">
                        @if ($semestre['promedio'] !== null)
                            <span class="semestre-promedio-valor"
                                style="color:{{ $semestre['promedio'] >= 7 ? '#68d391' : '#fc8181' }}">
                                {{ number_format($semestre['promedio'], 2) }}
                            </span>
                            <span class="semestre-promedio-label">Promedio</span>
                        @else
                            <span class="semestre-promedio-valor" style="color:#a0aec0">—</span>
                            <span class="semestre-promedio-label">Sin datos</span>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- Tabla materias --}}
            <table class="tabla-materias">
                <thead>
                    <tr>
                        <th class="th-left" style="width:26%">Materia</th>
                        <th style="width:5%">Crd.</th>
                        <th style="width:6%">Paralelo</th>
                        <th style="width:8%">Período</th>
                        <th style="width:7%">Asistencia</th>
                        <th style="width:7%">Act. Autón.</th>
                        <th style="width:7%">Act. Prác.</th>
                        <th style="width:7%">Act. Doc.</th>
                        <th style="width:5%">Ética</th>
                        <th style="width:6%">Prom. Ins.</th>
                        <th style="width:6%">Ex. Parcial</th>
                        <th style="width:6%">Ex. Final</th>
                        <th style="width:6%">Suspenso</th>
                        <th style="width:7%">Nota Final</th>
                        <th style="width:7%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($semestre['materias'] as $m)
                        <tr>
                            {{-- Nombre materia --}}
                            <td>
                                <div class="td-materia-nombre">{{ $m['materia_nombre'] }}</div>
                                <div class="td-materia-code">{{ $m['materia_code'] }}</div>
                                @if ($m['es_arrastre'] ?? false)
                                    <span class="badge badge-arrastre">Arrastre</span>
                                @endif
                            </td>

                            {{-- Créditos --}}
                            <td class="td-center" style="color:#4a5568; font-weight:bold">
                                {{ $m['creditos'] ?? '—' }}
                            </td>

                            {{-- Paralelo --}}
                            <td class="td-center" style="color:#4a5568">
                                {{ $m['paralelo'] ?? '—' }}
                            </td>

                            {{-- Período --}}
                            <td class="td-center" style="color:#4a5568; font-size:7.5px">
                                {{ $m['periodo'] ?? '—' }}
                            </td>

                            {{-- Insumos 1-5 --}}
                            @foreach (['insumo1', 'insumo2', 'insumo3', 'insumo4', 'insumo5'] as $ins)
                                <td class="col-insumo">
                                    @if ($m[$ins] !== null)
                                        <span class="{{ $m[$ins] >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                            {{ number_format($m[$ins], 1) }}
                                        </span>
                                    @else
                                        <span class="sin-dato">—</span>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Promedio insumos --}}
                            <td class="td-nota">
                                @if ($m['promedio_insumos'] !== null)
                                    <span class="{{ $m['promedio_insumos'] >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                        {{ number_format($m['promedio_insumos'], 2) }}
                                    </span>
                                @else
                                    <span class="sin-dato">—</span>
                                @endif
                            </td>

                            {{-- Examen parcial --}}
                            <td class="td-nota">
                                @if ($m['examen_parcial'] !== null)
                                    <span class="{{ $m['examen_parcial'] >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                        {{ number_format($m['examen_parcial'], 2) }}
                                    </span>
                                @else
                                    <span class="sin-dato">—</span>
                                @endif
                            </td>

                            {{-- Examen final --}}
                            <td class="td-nota">
                                @if ($m['examen_final'] !== null)
                                    <span class="{{ $m['examen_final'] >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                        {{ number_format($m['examen_final'], 2) }}
                                    </span>
                                @else
                                    <span class="sin-dato">—</span>
                                @endif
                            </td>

                            {{-- Nota suspenso --}}
                            <td class="td-nota">
                                @if ($m['nota_suspenso'] !== null)
                                    <span class="nota-amber">
                                        {{ number_format($m['nota_suspenso'], 2) }}
                                    </span>
                                @else
                                    <span class="sin-dato">—</span>
                                @endif
                            </td>

                            {{-- Nota final --}}
                            <td class="td-nota" style="font-size:10px">
                                @if ($m['nota_final'] !== null)
                                    <span class="{{ $m['nota_final'] >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                        {{ number_format($m['nota_final'], 2) }}
                                    </span>
                                @else
                                    <span class="sin-dato">—</span>
                                @endif
                            </td>

                            {{-- Estado final --}}
                            <td class="td-center">
                                @if ($m['estado_final'])
                                    @php
                                        $badge = match ($m['estado_final']) {
                                            'Aprobado' => 'badge-aprobado',
                                            'Reprobado' => 'badge-reprobado',
                                            'Suspenso' => 'badge-suspenso',
                                            default => 'badge-pendiente',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $m['estado_final'] }}</span>
                                @else
                                    <span class="badge badge-pendiente">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- ================================================================
     SECCIÓN III: TITULACIÓN Y EGRESO
     ================================================================ --}}
    <div style="margin-top:14px"></div>
    <div class="seccion-titulo">III. Proceso de Titulación y Egreso</div>
    <div style="margin-top:10px"></div>

    <table class="titulacion-grid">
        <tr>
            {{-- Resumen de egreso --}}
            <td style="width:65%">
                <div class="card-titulacion">
                    <div class="card-titulo">Resumen de Egreso</div>
                    <div class="card-body">
                        <table class="egreso-stats">
                            <tr>
                                <td>
                                    <span
                                        class="egreso-numero {{ $promedioMalla === null ? 'nota-gris' : ($promedioMalla >= 7 ? 'nota-verde' : 'nota-rojo') }}">
                                        {{ $promedioMalla !== null ? number_format($promedioMalla, 2) : '—' }}
                                    </span>
                                    <span class="egreso-label">Promedio Malla</span>
                                </td>
                                <td>
                                    @if ($titulacion)
                                        <span
                                            class="egreso-numero {{ $titulacion->nota_final_egreso >= 7 ? 'nota-verde' : 'nota-rojo' }}">
                                            {{ number_format($titulacion->nota_final_egreso, 2) }}
                                        </span>
                                        <span class="egreso-label">Nota Titulación</span>
                                        <div style="margin-top:3px; font-size:7.5px; color:#718096">
                                            {{ $titulacion->tipo_titulacion_label ?? '' }}
                                        </div>
                                    @else
                                        <span class="egreso-numero nota-gris">—</span>
                                        <span class="egreso-label">Titulación Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($titulacion?->estado === 'Aprobado')
                                        <span class="egreso-numero nota-verde">EGRESADO</span>
                                        <span class="egreso-label">Estado Final</span>
                                    @elseif ($titulacion?->estado === 'Reprobado')
                                        <span class="egreso-numero nota-rojo">Reprobado</span>
                                        <span class="egreso-label">Estado Final</span>
                                    @elseif ($mallaCompleta)
                                        <span class="egreso-numero nota-amber" style="font-size:13px">En
                                            Titulación</span>
                                        <span class="egreso-label">Estado Final</span>
                                    @else
                                        <span class="egreso-numero stat-azul" style="font-size:13px">En Curso</span>
                                        <span class="egreso-label">Estado Final</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        {{-- Datos de práctica --}}
                        @if ($practica)
                            <div style="margin-top:8px; padding-top:6px; border-top:1px solid #e2e8f0">
                                <div
                                    style="font-size:7px; font-weight:bold; color:#718096; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px">
                                    Prácticas Preprofesionales
                                </div>
                                <table style="width:100%">
                                    <tr>
                                        <td style="width:50%; font-size:8px">
                                            <span style="color:#718096">Empresa: </span>
                                            <strong>{{ $practica->empresa ?? '—' }}</strong>
                                        </td>
                                        <td style="width:25%; font-size:8px; text-align:center">
                                            <span style="color:#718096">Horas: </span>
                                            <strong>{{ $practica->horas_completadas ?? '—' }}</strong>
                                        </td>
                                        <td style="width:25%; font-size:8px; text-align:right">
                                            <span
                                                class="badge {{ $practica->estado === 'Aprobado' ? 'badge-aprobado' : 'badge-pendiente' }}">
                                                {{ $practica->estado ?? '—' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endif

                        {{-- Tribunal --}}
                        @if ($titulacion?->presidente_tribunal)
                            <div style="margin-top:8px; padding-top:6px; border-top:1px solid #e2e8f0">
                                <div
                                    style="font-size:7px; font-weight:bold; color:#718096; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px">
                                    Tribunal Evaluador
                                </div>
                                <table class="tribunal-row">
                                    <tr>
                                        <td>
                                            <div class="tribunal-cargo">Presidente</div>
                                            <div class="tribunal-nombre">{{ $titulacion->presidente_tribunal }}</div>
                                        </td>
                                        @if ($titulacion->miembro_tribunal_1)
                                            <td>
                                                <div class="tribunal-cargo">Miembro 1</div>
                                                <div class="tribunal-nombre">{{ $titulacion->miembro_tribunal_1 }}
                                                </div>
                                            </td>
                                        @endif
                                        @if ($titulacion->miembro_tribunal_2)
                                            <td>
                                                <div class="tribunal-cargo">Miembro 2</div>
                                                <div class="tribunal-nombre">{{ $titulacion->miembro_tribunal_2 }}
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </td>

            {{-- Historial de intentos --}}
            <td style="width:35%; padding-left:8px; padding-right:0">
                <div class="card-titulacion">
                    <div class="card-titulo">Historial de Intentos — Titulación</div>
                    <div class="card-body" style="padding:0">
                        @if ($intentos->count() > 0)
                            <table class="intentos-table">
                                <thead>
                                    <tr>
                                        <th style="width:10%">#</th>
                                        <th style="width:35%; text-align:left; padding-left:6px">Tipo</th>
                                        <th style="width:20%">Fecha</th>
                                        <th style="width:18%">Nota</th>
                                        <th style="width:17%">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($intentos as $intento)
                                        <tr>
                                            <td>
                                                <span
                                                    class="intento-numero
                                                {{ $intento->estado === 'Aprobado' ? 'nota-verde' : ($intento->estado === 'Reprobado' ? 'nota-rojo' : 'nota-amber') }}">
                                                    {{ $intento->numero_intento }}
                                                </span>
                                            </td>
                                            <td
                                                style="text-align:left; padding-left:6px; font-size:7.5px; color:#4a5568">
                                                {{ $intento->tipo_titulacion_label ?? '—' }}
                                            </td>
                                            <td style="font-size:7.5px; color:#718096">
                                                {{ $intento->fecha_evaluacion?->format('d/m/Y') ?? '—' }}
                                            </td>
                                            <td>
                                                <span style="font-weight:bold; font-size:9px"
                                                    class="{{ $intento->nota_final_egreso >= 7 ? 'nota-verde' : ($intento->nota_final_egreso ? 'nota-rojo' : 'nota-gris') }}">
                                                    {{ $intento->nota_final_egreso ? number_format($intento->nota_final_egreso, 2) : '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge
                                                {{ $intento->estado === 'Aprobado' ? 'badge-aprobado' : ($intento->estado === 'Reprobado' ? 'badge-reprobado' : 'badge-pendiente') }}">
                                                    {{ $intento->estado }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div style="padding:16px; text-align:center; color:#a0aec0; font-size:8px">
                                Sin intentos registrados
                            </div>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ================================================================
     FIRMAS
     ================================================================ --}}
    <div class="seccion-firmas">
        <table class="firmas-table">
            <tr>
                <td>
                    <div class="linea-firma">
                        <div class="firma-nombre">___________________________</div>
                        <div class="firma-nombre" style="margin-top:2px">Secretaría Académica</div>
                        <div class="firma-cargo">Instituto Superior Tecnológico Cumandá</div>
                    </div>
                </td>
                <td>
                    <div class="linea-firma">
                        <div class="firma-nombre">___________________________</div>
                        <div class="firma-nombre" style="margin-top:2px">Coordinación de Carrera</div>
                        <div class="firma-cargo">{{ $carrera->name }}</div>
                    </div>
                </td>
                <td>
                    <div class="linea-firma">
                        <div class="firma-nombre">___________________________</div>
                        <div class="firma-nombre" style="margin-top:2px">Rector / Vicerrector</div>
                        <div class="firma-cargo">Instituto Superior Tecnológico Cumandá</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ================================================================
     PIE DE PÁGINA
     ================================================================ --}}
    <table class="footer">
        <tr>
            <td>
                Instituto Superior Tecnológico Cumandá &nbsp;|&nbsp;
                Documento generado el {{ $fechaGeneracion }} &nbsp;|&nbsp;
                Código: {{ $codigoDoc }}
            </td>
            <td class="footer-derecha">
                Este documento tiene validez oficial con sello y firma institucional.
            </td>
        </tr>
    </table>

</body>

</html>
