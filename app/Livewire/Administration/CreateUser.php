<?php

namespace App\Livewire\Administration;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Traits\WithAuthorization;

class CreateUser extends Component
{
    use WithFileUploads, WithAuthorization;

    // Datos básicos
    public $first_name, $last_name, $email, $password, $password_confirmation;
    public $cedula, $phone, $address, $fecha_nacimiento, $matricula_numero;

    // Datos familiares
    public $padre, $madre, $tutor;

    // Datos personales
    public $nacionalidad, $genero, $estado_civil;
    public $telefono_emergencia, $contacto_emergencia;
    public $tipo_sangre, $observaciones_medicas;

    // Discapacidad
    public $discapacidad = false;
    public $discapacidad_descripcion;
    public $certificado_discapacidad;

    // Datos de facturación
    public $is_facturador = false;
    public $fact_nombre, $fact_documento, $fact_correo, $fact_direccion, $fact_telefono;

    // Otros
    public $profile_photo;
    public $role_id;
    public $is_active = true;

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'cedula' => 'required|string|unique:users,cedula',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'nullable|date',
            'matricula_numero' => 'nullable|string|max:50',
            'padre' => 'nullable|string|max:255',
            'madre' => 'nullable|string|max:255',
            'tutor' => 'nullable|string|max:255',
            'nacionalidad' => 'nullable|string|max:100',
            'genero' => 'nullable|in:Masculino,Femenino,Otro',
            'estado_civil' => 'nullable|string|max:50',
            'telefono_emergencia' => 'nullable|string|max:20',
            'contacto_emergencia' => 'nullable|string|max:255',
            'tipo_sangre' => 'nullable|string|max:10',
            'observaciones_medicas' => 'nullable|string',
            'discapacidad' => 'boolean',
            'discapacidad_descripcion' => 'required_if:discapacidad,true|nullable|string',
            'certificado_discapacidad' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'is_facturador' => 'boolean',
            'fact_nombre' => 'required_if:is_facturador,true|nullable|string|max:255',
            'fact_documento' => 'required_if:is_facturador,true|nullable|string|max:50',
            'fact_correo' => 'required_if:is_facturador,true|nullable|email',
            'fact_direccion' => 'required_if:is_facturador,true|nullable|string',
            'fact_telefono' => 'required_if:is_facturador,true|nullable|string|max:20',
            'profile_photo' => 'nullable|image|max:2048',
            'role_id' => 'required|exists:roles,id',
        ];
    }

    protected $messages = [
        'first_name.required' => 'El nombre es obligatorio',
        'last_name.required' => 'El apellido es obligatorio',
        'email.required' => 'El correo electrónico es obligatorio',
        'email.email' => 'Ingrese un correo válido',
        'email.unique' => 'Este correo ya está registrado',
        'password.required' => 'La contraseña es obligatoria',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        'password.confirmed' => 'Las contraseñas no coinciden',
        'cedula.required' => 'La cédula es obligatoria',
        'cedula.unique' => 'Esta cédula ya está registrada',
        'role_id.required' => 'Debe seleccionar un rol',
        'discapacidad_descripcion.required_if' => 'Describa la discapacidad',
    ];

    public function updatedDiscapacidad($value)
    {
        if (!$value) {
            $this->discapacidad_descripcion = null;
            $this->certificado_discapacidad = null;
        }
    }

    public function updatedIsFacturador($value)
    {
        if ($value) {
            $this->fact_nombre = null;
            $this->fact_documento = null;
            $this->fact_correo = null;
            $this->fact_direccion = null;
            $this->fact_telefono = null;
        }
    }

    public function save()
    {
        if ($this->sinPermiso('gestionar_usuarios')) return;

        $this->validate();

        $profilePhotoPath = null;
        if ($this->profile_photo) {
            $profilePhotoPath = $this->profile_photo->store('users-data/profile-photos', 'public');
        }

        $certificadoPath = null;
        if ($this->certificado_discapacidad) {
            $certificadoPath = $this->certificado_discapacidad->store('users-data/certificados-discapacidad', 'public');
        }

        DB::transaction(function () use ($profilePhotoPath, $certificadoPath) {
            $user = User::create([
                'name' => $this->first_name . ' ' . $this->last_name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'cedula' => $this->cedula,
                'phone' => $this->phone,
                'address' => $this->address,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'matricula_numero' => $this->matricula_numero,
                'padre' => $this->padre,
                'madre' => $this->madre,
                'tutor' => $this->tutor,
                'nacionalidad' => $this->nacionalidad,
                'genero' => $this->genero,
                'estado_civil' => $this->estado_civil,
                'telefono_emergencia' => $this->telefono_emergencia,
                'contacto_emergencia' => $this->contacto_emergencia,
                'tipo_sangre' => $this->tipo_sangre,
                'observaciones_medicas' => $this->observaciones_medicas,
                'discapacidad' => $this->discapacidad,
                'discapacidad_descripcion' => $this->discapacidad_descripcion,
                'certificado_discapacidad_path' => $certificadoPath,
                'is_facturador' => $this->is_facturador,
                'fact_nombre' => $this->fact_nombre,
                'fact_documento' => $this->fact_documento,
                'fact_correo' => $this->fact_correo,
                'fact_direccion' => $this->fact_direccion,
                'fact_telefono' => $this->fact_telefono,
                'profile_photo_path' => $profilePhotoPath,
                'is_active' => $this->is_active,
            ]);

            $user->assignRole(Role::find($this->role_id));
        });

        session()->flash('message', 'Usuario creado exitosamente.');

        return redirect()->route('administracion.administrativa.users.index');
    }

    public function render()
    {
        $roles = Role::whereIn('name', ['Administrador', 'Secretaria', 'Docente', 'Estudiante', 'Admision'])->get();

        return view('livewire.administration.create-user', [
            'roles' => $roles
        ]);
    }
}
