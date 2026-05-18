<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Bienvenido al sistema</title>
    <!--[if mso]>
    <noscript>
        <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    </noscript>
    <![endif]-->
    <style>
        :root { color-scheme: light only; }

        @media only screen and (max-width: 600px) {
            .email-card  { border-radius: 0 !important; }
            .body-td     { padding: 24px 20px !important; }
            .footer-td   { padding: 20px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;background-color:#f0f4f8;color-scheme:light;">

@php
    if ($tipoAcceso === 'docente') {
        $headerBg       = '#84219f';
        $headerGrad     = 'linear-gradient(135deg,#84219f 0%,#9d4edd 100%)';
        $headerSubtitle = 'Tu cuenta docente ha sido creada exitosamente';
        $accentColor    = '#84219f';
        $accentLight    = '#faf5ff';
        $accentBorder   = '#e9d5ff';
        $accentText     = '#6b21a8';
        $accentDark     = '#581c87';
        $badgeBg        = '#f3e8ff';
        $badgeColor     = '#7c3aed';
        $tipoLabel      = 'Docente';
    } else {
        $headerBg       = '#0369a1';
        $headerGrad     = 'linear-gradient(135deg,#0369a1 0%,#0284c7 100%)';
        $headerSubtitle = 'Tu cuenta administrativa ha sido creada exitosamente';
        $accentColor    = '#0369a1';
        $accentLight    = '#f0f9ff';
        $accentBorder   = '#bae6fd';
        $accentText     = '#0369a1';
        $accentDark     = '#075985';
        $badgeBg        = '#e0f2fe';
        $badgeColor     = '#0369a1';
        $tipoLabel      = 'Administrativo';
    }
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background-color:#f0f4f8;padding:32px 16px;">
    <tr>
        <td align="center">

            <table role="presentation" class="email-card" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;
                          box-shadow:0 8px 40px rgba(0,0,0,0.10);">

                {{-- ══ HEADER ══════════════════════════════════════════════ --}}
                <tr>
                    <td bgcolor="{{ $headerBg }}"
                        style="background-color:{{ $headerBg }};background:{{ $headerGrad }};padding:36px 40px;text-align:center;">

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

                        <p style="margin:0 0 4px;color:rgba(255,255,255,0.80);font-size:12px;
                                  letter-spacing:0.12em;text-transform:uppercase;">
                            {{ $instituto['nombre_largo'] }}
                        </p>
                        <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:-0.5px;">
                            Bienvenido al Sistema
                        </h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.80);font-size:14px;">
                            {{ $headerSubtitle }}
                        </p>
                    </td>
                </tr>

                {{-- ══ BODY ════════════════════════════════════════════════ --}}
                <tr>
                    <td class="body-td" bgcolor="#ffffff" style="padding:36px 40px;background-color:#ffffff;">

                        {{-- Saludo --}}
                        <p style="margin:0 0 8px;color:#1e293b;font-size:16px;font-weight:600;">
                            Hola, {{ $usuario->name }}.
                        </p>
                        <p style="margin:0 0 28px;color:#64748b;font-size:14px;line-height:1.7;">
                            Se ha creado tu cuenta de acceso al sistema de gestión académica de
                            <strong>{{ $instituto['nombre_largo'] }}</strong>.
                            A continuación encontrarás tus credenciales de ingreso.
                        </p>

                        {{-- ── CREDENCIALES ──────────────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid {{ $accentBorder }};border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="{{ $accentColor }}"
                                    style="background-color:{{ $accentColor }};padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Credenciales de Acceso
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="{{ $accentLight }}" style="background-color:{{ $accentLight }};padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td bgcolor="{{ $accentLight }}"
                                                style="background-color:{{ $accentLight }};padding:10px 0;border-bottom:1px solid {{ $accentBorder }};
                                                       font-size:13px;color:{{ $accentText }};width:42%;font-weight:600;">
                                                Correo electrónico
                                            </td>
                                            <td bgcolor="{{ $accentLight }}"
                                                style="background-color:{{ $accentLight }};padding:10px 0;border-bottom:1px solid {{ $accentBorder }};
                                                       font-size:13px;font-weight:700;color:{{ $accentDark }};
                                                       text-align:right;font-family:monospace;">
                                                {{ $usuario->email }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="{{ $accentBorder }}"
                                                style="background-color:{{ $accentBorder }};padding:10px 0;
                                                       font-size:13px;color:{{ $accentText }};font-weight:600;">
                                                Contraseña temporal
                                            </td>
                                            <td bgcolor="{{ $accentBorder }}"
                                                style="background-color:{{ $accentBorder }};padding:10px 0;
                                                       font-size:14px;font-weight:800;color:{{ $accentDark }};
                                                       text-align:right;font-family:monospace;letter-spacing:0.05em;">
                                                {{ $plainPassword }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- ── DATOS DEL USUARIO ──────────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#334155" style="background-color:#334155;padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Información de tu cuenta
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td bgcolor="#f8fafc"
                                                style="background-color:#f8fafc;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                       font-size:13px;color:#475569;width:42%;">
                                                Nombre completo
                                            </td>
                                            <td bgcolor="#f8fafc"
                                                style="background-color:#f8fafc;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                       font-size:13px;font-weight:700;color:#1e293b;text-align:right;">
                                                {{ $usuario->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#eef2f7"
                                                style="background-color:#eef2f7;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                       font-size:13px;color:#475569;">
                                                Cédula
                                            </td>
                                            <td bgcolor="#eef2f7"
                                                style="background-color:#eef2f7;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                       font-size:13px;font-weight:600;color:#1e293b;text-align:right;font-family:monospace;">
                                                {{ $usuario->cedula }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#f8fafc"
                                                style="background-color:#f8fafc;padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                       font-size:13px;color:#475569;">
                                                Tipo de acceso
                                            </td>
                                            <td bgcolor="#f8fafc"
                                                style="background-color:#f8fafc;padding:9px 0;border-bottom:1px solid #e2e8f0;text-align:right;">
                                                <span style="display:inline-block;background-color:{{ $accentColor }};
                                                             color:#ffffff;font-size:12px;font-weight:700;
                                                             padding:3px 12px;border-radius:20px;">
                                                    {{ $tipoLabel }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#eef2f7"
                                                style="background-color:#eef2f7;padding:9px 0;
                                                       font-size:13px;color:#475569;">
                                                Rol asignado
                                            </td>
                                            <td bgcolor="#eef2f7"
                                                style="background-color:#eef2f7;padding:9px 0;text-align:right;">
                                                <span style="display:inline-block;background-color:{{ $badgeBg }};color:{{ $badgeColor }};
                                                             font-size:12px;font-weight:700;
                                                             padding:3px 12px;border-radius:20px;border:1px solid {{ $accentBorder }};">
                                                    {{ $nombreRol }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- ── NOTA DE SEGURIDAD ──────────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #fde68a;border-radius:10px;margin-bottom:8px;">
                            <tr>
                                <td bgcolor="#fffbeb" style="background-color:#fffbeb;padding:16px 20px;">
                                    <p style="margin:0 0 5px;color:#92400e;font-size:13px;font-weight:700;">
                                        Recomendación de seguridad
                                    </p>
                                    <p style="margin:0;color:#78350f;font-size:13px;line-height:1.6;">
                                        Por favor cambia tu contraseña al ingresar por primera vez al sistema.
                                        Guarda tus credenciales en un lugar seguro y no las compartas con nadie.
                                    </p>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- ══ CTA BUTTON ═════════════════════════════════════════ --}}
                <tr>
                    <td style="padding:24px 40px;text-align:center;background-color:#ffffff;">
                        <a href="{{ $instituto['url_portal'] }}"
                           style="display:inline-block;background-color:{{ $accentColor }};color:#ffffff !important;
                                  text-decoration:none;padding:14px 36px;border-radius:8px;
                                  font-size:14px;font-weight:700;letter-spacing:0.02em;">
                            Ingresar al Sistema
                        </a>
                    </td>
                </tr>

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
