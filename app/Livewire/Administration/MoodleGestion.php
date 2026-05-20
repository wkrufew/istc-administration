<?php

namespace App\Livewire\Administration;

use App\Jobs\ActualizarDatosMoodleJob;
use App\Jobs\CrearUsuarioMoodleJob;
use App\Jobs\RestablecerCredencialesMoodleJob;
use App\Jobs\SincronizarMoodleIdJob;
use App\Jobs\SuspenderAccesoMoodleJob;
use App\Mail\BienvenidaMoodle;
use App\Models\User;
use App\Services\MoodleService;
use App\Services\SettingService;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MoodleGestion extends Component
{
    public User $usuario;

    // Estado de la UI
    public ?string $mensaje     = null;
    public string  $tipoMensaje = 'info'; // info | success | error | warning
    public bool    $cargando    = false;

    public function mount(User $usuario): void
    {
        $this->usuario = $usuario;
    }

    // =========================================================================
    // OBTENER / SINCRONIZAR moodle_id (solo busca, no crea)
    // =========================================================================
    public function sincronizarId(): void
    {
        $this->resetMensaje();

        if (! MoodleService::activo()) {
            $this->setMensaje('La integración con Moodle no está activa.', 'warning');
            return;
        }

        $this->cargando = true;
        SincronizarMoodleIdJob::dispatch($this->usuario->id);

        $this->setMensaje(
            'Búsqueda enviada. El ID de Moodle se actualizará en breve si el usuario existe en el campus virtual.',
            'info'
        );
        $this->cargando = false;
    }

    // =========================================================================
    // REGISTRAR EN MOODLE (crear si no existe + enviar email de bienvenida)
    // =========================================================================
    public function registrarEnMoodle(): void
    {
        $this->resetMensaje();

        if (! MoodleService::activo()) {
            $this->setMensaje('La integración con Moodle no está activa.', 'warning');
            return;
        }

        if ($this->usuario->moodle_id) {
            $this->setMensaje('Este usuario ya está registrado en Moodle (ID: ' . $this->usuario->moodle_id . ').', 'warning');
            return;
        }

        if (! $this->usuario->cedula) {
            $this->setMensaje('El usuario no tiene cédula registrada. Es necesaria para crear el acceso en Moodle.', 'error');
            return;
        }

        // Validar que tenga un rol con acceso al campus virtual
        $tieneAcceso = $this->usuario->hasAnyPermission([
            'acceso_administrativo',
            'acceso_docencia',
            'acceso_estudiantil',
        ]);

        if (! $tieneAcceso) {
            $this->setMensaje(
                'El usuario no tiene un rol con acceso al campus virtual. Asigne el rol correspondiente antes de registrarlo en Moodle.',
                'error'
            );
            return;
        }

        // Si es exclusivamente estudiantil, verificar matrícula activa
        $soloEstudiantil = $this->usuario->hasPermissionTo('acceso_estudiantil')
            && ! $this->usuario->hasAnyPermission(['acceso_administrativo', 'acceso_docencia']);

        if ($soloEstudiantil) {
            $tieneMatricula = $this->usuario->matriculas()
                ->where('estado', 'Habilitada')
                ->exists();

            if (! $tieneMatricula) {
                $this->setMensaje(
                    'El estudiante no tiene matrícula habilitada en ningún período activo. Habilite su matrícula antes de registrarlo en Moodle.',
                    'error'
                );
                return;
            }
        }

        // Crear en Moodle via Job
        CrearUsuarioMoodleJob::dispatch($this->usuario->id);

        // Enviar email de bienvenida Moodle si SMTP activo
        if (SettingService::get('smtp.activo', '0') === '1') {
            $mailer = SettingService::buildMailer();
            $mailer->to($this->usuario->email)->send(new BienvenidaMoodle($this->usuario));
        }

        $this->setMensaje(
            'Registro en Moodle encolado. El usuario recibirá sus credenciales por correo una vez procesado.',
            'success'
        );
    }

    // =========================================================================
    // ACTUALIZAR DATOS (nombre, email) EN MOODLE
    // =========================================================================
    public function actualizarDatos(): void
    {
        $this->resetMensaje();

        if (! MoodleService::activo()) {
            $this->setMensaje('La integración con Moodle no está activa.', 'warning');
            return;
        }

        if (! $this->usuario->moodle_id) {
            $this->setMensaje('El usuario no tiene Moodle ID. Regístrelo primero en Moodle.', 'error');
            return;
        }

        ActualizarDatosMoodleJob::dispatch($this->usuario->id);
        $this->setMensaje('Actualización de datos en Moodle encolada.', 'success');
    }

    // =========================================================================
    // RESTABLECER CREDENCIALES (password = cédula + email reenvío)
    // =========================================================================
    public function restablecerCredenciales(): void
    {
        $this->resetMensaje();

        if (! MoodleService::activo()) {
            $this->setMensaje('La integración con Moodle no está activa.', 'warning');
            return;
        }

        if (! $this->usuario->moodle_id) {
            $this->setMensaje('El usuario no tiene Moodle ID. Regístrelo primero en Moodle.', 'error');
            return;
        }

        if (! $this->usuario->cedula) {
            $this->setMensaje('El usuario no tiene cédula registrada. Se necesita para restablecer la contraseña.', 'error');
            return;
        }

        RestablecerCredencialesMoodleJob::dispatch($this->usuario->id);
        $this->setMensaje(
            'Restablecimiento de contraseña encolado. El usuario recibirá un correo con sus nuevas credenciales.',
            'success'
        );
    }

    // =========================================================================
    // SUSPENDER / REACTIVAR ACCESO EN MOODLE
    // =========================================================================
    public function suspenderAcceso(bool $suspender): void
    {
        $this->resetMensaje();

        if (! MoodleService::activo()) {
            $this->setMensaje('La integración con Moodle no está activa.', 'warning');
            return;
        }

        if (! $this->usuario->moodle_id) {
            $this->setMensaje('El usuario no tiene Moodle ID. Regístrelo primero en Moodle.', 'error');
            return;
        }

        SuspenderAccesoMoodleJob::dispatch($this->usuario->id, $suspender);

        $texto = $suspender
            ? 'Suspensión de acceso encolada. El usuario no podrá ingresar al campus virtual una vez procesado.'
            : 'Reactivación de acceso encolada. El usuario podrá ingresar al campus virtual una vez procesado.';

        $this->setMensaje($texto, 'success');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    private function resetMensaje(): void
    {
        $this->mensaje     = null;
        $this->tipoMensaje = 'info';
    }

    private function setMensaje(string $texto, string $tipo = 'info'): void
    {
        $this->mensaje     = $texto;
        $this->tipoMensaje = $tipo;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $this->usuario->refresh();
        $moodleActivo = MoodleService::activo();
        $moodleUrl    = SettingService::get('moodle.url', '');

        return view('livewire.administration.moodle-gestion', compact('moodleActivo', 'moodleUrl'));
    }
}
