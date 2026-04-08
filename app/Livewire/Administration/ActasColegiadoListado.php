<?php

namespace App\Livewire\Administration;

use App\Models\Ocsa;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class ActasColegiadoListado extends Component
{
    use WithPagination;

    public $search;

    public function render()
    {
        $documents = Ocsa::where('title', 'LIKE', '%' . $this->search . '%')
            ->latest('id')
            ->paginate(10);

        return view('livewire.administration.actas-colegiado-listado', compact('documents'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatusc($document)
    {
        $document = Ocsa::find($document);

        try {

            $document->is_active = !$document->is_active;
            $document->save();

            $this->dispatch('alert', [
                'message' => $document->is_active == 1 ? 'Documento publicado con éxito.' : 'Documento en borrador.',
                'type' => 'success',
                'title' => 'Actualización exitosa'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => 'Error al actualizar el estado del documento',
                'type' => 'error',
                'title' => 'Error'
            ]);
        }
    }

    public function deleteDocumento($document)
    {
        //dd($document);
        $document = Ocsa::find($document);

        try {
            $document->delete();
            /* ahora eliminar la imagen */
            if ($document->file) {
                Storage::delete($document->file);
            }

            $this->dispatch('alert', [
                'message' => 'Documento eliminado con éxito.',
                'type' => 'success',
                'title' => 'Eliminación exitosa'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => 'Error al eliminar el documento',
                'type' => 'error',
                'title' => 'Error'
            ]);
        }
    }
}
