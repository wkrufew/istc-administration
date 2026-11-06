@php
    $input = 'w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
              text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
              focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-800 transition-all';
    $label = 'block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5';
    $moodleActivo = (int) env('MOODLE_MODE', 0) === 1;
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-5">

    {{-- ══ HEADER ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm px-6 py-5">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118.82 17.5L12 21l-6.82-3.5a12.083 12.083 0 01.66-6.922L12 14z"/></svg>
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 leading-tight">Editar Docente</h1>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 truncate">{{ $user->name }} · {{ $user->roles->pluck('name')->implode(', ') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <x-boton-ficha-pdf :url="route('administracion.administrativa.docentes.ficha-pdf', $user)" />
                <a href="{{ route('administracion.administrativa.docentes.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver
                </a>
            </div>
        </div>

        {{-- Accesos a lo demás del docente --}}
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-wrap gap-2">
            <a href="{{ route('administracion.administrativa.docentes.documentos', $user) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 hover:bg-sky-100 dark:hover:bg-sky-900/40 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Documentos y contratos
            </a>
            <a href="{{ route('administracion.administrativa.docentes.asignar.form', $user) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Materias asignadas ({{ $asignacionesCount }})
            </a>
            @can('moodle_gestion')
                @if($moodleActivo)
                <a href="{{ route('administracion.administrativa.docentes.moodle', $user) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    Moodle
                </a>
                @endif
            @endcan
            @can('gestionar_usuarios')
            <a href="{{ route('administracion.administrativa.users.edit', $user) }}"
               title="Rol, correo de acceso y estado de la cuenta"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                Cuenta
            </a>
            @endcan
        </div>
    </div>

    <form wire:submit.prevent="guardar" autocomplete="off" novalidate class="space-y-5">

        @include('livewire.administration.partials.foto-perfil')

        {{-- ══ DATOS BÁSICOS ════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5">
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Datos básicos</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $label }}">Nombres <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="first_name" class="{{ $input }}">
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Apellidos <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="last_name" class="{{ $input }}">
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Cédula <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="cedula" maxlength="20" class="{{ $input }}">
                    @error('cedula') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Correo electrónico <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="email" class="{{ $input }}">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Es también su usuario para ingresar al sistema.</p>
                </div>
                <div>
                    <label class="{{ $label }}">Teléfono</label>
                    <input type="text" wire:model="phone" placeholder="0991234567" class="{{ $input }}">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Fecha de nacimiento</label>
                    <input type="date" wire:model="fecha_nacimiento" class="{{ $input }}">
                    @error('fecha_nacimiento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Género</label>
                    <select wire:model="genero" class="{{ $input }}">
                        <option value="">—</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                    @error('genero') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Nacionalidad</label>
                    <input type="text" wire:model="nacionalidad" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Estado civil</label>
                    <input type="text" wire:model="estado_civil" class="{{ $input }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Dirección</label>
                    <input type="text" wire:model="address" class="{{ $input }}">
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ══ CONTACTO DE EMERGENCIA ══════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5">
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Contacto de emergencia</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $label }}">Nombre del contacto</label>
                    <input type="text" wire:model="contacto_emergencia" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Teléfono del contacto</label>
                    <input type="text" wire:model="telefono_emergencia" class="{{ $input }}">
                    @error('telefono_emergencia') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ══ DISCAPACIDAD ═════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5">
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Discapacidad</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="inline-flex items-center gap-2 cursor-pointer sm:col-span-3">
                    <input type="checkbox" wire:model.live="tiene_discapacidad" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-slate-700 dark:text-slate-200">¿Tiene alguna discapacidad? <span class="text-xs text-slate-400">(se le pedirá el certificado CONADIS en sus documentos)</span></span>
                </label>
                @if ($tiene_discapacidad)
                    <div>
                        <label class="{{ $label }}">Tipo de discapacidad</label>
                        <select wire:model="tipo_discapacidad" class="{{ $input }}">
                            <option value="">—</option>
                            @foreach (\App\Support\FichaPersona::DISCAPACIDAD as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}">Porcentaje</label>
                        <input type="number" min="0" max="100" wire:model="porcentaje_discapacidad" class="{{ $input }}">
                        @error('porcentaje_discapacidad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">N.º carné CONADIS</label>
                        <input type="text" wire:model="nro_conadis" class="{{ $input }}">
                    </div>
                @endif
            </div>
        </div>

        {{-- ══ CONTRASEÑA ══════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5">
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Cambiar contraseña <span class="normal-case font-normal text-slate-400">(opcional)</span></h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $label }}">Nueva contraseña</label>
                    <input type="password" wire:model="password" autocomplete="new-password" placeholder="Déjala vacía para no cambiarla" class="{{ $input }}">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $label }}">Confirmar contraseña</label>
                    <input type="password" wire:model="password_confirmation" autocomplete="new-password" class="{{ $input }}">
                </div>
            </div>
        </div>

        {{-- ══ ESTADO + ACCIONES ════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm px-6 py-5">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Estado de la cuenta</span>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" wire:model.live="is_active" class="peer sr-only"/>
                            <div class="peer h-6 w-11 rounded-full border bg-slate-200 dark:bg-slate-700 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                        </label>
                        <span class="text-sm font-bold {{ $is_active ? 'text-green-600' : 'text-red-500' }}">{{ $is_active ? 'Activo' : 'Inactivo' }}</span>
                    </div>
                    @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    @if($this->aplicaReenvioCredenciales())
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span wire:loading.remove wire:target="reenviarCredenciales">Reenviar credenciales</span>
                            <span wire:loading wire:target="reenviarCredenciales">Enviando…</span>
                        </button>
                        @endcan
                    @endif

                    <a href="{{ route('administracion.administrativa.docentes.index') }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Cancelar
                    </a>
                    @can('editar_usuarios')
                    <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all disabled:opacity-60">
                        <span wire:loading.remove wire:target="guardar">Guardar cambios</span>
                        <span wire:loading wire:target="guardar">Guardando…</span>
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </form>
</div>
