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
