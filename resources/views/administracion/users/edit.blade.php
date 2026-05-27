<x-admin-layout>

<div class="max-w-4xl mx-auto px-4 py-6 space-y-5">

    {{-- ══════════════════════════════════════
         HEADER
    ══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">

        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40 flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Asignar Rol</h1>
                    <p class="text-xs text-lime-600 dark:text-lime-400/70 tracking-widest uppercase mt-1">Administración · Usuarios</p>
                </div>
            </div>

            <a href="{{ route('administracion.administrativa.users.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full
                       border border-slate-200 dark:border-white/10
                       bg-slate-100 dark:bg-white/5
                       text-slate-600 dark:text-slate-300 text-xs font-medium tracking-wide
                       hover:bg-slate-200 dark:hover:bg-white/10 hover:text-slate-800 dark:hover:text-white
                       hover:-translate-x-0.5 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al listado
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         PERFIL DE USUARIO (solo lectura)
    ══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-white/[0.06] overflow-hidden shadow-sm dark:shadow-xl dark:shadow-black/30">

        <div class="h-px bg-gradient-to-r from-transparent via-sky-500/30 to-transparent"></div>

        <div class="px-6 py-5 flex items-start gap-5 flex-wrap sm:flex-nowrap">

            {{-- Avatar --}}
            <div class="flex-shrink-0">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-700 to-sky-700
                            flex items-center justify-center shadow-lg shadow-green-900/30 ring-4
                            ring-white dark:ring-slate-800">
                    <span class="text-2xl font-bold text-white uppercase select-none">
                        {{ substr($user->first_name ?? $user->name, 0, 1) }}
                    </span>
                </div>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0 space-y-3">

                {{-- Nombre y estado --}}
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white/90 leading-tight">
                        {{ $user->name }}
                    </h2>
                    @if($user->is_active)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium
                                     bg-lime-500/10 border border-lime-500/20 text-lime-600 dark:text-lime-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-lime-500 dark:bg-lime-400 animate-pulse"></span>
                            Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium
                                     bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                            Inactivo
                        </span>
                    @endif
                </div>

                {{-- Datos en grilla compacta --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">

                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</span>
                    </div>

                    @if($user->cedula)
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                        </svg>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $user->cedula }}</span>
                    </div>
                    @endif

                    @if($user->phone)
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12 19.79 19.79 0 0 1 1.89 3.38 2 2 0 0 1 3.9 1.21h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $user->phone }}</span>
                    </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            Registrado {{ $user->created_at->diffForHumans() }}
                        </span>
                    </div>

                </div>

                {{-- Rol actual --}}
                <div class="flex items-center gap-2 flex-wrap pt-1">
                    <span class="text-[0.65rem] font-medium tracking-widest uppercase text-slate-400 dark:text-slate-500">Rol actual:</span>
                    @forelse($user->roles as $rol)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.65rem] font-semibold
                                     bg-sky-500/10 border border-sky-500/20 text-sky-600 dark:text-sky-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                            {{ $rol->name }}
                        </span>
                    @empty
                        <span class="text-xs text-slate-400 dark:text-slate-500 italic">Sin rol asignado</span>
                    @endforelse
                </div>

            </div>
        </div>

        <div class="h-px bg-gradient-to-r from-transparent via-slate-200 dark:via-white/[0.04] to-transparent"></div>
    </div>

    {{-- ══════════════════════════════════════
         SELECCIÓN DE ROL
    ══════════════════════════════════════ --}}
    <form action="{{ route('administracion.administrativa.users.update', $user) }}" method="POST" id="role-form">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-white/[0.06] overflow-hidden shadow-sm dark:shadow-xl dark:shadow-black/30"
             x-data="{ selected: {{ $user->roles->first()?->id ?? 'null' }} }">

            <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

            {{-- Encabezado sección --}}
            <div class="px-6 pt-5 pb-4 flex items-center gap-3">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-700 dark:text-white/80">Selecciona el nuevo rol</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Solo puede asignarse un rol por usuario. El rol actual será reemplazado.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[0.62rem] font-medium tracking-wide
                             bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.06]
                             text-slate-500 dark:text-slate-400">
                    {{ $roles->count() }} roles
                </span>
            </div>

            <div class="px-6 pb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach ($roles as $role)

                    {{-- Radio oculto --}}
                    <input type="radio"
                           id="role_{{ $role->id }}"
                           name="roles[]"
                           value="{{ $role->id }}"
                           class="sr-only"
                           {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                           x-model="selected">

                    <label for="role_{{ $role->id }}"
                           class="cursor-pointer select-none"
                           @click="selected = {{ $role->id }}">

                        <div :class="selected == {{ $role->id }}
                                ? 'bg-lime-500/[0.07] dark:bg-lime-500/[0.09] border-lime-500/50 shadow-md shadow-lime-500/10 -translate-y-0.5'
                                : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200 dark:border-white/[0.06] hover:border-slate-300 dark:hover:border-white/15 hover:-translate-y-0.5'"
                             class="relative flex flex-col gap-3 px-4 py-4 rounded-xl border transition-all duration-200">

                            {{-- Top row: ícono + indicador --}}
                            <div class="flex items-start justify-between">

                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm flex-shrink-0 transition-all duration-200"
                                     :class="selected == {{ $role->id }}
                                         ? 'bg-gradient-to-br from-lime-600 to-green-700 shadow-lime-600/30'
                                         : 'bg-gradient-to-br from-slate-600 to-slate-700 dark:from-slate-600 dark:to-slate-800'">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </div>

                                {{-- Indicador selección --}}
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200"
                                     :class="selected == {{ $role->id }}
                                         ? 'bg-lime-500 border-lime-500'
                                         : 'border-slate-300 dark:border-slate-600'">
                                    <svg x-show="selected == {{ $role->id }}"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 scale-75"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>

                            </div>

                            {{-- Nombre del rol --}}
                            <div>
                                <p class="text-sm font-bold leading-tight transition-colors duration-200"
                                   :class="selected == {{ $role->id }}
                                       ? 'text-lime-700 dark:text-lime-300'
                                       : 'text-slate-700 dark:text-white/80'">
                                    {{ $role->name }}
                                </p>

                                {{-- Permisos count --}}
                                <div class="flex items-center gap-1 mt-1.5">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <span class="text-[0.65rem] text-slate-400 dark:text-slate-500">
                                        {{ $role->permissions_count }} {{ $role->permissions_count === 1 ? 'permiso' : 'permisos' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Chip "Actual" si es el rol actual --}}
                            @if($user->roles->contains($role->id))
                                <div class="absolute -top-2 left-3">
                                    <span class="px-2 py-0.5 rounded-full text-[0.6rem] font-bold tracking-wide
                                                 bg-sky-500 text-white shadow-sm shadow-sky-500/30">
                                        Actual
                                    </span>
                                </div>
                            @endif

                        </div>
                    </label>

                @endforeach
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.05] bg-slate-50 dark:bg-black/15 flex items-center justify-between gap-3 flex-wrap">

                <p class="text-xs text-slate-400 dark:text-slate-500">
                    <span x-show="selected === null" class="text-amber-500 dark:text-amber-400">
                        Ningún rol seleccionado
                    </span>
                    <span x-show="selected !== null">
                        Rol seleccionado:
                        <span class="font-semibold text-slate-600 dark:text-slate-300">
                            @foreach($roles as $r)
                                <span x-show="selected == {{ $r->id }}">{{ $r->name }}</span>
                            @endforeach
                        </span>
                    </span>
                </p>

                <div class="flex items-center gap-3">
                    <a href="{{ route('administracion.administrativa.users.index') }}"
                        class="px-5 py-2 rounded-full text-xs font-medium tracking-wide
                               text-slate-500 dark:text-slate-400
                               border border-slate-200 dark:border-white/[0.08]
                               hover:bg-slate-100 dark:hover:bg-white/5
                               hover:text-slate-700 dark:hover:text-slate-200
                               transition-all duration-200">
                        Cancelar
                    </a>

                    <button type="button" onclick="confirmarAsignacion()"
                        class="inline-flex items-center gap-1.5 px-6 py-2 rounded-full text-xs font-medium tracking-widest uppercase
                               text-white/90
                               bg-gradient-to-r from-green-800/70 via-sky-800/60 to-purple-900/55
                               border border-lime-500/25
                               hover:from-green-700/80 hover:via-sky-700/70 hover:to-purple-800/65
                               hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-900/30
                               active:translate-y-0 transition-all duration-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        Asignar Rol
                    </button>
                </div>

            </div>

        </div>
    </form>

</div>

@push('js')
<script>
    function confirmarAsignacion() {
        const selected = document.querySelector('input[name="roles[]"]:checked');

        if (!selected) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin rol seleccionado',
                text: 'Selecciona un rol antes de continuar.',
                confirmButtonColor: '#ca8a04',
                customClass: { popup: 'rounded-xl border border-white/10 shadow-2xl' },
                ...swalTheme(),
            });
            return;
        }

        const roleId   = selected.value;
        const roleLabel = document.querySelector(`label[for="role_${roleId}"] p.text-sm`);
        const roleName = roleLabel ? roleLabel.textContent.trim() : 'seleccionado';
        const userName = @json($user->name);

        Swal.fire({
            title: '¿Confirmar asignación?',
            html: `Se asignará el rol <strong>${roleName}</strong> a <strong>${userName}</strong>.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#475569',
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-xl border border-white/10 shadow-2xl' },
            ...swalTheme(),
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('role-form').submit();
            }
        });
    }
</script>
@endpush

</x-admin-layout>
