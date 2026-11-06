<?php

namespace App\Livewire\Administration\Usuarios;

use App\Livewire\Concerns\ReenviaCredenciales;
use App\Models\AuditoriaGeneral;
use App\Models\User;
use App\Support\PerfilUsuario;
use App\Support\ReglasPersona;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

/**
 * Cuenta de acceso de cualquier persona (sección Usuarios): nombre, cédula, correo, contraseña,
 * estado y rol. Los datos personales se editan en su ficha (Estudiantes o Docentes).
 *
 * - Los datos de la cuenta exigen 'editar_usuarios'; el rol exige 'asignar_roles'.
 * - Super Admin solo lo asigna o modifica otro Super Admin.
 * - Nadie se desactiva a sí mismo ni se quita el permiso de asignar roles.
 */
class EditarCuenta extends Component
{
    use WithAuthorization, ReenviaCredenciales;

    public User $user;

    public string $first_name = '', $last_name = '', $cedula = '', $email = '';
    public ?string $password = null, $password_confirmation = null;
    public bool $is_active = true;
    public $role_id = null;

    public function mount(User $usuario): void
    {
        $this->requierePermiso('gestionar_usuarios');

        $this->user       = $usuario->load('roles');
        $this->first_name = (string) $usuario->first_name;
        $this->last_name  = (string) $usuario->last_name;
        $this->cedula     = (string) $usuario->cedula;
        $this->email      = (string) $usuario->email;
        $this->is_active  = (bool) $usuario->is_active;
        $this->role_id    = $usuario->roles->first()?->id;
    }

    /** Si no puede tocar esta cuenta (Super Admin), la pantalla queda solo de lectura. */
    public function motivoSoloLectura(): ?string
    {
        if ($motivo = PerfilUsuario::motivoNoPuedeModificar($this->user)) return $motivo;
        if (! auth()->user()->can('editar_usuarios') && ! auth()->user()->can('asignar_roles')) {
            return 'No tienes permiso para modificar cuentas.';
        }
        return null;
    }

    public function guardar(): void
    {
        if ($motivo = $this->motivoSoloLectura()) {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Sin permiso', 'text' => $motivo]);
            return;
        }

        $puedeDatos = auth()->user()->can('editar_usuarios');
        $puedeRol   = auth()->user()->can('asignar_roles');
        $rolesValidos = PerfilUsuario::rolesAsignables()->pluck('id')->all();

        $reglas = [];
        if ($puedeDatos) {
            $reglas += ReglasPersona::identidad($this->user->id) + ReglasPersona::contrasenaOpcional() + ['is_active' => 'boolean'];
        }
        if ($puedeRol) {
            $reglas['role_id'] = ['required', Rule::in($rolesValidos)];
        }
        $this->validate($reglas, ReglasPersona::mensajes());

        $esUnoMismo = auth()->id() === $this->user->id;
        $rolActual  = $this->user->roles->first();
        $rolNuevo   = $puedeRol ? Role::find($this->role_id) : $rolActual;

        if ($puedeDatos && ! $this->is_active && ($motivo = PerfilUsuario::motivoNoPuedeModificar($this->user, 'desactivar'))) {
            $this->addError('is_active', $motivo);
            return;
        }
        if ($esUnoMismo && $rolNuevo?->id !== $rolActual?->id && ! $rolNuevo?->hasPermissionTo('asignar_roles')) {
            $this->addError('role_id', 'No puedes quitarte a ti mismo el permiso de asignar roles.');
            return;
        }

        $antes = $this->resumenCuenta();

        DB::transaction(function () use ($puedeDatos, $puedeRol, $rolNuevo, $rolActual) {
            if ($puedeDatos) {
                $datos = [
                    'name'       => trim($this->first_name . ' ' . $this->last_name),
                    'first_name' => $this->first_name,
                    'last_name'  => $this->last_name,
                    'cedula'     => $this->cedula,
                    'email'      => $this->email,
                    'is_active'  => $this->is_active,
                ];
                if ($this->password) $datos['password'] = Hash::make($this->password);
                $this->user->update($datos);
            }
            if ($puedeRol && $rolNuevo && $rolNuevo->id !== $rolActual?->id) {
                $this->user->syncRoles([$rolNuevo]);
            }
        });

        $this->user->refresh()->load('roles');
        $despues = $this->resumenCuenta();
        $cambios = array_keys(array_diff_assoc($despues, $antes));
        if ($this->password) $cambios[] = 'contraseña';

        if ($cambios) {
            AuditoriaGeneral::create([
                'auditable_type'  => User::class,
                'auditable_id'    => $this->user->id,
                'evento'          => 'updated',
                'valores_antes'   => array_intersect_key($antes, array_flip($cambios)),
                'valores_despues' => array_intersect_key($despues, array_flip($cambios))
                    + ($this->password ? ['contraseña' => 'Cambiada a mano'] : []),
                'user_id'         => auth()->id(),
                'ip_address'      => request()?->ip(),
            ]);
        }

        $this->reset(['password', 'password_confirmation']);
        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => $cambios ? 'Cuenta actualizada.' : 'Sin cambios.',
            'text'  => $cambios ? $this->user->name . ' ha sido guardado correctamente.' : 'No había nada que guardar.',
            'timer' => 2500,
        ]);
    }

    private function resumenCuenta(): array
    {
        return [
            'name'      => (string) $this->user->name,
            'cedula'    => (string) $this->user->cedula,
            'email'     => (string) $this->user->email,
            'is_active' => $this->user->is_active ? 'Activo' : 'Inactivo',
            'rol'       => (string) $this->user->roles->pluck('name')->implode(', '),
        ];
    }

    public function render()
    {
        return view('livewire.administration.usuarios.editar-cuenta', [
            'roles'       => PerfilUsuario::rolesAsignables(),
            'rutaFicha'   => PerfilUsuario::rutaFicha($this->user),
            'soloLectura' => $this->motivoSoloLectura(),
        ]);
    }
}
