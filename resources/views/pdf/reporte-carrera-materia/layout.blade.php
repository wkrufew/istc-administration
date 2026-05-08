<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            margin: 14mm 12mm 12mm 12mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1e293b;
            background: #fff;
        }

        /* ═══════════════════════════════════════════════
           CABECERA INSTITUCIONAL
        ═══════════════════════════════════════════════ */
        .inst-header {
            width: 100%;
            margin-bottom: 0;
        }

        .inst-banner {
            background-color: #14451a;
            padding: 8px 12px;
            width: 100%;
        }

        .inst-banner-table {
            width: 100%;
        }

        .inst-logo-cell {
            width: 58px;
            vertical-align: middle;
            padding-right: 10px;
        }

        .inst-logo {
            width: 52px;
            height: 52px;
        }

        .inst-info-cell {
            vertical-align: middle;
        }

        .inst-name {
            font-size: 12.5px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.3px;
            line-height: 1.3;
        }

        .inst-detail {
            font-size: 7.5px;
            color: #86efac;
            margin-top: 2px;
            line-height: 1.5;
        }

        .inst-badge-cell {
            width: 170px;
            vertical-align: middle;
            text-align: right;
        }

        .inst-badge-table {
            width: 100%;
        }

        .inst-badge-label {
            font-size: 7px;
            color: #86efac;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .inst-badge-value {
            font-size: 8.5px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .inst-accent-bar {
            background-color: #22c55e;
            height: 3px;
        }

        /* Barra de título del reporte */
        .report-title-bar {
            background-color: #f0fdf4;
            border-bottom: 1px solid #bbf7d0;
            padding: 5px 12px;
            width: 100%;
        }

        .report-title-table {
            width: 100%;
        }

        .report-title-text {
            font-size: 10px;
            font-weight: bold;
            color: #14451a;
            letter-spacing: 0.2px;
        }

        .report-title-date {
            text-align: right;
            font-size: 7.5px;
            color: #64748b;
            vertical-align: middle;
        }

        /* Separador decorativo */
        .section-sep {
            height: 8px;
        }

        /* ═══════════════════════════════════════════════
           TARJETAS (CARDS)
        ═══════════════════════════════════════════════ */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            margin-bottom: 8px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .card-header {
            background-color: #14451a;
            color: #ffffff;
            padding: 6px 10px;
            font-size: 9.5px;
            font-weight: bold;
        }

        .card-header-light {
            background-color: #f0fdf4;
            border-bottom: 1px solid #bbf7d0;
            padding: 5px 10px;
            font-size: 9px;
            font-weight: bold;
            color: #14451a;
        }

        .card-body {
            padding: 8px 10px;
            background-color: #ffffff;
        }

        /* ═══════════════════════════════════════════════
           ESTADÍSTICAS
        ═══════════════════════════════════════════════ */
        .stats-wrapper {
            margin-bottom: 8px;
        }

        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
        }

        .stat-box {
            text-align: center;
            padding: 7px 4px;
            border-radius: 5px;
            vertical-align: middle;
        }

        .stat-box-green {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
        }

        .stat-box-blue {
            background-color: #eff6ff;
            border: 1px solid #93c5fd;
        }

        .stat-box-amber {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
        }

        .stat-box-red {
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
        }

        .stat-box-purple {
            background-color: #faf5ff;
            border: 1px solid #d8b4fe;
        }

        .stat-box-teal {
            background-color: #f0fdfa;
            border: 1px solid #99f6e4;
        }

        .stat-number {
            font-size: 17px;
            font-weight: bold;
            line-height: 1;
        }

        .stat-number-green  { color: #15803d; }
        .stat-number-blue   { color: #1d4ed8; }
        .stat-number-amber  { color: #b45309; }
        .stat-number-red    { color: #b91c1c; }
        .stat-number-purple { color: #7c3aed; }
        .stat-number-teal   { color: #0f766e; }

        .stat-label {
            font-size: 7.5px;
            color: #475569;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ═══════════════════════════════════════════════
           TABLAS DE DATOS
        ═══════════════════════════════════════════════ */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr th {
            background-color: #1e3a2a;
            color: #ffffff;
            padding: 5px 6px;
            font-size: 7.5px;
            font-weight: bold;
            text-align: left;
            border-right: 1px solid #2d5a3a;
            letter-spacing: 0.2px;
        }

        .data-table thead tr th:last-child {
            border-right: none;
        }

        .data-table tbody tr td {
            padding: 4px 6px;
            font-size: 8px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .data-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .data-table tbody tr:hover td {
            background-color: #f0fdf4;
        }

        .data-table tfoot tr td {
            padding: 5px 6px;
            font-size: 8px;
            font-weight: bold;
            background-color: #f0fdf4;
            border-top: 2px solid #16a34a;
            color: #14451a;
        }

        /* ═══════════════════════════════════════════════
           INFO GRID (pares label-valor)
        ═══════════════════════════════════════════════ */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 8px;
        }

        .info-label {
            color: #64748b;
            font-weight: bold;
            white-space: nowrap;
            padding-right: 6px;
        }

        .info-value {
            color: #1e293b;
        }

        /* ═══════════════════════════════════════════════
           BADGES
        ═══════════════════════════════════════════════ */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            font-size: 7.5px;
            border-radius: 9px;
            color: #ffffff;
            font-weight: bold;
        }

        .badge-green  { background-color: #16a34a; }
        .badge-red    { background-color: #dc2626; }
        .badge-amber  { background-color: #d97706; }
        .badge-blue   { background-color: #2563eb; }
        .badge-gray   { background-color: #64748b; }
        .badge-purple { background-color: #7c3aed; }
        .badge-teal   { background-color: #0d9488; }
        .badge-outline-green {
            display: inline-block;
            padding: 1px 5px;
            font-size: 7.5px;
            border-radius: 9px;
            color: #15803d;
            background-color: #dcfce7;
            font-weight: bold;
        }
        .badge-outline-red {
            display: inline-block;
            padding: 1px 5px;
            font-size: 7.5px;
            border-radius: 9px;
            color: #b91c1c;
            background-color: #fee2e2;
            font-weight: bold;
        }
        .badge-outline-amber {
            display: inline-block;
            padding: 1px 5px;
            font-size: 7.5px;
            border-radius: 9px;
            color: #92400e;
            background-color: #fef3c7;
            font-weight: bold;
        }
        .badge-outline-gray {
            display: inline-block;
            padding: 1px 5px;
            font-size: 7.5px;
            border-radius: 9px;
            color: #374151;
            background-color: #f3f4f6;
            font-weight: bold;
        }

        /* ═══════════════════════════════════════════════
           SUBSECCIONES
        ═══════════════════════════════════════════════ */
        .subsection-title {
            background-color: #f8fafc;
            border-left: 3px solid #16a34a;
            padding: 4px 8px;
            margin: 8px 0 3px 0;
            font-size: 8.5px;
            font-weight: bold;
            color: #14451a;
        }

        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 6px 0;
        }

        /* ═══════════════════════════════════════════════
           SECCIÓN DE FIRMAS
        ═══════════════════════════════════════════════ */
        .signatures-section {
            margin-top: 28px;
            page-break-inside: avoid;
        }

        .signatures-title {
            font-size: 8px;
            color: #64748b;
            text-align: center;
            margin-bottom: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sig-cell {
            text-align: center;
            vertical-align: bottom;
            padding: 0 12px;
            width: 33.3%;
        }

        .sig-line-area {
            border-top: 1px solid #374151;
            padding-top: 5px;
            margin-top: 68px;
        }

        .sig-name {
            font-size: 8.5px;
            font-weight: bold;
            color: #1e293b;
        }

        .sig-role {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ═══════════════════════════════════════════════
           TÍTULO DE SECCIÓN
        ═══════════════════════════════════════════════ */
        .seccion-titulo {
            background-color: #14451a;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            padding: 5px 10px;
            margin: 10px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ═══════════════════════════════════════════════
           COLORES DE NOTA
        ═══════════════════════════════════════════════ */
        .nota-verde { color: #15803d; font-weight: bold; }
        .nota-rojo  { color: #b91c1c; font-weight: bold; }
        .nota-amber { color: #b45309; font-weight: bold; }
        .nota-gris  { color: #94a3b8; }

        /* ═══════════════════════════════════════════════
           UTILIDADES
        ═══════════════════════════════════════════════ */
        .no-break { page-break-inside: avoid; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .bold        { font-weight: bold; }
        .text-muted  { color: #94a3b8; }
        .small       { font-size: 7.5px; }
        .mb-6        { margin-bottom: 6px; }
        .mb-4        { margin-bottom: 4px; }
    </style>
</head>
<body>

@php
    use App\Services\SettingService;
    $rptNombreLargo  = SettingService::get('instituto.nombre_largo', 'Instituto Superior Tecnológico Cumandá');
    $rptNombreCorto  = SettingService::get('instituto.nombre_corto', 'ISTC');
    $rptRuc          = SettingService::get('instituto.ruc', '—');
    $rptDireccion    = SettingService::get('instituto.direccion', '—');
    $rptTelefono     = SettingService::get('instituto.telefono', '—');
    $rptEmail        = SettingService::get('instituto.email', '—');
    $rptLogoPath     = SettingService::get('instituto.logo_path');
    $rptLogoFile     = $rptLogoPath
        ? storage_path('app/public/' . $rptLogoPath)
        : public_path('imagenes/icono.webp');
    $rptRector       = SettingService::get('documentos.rector', '—');
    $rptSecretario   = SettingService::get('documentos.secretario', '—');
    $rptCoordinador  = SettingService::get('documentos.coordinador', '—');
    $rptCiudad       = SettingService::get('documentos.ciudad', 'Cumandá, Ecuador');
@endphp

{{-- CABECERA INSTITUCIONAL --}}
<div class="inst-header">
    <div class="inst-banner">
        <table class="inst-banner-table">
            <tr>
                <td class="inst-logo-cell">
                    <img src="{{ $rptLogoFile }}" class="inst-logo" />
                </td>
                <td class="inst-info-cell">
                    <div class="inst-name">{{ strtoupper($rptNombreLargo) }}</div>
                    <div class="inst-detail">{{ $rptDireccion }}</div>
                    <div class="inst-detail">Tel: {{ $rptTelefono }}&nbsp;&nbsp;·&nbsp;&nbsp;{{ $rptEmail }}</div>
                </td>
                <td class="inst-badge-cell">
                    <table class="inst-badge-table">
                        <tr>
                            <td>
                                <div class="inst-badge-label">Cód. SENESCYT / RUC</div>
                                <div class="inst-badge-value">{{ $rptRuc }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="inst-badge-label">Fecha de emisión</div>
                                <div class="inst-badge-value">{{ now()->format('d/m/Y  H:i') }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="inst-accent-bar"></div>
    <div class="report-title-bar">
        <table class="report-title-table">
            <tr>
                <td class="report-title-text">@yield('report-title', 'REPORTE INSTITUCIONAL')</td>
                <td class="report-title-date">{{ $rptCiudad }}, {{ now()->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="section-sep"></div>

{{-- CONTENIDO PRINCIPAL --}}
@yield('content')

{{-- FIRMAS (sobreescribible por vistas hijas con @section('firmas')) --}}
@section('firmas')
<div class="signatures-section">
    <div class="signatures-title">Certifican la veracidad del presente documento</div>
    <table class="signatures-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-line-area">
                    <div class="sig-name">{{ $rptRector }}</div>
                    <div class="sig-role">Rector / Director</div>
                </div>
            </td>
            <td class="sig-cell">
                <div class="sig-line-area">
                    <div class="sig-name">{{ $rptSecretario }}</div>
                    <div class="sig-role">Secretario/a Académico/a</div>
                </div>
            </td>
            <td class="sig-cell">
                <div class="sig-line-area">
                    <div class="sig-name">{{ $rptCoordinador }}</div>
                    <div class="sig-role">Coordinación Académica</div>
                </div>
            </td>
        </tr>
    </table>
</div>
@show

</body>
</html>
