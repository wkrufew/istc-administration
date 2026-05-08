<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private string $apiUrl;
    private string $phoneNumberId;
    private string $accessToken;
    private string $apiVersion = 'v19.0';

    public function __construct()
    {
        $this->phoneNumberId = SettingService::get('whatsapp.phone_number_id') ?: config('whatsapp.phone_number_id');
        $this->accessToken   = SettingService::get('whatsapp.access_token')    ?: config('whatsapp.access_token');
        $this->apiUrl        = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
    }

    // =========================================================================
    // ENVIAR TEMPLATE — método base
    // =========================================================================
    public function enviarTemplate(
        string $telefono,
        string $templateName,
        string $idioma,
        array  $componentes = []
    ): bool {
        $telefono = $this->limpiarTelefono($telefono);

        if (! $telefono) {
            Log::warning('WhatsApp: teléfono inválido', ['raw' => $telefono]);
            return false;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $telefono,
            'type'              => 'template',
            'template'          => [
                'name'     => $templateName,
                'language' => ['code' => $idioma],
            ],
        ];

        if (! empty($componentes)) {
            $payload['template']['components'] = $componentes;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(15)
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                Log::info('WhatsApp enviado', [
                    'telefono' => $telefono,
                    'template' => $templateName,
                    'wamid'    => $response->json('messages.0.id'),
                ]);
                return true;
            }

            Log::error('WhatsApp: error de API', [
                'telefono' => $telefono,
                'template' => $templateName,
                'status'   => $response->status(),
                'body'     => $response->json(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp: excepción HTTP', [
                'telefono' => $telefono,
                'template' => $templateName,
                'error'    => $e->getMessage(),
            ]);
            return false;
        }
    }

    // =========================================================================
    // BIENVENIDA + CREDENCIALES (primera matrícula)
    // Template: matricula_bienvenida
    // {{1}} nombre         {{2}} nombreInstituto  {{3}} codigoMatricula
    // {{4}} carrera        {{5}} periodo          {{6}} monto
    // {{7}} fechaLimite    {{8}} correo           {{9}} urlPlataforma
    // =========================================================================
    public function enviarBienvenidaConCredenciales(
        string $telefono,
        string $nombre,
        string $nombreInstituto,
        string $codigoMatricula,
        string $carrera,
        string $periodo,
        string $monto,
        string $fechaLimite,
        string $correo,
        string $urlPlataforma,
    ): bool {
        $logoUrl     = $this->resolverLogoUrl();
        $componentes = [];

        if ($logoUrl) {
            $componentes[] = $this->componenteImagenHeader($logoUrl);
        }

        $componentes[] = [
            'type'       => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $nombre],
                ['type' => 'text', 'text' => $nombreInstituto],
                ['type' => 'text', 'text' => $codigoMatricula],
                ['type' => 'text', 'text' => $carrera],
                ['type' => 'text', 'text' => $periodo],
                ['type' => 'text', 'text' => $monto],
                ['type' => 'text', 'text' => $fechaLimite],
                ['type' => 'text', 'text' => $correo],
                ['type' => 'text', 'text' => $urlPlataforma ?: '—'],
            ],
        ];

        return $this->enviarTemplate(
            $telefono,
            SettingService::get('whatsapp.template_bienvenida') ?: config('whatsapp.templates.matricula_bienvenida', 'matricula_bienvenida'),
            'es',
            $componentes
        );
    }

    // =========================================================================
    // CONFIRMACIÓN DE MATRÍCULA (renovación — estudiante ya existente)
    // Template: matricula_confirmacion
    // {{1}} nombre         {{2}} nombreInstituto  {{3}} codigoMatricula
    // {{4}} carrera        {{5}} periodo          {{6}} cedula
    // {{7}} monto          {{8}} fechaLimite
    // =========================================================================
    public function enviarConfirmacionMatricula(
        string $telefono,
        string $nombre,
        string $nombreInstituto,
        string $codigoMatricula,
        string $carrera,
        string $periodo,
        string $cedula,
        string $monto,
        string $fechaLimite
    ): bool {
        $logoUrl     = $this->resolverLogoUrl();
        $componentes = [];

        if ($logoUrl) {
            $componentes[] = $this->componenteImagenHeader($logoUrl);
        }

        $componentes[] = [
            'type'       => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $nombre],
                ['type' => 'text', 'text' => $nombreInstituto],
                ['type' => 'text', 'text' => $codigoMatricula],
                ['type' => 'text', 'text' => $carrera],
                ['type' => 'text', 'text' => $periodo],
                ['type' => 'text', 'text' => $cedula],
                ['type' => 'text', 'text' => $monto],
                ['type' => 'text', 'text' => $fechaLimite],
            ],
        ];

        return $this->enviarTemplate(
            $telefono,
            SettingService::get('whatsapp.template_confirmacion') ?: config('whatsapp.templates.matricula_confirmacion', 'matricula_confirmacion'),
            'es',
            $componentes
        );
    }

    // =========================================================================
    // CONFIRMACIÓN DE PAGO GENÉRICO (colegiatura, multa, arrastre, etc.)
    // Template: pago_confirmacion
    // {{1}} nombre         {{2}} nombreInstituto  {{3}} tipoPago
    // {{4}} comprobante    {{5}} monto            {{6}} metodoPago
    // {{7}} fechaPago      {{8}} codigoMatricula
    // =========================================================================
    public function enviarConfirmacionPago(
        string $telefono,
        string $nombre,
        string $nombreInstituto,
        string $tipoPago,
        string $numeroComprobante,
        string $monto,
        string $metodoPago,
        string $fechaPago,
        string $codigoMatricula,
    ): bool {
        $logoUrl     = $this->resolverLogoUrl();
        $componentes = [];

        if ($logoUrl) {
            $componentes[] = $this->componenteImagenHeader($logoUrl);
        }

        $componentes[] = [
            'type'       => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $nombre],
                ['type' => 'text', 'text' => $nombreInstituto],
                ['type' => 'text', 'text' => $tipoPago],
                ['type' => 'text', 'text' => $numeroComprobante],
                ['type' => 'text', 'text' => $monto],
                ['type' => 'text', 'text' => $metodoPago],
                ['type' => 'text', 'text' => $fechaPago],
                ['type' => 'text', 'text' => $codigoMatricula],
            ],
        ];

        return $this->enviarTemplate(
            $telefono,
            SettingService::get('whatsapp.template_pago') ?: config('whatsapp.templates.pago_confirmacion', 'pago_confirmacion'),
            'es',
            $componentes
        );
    }

    // =========================================================================
    // PAGO DE PRIMERA MATRÍCULA (matrícula + inscripción auto-liquidada)
    // Template: pago_primera_matricula
    // {{1}} nombre                   {{2}} nombreInstituto
    // {{3}} comprobanteMatricula      {{4}} montoMatricula
    // {{5}} comprobanteInscripcion    {{6}} montoInscripcion
    // {{7}} totalCobrado              {{8}} metodoPago   {{9}} fechaPago
    // =========================================================================
    public function enviarPagoPrimeraMatricula(
        string $telefono,
        string $nombre,
        string $nombreInstituto,
        string $comprobanteMatricula,
        string $montoMatricula,
        string $comprobanteInscripcion,
        string $montoInscripcion,
        string $totalCobrado,
        string $metodoPago,
        string $fechaPago,
    ): bool {
        $logoUrl     = $this->resolverLogoUrl();
        $componentes = [];

        if ($logoUrl) {
            $componentes[] = $this->componenteImagenHeader($logoUrl);
        }

        $componentes[] = [
            'type'       => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $nombre],
                ['type' => 'text', 'text' => $nombreInstituto],
                ['type' => 'text', 'text' => $comprobanteMatricula],
                ['type' => 'text', 'text' => $montoMatricula],
                ['type' => 'text', 'text' => $comprobanteInscripcion],
                ['type' => 'text', 'text' => $montoInscripcion],
                ['type' => 'text', 'text' => $totalCobrado],
                ['type' => 'text', 'text' => $metodoPago],
                ['type' => 'text', 'text' => $fechaPago],
            ],
        ];

        return $this->enviarTemplate(
            $telefono,
            SettingService::get('whatsapp.template_pago_primera_matricula') ?: config('whatsapp.templates.pago_primera_matricula', 'pago_primera_matricula'),
            'es',
            $componentes
        );
    }

    // =========================================================================
    // NUEVO TICKET (notifica al usuario que lo creó)
    // Template: ticket_nuevo
    // {{1}} nombre  {{2}} numero  {{3}} titulo  {{4}} prioridad  {{5}} estado
    // =========================================================================
    public function enviarNuevoTicket(
        string $telefono,
        string $nombre,
        string $numero,
        string $titulo,
        string $prioridad,
        string $estado
    ): bool {
        $componentes = [
            [
                'type'       => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $nombre],
                    ['type' => 'text', 'text' => $numero],
                    ['type' => 'text', 'text' => $titulo],
                    ['type' => 'text', 'text' => $prioridad],
                    ['type' => 'text', 'text' => $estado],
                ],
            ],
        ];

        return $this->enviarTemplate(
            $telefono,
            config('whatsapp.templates.ticket_nuevo', 'ticket_nuevo'),
            'es',
            $componentes
        );
    }

    // =========================================================================
    // RESPUESTA EN TICKET
    // Template: ticket_respuesta
    // {{1}} nombre  {{2}} numero  {{3}} titulo  {{4}} respondido_por
    // =========================================================================
    public function enviarRespuestaTicket(
        string $telefono,
        string $nombre,
        string $numero,
        string $titulo,
        string $respondidoPor
    ): bool {
        $componentes = [
            [
                'type'       => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $nombre],
                    ['type' => 'text', 'text' => $numero],
                    ['type' => 'text', 'text' => $titulo],
                    ['type' => 'text', 'text' => $respondidoPor],
                ],
            ],
        ];

        return $this->enviarTemplate(
            $telefono,
            config('whatsapp.templates.ticket_respuesta', 'ticket_respuesta'),
            'es',
            $componentes
        );
    }

    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================

    /**
     * Devuelve la URL pública del logo si está configurada y no apunta a localhost.
     * Meta no puede acceder a URLs locales; en ese caso se omite el header de imagen.
     */
    private function resolverLogoUrl(): ?string
    {
        $logoPath = SettingService::get('instituto.logo_path');
        if (! $logoPath) {
            return null;
        }

        $url  = url('storage/' . $logoPath);
        $host = parse_url($url, PHP_URL_HOST);

        if (in_array($host, ['localhost', '127.0.0.1', '::1'])) {
            return null;
        }

        return $url;
    }

    /**
     * Construye el componente de encabezado con imagen para los templates.
     */
    private function componenteImagenHeader(string $imageUrl): array
    {
        return [
            'type'       => 'header',
            'parameters' => [
                [
                    'type'  => 'image',
                    'image' => ['link' => $imageUrl],
                ],
            ],
        ];
    }

    /**
     * Normaliza teléfonos ecuatorianos al formato internacional sin "+".
     * 09XXXXXXXX → 5939XXXXXXXX
     */
    private function limpiarTelefono(string $telefono): string
    {
        $limpio = preg_replace('/\D/', '', $telefono);

        if (empty($limpio)) return '';

        if (str_starts_with($limpio, '593')) {
            return $limpio;
        }

        if (str_starts_with($limpio, '0')) {
            return '593' . substr($limpio, 1);
        }

        if (str_starts_with($limpio, '9') && strlen($limpio) === 9) {
            return '593' . $limpio;
        }

        return $limpio;
    }
}
