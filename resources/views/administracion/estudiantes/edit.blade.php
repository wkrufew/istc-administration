<x-admin-layout>
    
        @if (session('menssage'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-5" role="alert">
                <p>{{ session('menssage') }}</p>
            </div>
        @endif

        @livewire('administration.edit-user', ['estudiante' => $estudiante], key($estudiante->id))
       
    </div>
</x-admin-layout>
