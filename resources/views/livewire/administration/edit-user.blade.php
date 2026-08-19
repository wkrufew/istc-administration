@php
    $backRoute = match($from) {
        'users'      => route('administracion.administrativa.users.index'),
        'docentes'   => route('administracion.administrativa.docentes.index'),
        'eliminados' => route('administracion.administrativa.users.eliminados'),
        default      => route('administracion.administrativa.estudiantes.index'),
    };
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-5">

    {{-- ══ HEADER ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm px-6 py-5">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                            flex items-center justify-center shadow-sm shadow-blue-500/20 flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 leading-tight">Editar Usuario</h1>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Actualiza los datos personales, académicos y de acceso.</p>
                </div>
            </div>
            <a href="{{ $backRoute }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                      text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700
                      hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <form wire:submit.prevent="update" autocomplete="off" novalidate class="space-y-5">

        {{-- ══ FOTO DE PERFIL ═══════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Foto de Perfil</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-6">
                    <div class="flex-shrink-0">
                        @if($profile_photo)
                            <img class="w-20 h-20 rounded-2xl object-cover ring-2 ring-blue-500/20 shadow-sm"
                                 src="{{ $profile_photo->temporaryUrl() }}" alt="Vista previa">
                        @elseif($profile_photo_actual)
                            <img class="w-20 h-20 rounded-2xl object-cover ring-2 ring-slate-200 dark:ring-slate-600 shadow-sm"
                                 src="{{ Storage::url($profile_photo_actual) }}" alt="Foto actual">
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200
                                        dark:from-slate-700 dark:to-slate-800
                                        flex items-center justify-center shadow-sm">
                                <svg class="w-8 h-8 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 space-y-2">
                        <input type="file" wire:model="profile_photo" accept="image/*"
                               class="block w-full text-sm text-slate-500 dark:text-slate-400
                                      file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                                      file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100 file:transition-colors cursor-pointer"/>
                        <p class="text-xs text-slate-400 dark:text-slate-500">JPG, PNG o GIF. Máx. 2MB.</p>
                        @if($profile_photo_actual)
                            <button type="button" wire:click="deletePhoto"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-500
                                           hover:text-red-700 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar foto actual
                            </button>
                        @endif
                        @error('profile_photo')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ DATOS BÁSICOS ════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Datos Básicos</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="first_name" placeholder="Nombre"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Apellido <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="last_name" placeholder="Apellido"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Cédula <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="cedula" placeholder="Número de cédula"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                    @error('cedula') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Correo Electrónico <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="email" placeholder="correo@ejemplo.com" autocomplete="off"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Nueva Contraseña</label>
                    <input type="password" wire:model="password" placeholder="Dejar vacío para no cambiar" autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Confirmar Contraseña</label>
                    <input type="password" wire:model="password_confirmation" placeholder="Repite la contraseña" autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Rol <span class="text-red-500">*</span></label>
                    <select wire:model="role_id"
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                   text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                   focus:bg-white dark:focus:bg-slate-800 transition-all">
                        <option value="">Seleccione un rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Teléfono</label>
                    <input type="text" wire:model="phone" placeholder="0991234567"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Fecha de Nacimiento</label>
                    <input type="date" wire:model="fecha_nacimiento"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Género</label>
                    <select wire:model="genero"
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                   text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                   focus:bg-white dark:focus:bg-slate-800 transition-all">
                        <option value="">Seleccione</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Dirección</label>
                    <input type="text" wire:model="address" placeholder="Calle, número, ciudad"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
            </div>
        </div>

        {{-- ══ DATOS ACADÉMICOS ════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Datos Académicos</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                        Número de Matrícula
                        <span class="ml-1 text-xs font-normal text-slate-400 dark:text-slate-500 normal-case">(se asigna después)</span>
                    </label>
                    <input type="text" wire:model="matricula_numero" placeholder="ISTC-0001" disabled
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-700
                                  bg-slate-100 dark:bg-slate-900/40 px-4 py-2.5 text-sm
                                  text-slate-400 dark:text-slate-600 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Nacionalidad</label>
                    <select wire:model="nacionalidad"
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                   text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                   focus:bg-white dark:focus:bg-slate-800 transition-all">
                        <option value="">Seleccione</option>
                        <option value="Ecuatoriana">Ecuatoriana</option>
                        <option value="Colombiana">Colombiana</option>
                        <option value="Peruana">Peruana</option>
                        <option value="Venezolana">Venezolana</option>
                        <option value="Boliviana">Boliviana</option>
                        <option value="Chilena">Chilena</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Cubana">Cubana</option>
                        <option value="Española">Española</option>
                        <option value="Estadounidense">Estadounidense</option>
                        <option value="Otra">Otra</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Etnia</label>
                    <select wire:model="etnia"
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                   text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                   focus:bg-white dark:focus:bg-slate-800 transition-all">
                        <option value="">Seleccione</option>
                        <option value="Mestizo/a">Mestizo/a</option>
                        <option value="Indígena">Indígena</option>
                        <option value="Afroecuatoriano/a">Afroecuatoriano/a</option>
                        <option value="Montubio/a">Montubio/a</option>
                        <option value="Blanco/a">Blanco/a</option>
                        <option value="Mulato/a">Mulato/a</option>
                        <option value="Otro">Otro</option>
                    </select>
                    @error('etnia') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Estado Civil</label>
                    <select wire:model="estado_civil"
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                   text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                   focus:bg-white dark:focus:bg-slate-800 transition-all">
                        <option value="">Seleccione</option>
                        <option value="Soltero/a">Soltero/a</option>
                        <option value="Casado/a">Casado/a</option>
                        <option value="Unión libre">Unión libre</option>
                        <option value="Divorciado/a">Divorciado/a</option>
                        <option value="Viudo/a">Viudo/a</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ══ DATOS FAMILIARES ════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Datos Familiares</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Nombre del Padre</label>
                    <input type="text" wire:model="padre" placeholder="Nombre completo"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Nombre de la Madre</label>
                    <input type="text" wire:model="madre" placeholder="Nombre completo"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Tutor Legal</label>
                    <input type="text" wire:model="tutor" placeholder="Nombre completo"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
            </div>
        </div>

        {{-- ══ DATOS MÉDICOS ════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Datos Médicos y de Emergencia</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Tipo de Sangre</label>
                    <select wire:model="tipo_sangre"
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                   text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                   focus:bg-white dark:focus:bg-slate-800 transition-all">
                        <option value="">Seleccione</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Teléfono de Emergencia</label>
                    <input type="text" wire:model="telefono_emergencia" placeholder="0991234567"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Contacto de Emergencia</label>
                    <input type="text" wire:model="contacto_emergencia" placeholder="Nombre y relación"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                  text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  focus:bg-white dark:focus:bg-slate-800 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Observaciones Médicas</label>
                    <textarea wire:model="observaciones_medicas" rows="3"
                              placeholder="Alergias, condiciones crónicas, medicación..."
                              class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                     bg-slate-50 dark:bg-slate-900/70 px-4 py-2.5 text-sm
                                     text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                     focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                     focus:bg-white dark:focus:bg-slate-800 transition-all resize-none">
                    </textarea>
                </div>
            </div>
        </div>

        {{-- ══ DATOS DE FACTURACIÓN ════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Datos de Facturación</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">¿Es el mismo usuario quien factura?</span>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" wire:model.live="is_facturador" class="peer sr-only"/>
                        <div class="peer h-6 w-11 rounded-full border bg-slate-200 dark:bg-slate-700
                                    after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5
                                    after:rounded-full after:border after:border-gray-300 after:bg-white
                                    after:transition-all after:content-['']
                                    peer-checked:bg-green-600 peer-checked:after:translate-x-full
                                    peer-checked:after:border-white peer-focus:ring-green-300"></div>
                    </label>
                    <span class="text-sm font-semibold {{ $is_facturador ? 'text-green-600' : 'text-slate-400 dark:text-slate-500' }}">
                        {{ $is_facturador ? 'Sí' : 'No — datos separados' }}
                    </span>
                </div>

                @if(!$is_facturador)
                <div class="p-5 bg-slate-50/60 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Nombre Completo <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="fact_nombre" placeholder="Nombre del facturador"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                          bg-white dark:bg-slate-800 px-4 py-2.5 text-sm
                                          text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                          focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                            @error('fact_nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Documento <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="fact_documento" placeholder="Cédula o RUC"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                          bg-white dark:bg-slate-800 px-4 py-2.5 text-sm
                                          text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                          focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                            @error('fact_documento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Correo <span class="text-red-500">*</span></label>
                            <input type="email" wire:model="fact_correo" placeholder="correo@ejemplo.com"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                          bg-white dark:bg-slate-800 px-4 py-2.5 text-sm
                                          text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                          focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                            @error('fact_correo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Teléfono <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="fact_telefono" placeholder="0991234567"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                          bg-white dark:bg-slate-800 px-4 py-2.5 text-sm
                                          text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                          focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                            @error('fact_telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Dirección <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="fact_direccion" placeholder="Calle, número, ciudad"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600
                                          bg-white dark:bg-slate-800 px-4 py-2.5 text-sm
                                          text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
                                          focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                            @error('fact_direccion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ══ ESTADO + ACCIONES ════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm px-6 py-5">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Estado del Usuario</span>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" wire:model="is_active" class="peer sr-only"/>
                        <div class="peer h-6 w-11 rounded-full border bg-slate-200 dark:bg-slate-700
                                    after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5
                                    after:rounded-full after:border after:border-gray-300 after:bg-white
                                    after:transition-all after:content-['']
                                    peer-checked:bg-green-600 peer-checked:after:translate-x-full
                                    peer-checked:after:border-white peer-focus:ring-green-300"></div>
                    </label>
                    <span class="text-sm font-bold {{ $is_active ? 'text-green-600' : 'text-red-500' }}">
                        {{ $is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="flex items-center gap-3 flex-wrap">

                    {{-- Reenviar Credenciales — admin/docente siempre, estudiante solo con matrícula habilitada --}}
                    @php
                        $rolUsuario    = $user->roles->first();
                        $tieneAdmin    = $rolUsuario?->hasPermissionTo('acceso_administrativo');
                        $tieneDocente  = $rolUsuario?->hasPermissionTo('acceso_docencia');
                        $tieneEstud    = $rolUsuario?->hasPermissionTo('acceso_estudiantil') &&
                                         $user->matriculas()->where('estado', 'Habilitada')->exists();
                        $puedeReenviar = $tieneAdmin || $tieneDocente || $tieneEstud;
                    @endphp

                    @if($puedeReenviar)
                        <button type="button" x-data
                            @click="Swal.fire({
                                title: '¿Reenviar credenciales?',
                                html: 'La contraseña será restablecida al número de cédula del usuario y se enviará un correo con las nuevas credenciales.<br><br><strong>Esta acción es inmediata.</strong>',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#d97706',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Sí, reenviar',
                                cancelButtonText: 'Cancelar',
                            }).then(r => r.isConfirmed && $wire.reenviarCredenciales())"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                   text-amber-700 dark:text-amber-400
                                   bg-amber-50 dark:bg-amber-900/20
                                   border border-amber-200 dark:border-amber-700/40
                                   hover:bg-amber-100 dark:hover:bg-amber-900/40
                                   transition-colors disabled:opacity-60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span wire:loading.remove wire:target="reenviarCredenciales">Reenviar Credenciales</span>
                            <span wire:loading wire:target="reenviarCredenciales">Enviando...</span>
                        </button>
                    @endif

                    <a href="{{ $backRoute }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold
                              text-slate-600 dark:text-slate-300
                              bg-slate-100 dark:bg-slate-700
                              hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold
                                   text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-500/20
                                   transition-all disabled:opacity-60">
                        <span wire:loading.remove wire:target="update">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        <span wire:loading wire:target="update">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="update">Actualizar Usuario</span>
                        <span wire:loading wire:target="update">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
