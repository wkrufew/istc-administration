<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- ═══════════════════════════════════════
         BLOQUE 1 — HEADER FUSIONADO
         Título + Buscador
    ═══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">

        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
        <div
            class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50">
        </div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">

            {{-- Ícono + Título --}}
            <div class="flex items-center gap-3 flex-shrink-0">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Listado de Estudiantes</h1>
                    <p class="text-xs text-lime-400/70 tracking-widest uppercase mt-1">Administración · Usuarios</p>
                </div>
            </div>

            {{-- Buscador --}}
            <div class="relative flex-1 min-w-[180px] max-w-xs">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </div>
                <input wire:model.live="search"
                    class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-slate-700 dark:text-white/80 placeholder-slate-400 dark:placeholder-slate-500
                           bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08]
                           focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10
                           transition-all duration-200"
                    placeholder="Buscar por nombre o correo…">
            </div>

        </div>
    </div>


    {{-- ═══════════════════════════════════════
         BLOQUE 1.5 — MÉTRICAS
    ═══════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/[0.06] rounded-xl px-5 py-4 flex items-center gap-4 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04] shadow-lg shadow-slate-200 dark:shadow-black/20">
            <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
            <div>
                <p class="text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500 mb-0.5">Total Estudiantes</p>
                <p class="text-2xl font-semibold text-slate-800 dark:text-white/90 leading-none">{{ $users->total() }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/[0.06] rounded-xl px-5 py-4 flex items-center gap-4 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04] shadow-lg shadow-slate-200 dark:shadow-black/20">
            <div class="w-10 h-10 rounded-xl bg-lime-500/10 border border-lime-500/20 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-lime-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500 mb-0.5">Activos</p>
                <p class="text-2xl font-semibold text-lime-400 leading-none">{{ $totalActivos }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/[0.06] rounded-xl px-5 py-4 flex items-center gap-4 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04] shadow-lg shadow-slate-200 dark:shadow-black/20">
            <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" /><path d="M15 9l-6 6" /><path d="M9 9l6 6" />
                </svg>
            </div>
            <div>
                <p class="text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500 mb-0.5">Inactivos</p>
                <p class="text-2xl font-semibold text-red-400 leading-none">{{ $totalInactivos }}</p>
            </div>
        </div>

    </div>


    {{-- ═══════════════════════════════════════
         BLOQUE 2 — TABLA
    ═══════════════════════════════════════ --}}
    <div
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-white/[0.06] overflow-hidden shadow-xl shadow-slate-200/80 dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04]">

        {{-- Shimmer top --}}
        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

        {{-- Flash --}}
        @if (session('notificacion'))
            <div
                class="mx-6 mt-5 px-4 py-3 rounded-lg bg-lime-500/10 border border-lime-500/20 text-lime-400 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('notificacion') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/[0.05]">
                        <th
                            class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-12">
                            ID
                        </th>
                        <th
                            class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Nombre
                        </th>
                        <th
                            class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Correo
                        </th>
                        <th
                            class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-36">
                            Status
                        </th>
                        <th
                            class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-24">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($users as $estudiante)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">

                            {{-- ID --}}
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-500 font-mono">#{{ $estudiante->id }}</span>
                            </td>

                            {{-- Nombre --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-7 h-7 rounded-full bg-gradient-to-br from-green-700/60 to-sky-700/60 border border-white/[0.08] flex items-center justify-center flex-shrink-0">
                                        <span class="text-[0.6rem] font-semibold text-white/70 uppercase">
                                            {{ substr($estudiante->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-slate-700 dark:text-white/75 font-medium leading-tight">{{ $estudiante->name }}</span>
                                        @if($estudiante->cedula)
                                            <p class="text-[0.65rem] text-slate-400 dark:text-slate-500 font-mono mt-0.5">C.I.: {{ $estudiante->cedula }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Correo --}}
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $estudiante->email }}</span>
                                <p class="text-[0.65rem] text-slate-400 dark:text-slate-500 mt-0.5">Registrado: {{ $estudiante->created_at->format('d M Y') }}</p>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1.5">
                                    @if ($estudiante->is_active)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-lime-500/10 border border-lime-500/20 text-lime-400 w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-lime-400 animate-pulse"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-red-500/10 border border-red-500/20 text-red-400 w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                            Inactivo
                                        </span>
                                    @endif

                                    {{-- Toggle --}}
                                    <label class="relative inline-flex cursor-pointer items-center">
                                        <input type="checkbox" class="peer sr-only" id="switch-{{ $estudiante->id }}"
                                            @if ($estudiante->is_active) checked @endif
                                            wire:click="toggleStatus({{ $estudiante }})">
                                        <div
                                            class="peer h-5 w-9 rounded-full bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-white/[0.06]
                                                    after:absolute after:left-[2px] after:top-[2px]
                                                    after:h-4 after:w-4 after:rounded-full
                                                    after:bg-white dark:after:bg-slate-400 after:transition-all after:content-['']
                                                    peer-checked:bg-lime-600/70 peer-checked:border-lime-500/30
                                                    peer-checked:after:translate-x-full peer-checked:after:bg-white
                                                    peer-focus:ring-2 peer-focus:ring-lime-500/20">
                                        </div>
                                    </label>
                                </div>
                            </td>

                            {{-- Opciones --}}
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1.5">

                                @can('moodle_gestion')
                                @if((int) env('MOODLE_MODE', 0) === 1)
                                <a href="{{ route('administracion.administrativa.users.moodle', $estudiante) }}?from=estudiantes"
                                    title="Gestión Moodle"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg transition-all duration-150
                                           {{ $estudiante->moodle_id
                                                ? 'border border-emerald-500/30 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 hover:border-emerald-400/40'
                                                : 'border border-slate-200 dark:border-slate-700/60 bg-slate-100 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 hover:bg-emerald-500/10 hover:border-emerald-500/25 hover:text-emerald-600 dark:hover:text-emerald-400' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                    </svg>
                                </a>
                                @endif
                                @endcan

                                <a href="{{ route('administracion.administrativa.estudiantes.edit', $estudiante) }}?from=estudiantes"
                                    title="Editar Usuario"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                           text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800
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

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                    </svg>
                                    <p class="text-sm">No existe registro que coincida con la búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50 dark:bg-black/10">
                {{ $users->links() }}
            </div>
        @endif

        {{-- Franja de colores bottom --}}
        <div
            class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50">
        </div>

    </div>

</div>

@push('js')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('alert', (eventData) => {
                const data = eventData[0];
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: data.type,
                    title: data.message
                });
            });
        });
    </script>
@endpush
