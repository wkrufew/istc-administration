<x-admin-layout>

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
                        Horarios
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Gestiona los horarios por día, paralelo, materia y periodo.
                    </p>
                </div>

                <a href="{{ route('administracion.administrativa.horarios.create') }}"
                    class="inline-flex items-center justify-center gap-2 bg-lime-600 hover:bg-lime-700 text-white
                           font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Crear Horario
                </a>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm shadow-sm">
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif


        {{-- ================= MOBILE VIEW (Cards) ================= --}}
        <div class="grid grid-cols-1 gap-4 lg:hidden">
            @forelse ($horarios as $key => $horario)
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm p-4">

                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                No. {{ $key + 1 }}
                            </p>

                            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">
                                {{ $horario->dia_semana }}
                            </h3>

                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                            </p>
                        </div>

                        {{-- Estado --}}
                        <div class="text-right">
                            @if ($horario->is_active)
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                           text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-900/40">
                                    Activo
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                           text-red-700 bg-red-100 dark:text-red-200 dark:bg-red-900/40">
                                    Inactivo
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="mt-4 space-y-2 text-sm">

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">Aula</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $horario->aula ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">Paralelo</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $horario->paralelo->name }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">Materia</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100 text-right">
                                {{ $horario->materia->name }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">Modalidad</span>
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                       text-blue-700 bg-blue-100 dark:text-blue-200 dark:bg-blue-900/40">
                                {{ $horario->modalidad_clase }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">Periodo</span>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                           text-sky-700 bg-sky-100 dark:text-sky-200 dark:bg-sky-900/40">
                                    {{ $horario->periodo->code }}
                                </span>

                                @if ($horario->periodo->is_current)
                                    <span
                                        class="ml-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                               text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-900/40">
                                        Actual
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">Docente</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100 text-right">
                                {{ $horario->asignacionDocente->docente->name ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">Carrera</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100 text-right">
                                {{ $horario->materia->semestre->carrera->name }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">Semestre</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100 text-right">
                                {{ $horario->materia->semestre->name }}
                            </span>
                        </div>

                    </div>

                    {{-- Acciones --}}
                    <div class="mt-5 flex items-center justify-end gap-2">

                        <a href="{{ route('administracion.administrativa.horarios.edit', $horario) }}"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold
                                   bg-gray-100 hover:bg-gray-200 text-gray-800
                                   dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100 transition">
                            ✏️ Editar
                        </a>

                        <form action="{{ route('administracion.administrativa.horarios.destroy', $horario) }}"
                            method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este horario?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold
                                       bg-red-600 hover:bg-red-700 text-white transition">
                                🗑 Eliminar
                            </button>
                        </form>

                    </div>
                </div>
            @empty
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 text-center">
                    <p class="text-gray-600 dark:text-gray-300">
                        No hay horarios registrados.
                    </p>
                </div>
            @endforelse
        </div>


        {{-- ================= DESKTOP VIEW (Table) ================= --}}
        <div
            class="hidden lg:block bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr class="text-gray-700 dark:text-gray-200">
                            <th class="px-4 py-3 text-center font-bold w-16">No.</th>
                            <th class="px-4 py-3 text-center font-bold w-44">Horario</th>
                            <th class="px-4 py-3 text-center font-bold">Aula / Paralelo</th>
                            <th class="px-4 py-3 text-center font-bold">Carrera / Semestre</th>
                            <th class="px-4 py-3 text-center font-bold">Materia</th>
                            <th class="px-4 py-3 text-center font-bold w-28">Periodo</th>
                            <th class="px-4 py-3 text-center font-bold">Docente</th>
                            <th class="px-4 py-3 text-center font-bold w-24">Estado</th>
                            <th class="px-4 py-3 text-center font-bold w-24">Opciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($horarios as $key => $horario)
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition text-gray-700 dark:text-gray-200">

                                <td class="px-4 py-3 text-center font-medium">
                                    {{ $key + 1 }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $horario->dia_semana }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $horario->aula ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $horario->paralelo->name }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $horario->materia->semestre->carrera->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $horario->materia->semestre->name }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $horario->materia->name }}
                                    </div>
                                    <div class="mt-1">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                                   text-blue-700 bg-blue-100 dark:text-blue-200 dark:bg-blue-900/40">
                                            {{ $horario->modalidad_clase }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                               text-sky-700 bg-sky-100 dark:text-sky-200 dark:bg-sky-900/40">
                                        {{ $horario->periodo->code }}
                                    </div>

                                    @if ($horario->periodo->is_current)
                                        <div class="mt-1">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                                       text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-900/40">
                                                Actual
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $horario->asignacionDocente->docente->name ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($horario->is_active)
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                                   text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-900/40">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                                   text-red-700 bg-red-100 dark:text-red-200 dark:bg-red-900/40">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('administracion.administrativa.horarios.edit', $horario) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                                   bg-gray-100 hover:bg-gray-200
                                                   dark:bg-gray-800 dark:hover:bg-gray-700 transition"
                                            title="Editar">
                                            <svg class="w-5 h-5 fill-green-600 dark:fill-green-400"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path
                                                    d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zM172.4 241.7c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7z" />
                                            </svg>
                                        </a>

                                        <form
                                            action="{{ route('administracion.administrativa.horarios.destroy', $horario) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este horario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                                       bg-red-600 hover:bg-red-700 transition"
                                                title="Eliminar">
                                                <svg class="w-5 h-5 fill-white" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 448 512">
                                                    <path
                                                        d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm96 64c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                    No hay horarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if (method_exists($horarios, 'hasPages') && $horarios->hasPages())
            <div class="mt-4">
                {{ $horarios->links() }}
            </div>
        @endif

    </div>

</x-admin-layout>
