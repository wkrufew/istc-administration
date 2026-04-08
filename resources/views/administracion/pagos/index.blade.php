<x-admin-layout>

    {{-- mensaje de session --}}
    @if (session('menssage'))
        <div class="max-w-7xl mx-auto">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-5 rounded-lg shadow">
                <p>{{ session('menssage') }}</p>
            </div>
        </div>
    @endif

    {{-- Contenedor limpio para el componente --}}
    {{-- <div class="max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700"> --}}
    @livewire('administration.pagos-listado')
    {{-- </div>
    </div> --}}

</x-admin-layout>
