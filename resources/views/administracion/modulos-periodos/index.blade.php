<x-admin-layout>
    {{--     <div class="p-4 rounded-lg bg-gray-100 dark:bg-gray-800 shadow-md">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                Módulos (Materia - Periodo - Paralelo)
            </h1>
        </div> --}}

    @if (session('menssage'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-5" role="alert">
            <p>{{ session('menssage') }}</p>
        </div>
    @endif

    <div class="">
        @livewire('administration.materia-periodo-paralelos-index')
    </div>
    {{-- </div> --}}

</x-admin-layout>
