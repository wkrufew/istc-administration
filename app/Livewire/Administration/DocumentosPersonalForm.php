<?php

namespace App\Livewire\Administration;

use App\Models\AsignacionDocente;
use App\Models\ExpedienteDocumento;
use App\Models\User;
use App\Services\ExpedienteService;
use App\Traits\WithAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Documentos del docente (sección Docentes): hoja de vida, SENESCYT, cédula y un contrato por
 * asignación. Todo se guarda en su expediente (ExpedienteService); reemplazar o quitar un
 * documento lo deja en el historial, nunca borra el archivo.
 */
class DocumentosPersonalForm extends Component
{
    use WithFileUploads, WithAuthorization;

    public User $user;
    public bool $esEdicion = false;

    // Contratos por asignación
    public ?int $asignacionContratoId = null;
    public $contratoTemporal          = null;

    // Eliminar un contrato definitivamente (solo Administrador)
    public ?int $contratoEliminarId     = null;
    public string $motivoEliminarContrato = '';

    /** Documentos de la cabecera (Expediente x / 3): columna que usa la vista → tipo del expediente. */
    private const SLOTS = [
        ['column' => 'file_curriculum', 'tipo' => 'hoja_vida'],
        ['column' => 'file_senescyt',   'tipo' => 'senescyt'],
        ['column' => 'file_cedula',     'tipo' => 'cedula'],
    ];

    public function mount(User $user): void
    {
        $this->requierePermiso('gestionar_docentes');

        $this->user      = $user;
        $this->esEdicion = $this->documentosPersonales()->isNotEmpty();
    }

    /** Versión vigente de cada documento personal, por tipo. */
    private function documentosPersonales()
    {
        return ExpedienteDocumento::vigentes()
            ->where('user_id', $this->user->id)
            ->whereIn('tipo', array_column(self::SLOTS, 'tipo'))
            ->whereNull('asignacion_docente_id')
            ->latest('id')
            ->get()
            ->unique('tipo')
            ->keyBy('tipo');
    }

    /** Los documentos personales se manejan en el panel del expediente; al cambiar, se refresca la cabecera. */
    #[On('expediente-actualizado')]
    public function expedienteActualizado(): void
    {
        $this->esEdicion = $this->documentosPersonales()->isNotEmpty();
    }

    public function seleccionarAsignacion(int $id): void
    {
        $this->asignacionContratoId = $id;
        $this->contratoTemporal     = null;
        $this->resetErrorBag('contratoTemporal');
    }

    public function cancelarContratoUpload(): void
    {
        $this->asignacionContratoId = null;
        $this->contratoTemporal     = null;
    }

    public function subirContrato(): void
    {
        if ($this->sinPermiso('gestionar_docentes')) return;

        $this->validate([
            'contratoTemporal' => 'required|file|mimes:pdf|max:10240',
        ], [
            'contratoTemporal.required' => 'Selecciona un archivo PDF.',
            'contratoTemporal.mimes'    => 'Solo se permiten archivos PDF.',
            'contratoTemporal.max'      => 'El archivo no puede superar 10 MB.',
        ]);

        $asignacion = AsignacionDocente::where('id', $this->asignacionContratoId)
            ->where('docente_id', $this->user->id)
            ->first();

        if (! $asignacion) {
            $this->addError('contratoTemporal', 'Asignación no encontrada.');
            return;
        }

        // Subido por secretaría: contrato firmado y aprobado
        ExpedienteService::subir($this->user, 'contrato', $this->contratoTemporal, 'secretaria', $asignacion->id, 'firmado');

        $this->asignacionContratoId = null;
        $this->contratoTemporal     = null;

        $this->dispatch('toast', message: 'Contrato guardado correctamente.', type: 'success');
    }

    /** Quita el contrato de la asignación: queda en el historial del expediente (el archivo no se borra). */
    public function eliminarContrato(int $asignacionId): void
    {
        if ($this->sinPermiso('gestionar_docentes')) return;

        $asignacion = AsignacionDocente::where('id', $asignacionId)->where('docente_id', $this->user->id)->first();
        $contrato   = $asignacion ? ExpedienteService::vigente($this->user->id, 'contrato', $asignacion->id) : null;

        if ($contrato) {
            ExpedienteService::retirar($contrato);
            $this->dispatch('toast', message: 'Contrato quitado (queda en el historial del expediente).', type: 'success');
        }
    }

    public function abrirEliminarContrato(int $asignacionId): void
    {
        if (! ExpedienteService::puedeEliminarDefinitivamente()) return;

        $asignacion = AsignacionDocente::where('id', $asignacionId)->where('docente_id', $this->user->id)->first();
        $this->contratoEliminarId     = $asignacion ? ExpedienteService::vigente($this->user->id, 'contrato', $asignacion->id)?->id : null;
        $this->motivoEliminarContrato = '';
        $this->resetErrorBag('motivoEliminarContrato');
    }

    public function cerrarEliminarContrato(): void
    {
        $this->reset(['contratoEliminarId', 'motivoEliminarContrato']);
        $this->resetErrorBag('motivoEliminarContrato');
    }

    /** Borra el contrato para siempre (subido por error); la versión anterior, si hay, vuelve a quedar vigente. */
    public function confirmarEliminarContrato(): void
    {
        $contrato = ExpedienteDocumento::where('user_id', $this->user->id)->where('tipo', 'contrato')->find($this->contratoEliminarId);
        if (! $contrato) {
            $this->cerrarEliminarContrato();
            return;
        }

        try {
            ExpedienteService::eliminarDefinitivamente($contrato, $this->motivoEliminarContrato);
        } catch (\DomainException $e) {
            $this->addError('motivoEliminarContrato', $e->getMessage());
            return;
        }

        $this->cerrarEliminarContrato();
        $this->dispatch('toast', message: 'Contrato eliminado definitivamente.', type: 'success');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $asignaciones = AsignacionDocente::where('docente_id', $this->user->id)
            ->with(['materia', 'paralelo', 'periodo'])
            ->orderByDesc('periodo_id')
            ->get();

        $personales = $this->documentosPersonales();
        $urls = collect(self::SLOTS)->mapWithKeys(fn($meta) => [$meta['column'] => $personales->get($meta['tipo'])?->url()])->all();

        // Contrato vigente de cada asignación (del propio docente)
        $contratos = ExpedienteDocumento::vigentes()
            ->where('user_id', $this->user->id)->where('tipo', 'contrato')
            ->whereIn('asignacion_docente_id', $asignaciones->pluck('id'))
            ->latest('id')->get()
            ->unique('asignacion_docente_id')
            ->mapWithKeys(fn($d) => [$d->asignacion_docente_id => $d->url()])
            ->all();

        $puedeEliminar = ExpedienteService::puedeEliminarDefinitivamente();

        return view('livewire.administration.documentos-personal-form', compact('asignaciones', 'urls', 'contratos', 'puedeEliminar'));
    }
}
