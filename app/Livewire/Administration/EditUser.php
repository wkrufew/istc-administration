<?php

namespace App\Livewire\Administration;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class EditUser extends Component
{
    use WithFileUploads;

    public $userId;
    public $user;

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
    public $certificado_discapacidad_actual;

    // Datos de facturación
    public $is_facturador = false;
    public $fact_nombre, $fact_documento, $fact_correo, $fact_direccion, $fact_telefono;

    // Otros
    public $profile_photo;
    public $profile_photo_actual;
    public $role_id;
    public $is_active = true;

    public function mount($estudiante)
    {

        //dd($estudiante);
        $this->userId = $estudiante->id;
        $this->user = User::findOrFail($this->userId);

        // Cargar datos
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->cedula = $this->user->cedula;
        $this->phone = $this->user->phone;
        $this->address = $this->user->address;
        //$this->fecha_nacimiento = $this->user->fecha_nacimiento;
        //fecha de nacimiento formateada
        $this->fecha_nacimiento = $this->user->fecha_nacimiento
            ? $this->user->fecha_nacimiento->format('Y-m-d')
            : null;

        $this->matricula_numero = $this->user->matricula_numero;
        $this->padre = $this->user->padre;
        $this->madre = $this->user->madre;
        $this->tutor = $this->user->tutor;
        $this->nacionalidad = $this->user->nacionalidad;
        $this->genero = $this->user->genero;
        $this->estado_civil = $this->user->estado_civil;
        $this->telefono_emergencia = $this->user->telefono_emergencia;
        $this->contacto_emergencia = $this->user->contacto_emergencia;
        $this->tipo_sangre = $this->user->tipo_sangre;
        $this->observaciones_medicas = $this->user->observaciones_medicas;
        $this->discapacidad = $this->user->discapacidad ?? false;
        $this->discapacidad_descripcion = $this->user->discapacidad_descripcion;
        $this->certificado_discapacidad_actual = $this->user->certificado_discapacidad_path;
        //dd($this->user->is_facturador);
        $this->is_facturador = $this->user->is_facturador ?? false;
        $this->fact_nombre = $this->user->fact_nombre;
        $this->fact_documento = $this->user->fact_documento;
        $this->fact_correo = $this->user->fact_correo;
        $this->fact_direccion = $this->user->fact_direccion;
        $this->fact_telefono = $this->user->fact_telefono;
        //dd($this->user->profile_photo_path);
        $this->profile_photo_actual = $this->user->profile_photo_path;
        $this->is_active = $this->user->is_active;

        $this->role_id = $this->user->roles->first()?->id;
    }

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => 'nullable|min:8|confirmed',
            'cedula' => 'required|string|unique:users,cedula,' . $this->userId,
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
            'fact_nombre' => 'required_if:is_facturador,false|nullable|string|max:255',
            'fact_documento' => 'required_if:is_facturador,false|nullable|string|max:50',
            'fact_correo' => 'required_if:is_facturador,false|nullable|email',
            'fact_direccion' => 'required_if:is_facturador,false|nullable|string',
            'fact_telefono' => 'required_if:is_facturador,false|nullable|string|max:20',
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

    public function deletePhoto()
    {
        if ($this->profile_photo_actual) {
            Storage::disk('public')->delete($this->profile_photo_actual);
            $this->user->update(['profile_photo_path' => null]);
            $this->profile_photo_actual = null;
            session()->flash('message', 'Foto eliminada exitosamente.');
        }
    }

    public function deleteCertificado()
    {
        if ($this->certificado_discapacidad_actual) {
            Storage::disk('public')->delete($this->certificado_discapacidad_actual);
            $this->user->update(['certificado_discapacidad_path' => null]);
            $this->certificado_discapacidad_actual = null;
            session()->flash('message', 'Certificado eliminado exitosamente.');
        }
    }

    public function update()
    {
        $this->validate();

        $data = [
            'name' => $this->first_name . ' ' . $this->last_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
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
            'is_facturador' => $this->is_facturador,
            'fact_nombre' => $this->fact_nombre,
            'fact_documento' => $this->fact_documento,
            'fact_correo' => $this->fact_correo,
            'fact_direccion' => $this->fact_direccion,
            'fact_telefono' => $this->fact_telefono,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->profile_photo) {
            if ($this->profile_photo_actual) {
                Storage::disk('public')->delete($this->profile_photo_actual);
            }
            $data['profile_photo_path'] = $this->profile_photo->store('users-data/profile-photos', 'public');
        }

        if ($this->certificado_discapacidad) {
            if ($this->certificado_discapacidad_actual) {
                Storage::disk('public')->delete($this->certificado_discapacidad_actual);
            }
            $data['certificado_discapacidad_path'] = $this->certificado_discapacidad->store('users-data/certificados-discapacidad', 'public');
        }

        $this->user->update($data);

        //dd($data);


        // Actualizar rol
        $role = Role::find($this->role_id);
        $this->user->syncRoles([$role]);

        session()->flash('message', 'Usuario actualizado exitosamente.');
        $this->dispatch('alert', [
            'message' => 'El usuario ' . $this->user->name .   'ha sido actualizado con éxito.',
            'type' => 'success',
            'title' => 'Actualización exitosa'
        ]);

        /* return redirect()->route('administracion.administrativa.estudiantes.index'); */
        return redirect()->back();
    }

    public function render()
    {
        $roles = Role::whereIn('name', ['Administrador', 'Secretaria', 'Docente', 'Estudiante', 'Admision'])->get();

        return view('livewire.administration.edit-user', [
            'roles' => $roles
        ]);
    }
}
