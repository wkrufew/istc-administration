<x-estudiantil-layout>
    <x-slot name="header">
            {{ __('Calificaciones') }}
    </x-slot>
        <div class="my-4 max-w-7xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            @livewire('estudiante.calificaciones-estudiante')
        </div>
</x-estudiantil-layout>
