<div class="max-w-4xl mx-auto px-4 py-6 space-y-4">

    {{-- ═══════ HEADER ═══════ --}}
    <div class="bg-slate-900 border border-slate-700/50 relative overflow-hidden rounded-xl">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-emerald-500 to-teal-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-emerald-700 via-teal-500 to-sky-600 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-700 to-teal-700 flex items-center justify-center shadow-lg shadow-emerald-900/40 flex-shrink-0">
                    {{-- Ícono Moodle-like --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-white/90 leading-none">Gestión Moodle</h1>
                    <p class="text-xs text-emerald-400/70 tracking-widest uppercase mt-1">
                        {{ $usuario->name }}
                    </p>
                </div>
            </div>

            <a href="{{ route('administracion.administrativa.users.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium tracking-widest uppercase
                      text-slate-400 border border-white/[0.06] hover:text-white hover:border-white/20 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    {{-- ═══════ ALERTA MOODLE INACTIVO ═══════ --}}
    @if(! $moodleActivo)
    <div class="bg-amber-500/10 border border-amber-500/25 rounded-xl px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div>
            <p class="text-amber-400 font-medium text-sm">Integración Moodle desactivada</p>
            <p class="text-amber-400/70 text-xs mt-0.5">
                Active la integración en
                <a href="{{ route('administracion.administrativa.settings') }}" class="underline hover:text-amber-300">
                    Configuración → Moodle
                </a>
                para habilitar estas acciones.
            </p>
        </div>
    </div>
    @endif

    {{-- ═══════ MENSAJE DE ESTADO ═══════ --}}
    @if($mensaje)
    <div class="rounded-xl px-5 py-4 flex items-start gap-3 border
        {{ $tipoMensaje === 'success' ? 'bg-lime-500/10 border-lime-500/25' : '' }}
        {{ $tipoMensaje === 'error'   ? 'bg-red-500/10 border-red-500/25'   : '' }}
        {{ $tipoMensaje === 'warning' ? 'bg-amber-500/10 border-amber-500/25' : '' }}
        {{ $tipoMensaje === 'info'    ? 'bg-sky-500/10 border-sky-500/25'   : '' }}
    ">
        @if($tipoMensaje === 'success')
            <svg class="w-5 h-5 text-lime-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-lime-400 text-sm">{{ $mensaje }}</p>
        @elseif($tipoMensaje === 'error')
            <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-red-400 text-sm">{{ $mensaje }}</p>
        @elseif($tipoMensaje === 'warning')
            <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="text-amber-400 text-sm">{{ $mensaje }}</p>
        @else
            <svg class="w-5 h-5 text-sky-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sky-400 text-sm">{{ $mensaje }}</p>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- ═══════ TARJETA DATOS DEL USUARIO ═══════ --}}
        <div class="bg-slate-900 border border-white/[0.06] rounded-xl p-5 space-y-3">
            <p class="text-[0.65rem] font-medium tracking-widest uppercase text-slate-500 mb-4">Datos del usuario</p>

            <div class="space-y-2">
                <div class="flex items-center justify-between py-2 border-b border-white/[0.05]">
                    <span class="text-xs text-slate-500">Nombre</span>
                    <span class="text-sm text-white/80 font-medium">{{ $usuario->name }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/[0.05]">
                    <span class="text-xs text-slate-500">Correo</span>
                    <span class="text-xs text-slate-300 font-mono">{{ $usuario->email }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/[0.05]">
                    <span class="text-xs text-slate-500">Cédula</span>
                    <span class="text-xs text-slate-300 font-mono">{{ $usuario->cedula ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/[0.05]">
                    <span class="text-xs text-slate-500">Moodle ID</span>
                    @if($usuario->moodle_id)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                     bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-mono">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            #{{ $usuario->moodle_id }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-slate-700/50 border border-white/[0.06] text-slate-500">
                            No sincronizado
                        </span>
                    @endif
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-xs text-slate-500">Estado acceso</span>
                    @if($usuario->moodle_id)
                        @if($usuario->moodle_suspended)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                         bg-red-500/15 border border-red-500/30 text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                Suspendido
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                         bg-lime-500/15 border border-lime-500/30 text-lime-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-lime-400"></span>
                                Activo
                            </span>
                        @endif
                    @else
                        <span class="text-xs text-slate-600">—</span>
                    @endif
                </div>
            </div>

            @if($moodleUrl)
            <a href="{{ $moodleUrl }}" target="_blank"
               class="mt-3 flex items-center gap-2 text-xs text-emerald-400/70 hover:text-emerald-400 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                {{ $moodleUrl }}
            </a>
            @endif
        </div>

        {{-- ═══════ TARJETA ACCIONES ═══════ --}}
        <div class="bg-slate-900 border border-white/[0.06] rounded-xl p-5 space-y-3">
            <p class="text-[0.65rem] font-medium tracking-widest uppercase text-slate-500 mb-4">Acciones Moodle</p>

            {{-- 1. Obtener / Sincronizar ID --}}
            <div class="group flex items-start gap-3 p-3 rounded-lg border border-white/[0.05] bg-slate-800/40 hover:border-sky-500/25 transition-all">
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 border border-sky-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white/80">Sincronizar ID</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Busca al usuario en Moodle por cédula o correo y guarda su ID aquí.</p>
                    <button wire:click="sincronizarId" wire:loading.attr="disabled"
                            class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   bg-sky-500/10 border border-sky-500/20 text-sky-400
                                   hover:bg-sky-500/20 hover:border-sky-400/40 transition-all duration-150
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="sincronizarId">Sincronizar</span>
                        <span wire:loading wire:target="sincronizarId">Buscando…</span>
                    </button>
                </div>
            </div>

            {{-- 2. Registrar en Moodle --}}
            @if(! $usuario->moodle_id)
            <div class="group flex items-start gap-3 p-3 rounded-lg border border-white/[0.05] bg-slate-800/40 hover:border-emerald-500/25 transition-all">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white/80">Registrar en Moodle</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Crea la cuenta en Moodle con cédula como usuario y contraseña. Envía email de bienvenida.</p>
                    <button wire:click="registrarEnMoodle" wire:loading.attr="disabled"
                            x-data
                            @click.prevent="Swal.fire({
                                title: 'Registrar en Moodle',
                                html: 'Se creará la cuenta de <strong>{{ addslashes($usuario->name) }}</strong> en el campus virtual.<br>Usuario y contraseña: <code>{{ $usuario->cedula }}</code>',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#059669',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Sí, registrar',
                                cancelButtonText: 'Cancelar',
                            }).then(r => r.isConfirmed && $wire.registrarEnMoodle())"
                            class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   bg-emerald-500/10 border border-emerald-500/20 text-emerald-400
                                   hover:bg-emerald-500/20 hover:border-emerald-400/40 transition-all duration-150
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="registrarEnMoodle">Registrar</span>
                        <span wire:loading wire:target="registrarEnMoodle">Procesando…</span>
                    </button>
                </div>
            </div>
            @endif

            {{-- 3. Actualizar datos --}}
            @if($usuario->moodle_id)
            <div class="group flex items-start gap-3 p-3 rounded-lg border border-white/[0.05] bg-slate-800/40 hover:border-amber-500/25 transition-all">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white/80">Actualizar datos</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Sincroniza nombre y correo del usuario hacia Moodle.</p>
                    <button wire:click="actualizarDatos" wire:loading.attr="disabled"
                            class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   bg-amber-500/10 border border-amber-500/20 text-amber-400
                                   hover:bg-amber-500/20 hover:border-amber-400/40 transition-all duration-150
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="actualizarDatos">Actualizar</span>
                        <span wire:loading wire:target="actualizarDatos">Actualizando…</span>
                    </button>
                </div>
            </div>

            {{-- 4. Restablecer credenciales --}}
            <div class="group flex items-start gap-3 p-3 rounded-lg border border-white/[0.05] bg-slate-800/40 hover:border-red-500/25 transition-all">
                <div class="w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white/80">Restablecer credenciales</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Resetea la contraseña Moodle a la cédula y envía correo de notificación al estudiante.</p>
                    <button wire:click="restablecerCredenciales" wire:loading.attr="disabled"
                            x-data
                            @click.prevent="Swal.fire({
                                title: 'Restablecer contraseña Moodle',
                                html: 'La contraseña de <strong>{{ addslashes($usuario->name) }}</strong> en Moodle se restablecerá a su cédula: <code>{{ $usuario->cedula }}</code>',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc2626',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Sí, restablecer',
                                cancelButtonText: 'Cancelar',
                            }).then(r => r.isConfirmed && $wire.restablecerCredenciales())"
                            class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   bg-red-500/10 border border-red-500/20 text-red-400
                                   hover:bg-red-500/20 hover:border-red-400/40 transition-all duration-150
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="restablecerCredenciales">Restablecer</span>
                        <span wire:loading wire:target="restablecerCredenciales">Procesando…</span>
                    </button>
                </div>
            </div>

            {{-- 5. Suspender / Reactivar acceso --}}
            @if(! $usuario->moodle_suspended)
            <div class="group flex items-start gap-3 p-3 rounded-lg border border-white/[0.05] bg-slate-800/40 hover:border-orange-500/25 transition-all">
                <div class="w-8 h-8 rounded-lg bg-orange-500/10 border border-orange-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white/80">Suspender acceso</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Bloquea el acceso de este usuario al campus virtual de Moodle.</p>
                    <button wire:loading.attr="disabled"
                            x-data
                            @click.prevent="Swal.fire({
                                title: 'Suspender acceso Moodle',
                                html: 'Se bloqueará el acceso de <strong>{{ addslashes($usuario->name) }}</strong> al campus virtual. El usuario no podrá iniciar sesión.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ea580c',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Sí, suspender',
                                cancelButtonText: 'Cancelar',
                            }).then(r => r.isConfirmed && $wire.suspenderAcceso(true))"
                            class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   bg-orange-500/10 border border-orange-500/20 text-orange-400
                                   hover:bg-orange-500/20 hover:border-orange-400/40 transition-all duration-150
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="suspenderAcceso">Suspender</span>
                        <span wire:loading wire:target="suspenderAcceso">Procesando…</span>
                    </button>
                </div>
            </div>
            @else
            <div class="group flex items-start gap-3 p-3 rounded-lg border border-white/[0.05] bg-slate-800/40 hover:border-lime-500/25 transition-all">
                <div class="w-8 h-8 rounded-lg bg-lime-500/10 border border-lime-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white/80">Reactivar acceso</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Restaura el acceso de este usuario al campus virtual de Moodle.</p>
                    <button wire:loading.attr="disabled"
                            x-data
                            @click.prevent="Swal.fire({
                                title: 'Reactivar acceso Moodle',
                                html: 'Se restaurará el acceso de <strong>{{ addslashes($usuario->name) }}</strong> al campus virtual.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#16a34a',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Sí, reactivar',
                                cancelButtonText: 'Cancelar',
                            }).then(r => r.isConfirmed && $wire.suspenderAcceso(false))"
                            class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   bg-lime-500/10 border border-lime-500/20 text-lime-400
                                   hover:bg-lime-500/20 hover:border-lime-400/40 transition-all duration-150
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="suspenderAcceso">Reactivar</span>
                        <span wire:loading wire:target="suspenderAcceso">Procesando…</span>
                    </button>
                </div>
            </div>
            @endif
            @endif

        </div>
    </div>

</div>
