<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- ═══════════════════════════════════════
         BLOQUE 1 — HEADER
    ═══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">

        {{-- Borde izquierdo acento --}}
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>

        {{-- Línea de colores inferior --}}
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">

            {{-- Ícono + Título --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Listo general de usuarios</h1>
                    <p class="text-xs text-lime-600 dark:text-lime-400/70 tracking-widest uppercase mt-1">Administración · Usuarios</p>
                </div>
            </div>

            {{-- Buscador --}}
            <div class="relative flex-1 max-w-xs">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </div>
                <input wire:model.live="search"
                    class="w-full pl-9 pr-4 py-2 rounded-full text-xs
                           text-slate-700 dark:text-white/80
                           placeholder-slate-400 dark:placeholder-slate-500
                           bg-slate-100 dark:bg-slate-800
                           border border-slate-200 dark:border-white/[0.08]
                           focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10
                           transition-all duration-200"
                    placeholder="Buscar por nombres, cedula o correo…">
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('administracion.administrativa.estudiantes.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium tracking-widest uppercase
                           text-white/90
                           bg-gradient-to-r from-green-800/70 via-sky-800/60 to-purple-900/55
                           border border-lime-500/25
                           hover:from-green-700/80 hover:via-sky-700/70 hover:to-purple-800/65
                           hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-900/30
                           active:translate-y-0 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    Crear Usuario
                </a>

                <a href="{{ route('administracion.administrativa.estudiantes.import') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium tracking-widest uppercase
                           text-amber-700 dark:text-amber-300/90
                           bg-amber-500/10 border border-amber-500/25
                           hover:bg-amber-500/20 hover:border-amber-400/40 hover:text-amber-600 dark:hover:text-amber-200
                           hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-900/20
                           active:translate-y-0 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                    Importar Usuarios
                </a>

                <a href="{{ route('administracion.administrativa.users.eliminados') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium tracking-widest uppercase
                           text-red-600 dark:text-red-300/80
                           bg-red-500/10 border border-red-500/20
                           hover:bg-red-500/20 hover:border-red-400/40 hover:text-red-700 dark:hover:text-red-200
                           hover:-translate-y-0.5 hover:shadow-lg hover:shadow-red-900/20
                           active:translate-y-0 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                        <path d="M10 11v6M14 11v6" />
                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                    </svg>
                    Usuarios Eliminados
                </a>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════
         BLOQUE 1.5 — FILTROS
    ═══════════════════════════════════════ --}}
    <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/[0.06] rounded-xl px-5 py-3.5 flex flex-wrap items-center gap-3">

        {{-- Filtro Rol --}}
        <div class="flex items-center gap-2">
            <span class="text-[0.65rem] font-medium tracking-widest uppercase text-slate-500">Rol</span>
            <select wire:model.live="filtroRol"
                    class="text-xs text-slate-700 dark:text-white/70
                           bg-white dark:bg-slate-800
                           border border-slate-200 dark:border-white/[0.08]
                           rounded-lg px-3 py-1.5
                           focus:outline-none focus:border-lime-500/40 focus:ring-1 focus:ring-lime-500/20 transition-all">
                <option value="">Todos</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Separador --}}
        <div class="w-px h-5 bg-slate-200 dark:bg-white/[0.07]"></div>

        {{-- Filtro Estado --}}
        <div class="flex items-center gap-2">
            <span class="text-[0.65rem] font-medium tracking-widest uppercase text-slate-500">Estado</span>
            <div class="flex items-center gap-1">
                <button wire:click="$set('filtroEstado', '')"
                        class="px-3 py-1 rounded-full text-[0.65rem] font-medium transition-all
                               {{ $filtroEstado === '' ? 'bg-slate-600 text-white border border-slate-500' : 'text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] hover:border-slate-400 dark:hover:border-white/20' }}">
                    Todos
                </button>
                <button wire:click="$set('filtroEstado', '1')"
                        class="px-3 py-1 rounded-full text-[0.65rem] font-medium transition-all
                               {{ $filtroEstado === '1' ? 'bg-lime-600/80 text-white border border-lime-500/50' : 'text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] hover:border-lime-500/30 hover:text-lime-600 dark:hover:text-lime-400' }}">
                    Activos
                </button>
                <button wire:click="$set('filtroEstado', '0')"
                        class="px-3 py-1 rounded-full text-[0.65rem] font-medium transition-all
                               {{ $filtroEstado === '0' ? 'bg-red-600/70 text-white border border-red-500/50' : 'text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] hover:border-red-500/30 hover:text-red-600 dark:hover:text-red-400' }}">
                    Inactivos
                </button>
            </div>
        </div>

        {{-- Limpiar filtros --}}
        @if($filtroRol !== '' || $filtroEstado !== '')
        <div class="flex items-center gap-2 ml-auto">
            <button wire:click="limpiarFiltros"
                    class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.65rem] font-medium
                           text-slate-500 dark:text-slate-400
                           border border-slate-200 dark:border-white/[0.06]
                           hover:text-slate-800 dark:hover:text-white
                           hover:border-slate-400 dark:hover:border-white/20
                           transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar filtros
            </button>
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════
         BLOQUE 2 — TABLA
    ═══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-white/[0.06] overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-900/[0.04] dark:ring-white/[0.04]">

        {{-- Shimmer top --}}
        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>


        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/[0.05]">
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-12">
                            ID
                        </th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Nombre
                        </th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Correo
                        </th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Rol(es)
                        </th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-36">
                            Status
                        </th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-40">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($users as $user)
                        @php
                            $cumple        = null;
                            $diasFaltan    = null;
                            $esCumple      = false;
                            $proximoCumple = false;

                            if ($user->fecha_nacimiento) {
                                $cumple = \Carbon\Carbon::parse($user->fecha_nacimiento)
                                    ->setYear(now()->year)
                                    ->startOfDay();
                                if ($cumple->isPast() && ! $cumple->isToday()) {
                                    $cumple->addYear();
                                }
                                $diasFaltan    = (int) now()->startOfDay()->diffInDays($cumple, false);
                                $esCumple      = $diasFaltan === 0;
                                $proximoCumple = $diasFaltan >= 1 && $diasFaltan <= 5;
                            }
                        @endphp
                        <tr class="group transition-colors duration-150
                                   {{ $esCumple
                                       ? 'bg-fuchsia-50/60 dark:bg-fuchsia-950/20 hover:bg-fuchsia-50 dark:hover:bg-fuchsia-950/30'
                                       : 'hover:bg-slate-50 dark:hover:bg-white/[0.02]' }}">

                            {{-- ID --}}
                            <td class="px-4 py-3 text-left">
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-mono">#{{ $user->id }}</span>
                            </td>

                            {{-- Nombre --}}
                            <td class="px-4 py-3 text-left">
                                <div class="flex items-center gap-2.5">
                                    <div class="relative w-7 h-7 rounded-full flex-shrink-0">
                                        @if($esCumple)
                                            <span class="absolute inset-0 rounded-full animate-ping bg-fuchsia-400 opacity-30"></span>
                                        @endif
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br
                                                    {{ $esCumple ? 'from-fuchsia-500 to-pink-500' : 'from-green-700/60 to-sky-700/60' }}
                                                    {{ $esCumple ? 'ring-2 ring-fuchsia-400 ring-offset-1 ring-offset-white dark:ring-offset-slate-900' : 'border border-slate-200 dark:border-white/[0.08]' }}
                                                    flex items-center justify-center">
                                            <span class="text-[0.6rem] font-semibold text-white uppercase">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-sm text-slate-800 dark:text-white/75 font-medium leading-tight">{{ $user->name }}</span>
                                        @if($user->cedula)
                                            <p class="text-[0.65rem] text-slate-400 dark:text-slate-500 font-mono mt-0.5">{{ $user->cedula }}</p>
                                        @endif
                                        @if($user->moodle_id && (int) env('MOODLE_MODE', 0) === 1)
                                            <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[0.6rem] font-bold
                                                         bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-mono">
                                                <span class="w-1 h-1 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                                M #{{ $user->moodle_id }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Correo --}}
                            <td class="px-4 py-3 text-left">
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</span>

                                @if($esCumple)
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-semibold
                                                     bg-fuchsia-500/15 border border-fuchsia-400/40 text-fuchsia-600 dark:text-fuchsia-300
                                                     animate-pulse">
                                            🎂 ¡Hoy es su cumpleaños!
                                        </span>
                                    </div>
                                @elseif($proximoCumple)
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium
                                                     bg-amber-500/10 border border-amber-400/30 text-amber-600 dark:text-amber-400">
                                            🎂 En {{ $diasFaltan }} {{ $diasFaltan === 1 ? 'día' : 'días' }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- Roles --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap gap-1 justify-center">
                                    @forelse ($user->roles as $role)
                                        <span class="px-2 py-0.5 rounded-full text-[0.65rem] font-medium
                                                     bg-sky-500/10 border border-sky-500/20 text-sky-600 dark:text-sky-400">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 dark:text-slate-600">—</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 text-left">
                                <div class="flex flex-col gap-1.5">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-lime-500/10 border border-lime-500/20 text-lime-600 dark:text-lime-400 w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-lime-500 dark:bg-lime-400 animate-pulse"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                            Inactivo
                                        </span>
                                    @endif

                                    {{-- Toggle --}}
                                    <label class="relative inline-flex cursor-pointer items-center">
                                        <input type="checkbox" class="peer sr-only" id="switch-{{ $user->id }}"
                                            @if ($user->is_active) checked @endif
                                            wire:click="toggleStatus({{ $user }})">
                                        <div class="peer h-5 w-9 rounded-full
                                                    bg-slate-300 dark:bg-slate-700
                                                    border border-slate-300 dark:border-white/[0.06]
                                                    after:absolute after:left-[2px] after:top-[2px]
                                                    after:h-4 after:w-4 after:rounded-full
                                                    after:bg-white after:transition-all after:content-['']
                                                    peer-checked:bg-lime-600/70 peer-checked:border-lime-500/30
                                                    peer-checked:after:translate-x-full peer-checked:after:bg-white
                                                    peer-focus:ring-2 peer-focus:ring-lime-500/20">
                                        </div>
                                    </label>
                                </div>
                            </td>

                            {{-- Opciones --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">

                                    {{-- Moodle --}}
                                    @if((int) env('MOODLE_MODE', 0) === 1)
                                    <a href="{{ route('administracion.administrativa.users.moodle', $user) }}"
                                        title="Gestión Moodle"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg transition-all duration-150
                                               {{ $user->moodle_id
                                                    ? 'border border-emerald-500/30 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 hover:border-emerald-400/40'
                                                    : 'border border-slate-200 dark:border-slate-700/60 bg-slate-100 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 hover:bg-emerald-500/10 hover:border-emerald-500/25 hover:text-emerald-600 dark:hover:text-emerald-400' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                        </svg>
                                    </a>
                                    @endif

                                    {{-- Editar perfil completo --}}
                                    <a href="{{ route('administracion.administrativa.estudiantes.edit', $user) }}?from=users"
                                        title="Editar usuario"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg
                                               border border-sky-500/20 bg-sky-500/10
                                               hover:bg-sky-500/20 hover:border-sky-400/40 text-sky-600 dark:text-sky-400
                                               transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>

                                    {{-- Asignar rol --}}
                                    <a href="{{ route('administracion.administrativa.users.edit', $user) }}"
                                        title="Asignar rol"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg
                                               border border-slate-200 dark:border-white/[0.06]
                                               bg-slate-100 dark:bg-slate-800
                                               hover:text-lime-600 dark:hover:text-lime-400
                                               hover:border-lime-500/30 hover:bg-lime-500/[0.06]
                                               text-slate-500 dark:text-slate-400
                                               transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        </svg>
                                    </a>

                                    {{-- Eliminar (softdelete + desactivar) --}}
                                    <button type="button" x-data
                                        @click="Swal.fire({
                                            title: '¿Eliminar usuario?',
                                            html: 'Se desactivará y moverá a la papelera a <strong>{{ addslashes($user->name) }}</strong>.<br>Podrás restaurarlo desde <em>Usuarios Eliminados</em>.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#dc2626',
                                            cancelButtonColor: '#475569',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar',
                                            customClass: { popup: 'rounded-xl border border-white/10 shadow-2xl' },
                                            ...swalTheme(),
                                        }).then(r => r.isConfirmed && $wire.eliminar({{ $user->id }}))"
                                        title="Eliminar usuario"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg
                                               border border-red-200 dark:border-red-900/40
                                               bg-red-50 dark:bg-red-950/30
                                               hover:bg-red-100 dark:hover:bg-red-950/60
                                               hover:border-red-300 dark:hover:border-red-700/50
                                               text-red-500
                                               transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                            <path d="M10 11v6M14 11v6" />
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                        </svg>
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400 dark:text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                    </svg>
                                    <p class="text-sm">No se encontraron usuarios.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.05] bg-slate-50/80 dark:bg-black/10">
                {{ $users->links() }}
            </div>
        @endif

        {{-- Franja de colores bottom --}}
        <div class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50"></div>

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
                    },
                    ...swalTheme(),
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

    <div class="p-4">
        <div class="card-header">
            <input wire:model.live="search"
                class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200 text-gray-800 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50{{ $errors->has('name') ? ' border-red-500' : '' }}"
                placeholder="Buscar por el nombre o correo">
        </div>
        <div class="mt-4">
            <table class="w-full table-fixed overflow-hidden rounded-lg bg-white dark:bg-gray-900 shadow-md">
                <thead>
                    <tr class="dark:bg-gray-600 dark:text-gray-50 overflow-hidden rounded-t-lg text-black bg-gray-300">
                        <th
                            class="w-2 px-6 py-3 border-b-2 border-gray-300 tsext-left text-xs leading-4 font-semibold uppercase tracking-wider">
                            ID</th>
                        <th
                            class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold uppercase tracking-wider">
                            Nombre</th>
                        <th
                            class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold uppercase tracking-wider">
                            Correo</th>
                        <th
                            class="px-6 py-3 border-b-2 border-gray-300 text-center text-xs leading-4 font-semibold uppercase tracking-wider">
                            Rol(es)</th>
                        <th
                            class="w-32 px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-semibold uppercase tracking-wider">
                            Status</th>
                        <th
                            class="w-24 px-6 py-3 border-b-2 border-gray-300 text-center text-xs leading-4 font-semibold uppercase tracking-wider">
                            Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="w-3 px-6 py-1 text-left border-b border-gray-300 text-sm leading-5 font-medium">
                                {{ $user->id }}</td>
                            <td
                                class="px-6 py-1 whitespace-no-wrap text-left border-b border-gray-300 text-sm leading-5 font-medium">
                                {{ $user->name }}</td>
                            <td
                                class="px-6 py-1 whitespace-no-wrap text-left border-b border-gray-300 text-sm leading-5 font-medium">
                                {{ $user->email }}</td>
                            <td
                                class="px-6 py-1 whitespace-no-wrap text-center border-b border-gray-300 text-sm leading-5 font-medium">
                                @foreach ($user->roles as $role)
                                    <span
                                        class="inline-block px-3 py-1 text-sm font-semibold text-blue-700 bg-blue-200 rounded-full">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td
                                class="px-6 py-1 whitespace-no-wrap text-left border-b border-gray-300 text-sm leading-5 font-medium">

                                <div>
                                    @if ($user->is_active)
                                        <span
                                            class="px-2  text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                    @else
                                        <span
                                            class="px-2  text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                                    @endif
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center mt-1">
                                    <input id="switch" type="checkbox" class="peer sr-only"
                                        id="switch-{{ $user->id }}" @if ($user->is_active) checked @endif
                                        wire:click="toggleStatus({{ $user }})" />
                                    <label for="switch" class="hidden"></label>
                                    <div
                                        class="peer h-6 w-11 rounded-full border bg-slate-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-green-300">
                                    </div>
                                </label>
                            </td>

                            <td
                                class="px-6 py-1 whitespace-no-wrap text-center border-b border-gray-300 text-sm leading-5 font-medium">
                                <a title="Editar Usuario" class="inline-block"
                                    href="{{ route('administracion.administrativa.users.edit', $user) }}">
                                    <svg class="w-5 h-5 fill-green-500" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 512 512">
                                        <path
                                            d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" />
                                    </svg>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No existe registro que coincida con el parametro de busqueda</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
        @if ($users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
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
                        },
                        ...swalTheme(),
                    });
                    Toast.fire({
                        icon: data.type,
                        title: data.message,
                    });
                });
            });
        </script>
    @endpush

</div> --}}
