<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirmación de Pago</title>
</head>
<body style="margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;background:#f0f4f8;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:32px 0;">
  <tr><td align="center">
  <table width="620" cellpadding="0" cellspacing="0" style="max-width:620px;width:100%;">

    {{-- ── CABECERA ───────────────────────────────────────────────────────── --}}
    <tr>
      <td style="background:linear-gradient(135deg,#1a6b3c 0%,#22a05e 100%);border-radius:12px 12px 0 0;padding:36px 40px;text-align:center;">
        <p style="margin:0 0 4px;color:rgba(255,255,255,0.85);font-size:13px;letter-spacing:1px;text-transform:uppercase;">
          {{ $nombreInstituto }}
        </p>
        <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:700;letter-spacing:-0.5px;">
          ✓ Pago Confirmado
        </h1>
        <p style="margin:10px 0 0;color:rgba(255,255,255,0.8);font-size:14px;">
          Tu pago ha sido registrado exitosamente
        </p>
      </td>
    </tr>

    {{-- ── CUERPO PRINCIPAL ───────────────────────────────────────────────── --}}
    <tr>
      <td style="background:#ffffff;padding:36px 40px;">

        {{-- Saludo --}}
        <p style="margin:0 0 24px;color:#1e293b;font-size:16px;">
          Hola, <strong>{{ $matricula->estudiante->name }}</strong>.
        </p>
        <p style="margin:0 0 28px;color:#475569;font-size:14px;line-height:1.6;">
          Confirmamos la recepción de tu pago. A continuación encontrarás el detalle del comprobante.
        </p>

        {{-- ── COMPROBANTE ── --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;margin-bottom:28px;overflow:hidden;">
          <tr>
            <td style="background:#16a34a;padding:12px 20px;">
              <p style="margin:0;color:#fff;font-size:13px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;">
                Comprobante de Pago
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:20px;">
              <table width="100%" cellpadding="6" cellspacing="0">
                <tr>
                  <td style="color:#64748b;font-size:13px;width:48%;">N.° Comprobante</td>
                  <td style="color:#1e293b;font-size:13px;font-weight:700;">{{ $pago->numero_comprobante }}</td>
                </tr>
                <tr style="background:#f8fafc;">
                  <td style="color:#64748b;font-size:13px;">Monto Pagado</td>
                  <td style="color:#16a34a;font-size:18px;font-weight:700;">{{ $montoFormateado }}</td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:13px;">Método de Pago</td>
                  <td style="color:#1e293b;font-size:13px;">{{ $pago->metodo_pago }}</td>
                </tr>
                @if($pago->codigo_referencia)
                <tr style="background:#f8fafc;">
                  <td style="color:#64748b;font-size:13px;">Referencia</td>
                  <td style="color:#1e293b;font-size:13px;">{{ $pago->codigo_referencia }}</td>
                </tr>
                @endif
                <tr @if(!$pago->codigo_referencia) style="background:#f8fafc;" @endif>
                  <td style="color:#64748b;font-size:13px;">Fecha y Hora</td>
                  <td style="color:#1e293b;font-size:13px;">{{ $fechaPago }}</td>
                </tr>
                <tr @if($pago->codigo_referencia) style="background:#f8fafc;" @endif>
                  <td style="color:#64748b;font-size:13px;">Estado</td>
                  <td>
                    <span style="display:inline-block;background:#dcfce7;color:#16a34a;font-size:12px;font-weight:700;padding:2px 10px;border-radius:20px;">
                      {{ $pago->estado }}
                    </span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        {{-- ── DATOS DE MATRÍCULA ── --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:28px;overflow:hidden;">
          <tr>
            <td style="background:#334155;padding:12px 20px;">
              <p style="margin:0;color:#fff;font-size:13px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;">
                Datos de Matrícula
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:20px;">
              <table width="100%" cellpadding="6" cellspacing="0">
                <tr>
                  <td style="color:#64748b;font-size:13px;width:48%;">Código de Matrícula</td>
                  <td style="color:#1e293b;font-size:13px;font-weight:700;">{{ $matricula->code }}</td>
                </tr>
                <tr style="background:#f0f4f8;">
                  <td style="color:#64748b;font-size:13px;">Estudiante</td>
                  <td style="color:#1e293b;font-size:13px;">{{ $matricula->estudiante->name }}</td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:13px;">Carrera</td>
                  <td style="color:#1e293b;font-size:13px;">{{ $matricula->carrera->name ?? '—' }}</td>
                </tr>
                <tr style="background:#f0f4f8;">
                  <td style="color:#64748b;font-size:13px;">Período</td>
                  <td style="color:#1e293b;font-size:13px;">{{ $matricula->periodo->name ?? '—' }}</td>
                </tr>
                <tr>
                  <td style="color:#64748b;font-size:13px;">Estado Matrícula</td>
                  <td>
                    <span style="display:inline-block;background:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:700;padding:2px 10px;border-radius:20px;">
                      {{ $matricula->estado }}
                    </span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        {{-- ── NOTA INFORMATIVA ── --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;margin-bottom:8px;">
          <tr>
            <td style="padding:16px 20px;">
              <p style="margin:0 0 4px;color:#92400e;font-size:13px;font-weight:700;">Información importante</p>
              <p style="margin:0;color:#78350f;font-size:13px;line-height:1.6;">
                Conserva este comprobante como respaldo de tu pago. Si tienes alguna consulta sobre tu matrícula,
                acércate a secretaría o comunícate con nosotros.
              </p>
            </td>
          </tr>
        </table>

      </td>
    </tr>

    {{-- ── PIE DE PÁGINA ──────────────────────────────────────────────────── --}}
    <tr>
      <td style="background:#1e293b;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
        <p style="margin:0 0 6px;color:#94a3b8;font-size:12px;">
          {{ $nombreInstituto }}
        </p>
        <p style="margin:0;color:#64748b;font-size:11px;">
          Este es un correo generado automáticamente. Por favor no respondas a este mensaje.
        </p>
      </td>
    </tr>

  </table>
  </td></tr>
</table>

</body>
</html>
