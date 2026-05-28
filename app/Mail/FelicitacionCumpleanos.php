<?php

namespace App\Mail;

use App\Models\Matricula;
use App\Models\User;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FelicitacionCumpleanos extends Mailable
{
    use Queueable, SerializesModels;

    public string  $tipo;       // administrativo | docente | estudiante
    public int     $edad;
    public ?string $carrera     = null;
    public ?string $periodo     = null;

    public function __construct(public readonly User $user)
    {
        $this->edad = Carbon::parse($user->fecha_nacimiento)->age;

        if ($user->hasPermissionTo('acceso_estudiantil')) {
            $this->tipo = 'estudiante';
            $matricula  = $user->matriculas->first();
            if ($matricula) {
                $this->carrera = $matricula->carrera?->nombre;
                $this->periodo = $matricula->periodo?->nombre;
            }
        } elseif ($user->hasPermissionTo('acceso_docencia')) {
            $this->tipo = 'docente';
        } else {
            $this->tipo = 'administrativo';
        }
    }

    public function envelope(): Envelope
    {
        $nombreCorto = SettingService::get('instituto.nombre_corto', 'ISTC');
        $primerNombre = explode(' ', trim($this->user->first_name ?? $this->user->name))[0];

        return new Envelope(
            subject: "🎂 ¡Feliz cumpleaños, {$primerNombre}! | {$nombreCorto}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: "emails.cumpleanos.{$this->tipo}",
            with: [
                'usuario'  => $this->user,
                'edad'     => $this->edad,
                'carrera'  => $this->carrera,
                'periodo'  => $this->periodo,
                'instituto' => [
                    'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto Superior Tecnológico'),
                    'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
                    'email'        => SettingService::get('instituto.email', ''),
                    'telefono'     => SettingService::get('instituto.telefono', ''),
                    'web'          => SettingService::get('instituto.web', ''),
                    'direccion'    => SettingService::get('instituto.direccion', ''),
                    'logo_url'     => $logoPath ? url('storage/' . $logoPath) : null,
                    'url_portal'   => url('/login'),
                ],
            ],
        );
    }
}
