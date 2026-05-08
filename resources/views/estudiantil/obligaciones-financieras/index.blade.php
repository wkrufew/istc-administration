<x-estudiantil-layout>
    <x-slot name="header">
            {{ __('Pagos') }}
    </x-slot>
        <div class="my-4 max-w-7xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            @livewire('estudiante.obligaciones-financieras')
        </div>
</x-estudiantil-layout>
