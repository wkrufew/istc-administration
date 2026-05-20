<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Acceso a la Plataforma Virtual</title>
    <style>
        :root { color-scheme: light only; }
        @media (prefers-color-scheme: dark) {
            body, .email-outer    { background-color: #eef2f7 !important; }
            .email-card           { background-color: #ffffff !important; }
            .email-body-td        { background-color: #ffffff !important; }
            .text-main            { color: #1e293b !important; }
            .text-muted           { color: #64748b !important; }
            .footer-td            { background-color: #f8fafc !important; }
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper { width: 100% !important; }
            .email-card    { border-radius: 0 !important; }
            .email-body    { padding: 24px 20px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#eef2f7;font-family:'Segoe UI',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background-color:#eef2f7;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" class="email-wrapper email-card" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;
                          box-shadow:0 8px 40px rgba(0,0,0,0.10);">

                {{-- HEADER --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#0f2d5a 0%,#1e56b0 100%);padding:36px 40px 28px;text-align:center;">
                        @if($instituto['logo_url'])
                            <img src="{{ $instituto['logo_url'] }}" alt="{{ $instituto['nombre_corto'] }}"
                                 width="56" height="56"
                                 style="height:56px;width:auto;display:block;margin:0 auto 16px;object-fit:contain;border:0;">
                        @else
                            <div style="width:56px;height:56px;background:rgba(255,255,255,0.18);border-radius:12px;
                                        display:inline-block;margin-bottom:16px;line-height:56px;
                                        font-size:22px;font-weight:800;color:#ffffff;text-align:center;">
                                {{ strtoupper(substr($instituto['nombre_corto'], 0, 2)) }}
                            </div>
                        @endif
                        <h1 style="margin:0;color:#ffffff;font-size:18px;font-weight:700;line-height:1.3;">
                            {{ $instituto['nombre_largo'] }}
                        </h1>
                        <p style="margin:6px 0 0;color:rgba(255,255,255,0.70);font-size:12px;
                                  text-transform:uppercase;letter-spacing:0.12em;">
                            Plataforma Virtual de Aprendizaje
                        </p>
                    </td>
                </tr>

                {{-- BANNER --}}
                <tr>
                    <td style="background:#059669;padding:11px 40px;text-align:center;">
                        <span style="color:#ffffff;font-size:13px;font-weight:700;
                                     text-transform:uppercase;letter-spacing:0.12em;">
                            Acceso al Campus Virtual
                        </span>
                    </td>
                </tr>

                {{-- BODY --}}
                <tr>
                    <td class="email-body email-body-td" style="padding:36px 40px 32px;background-color:#ffffff;">

                        <p class="text-main" style="margin:0 0 6px;font-size:17px;font-weight:600;color:#0f2d5a;">
                            Estimado/a {{ $usuario->name }},
                        </p>
                        <p class="text-muted" style="margin:0 0 28px;font-size:14px;color:#64748b;line-height:1.7;">
                            Le informamos que ya tiene acceso habilitado a la plataforma virtual del
                            <strong style="color:#0f2d5a;">{{ $instituto['nombre_largo'] }}</strong>.
                            A continuación encontrará sus credenciales de acceso.
                        </p>

                        {{-- CREDENCIALES MOODLE --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#f0fdf4;border:2px solid #86efac;border-radius:10px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:11px;font-weight:700;color:#166534;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Credenciales — Plataforma Virtual
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        @if($moodle_url)
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #bbf7d0;font-size:13px;color:#15803d;">
                                                URL de la plataforma
                                            </td>
                                            <td style="padding:9px 0;border-bottom:1px solid #bbf7d0;font-size:13px;
                                                        font-weight:700;color:#14532d;text-align:right;">
                                                <a href="{{ $moodle_url }}" style="color:#14532d;text-decoration:underline;">
                                                    {{ $moodle_url }}
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #bbf7d0;font-size:13px;color:#15803d;">
                                                Usuario
                                            </td>
                                            <td style="padding:9px 0;border-bottom:1px solid #bbf7d0;font-size:13px;
                                                        font-weight:700;color:#14532d;text-align:right;font-family:monospace;">
                                                {{ $usuario->cedula }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;font-size:13px;color:#15803d;">Contraseña inicial</td>
                                            <td style="padding:9px 0;font-size:13px;font-weight:700;color:#14532d;
                                                        text-align:right;font-family:monospace;">
                                                {{ $usuario->cedula }}
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:14px 0 0;font-size:12px;color:#15803d;line-height:1.6;">
                                        Por su seguridad, le recomendamos cambiar su contraseña en el primer inicio de sesión.
                                        Ingrese a la plataforma para acceder a sus clases, materiales y actividades académicas.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        {{-- CTA --}}
                        @if($moodle_url)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
                            <tr>
                                <td style="text-align:center;">
                                    <a href="{{ $moodle_url }}"
                                       style="display:inline-block;background-color:#059669;color:#ffffff !important;
                                              text-decoration:none;padding:14px 36px;border-radius:8px;
                                              font-size:14px;font-weight:700;letter-spacing:0.02em;">
                                        Ingresar al Campus Virtual
                                    </a>
                                </td>
                            </tr>
                        </table>
                        @endif

                    </td>
                </tr>

                {{-- DIVIDER --}}
                <tr>
                    <td style="padding:0 40px;background-color:#ffffff;">
                        <div style="height:1px;background-color:#e2e8f0;"></div>
                    </td>
                </tr>

                {{-- FOOTER --}}
                <tr>
                    <td style="padding:24px 40px 28px;text-align:center;background-color:#f8fafc;border-radius:0 0 16px 16px;">
                        <p style="margin:0 0 4px;font-size:13px;font-weight:600;color:#334155;">
                            {{ $instituto['nombre_largo'] }}
                        </p>
                        @if($instituto['direccion'])
                        <p style="margin:0 0 4px;font-size:12px;color:#94a3b8;">{{ $instituto['direccion'] }}</p>
                        @endif
                        @if($instituto['telefono'] || $instituto['email'])
                        <p style="margin:0 0 16px;font-size:12px;color:#94a3b8;">
                            @if($instituto['telefono']) Tel: {{ $instituto['telefono'] }} @endif
                            @if($instituto['telefono'] && $instituto['email']) &nbsp;&middot;&nbsp; @endif
                            @if($instituto['email']) {{ $instituto['email'] }} @endif
                        </p>
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
