<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Solicitud de admisión recibida</title>
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
            <div style="font-size:32px;margin-bottom:12px;">&#128203;</div>
            <h1 style="margin:0;color:#fff;font-size:20px;font-weight:700;letter-spacing:-0.3px;">
              ¡Tu solicitud fue recibida!
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
              Hola <strong>{{ $aspirante->user->name }}</strong>,
            </p>
            <p style="margin:0 0 24px;font-size:15px;color:#475569;line-height:1.6;">
              Hemos recibido tu solicitud de admisión a
              <strong>{{ $aspirante->cohorte?->carrera?->name ?? $instituto['nombre_corto'] }}</strong>.
              En este momento estamos analizando la información que compartiste y pronto tendrás noticias de parte del equipo de admisión.
            </p>

            {{-- Info de la cohorte --}}
            @if($aspirante->cohorte)
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:28px;">
              <tr>
                <td style="padding:20px 24px;">
                  <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;">
                    Cohorte
                  </p>
                  <p style="margin:0 0 12px;font-size:15px;font-weight:600;color:#1e293b;">
                    {{ $aspirante->cohorte->nombre }}
                  </p>
                  @if($aspirante->cohorte->fecha_inicio_clases)
                  <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;">
                    Inicio de clases previsto
                  </p>
                  <p style="margin:0;font-size:14px;color:#475569;">
                    {{ $aspirante->cohorte->fecha_inicio_clases->format('d/m/Y') }}
                  </p>
                  @endif
                </td>
              </tr>
            </table>
            @endif

            {{-- Pasos del proceso --}}
            <p style="margin:0 0 14px;font-size:14px;font-weight:600;color:#334155;">
              ¿Qué sigue?
            </p>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
              @foreach([
                  ['num' => '1', 'title' => 'Revisión de solicitud', 'desc' => 'El equipo de admisión revisará tu información.'],
                  ['num' => '2', 'title' => 'Acceso al portal', 'desc' => 'Recibirás un correo con tus credenciales para ingresar al portal y subir los documentos requeridos.'],
                  ['num' => '3', 'title' => 'Entrega de documentos', 'desc' => 'Cédula, título de bachiller o equivalente, y comprobante de pago.'],
                  ['num' => '4', 'title' => 'Resultado', 'desc' => 'Te notificaremos por correo cuando tengamos un resultado de tu proceso.'],
              ] as $paso)
              <tr>
                <td style="padding:8px 0;">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="36" valign="top" style="padding-top:2px;">
                        <div style="width:26px;height:26px;border-radius:50%;background:#dbeafe;color:#1d4ed8;
                                    font-size:12px;font-weight:700;text-align:center;line-height:26px;">
                          {{ $paso['num'] }}
                        </div>
                      </td>
                      <td style="padding-left:8px;">
                        <p style="margin:0 0 2px;font-size:13px;font-weight:600;color:#1e293b;">{{ $paso['title'] }}</p>
                        <p style="margin:0;font-size:13px;color:#64748b;line-height:1.5;">{{ $paso['desc'] }}</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              @endforeach
            </table>

            {{-- Aviso --}}
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;">
              <p style="margin:0;font-size:13px;color:#15803d;line-height:1.5;">
                <strong>Importante:</strong> Pronto recibirás otro correo con tus credenciales de acceso
                al portal de admisión. Revisa también tu carpeta de spam.
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
              @if(!empty($instituto['telefono'])) · {{ $instituto['telefono'] }} @endif
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
