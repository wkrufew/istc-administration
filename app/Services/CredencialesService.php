<?php

namespace App\Services;

use App\Mail\BienvenidaAspirante;
use App\Mail\ReenvioCredencialesAcceso;
use App\Models\Aspirante;
use App\Models\AuditoriaGeneral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Reenvío de credenciales: un solo lugar para Aspirantes, Estudiantes, Docentes y Usuarios.
 *
 * La contraseña se restablece a la cédula y el correo sale en el momento (no en cola) para
 * mostrar el resultado real. Todo ocurre en una sola operación con el correo al final: si el
 * envío falla se deshacen la contraseña, el registro y la auditoría. Entre dos envíos a la
 * misma persona hay una espera corta (evita duplicados por doble clic o dos personas a la vez).
 *
 * Errores: \DomainException = no se puede enviar (mensaje listo para mostrar);
 *          \RuntimeException = el correo no salió (mensaje listo para mostrar, ya registrado en el log).
 */
class CredencialesService
{
    // ── Acceso interno: administrativos, docentes y estudiantes ───────────────

    /** Tipo de acceso que anuncia el correo, o null si a esta persona no le aplica el reenvío. */
    public static function tipoAcceso(User $user): ?string
    {
        $rol = $user->roles->first();

        return match (true) {
            ! $rol                                          => null,
            $rol->hasPermissionTo('acceso_administrativo') => 'administrativo',
            $rol->hasPermissionTo('acceso_docencia')       => 'docente',
            $rol->hasPermissionTo('acceso_estudiantil')
                && $user->matriculas()->where('estado', 'Habilitada')->exists() => 'estudiante',
            default                                         => null,
        };
    }

    /** Por qué no se le pueden reenviar credenciales (null = se puede). */
    public static function motivoNoReenvioAcceso(User $user): ?string
    {
        $rol = $user->roles->first();
        if (! $rol) return 'El usuario no tiene un rol asignado.';

        if (! self::tipoAcceso($user)) {
            return $rol->hasPermissionTo('acceso_estudiantil')
                ? 'El estudiante no tiene ninguna matrícula habilitada. Habilita una matrícula antes de reenviar credenciales.'
                : 'El reenvío de credenciales solo aplica a usuarios administrativos, docentes o estudiantes con matrícula habilitada.';
        }

        return self::motivoComun($user);
    }

    /** Segundos que faltan para volver a enviarle credenciales, según su último reenvío auditado. */
    public static function segundosParaReenvio(User $user): int
    {
        $ultimo = AuditoriaGeneral::where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->whereNotNull('valores_despues->credenciales')
            ->latest('created_at')
            ->value('created_at');

        return self::segundosDesde($ultimo ? Carbon::parse($ultimo) : null);
    }

    public static function reenviarAcceso(User $user): void
    {
        $user->loadMissing('roles');
        if ($motivo = self::motivoNoReenvioAcceso($user)) {
            throw new \DomainException($motivo);
        }

        $correo = new ReenvioCredencialesAcceso(
            usuario: $user,
            plainPassword: $user->cedula,
            tipoAcceso: self::tipoAcceso($user),
            nombreRol: $user->roles->first()->name,
        );

        self::ejecutar($user, $correo, User::class, $user->id, ['credenciales' => 'Reenviadas a ' . $user->email],
            preparar: function () use ($user) {
                User::whereKey($user->id)->lockForUpdate()->first();
                self::exigirEspera(self::segundosParaReenvio($user));
            });
    }

    // ── Aspirantes (portal de admisión) ───────────────────────────────────────

    public static function reenviarAspirante(Aspirante $aspirante): void
    {
        $aspirante->loadMissing('user');
        $user = $aspirante->user;

        if ($motivo = $aspirante->motivoNoReenvioCredenciales() ?? self::motivoComun($user)) {
            throw new \DomainException($motivo);
        }

        self::ejecutar($user, new BienvenidaAspirante($user, $user->cedula, esReenvio: true),
            Aspirante::class, $aspirante->id, ['user_id' => $user->id, 'credenciales' => 'Reenviadas a ' . $user->email],
            preparar: function () use ($aspirante) {
                $bloqueado = Aspirante::whereKey($aspirante->id)->lockForUpdate()->first();
                self::exigirEspera($bloqueado->segundosParaReenvio());
                $bloqueado->update(['credenciales_enviadas_at' => now(), 'credenciales_enviadas_por' => auth()->id()]);
            });
    }

    // ── Interno ───────────────────────────────────────────────────────────────

    public static function segundosDesde(?Carbon $ultimoEnvio): int
    {
        if (! $ultimoEnvio) return 0;

        $disponible = $ultimoEnvio->copy()->addMinutes(User::ESPERA_REENVIO_CREDENCIALES_MINUTOS);
        return $disponible->isFuture() ? (int) ceil(now()->diffInSeconds($disponible)) : 0;
    }

    private static function motivoComun(User $user): ?string
    {
        if (! $user->cedula) return 'No tiene cédula registrada (la contraseña se restablece a la cédula).';
        if (! $user->email)  return 'No tiene correo registrado.';
        if (SettingService::get('smtp.activo', '0') !== '1') {
            return 'El envío de correos está desactivado en Ajustes → Correo. Actívalo para poder reenviar credenciales.';
        }

        return null;
    }

    private static function exigirEspera(int $segundos): void
    {
        if ($segundos > 0) {
            throw new \DomainException("Ya se enviaron hace un momento. Espera {$segundos} segundos antes de volver a enviarlas.");
        }
    }

    /**
     * @param callable $preparar bloquea el registro, verifica la espera y guarda lo propio de cada
     *                           caso; corre dentro de la transacción antes de cambiar la contraseña
     */
    private static function ejecutar(User $user, Mailable $correo, string $auditableType, int $auditableId,
                                     array $valoresDespues, callable $preparar): void
    {
        try {
            DB::transaction(function () use ($user, $correo, $auditableType, $auditableId, $valoresDespues, $preparar) {
                $preparar();

                $user->update(['password' => Hash::make($user->cedula)]);

                AuditoriaGeneral::create([
                    'auditable_type'  => $auditableType,
                    'auditable_id'    => $auditableId,
                    'evento'          => 'updated',
                    'valores_antes'   => null,
                    'valores_despues' => $valoresDespues,
                    'user_id'         => auth()->id(),
                    'ip_address'      => request()?->ip(),
                ]);

                // Al final: si falla, se deshace todo lo anterior
                SettingService::buildMailer()->to($user->email)->send($correo);
            });
        } catch (\DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Reenvío de credenciales falló', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
            throw new \RuntimeException(
                'No se pudo enviar el correo a ' . $user->email . '. La contraseña no se cambió.'
                    . (app()->isLocal() ? ' Detalle: ' . $e->getMessage() : ' Revisa la configuración del correo o inténtalo más tarde.'),
                0, $e
            );
        }
    }
}
