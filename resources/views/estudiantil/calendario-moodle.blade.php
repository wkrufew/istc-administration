<x-estudiantil-layout>
    <x-slot name="header">
        {{ __('Actividades Moodle') }}
    </x-slot>
    <div class="py-1">
        @livewire('estudiante.calendario-moodle')
    </div>
</x-estudiantil-layout>
