<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

/**
 * Quién es cada persona en el sistema y qué se puede hacer con su cuenta.
 *
 * El menú se organiza por perfil: Estudiantes, Docentes y Usuarios (todas las cuentas).
 * Administrador y Super Admin reciben todos los permisos, incluidos los de los portales, así
 * que se excluyen a propósito de los listados de estudiantes y docentes.
 */
class PerfilUsuario
{
    /** Roles del sistema: tienen todos los permisos pero no son estudiantes ni docentes. */
    const ROLES_SISTEMA = ['Super Admin', 'Administrador'];

    const SUPER_ADMIN = 'Super Admin';

    const ESTUDIANTE = 'estudiante';
    const DOCENTE    = 'docente';
    const CUENTA     = 'cuenta';

    // ── Listados ──────────────────────────────────────────────────────────────

    public static function estudiantes(): Builder
    {
        return User::permission('acceso_estudiantil')
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', self::ROLES_SISTEMA));
    }

    /** Incluye a las autoridades que también dictan clases (Rector, Vicerrectora, etc.). */
    public static function docentes(): Builder
    {
        return User::permission('acceso_docencia')
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', self::ROLES_SISTEMA));
    }

    // ── Una persona ───────────────────────────────────────────────────────────

    public static function esEstudiante(User $user): bool
    {
        return ! self::esDeSistema($user) && $user->hasPermissionTo('acceso_estudiantil');
    }

    public static function esDocente(User $user): bool
    {
        return ! self::esDeSistema($user) && $user->hasPermissionTo('acceso_docencia');
    }

    public static function esSuperAdmin(?User $user): bool
    {
        return (bool) $user?->hasRole(self::SUPER_ADMIN);
    }

    private static function esDeSistema(User $user): bool
    {
        return $user->hasAnyRole(self::ROLES_SISTEMA);
    }

    /**
     * Dónde se editan los datos personales de la persona (su ficha), si tiene una y quien
     * consulta tiene permiso para verla. En Usuarios solo se maneja la cuenta.
     */
    public static function rutaFicha(User $user, ?User $quienConsulta = null): ?string
    {
        $quienConsulta ??= auth()->user();

        if (self::esEstudiante($user)) {
            return $quienConsulta?->can('gestionar_estudiantes')
                ? route('administracion.administrativa.estudiantes.edit', $user) : null;
        }
        if (self::esDocente($user)) {
            return $quienConsulta?->can('gestionar_docentes')
                ? route('administracion.administrativa.docentes.edit', $user) : null;
        }
        if ($user->hasPermissionTo('acceso_admision') && $user->cedula) {
            return $quienConsulta?->can('gestionar_aspirantes')
                ? route('administracion.administrativa.aspirantes.index', ['q' => $user->cedula]) : null;
        }

        return null;
    }

    // ── Roles ─────────────────────────────────────────────────────────────────

    /**
     * Roles que quien está conectado puede asignar. Al crear desde Estudiantes o Docentes solo
     * se ofrecen los de ese perfil; en Usuarios, los administrativos. Super Admin solo lo
     * asigna otro Super Admin.
     */
    public static function rolesAsignables(?string $perfil = null, ?User $quienAsigna = null): Collection
    {
        $quienAsigna ??= auth()->user();

        $roles = Role::with('permissions:id,name')->orderBy('name')->get()
            ->reject(fn(Role $r) => $r->name === self::SUPER_ADMIN && ! self::esSuperAdmin($quienAsigna));

        $tiene = fn(Role $r, string $permiso) => $r->permissions->contains('name', $permiso);

        $filtrados = match ($perfil) {
            self::ESTUDIANTE => $roles->filter(fn($r) => $tiene($r, 'acceso_estudiantil') && ! $tiene($r, 'acceso_administrativo')),
            self::DOCENTE    => $roles->filter(fn($r) => $tiene($r, 'acceso_docencia') && ! $tiene($r, 'acceso_administrativo')),
            self::CUENTA     => $roles->filter(fn($r) => $tiene($r, 'acceso_administrativo')),
            default          => $roles,
        };

        return $filtrados->values();
    }

    /**
     * Por qué quien está conectado no puede modificar la cuenta de $objetivo (null = sí puede).
     * Protege al Super Admin y evita que alguien se desactive o elimine a sí mismo.
     */
    public static function motivoNoPuedeModificar(User $objetivo, string $accion = 'modificar', ?User $quien = null): ?string
    {
        $quien ??= auth()->user();

        if (self::esSuperAdmin($objetivo) && ! self::esSuperAdmin($quien)) {
            return 'Solo un Super Admin puede ' . $accion . ' la cuenta de un Super Admin.';
        }
        if (in_array($accion, ['desactivar', 'eliminar'], true) && $quien && $quien->id === $objetivo->id) {
            return 'No puedes ' . $accion . ' tu propia cuenta.';
        }

        return null;
    }
}
