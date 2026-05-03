<x-admin-layout>
    @if (session('menssage'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-5" role="alert">
            <p>{{ session('menssage') }}</p>
        </div>
    @endif
    {{-- Livewire component --}}
    @livewire('administration.matriculacion')
</x-admin-layout>
