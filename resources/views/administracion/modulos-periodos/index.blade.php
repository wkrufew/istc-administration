<x-admin-layout>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm shadow-sm">
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @livewire('administration.materia-periodo-paralelos-index')

</x-admin-layout>
