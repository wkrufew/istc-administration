<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Observación sobre tu documentación</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
  <tr>
    <td align="center">
      <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

        {{-- HEADER --}}
        <tr>
          <td style="background:linear-gradient(135deg,#78350f 0%,#92400e 60%,#b45309 100%);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
            @if(!empty($instituto['logo_url']))
              <img src="{{ $instituto['logo_url'] }}" alt="{{ $instituto['nombre_corto'] }}"
                   style="height:52px;object-fit:contain;margin-bottom:16px;border-radius:8px;">
            @endif
            <div style="font-size:32px;margin-bottom:12px;">&#9888;&#65039;</div>
            <h1 style="margin:0;color:#fff;font-size:20px;font-weight:700;letter-spacing:-0.3px;">
              Hay una observación en tu documentación
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

            <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.6;">
              El equipo de admisiones de
              <strong>{{ $instituto['nombre_corto'] }}</strong>
              ha revisado tu documentación y encontró un problema en el siguiente documento que debes corregir:
            </p>

            {{-- Documento con problema --}}
            <div style="background:#fff7ed;border:2px solid #fed7aa;border-radius:12px;padding:20px 24px;margin-bottom:24px;">
              <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:#c2410c;text-transform:uppercase;letter-spacing:.06em;">
                Documento con observación
              </p>
              <p style="margin:0 0 16px;font-size:16px;font-weight:700;color:#7c2d12;">
                {{ $labelDocumento }}
              </p>
              <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:#c2410c;text-transform:uppercase;letter-spacing:.06em;">
                Observación
              </p>
              <p style="margin:0;font-size:14px;color:#431407;line-height:1.6;background:#fef3c7;border-radius:8px;padding:12px 16px;border-left:4px solid #f59e0b;">
                {{ $observacion }}
              </p>
            </div>

            {{-- Instrucciones --}}
            <p style="margin:0 0 12px;font-size:14px;font-weight:600;color:#334155;">
              ¿Qué debes hacer?
            </p>
            <ol style="margin:0 0 28px;padding-left:20px;font-size:14px;color:#64748b;line-height:1.8;">
              <li>Ingresa al portal de admisión con tus credenciales</li>
              <li>Ve a la sección <strong>Mis documentos</strong></li>
              <li>Busca el documento marcado y sube la versión corregida</li>
              <li>Asegúrate de que el archivo sea claro, legible y en el formato correcto</li>
            </ol>

            {{-- CTA --}}
            <div style="text-align:center;margin-bottom:8px;">
              <a href="{{ $portalUrl }}"
                 style="display:inline-block;background:#b45309;color:#fff;font-size:15px;font-weight:600;
                        padding:13px 32px;border-radius:10px;text-decoration:none;letter-spacing:-0.2px;">
                Ir al portal y corregir →
              </a>
            </div>

            <p style="margin:20px 0 0;font-size:13px;color:#94a3b8;text-align:center;line-height:1.5;">
              Si ya corregiste el documento y tienes dudas, comunícate con nosotros directamente.
            </p>

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
