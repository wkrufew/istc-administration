<?php

namespace App\Livewire\Administration;

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

    public $fileCurriculum = null;
    public $fileSenescyt   = null;
    public $fileContrato   = null;
    public $fileOtro       = null;

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
            'fileContrato'   => 'nullable|file|mimes:pdf|max:10240',
            'fileOtro'       => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if (! $this->esEdicion && ! $this->fileCurriculum && ! $this->fileSenescyt
            && ! $this->fileContrato && ! $this->fileOtro) {
            $this->addError('files', 'Debe cargar al menos un archivo para continuar.');
            return;
        }

        $userSlug = Str::slug($this->user->name);

        $slots = [
            'fileCurriculum' => ['column' => 'file_curriculum', 'dir' => 'documentos-personal/curriculum', 'suffix' => 'curriculum'],
            'fileSenescyt'   => ['column' => 'file_senescyt',   'dir' => 'documentos-personal/senescyt',   'suffix' => 'senescyt'],
            'fileContrato'   => ['column' => 'file_contrato',   'dir' => 'documentos-personal/contrato',   'suffix' => 'contrato'],
            'fileOtro'       => ['column' => 'file_otro',       'dir' => 'documentos-personal/otro',       'suffix' => 'otro'],
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

        // Reset file inputs
        $this->fileCurriculum = null;
        $this->fileSenescyt   = null;
        $this->fileContrato   = null;
        $this->fileOtro       = null;

        // Refresh document from DB
        $this->document = Document::where('user_id', $this->user->id)->first();

        $this->dispatch('toast', message: 'Documentos guardados correctamente.', type: 'success');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.documentos-personal-form');
    }
}
