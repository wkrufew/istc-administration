<?php

namespace App\Livewire\Administration;

use App\Mail\BienvenidaMoodle;
use App\Mail\ReenvioCredencialesAcceso;
use App\Models\User;
use App\Services\MoodleService;
use App\Services\SettingService;
use App\Traits\WithAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

class MoodleGestion extends Component
{
    use WithAuthorization;

    public User $usuario;

    public function mount(User $usuario): void
    {
        $this->requierePermiso('moodle_gestion');
        $this->usuario = $usuario;
    }

    // =========================================================================
    // SINCRONIZAR ID — busca en Moodle y guarda el moodle_id si lo encuentra
    // =========================================================================
    public function sincronizarId(): void
    {
        if (! MoodleService::activo()) {
            $this->toastError('Integración desactivada', 'Active la integración Moodle en Configuración.');
            return;
        }

        try {
            $moodle     = new MoodleService();
            $encontrado = $moodle->buscarUsuario($this->usuario);

            if ($encontrado) {
                $this->usuario->moodle_id = $encontrado['id'];
                $this->usuario->saveQuietly();
                $this->toastSuccess('ID Sincronizado', "Moodle ID #{$encontrado['id']} guardado correctamente.");
            } else {
                $this->toastWarning('No encontrado', 'El usuario no existe en Moodle. Use "Registrar en Moodle" para crearlo.');
            }
        } catch (Throwable $e) {
            $this->toastError('Error al sincronizar', $e->getMessage());
        }
    }

    // =========================================================================
    // REGISTRAR EN MOODLE — crea cuenta o vincula si ya existe
    // =========================================================================
    public function registrarEnMoodle(): void
    {
        if (! MoodleService::activo()) {
            $this->toastError('Integración desactivada', 'Active la integración Moodle en Configuración.');
            return;
        }

        if ($this->usuario->moodle_id) {
            $this->toastWarning('Ya registrado', 'Este usuario ya tiene Moodle ID #' . $this->usuario->moodle_id . '.');
            return;
        }

        if (! $this->usuario->cedula) {
            $this->toastError('Sin cédula', 'El usuario no tiene cédula registrada. Es necesaria para crear el acceso en Moodle.');
            return;
        }

        $tieneAcceso = $this->usuario->hasAnyPermission([
            'acceso_administrativo',
            'acceso_docencia',
            'acceso_estudiantil',
        ]);

        if (! $tieneAcceso) {
            $this->toastError('Sin rol de acceso', 'Asigne un rol con acceso al campus virtual antes de registrar en Moodle.');
            return;
        }

        $soloEstudiantil = $this->usuario->hasPermissionTo('acceso_estudiantil')
            && ! $this->usuario->hasAnyPermission(['acceso_administrativo', 'acceso_docencia']);

        if ($soloEstudiantil && ! $this->usuario->matriculas()->where('estado', 'Habilitada')->exists()) {
            $this->toastError('Sin matrícula activa', 'El estudiante no tiene matrícula habilitada. Habilite su matrícula antes de registrarlo en Moodle.');
            return;
        }

        try {
            $moodle     = new MoodleService();
            $encontrado = $moodle->buscarUsuario($this->usuario);

            if ($encontrado) {
                $moodleId = (int) $encontrado['id'];
                $this->usuario->moodle_id = $moodleId;
                $this->usuario->saveQuietly();
                $this->toastSuccess('Usuario vinculado', "El usuario ya existía en Moodle. ID #{$moodleId} guardado.");
            } else {
                $moodleId = $moodle->crearUsuario($this->usuario);
                $this->usuario->moodle_id = $moodleId;
                $this->usuario->saveQuietly();

                if (SettingService::get('smtp.activo', '0') === '1') {
                    $mailer = SettingService::buildMailer();
                    $mailer->to($this->usuario->email)->send(new BienvenidaMoodle($this->usuario));
                }

                $this->toastSuccess('Registrado en Moodle', "Cuenta creada. Moodle ID #{$moodleId}. Credenciales enviadas por correo.");
            }
        } catch (Throwable $e) {
            $this->toastError('Error al registrar', $e->getMessage());
        }
    }

    // =========================================================================
    // ACTUALIZAR DATOS — sincroniza nombre, correo, teléfono y dirección
    // =========================================================================
    public function actualizarDatos(): void
    {
        if (! MoodleService::activo()) {
            $this->toastError('Integración desactivada', 'Active la integración Moodle en Configuración.');
            return;
        }

        if (! $this->usuario->moodle_id) {
            $this->toastError('Sin Moodle ID', 'El usuario no tiene Moodle ID. Regístrelo primero.');
            return;
        }

        try {
            (new MoodleService())->actualizarUsuario($this->usuario);
            $this->toastSuccess('Datos actualizados', 'Nombre, correo, teléfono y dirección sincronizados en Moodle.');
        } catch (Throwable $e) {
            $this->toastError('Error al actualizar', $e->getMessage());
        }
    }

    // =========================================================================
    // RESTABLECER CREDENCIALES — password = cédula + email de notificación
    // =========================================================================
    public function restablecerCredenciales(): void
    {
        if (! MoodleService::activo()) {
            $this->toastError('Integración desactivada', 'Active la integración Moodle en Configuración.');
            return;
        }

        if (! $this->usuario->moodle_id) {
            $this->toastError('Sin Moodle ID', 'El usuario no tiene Moodle ID. Regístrelo primero.');
            return;
        }

        if (! $this->usuario->cedula) {
            $this->toastError('Sin cédula', 'El usuario no tiene cédula registrada. Se necesita para restablecer la contraseña.');
            return;
        }

        try {
            (new MoodleService())->restablecerPassword($this->usuario);

            if (SettingService::get('smtp.activo', '0') === '1') {
                $mailer = SettingService::buildMailer();
                $mailer->to($this->usuario->email)->send(
                    new ReenvioCredencialesAcceso(
                        usuario: $this->usuario,
                        plainPassword: $this->usuario->cedula,
                        tipoAcceso: 'moodle',
                        nombreRol: 'Campus Virtual',
                    )
                );
            }

            $this->toastSuccess('Contraseña restablecida', 'La contraseña Moodle fue restablecida a la cédula del usuario.');
        } catch (Throwable $e) {
            $this->toastError('Error al restablecer', $e->getMessage());
        }
    }

    // =========================================================================
    // SUSPENDER / REACTIVAR ACCESO
    // =========================================================================
    public function suspenderAcceso(bool $suspender): void
    {
        if (! MoodleService::activo()) {
            $this->toastError('Integración desactivada', 'Active la integración Moodle en Configuración.');
            return;
        }

        if (! $this->usuario->moodle_id) {
            $this->toastError('Sin Moodle ID', 'El usuario no tiene Moodle ID. Regístrelo primero.');
            return;
        }

        try {
            (new MoodleService())->suspenderAcceso($this->usuario, $suspender);

            $this->usuario->moodle_suspended = $suspender;
            $this->usuario->saveQuietly();

            $titulo  = $suspender ? 'Acceso suspendido'  : 'Acceso reactivado';
            $mensaje = $suspender
                ? 'El usuario ya no puede ingresar al campus virtual.'
                : 'El usuario puede ingresar nuevamente al campus virtual.';

            $this->toastSuccess($titulo, $mensaje);
        } catch (Throwable $e) {
            $this->toastError('Error', $e->getMessage());
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    private function toastSuccess(string $titulo, string $mensaje): void
    {
        $this->dispatch('moodle-toast', tipo: 'success', titulo: $titulo, mensaje: $mensaje);
    }

    private function toastError(string $titulo, string $mensaje): void
    {
        $this->dispatch('moodle-toast', tipo: 'error', titulo: $titulo, mensaje: $mensaje);
    }

    private function toastWarning(string $titulo, string $mensaje): void
    {
        $this->dispatch('moodle-toast', tipo: 'warning', titulo: $titulo, mensaje: $mensaje);
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
