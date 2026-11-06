<x-admin-layout>
    @livewire('administration.estudiantes.ficha-estudiante', ['estudiante' => $estudiante], key('ficha-' . $estudiante->id))
</x-admin-layout>
