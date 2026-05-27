<x-admin-layout>

    <div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

        {{-- ═══════════════════════════════════════
             HEADER
        ═══════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">

            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
            <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50"></div>

            <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Roles</h1>
                        <p class="text-xs text-lime-600 dark:text-lime-400/70 tracking-widest uppercase mt-1">Administración · Roles y Permisos</p>
                    </div>
                </div>

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
             TABLA
        ═══════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-white/[0.06] overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-900/[0.04] dark:ring-white/[0.04]">

            <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

            {{-- Flash éxito --}}
            @if (session('menssage'))
                <div class="mx-6 mt-5 px-4 py-3 rounded-lg bg-lime-500/10 border border-lime-500/20 text-lime-600 dark:text-lime-400 text-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('menssage') }}
                </div>
            @endif

            {{-- Flash error rol en uso --}}
            @if (session('error_rol'))
                <div class="mx-6 mt-5 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ session('error_rol') }}
                </div>
            @endif

            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/[0.05]">
                        <th class="px-6 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Nombre
                        </th>
                        <th class="px-6 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-32">
                            Usuarios
                        </th>
                        <th class="px-6 py-3.5 text-right text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-44">
                            Opciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($roles as $role)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">

                            {{-- Nombre --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.06] flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-lime-600 dark:text-lime-400/60"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-slate-800 dark:text-white/75 font-medium">{{ $role->name }}</span>
                                </div>
                            </td>

                            {{-- Contador usuarios --}}
                            <td class="px-6 py-4 text-center">
                                @if ($role->users_count > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.65rem] font-medium
                                                 bg-sky-500/10 border border-sky-500/20 text-sky-600 dark:text-sky-400">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                        {{ $role->users_count }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-600">—</span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- Editar --}}
                                    <a href="{{ route('administracion.administrativa.roles.edit', $role) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                               text-slate-500 dark:text-slate-400
                                               border border-slate-200 dark:border-white/[0.06]
                                               bg-slate-100 dark:bg-slate-800
                                               hover:text-lime-600 dark:hover:text-lime-400
                                               hover:border-lime-500/30 hover:bg-lime-500/[0.06]
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
                                    <button type="button" x-data
                                        @click="
                                            @if($role->users_count > 0)
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Rol en uso',
                                                    html: 'El rol <strong>{{ addslashes($role->name) }}</strong> está asignado a <strong>{{ $role->users_count }}</strong> usuario(s) y no puede eliminarse.',
                                                    confirmButtonText: 'Entendido',
                                                    confirmButtonColor: '#6366f1',
                                                    customClass: { popup: 'rounded-xl border border-white/10 shadow-2xl' },
                                                    ...swalTheme(),
                                                })
                                            @else
                                                Swal.fire({
                                                    title: '¿Eliminar rol?',
                                                    html: 'Se eliminará permanentemente el rol <strong>{{ addslashes($role->name) }}</strong>.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#dc2626',
                                                    cancelButtonColor: '#475569',
                                                    confirmButtonText: 'Sí, eliminar',
                                                    cancelButtonText: 'Cancelar',
                                                    customClass: { popup: 'rounded-xl border border-white/10 shadow-2xl' },
                                                    ...swalTheme(),
                                                }).then(r => { if (r.isConfirmed) document.getElementById('delete-role-{{ $role->id }}').submit() })
                                            @endif
                                        "
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                               transition-all duration-150
                                               @if($role->users_count > 0)
                                                   text-slate-400 dark:text-slate-600
                                                   border border-slate-200 dark:border-white/[0.04]
                                                   bg-slate-50 dark:bg-slate-800/40
                                                   cursor-not-allowed opacity-50
                                               @else
                                                   text-slate-500 dark:text-slate-400
                                                   border border-slate-200 dark:border-white/[0.06]
                                                   bg-slate-100 dark:bg-slate-800
                                                   hover:text-red-600 dark:hover:text-red-400
                                                   hover:border-red-500/30 hover:bg-red-500/[0.06]
                                               @endif">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                            <path d="M10 11v6M14 11v6" />
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                        </svg>
                                        Eliminar
                                    </button>

                                    {{-- Form oculto para submit --}}
                                    @if($role->users_count === 0)
                                        <form id="delete-role-{{ $role->id }}"
                                            action="{{ route('administracion.administrativa.roles.destroy', $role) }}"
                                            method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400 dark:text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    </svg>
                                    <p class="text-sm">No hay roles creados aún.</p>
                                    <a href="{{ route('administracion.administrativa.roles.create') }}"
                                        class="text-xs text-lime-600 dark:text-lime-400/70 hover:text-lime-500 dark:hover:text-lime-400 transition-colors">
                                        Crear el primer rol →
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($roles->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.05] bg-slate-50/80 dark:bg-black/10">
                    {{ $roles->links() }}
                </div>
            @endif

            <div class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50"></div>

        </div>

    </div>

</x-admin-layout>
