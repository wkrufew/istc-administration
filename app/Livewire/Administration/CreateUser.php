<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarBienvenidaAccesoInternoJob;
use App\Models\User;
use App\Services\SettingService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
    public $nacionalidad, $etnia, $genero, $estado_civil;
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

    // Consulta cédula — entry modal
    public bool   $apiActiva        = false;
    public bool   $showEntryModal   = false;
    public string $cedulaModalInput = '';
    public array  $cedulaModalData  = [];

    public function mount(): void
    {
        $this->apiActiva = !empty(SettingService::get('cedula_api.token', ''));
    }

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
            'etnia' => 'nullable|string|max:100',
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
            'fact_nombre' => 'nullable|string|max:255',
            'fact_documento' => 'nullable|string|max:50',
            'fact_correo' => 'nullable|email',
            'fact_direccion' => 'nullable|string',
            'fact_telefono' => 'nullable|string|max:20',
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

    public function abrirEntryModal(): void
    {
        $this->showEntryModal = true;
        $this->dispatch('entry-modal-opened');
    }

    public function cerrarEntryModal(): void
    {
        $this->showEntryModal   = false;
        $this->cedulaModalInput = '';
        $this->cedulaModalData  = [];
        $this->resetErrorBag('cedulaModal');
        $this->dispatch('entry-modal-closed');
    }

    public function consultarEnModal(): void
    {
        $this->resetErrorBag('cedulaModal');

        if (!trim($this->cedulaModalInput)) {
            $this->addError('cedulaModal', 'Ingrese el número de cédula.');
            return;
        }

        $token = SettingService::get('cedula_api.token', '');
        $url   = SettingService::get('cedula_api.url', '');

        if (!$token || !$url) {
            $this->addError('cedulaModal', 'API no configurada. Ve a Ajustes → API Cédula.');
            return;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->get(rtrim($url, '/') . '/' . trim($this->cedulaModalInput));

            if ($response->successful() && !empty($response->json('identificacion'))) {
                $this->cedulaModalData = $response->json();
            } else {
                $this->cedulaModalData = [];
                $this->addError('cedulaModal', 'Cédula no encontrada o no registrada en el sistema.');
            }
        } catch (\Throwable $e) {
            Log::warning('consultarEnModal — error', ['cedula' => $this->cedulaModalInput, 'error' => $e->getMessage()]);
            $this->addError('cedulaModal', 'No se pudo conectar con el servicio. Intente nuevamente.');
        }
    }

    public function aplicarDesdeModal(): void
    {
        if (empty($this->cedulaModalData)) return;

        $d = $this->cedulaModalData;

        // Cédula del modal → campo del formulario
        $this->cedula = trim($this->cedulaModalInput);

        // Nombres: formato Ecuador → [apellido_pat apellido_mat nombre1 nombre2...]
        $palabras = preg_split('/\s+/', trim($d['nombres'] ?? ''));
        $total    = count($palabras);

        if ($total >= 4) {
            $this->last_name  = ucwords(strtolower($palabras[0] . ' ' . $palabras[1]));
            $this->first_name = ucwords(strtolower(implode(' ', array_slice($palabras, 2))));
        } elseif ($total === 3) {
            $this->last_name  = ucwords(strtolower($palabras[0] . ' ' . $palabras[1]));
            $this->first_name = ucwords(strtolower($palabras[2]));
        } elseif ($total === 2) {
            $this->last_name  = ucwords(strtolower($palabras[0]));
            $this->first_name = ucwords(strtolower($palabras[1]));
        } else {
            $this->first_name = ucwords(strtolower($d['nombres'] ?? ''));
        }

        // Género
        $generoApi    = strtoupper($d['genero'] ?? $d['sexo'] ?? '');
        $this->genero = match(true) {
            in_array($generoApi, ['HOMBRE', 'MASCULINO', 'M']) => 'Masculino',
            in_array($generoApi, ['MUJER', 'FEMENINO', 'F'])   => 'Femenino',
            default                                             => null,
        };

        // Estado civil
        $ec = strtoupper($d['estadoCivil'] ?? '');
        $this->estado_civil = match(true) {
            str_contains($ec, 'SOLTERO') || str_contains($ec, 'SOLTERA') => 'Soltero/a',
            str_contains($ec, 'CASADO')  || str_contains($ec, 'CASADA')  => 'Casado/a',
            str_contains($ec, 'DIVOR')                                    => 'Divorciado/a',
            str_contains($ec, 'VIUDO')   || str_contains($ec, 'VIUDA')   => 'Viudo/a',
            str_contains($ec, 'UNION')   || str_contains($ec, 'LIBRE')   => 'Unión libre',
            default                                                        => null,
        };

        // Fecha de nacimiento
        $this->fecha_nacimiento = !empty($d['fechaNacimiento']) ? $d['fechaNacimiento'] : null;

        // Nacionalidad
        $nacApi = ucfirst(strtolower($d['nacionalidad'] ?? ''));
        $nacMap = [
            'Ecuatoriana' => 'Ecuatoriana', 'Colombiana'    => 'Colombiana',
            'Peruana'     => 'Peruana',     'Venezolana'    => 'Venezolana',
            'Boliviana'   => 'Boliviana',   'Chilena'       => 'Chilena',
            'Argentina'   => 'Argentina',   'Cubana'        => 'Cubana',
            'Española'    => 'Española',    'Estadounidense'=> 'Estadounidense',
        ];
        $this->nacionalidad = $nacMap[$nacApi] ?? null;

        // Padres
        $this->padre = !empty($d['nombrePadre']) ? ucwords(strtolower($d['nombrePadre'])) : null;
        $this->madre = !empty($d['nombreMadre']) ? ucwords(strtolower($d['nombreMadre'])) : null;

        // Cerrar modal y limpiar
        $this->showEntryModal   = false;
        $this->cedulaModalInput = '';
        $this->cedulaModalData  = [];
        $this->dispatch('entry-modal-closed');

        // Alerta centrada (no toast)
        $this->dispatch('swal-aplicado', [
            'nombre' => ucwords(strtolower($d['nombres'] ?? '')),
        ]);
    }

    public function save()
    {
        if ($this->sinPermiso('crear_usuarios')) return;

        $this->validate();

        $profilePhotoPath = null;
        if ($this->profile_photo) {
            $profilePhotoPath = $this->profile_photo->store('users-data/profile-photos', 'public');
        }

        $certificadoPath = null;
        if ($this->certificado_discapacidad) {
            $certificadoPath = $this->certificado_discapacidad->store('users-data/certificados-discapacidad', 'public');
        }

        // Capturar antes de Hash::make() dentro de la transacción
        $plainPassword = $this->password;

        $user = null;
        $role = null;

        DB::transaction(function () use ($profilePhotoPath, $certificadoPath, &$user, &$role) {
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
                'etnia' => $this->etnia,
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

            $role = Role::findOrFail($this->role_id);
            $user->assignRole($role);
        });

        // Enviar correo de bienvenida fuera de la transacción para que un fallo de SMTP
        // no revierta la creación del usuario
        $smtpActivo = SettingService::get('smtp.activo', '0');
        Log::info('CreateUser — smtp.activo', ['valor' => $smtpActivo, 'role' => $role?->name]);

        if ($user && $role && $smtpActivo === '1') {
            $tipoAcceso = null;

            $tieneAdmin   = $role->hasPermissionTo('acceso_administrativo');
            $tieneDocente = $role->hasPermissionTo('acceso_docencia');

            Log::info('CreateUser — permisos del rol', [
                'role'                  => $role->name,
                'acceso_administrativo' => $tieneAdmin,
                'acceso_docencia'       => $tieneDocente,
            ]);

            if ($tieneAdmin) {
                $tipoAcceso = 'administrativo';
            } elseif ($tieneDocente) {
                $tipoAcceso = 'docente';
            }

            if ($tipoAcceso && $user->email) {
                EnviarBienvenidaAccesoInternoJob::dispatch($user->id, $plainPassword, $tipoAcceso, $role->name);
                Log::info('CreateUser — correo encolado', ['user_id' => $user->id, 'tipo' => $tipoAcceso]);
            } else {
                Log::warning('CreateUser — correo no enviado', [
                    'tipoAcceso' => $tipoAcceso,
                    'email'      => $user->email,
                    'role'       => $role->name,
                ]);
            }
        } else {
            Log::info('CreateUser — correo omitido', [
                'smtp_activo' => $smtpActivo,
                'user_id'     => $user?->id,
                'role'        => $role?->name,
            ]);
        }

        session()->flash('message', 'Usuario creado exitosamente.');

        return redirect()->route('administracion.administrativa.users.index');
    }

    public function render()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->orderBy('name')->get();

        return view('livewire.administration.create-user', [
            'roles' => $roles
        ]);
    }
}
