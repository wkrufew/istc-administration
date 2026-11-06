{{-- Alta de una persona desde su sección: Estudiantes, Docentes o Usuarios (cuentas administrativas) --}}
<x-admin-layout>
    @livewire('administration.create-user', ['perfil' => $perfil])
</x-admin-layout>
