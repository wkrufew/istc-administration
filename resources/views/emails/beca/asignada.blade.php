<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Beca Asignada</title>
    <style>
        :root { color-scheme: light only; }
        @media only screen and (max-width: 600px) {
            .email-card { border-radius: 0 !important; }
            .body-td    { padding: 24px 20px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;background-color:#f0f4f8;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background-color:#f0f4f8;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" class="email-card" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;
                          box-shadow:0 8px 40px rgba(0,0,0,0.10);">

                {{-- HEADER --}}
                <tr>
                    <td bgcolor="#1a4e8a"
                        style="background-color:#1a4e8a;background:linear-gradient(135deg,#1a4e8a 0%,#2563eb 100%);
                               padding:36px 40px;text-align:center;">
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
                            🎓 Beca Asignada
                        </h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.80);font-size:14px;">
                            Se ha aplicado un descuento a tu colegiatura
                        </p>
                    </td>
                </tr>

                {{-- BODY --}}
                <tr>
                    <td bgcolor="#ffffff" class="body-td" style="padding:36px 40px;background-color:#ffffff;">

                        <p style="margin:0 0 8px;color:#1e293b;font-size:16px;font-weight:600;">
                            Hola, {{ $beca->estudiante->name }}.
                        </p>
                        <p style="margin:0 0 28px;color:#64748b;font-size:14px;line-height:1.7;">
                            Nos complace informarte que se te ha asignado una beca para el período académico actual.
                            Tu colegiatura ha sido recalculada con el descuento correspondiente.
                        </p>

                        {{-- DETALLE BECA --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #bfdbfe;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#1d4ed8" style="background-color:#1d4ed8;padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Detalle de la Beca
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#eff6ff" style="background-color:#eff6ff;padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td bgcolor="#eff6ff" style="background-color:#eff6ff;padding:9px 0;border-bottom:1px solid #bfdbfe;
                                                        font-size:13px;color:#1e40af;width:48%;">Tipo de Beca</td>
                                            <td bgcolor="#eff6ff" style="background-color:#eff6ff;padding:9px 0;border-bottom:1px solid #bfdbfe;
                                                        font-size:13px;font-weight:700;color:#1e3a8a;text-align:right;">
                                                {{ $beca->tipoBeca->nombre }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#dbeafe" style="background-color:#dbeafe;padding:9px 0;border-bottom:1px solid #bfdbfe;
                                                        font-size:13px;color:#1e40af;">Descuento Aplicado</td>
                                            <td bgcolor="#dbeafe" style="background-color:#dbeafe;padding:9px 0;border-bottom:1px solid #bfdbfe;
                                                        font-size:28px;font-weight:800;color:#1d4ed8;text-align:right;">
                                                {{ $beca->porcentaje_aplicado }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#eff6ff" style="background-color:#eff6ff;padding:9px 0;border-bottom:1px solid #bfdbfe;
                                                        font-size:13px;color:#1e40af;">Fecha de Asignación</td>
                                            <td bgcolor="#eff6ff" style="background-color:#eff6ff;padding:9px 0;border-bottom:1px solid #bfdbfe;
                                                        font-size:13px;font-weight:600;color:#1e3a8a;text-align:right;">
                                                {{ \Carbon\Carbon::parse($beca->fecha_asignacion)->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#dbeafe" style="background-color:#dbeafe;padding:9px 0;font-size:13px;color:#1e40af;">
                                                Ya Abonado
                                            </td>
                                            <td bgcolor="#dbeafe" style="background-color:#dbeafe;padding:9px 0;
                                                        font-size:13px;font-weight:600;color:#1e3a8a;text-align:right;">
                                                ${{ number_format($totalPagado, 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- CUOTAS PENDIENTES --}}
                        @if($obligacionesPendientes->isNotEmpty())
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#334155" style="background-color:#334155;padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Cuotas Pendientes con Descuento
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#f8fafc" style="background-color:#f8fafc;padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        @foreach($obligacionesPendientes as $i => $ob)
                                        <tr>
                                            <td bgcolor="{{ $i % 2 === 0 ? '#f8fafc' : '#eef2f7' }}"
                                                style="background-color:{{ $i % 2 === 0 ? '#f8fafc' : '#eef2f7' }};
                                                       padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                       font-size:13px;color:#475569;">
                                                Cuota {{ $i + 1 }}
                                                @if($ob->fecha_vencimiento)
                                                    &nbsp;·&nbsp; Vence {{ \Carbon\Carbon::parse($ob->fecha_vencimiento)->format('d/m/Y') }}
                                                @endif
                                            </td>
                                            <td bgcolor="{{ $i % 2 === 0 ? '#f8fafc' : '#eef2f7' }}"
                                                style="background-color:{{ $i % 2 === 0 ? '#f8fafc' : '#eef2f7' }};
                                                       padding:9px 0;border-bottom:1px solid #e2e8f0;
                                                       font-size:13px;font-weight:700;color:#1e293b;text-align:right;">
                                                ${{ number_format((float)$ob->monto_final, 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td bgcolor="#1d4ed8" style="background-color:#1d4ed8;padding:11px 0;
                                                        font-size:13px;font-weight:700;color:#ffffff;">
                                                Total con Beca
                                            </td>
                                            <td bgcolor="#1d4ed8" style="background-color:#1d4ed8;padding:11px 0;
                                                        font-size:16px;font-weight:800;color:#ffffff;text-align:right;">
                                                ${{ number_format($totalConDescuento, 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- NOTA --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #fde68a;border-radius:10px;">
                            <tr>
                                <td bgcolor="#fffbeb" style="background-color:#fffbeb;padding:16px 20px;">
                                    <p style="margin:0 0 5px;color:#92400e;font-size:13px;font-weight:700;">
                                        Información importante
                                    </p>
                                    <p style="margin:0;color:#78350f;font-size:13px;line-height:1.6;">
                                        El descuento aplica a las cuotas de colegiatura del período actual.
                                        Puedes revisar el estado de tus obligaciones en el portal estudiantil.
                                    </p>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- CTA --}}
                <tr>
                    <td style="padding:24px 40px;text-align:center;background-color:#ffffff;">
                        <a href="{{ $instituto['url_portal'] }}"
                           style="display:inline-block;background-color:#1d4ed8;color:#ffffff !important;
                                  text-decoration:none;padding:14px 36px;border-radius:8px;
                                  font-size:14px;font-weight:700;letter-spacing:0.02em;">
                            Ver mis obligaciones
                        </a>
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
