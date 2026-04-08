<x-admin-layout>
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div
            class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
                        Materias
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Gestión de materias, créditos, horas y estado.
                    </p>
                </div>

                <a href="{{ route('administracion.administrativa.materias.create') }}"
                    class="inline-flex items-center justify-center gap-2 bg-lime-600 hover:bg-lime-700 text-white px-4 py-2.5 rounded-lg transition font-semibold shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Crear Materia
                </a>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div
                class="rounded-xl border border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-950/40 px-4 py-3">
                <p class="text-sm font-medium text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        {{-- ====== MOBILE VIEW (Cards) ====== --}}
        <div class="grid grid-cols-1 gap-4 lg:hidden">
            @forelse ($materias as $materia)
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                ID #{{ $materia->id }}
                            </p>

                            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 leading-tight">
                                {{ $materia->name }}
                            </h3>

                            {{-- Tipo --}}
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $materia->tipo == 'Obligatoria'
                                        ? 'text-red-700 bg-red-100 dark:text-red-200 dark:bg-red-900/40'
                                        : ($materia->tipo == 'Electiva'
                                            ? 'text-yellow-800 bg-yellow-100 dark:text-yellow-200 dark:bg-yellow-900/40'
                                            : 'text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-900/40') }}">
                                    {{ $materia->tipo }}
                                </span>
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="text-right">
                            @if ($materia->is_active)
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-900/40">
                                    Activo
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-red-700 bg-red-100 dark:text-red-200 dark:bg-red-900/40">
                                    Inactivo
                                </span>
                            @endif

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $materia->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Código</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $materia->code }}
                            </p>
                            <p class="mt-1 text-xs text-sky-700 dark:text-sky-300">
                                ({{ $materia->semestre->name }})
                            </p>
                        </div>

                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Créditos</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $materia->credits }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Horas</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $materia->horas_teoricas }} Teóricas
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                {{ $materia->horas_practicas }} Prácticas
                            </p>
                        </div>

                        <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Nota mínima</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $materia->nota_minima_aprobacion }} pts
                            </p>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="mt-4 flex items-center justify-end gap-2">
                        <a href="{{ route('administracion.administrativa.materias.edit', $materia) }}"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold
                                   bg-gray-100 hover:bg-gray-200 text-gray-800
                                   dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100 transition">
                            ✏️ Editar
                        </a>

                        <form action="{{ route('administracion.administrativa.materias.destroy', $materia) }}"
                            method="POST">
                            @method('DELETE')
                            @csrf
                            <button type="submit" onclick="return confirm('¿Seguro que deseas eliminar esta materia?')"
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
                        No hay ninguna materia registrada.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- ====== DESKTOP VIEW (Table) ====== --}}
        <div
            class="hidden lg:block bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr class="text-gray-700 dark:text-gray-200">
                            <th class="px-4 py-3 text-center font-bold">ID</th>
                            <th class="px-4 py-3 text-left font-bold">Materia</th>
                            <th class="px-4 py-3 text-center font-bold">Código</th>
                            <th class="px-4 py-3 text-center font-bold">Créditos</th>
                            <th class="px-4 py-3 text-center font-bold">Horas</th>
                            <th class="px-4 py-3 text-center font-bold">Nota Mín.</th>
                            <th class="px-4 py-3 text-center font-bold">Estado</th>
                            <th class="px-4 py-3 text-center font-bold">Opciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($materias as $materia)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-200">
                                    {{ $materia->id }}
                                </td>

                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $materia->name }}
                                    </p>

                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold mt-1
                                        {{ $materia->tipo == 'Obligatoria'
                                            ? 'text-red-700 bg-red-100 dark:text-red-200 dark:bg-red-900/40'
                                            : ($materia->tipo == 'Electiva'
                                                ? 'text-yellow-800 bg-yellow-100 dark:text-yellow-200 dark:bg-yellow-900/40'
                                                : 'text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-900/40') }}">
                                        {{ $materia->tipo }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <p class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $materia->code }}
                                    </p>

                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold mt-1
                                               text-sky-700 bg-sky-100 dark:text-sky-200 dark:bg-sky-900/40">
                                        {{ $materia->semestre->name }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-200">
                                    {{ $materia->credits }}
                                </td>

                                <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-200">
                                    <p>{{ $materia->horas_teoricas }} H. Teóricas</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $materia->horas_practicas }} H. Prácticas
                                    </p>
                                </td>

                                <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-200">
                                    {{ $materia->nota_minima_aprobacion }} pts
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($materia->is_active)
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

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $materia->updated_at->diffForHumans() }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('administracion.administrativa.materias.edit', $materia) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                                   bg-gray-100 hover:bg-gray-200
                                                   dark:bg-gray-800 dark:hover:bg-gray-700 transition"
                                            title="Editar">
                                            <svg class="w-5 h-5 fill-green-600 dark:fill-green-400"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path
                                                    d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" />
                                            </svg>
                                        </a>

                                        <form
                                            action="{{ route('administracion.administrativa.materias.destroy', $materia) }}"
                                            method="POST">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('¿Seguro que deseas eliminar esta materia?')"
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
                                <td colspan="8" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                    No hay ninguna materia registrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admin-layout>
