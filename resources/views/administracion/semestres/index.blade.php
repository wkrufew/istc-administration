<x-admin-layout>
    <div
        class="max-w-7xl mx-auto bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl p-4 mb-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Semestres
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Listado general de semestres registrados
                </p>
            </div>

            <a href="{{ route('administracion.administrativa.semestres.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-lime-600 text-white px-5 py-2.5 rounded-xl
                hover:bg-lime-700 transition font-medium shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Crear Semestre
            </a>
        </div>

        {{-- Alert Success --}}
        @if (session('success'))
            <div
                class="mb-5 rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-green-800 dark:text-green-200 text-sm">
                <span class="font-semibold">✔</span> {{ session('success') }}
            </div>
        @endif

        {{-- TABLA Desktop --}}
        <div class="hidden md:block overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-900 dark:bg-black text-white text-xs uppercase tracking-wide">
                        <th class="px-4 py-3 text-center">ID</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-center">Código</th>
                        <th class="px-4 py-3 text-center">Carrera</th>
                        <th class="px-4 py-3 text-center">Créditos</th>
                        <th class="px-4 py-3 text-center">Orden</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Opciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                    @forelse ($semestres as $semestre)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                            <td class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-200">
                                {{ $semestre->id }}
                            </td>

                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100 font-semibold">
                                {{ $semestre->name }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-xs font-medium">
                                    {{ $semestre->code }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-200">
                                {{ $semestre->carrera->name }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-200">
                                <span class="font-semibold">{{ $semestre->creditos_minimos }}</span>
                                -
                                <span class="font-semibold">{{ $semestre->creditos_maximos }}</span> <br>
                                <span class="text-xs text-gray-500 dark:text-gray-400">créditos</span>
                            </td>

                            <td class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-200">
                                {{ $semestre->order }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($semestre->is_active)
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold
                                        bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-200 border border-green-200 dark:border-green-800">
                                        ● Activo
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold
                                        bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-200 border border-red-200 dark:border-red-800">
                                        ● Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-3">

                                    {{-- Edit --}}
                                    <a href="{{ route('administracion.administrativa.semestres.edit', $semestre) }}"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl
                                        bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800
                                        hover:bg-green-100 dark:hover:bg-green-900/50 transition">
                                        <svg class="w-5 h-5 fill-green-600 dark:fill-green-300"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path
                                                d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" />
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form
                                        action="{{ route('administracion.administrativa.semestres.destroy', $semestre) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este semestre?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-xl
                                            bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800
                                            hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                                            <svg class="w-5 h-5 fill-red-600 dark:fill-red-300"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
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
                            <td colspan="8" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                                No hay ningún semestre registrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="grid grid-cols-1 gap-4 md:hidden">
            @forelse ($semestres as $semestre)
                <div
                    class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white dark:bg-gray-900 shadow-sm">

                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">
                                {{ $semestre->name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $semestre->carrera->name }}
                            </p>
                        </div>

                        @if ($semestre->is_active)
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-200">
                                Activo
                            </span>
                        @else
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-200">
                                Inactivo
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 text-sm text-gray-700 dark:text-gray-200 space-y-1">
                        <p><span class="font-semibold">Código:</span> {{ $semestre->code }}</p>
                        <p><span class="font-semibold">Créditos:</span>
                            {{ $semestre->creditos_minimos }} - {{ $semestre->creditos_maximos }}
                        </p>
                        <p><span class="font-semibold">Orden:</span> {{ $semestre->order }}</p>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('administracion.administrativa.semestres.edit', $semestre) }}"
                            class="flex-1 text-center px-4 py-2 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 transition">
                            Editar
                        </a>

                        <form action="{{ route('administracion.administrativa.semestres.destroy', $semestre) }}"
                            method="POST" class="flex-1"
                            onsubmit="return confirm('¿Seguro que deseas eliminar este semestre?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="w-full px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">
                                Eliminar
                            </button>
                        </form>
                    </div>

                </div>
            @empty
                <div class="text-center text-gray-500 dark:text-gray-400 py-10">
                    No hay ningún semestre registrado.
                </div>
            @endforelse
        </div>

    </div>
</x-admin-layout>
