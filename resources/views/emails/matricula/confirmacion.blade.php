<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>
        @if($esEdicion) Actualización de Matrícula
        @elseif($esPrimerMatricula) Bienvenido — Matrícula Confirmada
        @else Confirmación de Matrícula
        @endif
    </title>
    <!--[if mso]>
    <noscript>
        <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    </noscript>
    <![endif]-->
    <style>
        /* ── Forzar modo claro en todos los clientes ── */
        :root { color-scheme: light only; }

        /* ── Override Apple Mail / iOS dark mode ── */
        @media (prefers-color-scheme: dark) {
            body,
            .email-outer    { background-color: #eef2f7 !important; }
            .email-card     { background-color: #ffffff !important; }
            .email-body-td  { background-color: #ffffff !important; }
            .section-card   { background-color: #f8fafc !important; }
            .text-main      { color: #1e293b !important; }
            .text-muted     { color: #64748b !important; }
            .text-label     { color: #94a3b8 !important; }
            .divider-line   { background-color: #e2e8f0 !important; }
            .footer-td      { background-color: #f8fafc !important; }
            .footer-name    { color: #334155 !important; }
            .footer-sub     { color: #94a3b8 !important; }
            .outer-note     { color: #94a3b8 !important; }
            /* Secciones de color oscuro (headers de tabla, banners) — NO tocar */
        }

        /* ── Responsive ── */
        @media only screen and (max-width: 600px) {
            .email-wrapper  { width: 100% !important; }
            .email-card     { border-radius: 0 !important; }
            .email-body     { padding: 24px 20px !important; }
            .email-footer   { padding: 20px !important; }
            .btn-cta        { display: block !important; text-align: center !important; }
            .info-grid td   { display: block !important; width: 100% !important; text-align: left !important; }
            .payment-row td { display: block !important; width: 100% !important; text-align: left !important; padding-bottom: 8px !important; }
        }
    </style>
</head>
<body class="email-outer" style="margin:0;padding:0;background-color:#eef2f7 !important;font-family:'Segoe UI',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;color-scheme:light;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background-color:#eef2f7;padding:32px 16px;" class="email-outer">
    <tr>
        <td align="center">

            {{-- ─── CARD ─────────────────────────────────────────────────── --}}
            <table role="presentation" class="email-wrapper email-card" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;
                          box-shadow:0 8px 40px rgba(0,0,0,0.10);">

                {{-- ══ HEADER ══════════════════════════════════════════════ --}}
                <tr>
                    <td bgcolor="#0f2d5a"
                        style="background-color:#0f2d5a;background:linear-gradient(135deg,#0f2d5a 0%,#1e56b0 100%);padding:36px 40px 28px;text-align:center;">
                        {{-- Iniciales siempre visibles; logo encima si la URL es pública --}}
                        <div style="width:56px;height:56px;background:rgba(255,255,255,0.18);border-radius:12px;
                                    display:inline-block;margin-bottom:16px;line-height:56px;
                                    font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-1px;text-align:center;
                                    {{ $instituto['logo_url'] ? 'display:none;' : '' }}">
                            {{ strtoupper(substr($instituto['nombre_corto'], 0, 2)) }}
                        </div>
                        @if($instituto['logo_url'])
                            <img src="{{ $instituto['logo_url'] }}"
                                 alt="{{ $instituto['nombre_corto'] }}"
                                 width="56" height="56"
                                 style="height:56px;width:auto;display:block;margin:0 auto 16px;object-fit:contain;border:0;">
                        @endif
                        <h1 style="margin:0;color:#ffffff;font-size:18px;font-weight:700;line-height:1.3;letter-spacing:-0.3px;">
                            {{ $instituto['nombre_largo'] }}
                        </h1>
                        <p style="margin:6px 0 0;color:rgba(255,255,255,0.70);font-size:12px;
                                  text-transform:uppercase;letter-spacing:0.12em;">
                            Sistema de Gestión Académica
                        </p>
                    </td>
                </tr>

                {{-- ══ STATUS BANNER ══════════════════════════════════════ --}}
                <tr>
                    <td style="background:{{ $esEdicion ? '#d97706' : ($esPrimerMatricula ? '#059669' : '#1e56b0') }};
                               padding:11px 40px;text-align:center;">
                        <span style="color:#ffffff;font-size:13px;font-weight:700;
                                     text-transform:uppercase;letter-spacing:0.12em;">
                            @if($esEdicion)  Matrícula Actualizada
                            @elseif($esPrimerMatricula)  ¡Bienvenido! — Primera Matrícula
                            @else  Matrícula Confirmada
                            @endif
                        </span>
                    </td>
                </tr>

                {{-- ══ BODY ════════════════════════════════════════════════ --}}
                <tr>
                    <td class="email-body email-body-td" style="padding:36px 40px 0;background-color:#ffffff;">

                        {{-- Saludo --}}
                        <p class="text-main" style="margin:0 0 6px;font-size:17px;font-weight:600;color:#0f2d5a;">
                            Estimado/a {{ $estudiante->name }},
                        </p>
                        <p class="text-muted" style="margin:0 0 28px;font-size:14px;color:#64748b;line-height:1.7;">
                            @if($esEdicion)
                                Le informamos que su matrícula en el período académico
                                <strong style="color:#0f2d5a;">{{ $matricula->periodo?->code }}</strong>
                                ha sido actualizada exitosamente.
                            @elseif($esPrimerMatricula)
                                Le damos la más cordial bienvenida al
                                <strong style="color:#0f2d5a;">{{ $instituto['nombre_largo'] }}</strong>.
                                Su proceso de matrícula ha sido completado exitosamente. A continuación encontrará
                                los detalles de su inscripción y sus credenciales de acceso.
                            @else
                                Su matrícula para el período académico
                                <strong style="color:#0f2d5a;">{{ $matricula->periodo?->code }}</strong>
                                ha sido confirmada exitosamente. A continuación encontrará el resumen de su inscripción.
                            @endif
                        </p>

                        {{-- ── DATOS DE MATRÍCULA ──────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               class="section-card"
                               style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p class="text-label" style="margin:0 0 14px;font-size:11px;font-weight:700;color:#94a3b8;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Datos de Matrícula
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">Código</td>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:700;
                                                        color:#0f2d5a;text-align:right;">
                                                <span style="font-family:monospace;background-color:#e8f0fe;color:#1d4ed8;
                                                             padding:2px 8px;border-radius:4px;font-size:13px;">
                                                    {{ $matricula->code }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">Carrera</td>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;
                                                        color:#1e293b;text-align:right;">{{ $matricula->carrera?->name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">Período Académico</td>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;
                                                        color:#1e293b;text-align:right;">{{ $matricula->periodo?->code }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">Tipo de Matrícula</td>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;text-align:right;">
                                                @php
                                                    $tipoBg    = match($matricula->tipo) { 'Nueva' => '#dcfce7', 'Arrastre' => '#fef3c7', default => '#dbeafe' };
                                                    $tipoColor = match($matricula->tipo) { 'Nueva' => '#15803d', 'Arrastre' => '#92400e', default => '#1d4ed8' };
                                                @endphp
                                                <span style="font-size:12px;font-weight:700;background-color:{{ $tipoBg }};
                                                             color:{{ $tipoColor }};padding:3px 10px;border-radius:20px;">
                                                    {{ $matricula->tipo }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;font-size:13px;color:#64748b;">Fecha de Matrícula</td>
                                            <td style="padding:9px 0;font-size:13px;font-weight:600;color:#1e293b;text-align:right;">
                                                {{ $matricula->fecha_matricula?->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- ── BECA / CONVENIO ACTIVO ─────────────────── --}}
                        @if(!$esEdicion && ($becaActiva || $convenioActivo))
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#faf5ff;border:2px solid #c4b5fd;border-radius:10px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:11px;font-weight:700;color:#6d28d9;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Beneficios Aplicados al Arancel
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        @if($becaActiva)
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #ddd6fe;font-size:13px;color:#7c3aed;">
                                                Beca · {{ $becaActiva->tipoBeca?->nombre ?? '—' }}
                                            </td>
                                            <td style="padding:9px 0;border-bottom:1px solid #ddd6fe;text-align:right;">
                                                <span style="font-size:13px;font-weight:700;background-color:#ede9fe;
                                                             color:#6d28d9;padding:3px 10px;border-radius:20px;">
                                                    {{ number_format($becaActiva->porcentaje_aplicado, 0) }}% descuento
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($convenioActivo)
                                        <tr>
                                            <td style="padding:9px 0;{{ $becaActiva ? '' : 'border-bottom:1px solid #ddd6fe;' }}font-size:13px;color:#7c3aed;">
                                                Convenio · {{ $convenioActivo->tipoConvenio?->nombre ?? '—' }}
                                                @if($convenioActivo->motivo)
                                                    <span style="font-size:11px;color:#a78bfa;"> — {{ $convenioActivo->motivo }}</span>
                                                @endif
                                            </td>
                                            <td style="padding:9px 0;{{ $becaActiva ? '' : 'border-bottom:1px solid #ddd6fe;' }}text-align:right;">
                                                <span style="font-size:13px;font-weight:700;background-color:#ede9fe;
                                                             color:#6d28d9;padding:3px 10px;border-radius:20px;">
                                                    {{ number_format($convenioActivo->porcentaje_aplicado, 0) }}% descuento
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                        @php
                                            $pctBeca     = $becaActiva ? (float) $becaActiva->porcentaje_aplicado : 0;
                                            $pctConvenio = $convenioActivo ? (float) $convenioActivo->porcentaje_aplicado : 0;
                                            $pctTotal    = min(100, $pctBeca + $pctConvenio);
                                        @endphp
                                        @if($pctBeca > 0 && $pctConvenio > 0)
                                        <tr>
                                            <td style="padding:9px 0 0;font-size:13px;color:#6d28d9;font-weight:600;">
                                                Descuento total combinado
                                            </td>
                                            <td style="padding:9px 0 0;text-align:right;">
                                                @if($pctTotal >= 100)
                                                <span style="font-size:13px;font-weight:700;background-color:#d1fae5;
                                                             color:#065f46;padding:3px 10px;border-radius:20px;">
                                                    GRATUIDAD (100%)
                                                </span>
                                                @else
                                                <span style="font-size:13px;font-weight:700;background-color:#ede9fe;
                                                             color:#6d28d9;padding:3px 10px;border-radius:20px;">
                                                    {{ $pctTotal }}% total
                                                </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @elseif($pctTotal >= 100)
                                        <tr>
                                            <td colspan="2" style="padding:9px 0 0;text-align:center;">
                                                <span style="font-size:13px;font-weight:700;background-color:#d1fae5;
                                                             color:#065f46;padding:4px 14px;border-radius:20px;">
                                                    GRATUIDAD — Arancel $0.00
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                    <p style="margin:14px 0 0;font-size:12px;color:#7c3aed;line-height:1.6;">
                                        Estos beneficios se aplican únicamente al arancel semestral. La cuota de matrícula no se modifica.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- ── MATERIAS INSCRITAS ──────────────────────── --}}
                        @if($matricula->detalles->isNotEmpty())
                        <p class="text-label" style="margin:0 0 12px;font-size:11px;font-weight:700;color:#94a3b8;
                                   text-transform:uppercase;letter-spacing:0.13em;">
                            Materias Inscritas
                        </p>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;margin-bottom:24px;">
                            <thead>
                                <tr style="background-color:#0f2d5a;">
                                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;
                                               color:rgba(255,255,255,0.85);text-transform:uppercase;letter-spacing:0.08em;">#</th>
                                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;
                                               color:rgba(255,255,255,0.85);text-transform:uppercase;letter-spacing:0.08em;">Materia</th>
                                    <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;
                                               color:rgba(255,255,255,0.85);text-transform:uppercase;letter-spacing:0.08em;">Tipo</th>
                                    <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;
                                               color:rgba(255,255,255,0.85);text-transform:uppercase;letter-spacing:0.08em;">Créditos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matricula->detalles as $i => $detalle)
                                <tr style="background-color:{{ $i % 2 === 0 ? '#f8fafc' : '#ffffff' }};border-top:1px solid #e2e8f0;">
                                    <td style="padding:10px 16px;font-size:13px;color:#94a3b8;">{{ $i + 1 }}</td>
                                    <td style="padding:10px 16px;font-size:13px;color:#1e293b;font-weight:500;">
                                        {{ $detalle->materia?->name }}
                                    </td>
                                    <td style="padding:10px 16px;text-align:center;">
                                        @if($detalle->tipo === 'Arrastre')
                                            <span style="font-size:11px;font-weight:700;background-color:#fef3c7;
                                                         color:#92400e;padding:2px 8px;border-radius:20px;">Arrastre</span>
                                        @else
                                            <span style="font-size:11px;font-weight:700;background-color:#dcfce7;
                                                         color:#15803d;padding:2px 8px;border-radius:20px;">Normal</span>
                                        @endif
                                    </td>
                                    <td style="padding:10px 16px;font-size:13px;font-weight:700;color:#0f2d5a;text-align:center;">
                                        {{ number_format((($detalle->materia?->horas_teoricas ?? 0) + ($detalle->materia?->horas_practicas ?? 0)) / 48, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                        {{-- ── CREDENCIALES (solo primera matrícula) ──── --}}
                        @if($esPrimerMatricula && !$esEdicion)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#eff6ff;border:2px solid #93c5fd;border-radius:10px;margin-bottom:16px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:11px;font-weight:700;color:#1d4ed8;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Credenciales de Acceso al Portal Estudiantil
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #bfdbfe;font-size:13px;color:#2563eb;">
                                                Usuario (correo electrónico)
                                            </td>
                                            <td style="padding:9px 0;border-bottom:1px solid #bfdbfe;font-size:13px;
                                                        font-weight:700;color:#1e3a8a;text-align:right;font-family:monospace;">
                                                {{ $estudiante->email }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;font-size:13px;color:#2563eb;">Contraseña inicial</td>
                                            <td style="padding:9px 0;font-size:13px;font-weight:700;color:#1e3a8a;
                                                        text-align:right;font-family:monospace;">
                                                {{ $estudiante->cedula ?? '(su número de cédula)' }}
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:14px 0 0;font-size:12px;color:#2563eb;line-height:1.6;">
                                        Por su seguridad, le recomendamos cambiar su contraseña en el primer inicio de sesión.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        {{-- ── CREDENCIALES PLATAFORMA VIRTUAL (solo si Moodle activo) ── --}}
                        @if(isset($moodle_activo) && $moodle_activo && isset($moodle_url) && $moodle_url)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#f0fdf4;border:2px solid #86efac;border-radius:10px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:11px;font-weight:700;color:#166534;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Acceso a la Plataforma Virtual
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #bbf7d0;font-size:13px;color:#15803d;">
                                                URL de la plataforma
                                            </td>
                                            <td style="padding:9px 0;border-bottom:1px solid #bbf7d0;font-size:13px;
                                                        font-weight:700;color:#14532d;text-align:right;">
                                                <a href="{{ $moodle_url }}"
                                                   style="color:#14532d;text-decoration:underline;">
                                                    {{ $moodle_url }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #bbf7d0;font-size:13px;color:#15803d;">
                                                Usuario
                                            </td>
                                            <td style="padding:9px 0;border-bottom:1px solid #bbf7d0;font-size:13px;
                                                        font-weight:700;color:#14532d;text-align:right;font-family:monospace;">
                                                {{ $estudiante->cedula ?? '(número de cédula)' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;font-size:13px;color:#15803d;">Contraseña inicial</td>
                                            <td style="padding:9px 0;font-size:13px;font-weight:700;color:#14532d;
                                                        text-align:right;font-family:monospace;">
                                                {{ $estudiante->cedula ?? '(número de cédula)' }}
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:14px 0 0;font-size:12px;color:#15803d;line-height:1.6;">
                                        Ingrese a esta plataforma para acceder a sus clases, materiales y actividades académicas.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        @endif

                        @endif

                        {{-- ── PAGO INMEDIATO: MATRÍCULA ────────────── --}}
                        @if($obligacion && !$esEdicion)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#f0fdf4;border:2px solid #86efac;border-radius:10px;margin-bottom:16px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:11px;font-weight:700;color:#166534;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Pago Inmediato — Matrícula
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:#166534;">Valor a cancelar</p>
                                                <p style="margin:4px 0 0;font-size:34px;font-weight:800;color:#15803d;line-height:1;">
                                                    ${{ $monto }}
                                                </p>
                                            </td>
                                            <td style="text-align:right;vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:#166534;">Fecha límite</p>
                                                <p style="margin:4px 0 0;font-size:22px;font-weight:700;color:#15803d;">
                                                    {{ $fechaLimite ?? '—' }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="margin-top:14px;padding-top:12px;border-top:1px solid #bbf7d0;">
                                        <p style="margin:0;font-size:12px;color:#166534;line-height:1.6;">
                                            Realice este pago antes de la fecha límite para garantizar su cupo.
                                            Comuníquese con secretaría para más información.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- ── INSCRIPCIÓN (primera matrícula) ────────── --}}
                        @if(isset($montoInscripcion) && $montoInscripcion && $esPrimerMatricula && !$esEdicion)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#fff7ed;border:2px solid #fdba74;border-radius:10px;margin-bottom:16px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:11px;font-weight:700;color:#9a3412;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Valor de Inscripción — Primera Matrícula
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:#9a3412;">Valor a cancelar</p>
                                                <p style="margin:4px 0 0;font-size:34px;font-weight:800;color:#c2410c;line-height:1;">
                                                    ${{ $montoInscripcion }}
                                                </p>
                                            </td>
                                            <td style="text-align:right;vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:#9a3412;">Fecha límite</p>
                                                <p style="margin:4px 0 0;font-size:22px;font-weight:700;color:#c2410c;">
                                                    {{ $fechaInscripcion ?? '—' }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="margin-top:14px;padding-top:12px;border-top:1px solid #fed7aa;">
                                        <p style="margin:0;font-size:12px;color:#9a3412;line-height:1.6;">
                                            Cargo único de inscripción por primera matrícula. Ya incluido en el total a pagar.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- ── ARANCEL SEMESTRAL ────────────────────── --}}
                        @if($montoArancel && !$esEdicion)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#eff6ff;border:2px solid #93c5fd;border-radius:10px;margin-bottom:32px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:11px;font-weight:700;color:#1d4ed8;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Arancel Semestral — A Pagar durante el Semestre
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:#1d4ed8;">
                                                        Valor del semestre{{ isset($numCuotasArancel) && $numCuotasArancel > 1 ? ' (' . $numCuotasArancel . ' cuotas)' : '' }}
                                                    </p>
                                                <p style="margin:4px 0 0;font-size:34px;font-weight:800;color:#1e40af;line-height:1;">
                                                    ${{ $montoArancel }}
                                                </p>
                                            </td>
                                            <td style="text-align:right;vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:#1d4ed8;">Fecha límite</p>
                                                <p style="margin:4px 0 0;font-size:22px;font-weight:700;color:#1e40af;">
                                                    {{ $fechaArancel ?? '—' }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="margin-top:14px;padding-top:12px;border-top:1px solid #bfdbfe;">
                                        <p style="margin:0;font-size:12px;color:#1d4ed8;line-height:1.6;">
                                            Este valor corresponde al arancel del semestre en curso y debe ser cancelado
                                            durante el transcurso del período
                                            <strong style="color:#1e3a8a;">{{ $matricula->periodo?->code }}</strong>.
                                            Puede realizarlo en cuotas. Consulte con secretaría su plan de pago.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        @elseif($esEdicion)
                        <div style="height:32px;"></div>
                        @endif

                    </td>
                </tr>

                {{-- ══ CTA BUTTON ═════════════════════════════════════════ --}}
                @if($instituto['url_portal'])
                <tr>
                    <td style="padding:0 40px 32px;text-align:center;background-color:#ffffff;">
                        <a href="{{ $instituto['url_portal'] }}" class="btn-cta"
                           style="display:inline-block;background-color:#1e56b0;color:#ffffff !important;
                                  text-decoration:none;padding:14px 36px;border-radius:8px;
                                  font-size:14px;font-weight:700;letter-spacing:0.02em;">
                            Acceder al Portal Estudiantil
                        </a>
                    </td>
                </tr>
                @else
                <tr><td style="height:32px;background-color:#ffffff;"></td></tr>
                @endif

                {{-- ══ DIVIDER ════════════════════════════════════════════ --}}
                <tr>
                    <td style="padding:0 40px;background-color:#ffffff;">
                        <div class="divider-line" style="height:1px;background-color:#e2e8f0;"></div>
                    </td>
                </tr>

                {{-- ══ FOOTER ══════════════════════════════════════════════ --}}
                <tr>
                    <td class="email-footer footer-td" style="padding:24px 40px 28px;text-align:center;background-color:#f8fafc;border-radius:0 0 16px 16px;">
                        <p class="footer-name" style="margin:0 0 4px;font-size:13px;font-weight:600;color:#334155;">
                            {{ $instituto['nombre_largo'] }}
                        </p>
                        @if($instituto['direccion'])
                        <p class="footer-sub" style="margin:0 0 4px;font-size:12px;color:#94a3b8;">
                            {{ $instituto['direccion'] }}
                        </p>
                        @endif
                        @if($instituto['telefono'] || $instituto['email'])
                        <p class="footer-sub" style="margin:0 0 16px;font-size:12px;color:#94a3b8;">
                            @if($instituto['telefono']) Tel: {{ $instituto['telefono'] }} @endif
                            @if($instituto['telefono'] && $instituto['email']) &nbsp;&middot;&nbsp; @endif
                            @if($instituto['email']) {{ $instituto['email'] }} @endif
                        </p>
                        @else
                        <div style="height:16px;"></div>
                        @endif
                        <p class="footer-sub" style="margin:0;font-size:11px;color:#94a3b8;line-height:1.6;">
                            Este es un correo automático generado por el sistema de gestión académica.
                            Por favor no responda a este mensaje directamente.
                        </p>
                    </td>
                </tr>

            </table>
            {{-- /CARD --}}

            <p class="outer-note" style="margin:16px 0 0;font-size:11px;color:#94a3b8;text-align:center;">
                &copy; {{ date('Y') }} {{ $instituto['nombre_corto'] }} &mdash; Todos los derechos reservados
            </p>

        </td>
    </tr>
</table>

</body>
</html>
