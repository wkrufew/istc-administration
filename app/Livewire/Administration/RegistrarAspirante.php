<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarConfirmacionRegistroAspirante;
use App\Models\Aspirante;
use App\Models\Cohorte;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;

class RegistrarAspirante extends Component
{
    use WithAuthorization;

    public string $firstName              = '';
    public string $lastName               = '';
    public string $email                  = '';
    public string $cedula                 = '';
    public string $telefono               = '';
    public bool   $documentoInternacional = false;
    public ?int   $cohorte_id             = null;
    public string $tipo_proceso           = 'regular';

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
            'firstName'  => 'required|string|max:100',
            'lastName'   => 'required|string|max:100',
            'email'      => 'required|email|max:255|unique:users,email',
            'cedula'     => $cedulaRules,
            'telefono'     => 'nullable|string|max:20',
            'cohorte_id'   => 'required|exists:cohortes,id',
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
    ];

    public function mount(): void
    {
        $this->requierePermiso('gestionar_aspirantes');
    }

    #[Computed]
    public function cohortes()
    {
        return Cohorte::with('carrera')
            ->where('estado', 'abierto')
            ->orderByDesc('created_at')
            ->get();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.registrar-aspirante', [
            'cohortes' => $this->cohortes,
        ]);
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $data = $this->validate();

        try {
            DB::transaction(function () use ($data) {
                $firstName = mb_strtoupper(trim($data['firstName']), 'UTF-8');
                $lastName  = mb_strtoupper(trim($data['lastName']), 'UTF-8');
                $email     = strtolower(trim($data['email']));

                $user = \App\Models\User::create([
                    'name'       => $firstName . ' ' . $lastName,
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                    'cedula'     => $data['cedula'],
                    'phone'      => $data['telefono'] ?: null,
                    'password'   => Str::random(16),
                    'is_active'  => true,
                ]);

                $user->assignRole('Admision');

                $permiso = Permission::firstOrCreate([
                    'name'       => 'acceso_admision',
                    'guard_name' => 'web',
                ]);
                $user->givePermissionTo($permiso);

                $aspirante = Aspirante::create([
                    'user_id'       => $user->id,
                    'cohorte_id'    => $data['cohorte_id'],
                    'tipo_proceso'  => $data['tipo_proceso'],
                    'estado'        => 'pendiente',
                    'registrado_por'=> auth()->id(),
                ]);

                EnviarConfirmacionRegistroAspirante::dispatch($aspirante->id);
            });

            $this->reset(['firstName', 'lastName', 'email', 'cedula', 'telefono', 'cohorte_id', 'documentoInternacional', 'tipo_proceso']);
            $this->resetValidation();
            unset($this->cohortes);

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

    private function validarCedulaEcuador(string $cedula): bool
    {
        if (! ctype_digit($cedula) || strlen($cedula) !== 10) {
            return false;
        }

        $provincia = (int) substr($cedula, 0, 2);
        if ($provincia < 1 || $provincia > 24) {
            return false;
        }

        // Solo personas naturales (tercer dígito 0–5)
        if ((int) $cedula[2] > 5) {
            return false;
        }

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
