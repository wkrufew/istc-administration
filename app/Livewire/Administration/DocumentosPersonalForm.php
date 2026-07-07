<?php

namespace App\Livewire\Administration;

use App\Models\AsignacionDocente;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentosPersonalForm extends Component
{
    use WithFileUploads;

    public User      $user;
    public ?Document $document = null;
    public bool      $esEdicion = false;

    // Documentos permanentes
    public $fileCurriculum = null;
    public $fileSenescyt   = null;
    public $fileCedula     = null;

    // Contratos por asignación
    public ?int $asignacionContratoId = null;
    public $contratoTemporal          = null;

    public function mount(User $user): void
    {
        $this->user      = $user;
        $this->document  = Document::where('user_id', $user->id)->first();
        $this->esEdicion = (bool) $this->document;
    }

    public function save(): void
    {
        $this->validate([
            'fileCurriculum' => 'nullable|file|mimes:pdf|max:10240',
            'fileSenescyt'   => 'nullable|file|mimes:pdf|max:10240',
            'fileCedula'     => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if (! $this->esEdicion && ! $this->fileCurriculum && ! $this->fileSenescyt && ! $this->fileCedula) {
            $this->addError('files', 'Debe cargar al menos un archivo para continuar.');
            return;
        }

        $userSlug = Str::slug($this->user->name);

        $slots = [
            'fileCurriculum' => ['column' => 'file_curriculum', 'dir' => 'documentos-personal/curriculum', 'suffix' => 'curriculum'],
            'fileSenescyt'   => ['column' => 'file_senescyt',   'dir' => 'documentos-personal/senescyt',   'suffix' => 'senescyt'],
            'fileCedula'     => ['column' => 'file_cedula',     'dir' => 'documentos-personal/cedula',     'suffix' => 'cedula'],
        ];

        $data = ['user_id' => $this->user->id];

        foreach ($slots as $prop => $meta) {
            if ($this->$prop) {
                if ($this->document?->{$meta['column']}) {
                    Storage::disk('public')->delete($this->document->{$meta['column']});
                }
                $filename          = $userSlug . '-' . $meta['suffix'] . '-' . time() . '.' . $this->$prop->getClientOriginalExtension();
                $data[$meta['column']] = $this->$prop->storeAs($meta['dir'], $filename, 'public');
            }
        }

        if ($this->document) {
            $this->document->update($data);
        } else {
            $this->document  = Document::create($data);
            $this->esEdicion = true;
        }

        $this->fileCurriculum = null;
        $this->fileSenescyt   = null;
        $this->fileCedula     = null;

        $this->document = Document::where('user_id', $this->user->id)->first();

        $this->dispatch('toast', message: 'Documentos guardados correctamente.', type: 'success');
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

        if ($asignacion->file_contrato) {
            Storage::disk('public')->delete($asignacion->file_contrato);
        }

        $userSlug = Str::slug($this->user->name);
        $filename = $userSlug . '-contrato-' . $asignacion->id . '-' . time() . '.' . $this->contratoTemporal->getClientOriginalExtension();
        $path     = $this->contratoTemporal->storeAs('documentos-personal/contratos', $filename, 'public');

        $asignacion->update(['file_contrato' => $path]);

        $this->asignacionContratoId = null;
        $this->contratoTemporal     = null;

        $this->dispatch('toast', message: 'Contrato guardado correctamente.', type: 'success');
    }

    public function eliminarContrato(int $asignacionId): void
    {
        $asignacion = AsignacionDocente::where('id', $asignacionId)
            ->where('docente_id', $this->user->id)
            ->first();

        if ($asignacion?->file_contrato) {
            Storage::disk('public')->delete($asignacion->file_contrato);
            $asignacion->update(['file_contrato' => null]);
            $this->dispatch('toast', message: 'Contrato eliminado.', type: 'success');
        }
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $asignaciones = AsignacionDocente::where('docente_id', $this->user->id)
            ->with(['materia', 'paralelo', 'periodo'])
            ->orderByDesc('periodo_id')
            ->get();

        return view('livewire.administration.documentos-personal-form', compact('asignaciones'));
    }
}
