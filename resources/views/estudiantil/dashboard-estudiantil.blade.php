<x-estudiantil-layout>
    <x-slot name="header">
            {{ __('Dashboard del Estudiante') }}
    </x-slot>
    <div class="py-1">
        @livewire('estudiante.dashboard-estudiante')
    </div>
</x-estudiantil-layout>
