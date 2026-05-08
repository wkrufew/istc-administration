<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Confirmación de Pago</title>
    <!--[if mso]>
    <noscript>
        <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    </noscript>
    <![endif]-->
    <style>
        :root { color-scheme: light only; }

        @media (prefers-color-scheme: dark) {
            body,
            .email-outer    { background-color: #f0f4f8 !important; }
            .email-card     { background-color: #ffffff !important; }
            .body-td        { background-color: #ffffff !important; }
            .receipt-card   { background-color: #f0fdf4 !important; }
            .data-card      { background-color: #f8fafc !important; }
            .note-card      { background-color: #fffbeb !important; }
            .text-dark      { color: #1e293b !important; }
            .text-muted     { color: #64748b !important; }
        }

        @media only screen and (max-width: 600px) {
            .email-card  { border-radius: 0 !important; }
            .body-td     { padding: 24px 20px !important; }
            .footer-td   { padding: 20px !important; }
        }
    </style>
</head>
<body class="email-outer" style="margin:0;padding:0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;background-color:#f0f4f8;color-scheme:light;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       class="email-outer" style="background-color:#f0f4f8;padding:32px 16px;">
    <tr>
        <td align="center">

            <table role="presentation" class="email-card" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;
                          box-shadow:0 8px 40px rgba(0,0,0,0.10);">

                {{-- ══ HEADER ══════════════════════════════════════════════ --}}
                <tr>
                    <td bgcolor="#1a6b3c"
                        style="background-color:#1a6b3c;background:linear-gradient(135deg,#1a6b3c 0%,#22a05e 100%);padding:36px 40px;text-align:center;">

                        {{-- Iniciales siempre visibles como fallback --}}
                        <div style="width:52px;height:52px;background-color:rgba(255,255,255,0.20);border-radius:50%;
                                    display:inline-block;line-height:52px;margin-bottom:14px;
                                    font-size:20px;font-weight:800;color:#ffffff;text-align:center;
                                    {{ $instituto['logo_url'] ? 'display:none;' : '' }}">
                            {{ strtoupper(substr($instituto['nombre_corto'], 0, 2)) }}
                        </div>
                        @if($instituto['logo_url'])
                            <img src="{{ $instituto['logo_url'] }}"
                                 alt="{{ $instituto['nombre_corto'] }}"
                                 width="52" height="52"
                                 style="height:52px;width:auto;display:block;margin:0 auto 14px;object-fit:contain;border:0;">
                        @endif

                        <p style="margin:0 0 4px;color:rgba(255,255,255,0.80);font-size:12px;
                                  letter-spacing:0.12em;text-transform:uppercase;">
                            {{ $instituto['nombre_largo'] }}
                        </p>
                        <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:-0.5px;">
                            Pago Confirmado
                        </h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.80);font-size:14px;">
                            Tu pago ha sido registrado exitosamente
                        </p>
                    </td>
                </tr>

                {{-- ══ BODY ════════════════════════════════════════════════ --}}
                <tr>
                    <td bgcolor="#ffffff" class="body-td" style="padding:36px 40px;background-color:#ffffff;">

                        {{-- Saludo --}}
                        <p class="text-dark" style="margin:0 0 8px;color:#1e293b;font-size:16px;font-weight:600;">
                            Hola, {{ $matricula->estudiante->name }}.
                        </p>
                        <p class="text-muted" style="margin:0 0 28px;color:#64748b;font-size:14px;line-height:1.7;">
                            Confirmamos la recepción de tu pago. A continuación encontrarás el detalle del comprobante.
                        </p>

                        {{-- ── COMPROBANTE ────────────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #bbf7d0;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#16a34a" style="background-color:#16a34a;padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Comprobante de Pago
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:13px;color:#166534;width:48%;">N.° Comprobante</td>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:13px;font-weight:700;color:#14532d;
                                                        font-family:monospace;text-align:right;">
                                                {{ $pago->numero_comprobante }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:13px;color:#166534;">Monto Pagado</td>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:22px;font-weight:800;color:#16a34a;text-align:right;">
                                                {{ $montoFormateado }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:13px;color:#166534;">Método de Pago</td>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:13px;font-weight:600;color:#14532d;text-align:right;">
                                                {{ $pago->metodo_pago }}
                                            </td>
                                        </tr>
                                        @if($pago->codigo_referencia)
                                        <tr>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:13px;color:#166534;">N.° Referencia</td>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:13px;color:#14532d;text-align:right;font-family:monospace;">
                                                {{ $pago->codigo_referencia }}
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:13px;color:#166534;">Fecha y Hora</td>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;border-bottom:1px solid #d1fae5;
                                                        font-size:13px;color:#14532d;text-align:right;">
                                                {{ $fechaPago }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;font-size:13px;color:#166534;">Estado</td>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;text-align:right;">
                                                <span style="display:inline-block;background-color:#166534;color:#ffffff;
                                                             font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;">
                                                    {{ $pago->estado }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- ── COMPROBANTE INSCRIPCIÓN (si fue primera matrícula) ── --}}
                        @if(isset($pagoInscripcion) && $pagoInscripcion)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #fed7aa;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#ea580c" style="background-color:#ea580c;padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Inscripción — Primera Matrícula
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:13px;color:#9a3412;width:48%;">N.° Comprobante</td>
                                            <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:13px;font-weight:700;color:#7c2d12;
                                                        font-family:monospace;text-align:right;">
                                                {{ $pagoInscripcion->numero_comprobante }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#fde8d0" style="background-color:#fde8d0;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:13px;color:#9a3412;">Monto Inscripción</td>
                                            <td bgcolor="#fde8d0" style="background-color:#fde8d0;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:22px;font-weight:800;color:#ea580c;text-align:right;">
                                                ${{ number_format((float) $pagoInscripcion->monto, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:9px 0;font-size:13px;color:#9a3412;">Estado</td>
                                            <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:9px 0;text-align:right;">
                                                <span style="display:inline-block;background-color:#ea580c;color:#ffffff;
                                                             font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;">
                                                    {{ $pagoInscripcion->estado }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:12px 0 0;font-size:12px;color:#9a3412;line-height:1.6;">
                                        Cargo único de inscripción liquidado junto con el pago de matrícula.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- ── DATOS DE MATRÍCULA ──────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#334155" style="background-color:#334155;padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Datos de Matrícula
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#475569;width:48%;">Código de Matrícula</td>
                                            <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;font-weight:700;color:#1e293b;
                                                        text-align:right;font-family:monospace;">
                                                {{ $matricula->code }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#eef2f7" style="background-color:#eef2f7;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#475569;">Estudiante</td>
                                            <td bgcolor="#eef2f7" style="background-color:#eef2f7;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;font-weight:600;color:#1e293b;text-align:right;">
                                                {{ $matricula->estudiante->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#475569;">Carrera</td>
                                            <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#1e293b;text-align:right;">
                                                {{ $matricula->carrera->name ?? '—' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#eef2f7" style="background-color:#eef2f7;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#475569;">Período</td>
                                            <td bgcolor="#eef2f7" style="background-color:#eef2f7;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#1e293b;text-align:right;">
                                                {{ $matricula->periodo?->code ?? '—' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:9px 0;font-size:13px;color:#475569;">Estado Matrícula</td>
                                            <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:9px 0;text-align:right;">
                                                <span style="display:inline-block;background-color:#1d4ed8;color:#ffffff;
                                                             font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;">
                                                    {{ $matricula->estado }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- ── NOTA INFORMATIVA ──────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #fde68a;border-radius:10px;margin-bottom:8px;">
                            <tr>
                                <td bgcolor="#fffbeb" style="background-color:#fffbeb;padding:16px 20px;">
                                    <p style="margin:0 0 5px;color:#92400e;font-size:13px;font-weight:700;">
                                        Información importante
                                    </p>
                                    <p style="margin:0;color:#78350f;font-size:13px;line-height:1.6;">
                                        Conserva este comprobante como respaldo de tu pago. Si tienes alguna consulta
                                        sobre tu matrícula, acércate a secretaría o comunícate con nosotros.
                                    </p>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- ══ CTA BUTTON ═════════════════════════════════════════ --}}
                @if($instituto['url_portal'])
                <tr>
                    <td style="padding:24px 40px;text-align:center;background-color:#ffffff;">
                        <a href="{{ $instituto['url_portal'] }}"
                           style="display:inline-block;background-color:#16a34a;color:#ffffff !important;
                                  text-decoration:none;padding:14px 36px;border-radius:8px;
                                  font-size:14px;font-weight:700;letter-spacing:0.02em;">
                            Acceder al Portal Estudiantil
                        </a>
                    </td>
                </tr>
                @else
                <tr><td style="height:24px;background-color:#ffffff;"></td></tr>
                @endif

                {{-- ══ DIVIDER ════════════════════════════════════════════ --}}
                <tr>
                    <td style="padding:0 40px;background-color:#ffffff;">
                        <div style="height:1px;background-color:#e2e8f0;"></div>
                    </td>
                </tr>

                {{-- ══ FOOTER ══════════════════════════════════════════════ --}}
                <tr>
                    <td class="footer-td" style="padding:24px 40px 28px;text-align:center;
                               background-color:#f8fafc;border-radius:0 0 16px 16px;">
                        <p style="margin:0 0 4px;font-size:13px;font-weight:600;color:#334155;">
                            {{ $instituto['nombre_largo'] }}
                        </p>
                        @if($instituto['direccion'])
                        <p style="margin:0 0 4px;font-size:12px;color:#94a3b8;">
                            {{ $instituto['direccion'] }}
                        </p>
                        @endif
                        @if($instituto['telefono'] || $instituto['email'])
                        <p style="margin:0 0 16px;font-size:12px;color:#94a3b8;">
                            @if($instituto['telefono']) Tel: {{ $instituto['telefono'] }} @endif
                            @if($instituto['telefono'] && $instituto['email']) &nbsp;&middot;&nbsp; @endif
                            @if($instituto['email']) {{ $instituto['email'] }} @endif
                        </p>
                        @else
                        <div style="height:16px;"></div>
                        @endif
                        <p style="margin:0;font-size:11px;color:#94a3b8;line-height:1.6;">
                            Este es un correo automático generado por el sistema de gestión académica.
                            Por favor no responda a este mensaje directamente.
                        </p>
                    </td>
                </tr>

            </table>

            <p style="margin:16px 0 0;font-size:11px;color:#94a3b8;text-align:center;">
                &copy; {{ date('Y') }} {{ $instituto['nombre_corto'] }} &mdash; Todos los derechos reservados
            </p>

        </td>
    </tr>
</table>

</body>
</html>
