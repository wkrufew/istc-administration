<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarConfirmacionRegistroAspirante;
use App\Models\Aspirante;
use App\Models\Carrera;
use App\Models\Cohorte;
use App\Services\SettingService;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class RegistrarAspirante extends Component
{
    use WithAuthorization;

    // ── Datos del formulario ──────────────────────────────────────────────
    public string $firstName              = '';
    public string $lastName               = '';
    public string $email                  = '';
    public string $cedula                 = '';
    public string $telefono               = '';
    public bool   $documentoInternacional = false;
    public ?int   $cohorte_id             = null;
    public ?int   $carrera_id             = null;
    public string $tipo_proceso           = 'regular';

    // Datos auto-completados desde la API
    public string $genero        = '';
    public string $estadoCivil   = '';
    public string $fechaNac      = '';
    public string $nacionalidad  = '';
    public string $padre         = '';
    public string $madre         = '';

    // ── Modal cédula ──────────────────────────────────────────────────────
    public bool   $apiActiva        = false;
    public bool   $showEntryModal   = false;
    public string $cedulaModalInput = '';
    public array  $cedulaModalData  = [];

    public function mount(): void
    {
        $this->requierePermiso('gestionar_aspirantes');
        $this->apiActiva      = ! empty(SettingService::get('cedula_api.token', ''));
        $this->showEntryModal = true; // Abre el modal automáticamente al entrar
    }

    protected function rules(): array
    {
        $cedulaRules = ['required', 'string', 'max:20', 'unique:users,cedula'];

        if (! $this->documentoInternacional) {
            $cedulaRules[] = 'digits:10';
            $cedulaRules[] = function (string $attribute, mixed $value, \Closure $fail) {
                if (! $this->validarCedulaEcuador($value)) {
                    $fail('La cédula ingresada no es válida.');
                }
            };
        }

        return [
            'firstName'    => 'required|string|max:100',
            'lastName'     => 'required|string|max:100',
            'email'        => 'required|email|max:255|unique:users,email',
            'cedula'       => $cedulaRules,
            'telefono'     => 'nullable|string|max:20',
            'cohorte_id'   => 'required|exists:cohortes,id',
            'carrera_id'   => 'required|exists:carreras,id',
            'tipo_proceso' => 'required|in:regular,validacion_conocimientos',
        ];
    }

    protected $messages = [
        'firstName.required'  => 'El nombre es obligatorio.',
        'lastName.required'   => 'El apellido es obligatorio.',
        'email.required'      => 'El correo es obligatorio.',
        'email.unique'        => 'Ya existe un usuario con ese correo.',
        'cedula.required'     => 'La cédula es obligatoria.',
        'cedula.digits'       => 'La cédula debe tener exactamente 10 dígitos.',
        'cedula.unique'       => 'Ya existe un usuario con esa cédula.',
        'cohorte_id.required' => 'Selecciona una cohorte.',
        'carrera_id.required' => 'Selecciona una carrera.',
    ];

    // ── Modal cédula ──────────────────────────────────────────────────────

    public function abrirEntryModal(): void
    {
        $this->showEntryModal   = true;
        $this->cedulaModalInput = '';
        $this->cedulaModalData  = [];
        $this->resetErrorBag('cedulaModal');
    }

    public function cerrarEntryModal(): void
    {
        $this->showEntryModal   = false;
        $this->cedulaModalInput = '';
        $this->cedulaModalData  = [];
        $this->resetErrorBag('cedulaModal');
    }

    public function consultarEnModal(): void
    {
        $this->resetErrorBag('cedulaModal');

        if (! trim($this->cedulaModalInput)) {
            $this->addError('cedulaModal', 'Ingrese el número de cédula.');
            return;
        }

        $token = SettingService::get('cedula_api.token', '');
        $url   = SettingService::get('cedula_api.url', '');

        if (! $token || ! $url) {
            $this->addError('cedulaModal', 'API no configurada. Ve a Ajustes → API Cédula.');
            return;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->get(rtrim($url, '/') . '/' . trim($this->cedulaModalInput));

            if ($response->successful() && ! empty($response->json('identificacion'))) {
                $this->cedulaModalData = $response->json();
            } else {
                $this->cedulaModalData = [];
                $this->addError('cedulaModal', 'Cédula no encontrada o no registrada en el sistema.');
            }
        } catch (\Throwable $e) {
            Log::warning('RegistrarAspirante.consultarEnModal', [
                'cedula' => $this->cedulaModalInput,
                'error'  => $e->getMessage(),
            ]);
            $this->addError('cedulaModal', 'No se pudo conectar con el servicio. Intente nuevamente.');
        }
    }

    public function aplicarDesdeModal(): void
    {
        if (empty($this->cedulaModalData)) return;

        $d = $this->cedulaModalData;

        $this->cedula = trim($this->cedulaModalInput);

        // Nombres en formato Ecuador: APELLIDO_PAT APELLIDO_MAT NOMBRE1 [NOMBRE2...]
        $palabras = preg_split('/\s+/', trim($d['nombres'] ?? ''));
        $total    = count($palabras);
        if ($total >= 4) {
            $this->lastName  = ucwords(strtolower($palabras[0] . ' ' . $palabras[1]));
            $this->firstName = ucwords(strtolower(implode(' ', array_slice($palabras, 2))));
        } elseif ($total === 3) {
            $this->lastName  = ucwords(strtolower($palabras[0] . ' ' . $palabras[1]));
            $this->firstName = ucwords(strtolower($palabras[2]));
        } elseif ($total === 2) {
            $this->lastName  = ucwords(strtolower($palabras[0]));
            $this->firstName = ucwords(strtolower($palabras[1]));
        } else {
            $this->firstName = ucwords(strtolower($d['nombres'] ?? ''));
        }

        $generoApi       = strtoupper($d['genero'] ?? $d['sexo'] ?? '');
        $this->genero    = match(true) {
            in_array($generoApi, ['HOMBRE', 'MASCULINO', 'M']) => 'Masculino',
            in_array($generoApi, ['MUJER', 'FEMENINO', 'F'])   => 'Femenino',
            default                                             => '',
        };

        $ec = strtoupper($d['estadoCivil'] ?? '');
        $this->estadoCivil = match(true) {
            str_contains($ec, 'SOLTERO') || str_contains($ec, 'SOLTERA') => 'Soltero/a',
            str_contains($ec, 'CASADO')  || str_contains($ec, 'CASADA')  => 'Casado/a',
            str_contains($ec, 'DIVOR')                                    => 'Divorciado/a',
            str_contains($ec, 'VIUDO')   || str_contains($ec, 'VIUDA')   => 'Viudo/a',
            str_contains($ec, 'UNION')   || str_contains($ec, 'LIBRE')   => 'Unión libre',
            default                                                        => '',
        };

        $this->fechaNac    = ! empty($d['fechaNacimiento']) ? $d['fechaNacimiento'] : '';
        $this->nacionalidad = ucfirst(strtolower($d['nacionalidad'] ?? ''));
        $this->padre        = ! empty($d['nombrePadre']) ? ucwords(strtolower($d['nombrePadre'])) : '';
        $this->madre        = ! empty($d['nombreMadre']) ? ucwords(strtolower($d['nombreMadre'])) : '';

        $this->showEntryModal   = false;
        $this->cedulaModalInput = '';
        $this->cedulaModalData  = [];
    }

    // ── Carreras y cohortes ───────────────────────────────────────────────

    #[Computed]
    public function cohortes()
    {
        return Cohorte::where('estado', 'abierto')
            ->orderByDesc('created_at')
            ->get(['id', 'nombre']);
    }

    #[Computed]
    public function carreras()
    {
        return Carrera::orderBy('name')->get(['id', 'name']);
    }

    // ── Guardar ───────────────────────────────────────────────────────────

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $data = $this->validate();

        try {
            DB::transaction(function () use ($data) {
                $firstName = ucwords(strtolower(trim($data['firstName'])));
                $lastName  = ucwords(strtolower(trim($data['lastName'])));
                $email     = strtolower(trim($data['email']));

                $user = \App\Models\User::create([
                    'name'             => $firstName . ' ' . $lastName,
                    'first_name'       => $firstName,
                    'last_name'        => $lastName,
                    'email'            => $email,
                    'cedula'           => $data['cedula'],
                    'phone'            => $data['telefono'] ?: null,
                    'password'         => Hash::make(Str::random(16)),
                    'genero'           => $this->genero ?: null,
                    'estado_civil'     => $this->estadoCivil ?: null,
                    'fecha_nacimiento' => $this->fechaNac ?: null,
                    'nacionalidad'     => $this->nacionalidad ?: null,
                    'padre'            => $this->padre ?: null,
                    'madre'            => $this->madre ?: null,
                    'is_active'        => true,
                ]);

                $user->assignRole('Admision');

                $permiso = Permission::firstOrCreate([
                    'name'       => 'acceso_admision',
                    'guard_name' => 'web',
                ]);
                $user->givePermissionTo($permiso);

                $aspirante = Aspirante::create([
                    'user_id'        => $user->id,
                    'cohorte_id'     => $data['cohorte_id'],
                    'carrera_id'     => $data['carrera_id'],
                    'tipo_proceso'   => $data['tipo_proceso'],
                    'estado'         => 'pendiente',
                    'registrado_por' => auth()->id(),
                ]);

                EnviarConfirmacionRegistroAspirante::dispatch($aspirante->id);
            });

            $this->reset([
                'firstName', 'lastName', 'email', 'cedula', 'telefono',
                'cohorte_id', 'carrera_id', 'documentoInternacional', 'tipo_proceso',
                'genero', 'estadoCivil', 'fechaNac', 'nacionalidad', 'padre', 'madre',
            ]);
            $this->resetValidation();
            unset($this->cohortes, $this->carreras);

            // Reabrir modal para el siguiente registro
            $this->showEntryModal = true;

            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => 'Aspirante registrado',
                'text'  => 'Se creó la cuenta y se envió un correo de confirmación al aspirante.',
                'timer' => 4000,
            ]);

        } catch (\Throwable $e) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Error al registrar',
                'text'  => app()->isLocal() ? $e->getMessage() : 'Contacte al administrador del sistema.',
            ]);
        }
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.registrar-aspirante', [
            'cohortes' => $this->cohortes,
            'carreras' => $this->carreras,
        ]);
    }

    // ── Validación cédula Ecuador ─────────────────────────────────────────

    private function validarCedulaEcuador(string $cedula): bool
    {
        if (! ctype_digit($cedula) || strlen($cedula) !== 10) return false;

        $provincia = (int) substr($cedula, 0, 2);
        if ($provincia < 1 || $provincia > 24) return false;
        if ((int) $cedula[2] > 5) return false;

        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;
        for ($i = 0; $i < 9; $i++) {
            $valor = (int) $cedula[$i] * $coeficientes[$i];
            if ($valor >= 10) $valor -= 9;
            $suma += $valor;
        }

        $residuo  = $suma % 10;
        $esperado = $residuo === 0 ? 0 : 10 - $residuo;

        return $esperado === (int) $cedula[9];
    }
}
