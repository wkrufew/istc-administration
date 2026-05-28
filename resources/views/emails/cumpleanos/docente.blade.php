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
                    <td style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 60%,#2563eb 100%);padding:40px 40px 32px;text-align:center;">

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

                        <div style="font-size:48px;line-height:1;margin:10px 0 6px;">🎂</div>

                        <h1 style="margin:8px 0 4px;font-size:26px;font-weight:800;color:#ffffff;letter-spacing:-0.02em;">
                            ¡Feliz cumpleaños, Docente!
                        </h1>
                        <p style="margin:0;font-size:14px;color:rgba(255,255,255,0.80);">
                            {{ now()->format('d \d\e F \d\e Y') }}
                        </p>
                    </td>
                </tr>

                {{-- ══ BODY ══ --}}
                <tr>
                    <td class="body-td" style="padding:36px 40px 28px;background-color:#ffffff;">

                        <p style="margin:0 0 6px;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.1em;font-weight:600;">
                            Estimado/a docente
                        </p>
                        <h2 style="margin:0 0 24px;font-size:22px;font-weight:700;color:#1e293b;">
                            {{ $usuario->name }}
                        </h2>

                        <p style="margin:0 0 16px;font-size:15px;color:#374151;line-height:1.7;">
                            En este día tan especial, el equipo del
                            <strong style="color:#1d4ed8;">{{ $instituto['nombre_largo'] }}</strong>
                            le hace llegar nuestros más cálidos deseos de felicidad y bienestar.
                        </p>

                        <p style="margin:0 0 28px;font-size:15px;color:#374151;line-height:1.7;">
                            Su labor como docente va más allá del aula: cada conocimiento compartido, cada estudiante orientado
                            y cada esfuerzo realizado deja una huella permanente en quienes tenemos el privilegio de contar con usted.
                            <strong style="color:#1d4ed8;">Gracias por transformar vidas a través de la educación.</strong>
                        </p>

                        {{-- Tarjeta de celebración --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #bfdbfe;border-radius:12px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:20px 24px;text-align:center;">
                                    <div style="font-size:36px;margin-bottom:8px;">🏆</div>
                                    <p style="margin:0 0 4px;font-size:13px;color:#1d4ed8;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;">
                                        Celebrando {{ $edad }} años de vida
                                    </p>
                                    <p style="margin:0;font-size:14px;color:#1e40af;line-height:1.6;">
                                        Que cada uno de esos años represente la sabiduría y experiencia<br>
                                        que comparte generosamente con sus estudiantes.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        {{-- Cita inspiradora --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="margin-bottom:28px;">
                            <tr>
                                <td style="border-left:3px solid #3b82f6;padding:12px 20px;">
                                    <p style="margin:0;font-size:15px;color:#374151;line-height:1.7;font-style:italic;">
                                        "Un buen maestro puede inspirar esperanza, encender la imaginación y despertar el amor por el aprendizaje."
                                    </p>
                                    <p style="margin:6px 0 0;font-size:12px;color:#94a3b8;font-weight:600;">— Brad Henry</p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 32px;font-size:15px;color:#374151;line-height:1.7;">
                            Le deseamos que este nuevo año de vida esté lleno de salud, prosperidad y momentos de alegría.
                            ¡Que siga iluminando el camino de muchos más estudiantes!
                        </p>

                        {{-- Botón --}}
                        <div style="text-align:center;margin-bottom:8px;">
                            <a href="{{ $instituto['url_portal'] }}"
                               style="display:inline-block;background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#ffffff;text-decoration:none;padding:13px 32px;border-radius:50px;font-size:14px;font-weight:700;letter-spacing:0.04em;">
                                Acceder al portal docente
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
                    <td style="height:4px;background:linear-gradient(90deg,#1e3a8a,#2563eb,#60a5fa,#93c5fd);"></td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
