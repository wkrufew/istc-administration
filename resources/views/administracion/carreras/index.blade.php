<x-admin-layout>
    <div
        class="max-w-7xl mx-auto bg-white dark:bg-gray-900 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 p-5 sm:p-6 mb-6">

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Carreras
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Administra las carreras del instituto.
                </p>
            </div>

            <a href="{{ route('administracion.administrativa.carreras.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-lime-600 hover:bg-lime-700 text-white px-4 py-2.5 rounded-xl transition font-semibold shadow-sm w-full sm:w-auto">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Crear Carrera
            </a>
        </div>

        {{-- Alert Success --}}
        @if (session('success'))
            <div
                class="mb-5 rounded-xl border border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-950/40 p-4">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53-1.59-1.59a.75.75 0 10-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.716-5.184z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>

                    <div class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr class="text-gray-700 dark:text-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Carrera</th>
                        <th class="px-4 py-3 text-left font-semibold">Código</th>
                        <th class="px-4 py-3 text-left font-semibold">Costos / Duración</th>
                        <th class="px-4 py-3 text-center font-semibold">Estado</th>
                        <th class="px-4 py-3 text-center font-semibold">Opciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                    @forelse ($carreras as $carrera)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100 font-medium text-left">
                                #{{ $carrera->id }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $carrera->name }}
                                </div>

                                {{-- Modalidad Badge --}}
                                @php
                                    $modalidad = $carrera->modalidad;

                                    $modalidadClass = match ($modalidad) {
                                        'Presencial'
                                            => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 border border-green-200 dark:border-green-800',
                                        'Virtual'
                                            => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200 dark:border-purple-800',
                                        'Híbrida'
                                            => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300 border border-sky-200 dark:border-sky-800',
                                        default
                                            => 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300 border border-teal-200 dark:border-teal-800',
                                    };
                                @endphp

                                <span
                                    class="inline-flex items-center mt-2 px-2.5 py-1 rounded-full text-xs font-semibold {{ $modalidadClass }}">
                                    {{ $modalidad }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-medium">{{ $carrera->code }}</span>
                            </td>

                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <div class="leading-5">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                                        $ {{ $carrera->costo_credito }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">/ crédito</span>
                                </div>

                                <div class="leading-5 mt-1">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $carrera->duracion_semestres }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">semestres</span>
                                </div>

                                <div class="leading-5 mt-1">
                                    <span class="text-gray-500 dark:text-gray-400">Total:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                                        $ {{ $carrera->costo_carrera }}
                                    </span>
                                </div>
                            </td>

                            {{-- Estado --}}
                            <td class="px-4 py-3 text-center">
                                @if ($carrera->is_active)
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold
                                        bg-green-100 text-green-800 border border-green-200
                                        dark:bg-green-900/40 dark:text-green-200 dark:border-green-800">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        Activo
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold
                                        bg-red-100 text-red-800 border border-red-200
                                        dark:bg-red-900/40 dark:text-red-200 dark:border-red-800">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            {{-- Opciones --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ route('administracion.administrativa.carreras.edit', $carrera) }}"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                                        bg-gray-100 hover:bg-gray-200 text-gray-700
                                        dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200 transition"
                                        title="Editar">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                            fill="currentColor">
                                            <path
                                                d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" />
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form class="eliminar-carrera"
                                        action="{{ route('administracion.administrativa.carreras.destroy', $carrera) }}"
                                        method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                                            bg-red-50 hover:bg-red-100 text-red-700
                                            dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-200 transition"
                                            title="Eliminar">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 448 512" fill="currentColor">
                                                <path
                                                    d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm96 64c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16z" />
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Asignación --}}
                                    <a href="#"
                                        class="hidden lg:inline-flex items-center justify-center px-3 py-2 rounded-xl
                                        bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs
                                        dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-200 transition">
                                        Asignación
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    No hay ninguna carrera registrada.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('js')
        <script>
            $('.eliminar-carrera').submit(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: '¿Estás seguro de eliminar esta carrera?',
                    text: "El borrado es permanente y no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#dc2626',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                })
            })
        </script>
    @endpush
</x-admin-layout>
