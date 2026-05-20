<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(SettingService::get('moodle.url', ''), '/');
        $this->token   = SettingService::get('moodle.token', '');
    }

    // =========================================================================
    // ¿ESTÁ ACTIVA LA INTEGRACIÓN?
    // =========================================================================
    public static function activo(): bool
    {
        return (int) env('MOODLE_MODE', 0) === 1
            && SettingService::get('moodle.activo', '0') === '1';
    }

    // =========================================================================
    // BUSCAR USUARIO — primero por idnumber (cédula), luego por email
    // Retorna el usuario Moodle o null si no existe
    // =========================================================================
    public function buscarUsuario(User $user): ?array
    {
        // Intento 1: por idnumber (cédula)
        if ($user->cedula) {
            $resultado = $this->buscarPorCampo('idnumber', $user->cedula);
            if ($resultado !== null) {
                return $resultado;
            }
        }

        // Intento 2: por email
        if ($user->email) {
            $resultado = $this->buscarPorCampo('email', $user->email);
            if ($resultado !== null) {
                return $resultado;
            }
        }

        return null;
    }

    // =========================================================================
    // CREAR USUARIO EN MOODLE
    // Retorna el moodle_id del usuario creado
    // =========================================================================
    public function crearUsuario(User $user): int
    {
        $response = $this->call('core_user_create_users', [
            'users' => [
                [
                    'username'    => $user->cedula,
                    'password'    => $user->cedula,
                    'firstname'   => $user->first_name ?? $user->name,
                    'lastname'    => $user->last_name  ?? '',
                    'email'       => $user->email,
                    'idnumber'    => $user->cedula ?? '',
                    'lang'        => 'es',
                    'auth'        => 'manual',
                    'phone1'      => $user->phone   ?? '',
                    'address'     => $user->address ?? '',
                ],
            ],
        ]);

        $data = $response->json();

        if (isset($data[0]['id'])) {
            return (int) $data[0]['id'];
        }

        $this->lanzarError('core_user_create_users', $data, $user);
    }

    // =========================================================================
    // ACTUALIZAR DATOS DEL USUARIO EN MOODLE
    // =========================================================================
    public function actualizarUsuario(User $user): void
    {
        if (! $user->moodle_id) {
            throw new \RuntimeException("El usuario #{$user->id} no tiene moodle_id asignado.");
        }

        $response = $this->call('core_user_update_users', [
            'users' => [
                [
                    'id'        => $user->moodle_id,
                    'firstname' => $user->first_name ?? $user->name,
                    'lastname'  => $user->last_name  ?? '',
                    'email'     => $user->email,
                    'idnumber'  => $user->cedula ?? '',
                    'phone1'    => $user->phone   ?? '',
                    'address'   => $user->address ?? '',
                ],
            ],
        ]);

        $data = $response->json();

        if ($response->failed() || isset($data['exception'])) {
            $this->lanzarError('core_user_update_users', $data, $user);
        }
    }

    // =========================================================================
    // SUSPENDER / REACTIVAR ACCESO EN MOODLE
    // =========================================================================
    public function suspenderAcceso(User $user, bool $suspender): void
    {
        if (! $user->moodle_id) {
            throw new \RuntimeException("El usuario #{$user->id} no tiene moodle_id asignado.");
        }

        $response = $this->call('core_user_update_users', [
            'users' => [
                [
                    'id'        => $user->moodle_id,
                    'suspended' => $suspender ? 1 : 0,
                ],
            ],
        ]);

        $data = $response->json();

        if ($response->failed() || isset($data['exception'])) {
            $accion = $suspender ? 'suspender' : 'reactivar';
            $this->lanzarError("core_user_update_users ({$accion})", $data, $user);
        }
    }

    // =========================================================================
    // RESTABLECER CONTRASEÑA A CÉDULA EN MOODLE
    // =========================================================================
    public function restablecerPassword(User $user): void
    {
        if (! $user->moodle_id) {
            throw new \RuntimeException("El usuario #{$user->id} no tiene moodle_id asignado.");
        }

        if (! $user->cedula) {
            throw new \RuntimeException("El usuario #{$user->id} no tiene cédula registrada.");
        }

        $response = $this->call('core_user_update_users', [
            'users' => [
                [
                    'id'       => $user->moodle_id,
                    'password' => $user->cedula,
                ],
            ],
        ]);

        $data = $response->json();

        if ($response->failed() || isset($data['exception'])) {
            $this->lanzarError('core_user_update_users (reset password)', $data, $user);
        }
    }

    // =========================================================================
    // PRIVADOS
    // =========================================================================
    private function buscarPorCampo(string $field, string $value): ?array
    {
        $response = $this->call('core_user_get_users_by_field', [
            'field'  => $field,
            'values' => [$value],
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if (isset($data['exception'])) {
            Log::warning('MoodleService: excepción al buscar por campo', [
                'field' => $field,
                'value' => $value,
                'data'  => $data,
            ]);
            return null;
        }

        if (is_array($data) && count($data) > 0 && isset($data[0]['id'])) {
            return $data[0];
        }

        return null;
    }

    private function call(string $function, array $params = []): Response
    {
        return Http::timeout(30)
            ->asForm()
            ->post("{$this->baseUrl}/webservice/rest/server.php", array_merge([
                'wstoken'                => $this->token,
                'wsfunction'             => $function,
                'moodlewsrestformat'     => 'json',
            ], $this->flattenParams($params)));
    }

    // Moodle REST necesita parámetros en formato array[0][campo]=valor
    private function flattenParams(array $params, string $prefix = ''): array
    {
        $flat = [];
        foreach ($params as $key => $value) {
            $fullKey = $prefix !== '' ? "{$prefix}[{$key}]" : $key;
            if (is_array($value)) {
                $flat = array_merge($flat, $this->flattenParams($value, $fullKey));
            } else {
                $flat[$fullKey] = $value;
            }
        }
        return $flat;
    }

    private function lanzarError(string $funcion, mixed $data, User $user): never
    {
        $mensaje = $data['message'] ?? $data['error'] ?? json_encode($data);
        Log::error("MoodleService: error en {$funcion}", [
            'user_id' => $user->id,
            'email'   => $user->email,
            'data'    => $data,
        ]);
        throw new \RuntimeException("Moodle [{$funcion}]: {$mensaje}");
    }
}
