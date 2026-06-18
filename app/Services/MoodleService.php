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
                    'username' => $user->cedula,
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
    // CALENDARIO DE ACTIVIDADES MOODLE
    // Retorna los eventos del calendario para el estudiante (15 días pasados → 90 futuros)
    // =========================================================================
    public function obtenerEventosCalendario(int $moodleUserId): array
    {
        // 1. Obtener cursos en los que está matriculado el estudiante
        $respCursos = $this->call('core_enrol_get_users_courses', [
            'userid' => $moodleUserId,
        ]);

        if ($respCursos->failed()) {
            return [];
        }

        $cursos = $respCursos->json();

        if (! is_array($cursos) || isset($cursos['exception']) || empty($cursos)) {
            return [];
        }

        $courseIds = array_column($cursos, 'id');
        $cursosMap = array_column($cursos, 'fullname', 'id');

        // 2. Obtener eventos del calendario para esos cursos
        $ahora     = now();
        $timestart = $ahora->copy()->subDays(15)->startOfDay()->timestamp;
        $timeend   = $ahora->copy()->addDays(90)->endOfDay()->timestamp;

        $respEventos = $this->call('core_calendar_get_calendar_events', [
            'events' => [
                'courseids' => $courseIds,
            ],
            'options' => [
                'userevents' => 0,
                'siteevents' => 0,
                'timestart'  => $timestart,
                'timeend'    => $timeend,
            ],
        ]);

        if ($respEventos->failed()) {
            return [];
        }

        $data = $respEventos->json();

        if (isset($data['exception']) || ! isset($data['events'])) {
            Log::warning('MoodleService: respuesta inesperada en core_calendar_get_calendar_events', [
                'moodle_user_id' => $moodleUserId,
                'data'           => $data,
            ]);
            return [];
        }

        $ahoraTs          = $ahora->timestamp;
        $tiposPermitidos  = ['due', 'close', 'open', 'closeevent'];
        $modulosPermitidos = ['assign', 'quiz', 'forum', 'scorm', 'lesson', 'workshop'];

        return collect($data['events'])
            ->filter(function ($e) use ($tiposPermitidos, $modulosPermitidos) {
                if (empty($e['timestart'])) {
                    return false;
                }
                return in_array($e['eventtype'] ?? '', $tiposPermitidos)
                    || in_array($e['modulename'] ?? '', $modulosPermitidos);
            })
            ->map(function ($e) use ($ahoraTs, $cursosMap) {
                $ts      = (int) ($e['timestart'] ?? 0);
                $fecha   = \Carbon\Carbon::createFromTimestamp($ts);

                return [
                    'id'             => $e['id'],
                    'nombre'         => $e['name'],
                    'descripcion'    => strip_tags($e['description'] ?? ''),
                    'curso'          => $cursosMap[$e['courseid']] ?? 'Curso desconocido',
                    'courseid'       => $e['courseid'],
                    'modulename'     => $e['modulename'] ?? '',
                    'eventtype'      => $e['eventtype'] ?? '',
                    'timestart'      => $ts,
                    'fecha'          => $fecha->format('d/m/Y H:i'),
                    'fecha_relativa' => $fecha->diffForHumans(),
                    'vencido'        => $ts < $ahoraTs,
                    'url'            => $e['url'] ?? null,
                ];
            })
            ->sortBy('timestart')
            ->values()
            ->toArray();
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
