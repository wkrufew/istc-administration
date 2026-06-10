<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            margin: 15mm 18mm 15mm 18mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #fff;
        }

        /* ── Cabecera institucional ── */
        .inst-header {
            width: 100%;
            background-color: #14532d;
            padding: 10px 14px;
            border-radius: 6px 6px 0 0;
        }
        .inst-header table { width: 100%; border-collapse: collapse; }
        .inst-logo { height: 52px; width: auto; }
        .inst-name {
            font-size: 13px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .inst-detail { font-size: 8.5px; color: rgba(255,255,255,0.75); margin-top: 2px; }

        .inst-badge {
            text-align: right;
            padding-left: 10px;
        }
        .badge-label { font-size: 7.5px; color: rgba(255,255,255,0.65); text-transform: uppercase; letter-spacing: 0.06em; }
        .badge-value { font-size: 9px; color: #ffffff; font-weight: bold; margin-top: 1px; }

        /* ── Franja verde decorativa ── */
        .stripe {
            height: 4px;
            background: linear-gradient(to right, #14532d, #16a34a, #4ade80, #86efac);
            margin-bottom: 18px;
        }

        /* ── Título del certificado ── */
        .cert-title-wrapper {
            text-align: center;
            margin-bottom: 16px;
        }
        .cert-label {
            font-size: 9px;
            color: #15803d;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: bold;
        }
        .cert-title {
            font-size: 18px;
            font-weight: bold;
            color: #14532d;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .cert-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
            letter-spacing: 0.04em;
        }
        .cert-divider {
            width: 60px;
            height: 2px;
            background-color: #16a34a;
            margin: 8px auto 0;
        }

        /* ── Cuerpo del certificado ── */
        .cert-body {
            font-size: 11px;
            color: #374151;
            line-height: 1.9;
            text-align: justify;
            margin-bottom: 20px;
        }
        .cert-body .bold { font-weight: bold; color: #14532d; }

        /* ── Tarjeta de datos del estudiante ── */
        .data-card {
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            background-color: #f0fdf4;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .data-card-title {
            font-size: 8px;
            font-weight: bold;
            color: #15803d;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 10px;
            border-bottom: 1px solid #bbf7d0;
            padding-bottom: 5px;
        }
        .data-grid { width: 100%; border-collapse: collapse; }
        .data-label {
            font-size: 8px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 4px 8px 4px 0;
            width: 35%;
            vertical-align: top;
        }
        .data-value {
            font-size: 10px;
            color: #14532d;
            font-weight: bold;
            padding: 4px 0;
            vertical-align: top;
        }

        /* ── Código del certificado ── */
        .cert-code-box {
            text-align: center;
            margin-bottom: 24px;
        }
        .cert-code {
            display: inline-block;
            border: 1.5px solid #16a34a;
            border-radius: 20px;
            padding: 4px 18px;
            font-size: 11px;
            font-weight: bold;
            color: #15803d;
            letter-spacing: 0.1em;
            background-color: #f0fdf4;
        }
        .cert-date {
            font-size: 8.5px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* ── Sección de firmas ── */
        .signatures { margin-top: 28px; }
        .sig-table { width: 100%; border-collapse: collapse; }
        .sig-cell {
            text-align: center;
            padding: 0 10px;
            vertical-align: bottom;
            width: 50%;
        }
        .sig-line {
            border-top: 1.5px solid #374151;
            margin-bottom: 5px;
            margin-top: 24px;
        }
        .sig-name { font-size: 10px; font-weight: bold; color: #1e293b; }
        .sig-role { font-size: 8px; color: #64748b; margin-top: 2px; }

        /* ── Pie de página ── */
        .footer {
            margin-top: 28px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
        }
        .footer-text { font-size: 7.5px; color: #94a3b8; line-height: 1.6; }
        .footer-note {
            font-size: 7px;
            color: #cbd5e1;
            margin-top: 4px;
            font-style: italic;
        }

        .no-data { color: #94a3b8; font-style: italic; }
    </style>
</head>
<body>

{{-- ── CABECERA ── --}}
<div class="inst-header">
    <table>
        <tr>
            <td style="width:70px; vertical-align:middle;">
                @if(file_exists($instituto['logo_file']))
                    <img src="{{ $instituto['logo_file'] }}" class="inst-logo" alt="Logo">
                @endif
            </td>
            <td style="vertical-align:middle; padding-left:10px;">
                <div class="inst-name">{{ $instituto['nombre_largo'] }}</div>
                @if($instituto['direccion'])
                    <div class="inst-detail">{{ $instituto['direccion'] }}</div>
                @endif
                <div class="inst-detail">
                    @if($instituto['telefono']) Tel: {{ $instituto['telefono'] }} &nbsp;&nbsp; @endif
                    @if($instituto['email']) {{ $instituto['email'] }} @endif
                </div>
            </td>
            <td class="inst-badge" style="vertical-align:middle; width:150px;">
                @if($instituto['ruc'])
                    <div class="badge-label">RUC</div>
                    <div class="badge-value">{{ $instituto['ruc'] }}</div>
                @endif
                @if($instituto['senescyt'])
                    <div class="badge-label" style="margin-top:4px;">Cód. SENESCYT</div>
                    <div class="badge-value">{{ $instituto['senescyt'] }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>

<div class="stripe"></div>

{{-- ── TÍTULO ── --}}
<div class="cert-title-wrapper">
    <div class="cert-label">Certificación Oficial</div>
    <div class="cert-title">Certificado de No Adeudar</div>
    <div class="cert-subtitle">{{ strtoupper($instituto['ciudad']) }} &nbsp;·&nbsp; {{ now()->format('d/m/Y') }}</div>
    <div class="cert-divider"></div>
</div>

{{-- ── CÓDIGO DEL CERTIFICADO ── --}}
<div class="cert-code-box">
    <div class="cert-code">{{ $codigo }}</div>
    <div class="cert-date">Emitido el {{ now()->isoFormat('D [de] MMMM [de] Y') }}</div>
</div>

{{-- ── DATOS DEL ESTUDIANTE ── --}}
<div class="data-card">
    <div class="data-card-title">Datos del Estudiante</div>
    <table class="data-grid">
        <tr>
            <td class="data-label">Nombres completos:</td>
            <td class="data-value">{{ strtoupper($estudiante->name) }}</td>
            <td class="data-label">Cédula de identidad:</td>
            <td class="data-value">{{ $estudiante->cedula ?? '—' }}</td>
        </tr>
        <tr>
            <td class="data-label">Carrera:</td>
            <td class="data-value">{{ strtoupper($matricula?->carrera?->name ?? '—') }}</td>
            <td class="data-label">N° Matrícula:</td>
            <td class="data-value">{{ $matricula?->code ?? '—' }}</td>
        </tr>
        <tr>
            <td class="data-label">Cohorte:</td>
            <td class="data-value">
                {{ $matricula?->periodo?->code ?? '—' }}
                @if($matricula?->periodo?->description)
                    &nbsp;·&nbsp; {{ $matricula->periodo->description }}
                @endif
            </td>
            <td class="data-label">Período académico:</td>
            <td class="data-value">
                @if($periodoCarrera)
                    {{ $periodoCarrera->code }}
                    @if($periodoCarrera->description)
                        &nbsp;·&nbsp; {{ $periodoCarrera->description }}
                    @endif
                @else
                    <span class="no-data">—</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="data-label">Estado matrícula:</td>
            <td class="data-value">{{ $matricula?->estado ?? '—' }}</td>
            <td class="data-label">Correo electrónico:</td>
            <td class="data-value" style="font-size:9px;">{{ $estudiante->email ?? '—' }}</td>
        </tr>
    </table>
</div>

{{-- ── CUERPO DEL CERTIFICADO ── --}}
<div class="cert-body">
    <p>
        La <span class="bold">{{ $instituto['nombre_largo'] }}</span>, a través de su
        Departamento Financiero, <span class="bold">CERTIFICA</span> que el/la estudiante
        <span class="bold">{{ strtoupper($estudiante->name) }}</span>,
        portador/a de la cédula de ciudadanía N°
        <span class="bold">{{ $estudiante->cedula ?? '—' }}</span>,
        matriculado/a en la carrera de
        <span class="bold">{{ strtoupper($matricula?->carrera?->name ?? '—') }}</span>,
        <span class="bold">NO REGISTRA OBLIGACIONES FINANCIERAS PENDIENTES</span>
        con esta institución a la fecha de emisión del presente documento.
    </p>

    <br>

    <p>
        El presente certificado se expide a petición del/la interesado/a, para los fines
        que estime convenientes, en la ciudad de
        <span class="bold">{{ $instituto['ciudad'] }}</span>,
        a los <span class="bold">{{ now()->isoFormat('D [días del mes de] MMMM [del año] Y') }}</span>.
    </p>
</div>

{{-- ── FIRMAS ── --}}
<div class="signatures">
    <table class="sig-table">
        <tr>
            @php
                $rector     = trim($instituto['rector'] ?? '');
                $financiero = trim($instituto['departamento_financiero'] ?? '');
                $cols = ($financiero) ? 2 : 1;
            @endphp

            @if($financiero)
            <td class="sig-cell">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $financiero }}</div>
                <div class="sig-role">Departamento Financiero</div>
            </td>
            @endif

            <td class="sig-cell">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $rector ?: $instituto['nombre_corto'] }}</div>
                <div class="sig-role">Rector / Director</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── PIE DE PÁGINA ── --}}
<div class="footer">
    <div class="footer-text">
        {{ $instituto['nombre_largo'] }}
        @if($instituto['web']) &nbsp;·&nbsp; {{ $instituto['web'] }} @endif
        @if($instituto['telefono']) &nbsp;·&nbsp; {{ $instituto['telefono'] }} @endif
    </div>
    <div class="footer-note">
        Este documento tiene validez de 30 días a partir de su fecha de emisión.
        Cód. de verificación: <strong>{{ $codigo }}</strong> &nbsp;·&nbsp;
        Generado por el Sistema de Gestión Institucional.
    </div>
</div>

</body>
</html>
