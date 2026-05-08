<x-estudiantil-layout>
    <x-slot name="header">
            {{ __('Horario') }}
    </x-slot>
    <div class="py-6">
        {{-- @livewire('docente.dashboard') --}}
        <div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow-lg">
            <h1 class="uppercase text-lg font-semibold text-center">Datos generales</h1>
            @livewire('estudiante.horario-estudiante')
        </div>
    </div>
</x-estudiantil-layout>
