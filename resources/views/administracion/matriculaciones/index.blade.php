<x-admin-layout>
    <div class="p-4 rounded-lg bg-gray-100 dark:bg-gray-800 shadow-md">
        {{-- titulo --}}
        {{-- <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Matriculacion de Estudiantes</h1>
            <a href="#"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline text-sm">Por
                definir</a>
        </div> --}}
        {{-- mensaje de session --}}
        @if (session('menssage'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-5" role="alert">
                <p>{{ session('menssage') }}</p>
            </div>
        @endif

        <div class="bg-white p-4 rounded-lg shadow-md">
            {{-- Livewire component --}}
            @livewire('administration.matriculacion')
        </div>
    </div>
</x-admin-layout>
