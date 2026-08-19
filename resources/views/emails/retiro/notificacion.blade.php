<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Notificación de Retiro</title>
    <!--[if mso]>
    <noscript>
        <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    </noscript>
    <![endif]-->
    <style>
        :root { color-scheme: light only; }
        @media (prefers-color-scheme: dark) {
            body, .email-outer   { background-color: #eef2f7 !important; }
            .email-card          { background-color: #ffffff !important; }
            .email-body-td       { background-color: #ffffff !important; }
            .section-card        { background-color: #f8fafc !important; }
            .text-main           { color: #1e293b !important; }
            .text-muted          { color: #64748b !important; }
            .text-label          { color: #94a3b8 !important; }
            .divider-line        { background-color: #e2e8f0 !important; }
            .footer-td           { background-color: #f8fafc !important; }
            .footer-name         { color: #334155 !important; }
            .footer-sub          { color: #94a3b8 !important; }
            .outer-note          { color: #94a3b8 !important; }
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper { width: 100% !important; }
            .email-card    { border-radius: 0 !important; }
            .email-body    { padding: 24px 20px !important; }
            .email-footer  { padding: 20px !important; }
            .info-grid td  { display: block !important; width: 100% !important; text-align: left !important; }
        }
    </style>
</head>
<body class="email-outer" style="margin:0;padding:0;background-color:#eef2f7 !important;font-family:'Segoe UI',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;color-scheme:light;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background-color:#eef2f7;padding:32px 16px;" class="email-outer">
    <tr>
        <td align="center">

            <table role="presentation" class="email-wrapper email-card" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;
                          box-shadow:0 8px 40px rgba(0,0,0,0.10);">

                {{-- HEADER --}}
                <tr>
                    <td bgcolor="#7c2d12"
                        style="background-color:#7c2d12;background:linear-gradient(135deg,#7c2d12 0%,#c2410c 100%);padding:36px 40px 28px;text-align:center;">
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

                {{-- STATUS BANNER --}}
                <tr>
                    <td bgcolor="#c2410c"
                        style="background-color:#c2410c;padding:11px 40px;text-align:center;">
                        <span style="color:#ffffff;font-size:13px;font-weight:700;
                                     text-transform:uppercase;letter-spacing:0.12em;">
                            Retiro de Matrícula Registrado
                        </span>
                    </td>
                </tr>

                {{-- BODY --}}
                <tr>
                    <td class="email-body email-body-td" style="padding:36px 40px 0;background-color:#ffffff;">

                        <p class="text-main" style="margin:0 0 6px;font-size:17px;font-weight:600;color:#0f2d5a;">
                            Estimado/a {{ $estudiante->name }},
                        </p>
                        <p class="text-muted" style="margin:0 0 28px;font-size:14px;color:#64748b;line-height:1.7;">
                            Le informamos que se ha registrado el <strong style="color:#7c2d12;">retiro</strong>
                            de su matrícula en el período académico
                            <strong style="color:#0f2d5a;">{{ $matricula->periodo?->code }}</strong>.
                            A continuación encontrará el detalle de esta notificación.
                        </p>

                        {{-- DATOS DEL RETIRO --}}
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
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">Código de Matrícula</td>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:700;color:#0f2d5a;text-align:right;">
                                                <span style="font-family:monospace;background-color:#e8f0fe;color:#1d4ed8;padding:2px 8px;border-radius:4px;font-size:13px;">
                                                    {{ $matricula->code }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">Carrera</td>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#1e293b;text-align:right;">
                                                {{ $matricula->carrera?->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">Período Académico</td>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#1e293b;text-align:right;">
                                                {{ $matricula->periodo?->code }}
                                            </td>
                                        </tr>
                                        @if($estudiante->cedula)
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">Cédula</td>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#1e293b;text-align:right;">
                                                {{ $estudiante->cedula }}
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b;">Fecha de Retiro</td>
                                            <td style="padding:9px 0;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#1e293b;text-align:right;">
                                                {{ $retiro?->fecha_retiro?->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                        @if($retiro?->motivo)
                                        <tr>
                                            <td style="padding:9px 0;font-size:13px;color:#64748b;vertical-align:top;">Motivo</td>
                                            <td style="padding:9px 0;font-size:13px;color:#1e293b;text-align:right;line-height:1.5;">
                                                {{ $retiro->motivo }}
                                            </td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td style="padding:9px 0;font-size:13px;color:#64748b;">Motivo</td>
                                            <td style="padding:9px 0;font-size:13px;color:#94a3b8;text-align:right;">No especificado</td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- MATERIAS RETIRADAS --}}
                        @if($detalles->count())
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               class="section-card"
                               style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p class="text-label" style="margin:0 0 14px;font-size:11px;font-weight:700;color:#94a3b8;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Materias Marcadas como Retirado
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        @foreach($detalles as $detalle)
                                        <tr>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:13px;color:#1e293b;">
                                                {{ $detalle->materia?->name ?? '—' }}
                                            </td>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;text-align:right;">
                                                <span style="font-size:11px;font-weight:700;background-color:#fee2e2;color:#b91c1c;
                                                             padding:2px 8px;border-radius:20px;">
                                                    Retirado
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- AVISO RECARGO --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#fff7ed;border:1px solid #fed7aa;border-radius:10px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0;font-size:13px;color:#92400e;line-height:1.6;">
                                        <strong>Importante:</strong> Si desea re-matricularse en un período futuro,
                                        se aplicará un recargo del <strong>10%</strong> sobre el costo de matrícula
                                        correspondiente (reintegro por retiro voluntario).
                                    </p>
                                </td>
                            </tr>
                        </table>

                        {{-- CONTACTO --}}
                        <p class="text-muted" style="margin:0 0 28px;font-size:13px;color:#64748b;line-height:1.7;">
                            Si tiene alguna consulta sobre este proceso, comuníquese con la secretaría académica
                            @if($instituto['email'])
                                en <a href="mailto:{{ $instituto['email'] }}" style="color:#1d4ed8;text-decoration:none;">{{ $instituto['email'] }}</a>
                            @endif
                            @if($instituto['telefono'])
                                o al teléfono <strong style="color:#1e293b;">{{ $instituto['telefono'] }}</strong>
                            @endif
                            .
                        </p>

                    </td>
                </tr>

                {{-- FOOTER --}}
                <tr>
                    <td class="email-footer footer-td" style="background-color:#f8fafc;padding:28px 40px;border-top:1px solid #e2e8f0;">
                        <p class="footer-name text-label" style="margin:0 0 4px;font-size:13px;font-weight:700;color:#334155;text-align:center;">
                            {{ $instituto['nombre_largo'] }}
                        </p>
                        @if($instituto['direccion'])
                        <p class="footer-sub text-label" style="margin:0 0 4px;font-size:12px;color:#94a3b8;text-align:center;">
                            {{ $instituto['direccion'] }}
                        </p>
                        @endif
                        @if($instituto['web'])
                        <p class="footer-sub" style="margin:0;font-size:12px;text-align:center;">
                            <a href="{{ $instituto['web'] }}" style="color:#1d4ed8;text-decoration:none;">{{ $instituto['web'] }}</a>
                        </p>
                        @endif
                        <p class="outer-note" style="margin:20px 0 0;font-size:11px;color:#94a3b8;text-align:center;line-height:1.5;">
                            Este es un correo generado automáticamente por el Sistema de Gestión Académica.
                            Por favor no responda a este mensaje.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
