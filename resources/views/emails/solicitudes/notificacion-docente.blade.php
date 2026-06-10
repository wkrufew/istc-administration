<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Notificación de Solicitud</title>
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
        }
        @media only screen and (max-width: 600px) {
            .email-card { border-radius: 0 !important; }
            .body-td    { padding: 28px 20px !important; }
            .footer-td  { padding: 20px !important; }
        }
        .ck-content p  { margin: 0 0 10px; }
        .ck-content ul { margin: 0 0 10px; padding-left: 20px; }
        .ck-content ol { margin: 0 0 10px; padding-left: 20px; }
        .ck-content blockquote { border-left: 3px solid #d1d5db; margin: 0 0 10px; padding: 4px 12px; color: #6b7280; }
    </style>
</head>
<body class="email-outer" style="margin:0;padding:0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;background-color:#f0f4f8;color-scheme:light;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       class="email-outer" style="background-color:#f0f4f8;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" class="email-card" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.10);">

                {{-- ══ HEADER ══ --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 55%,#1d4ed8 100%);padding:36px 40px 28px;text-align:center;">

                        @if($instituto['logo_url'])
                            <img src="{{ $instituto['logo_url'] }}"
                                 alt="{{ $instituto['nombre_corto'] }}"
                                 width="52" height="52"
                                 style="height:52px;width:auto;display:block;margin:0 auto 14px;object-fit:contain;border:0;">
                        @else
                            <div style="width:52px;height:52px;background-color:rgba(255,255,255,0.15);border-radius:50%;display:inline-block;line-height:52px;margin-bottom:14px;font-size:20px;font-weight:800;color:#ffffff;text-align:center;">
                                {{ strtoupper(substr($instituto['nombre_corto'], 0, 2)) }}
                            </div>
                        @endif

                        <p style="margin:0 0 8px;font-size:12px;color:rgba(255,255,255,0.65);letter-spacing:0.12em;text-transform:uppercase;font-weight:600;">
                            {{ $instituto['nombre_largo'] }}
                        </p>

                        <div style="font-size:36px;line-height:1;margin:8px 0 4px;">📋</div>

                        <h1 style="margin:8px 0 4px;font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-0.02em;">
                            Solicitud procesada — Acción requerida
                        </h1>
                        <p style="margin:0;font-size:13px;color:rgba(255,255,255,0.75);">
                            {{ now()->format('d \d\e F \d\e Y') }}
                        </p>
                    </td>
                </tr>

                {{-- ══ BODY ══ --}}
                <tr>
                    <td class="body-td" style="padding:36px 40px 28px;background-color:#ffffff;">

                        @php $primerNombre = explode(' ', trim($docente->first_name ?? $docente->name))[0]; @endphp

                        <p style="margin:0 0 6px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.1em;font-weight:600;">
                            Estimado/a docente
                        </p>
                        <h2 style="margin:0 0 20px;font-size:20px;font-weight:700;color:#1e293b;">
                            {{ $docente->name }}
                        </h2>

                        <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.7;">
                            Le comunicamos que la siguiente solicitud ha sido procesada y requiere su atención:
                        </p>

                        {{-- Tarjeta info solicitud --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #bfdbfe;border-radius:12px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:18px 22px;">
                                    <p style="margin:0 0 12px;font-size:11px;color:#1d4ed8;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;">
                                        Detalle de la solicitud
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:5px 0;border-bottom:1px solid #bfdbfe;">
                                                <span style="font-size:11px;color:#3b82f6;font-weight:700;text-transform:uppercase;">Tipo</span>
                                            </td>
                                            <td style="padding:5px 0;border-bottom:1px solid #bfdbfe;text-align:right;">
                                                <span style="font-size:13px;color:#1e3a8a;font-weight:600;">{{ $tipo->nombre }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:5px 0;border-bottom:1px solid #bfdbfe;">
                                                <span style="font-size:11px;color:#3b82f6;font-weight:700;text-transform:uppercase;">Estudiante</span>
                                            </td>
                                            <td style="padding:5px 0;border-bottom:1px solid #bfdbfe;text-align:right;">
                                                <span style="font-size:13px;color:#1e3a8a;font-weight:600;">{{ $estudiante->name }}</span>
                                            </td>
                                        </tr>
                                        @if($estudiante->cedula)
                                        <tr>
                                            <td style="padding:5px 0;border-bottom:1px solid #bfdbfe;">
                                                <span style="font-size:11px;color:#3b82f6;font-weight:700;text-transform:uppercase;">Cédula</span>
                                            </td>
                                            <td style="padding:5px 0;border-bottom:1px solid #bfdbfe;text-align:right;">
                                                <span style="font-size:13px;color:#1e3a8a;font-weight:600;">{{ $estudiante->cedula }}</span>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td style="padding:5px 0;">
                                                <span style="font-size:11px;color:#3b82f6;font-weight:700;text-transform:uppercase;">Fecha solicitud</span>
                                            </td>
                                            <td style="padding:5px 0;text-align:right;">
                                                <span style="font-size:13px;color:#1e3a8a;font-weight:600;">{{ $solicitud->created_at->format('d/m/Y H:i') }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- Descripción del estudiante --}}
                        @if($solicitud->descripcion)
                        <p style="margin:0 0 8px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;font-weight:600;">
                            Descripción del estudiante
                        </p>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="margin-bottom:20px;">
                            <tr>
                                <td style="border-left:3px solid #93c5fd;padding:10px 16px;background-color:#f8fafc;border-radius:0 8px 8px 0;">
                                    <p style="margin:0;font-size:14px;color:#374151;line-height:1.7;">{{ $solicitud->descripcion }}</p>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- Observación / detalle de secretaría --}}
                        @if($solicitud->notas_admin)
                        <p style="margin:0 0 8px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;font-weight:600;">
                            Indicaciones de secretaría
                        </p>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:#fefce8;border:1px solid #fde68a;border-radius:10px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:16px 20px;font-size:14px;color:#374151;line-height:1.7;" class="ck-content">
                                    {!! $solicitud->notas_admin !!}
                                </td>
                            </tr>
                        </table>
                        @endif

                        <p style="margin:0 0 28px;font-size:15px;color:#374151;line-height:1.7;">
                            Ante cualquier consulta, comuníquese con la secretaría de la institución.
                        </p>

                        {{-- Botón --}}
                        <div style="text-align:center;margin-bottom:8px;">
                            <a href="{{ $instituto['url_portal'] }}"
                               style="display:inline-block;background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#ffffff;text-decoration:none;padding:12px 30px;border-radius:50px;font-size:14px;font-weight:700;letter-spacing:0.04em;">
                                Acceder al portal
                            </a>
                        </div>

                    </td>
                </tr>

                {{-- ══ FOOTER ══ --}}
                <tr>
                    <td class="footer-td" style="background-color:#f8fafc;border-top:1px solid #e2e8f0;padding:22px 40px;text-align:center;">
                        <p style="margin:0 0 4px;font-size:13px;font-weight:700;color:#374151;">
                            {{ $instituto['nombre_largo'] }}
                        </p>
                        @if($instituto['direccion'])
                            <p style="margin:0 0 2px;font-size:11px;color:#94a3b8;">{{ $instituto['direccion'] }}</p>
                        @endif
                        <p style="margin:0;font-size:11px;color:#94a3b8;">
                            @if($instituto['telefono']) {{ $instituto['telefono'] }} &nbsp;·&nbsp; @endif
                            @if($instituto['email'])
                                <a href="mailto:{{ $instituto['email'] }}" style="color:#94a3b8;text-decoration:none;">{{ $instituto['email'] }}</a>
                                @if($instituto['web']) &nbsp;·&nbsp; @endif
                            @endif
                            @if($instituto['web'])
                                <a href="{{ $instituto['web'] }}" style="color:#94a3b8;text-decoration:none;">{{ $instituto['web'] }}</a>
                            @endif
                        </p>
                        <p style="margin:10px 0 0;font-size:10px;color:#cbd5e1;">
                            Este mensaje fue generado automáticamente por el sistema de gestión institucional.
                        </p>
                    </td>
                </tr>

                {{-- Franja de colores --}}
                <tr>
                    <td style="height:4px;background:linear-gradient(90deg,#0f172a,#1e3a8a,#2563eb,#60a5fa);"></td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
