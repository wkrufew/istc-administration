<x-admin-layout>

    <div>
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-5" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @livewire('administration.docente-administration')
    </div>
</x-admin-layout>
