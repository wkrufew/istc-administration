<?php

namespace App\Livewire\Administration;

use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminSettings extends Component
{
    use WithFileUploads;

    public string $tab = 'instituto';

    // =========================================================================
    // GRUPO: INSTITUTO
    // =========================================================================
    public string $instituto_nombre_largo  = '';
    public string $instituto_nombre_corto  = '';
    public string $instituto_ruc           = '';
    public string $instituto_direccion     = '';
    public string $instituto_telefono      = '';
    public string $instituto_email         = '';
    public string $instituto_web           = '';
    public ?string $instituto_logo_path    = null;
    public ?string $instituto_favicon_path = null;
    public $nuevoLogo    = null;
    public $nuevoFavicon = null;

    // =========================================================================
    // GRUPO: WHATSAPP
    // =========================================================================
    public string $whatsapp_phone_number_id               = '';
    public string $whatsapp_access_token                  = '';
    public string $whatsapp_template_confirmacion         = '';
    public string $whatsapp_template_bienvenida           = '';
    public string $whatsapp_template_pago                 = '';
    public string $whatsapp_template_pago_primera         = '';
    public string $whatsapp_activo                        = '0';

    // =========================================================================
    // GRUPO: SMTP
    // =========================================================================
    public string $smtp_driver      = 'smtp';
    public string $smtp_host        = '';
    public string $smtp_port        = '587';
    public string $smtp_username    = '';
    public string $smtp_password    = '';
    public string $smtp_encryption  = 'tls';
    public string $smtp_from_name   = '';
    public string $smtp_from_address = '';
    public string $smtp_activo      = '0';
    public string $emailPrueba      = '';

    // =========================================================================
    // GRUPO: DOCUMENTOS
    // =========================================================================
    public string $doc_rector           = '';
    public string $doc_secretario       = '';
    public string $doc_coordinador      = '';
    public string $doc_ciudad           = '';
    public string $doc_pie_pagina       = '';

    // =========================================================================
    // GRUPO: MATRÍCULA
    // =========================================================================
    public string $matricula_valor_inscripcion   = '10.00';
    public string $matricula_porcentaje_arrastre = '10';

    // =========================================================================
    // GRUPO: API CÉDULA
    // =========================================================================
    public string $cedula_api_url   = '';
    public string $cedula_api_token = '';

    // =========================================================================
    // GRUPO: NOTIFICACIONES
    // =========================================================================
    public string $notif_matricula_whatsapp  = '0';
    public string $notif_matricula_email     = '0';
    public string $notif_pago_whatsapp       = '0';
    public string $notif_pago_email          = '0';
    public string $notif_titulacion_whatsapp = '0';
    public string $notif_titulacion_email    = '0';

    // =========================================================================
    // MOUNT — carga todos los valores desde caché/BD
    // =========================================================================
    public function mount(): void
    {
        $s = SettingService::all();

        // Instituto
        $this->instituto_nombre_largo  = $s['instituto.nombre_largo']  ?? '';
        $this->instituto_nombre_corto  = $s['instituto.nombre_corto']  ?? '';
        $this->instituto_ruc           = $s['instituto.ruc']           ?? '';
        $this->instituto_direccion     = $s['instituto.direccion']      ?? '';
        $this->instituto_telefono      = $s['instituto.telefono']       ?? '';
        $this->instituto_email         = $s['instituto.email']          ?? '';
        $this->instituto_web           = $s['instituto.web']            ?? '';
        $this->instituto_logo_path     = $s['instituto.logo_path']     ?? null;
        $this->instituto_favicon_path  = $s['instituto.favicon_path']  ?? null;

        // WhatsApp
        $this->whatsapp_phone_number_id               = $s['whatsapp.phone_number_id']                       ?? '';
        $this->whatsapp_access_token                  = $s['whatsapp.access_token']                          ?? '';
        $this->whatsapp_template_confirmacion         = $s['whatsapp.template_confirmacion']                 ?? '';
        $this->whatsapp_template_bienvenida           = $s['whatsapp.template_bienvenida']                   ?? '';
        $this->whatsapp_template_pago                 = $s['whatsapp.template_pago']                         ?? '';
        $this->whatsapp_template_pago_primera         = $s['whatsapp.template_pago_primera_matricula']       ?? '';
        $this->whatsapp_activo                        = $s['whatsapp.activo']                                ?? '0';

        // SMTP
        $this->smtp_driver       = $s['smtp.driver']       ?? 'smtp';
        $this->smtp_host         = $s['smtp.host']         ?? '';
        $this->smtp_port         = $s['smtp.port']         ?? '587';
        $this->smtp_username     = $s['smtp.username']     ?? '';
        $this->smtp_password     = $s['smtp.password']     ?? '';
        $this->smtp_encryption   = $s['smtp.encryption']   ?? 'tls';
        $this->smtp_from_name    = $s['smtp.from_name']    ?? '';
        $this->smtp_from_address = $s['smtp.from_address'] ?? '';
        $this->smtp_activo       = $s['smtp.activo']       ?? '0';

        // Documentos
        $this->doc_rector      = $s['documentos.rector']      ?? '';
        $this->doc_secretario  = $s['documentos.secretario']  ?? '';
        $this->doc_coordinador = $s['documentos.coordinador'] ?? '';
        $this->doc_ciudad      = $s['documentos.ciudad']      ?? '';
        $this->doc_pie_pagina  = $s['documentos.pie_pagina']  ?? '';

        // Matrícula
        $this->matricula_valor_inscripcion   = $s['matricula.valor_inscripcion']   ?? '10.00';
        $this->matricula_porcentaje_arrastre = $s['matricula.porcentaje_arrastre'] ?? '10';

        // API Cédula
        $this->cedula_api_url   = $s['cedula_api.url']   ?? '';
        $this->cedula_api_token = $s['cedula_api.token'] ?? '';

        // Notificaciones
        $this->notif_matricula_whatsapp  = $s['notificaciones.matricula_whatsapp']  ?? '0';
        $this->notif_matricula_email     = $s['notificaciones.matricula_email']     ?? '0';
        $this->notif_pago_whatsapp       = $s['notificaciones.pago_whatsapp']       ?? '0';
        $this->notif_pago_email          = $s['notificaciones.pago_email']          ?? '0';
        $this->notif_titulacion_whatsapp = $s['notificaciones.titulacion_whatsapp'] ?? '0';
        $this->notif_titulacion_email    = $s['notificaciones.titulacion_email']    ?? '0';
    }

    // =========================================================================
    // GUARDAR INSTITUTO
    // =========================================================================
    public function guardarInstituto(): void
    {
        $this->validate([
            'instituto_nombre_largo' => 'required|string|max:255',
            'instituto_nombre_corto' => 'required|string|max:50',
            'instituto_ruc'          => 'nullable|string|max:20',
            'instituto_direccion'    => 'nullable|string|max:255',
            'instituto_telefono'     => 'nullable|string|max:30',
            'instituto_email'        => 'nullable|email|max:255',
            'instituto_web'          => 'nullable|url|max:255',
            'nuevoLogo'              => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'nuevoFavicon'           => 'nullable|image|mimes:png,jpg,jpeg,webp,ico|max:512',
        ], [
            'instituto_nombre_largo.required' => 'El nombre largo del instituto es obligatorio.',
            'instituto_nombre_corto.required' => 'El nombre abreviado es obligatorio.',
            'nuevoLogo.image'                 => 'El logo debe ser una imagen.',
            'nuevoLogo.max'                   => 'El logo no debe superar 2 MB.',
            'nuevoFavicon.max'                => 'El favicon no debe superar 512 KB.',
        ]);

        // Subir logo
        if ($this->nuevoLogo) {
            if ($this->instituto_logo_path) {
                Storage::disk('public')->delete($this->instituto_logo_path);
            }
            $this->instituto_logo_path = $this->nuevoLogo->storeAs(
                'settings',
                'logo.' . $this->nuevoLogo->getClientOriginalExtension(),
                'public'
            );
            $this->nuevoLogo = null;
        }

        // Subir favicon
        if ($this->nuevoFavicon) {
            if ($this->instituto_favicon_path) {
                Storage::disk('public')->delete($this->instituto_favicon_path);
            }
            $this->instituto_favicon_path = $this->nuevoFavicon->storeAs(
                'settings',
                'favicon.' . $this->nuevoFavicon->getClientOriginalExtension(),
                'public'
            );
            $this->nuevoFavicon = null;
        }

        $this->upsertGroup('instituto', [
            'nombre_largo'  => $this->instituto_nombre_largo,
            'nombre_corto'  => $this->instituto_nombre_corto,
            'ruc'           => $this->instituto_ruc,
            'direccion'     => $this->instituto_direccion,
            'telefono'      => $this->instituto_telefono,
            'email'         => $this->instituto_email,
            'web'           => $this->instituto_web,
            'logo_path'     => $this->instituto_logo_path,
            'favicon_path'  => $this->instituto_favicon_path,
        ]);

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Información del instituto guardada.', 'timer' => 2000]);
    }

    // =========================================================================
    // GUARDAR WHATSAPP
    // =========================================================================
    public function guardarWhatsapp(): void
    {
        $this->validate([
            'whatsapp_phone_number_id'       => 'nullable|string|max:100',
            'whatsapp_access_token'          => 'nullable|string|max:500',
            'whatsapp_template_confirmacion' => 'nullable|string|max:100',
            'whatsapp_template_bienvenida'   => 'nullable|string|max:100',
            'whatsapp_template_pago'         => 'nullable|string|max:100',
            'whatsapp_template_pago_primera' => 'nullable|string|max:100',
        ]);

        $this->upsertGroup('whatsapp', [
            'phone_number_id'               => $this->whatsapp_phone_number_id,
            'access_token'                  => $this->whatsapp_access_token,
            'template_confirmacion'         => $this->whatsapp_template_confirmacion,
            'template_bienvenida'           => $this->whatsapp_template_bienvenida,
            'template_pago'                 => $this->whatsapp_template_pago,
            'template_pago_primera_matricula' => $this->whatsapp_template_pago_primera,
            'activo'                        => $this->whatsapp_activo,
        ], encryptedKeys: ['access_token']);

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Configuración de WhatsApp guardada.', 'timer' => 2000]);
    }

    // =========================================================================
    // GUARDAR SMTP
    // =========================================================================
    public function guardarSmtp(): void
    {
        $this->validate([
            'smtp_host'         => 'nullable|string|max:255',
            'smtp_port'         => 'nullable|integer|min:1|max:65535',
            'smtp_username'     => 'nullable|string|max:255',
            'smtp_password'     => 'nullable|string|max:255',
            'smtp_from_name'    => 'nullable|string|max:100',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_encryption'   => 'nullable|in:tls,ssl,none',
        ]);

        $this->upsertGroup('smtp', [
            'driver'       => $this->smtp_driver,
            'host'         => $this->smtp_host,
            'port'         => $this->smtp_port,
            'username'     => $this->smtp_username,
            'password'     => $this->smtp_password,
            'encryption'   => $this->smtp_encryption,
            'from_name'    => $this->smtp_from_name,
            'from_address' => $this->smtp_from_address,
            'activo'       => $this->smtp_activo,
        ], encryptedKeys: ['password']);

        Log::info('SMTP config guardada y caché limpiada', [
            'host'       => $this->smtp_host,
            'port'       => $this->smtp_port,
            'encryption' => $this->smtp_encryption,
            'username'   => $this->smtp_username,
        ]);

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Configuración SMTP guardada.', 'timer' => 2000]);
    }

    // =========================================================================
    // ENVIAR CORREO DE PRUEBA
    // =========================================================================
    public function enviarCorreoPrueba(): void
    {
        $this->validate([
            'emailPrueba' => 'required|email',
        ], [
            'emailPrueba.required' => 'Ingresa una dirección de correo de destino.',
            'emailPrueba.email'    => 'La dirección no es válida.',
        ]);

        if (! $this->smtp_host || ! $this->smtp_username) {
            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'Configuración incompleta',
                'text'  => 'Completa el host y el usuario SMTP antes de enviar la prueba.',
            ]);
            return;
        }

        // Detectar si el password parece ser un ciphertext de Laravel (no el valor real)
        $passwordPareceCifrado = str_starts_with($this->smtp_password, 'eyJ')
            || strlen($this->smtp_password) > 200;

        $contexto = [
            'host'                    => $this->smtp_host,
            'port'                    => $this->smtp_port,
            'encryption'              => $this->smtp_encryption,
            'username'                => $this->smtp_username,
            'from_address'            => $this->smtp_from_address,
            'destino'                 => $this->emailPrueba,
            'password_len'            => strlen($this->smtp_password),
            'password_parece_cifrado' => $passwordPareceCifrado,
        ];

        if ($passwordPareceCifrado) {
            Log::warning('SMTP prueba — el password parece ser un ciphertext, no la contraseña real. Guardad de nuevo los ajustes SMTP.', $contexto);
        }

        Log::info('SMTP prueba — iniciando envío', $contexto);

        try {
            $nombre      = $this->instituto_nombre_corto ?: config('app.name');
            $fromAddress = $this->smtp_from_address ?: $this->smtp_username;
            $fromName    = $this->smtp_from_name    ?: $nombre;

            // Build the Symfony transport directly so we can set SSL stream
            // options before the socket connects — the only reliable way to
            // bypass Brevo's CN mismatch on their South-America regional servers.
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                $this->smtp_host,
                (int) $this->smtp_port,
                $this->smtp_encryption === 'ssl'   // true = implicit TLS (465)
            );
            $transport->setUsername($this->smtp_username);
            $transport->setPassword($this->smtp_password);

            $stream = $transport->getStream();
            if ($stream instanceof \Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream) {
                $stream->setStreamOptions([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                    ],
                ]);
            }

            $message = (new \Symfony\Component\Mime\Email())
                ->from(new \Symfony\Component\Mime\Address($fromAddress, $fromName))
                ->to($this->emailPrueba)
                ->subject("Prueba de correo — {$nombre}")
                ->text(
                    "Correo de prueba desde {$nombre}.\n\n"
                    . "La configuración SMTP está funcionando correctamente.\n\n"
                    . "Servidor : {$this->smtp_host}:{$this->smtp_port}\n"
                    . "Cifrado  : {$this->smtp_encryption}\n"
                    . "Remitente: {$fromAddress}"
                );

            (new \Symfony\Component\Mailer\Mailer($transport))->send($message);

            Log::info('SMTP prueba — enviado correctamente', $contexto);

            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => 'Correo enviado correctamente',
                'text'  => "Mensaje enviado a {$this->emailPrueba}. Revisa tu bandeja de entrada.",
                'timer' => 3000,
            ]);

        } catch (\Exception $e) {
            Log::error('SMTP prueba — falló', array_merge($contexto, [
                'error' => $e->getMessage(),
                'clase' => get_class($e),
                'linea' => $e->getFile() . ':' . $e->getLine(),
            ]));

            // Modal completo para ver el error sin que desaparezca
            $this->dispatch('swal', [
                'icon'              => 'error',
                'title'             => 'Error al conectar con el servidor SMTP',
                'text'              => $e->getMessage(),
                'confirmButtonText' => 'Entendido',
            ]);
        }
    }

    // =========================================================================
    // GUARDAR API CÉDULA
    // =========================================================================
    public function guardarCedulaApi(): void
    {
        $this->validate([
            'cedula_api_url'   => 'nullable|url|max:500',
            'cedula_api_token' => 'nullable|string|max:2000',
        ], [
            'cedula_api_url.url' => 'Ingrese una URL válida (debe comenzar con http:// o https://).',
        ]);

        $this->upsertGroup('cedula_api', [
            'url'   => $this->cedula_api_url,
            'token' => $this->cedula_api_token,
        ], encryptedKeys: ['token']);

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Configuración de API Cédula guardada.', 'timer' => 2000]);
    }

    // =========================================================================
    // GUARDAR DOCUMENTOS
    // =========================================================================
    public function guardarDocumentos(): void
    {
        $this->validate([
            'doc_rector'      => 'nullable|string|max:150',
            'doc_secretario'  => 'nullable|string|max:150',
            'doc_coordinador' => 'nullable|string|max:150',
            'doc_ciudad'      => 'nullable|string|max:100',
            'doc_pie_pagina'  => 'nullable|string|max:500',
        ]);

        $this->upsertGroup('documentos', [
            'rector'      => $this->doc_rector,
            'secretario'  => $this->doc_secretario,
            'coordinador' => $this->doc_coordinador,
            'ciudad'      => $this->doc_ciudad,
            'pie_pagina'  => $this->doc_pie_pagina,
        ]);

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Datos de documentos guardados.', 'timer' => 2000]);
    }

    // =========================================================================
    // GUARDAR MATRÍCULA
    // =========================================================================
    public function guardarMatricula(): void
    {
        $this->validate([
            'matricula_valor_inscripcion'   => 'required|numeric|min:0|max:9999.99',
            'matricula_porcentaje_arrastre' => 'required|numeric|min:0|max:100',
        ], [
            'matricula_valor_inscripcion.required'   => 'El valor de inscripción es obligatorio.',
            'matricula_valor_inscripcion.numeric'    => 'Debe ser un número.',
            'matricula_porcentaje_arrastre.required' => 'El porcentaje de arrastre es obligatorio.',
            'matricula_porcentaje_arrastre.max'      => 'El porcentaje no puede superar 100.',
        ]);

        $this->upsertGroup('matricula', [
            'valor_inscripcion'   => $this->matricula_valor_inscripcion,
            'porcentaje_arrastre' => $this->matricula_porcentaje_arrastre,
        ]);

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Configuración de matrícula guardada.', 'timer' => 2000]);
    }

    // =========================================================================
    // GUARDAR NOTIFICACIONES
    // =========================================================================
    public function guardarNotificaciones(): void
    {
        $this->upsertGroup('notificaciones', [
            'matricula_whatsapp'  => $this->notif_matricula_whatsapp,
            'matricula_email'     => $this->notif_matricula_email,
            'pago_whatsapp'       => $this->notif_pago_whatsapp,
            'pago_email'          => $this->notif_pago_email,
            'titulacion_whatsapp' => $this->notif_titulacion_whatsapp,
            'titulacion_email'    => $this->notif_titulacion_email,
        ]);

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Preferencias de notificaciones guardadas.', 'timer' => 2000]);
    }

    // =========================================================================
    // HELPER PRIVADO — upsert de un grupo de settings + flush caché
    // =========================================================================
    private function upsertGroup(string $group, array $pairs, array $encryptedKeys = []): void
    {
        foreach ($pairs as $subKey => $value) {
            $key        = "{$group}.{$subKey}";
            $encrypted  = in_array($subKey, $encryptedKeys);

            $setting = Setting::firstOrNew(['key' => $key]);
            $setting->group        = $group;
            $setting->key          = $key;
            $setting->is_encrypted = $encrypted;
            // Preserve existing label/type; only set defaults for brand-new rows
            $setting->label        = $setting->label ?: $subKey;
            $setting->type         = $setting->type  ?: 'text';

            $setting->value = $value;

            $setting->save();
        }

        SettingService::flush();
    }

    // =========================================================================
    // RENDER
    // =========================================================================
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.admin-settings');
    }
}
