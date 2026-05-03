<x-admin-layout>

    <div class="p-6 space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4
            bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Asignación de Docente</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gestión académica · asignación de materias por período</p>
            </div>
            <a href="{{ route('administracion.administrativa.docentes.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                    bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300
                    hover:bg-gray-200 dark:hover:bg-gray-700 transition text-sm font-medium">
                ← Volver al listado
            </a>
        </div>

        {{-- PERFIL DOCENTE --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center gap-6">

                <div class="h-16 w-16 rounded-2xl bg-indigo-600 text-white
                    flex items-center justify-center text-2xl font-bold shadow-lg shrink-0">
                    {{ strtoupper(substr($docente->first_name ?? $docente->name, 0, 1)) }}
                </div>

                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $docente->first_name ?? '' }} {{ $docente->last_name ?? $docente->name }}
                    </h2>
                    <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium mb-3">Docente</p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Cédula</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $docente->cedula ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Celular</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $docente->phone ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Correo</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $docente->email ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Género</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $docente->genero ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COMPONENTE LIVEWIRE --}}
        @livewire('administration.asignacion-docente', ['docente' => $docente])

    </div>

</x-admin-layout>
