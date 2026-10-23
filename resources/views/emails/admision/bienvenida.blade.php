<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenido al proceso de ingreso</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
  <tr>
    <td align="center">
      <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

        {{-- HEADER --}}
        <tr>
          <td style="background:linear-gradient(135deg,#1e3a5f 0%,#1e40af 60%,#1d4ed8 100%);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
            @if(!empty($instituto['logo_url']))
              <img src="{{ $instituto['logo_url'] }}" alt="{{ $instituto['nombre_corto'] }}"
                   style="height:52px;object-fit:contain;margin-bottom:16px;border-radius:8px;">
            @endif
            <h1 style="margin:0;color:#fff;font-size:20px;font-weight:700;letter-spacing:-0.3px;">
              ¡Bienvenido a tu proceso de ingreso!
            </h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.75);font-size:14px;">
              {{ $instituto['nombre_largo'] }}
            </p>
          </td>
        </tr>

        {{-- BODY --}}
        <tr>
          <td style="background:#fff;padding:36px 40px;">

            <p style="margin:0 0 20px;font-size:15px;color:#334155;line-height:1.6;">
              Hola <strong>{{ $user->name }}</strong>,
            </p>
            <p style="margin:0 0 24px;font-size:15px;color:#475569;line-height:1.6;">
              Tu solicitud de ingreso ha sido registrada en el sistema de
              <strong>{{ $instituto['nombre_largo'] }}</strong>.
              A continuación encontrarás tus credenciales de acceso al portal:
            </p>

            {{-- Credenciales --}}
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:28px;">
              <tr>
                <td style="padding:20px 24px;">
                  <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;">
                    Correo electrónico
                  </p>
                  <p style="margin:0 0 16px;font-size:16px;font-weight:600;color:#1e293b;">
                    {{ $user->email }}
                  </p>
                  <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;">
                    Contraseña temporal
                  </p>
                  <p style="margin:0;font-size:22px;font-weight:700;color:#1d4ed8;letter-spacing:2px;font-family:monospace;">
                    {{ $password }}
                  </p>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 8px;font-size:14px;color:#64748b;line-height:1.6;">
              Con estas credenciales podrás ingresar al portal y:
            </p>
            <ul style="margin:0 0 28px;padding-left:20px;font-size:14px;color:#64748b;line-height:1.8;">
              <li>Ver el estado de tu solicitud de ingreso</li>
              <li>Subir los documentos requeridos (cédula, título de bachiller, comprobante de pago)</li>
              <li>Enviar tu documentación para revisión</li>
            </ul>

            {{-- Valor de la matrícula --}}
            @if(!empty($resumenPago) && $resumenPago['valor_matricula'] > 0)
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;margin-bottom:28px;">
              <tr>
                <td style="padding:20px 24px;">
                  <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#2563eb;text-transform:uppercase;letter-spacing:.08em;">
                    {{ $resumenPago['es_validacion'] ? 'Pago inicial de matrícula' : 'Valor de tu matrícula' }}
                    · {{ $resumenPago['es_validacion'] ? 'Validación de conocimientos' : 'Proceso regular' }}
                  </p>
                  <p style="margin:0 0 6px;font-size:24px;font-weight:700;color:#1e3a8a;">
                    ${{ number_format($resumenPago['valor_matricula'], 2) }}
                  </p>
                  <p style="margin:0 0 14px;font-size:13px;color:#475569;line-height:1.5;">
                    Realiza este pago y sube el comprobante en el portal.
                  </p>

                  <p style="margin:0 0 {{ $resumenPago['es_validacion'] ? '14px' : '0' }};font-size:13px;line-height:1.5;padding:10px 12px;border-radius:8px;
                            {{ $resumenPago['convenio'] ? 'background:#f5f3ff;color:#5b21b6;' : 'background:#ffffff;color:#64748b;' }}">
                    @if($resumenPago['convenio'])
                      <strong>Convenio:</strong> {{ $resumenPago['convenio']['nombre'] }} ·
                      <strong>{{ number_format($resumenPago['convenio']['porcentaje'], 0) }}% de descuento</strong> en el arancel (colegiatura). La matrícula no lleva descuento.
                    @else
                      <strong>Convenio:</strong> no tienes un convenio asignado.
                    @endif
                  </p>

                  @if($resumenPago['es_validacion'] && count($resumenPago['escenarios']))
                  <p style="margin:0 0 8px;font-size:13px;color:#334155;line-height:1.5;">
                    <strong>¿Por qué es un pago inicial?</strong> La matrícula de tu carrera cuesta
                    <strong>${{ number_format($resumenPago['total_matricula'], 2) }} en total</strong> y se divide entre los semestres que vayas a cursar.
                    Según cuántos semestres valides en tu prueba:
                  </p>
                  <table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;color:#334155;margin-bottom:8px;">
                    <tr>
                      <td style="padding:4px 6px;font-weight:600;color:#64748b;border-bottom:1px solid #bfdbfe;">Validas</td>
                      <td style="padding:4px 6px;font-weight:600;color:#64748b;border-bottom:1px solid #bfdbfe;">Matrícula por semestre</td>
                      <td style="padding:4px 6px;font-weight:600;color:#64748b;border-bottom:1px solid #bfdbfe;">Pagas adicional</td>
                    </tr>
                    @foreach($resumenPago['escenarios'] as $esc)
                    <tr>
                      <td style="padding:4px 6px;border-bottom:1px solid #dbeafe;">{{ $esc['exonera'] }} {{ $esc['exonera'] === 1 ? 'semestre' : 'semestres' }}</td>
                      <td style="padding:4px 6px;border-bottom:1px solid #dbeafe;">${{ number_format($resumenPago['total_matricula'], 2) }} ÷ {{ $esc['cursa'] }} = <strong>${{ number_format($esc['valor'], 2) }}</strong></td>
                      <td style="padding:4px 6px;border-bottom:1px solid #dbeafe;font-weight:600;color:{{ $esc['diferencia'] > 0 ? '#b45309' : '#047857' }};">
                        {{ $esc['diferencia'] > 0 ? '$' . number_format($esc['diferencia'], 2) : 'Nada más' }}
                      </td>
                    </tr>
                    @endforeach
                  </table>
                  <p style="margin:0;font-size:12px;color:#64748b;line-height:1.5;">
                    Lo que pagas ahora se descuenta del valor final; la diferencia, si la hay, la completas al matricularte.
                  </p>
                  @endif
                </td>
              </tr>
            </table>
            @endif

            {{-- CTA --}}
            <div style="text-align:center;margin-bottom:28px;">
              <a href="{{ $portalUrl }}"
                 style="display:inline-block;background:#1d4ed8;color:#fff;font-size:15px;font-weight:600;
                        padding:13px 32px;border-radius:10px;text-decoration:none;letter-spacing:-0.2px;">
                Ingresar al portal →
              </a>
            </div>

            {{-- Aviso contraseña --}}
            <div style="background:#fefce8;border:1px solid #fde047;border-radius:10px;padding:14px 18px;margin-bottom:8px;">
              <p style="margin:0;font-size:13px;color:#854d0e;line-height:1.5;">
                <strong>Importante:</strong> Te recomendamos cambiar tu contraseña al ingresar por primera vez.
                Guarda estas credenciales en un lugar seguro.
              </p>
            </div>

          </td>
        </tr>

        {{-- FOOTER --}}
        <tr>
          <td style="background:#f8fafc;border-top:1px solid #e2e8f0;border-radius:0 0 16px 16px;padding:20px 40px;text-align:center;">
            <p style="margin:0 0 6px;font-size:12px;color:#94a3b8;">
              {{ $instituto['nombre_largo'] }}
            </p>
            @if(!empty($instituto['email']))
            <p style="margin:0;font-size:12px;color:#94a3b8;">
              {{ $instituto['email'] }}
              @if(!empty($instituto['telefono']))· {{ $instituto['telefono'] }}@endif
            </p>
            @endif
            <p style="margin:8px 0 0;font-size:11px;color:#cbd5e1;">
              Este es un correo generado automáticamente, por favor no respondas a este mensaje.
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
