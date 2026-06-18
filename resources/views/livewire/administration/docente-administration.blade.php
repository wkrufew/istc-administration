<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- ═══════════════════════════════════════
         BLOQUE 1 — HEADER FUSIONADO
         Título + Buscador + Botones
    ═══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">

        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
        <div
            class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50">
        </div>

        <div class="px-6 py-4 flex items-center gap-4 flex-wrap">

            {{-- Ícono + Título --}}
            <div class="flex items-center gap-3 flex-shrink-0">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path d="M12 14v7" />
                        <path d="M12 14l6.16-3.422A12.083 12.083 0 0 1 18 9.002" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Gestión de Docentes</h1>
                    <p class="text-xs text-lime-400/70 tracking-widest uppercase mt-1">Administración · Personal
                        Académico</p>
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
                    placeholder="Buscar docente…">
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-2 flex-shrink-0 ml-auto">

                <a href="{{ route('administracion.administrativa.documentacion-personal.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium tracking-wide
                           text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5
                           hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-800 dark:hover:text-white transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="12" y1="18" x2="12" y2="12" />
                        <line x1="9" y1="15" x2="15" y2="15" />
                    </svg>
                    Documentación
                </a>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════
         BLOQUE 2 — MÉTRICAS
    ═══════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div
            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/[0.06] rounded-xl px-5 py-4 flex items-center gap-4 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04] shadow-lg shadow-slate-200 dark:shadow-black/20">
            <div
                class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
            <div>
                <p class="text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500 mb-0.5">Total Docentes
                </p>
                <p class="text-2xl font-semibold text-slate-800 dark:text-white/90 leading-none">{{ $totalDocentes }}</p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/[0.06] rounded-xl px-5 py-4 flex items-center gap-4 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04] shadow-lg shadow-slate-200 dark:shadow-black/20">
            <div
                class="w-10 h-10 rounded-xl bg-lime-500/10 border border-lime-500/20 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-lime-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500 mb-0.5">Activos</p>
                <p class="text-2xl font-semibold text-lime-400 leading-none">
                    {{ $totalActivos }}</p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/[0.06] rounded-xl px-5 py-4 flex items-center gap-4 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04] shadow-lg shadow-slate-200 dark:shadow-black/20">
            <div
                class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M15 9l-6 6" />
                    <path d="M9 9l6 6" />
                </svg>
            </div>
            <div>
                <p class="text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500 mb-0.5">Inactivos</p>
                <p class="text-2xl font-semibold text-red-400 leading-none">
                    {{ $totalInactivos }}</p>
            </div>
        </div>

    </div>


    {{-- ═══════════════════════════════════════
         BLOQUE 3 — TABLA
    ═══════════════════════════════════════ --}}
    <div
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-white/[0.06] overflow-hidden shadow-xl shadow-slate-200/80 dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04]">

        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>



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
                            class="px-5 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Docente</th>
                        <th
                            class="px-5 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Contacto</th>
                        <th
                            class="px-5 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-32">
                            Estado</th>
                        <th
                            class="px-5 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-48">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($users as $user)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">

                            {{-- Docente --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-gradient-to-br from-green-700/70 to-sky-700/70 border border-white/[0.08] flex items-center justify-center flex-shrink-0 shadow-md">
                                        <span class="text-xs font-semibold text-white/80 uppercase">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-white/80">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">C.I: {{ $user->cedula }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Contacto --}}
                            <td class="px-5 py-3.5">
                                <p class="text-xs text-slate-600 dark:text-slate-300">{{ $user->email }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Registrado:
                                    {{ $user->created_at->format('d M Y') }}</p>
                            </td>

                            {{-- Estado --}}
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    @if ($user->is_active)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-lime-500/10 border border-lime-500/20 text-lime-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-lime-400 animate-pulse"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-red-500/10 border border-red-500/20 text-red-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                    <label class="relative inline-flex cursor-pointer items-center">
                                        <input type="checkbox" class="peer sr-only"
                                            @if ($user->is_active) checked @endif
                                            wire:click="toggleStatus({{ $user->id }})">
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

                            {{-- Acciones --}}
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">

                                    <a title="Editar datos"
                                        href="{{ route('administracion.administrativa.estudiantes.edit', $user) }}?from=docentes"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.7rem] font-medium
                                               text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800
                                               hover:text-lime-400 hover:border-lime-500/30 hover:bg-lime-500/[0.06]
                                               transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        Editar
                                    </a>

                                    <a title="Documentos del docente"
                                        href="{{ route('administracion.administrativa.docentes.documentos', $user) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.7rem] font-medium
                                               text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800
                                               hover:text-sky-400 hover:border-sky-500/30 hover:bg-sky-500/[0.06]
                                               transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="12" y1="18" x2="12" y2="12" />
                                            <line x1="9" y1="15" x2="15" y2="15" />
                                        </svg>
                                        Docs
                                    </a>

                                    <a title="Asignación de materias"
                                        href="{{ route('administracion.administrativa.docentes.asignar.form', $user) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.7rem] font-medium
                                               text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800
                                               hover:text-purple-400 hover:border-purple-500/30 hover:bg-purple-500/[0.06]
                                               transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                        </svg>
                                        Materias
                                    </a>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path d="M12 14v7" />
                                    </svg>
                                    <p class="text-sm">No se encontraron docentes.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50 dark:bg-black/10">
                {{ $users->links() }}
            </div>
        @endif

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
{{-- <div>
    @if (session('notificacion'))
        <div class="alert alert-success" role="alert">
            <strong>Exito!</strong>{{ session('notificacion') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="p-6 space-y-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Gestión de Docentes
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Administración del personal académico
                </p>
            </div>

            <div class="relative w-full lg:w-80">
                <input wire:model.live="search" type="text" placeholder="Buscar docente..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 
                          bg-white dark:bg-gray-900 
                          text-gray-800 dark:text-gray-200
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          shadow-sm">

                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 
                    rounded-2xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Docentes</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $totalDocentes }}
                </p>
            </div>

            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800
                    rounded-2xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Activos</p>
                <p class="text-3xl font-bold text-emerald-600">
                    {{ $totalActivos }}
                </p>
            </div>

            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800
                    rounded-2xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Inactivos</p>
                <p class="text-3xl font-bold text-red-600">
                    {{ $totalInactivos }}
                </p>
            </div>

        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">

                    <thead
                        class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left">Docente</th>
                            <th class="px-6 py-4 text-left">Contacto</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">

                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">

                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-4">

                                        <div
                                            class="h-11 w-11 rounded-full bg-blue-600 
                                                text-white flex items-center 
                                                justify-center font-semibold shadow-md">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>

                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                C.I: {{ $user->cedula }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-2">
                                    <p class="text-gray-700 dark:text-gray-300">
                                        {{ $user->email }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Registrado: {{ $user->created_at->format('d M Y') }}
                                    </p>
                                </td>

                                <td class="px-4 py-2 text-center">

                                    @if ($user->is_active)
                                        <span
                                            class="px-3 py-1 text-xs font-semibold 
                                                 rounded-full bg-emerald-500/10 text-emerald-600">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs font-semibold 
                                                 rounded-full bg-red-500/10 text-red-600">
                                            Inactivo
                                        </span>
                                    @endif

                                    <div class="mt-2">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer"
                                                @if ($user->is_active) checked @endif
                                                wire:click="toggleStatus({{ $user->id }})">

                                            <div
                                                class="w-11 h-6 bg-gray-300 rounded-full peer 
                                                    peer-checked:bg-emerald-600
                                                    relative transition">

                                                <div
                                                    class="absolute left-1 top-1 
                                                        w-4 h-4 bg-white rounded-full
                                                        transition peer-checked:translate-x-5">
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                </td>

                                <td class="px-4 py-2 text-center">
                                    <div class="flex justify-center gap-3">

                                        <a title="Editar datos"
                                            href="{{ route('administracion.administrativa.estudiantes.edit', $user) }}?from=docentes"
                                            class="p-2 rounded-lg bg-blue-500/10 text-blue-600 
                                              hover:bg-blue-500 hover:text-white 
                                              transition shadow-sm">
                                            ✏
                                        </a>

                                        <a title="Documentos del docente"
                                            href="{{ route('administracion.administrativa.docentes.documentos', $user) }}"
                                            class="p-2 rounded-lg bg-indigo-500/10 text-indigo-600
                                              hover:bg-indigo-500 hover:text-white
                                              transition shadow-sm">
                                            📄
                                        </a>

                                        <a title="Asignacion de materias"
                                            href="{{ route('administracion.administrativa.docentes.asignar.form', $user) }}"
                                            class="p-2 rounded-lg bg-violet-500/10 text-violet-600
                                              hover:bg-violet-500 hover:text-white
                                              transition shadow-sm">
                                            📚
                                        </a>

                                        @can('moodle_gestion')
                                        @if((int) env('MOODLE_MODE', 0) === 1)
                                        <a title="Gestión Moodle"
                                            href="{{ route('administracion.administrativa.users.moodle', $user) }}?from=docentes"
                                            class="p-2 rounded-lg {{ $user->moodle_id ? 'bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500' : 'bg-slate-500/10 text-slate-400 hover:bg-slate-500' }}
                                              hover:text-white transition shadow-sm">
                                            🎓
                                        </a>
                                        @endif
                                        @endcan

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                    No se encontraron docentes
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="mt-4"> {{ $users->links() }} </div>
            @endif
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
                        title: data.message,
                    });
                });
            });
        </script>
    @endpush
</div>
 --}}
