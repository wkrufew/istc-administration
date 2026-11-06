<x-admin-layout>
    @livewire('administration.usuarios.editar-cuenta', ['usuario' => $user], key('cuenta-' . $user->id))
</x-admin-layout>
