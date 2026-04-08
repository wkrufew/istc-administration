<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase">
            {{ __('DASHBOARD DE DOCENCIA') }}
            {{-- {{ __('BIENVENIDO AL AREA DE DOCENTE ESTIMADO(A)') }} {{ auth()->user()->name }} --}}
        </h2>
    </x-slot>
    <div>
        @livewire('docente.dashboard')
    </div>
    <div>
        @livewire('docente.reporte-asistencias')
    </div>
</x-app-layout>
