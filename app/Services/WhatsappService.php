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
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->phoneNumberId = config('whatsapp.phone_number_id');
        $this->accessToken   = config('whatsapp.access_token');
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
    // CONFIRMACIÓN DE MATRÍCULA (estudiante ya existente)
    // Template: matricula_confirmacion
    // Parámetros: {{1}} nombre  {{2}} codigo  {{3}} carrera
    //             {{4}} periodo {{5}} cedula  {{6}} monto  {{7}} fecha_limite
    // =========================================================================
    public function enviarConfirmacionMatricula(
        string $telefono,
        string $nombre,
        string $codigoMatricula,
        string $carrera,
        string $periodo,
        string $cedula,
        string $monto,
        string $fechaLimite
    ): bool {
        $componentes = [
            [
                'type'       => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $nombre],
                    ['type' => 'text', 'text' => $codigoMatricula],
                    ['type' => 'text', 'text' => $carrera],
                    ['type' => 'text', 'text' => $periodo],
                    ['type' => 'text', 'text' => $cedula],
                    ['type' => 'text', 'text' => $monto],
                    ['type' => 'text', 'text' => $fechaLimite],
                ],
            ],
        ];

        return $this->enviarTemplate(
            $telefono,
            config('whatsapp.templates.matricula_confirmacion', 'matricula_confirmacion'),
            'es',
            $componentes
        );
    }

    // =========================================================================
    // BIENVENIDA + CREDENCIALES (primer acceso al sistema)
    // Template: matricula_bienvenida
    // Parámetros: {{1}} nombre  {{2}} correo  {{3}} cedula (password inicial)
    //             {{4}} codigo  {{5}} carrera  {{6}} periodo
    //             {{7}} monto   {{8}} fecha_limite
    // =========================================================================
    public function enviarBienvenidaConCredenciales(
        string $telefono,
        string $nombre,
        string $correo,
        string $cedula,
        string $codigoMatricula,
        string $carrera,
        string $periodo,
        string $monto,
        string $fechaLimite
    ): bool {
        $componentes = [
            [
                'type'       => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $nombre],
                    ['type' => 'text', 'text' => $correo],
                    ['type' => 'text', 'text' => $cedula],          // password inicial
                    ['type' => 'text', 'text' => $codigoMatricula],
                    ['type' => 'text', 'text' => $carrera],
                    ['type' => 'text', 'text' => $periodo],
                    ['type' => 'text', 'text' => $monto],
                    ['type' => 'text', 'text' => $fechaLimite],
                ],
            ],
        ];

        return $this->enviarTemplate(
            $telefono,
            config('whatsapp.templates.matricula_bienvenida', 'matricula_bienvenida'),
            'es',
            $componentes
        );
    }

    // =========================================================================
    // HELPER: limpiar teléfono → formato internacional sin +
    // Ecuador: 09XXXXXXXX → 5939XXXXXXXX
    // =========================================================================
    private function limpiarTelefono(string $telefono): string
    {
        // Quitar todo excepto dígitos
        $limpio = preg_replace('/\D/', '', $telefono);

        if (empty($limpio)) return '';

        // Si ya tiene código de país Ecuador (593)
        if (str_starts_with($limpio, '593')) {
            return $limpio;
        }

        // Si empieza en 0 (formato local Ecuador: 09XXXXXXXX)
        if (str_starts_with($limpio, '0')) {
            return '593' . substr($limpio, 1);
        }

        // Si empieza en 9 (sin el 0 inicial)
        if (str_starts_with($limpio, '9') && strlen($limpio) === 9) {
            return '593' . $limpio;
        }

        return $limpio;
    }
}
