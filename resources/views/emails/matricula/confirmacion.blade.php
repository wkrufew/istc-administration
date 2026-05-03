<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @if($esEdicion) Actualización de Matrícula
        @elseif($esPrimerMatricula) Bienvenido — Matrícula Confirmada
        @else Confirmación de Matrícula
        @endif
    </title>
    <!--[if mso]>
    <noscript>
        <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    </noscript>
    <![endif]-->
    <style>
        @media only screen and (max-width:600px) {
            .email-wrapper  { width:100% !important; }
            .email-card     { border-radius:0 !important; }
            .email-body     { padding:24px 20px !important; }
            .email-footer   { padding:20px !important; }
            .btn-cta        { display:block !important; text-align:center !important; }
            .info-grid td   { display:block !important; width:100% !important; text-align:left !important; }
            .payment-row td { display:block !important; width:100% !important; text-align:left !important; padding-bottom:8px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#eef2f7;font-family:'Segoe UI',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

{{-- ─── OUTER WRAPPER ─────────────────────────────────────────────────────── --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef2f7;padding:32px 16px;">
    <tr>
        <td align="center">

            {{-- ─── CARD ──────────────────────────────────────────────── --}}
            <table role="presentation" class="email-wrapper email-card" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;
                          box-shadow:0 8px 40px rgba(0,0,0,0.10);">

                {{-- ══ HEADER ══════════════════════════════════════════════ --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#0f2d5a 0%,#1e56b0 100%);padding:36px 40px 28px;text-align:center;">
                        @if($instituto['logo_url'])
                            <img src="{{ $instituto['logo_url'] }}"
                                 alt="{{ $instituto['nombre_corto'] }}"
                                 style="height:56px;display:block;margin:0 auto 16px;object-fit:contain;">
                        @else
                            <div style="width:56px;height:56px;background:rgba(255,255,255,0.15);border-radius:12px;
                                        display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;
                                        font-size:22px;font-weight:800;color:#fff;letter-spacing:-1px;">
                                {{ strtoupper(substr($instituto['nombre_corto'], 0, 2)) }}
                            </div>
                        @endif
                        <h1 style="margin:0;color:#ffffff;font-size:18px;font-weight:700;line-height:1.3;
                                   letter-spacing:-0.3px;">{{ $instituto['nombre_largo'] }}</h1>
                        <p style="margin:6px 0 0;color:rgba(255,255,255,0.60);font-size:12px;
                                  text-transform:uppercase;letter-spacing:0.12em;">
                            Sistema de Gestión Académica
                        </p>
                    </td>
                </tr>

                {{-- ══ STATUS BANNER ═══════════════════════════════════════ --}}
                <tr>
                    <td style="background:{{ $esEdicion ? '#d97706' : ($esPrimerMatricula ? '#059669' : '#1e56b0') }};
                               padding:11px 40px;text-align:center;">
                        <span style="color:#ffffff;font-size:13px;font-weight:600;
                                     text-transform:uppercase;letter-spacing:0.12em;">
                            @if($esEdicion)
                                Matrícula Actualizada
                            @elseif($esPrimerMatricula)
                                ¡Bienvenido! — Primera Matrícula
                            @else
                                Matrícula Confirmada
                            @endif
                        </span>
                    </td>
                </tr>

                {{-- ══ BODY ════════════════════════════════════════════════ --}}
                <tr>
                    <td class="email-body" style="padding:36px 40px 0;">

                        {{-- Saludo --}}
                        <p style="margin:0 0 6px;font-size:17px;font-weight:600;color:#0f2d5a;">
                            Estimado/a {{ $estudiante->name }},
                        </p>
                        <p style="margin:0 0 28px;font-size:14px;color:#64748b;line-height:1.7;">
                            @if($esEdicion)
                                Le informamos que su matrícula en el período académico
                                <strong style="color:#0f2d5a;">{{ $matricula->periodo?->code }}</strong>
                                ha sido actualizada exitosamente. A continuación encontrará el resumen con los cambios realizados.
                            @elseif($esPrimerMatricula)
                                Le damos la más cordial bienvenida al
                                <strong style="color:#0f2d5a;">{{ $instituto['nombre_largo'] }}</strong>.
                                Su proceso de matrícula ha sido completado exitosamente. A continuación encontrará
                                los detalles de su inscripción y sus credenciales para acceder al portal estudiantil.
                            @else
                                Su matrícula para el período académico
                                <strong style="color:#0f2d5a;">{{ $matricula->periodo?->code }}</strong>
                                ha sido confirmada exitosamente. A continuación encontrará el resumen de su inscripción.
                            @endif
                        </p>

                        {{-- ── DATOS DE MATRÍCULA ────────────────────── --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:11px;font-weight:700;color:#94a3b8;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Datos de Matrícula
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#64748b;">Código</td>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;font-weight:700;color:#0f2d5a;
                                                        text-align:right;">
                                                <span style="font-family:monospace;background:#e8f0fe;
                                                             padding:2px 8px;border-radius:4px;">
                                                    {{ $matricula->code }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#64748b;">Carrera</td>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;font-weight:600;color:#1e293b;
                                                        text-align:right;">{{ $matricula->carrera?->name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#64748b;">Período Académico</td>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;font-weight:600;color:#1e293b;
                                                        text-align:right;">{{ $matricula->periodo?->code }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;
                                                        font-size:13px;color:#64748b;">Tipo de Matrícula</td>
                                            <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;
                                                        text-align:right;">
                                                @php
                                                    $tipoBg    = match($matricula->tipo) {
                                                        'Nueva'     => '#dcfce7',
                                                        'Arrastre'  => '#fef3c7',
                                                        default     => '#dbeafe',
                                                    };
                                                    $tipoColor = match($matricula->tipo) {
                                                        'Nueva'     => '#15803d',
                                                        'Arrastre'  => '#92400e',
                                                        default     => '#1d4ed8',
                                                    };
                                                @endphp
                                                <span style="font-size:12px;font-weight:600;
                                                             background:{{ $tipoBg }};color:{{ $tipoColor }};
                                                             padding:3px 10px;border-radius:20px;">
                                                    {{ $matricula->tipo }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;font-size:13px;color:#64748b;">Fecha de Matrícula</td>
                                            <td style="padding:8px 0;font-size:13px;font-weight:600;
                                                        color:#1e293b;text-align:right;">
                                                {{ $matricula->fecha_matricula?->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        {{-- ── MATERIAS INSCRITAS ────────────────────── --}}
                        @if($matricula->detalles->isNotEmpty())
                        <p style="margin:0 0 12px;font-size:11px;font-weight:700;color:#94a3b8;
                                   text-transform:uppercase;letter-spacing:0.13em;">
                            Materias Inscritas
                        </p>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;margin-bottom:24px;">
                            <thead>
                                <tr style="background:#0f2d5a;">
                                    <th style="padding:10px 16px;text-align:left;font-size:11px;
                                               font-weight:600;color:rgba(255,255,255,0.8);
                                               text-transform:uppercase;letter-spacing:0.08em;">#</th>
                                    <th style="padding:10px 16px;text-align:left;font-size:11px;
                                               font-weight:600;color:rgba(255,255,255,0.8);
                                               text-transform:uppercase;letter-spacing:0.08em;">Materia</th>
                                    <th style="padding:10px 16px;text-align:center;font-size:11px;
                                               font-weight:600;color:rgba(255,255,255,0.8);
                                               text-transform:uppercase;letter-spacing:0.08em;">Tipo</th>
                                    <th style="padding:10px 16px;text-align:center;font-size:11px;
                                               font-weight:600;color:rgba(255,255,255,0.8);
                                               text-transform:uppercase;letter-spacing:0.08em;">Créditos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matricula->detalles as $i => $detalle)
                                <tr style="background:{{ $i % 2 === 0 ? '#f8fafc' : '#ffffff' }};
                                           border-top:1px solid #e2e8f0;">
                                    <td style="padding:10px 16px;font-size:13px;color:#94a3b8;">{{ $i + 1 }}</td>
                                    <td style="padding:10px 16px;font-size:13px;color:#1e293b;">
                                        {{ $detalle->materia?->name }}
                                    </td>
                                    <td style="padding:10px 16px;text-align:center;">
                                        @if($detalle->tipo === 'Arrastre')
                                            <span style="font-size:11px;font-weight:600;
                                                         background:#fef3c7;color:#92400e;
                                                         padding:2px 8px;border-radius:20px;">
                                                Arrastre
                                            </span>
                                        @else
                                            <span style="font-size:11px;font-weight:600;
                                                         background:#dcfce7;color:#15803d;
                                                         padding:2px 8px;border-radius:20px;">
                                                Normal
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding:10px 16px;font-size:13px;font-weight:600;
                                               color:#0f2d5a;text-align:center;">
                                        {{ number_format((($detalle->materia?->horas_teoricas ?? 0) + ($detalle->materia?->horas_practicas ?? 0)) / 48, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                        {{-- ── CREDENCIALES (sólo primera matrícula, no edición) ─ --}}
                        @if($esPrimerMatricula && !$esEdicion)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:#eff6ff;border:2px solid #93c5fd;border-radius:10px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 14px;font-size:11px;font-weight:700;color:#1d4ed8;
                                               text-transform:uppercase;letter-spacing:0.13em;">
                                        Credenciales de Acceso al Portal
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:8px 0;border-bottom:1px solid #bfdbfe;
                                                        font-size:13px;color:#3b82f6;">
                                                Usuario (correo electrónico)
                                            </td>
                                            <td style="padding:8px 0;border-bottom:1px solid #bfdbfe;
                                                        font-size:13px;font-weight:700;color:#1e3a8a;
                                                        text-align:right;font-family:monospace;">
                                                {{ $estudiante->email }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;font-size:13px;color:#3b82f6;">
                                                Contraseña inicial
                                            </td>
                                            <td style="padding:8px 0;font-size:13px;font-weight:700;
                                                        color:#1e3a8a;text-align:right;font-family:monospace;">
                                                {{ $estudiante->cedula ?? '(su número de cédula)' }}
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:14px 0 0;font-size:12px;color:#3b82f6;line-height:1.6;">
                                        Por su seguridad, le recomendamos cambiar su contraseña en el primer inicio de sesión.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- ── PAGO INMEDIATO: MATRÍCULA ────────────────── --}}
                        @if($obligacion && !$esEdicion)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:linear-gradient(135deg,#064e3b 0%,#059669 100%);
                                      border-radius:10px;margin-bottom:16px;">
                            <tr>
                                <td style="padding:22px 28px;">
                                    <p style="margin:0 0 16px;font-size:11px;font-weight:700;
                                               color:rgba(255,255,255,0.65);text-transform:uppercase;
                                               letter-spacing:0.13em;">
                                        Pago Inmediato — Matrícula
                                    </p>
                                    <table role="presentation" class="payment-row" width="100%"
                                           cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.7);">
                                                    Valor a cancelar
                                                </p>
                                                <p style="margin:4px 0 0;font-size:32px;font-weight:800;
                                                           color:#ffffff;line-height:1;">${{ $monto }}</p>
                                            </td>
                                            <td style="text-align:right;vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.7);">
                                                    Fecha límite
                                                </p>
                                                <p style="margin:4px 0 0;font-size:20px;font-weight:700;
                                                           color:#ffffff;">{{ $fechaLimite ?? '—' }}</p>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="margin-top:16px;padding-top:14px;
                                                border-top:1px solid rgba(255,255,255,0.20);">
                                        <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.65);
                                                   line-height:1.6;">
                                            Realice este pago antes de la fecha límite para garantizar su cupo.
                                            Comuníquese con secretaría para más información.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- ── ARANCEL SEMESTRAL ────────────────────────── --}}
                        @if($montoArancel && !$esEdicion)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:linear-gradient(135deg,#1e3a5f 0%,#1e56b0 100%);
                                      border-radius:10px;margin-bottom:32px;">
                            <tr>
                                <td style="padding:22px 28px;">
                                    <p style="margin:0 0 16px;font-size:11px;font-weight:700;
                                               color:rgba(255,255,255,0.65);text-transform:uppercase;
                                               letter-spacing:0.13em;">
                                        Arancel Semestral — A Pagar durante el Semestre
                                    </p>
                                    <table role="presentation" class="payment-row" width="100%"
                                           cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.7);">
                                                    Valor del semestre
                                                </p>
                                                <p style="margin:4px 0 0;font-size:32px;font-weight:800;
                                                           color:#ffffff;line-height:1;">${{ $montoArancel }}</p>
                                            </td>
                                            <td style="text-align:right;vertical-align:top;">
                                                <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.7);">
                                                    Fecha límite
                                                </p>
                                                <p style="margin:4px 0 0;font-size:20px;font-weight:700;
                                                           color:#ffffff;">{{ $fechaArancel ?? '—' }}</p>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="margin-top:16px;padding-top:14px;
                                                border-top:1px solid rgba(255,255,255,0.20);">
                                        <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.65);
                                                   line-height:1.6;">
                                            Este valor corresponde al arancel del semestre en curso y debe ser
                                            cancelado durante el transcurso del período académico
                                            <strong style="color:#fff;">{{ $matricula->periodo?->code }}</strong>.
                                            Podrá realizarlo en cuotas según las facilidades de pago disponibles.
                                            Consulte con secretaría para coordinar su plan de pago.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        @elseif($esEdicion)
                        <div style="height:32px;"></div>
                        @endif

                    </td>
                </tr>

                {{-- ══ CTA BUTTON ══════════════════════════════════════════ --}}
                @if($instituto['web'])
                <tr>
                    <td style="padding:0 40px 32px;text-align:center;">
                        <a href="{{ $instituto['web'] }}" class="btn-cta"
                           style="display:inline-block;background:#1e56b0;color:#ffffff;
                                  text-decoration:none;padding:14px 36px;border-radius:8px;
                                  font-size:14px;font-weight:600;letter-spacing:0.02em;">
                            Acceder al Portal Estudiantil
                        </a>
                    </td>
                </tr>
                @else
                <tr><td style="height:32px;"></td></tr>
                @endif

                {{-- ══ DIVIDER ════════════════════════════════════════════ --}}
                <tr>
                    <td style="padding:0 40px;">
                        <div style="height:1px;background:#e2e8f0;"></div>
                    </td>
                </tr>

                {{-- ══ FOOTER ════════════════════════════════════════════ --}}
                <tr>
                    <td class="email-footer" style="padding:24px 40px 28px;text-align:center;">
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
                            @if($instituto['telefono'])
                                Tel: {{ $instituto['telefono'] }}
                            @endif
                            @if($instituto['telefono'] && $instituto['email'])
                                &nbsp;&middot;&nbsp;
                            @endif
                            @if($instituto['email'])
                                {{ $instituto['email'] }}
                            @endif
                        </p>
                        @else
                        <div style="height:16px;"></div>
                        @endif
                        <p style="margin:0;font-size:11px;color:#cbd5e1;line-height:1.6;">
                            Este es un correo automático generado por el sistema de gestión académica.
                            Por favor no responda a este mensaje directamente.
                        </p>
                    </td>
                </tr>

            </table>
            {{-- /CARD --}}

            {{-- Pequeño aviso bajo la tarjeta --}}
            <p style="margin:16px 0 0;font-size:11px;color:#94a3b8;text-align:center;">
                &copy; {{ date('Y') }} {{ $instituto['nombre_corto'] }} &mdash; Todos los derechos reservados
            </p>

        </td>
    </tr>
</table>

</body>
</html>
