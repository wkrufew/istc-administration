<?php

namespace App\Livewire\Administration\Docentes;

use App\Livewire\Concerns\GestionaFotoPerfil;
use App\Livewire\Concerns\ReenviaCredenciales;
use App\Models\User;
use App\Support\PerfilUsuario;
use App\Support\ReglasPersona;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Datos del docente (sección Docentes): solo lo que aplica al personal. Sus documentos y
 * materias asignadas tienen su propia pantalla; la cuenta —rol y acceso— se maneja en Usuarios.
 */
class EditarDocente extends Component
{
    use WithFileUploads, WithAuthorization, GestionaFotoPerfil, ReenviaCredenciales;

    public User $user;

    public string $first_name = '', $last_name = '', $cedula = '', $email = '';
    public ?string $phone = null, $address = null, $fecha_nacimiento = null;
    public ?string $genero = null, $nacionalidad = null, $estado_civil = null;
    public ?string $contacto_emergencia = null, $telefono_emergencia = null;
    public ?string $password = null, $password_confirmation = null;
    public bool $is_active = true;

    // Discapacidad (de esto depende que se le pida el certificado CONADIS)
    public bool $tiene_discapacidad = false;
    public ?string $tipo_discapacidad = null, $porcentaje_discapacidad = null, $nro_conadis = null;

    public function mount(User $docente): void
    {
        $this->requierePermiso('gestionar_docentes');

        $this->user = $docente;
        $this->fill($docente->only([
            'phone', 'address', 'genero', 'nacionalidad', 'estado_civil', 'contacto_emergencia', 'telefono_emergencia',
        ]));
        $this->first_name = (string) $docente->first_name;
        $this->last_name  = (string) $docente->last_name;
        $this->cedula     = (string) $docente->cedula;
        $this->email      = (string) $docente->email;
        $this->fecha_nacimiento     = $docente->fecha_nacimiento?->format('Y-m-d');
        $this->is_active            = (bool) $docente->is_active;
        $this->tiene_discapacidad      = (bool) $docente->tiene_discapacidad;
        $this->tipo_discapacidad       = $docente->tipo_discapacidad;
        $this->porcentaje_discapacidad = $docente->porcentaje_discapacidad !== null ? (string) $docente->porcentaje_discapacidad : null;
        $this->nro_conadis             = $docente->nro_conadis;
        $this->profile_photo_actual = $docente->profile_photo_path;
    }

    protected function permisoEdicion(): string
    {
        return 'editar_usuarios';
    }

    protected function rules(): array
    {
        return ReglasPersona::identidad($this->user->id)
            + ReglasPersona::contacto()
            + ReglasPersona::contrasenaOpcional()
            + $this->reglasFoto()
            + [
                'is_active'               => 'boolean',
                'tiene_discapacidad'      => 'boolean',
                'tipo_discapacidad'       => 'nullable|string|max:60',
                'porcentaje_discapacidad' => 'nullable|numeric|min:0|max:100',
                'nro_conadis'             => 'nullable|string|max:50',
            ];
    }

    protected function messages(): array
    {
        return ReglasPersona::mensajes();
    }

    public function guardar(): void
    {
        if ($this->sinPermiso($this->permisoEdicion())) return;

        if (! $this->is_active && ($motivo = PerfilUsuario::motivoNoPuedeModificar($this->user, 'desactivar'))) {
            $this->addError('is_active', $motivo);
            return;
        }

        $this->validate();

        $datos = [
            'name'                => trim($this->first_name . ' ' . $this->last_name),
            'first_name'          => $this->first_name,
            'last_name'           => $this->last_name,
            'cedula'              => $this->cedula,
            'email'               => $this->email,
            'phone'               => $this->phone,
            'address'             => $this->address,
            'fecha_nacimiento'    => $this->fecha_nacimiento ?: null,
            'genero'              => $this->genero ?: null,
            'nacionalidad'        => $this->nacionalidad,
            'estado_civil'        => $this->estado_civil,
            'contacto_emergencia' => $this->contacto_emergencia,
            'telefono_emergencia' => $this->telefono_emergencia,
            'is_active'           => $this->is_active,
            // Si no tiene discapacidad, los datos dependientes se vacían
            'tiene_discapacidad'      => $this->tiene_discapacidad,
            'tipo_discapacidad'       => $this->tiene_discapacidad ? ($this->tipo_discapacidad ?: null) : null,
            'porcentaje_discapacidad' => $this->tiene_discapacidad && $this->porcentaje_discapacidad !== '' ? $this->porcentaje_discapacidad : null,
            'nro_conadis'             => $this->tiene_discapacidad ? ($this->nro_conadis ?: null) : null,
        ];
        if ($this->password) {
            $datos['password'] = Hash::make($this->password);
        }
        $this->aplicarFotoNueva($datos);

        $this->user->update($datos);
        $this->reset(['password', 'password_confirmation']);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Docente actualizado.',
            'text'  => $this->user->name . ' ha sido guardado correctamente.',
            'timer' => 2500,
        ]);
    }

    public function render()
    {
        return view('livewire.administration.docentes.editar-docente', [
            'asignacionesCount' => $this->user->asignacionesDocente()->count(),
        ]);
    }
}
