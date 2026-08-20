<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Actualización de tu proceso de ingreso</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b;">

@php
$config = match($nuevoEstado) {
    'proceso'     => [
        'gradient'    => 'linear-gradient(135deg,#1e3a5f 0%,#1e40af 60%,#1d4ed8 100%)',
        'badge_bg'    => '#dbeafe',
        'badge_color' => '#1d4ed8',
        'badge_label' => 'En revisión',
        'icon'        => '&#128269;',
        'titulo'      => 'Tu solicitud está siendo revisada',
        'cta_bg'      => '#1d4ed8',
        'mostrar_portal' => true,
    ],
    'aprobado'    => [
        'gradient'    => 'linear-gradient(135deg,#064e3b 0%,#065f46 60%,#047857 100%)',
        'badge_bg'    => '#d1fae5',
        'badge_color' => '#065f46',
        'badge_label' => 'Pre-matrícula aprobada',
        'icon'        => '&#9989;',
        'titulo'      => '¡Tu pre-matrícula ha sido aprobada!',
        'cta_bg'      => '#047857',
        'mostrar_portal' => true,
    ],
    'rechazado'   => [
        'gradient'    => 'linear-gradient(135deg,#7f1d1d 0%,#991b1b 60%,#b91c1c 100%)',
        'badge_bg'    => '#fee2e2',
        'badge_color' => '#991b1b',
        'badge_label' => 'Solicitud no aprobada',
        'icon'        => '&#10060;',
        'titulo'      => 'Resultado de tu solicitud de ingreso',
        'cta_bg'      => '#dc2626',
        'mostrar_portal' => false,
    ],
    'matriculado' => [
        'gradient'    => 'linear-gradient(135deg,#3b0764 0%,#4c1d95 60%,#6d28d9 100%)',
        'badge_bg'    => '#ede9fe',
        'badge_color' => '#4c1d95',
        'badge_label' => 'Matriculado',
        'icon'        => '&#127891;',
        'titulo'      => '¡Tu matrícula ha sido registrada!',
        'cta_bg'      => '#6d28d9',
        'mostrar_portal' => false,
    ],
    default       => [
        'gradient'    => 'linear-gradient(135deg,#1e3a5f 0%,#1e40af 60%,#1d4ed8 100%)',
        'badge_bg'    => '#dbeafe',
        'badge_color' => '#1d4ed8',
        'badge_label' => 'Actualización',
        'icon'        => '&#128276;',
        'titulo'      => 'Actualización de tu proceso',
        'cta_bg'      => '#1d4ed8',
        'mostrar_portal' => true,
    ],
};
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
  <tr>
    <td align="center">
      <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

        {{-- HEADER --}}
        <tr>
          <td style="background:{{ $config['gradient'] }};border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
            @if(!empty($instituto['logo_url']))
              <img src="{{ $instituto['logo_url'] }}" alt="{{ $instituto['nombre_corto'] }}"
                   style="height:52px;object-fit:contain;margin-bottom:16px;border-radius:8px;">
            @endif
            <div style="font-size:32px;margin-bottom:12px;">{!! $config['icon'] !!}</div>
            <h1 style="margin:0;color:#fff;font-size:20px;font-weight:700;letter-spacing:-0.3px;">
              {{ $config['titulo'] }}
            </h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.75);font-size:14px;">
              {{ $instituto['nombre_largo'] }}
            </p>
          </td>
        </tr>

        {{-- BODY --}}
        <tr>
          <td style="background:#fff;padding:36px 40px;">

            {{-- Saludo --}}
            <p style="margin:0 0 20px;font-size:15px;color:#334155;line-height:1.6;">
              Hola <strong>{{ $aspirante->user->name }}</strong>,
            </p>

            {{-- Badge de estado --}}
            <div style="margin-bottom:24px;">
              <span style="display:inline-block;background:{{ $config['badge_bg'] }};color:{{ $config['badge_color'] }};
                           font-size:12px;font-weight:700;padding:5px 14px;border-radius:999px;letter-spacing:.04em;
                           text-transform:uppercase;">
                {{ $config['badge_label'] }}
              </span>
            </div>

            {{-- Mensaje según estado --}}
            @if($nuevoEstado === 'proceso')
              <p style="margin:0 0 16px;font-size:15px;color:#475569;line-height:1.6;">
                Hemos recibido tu documentación y tu solicitud de ingreso a
                <strong>{{ $aspirante->carrera?->name ?? $instituto['nombre_corto'] }}</strong>
                está actualmente <strong>en proceso de revisión</strong>.
              </p>
              <p style="margin:0 0 24px;font-size:15px;color:#475569;line-height:1.6;">
                Nuestro equipo evaluará los documentos que subiste al portal.
                Te notificaremos cuando haya una actualización.
              </p>

            @elseif($nuevoEstado === 'aprobado')
              <p style="margin:0 0 16px;font-size:15px;color:#475569;line-height:1.6;">
                Tu solicitud de ingreso a
                <strong>{{ $aspirante->carrera?->name ?? $instituto['nombre_corto'] }}</strong>
                ha sido <strong>aprobada</strong>. Estás habilitado para continuar con el proceso de matrícula.
              </p>
              <p style="margin:0 0 8px;font-size:14px;color:#64748b;line-height:1.6;">
                Los próximos pasos son:
              </p>
              <ul style="margin:0 0 28px;padding-left:20px;font-size:14px;color:#64748b;line-height:1.8;">
                <li>Secretaría se pondrá en contacto contigo para coordinar tu matrícula</li>
                <li>Una vez matriculado recibirás tus credenciales de acceso al portal estudiantil</li>
                <li>Ahí podrás consultar tu horario, clases y actividades</li>
              </ul>

            @elseif($nuevoEstado === 'rechazado')
              <p style="margin:0 0 16px;font-size:15px;color:#475569;line-height:1.6;">
                Lamentamos informarte que tu solicitud de ingreso a
                <strong>{{ $aspirante->carrera?->name ?? $instituto['nombre_corto'] }}</strong>
                no pudo ser aprobada en este proceso.
              </p>
              @if(!empty($aspirante->motivo_rechazo))
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:16px 20px;margin-bottom:24px;">
                  <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.06em;">
                    Motivo
                  </p>
                  <p style="margin:0;font-size:14px;color:#7f1d1d;line-height:1.6;">
                    {{ $aspirante->motivo_rechazo }}
                  </p>
                </div>
              @endif
              <p style="margin:0 0 24px;font-size:14px;color:#64748b;line-height:1.6;">
                Si tienes preguntas o deseas más información, comunícate directamente con el instituto.
              </p>

            @elseif($nuevoEstado === 'matriculado')
              <p style="margin:0 0 16px;font-size:15px;color:#475569;line-height:1.6;">
                Te confirmamos que tu matrícula en
                <strong>{{ $aspirante->carrera?->name ?? $instituto['nombre_corto'] }}</strong>
                ha sido registrada exitosamente en el sistema.
              </p>
              <p style="margin:0 0 8px;font-size:14px;color:#64748b;line-height:1.6;">
                ¡Bienvenido a nuestra institución! Recuerda:
              </p>
              <ul style="margin:0 0 28px;padding-left:20px;font-size:14px;color:#64748b;line-height:1.8;">
                <li>Asiste puntualmente desde el primer día de clases</li>
                <li>Trae contigo tu cédula de identidad</li>
                <li>Consulta tu horario en el sistema estudiantil</li>
              </ul>

            @else
              <p style="margin:0 0 24px;font-size:15px;color:#475569;line-height:1.6;">
                Tu proceso de ingreso ha sido actualizado. Ingresa al portal para ver los detalles.
              </p>
            @endif

            {{-- Cohorte info --}}
            @if($aspirante->cohorte)
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:28px;">
              <tr>
                <td style="padding:16px 20px;">
                  <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;">
                    Cohorte
                  </p>
                  <p style="margin:0 0 10px;font-size:14px;font-weight:600;color:#1e293b;">
                    {{ $aspirante->cohorte->nombre }}
                  </p>
                  @if($aspirante->cohorte->fecha_inicio_clases)
                  <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;">
                    Inicio de clases
                  </p>
                  <p style="margin:0;font-size:14px;color:#475569;">
                    {{ $aspirante->cohorte->fecha_inicio_clases->format('d/m/Y') }}
                  </p>
                  @endif
                </td>
              </tr>
            </table>
            @endif

            {{-- CTA --}}
            @if($config['mostrar_portal'])
            <div style="text-align:center;margin-bottom:8px;">
              <a href="{{ $portalUrl }}"
                 style="display:inline-block;background:{{ $config['cta_bg'] }};color:#fff;font-size:15px;font-weight:600;
                        padding:13px 32px;border-radius:10px;text-decoration:none;letter-spacing:-0.2px;">
                Ver mi proceso en el portal →
              </a>
            </div>
            @endif

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
