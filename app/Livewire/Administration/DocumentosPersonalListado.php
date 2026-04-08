<?php

namespace App\Livewire\Administration;

use App\Models\Document;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use App\Models\User;

class DocumentosPersonalListado extends Component
{
    use WithPagination;

    public $search;

    /* public ?User $user = null;

    public function mount(?User $user = null)
    {
        $this->user = $user;
    } */

    public function render()
    {
        /* $documents = Document::with('user')
            ->whereHas('user', function ($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $this->search . '%');
            })
            ->latest('id')
            ->paginate(10); */
        $documentacion_personals = Document::with('user.roles')
            ->whereHas('user', function ($query) {
                // Filtrar usuarios que NO tengan los roles de 'estudiante' o 'admision'
                $query->whereDoesntHave('roles', function ($roleQuery) {
                    $roleQuery->whereIn('name', ['Estudiante', 'Admision']);
                })
                    ->where(function ($searchQuery) {
                        $searchQuery->where('name', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('email', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('cedula', 'LIKE', '%' . $this->search . '%');
                    });
            })
            ->latest('id')
            ->paginate(10);

        return view('livewire.administration.documentos-personal-listado', compact('documentacion_personals'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function countFiles($documentacion_personal)
    {
        $count = 0;
        if ($documentacion_personal->file_curriculum) $count++;
        if ($documentacion_personal->file_senescyt) $count++;
        if ($documentacion_personal->file_contrato) $count++;
        if ($documentacion_personal->file_otro) $count++;
        return $count;
    }

    public function deleteDocument($documentId)
    {
        $documentacion_personal = Document::find($documentId);

        try {
            // Eliminar todos los archivos asociados
            if ($documentacion_personal->file_curriculum && Storage::disk('public')->exists($documentacion_personal->file_curriculum)) {
                Storage::disk('public')->delete($documentacion_personal->file_curriculum);
            }
            if ($documentacion_personal->file_senescyt && Storage::disk('public')->exists($documentacion_personal->file_senescyt)) {
                Storage::disk('public')->delete($documentacion_personal->file_senescyt);
            }
            if ($documentacion_personal->file_contrato && Storage::disk('public')->exists($documentacion_personal->file_contrato)) {
                Storage::disk('public')->delete($documentacion_personal->file_contrato);
            }
            if ($documentacion_personal->file_otro && Storage::disk('public')->exists($documentacion_personal->file_otro)) {
                Storage::disk('public')->delete($documentacion_personal->file_otro);
            }

            $documentacion_personal->delete();

            $this->dispatch('alert', [
                'message' => 'Documentos eliminados con éxito.',
                'type' => 'success',
                'title' => 'Eliminación exitosa'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => 'Error al eliminar los documentos',
                'type' => 'error',
                'title' => 'Error'
            ]);
        }
    }
}
