<?php

namespace App\Livewire\Administration;

use App\Models\ExpedienteDocumento;
use App\Models\User;
use App\Services\ExpedienteService;
use App\Traits\WithAuthorization;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado "Documentación" del personal: quién tiene cargados hoja de vida, SENESCYT y cédula
 * en su expediente. Quitar los documentos los deja en el historial (no borra archivos).
 */
class DocumentosPersonalListado extends Component
{
    use WithPagination, WithAuthorization;

    public string $search = '';

    /** Columna que usa la vista → tipo del expediente. */
    private const TIPOS = ['file_curriculum' => 'hoja_vida', 'file_senescyt' => 'senescyt', 'file_cedula' => 'cedula'];

    public function mount(): void
    {
        $this->requierePermiso('gestionar_docentes');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /** Quita los documentos personales vigentes de la persona (quedan en el historial). */
    public function deleteDocument(int $userId): void
    {
        if ($this->sinPermiso('gestionar_docentes')) return;

        $docs = ExpedienteDocumento::vigentes()->where('user_id', $userId)
            ->whereIn('tipo', self::TIPOS)->whereNull('asignacion_docente_id')->get();

        if ($docs->isEmpty()) {
            $this->dispatch('toast', message: 'Documento no encontrado.', type: 'error');
            return;
        }

        $docs->each(fn($d) => ExpedienteService::retirar($d));

        $this->dispatch('toast', message: 'Documentos quitados (quedan en el historial del expediente).', type: 'success');
    }

    public function countFiles(object $doc): int
    {
        return collect(array_keys(self::TIPOS))->filter(fn($f) => (bool) $doc->$f)->count();
    }

    public function render()
    {
        $conDocumentos = fn($q) => $q->vigentes()->whereIn('tipo', self::TIPOS)->whereNull('asignacion_docente_id');

        $documentos = User::query()
            ->whereHas('expediente', $conDocumentos)
            ->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Estudiante', 'Admision']))
            ->when($this->search, fn($q) => $q->where(fn($s) => $s
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('cedula', 'like', "%{$this->search}%")
            ))
            ->with(['roles', 'expediente' => $conDocumentos])
            ->withMax(['expediente as ultimo_documento' => $conDocumentos], 'subido_at')
            ->orderByDesc('ultimo_documento')
            ->paginate(12)
            ->through(function (User $user) {
                $vigentes = $user->expediente->sortByDesc('id')->unique('tipo')->keyBy('tipo');
                $fila = (object) [
                    'id'         => $user->id,
                    'user'       => $user,
                    'created_at' => \Illuminate\Support\Carbon::parse($user->ultimo_documento ?? $user->created_at),
                ];
                foreach (self::TIPOS as $columna => $tipo) {
                    $fila->$columna = $vigentes->get($tipo)?->url();
                }
                return $fila;
            });

        return view('livewire.administration.documentos-personal-listado', compact('documentos'));
    }
}
