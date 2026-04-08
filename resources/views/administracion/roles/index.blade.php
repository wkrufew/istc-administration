<x-admin-layout>

    <div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

        {{-- ═══════════════════════════════════════
             BLOQUE 1 — HEADER (slate-900)
        ═══════════════════════════════════════ --}}
        <div class="bg-slate-900 border border-slate-700/50 relative overflow-hidden rounded-xl">

            {{-- Borde izquierdo acento --}}
            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>

            {{-- Línea de colores inferior --}}
            <div
                class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50">
            </div>

            <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">

                {{-- Ícono + Título --}}
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-white/90 leading-none">Roles</h1>
                        <p class="text-xs text-lime-400/70 tracking-widest uppercase mt-1">Administración · Roles</p>
                    </div>
                </div>

                {{-- Botón crear --}}
                <a href="{{ route('administracion.administrativa.roles.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full
                           bg-gradient-to-r from-green-800/70 via-sky-800/60 to-purple-900/55
                           border border-lime-500/25 text-white/90 text-xs font-medium tracking-widest uppercase
                           hover:from-green-700/80 hover:via-sky-700/70 hover:to-purple-800/65
                           hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-900/30
                           active:translate-y-0 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    Crear Rol
                </a>

            </div>
        </div>


        {{-- ═══════════════════════════════════════
             BLOQUE 2 — TABLA (slate-900)
        ═══════════════════════════════════════ --}}
        <div
            class="bg-slate-900 rounded-2xl border border-white/[0.06] overflow-hidden shadow-2xl shadow-black/40 ring-1 ring-inset ring-white/[0.04]">

            {{-- Shimmer top --}}
            <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

            {{-- Flash message --}}
            @if (session('menssage'))
                <div
                    class="mx-6 mt-5 px-4 py-3 rounded-lg bg-lime-500/10 border border-lime-500/20 text-lime-400 text-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('menssage') }}
                </div>
            @endif

            {{-- Tabla --}}
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/[0.05]">
                        <th
                            class="px-6 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">
                            Nombre
                        </th>
                        <th
                            class="px-6 py-3.5 text-right text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @forelse ($roles as $role)
                        <tr class="group hover:bg-white/[0.02] transition-colors duration-150">

                            {{-- Nombre --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-slate-800 border border-white/[0.06] flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-lime-400/60"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-white/75 font-medium">{{ $role->name }}</span>
                                </div>
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- Editar --}}
                                    <a href="{{ route('administracion.administrativa.roles.edit', $role) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                               text-slate-400 border border-white/[0.06] bg-slate-800
                                               hover:text-lime-400 hover:border-lime-500/30 hover:bg-lime-500/[0.06]
                                               transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        Editar
                                    </a>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('administracion.administrativa.roles.destroy', $role) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Eliminar el rol {{ $role->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                                   text-slate-400 border border-white/[0.06] bg-slate-800
                                                   hover:text-red-400 hover:border-red-500/30 hover:bg-red-500/[0.06]
                                                   transition-all duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                                <path d="M10 11v6" />
                                                <path d="M14 11v6" />
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                    <p class="text-sm">No hay roles creados aún.</p>
                                    <a href="{{ route('administracion.administrativa.roles.create') }}"
                                        class="text-xs text-lime-400/70 hover:text-lime-400 transition-colors">
                                        Crear el primer rol →
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginación --}}
            @if ($roles->hasPages())
                <div class="px-6 py-4 border-t border-white/[0.05] bg-black/10">
                    {{ $roles->links() }}
                </div>
            @endif

            {{-- Franja de colores bottom --}}
            <div
                class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50">
            </div>

        </div>

    </div>

</x-admin-layout>
{{-- <x-admin-layout>
    <div class="p-4 rounded-lg bg-gray-100 dark:bg-gray-800 shadow-md">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Roles</h1>
            <a href="{{ route('administracion.administrativa.roles.create') }}"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">Crear
                Rol</a>
        </div>

        @if (session('menssage'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mt-5" role="alert">
                <p>{{ session('menssage') }}</p>
            </div>
        @endif
        <div class="mt-4">
            <table class="w-full table-fixed overflow-hidden rounded-lg bg-white dark:bg-gray-900 shadow-md">
                <thead>
                    <tr class="dark:bg-gray-600 dark:text-gray-50 overflow-hidden rounded-t-lg text-black bg-gray-300">
                        <th
                            class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold uppercase tracking-wider">
                            Nombre</th>
                        <th
                            class="px-6 py-3 border-b-2 border-gray-300 text-right text-xs leading-4 font-semibold uppercase tracking-wider">
                            Opciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($roles as $role)
                        <tr class="text-gray-900 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <div class="text-sm leading-5 font-medium">{{ $role->name }}</div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium flex justify-end space-x-2">
                                <a href="{{ route('administracion.administrativa.roles.edit', $role) }}"
                                    class="text-indigo-600 hover:text-indigo-900 inline-block">
                                    <svg class="w-5 h-5 fill-green-600" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 512 512">
                                        <path
                                            d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" />
                                    </svg>
                                </a>
                                <div class="inline-block">
                                    <form action="{{ route('administracion.administrativa.roles.destroy', $role) }}"
                                        method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5 fill-red-600" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 448 512">
                                                <path
                                                    d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm96 64c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($roles->hasPages())
            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</x-admin-layout> --}}
