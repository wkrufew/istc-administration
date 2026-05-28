<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>¡Feliz cumpleaños!</title>
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
            .text-dark         { color: #1e293b !important; }
            .text-muted        { color: #64748b !important; }
        }
        @media only screen and (max-width: 600px) {
            .email-card { border-radius: 0 !important; }
            .body-td    { padding: 28px 20px !important; }
            .footer-td  { padding: 20px !important; }
        }
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
                    <td style="background:linear-gradient(135deg,#4a1d96 0%,#6d28d9 55%,#a855f7 100%);padding:40px 40px 32px;text-align:center;">

                        @if($instituto['logo_url'])
                            <img src="{{ $instituto['logo_url'] }}"
                                 alt="{{ $instituto['nombre_corto'] }}"
                                 width="56" height="56"
                                 style="height:56px;width:auto;display:block;margin:0 auto 16px;object-fit:contain;border:0;">
                        @else
                            <div style="width:56px;height:56px;background-color:rgba(255,255,255,0.20);border-radius:50%;display:inline-block;line-height:56px;margin-bottom:16px;font-size:22px;font-weight:800;color:#ffffff;text-align:center;">
                                {{ strtoupper(substr($instituto['nombre_corto'], 0, 2)) }}
                            </div>
                        @endif

                        <p style="margin:0 0 10px;font-size:13px;color:rgba(255,255,255,0.75);letter-spacing:0.12em;text-transform:uppercase;font-weight:600;">
                            {{ $instituto['nombre_largo'] }}
                        </p>

                        {{-- Emojis celebratorios --}}
                        <div style="font-size:36px;line-height:1;margin:10px 0 2px;letter-spacing:4px;">🎉 🎂 🎈</div>

                        <h1 style="margin:10px 0 4px;font-size:28px;font-weight:800;color:#ffffff;letter-spacing:-0.02em;">
                            ¡Hoy es tu día!
                        </h1>
                        <p style="margin:0;font-size:14px;color:rgba(255,255,255,0.80);">
                            {{ now()->format('d \d\e F \d\e Y') }}
                        </p>
                    </td>
                </tr>

                {{-- ══ BODY ══ --}}
                <tr>
                    <td class="body-td" style="padding:36px 40px 28px;background-color:#ffffff;">

                        @php $primerNombre = explode(' ', trim($usuario->first_name ?? $usuario->name))[0]; @endphp

                        <p style="margin:0 0 6px;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.1em;font-weight:600;">
                            ¡Felicidades!
                        </p>
                        <h2 style="margin:0 0 24px;font-size:22px;font-weight:700;color:#1e293b;">
                            {{ $usuario->name }} 🌟
                        </h2>

                        <p style="margin:0 0 16px;font-size:15px;color:#374151;line-height:1.7;">
                            ¡Hola <strong style="color:#7c3aed;">{{ $primerNombre }}</strong>!
                            Hoy cumples <strong style="color:#7c3aed;">{{ $edad }} años</strong> y desde
                            <strong style="color:#7c3aed;">{{ $instituto['nombre_largo'] }}</strong>
                            queremos que este día sea tan especial como tú lo eres para nosotros.
                        </p>

                        <p style="margin:0 0 28px;font-size:15px;color:#374151;line-height:1.7;">
                            Estamos orgullosos de acompañarte en esta etapa de tu vida académica.
                            Cada esfuerzo que realizas, cada meta que alcanzas, nos llena de satisfacción.
                            ¡Sigue adelante, el éxito ya te pertenece!
                        </p>

                        {{-- Tarjeta de datos del estudiante --}}
                        @if($carrera || $periodo)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:linear-gradient(135deg,#faf5ff,#f3e8ff);border:1px solid #e9d5ff;border-radius:12px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:12px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;text-align:center;">
                                        Tu perfil estudiantil
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        @if($carrera)
                                        <tr>
                                            <td style="padding:6px 0;border-bottom:1px solid #e9d5ff;">
                                                <span style="font-size:11px;color:#9333ea;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">📚 Carrera</span>
                                            </td>
                                            <td style="padding:6px 0;border-bottom:1px solid #e9d5ff;text-align:right;">
                                                <span style="font-size:13px;color:#4a1d96;font-weight:600;">{{ $carrera }}</span>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($periodo)
                                        <tr>
                                            <td style="padding:6px 0;">
                                                <span style="font-size:11px;color:#9333ea;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">📅 Período</span>
                                            </td>
                                            <td style="padding:6px 0;text-align:right;">
                                                <span style="font-size:13px;color:#4a1d96;font-weight:600;">{{ $periodo }}</span>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>
                            </tr>
                        </table>
                        @else
                        {{-- Sin matrícula activa: tarjeta solo con los años --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:linear-gradient(135deg,#faf5ff,#f3e8ff);border:1px solid #e9d5ff;border-radius:12px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:20px 24px;text-align:center;">
                                    <div style="font-size:42px;font-weight:800;color:#7c3aed;line-height:1;">{{ $edad }}</div>
                                    <div style="font-size:14px;color:#9333ea;font-weight:600;margin-top:4px;">años de vida 🎊</div>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- Mensaje motivador --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="margin-bottom:28px;">
                            <tr>
                                <td style="border-left:3px solid #a855f7;padding:12px 20px;">
                                    <p style="margin:0;font-size:15px;color:#374151;line-height:1.7;font-style:italic;">
                                        "La educación es el arma más poderosa que puedes usar para cambiar el mundo."
                                    </p>
                                    <p style="margin:6px 0 0;font-size:12px;color:#94a3b8;font-weight:600;">— Nelson Mandela</p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 32px;font-size:15px;color:#374151;line-height:1.7;text-align:center;">
                            ¡Que este nuevo año de vida esté repleto de logros, aprendizajes y momentos inolvidables! 🚀
                        </p>

                        {{-- Botón --}}
                        <div style="text-align:center;margin-bottom:8px;">
                            <a href="{{ $instituto['url_portal'] }}"
                               style="display:inline-block;background:linear-gradient(135deg,#6d28d9,#a855f7);color:#ffffff;text-decoration:none;padding:13px 32px;border-radius:50px;font-size:14px;font-weight:700;letter-spacing:0.04em;">
                                Ver mi portal estudiantil
                            </a>
                        </div>

                    </td>
                </tr>

                {{-- ══ FOOTER ══ --}}
                <tr>
                    <td class="footer-td" style="background-color:#f8fafc;border-top:1px solid #e2e8f0;padding:24px 40px;text-align:center;">
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
                        <p style="margin:12px 0 0;font-size:10px;color:#cbd5e1;">
                            Este mensaje fue generado automáticamente por el sistema de gestión institucional.
                        </p>
                    </td>
                </tr>

                {{-- Franja de colores --}}
                <tr>
                    <td style="height:4px;background:linear-gradient(90deg,#4a1d96,#7c3aed,#a855f7,#f0abfc);"></td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
