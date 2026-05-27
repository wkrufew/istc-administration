<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Tu documento está listo</title>
    <!--[if mso]>
    <noscript>
        <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    </noscript>
    <![endif]-->
    <style>
        :root { color-scheme: light only; }

        @media (prefers-color-scheme: dark) {
            body, .email-outer { background-color: #f0f4f8 !important; }
            .email-card        { background-color: #ffffff !important; }
            .body-td           { background-color: #ffffff !important; }
            .doc-card          { background-color: #f0fdf4 !important; }
            .note-card         { background-color: #fffbeb !important; }
            .text-dark         { color: #1e293b !important; }
            .text-muted        { color: #64748b !important; }
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
                    <td style="background-color:#065f46;background:linear-gradient(135deg,#065f46 0%,#059669 100%);padding:36px 40px;text-align:center;">

                        @if($instituto['logo_url'])
                            <img src="{{ $instituto['logo_url'] }}"
                                 alt="{{ $instituto['nombre_corto'] }}"
                                 width="52" height="52"
                                 style="height:52px;width:auto;display:block;margin:0 auto 14px;object-fit:contain;border:0;">
                        @else
                            <div style="width:52px;height:52px;background-color:rgba(255,255,255,0.20);border-radius:50%;
                                        display:inline-block;line-height:52px;margin-bottom:14px;
                                        font-size:20px;font-weight:800;color:#ffffff;text-align:center;">
                                {{ strtoupper(substr($instituto['nombre_corto'], 0, 2)) }}
                            </div>
                        @endif

                        <p style="margin:0 0 4px;color:rgba(255,255,255,0.75);font-size:12px;
                                  letter-spacing:0.12em;text-transform:uppercase;">
                            {{ $instituto['nombre_largo'] }}
                        </p>
                        <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:-0.5px;">
                            ¡Tu documento está listo!
                        </h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.80);font-size:14px;">
                            Tu solicitud ha sido procesada exitosamente
                        </p>
                    </td>
                </tr>

                {{-- ══ ICONO CONFIRMACIÓN ══════════════════════════════════ --}}
                <tr>
                    <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:28px 40px 0;text-align:center;">
                        <div style="width:64px;height:64px;border-radius:50%;background-color:#dcfce7;
                                    border:3px solid #86efac;display:inline-block;line-height:58px;
                                    font-size:28px;text-align:center;">
                            ✉
                        </div>
                    </td>
                </tr>

                {{-- ══ BODY ════════════════════════════════════════════════ --}}
                <tr>
                    <td bgcolor="#ffffff" class="body-td" style="padding:28px 40px 36px;background-color:#ffffff;">

                        {{-- Saludo --}}
                        <p class="text-dark" style="margin:0 0 6px;color:#1e293b;font-size:16px;font-weight:600;">
                            Hola, {{ $solicitud->estudiante->name }}.
                        </p>
                        <p class="text-muted" style="margin:0 0 28px;color:#64748b;font-size:14px;line-height:1.7;">
                            Nos complace informarte que tu solicitud ha sido procesada y tu
                            <strong>{{ $solicitud->tipoSolicitud?->nombre ?? 'documento' }}</strong>
                            ha sido enviado a este correo electrónico. Si no encuentras el documento
                            adjunto, revisa tu carpeta de <em>spam</em> o comunícate con secretaría.
                        </p>

                        {{-- ── DETALLE DEL DOCUMENTO ───────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #bbf7d0;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#16a34a" style="background-color:#16a34a;padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Detalle del Documento
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#f0fdf4" class="doc-card" style="background-color:#f0fdf4;padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;
                                                        border-bottom:1px solid #d1fae5;font-size:13px;color:#166534;width:48%;">
                                                Tipo de Documento
                                            </td>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;
                                                        border-bottom:1px solid #d1fae5;font-size:13px;
                                                        font-weight:700;color:#14532d;text-align:right;">
                                                {{ $solicitud->tipoSolicitud?->nombre ?? '—' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;
                                                        border-bottom:1px solid #d1fae5;font-size:13px;color:#166534;">
                                                N.° de Solicitud
                                            </td>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;
                                                        border-bottom:1px solid #d1fae5;font-size:13px;
                                                        font-weight:600;color:#14532d;text-align:right;font-family:monospace;">
                                                #{{ str_pad($solicitud->id, 6, '0', STR_PAD_LEFT) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;
                                                        border-bottom:1px solid #d1fae5;font-size:13px;color:#166534;">
                                                Estudiante
                                            </td>
                                            <td bgcolor="#f0fdf4" style="background-color:#f0fdf4;padding:9px 0;
                                                        border-bottom:1px solid #d1fae5;font-size:13px;
                                                        font-weight:600;color:#14532d;text-align:right;">
                                                {{ $solicitud->estudiante->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;
                                                        font-size:13px;color:#166534;">
                                                Fecha de Emisión
                                            </td>
                                            <td bgcolor="#dcfce7" style="background-color:#dcfce7;padding:9px 0;
                                                        font-size:13px;color:#14532d;text-align:right;">
                                                {{ now()->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- ── PROGRESO COMPLETADO ─────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#334155" style="background-color:#334155;padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Estado del Trámite
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:0;width:33%;text-align:center;vertical-align:top;">
                                                <div style="width:32px;height:32px;border-radius:50%;
                                                            background-color:#16a34a;margin:0 auto 8px;
                                                            display:inline-block;line-height:32px;">
                                                    <span style="color:#ffffff;font-size:14px;font-weight:700;">✓</span>
                                                </div>
                                                <p style="margin:0;font-size:11px;font-weight:700;color:#16a34a;">Aprobado</p>
                                            </td>
                                            <td style="padding:0 4px;vertical-align:middle;text-align:center;">
                                                <div style="height:2px;background-color:#16a34a;margin-top:-12px;"></div>
                                            </td>
                                            <td style="padding:0;width:33%;text-align:center;vertical-align:top;">
                                                <div style="width:32px;height:32px;border-radius:50%;
                                                            background-color:#16a34a;margin:0 auto 8px;
                                                            display:inline-block;line-height:32px;">
                                                    <span style="color:#ffffff;font-size:14px;font-weight:700;">✓</span>
                                                </div>
                                                <p style="margin:0;font-size:11px;font-weight:700;color:#16a34a;">Elaborado</p>
                                            </td>
                                            <td style="padding:0 4px;vertical-align:middle;text-align:center;">
                                                <div style="height:2px;background-color:#16a34a;margin-top:-12px;"></div>
                                            </td>
                                            <td style="padding:0;width:33%;text-align:center;vertical-align:top;">
                                                <div style="width:32px;height:32px;border-radius:50%;
                                                            background-color:#16a34a;margin:0 auto 8px;
                                                            display:inline-block;line-height:32px;">
                                                    <span style="color:#ffffff;font-size:16px;">✉</span>
                                                </div>
                                                <p style="margin:0;font-size:11px;font-weight:700;color:#16a34a;">Enviado</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- ── NOTA ────────────────────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #fde68a;border-radius:10px;margin-bottom:8px;">
                            <tr>
                                <td bgcolor="#fffbeb" class="note-card" style="background-color:#fffbeb;padding:16px 20px;">
                                    <p style="margin:0 0 5px;color:#92400e;font-size:13px;font-weight:700;">
                                        Información importante
                                    </p>
                                    <p style="margin:0;color:#78350f;font-size:13px;line-height:1.7;">
                                        Si no encuentras el documento en tu bandeja de entrada, revisa la carpeta de
                                        <strong>correo no deseado</strong> o <strong>spam</strong>. Si el problema persiste
                                        o tienes alguna observación sobre el documento, comunícate con secretaría indicando
                                        tu número de solicitud <strong>#{{ str_pad($solicitud->id, 6, '0', STR_PAD_LEFT) }}</strong>.
                                    </p>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- ══ CTA ════════════════════════════════════════════════ --}}
                @if($instituto['url_portal'])
                <tr>
                    <td style="padding:24px 40px;text-align:center;background-color:#ffffff;">
                        <a href="{{ $instituto['url_portal'] }}"
                           style="display:inline-block;background-color:#059669;color:#ffffff !important;
                                  text-decoration:none;padding:14px 36px;border-radius:8px;
                                  font-size:14px;font-weight:700;letter-spacing:0.02em;">
                            Ver mis solicitudes en el portal
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

                {{-- ══ FOOTER ═════════════════════════════════════════════ --}}
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
