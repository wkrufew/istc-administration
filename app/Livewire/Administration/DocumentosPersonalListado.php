<?php

namespace App\Livewire\Administration;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentosPersonalListado extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteDocument(int $documentId): void
    {
        $doc = Document::find($documentId);

        if (! $doc) {
            $this->dispatch('toast', message: 'Documento no encontrado.', type: 'error');
            return;
        }

        foreach (['file_curriculum', 'file_senescyt', 'file_cedula'] as $field) {
            if ($doc->$field && Storage::disk('public')->exists($doc->$field)) {
                Storage::disk('public')->delete($doc->$field);
            }
        }

        $doc->delete();

        $this->dispatch('toast', message: 'Documentos eliminados correctamente.', type: 'success');
    }

    public function countFiles(Document $doc): int
    {
        return collect(['file_curriculum', 'file_senescyt', 'file_cedula'])
            ->filter(fn($f) => (bool) $doc->$f)
            ->count();
    }

    public function render()
    {
        $documentos = Document::with('user.roles')
            ->whereHas('user', function ($q) {
                $q->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Estudiante', 'Admision']))
                  ->where(fn($s) => $s
                      ->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('cedula', 'like', "%{$this->search}%")
                  );
            })
            ->latest()
            ->paginate(12);

        return view('livewire.administration.documentos-personal-listado', compact('documentos'));
    }
}
