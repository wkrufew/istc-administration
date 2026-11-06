@php
    $input = 'w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
              text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
              focus:outline-none focus:ring-2 focus:ring-lime-500/30 focus:border-lime-500 focus:bg-white dark:focus:bg-slate-800
              disabled:opacity-60 disabled:cursor-not-allowed transition-all';
    $label = 'block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5';
    $puedeDatos = ! $soloLectura && auth()->user()->can('editar_usuarios');
    $puedeRol   = ! $soloLectura && auth()->user()->can('asignar_roles');
    $moodleActivo = (int) env('MOODLE_MODE', 0) === 1;
@endphp

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-5">

    {{-- ══ HEADER ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm px-6 py-5">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 leading-tight">Cuenta de usuario</h1>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 truncate">{{ $user->name }} · Acceso al sistema y rol</p>
                </div>
            </div>
            <a href="{{ route('administracion.administrativa.users.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
        </div>

        @if($rutaFicha || ($moodleActivo && auth()->user()->can('moodle_gestion')))
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-wrap items-center gap-2">
            @if($rutaFicha)
            <a href="{{ $rutaFicha }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 hover:bg-sky-100 dark:hover:bg-sky-900/40 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                Ver ficha (datos personales)
            </a>
            @endif
            @can('moodle_gestion')
                @if($moodleActivo)
                <a href="{{ route('administracion.administrativa.users.moodle', $user) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                    Moodle
                </a>
                @endif
            @endcan
        </div>
        @endif
    </div>

    @if($soloLectura)
        <div class="rounded-xl px-4 py-3 text-sm bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300">
            {{ $soloLectura }}
        </div>
    @endif

    <form wire:submit.prevent="guardar" autocomplete="off" novalidate class="space-y-5">

        {{-- ══ ACCESO ═══════════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5">
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Datos de acceso</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $label }}">Nombres <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="first_name" @disabled(! $puedeDatos) class="{{ $input }}">
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Apellidos <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="last_name" @disabled(! $puedeDatos) class="{{ $input }}">
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Cédula <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="cedula" maxlength="20" @disabled(! $puedeDatos) class="{{ $input }}">
                    @error('cedula') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Correo (usuario de acceso) <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="email" @disabled(! $puedeDatos) class="{{ $input }}">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Nueva contraseña <span class="font-normal text-slate-400">(opcional)</span></label>
                    <input type="password" wire:model="password" autocomplete="new-password" placeholder="Déjala vacía para no cambiarla" @disabled(! $puedeDatos) class="{{ $input }}">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Confirmar contraseña</label>
                    <input type="password" wire:model="password_confirmation" autocomplete="new-password" @disabled(! $puedeDatos) class="{{ $input }}">
                </div>
            </div>
        </div>

        {{-- ══ ROL ══════════════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5">
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Rol</h3>
            </div>
            <div class="p-6">
                @if($puedeRol)
                    <select wire:model="role_id" class="{{ $input }}">
                        <option value="">Seleccione un rol</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-[11px] text-slate-400 mt-2">El rol define a qué portal entra y qué puede hacer. El cambio queda registrado en Auditoría.</p>
                @else
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $user->roles->pluck('name')->implode(', ') ?: 'Sin rol' }}</p>
                    @unless($soloLectura)
                        <p class="text-[11px] text-slate-400 mt-1">Solo quien tiene permiso para asignar roles puede cambiarlo.</p>
                    @endunless
                @endif
            </div>
        </div>

        {{-- ══ ESTADO + ACCIONES ════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm px-6 py-5">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Estado de la cuenta</span>
                        <label class="relative inline-flex items-center {{ $puedeDatos ? 'cursor-pointer' : 'opacity-60 cursor-not-allowed' }}">
                            <input type="checkbox" wire:model.live="is_active" @disabled(! $puedeDatos) class="peer sr-only"/>
                            <div class="peer h-6 w-11 rounded-full border bg-slate-200 dark:bg-slate-700 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                        </label>
                        <span class="text-sm font-bold {{ $is_active ? 'text-green-600' : 'text-red-500' }}">{{ $is_active ? 'Activo' : 'Inactivo' }}</span>
                    </div>
                    @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    @if(! $soloLectura && $this->aplicaReenvioCredenciales())
                        @can('reenviar_credenciales')
                        <button type="button" x-data
                            @click="Swal.fire({
                                title: '¿Reenviar credenciales?',
                                html: 'La contraseña será restablecida al número de cédula y se enviará un correo con las nuevas credenciales.<br><br><strong>Esta acción es inmediata.</strong>',
                                icon: 'question', showCancelButton: true,
                                confirmButtonColor: '#d97706', cancelButtonColor: '#64748b',
                                confirmButtonText: 'Sí, reenviar', cancelButtonText: 'Cancelar',
                            }).then(r => r.isConfirmed && $wire.reenviarCredenciales())"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors disabled:opacity-60">
                            <span wire:loading.remove wire:target="reenviarCredenciales">Reenviar credenciales</span>
                            <span wire:loading wire:target="reenviarCredenciales">Enviando…</span>
                        </button>
                        @endcan
                    @endif

                    <a href="{{ route('administracion.administrativa.users.index') }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Cancelar
                    </a>
                    @if($puedeDatos || $puedeRol)
                    <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-lime-700 hover:bg-lime-600 shadow-sm transition-all disabled:opacity-60">
                        <span wire:loading.remove wire:target="guardar">Guardar cambios</span>
                        <span wire:loading wire:target="guardar">Guardando…</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
