<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class UserProfile extends Component
{

    use WithFileUploads;

    // Datos editables
    public $name;
    public $firstname;
    public $lastname;
    public $cedula;
    public $email;
    public $phone;
    public $address;
    public $fecha_nacimiento;
    public $genero;
    public $estado_civil;
    public $nacionalidad;

    // Datos familiares
    public $padre, $madre, $tutor;

    // Datos personales
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

    // Foto
    public $photo;
    public $photo_preview;

    // Password
    public $password;
    public $password_confirmation;
    public $current_password;

    // Datos NO editables (solo mostrar)
    public $is_active;
    public $matricula_numero;

    public $user;

    public function mount()
    {
        $this->user = Auth::user();
        // Cargar datos
        $this->name = $this->user->name;
        $this->firstname = $this->user->first_name ?? null;
        $this->lastname = $this->user->last_name ?? null;
        $this->cedula = $this->user->cedula ?? null;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone ?? null;
        $this->address = $this->user->address ?? null;
        $this->fecha_nacimiento = $this->user->fecha_nacimiento?->format('Y-m-d');
        $this->matricula_numero = $this->user->matricula_numero;
        /* $this->fecha_nacimiento = optional($this->user->fecha_nacimiento)->format('Y-m-d'); */
        $this->genero = $this->user->genero ?? null;
        $this->nacionalidad = $this->user->nacionalidad ?? null;
        $this->estado_civil = $this->user->estado_civil ?? null;

        $this->padre = $this->user->padre;
        $this->madre = $this->user->madre;
        $this->tutor = $this->user->tutor;
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

        // No editables
        $this->is_active = $this->user->is_active ?? null;

        //$this->matricula_numero = $this->user->matriculas->first()->code ?? null;

        // Foto preview actual
        $this->photo_preview = $this->user->profile_photo_path ?? null;
    }

    public function rules()
    {
        return [
            /* 'name' => ['required', 'string', 'max:120'], */
            'firstname' => ['nullable', 'string', 'max:120'],
            'lastname' => ['nullable', 'string', 'max:120'],
            'cedula' => 'required|string|unique:users,cedula,' . $this->user->id,
            'email' => [
                'required',
                'email',
                'max:120',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'phone' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:255'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'genero' => ['nullable', 'string', 'max:20'],
            'estado_civil' => ['nullable', 'string', 'max:40'],
            'nacionalidad' => ['nullable', 'string', 'max:20'],
            /* 'matricula_numero' => 'nullable|string|max:50', */
            'padre' => 'nullable|string|max:255',
            'madre' => 'nullable|string|max:255',
            'tutor' => 'nullable|string|max:255',
            'telefono_emergencia' => 'nullable|string|max:10',
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

            /* 'photo' => ['nullable', 'image', 'max:2048'], */ // 2MB
        ];
    }

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
        if ($this->photo_preview) {
            Storage::disk('public')->delete($this->photo_preview);
            $this->user->update(['profile_photo_path' => null]);
            $this->photo_preview = null;
            session()->flash('message', 'Foto eliminada exitosamente.');
        }
    }

    /* public function deleteCertificado()
    {
        if ($this->certificado_discapacidad_actual) {
            Storage::disk('public')->delete($this->certificado_discapacidad_actual);
            $this->user->update(['certificado_discapacidad_path' => null]);
            $this->certificado_discapacidad_actual = null;
            session()->flash('message', 'Certificado eliminado exitosamente.');
        }
    } */

    /* public function updatedPhoto()
    {
        $this->validateOnly('photo');

        $this->photo_preview = $this->photo->temporaryUrl();
    } */

    public function saveProfile()
    {
        //dd($this->validate());
        $this->validate();


        $user = User::findOrFail($this->user->id);

        // 🔒 NO PERMITIR editar estos 3 JAMÁS
        // role_id, is_active, matricula_numero NO se tocan aquí

        $user->name = $this->firstname . ' ' . $this->lastname;
        $user->first_name = $this->firstname;
        $user->last_name = $this->lastname;
        $user->cedula = $this->cedula;
        $user->matricula_numero = $this->matricula_numero;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->address = $this->address;
        $user->fecha_nacimiento = $this->fecha_nacimiento;
        $user->genero = $this->genero;
        $user->estado_civil = $this->estado_civil;
        $user->nacionalidad = $this->nacionalidad;
        $user->telefono_emergencia = $this->telefono_emergencia;
        $user->contacto_emergencia = $this->contacto_emergencia;
        $user->padre = $this->padre;
        $user->madre = $this->madre;
        $user->tutor = $this->tutor;
        $user->observaciones_medicas = $this->observaciones_medicas;
        $user->tipo_sangre = $this->tipo_sangre;
        $user->discapacidad = $this->discapacidad;
        $user->discapacidad_descripcion = $this->discapacidad_descripcion;
        $user->is_facturador = $this->is_facturador;
        $user->fact_nombre = $this->fact_nombre;
        $user->fact_documento = $this->fact_documento;
        $user->fact_direccion = $this->fact_direccion;
        $user->fact_telefono = $this->fact_telefono;

        // Foto
        /* if ($this->photo) {
            $path = $this->photo->store('users-data/profile-photos', 'public');
            $user->profile_photo_path = $path;
        } */

        /* if ($this->photo) {
            if ($this->photo_preview) {
                Storage::disk('public')->delete($this->photo_preview);
            }
            $user->profile_photo_path = $this->photo->store('users-data/profile-photos', 'public');
        } */

        /*  if ($this->certificado_discapacidad) {
            if ($this->certificado_discapacidad_actual) {
                Storage::disk('public')->delete($this->certificado_discapacidad_actual);
            }
            $user->certificado_discapacidad_path = $this->certificado_discapacidad->store('users-data/certificados-discapacidad', 'public');
        } */

        $user->save();

        // Refrescar datos
        $this->user = $user;

        session()->flash('success', '✅ Tu perfil se actualizó correctamente.');
    }

    public function savePhoto()
    {

        $this->validate([
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $usuario = User::findOrFail($this->user->id);

        if ($this->photo) {
            if ($this->photo_preview) {
                Storage::disk('public')->delete($this->photo_preview);
            }
            $usuario->profile_photo_path = $this->photo->store('users-data/profile-photos', 'public');
        }

        $usuario->save();

        // Refrescar datos
        $this->user = $usuario;
        // limpiar
        $this->photo = '';
        session()->flash('success', '✅ Foto cargada correctamente.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail($this->user->id);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'La contraseña actual no es correcta.');
            return;
        }

        $user->password = Hash::make($this->password);
        $user->save();

        // limpiar
        $this->current_password = null;
        $this->password = null;
        $this->password_confirmation = null;

        session()->flash('success_password', '🔐 Contraseña actualizada correctamente.');
    }

    public function getEsDocenteProperty()
    {
        return auth()->user()->hasRole('Docente');
    }

    public function getEsEstudianteProperty()
    {
        return auth()->user()->hasRole('Estudiante');
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}
