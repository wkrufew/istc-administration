<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Beca Revocada</title>
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
                    <td bgcolor="#7c2d12"
                        style="background-color:#7c2d12;background:linear-gradient(135deg,#7c2d12 0%,#c2410c 100%);
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
                            Ajuste de Colegiatura
                        </h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.80);font-size:14px;">
                            Actualización de tus obligaciones financieras
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
                            Te informamos que la beca <strong>{{ $beca->tipoBeca->nombre }}</strong> que tenías asignada
                            ha sido revocada. Tus cuotas de colegiatura han sido ajustadas al valor original.
                        </p>

                        {{-- DETALLE AJUSTE --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #fed7aa;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#c2410c" style="background-color:#c2410c;padding:13px 20px;">
                                    <p style="margin:0;color:#ffffff;font-size:12px;font-weight:700;
                                               letter-spacing:0.10em;text-transform:uppercase;">
                                        Detalle del Ajuste
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:13px;color:#9a3412;width:48%;">Beca Revocada</td>
                                            <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:13px;font-weight:700;color:#7c2d12;text-align:right;">
                                                {{ $beca->tipoBeca->nombre }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#fde8d0" style="background-color:#fde8d0;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:13px;color:#9a3412;">Descuento que Aplicaba</td>
                                            <td bgcolor="#fde8d0" style="background-color:#fde8d0;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:20px;font-weight:800;color:#c2410c;text-align:right;">
                                                {{ $beca->porcentaje_aplicado }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:13px;color:#9a3412;">Total Original Colegiatura</td>
                                            <td bgcolor="#fff7ed" style="background-color:#fff7ed;padding:9px 0;border-bottom:1px solid #fed7aa;
                                                        font-size:13px;font-weight:700;color:#7c2d12;text-align:right;">
                                                ${{ number_format($totalOriginal, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#fde8d0" style="background-color:#fde8d0;padding:9px 0;font-size:13px;color:#9a3412;">
                                                Ya Abonado
                                            </td>
                                            <td bgcolor="#fde8d0" style="background-color:#fde8d0;padding:9px 0;
                                                        font-size:13px;font-weight:600;color:#7c2d12;text-align:right;">
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
                                        Nuevas Cuotas a Pagar
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
                                            <td bgcolor="#c2410c" style="background-color:#c2410c;padding:11px 0;
                                                        font-size:13px;font-weight:700;color:#ffffff;">
                                                Total Pendiente
                                            </td>
                                            <td bgcolor="#c2410c" style="background-color:#c2410c;padding:11px 0;
                                                        font-size:16px;font-weight:800;color:#ffffff;text-align:right;">
                                                ${{ number_format($obligacionesPendientes->sum('monto_final'), 2) }}
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
                                        Si tienes alguna consulta sobre este ajuste, comunícate con la secretaría
                                        académica. Puedes revisar el estado de tus obligaciones en el portal estudiantil.
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
                           style="display:inline-block;background-color:#334155;color:#ffffff !important;
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
