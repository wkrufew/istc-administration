<x-admin-layout>
    {{-- <div class="p-4 rounded-lg bg-gray-100 dark:bg-gray-800 shadow-md">
        @if (session('menssage'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-5" role="alert">
                <p>{{ session('menssage') }}</p>
            </div>
        @endif --}}


    @livewire('administration.proceso-titulacion')

    {{--  </div> --}}
</x-admin-layout>
