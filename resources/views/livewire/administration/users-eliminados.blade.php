<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- ═══════════════════════════════════════
         HEADER
    ═══════════════════════════════════════ --}}
    <div class="bg-slate-900 border border-slate-700/50 relative overflow-hidden rounded-xl">

        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-red-500 to-orange-500 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-red-700 via-orange-500 to-amber-500 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">

            {{-- Ícono + Título --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-700 to-orange-700
                            flex items-center justify-center shadow-lg shadow-red-900/40 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                        <path d="M10 11v6M14 11v6" />
                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-white/90 leading-none">Usuarios Eliminados</h1>
                    <p class="text-xs text-red-400/70 tracking-widest uppercase mt-1">Papelera · Restauración</p>
                </div>
            </div>

            {{-- Buscador predictivo --}}
            <div class="relative flex-1 max-w-xs">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search"
                    class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-white/80 placeholder-slate-500
                           bg-slate-800 border border-white/[0.08]
                           focus:outline-none focus:border-red-500/50 focus:ring-2 focus:ring-red-500/10
                           transition-all duration-200"
                    placeholder="Buscar por nombre, cédula o correo…">
                @if($search)
                    <button wire:click="$set('search','')"
                        class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Volver al listado --}}
            <a href="{{ route('administracion.administrativa.users.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium tracking-widest uppercase
                       text-slate-300/80 bg-slate-800 border border-slate-700/50
                       hover:bg-slate-700 hover:text-white hover:-translate-y-0.5
                       active:translate-y-0 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Volver al listado
            </a>

        </div>
    </div>

    {{-- ═══════════════════════════════════════
         TABLA
    ═══════════════════════════════════════ --}}
    <div class="bg-slate-900 rounded-2xl border border-white/[0.06] overflow-hidden shadow-2xl shadow-black/40 ring-1 ring-inset ring-white/[0.04]">

        <div class="h-px bg-gradient-to-r from-transparent via-red-500/30 to-transparent"></div>

        {{-- Contador --}}
        <div class="px-6 pt-4 pb-2">
            <p class="text-xs text-slate-500">
                <span class="text-red-400 font-semibold">{{ $users->total() }}</span>
                {{ $users->total() === 1 ? 'usuario eliminado' : 'usuarios eliminados' }}
                @if($search) · filtrado por "<span class="text-white/60">{{ $search }}</span>" @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/[0.05]">
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400 w-12">ID</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">Nombre</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">Correo</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">Rol(es)</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400 w-36">Eliminado</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400 w-52">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @forelse ($users as $user)
                        <tr class="group hover:bg-white/[0.02] transition-colors duration-150">

                            {{-- ID --}}
                            <td class="px-4 py-3 text-left">
                                <span class="text-xs text-slate-500 font-mono">#{{ $user->id }}</span>
                            </td>

                            {{-- Nombre --}}
                            <td class="px-4 py-3 text-left">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-800/40 to-slate-700/60
                                                border border-white/[0.06] flex items-center justify-center flex-shrink-0">
                                        <span class="text-[0.6rem] font-semibold text-white/50 uppercase">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-white/50 font-medium leading-tight line-through decoration-red-700/50">{{ $user->name }}</span>
                                        @if($user->cedula)
                                            <p class="text-[0.65rem] text-slate-600 font-mono mt-0.5">{{ $user->cedula }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Correo --}}
                            <td class="px-4 py-3 text-left">
                                <span class="text-xs text-slate-500">{{ $user->email }}</span>
                            </td>

                            {{-- Roles --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap gap-1 justify-center">
                                    @forelse ($user->roles as $role)
                                        <span class="px-2 py-0.5 rounded-full text-[0.65rem] font-medium
                                                     bg-slate-700/50 border border-slate-600/30 text-slate-500">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-600">—</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Fecha eliminación --}}
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs text-red-400/70 font-mono">
                                    {{ $user->deleted_at->format('d/m/Y H:i') }}
                                </span>
                            </td>

                            {{-- Acciones --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Restaurar --}}
                                    <button type="button" x-data
                                        @click="Swal.fire({
                                            title: '¿Restaurar usuario?',
                                            html: 'Se habilitará y activará la cuenta de <strong>{{ addslashes($user->name) }}</strong>.',
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonColor: '#16a34a',
                                            cancelButtonColor: '#475569',
                                            confirmButtonText: 'Sí, restaurar',
                                            cancelButtonText: 'Cancelar',
                                            customClass: { popup: 'rounded-xl border border-white/10 shadow-2xl' },
                                            ...swalTheme(),
                                        }).then(r => r.isConfirmed && $wire.restaurar({{ $user->id }}))"
                                        title="Restaurar usuario"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                               text-lime-400 border border-lime-500/20 bg-lime-500/[0.07]
                                               hover:bg-lime-500/20 hover:border-lime-500/40
                                               transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                            <path d="M3 3v5h5" />
                                        </svg>
                                        Restaurar
                                    </button>

                                    {{-- Eliminar definitivamente --}}
                                    <button type="button" x-data
                                        @click="Swal.fire({
                                            title: '¿Eliminar definitivamente?',
                                            html: 'Esta acción es <strong>irreversible</strong>.<br>El usuario <strong>{{ addslashes($user->name) }}</strong> será borrado de forma permanente del sistema.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#dc2626',
                                            cancelButtonColor: '#475569',
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar',
                                            customClass: { popup: 'rounded-xl border border-white/10 shadow-2xl' },
                                            ...swalTheme(),
                                        }).then(r => r.isConfirmed && $wire.forceEliminar({{ $user->id }}))"
                                        title="Eliminar permanentemente"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                               text-red-400 border border-red-500/20 bg-red-500/[0.07]
                                               hover:bg-red-500/20 hover:border-red-500/40
                                               transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18M8 6V4h8v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                            <line x1="10" y1="11" x2="10" y2="17" />
                                            <line x1="14" y1="11" x2="14" y2="17" />
                                        </svg>
                                        Definitivo
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                    </svg>
                                    <p class="text-sm">
                                        @if($search)
                                            No se encontraron usuarios eliminados con ese criterio.
                                        @else
                                            La papelera está vacía.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-white/[0.05] bg-black/10">
                {{ $users->links() }}
            </div>
        @endif

        <div class="h-0.5 bg-gradient-to-r from-red-700 via-orange-500 to-amber-500 opacity-50"></div>

    </div>

</div>
