<?php

namespace App\Livewire\Expediente;

use App\Models\ExpedienteDocumento;
use App\Models\User;
use App\Services\ExpedienteService;
use App\Traits\WithAuthorization;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Documentos del expediente de una persona, para secretaría: ver, subir o reemplazar (queda
 * aprobado), aprobar, rechazar o solicitar con observación, historial de versiones y, solo
 * Administrador, eliminar definitivamente un archivo subido por error.
 * Se usa en la ficha del estudiante y en los documentos del docente (los contratos van aparte).
 */
class PanelDocumentos extends Component
{
    use WithFileUploads, WithAuthorization;

    #[Locked] public int $userId;
    #[Locked] public string $perfil = 'estudiante';   // estudiante | docente

    /** Archivo elegido para cada tipo (subir / reemplazar). */
    public array $archivos = [];
    public string $descripcionOtro = '';

    /** Tipo con el historial desplegado. */
    public ?string $historialTipo = null;

    // Modal de rechazar / solicitar / eliminar definitivamente
    public ?string $accion = null;
    public ?int $accionDocId = null;
    public ?string $accionTipo = null;
    public string $accionTexto = '';

    public function mount(User $user, string $perfil = 'estudiante'): void
    {
        $this->userId = $user->id;
        $this->perfil = $perfil === 'docente' ? 'docente' : 'estudiante';
        $this->requierePermiso($this->permiso());
    }

    private function permiso(): string
    {
        return $this->perfil === 'docente' ? 'gestionar_docentes' : 'gestionar_estudiantes';
    }

    private function persona(): User
    {
        return User::findOrFail($this->userId);
    }

    /** Documento de esta persona (nunca de otra, aunque se envíe otro id). */
    private function documento(int $id): ExpedienteDocumento
    {
        return ExpedienteDocumento::where('user_id', $this->userId)->findOrFail($id);
    }

    /** Documentos que corresponden a esta persona (ver ExpedienteService::tiposVisibles). */
    public function tipos(): array
    {
        return ExpedienteService::tiposVisibles($this->persona(), $this->perfil);
    }

    // ── Acciones ──────────────────────────────────────────────────────────────

    public function subir(string $tipo): void
    {
        if ($this->sinPermiso($this->permiso())) return;
        if ($tipo !== 'otro' && ! array_key_exists($tipo, $this->tipos())) return;

        $this->validate(
            ["archivos.{$tipo}" => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240']
                + ($tipo === 'otro' ? ['descripcionOtro' => 'required|string|max:150'] : []),
            [
                "archivos.{$tipo}.required" => 'Elige el archivo.',
                "archivos.{$tipo}.mimes"    => 'Solo PDF o imagen (JPG, PNG).',
                "archivos.{$tipo}.max"      => 'El archivo no puede superar 10 MB.',
                'descripcionOtro.required'  => 'Indica qué documento es.',
            ]
        );

        ExpedienteService::subir($this->persona(), $tipo, $this->archivos[$tipo], 'secretaria',
            descripcion: $tipo === 'otro' ? trim($this->descripcionOtro) : null);

        unset($this->archivos[$tipo]);
        if ($tipo === 'otro') $this->descripcionOtro = '';
        $this->avisar('Documento guardado y aprobado.');
    }

    public function aprobar(int $docId): void
    {
        if ($this->sinPermiso($this->permiso())) return;

        try {
            ExpedienteService::aprobar($this->documento($docId));
            $this->avisar('Documento aprobado.');
        } catch (\DomainException $e) {
            $this->avisar($e->getMessage(), 'warning');
        }
    }

    /** Quita un documento "otro" de lo vigente (queda en el historial). */
    public function quitar(int $docId): void
    {
        if ($this->sinPermiso($this->permiso())) return;
        ExpedienteService::retirar($this->documento($docId));
        $this->avisar('Documento quitado (queda en el historial).');
    }

    public function abrirAccion(string $accion, ?int $docId = null, ?string $tipo = null): void
    {
        if (! in_array($accion, ['rechazar', 'solicitar', 'eliminar'], true)) return;
        if ($accion === 'eliminar' && ! ExpedienteService::puedeEliminarDefinitivamente()) return;

        $this->resetErrorBag('accionTexto');
        $this->accion      = $accion;
        $this->accionDocId = $docId ? $this->documento($docId)->id : null;
        $this->accionTipo  = $tipo;
        $this->accionTexto = '';
    }

    public function cerrarAccion(): void
    {
        $this->reset(['accion', 'accionDocId', 'accionTipo', 'accionTexto']);
        $this->resetErrorBag('accionTexto');
    }

    public function confirmarAccion(): void
    {
        if ($this->sinPermiso($this->permiso())) return;

        $this->validate(['accionTexto' => 'required|string|min:5|max:500'], [
            'accionTexto.required' => $this->accion === 'eliminar' ? 'Indica el motivo.' : 'Escribe la observación para la persona.',
            'accionTexto.min'      => 'Explícalo con un poco más de detalle.',
        ]);

        try {
            match ($this->accion) {
                'rechazar'  => ExpedienteService::rechazar($this->documento($this->accionDocId), $this->accionTexto),
                'solicitar' => ExpedienteService::solicitar($this->persona(), (string) $this->accionTipo, $this->accionTexto),
                'eliminar'  => ExpedienteService::eliminarDefinitivamente($this->documento($this->accionDocId), $this->accionTexto),
                default     => null,
            };
        } catch (\DomainException $e) {
            $this->addError('accionTexto', $e->getMessage());
            return;
        }

        $mensaje = match ($this->accion) {
            'rechazar'  => 'Documento rechazado con tu observación.',
            'solicitar' => 'Documento solicitado.',
            'eliminar'  => 'Documento eliminado definitivamente.',
            default     => '',
        };
        $this->cerrarAccion();
        $this->avisar($mensaje);
    }

    public function verHistorial(string $tipo): void
    {
        $this->historialTipo = $this->historialTipo === $tipo ? null : $tipo;
    }

    private function avisar(string $texto, string $icono = 'success'): void
    {
        $this->dispatch('swal', ['icon' => $icono, 'title' => $texto, 'toast' => true, 'position' => 'top-end', 'timer' => 3000]);
        $this->dispatch('expediente-actualizado');
    }

    public function render()
    {
        $todos = ExpedienteDocumento::with(['subidoPor:id,name', 'revisadoPor:id,name'])
            ->where('user_id', $this->userId)
            ->whereNull('asignacion_docente_id')
            ->where('tipo', '!=', 'contrato')
            ->latest('id')->get();

        $vigentes = $todos->where('vigente', true);

        $persona = $this->persona();

        return view('livewire.expediente.panel-documentos', [
            'tipos'        => $this->tipos(),
            'esValidacion' => $this->perfil === 'estudiante' && ExpedienteService::esValidacion($persona),
            'pagoAdmision' => $this->perfil === 'estudiante' ? ExpedienteService::resumenPagoAdmision($persona) : null,
            'vigentePorTipo' => $vigentes->where('tipo', '!=', 'otro')->unique('tipo')->keyBy('tipo'),
            'versiones'    => $todos->groupBy('tipo')->map->count(),
            'otros'        => $vigentes->where('tipo', 'otro')->values(),
            'historial'    => $this->historialTipo ? $todos->where('tipo', $this->historialTipo)->values() : collect(),
            'puedeEliminar'=> ExpedienteService::puedeEliminarDefinitivamente(),
            'accionDoc'    => $this->accionDocId ? $todos->firstWhere('id', $this->accionDocId) : null,
        ]);
    }
}
