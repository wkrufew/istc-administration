<x-estudiantil-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase">
            {{ __('BIENVENIDO AL DASHBOARD ESTUDIANTIL') }} {{ auth()->user()->name }}
        </h2>
    </x-slot> --}}
    <div class="py-1">
        @livewire('estudiante.dashboard-estudiante')
    </div>
</x-estudiantil-layout>
