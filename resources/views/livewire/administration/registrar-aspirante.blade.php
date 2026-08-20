<div>
    {{-- ══ MODAL CÉDULA ════════════════════════════════════════════════════ --}}
    @if($showEntryModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarEntryModal"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4">

            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white">Consultar datos por cédula</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Ingresa el número de cédula para auto-completar nombres y datos personales.
                    </p>
                </div>
                <button wire:click="cerrarEntryModal"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if(!$apiActiva)
            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-xs text-amber-700 dark:text-amber-300">
                La API de cédulas no está configurada. Ve a <strong>Ajustes → API Cédula</strong> para activarla.
            </div>
            @endif

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                        Número de cédula
                    </label>
                    <div class="flex gap-2">
                        <input wire:model="cedulaModalInput" type="text"
                               wire:keydown.enter="consultarEnModal"
                               placeholder="Ej. 0601234567"
                               {{ !$apiActiva ? 'disabled' : '' }}
                               class="flex-1 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                      text-slate-800 dark:text-slate-200 text-sm px-3 py-2.5 focus:outline-none
                                      focus:ring-2 focus:ring-lime-500/40 disabled:opacity-50 transition-colors">
                        <button wire:click="consultarEnModal"
                                wire:loading.attr="disabled"
                                {{ !$apiActiva ? 'disabled' : '' }}
                                class="px-4 py-2.5 rounded-xl bg-lime-600 hover:bg-lime-700 disabled:opacity-50 text-white text-sm font-medium transition-colors whitespace-nowrap">
                            <span wire:loading.remove wire:target="consultarEnModal">Buscar</span>
                            <span wire:loading wire:target="consultarEnModal">…</span>
                        </button>
                    </div>
                    @error('cedulaModal')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Resultado de la consulta --}}
                @if(!empty($cedulaModalData))
                <div class="p-3.5 rounded-xl bg-lime-50 dark:bg-lime-900/20 border border-lime-200 dark:border-lime-700 space-y-1">
                    <p class="text-xs font-semibold text-lime-800 dark:text-lime-300 uppercase tracking-wide">Datos encontrados</p>
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $cedulaModalData['nombres'] ?? '—' }}</p>
                    @if(!empty($cedulaModalData['fechaNacimiento']))
                    <p class="text-xs text-slate-500">Nacimiento: {{ $cedulaModalData['fechaNacimiento'] }}</p>
                    @endif
                    @if(!empty($cedulaModalData['genero'] ?? $cedulaModalData['sexo'] ?? null))
                    <p class="text-xs text-slate-500">Género: {{ $cedulaModalData['genero'] ?? $cedulaModalData['sexo'] }}</p>
                    @endif
                </div>
                @endif
            </div>

            <div class="flex justify-between gap-3 pt-1">
                <button wire:click="cerrarEntryModal"
                        class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors underline underline-offset-2">
                    Rellenar manualmente
                </button>
                @if(!empty($cedulaModalData))
                <button wire:click="aplicarDesdeModal"
                        class="px-5 py-2 rounded-xl bg-lime-600 hover:bg-lime-700 text-white text-sm font-medium transition-colors shadow-sm">
                    Aplicar datos
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-4 mb-6 mt-2">
        <a href="{{ route('administracion.administrativa.aspirantes.index') }}"
           class="p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Registrar aspirante</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">El aspirante recibirá sus credenciales de acceso cuando su solicitud pase a proceso</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto p-6">
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">

            {{-- Aviso informativo --}}
            <div class="flex gap-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-sm text-blue-700 dark:text-blue-300">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <p>Se enviará un correo de confirmación al registrar. Las credenciales de acceso al portal se enviarán automáticamente cuando la solicitud pase a <strong>proceso</strong>.</p>
            </div>

            {{-- Botón consultar cédula (si ya cerró el modal) --}}
            @if(!$showEntryModal)
            <div class="flex justify-end">
                <button wire:click="abrirEntryModal"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium
                               text-lime-700 dark:text-lime-300 bg-lime-50 dark:bg-lime-900/20 border border-lime-200 dark:border-lime-700
                               hover:bg-lime-100 dark:hover:bg-lime-900/40 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                    </svg>
                    Consultar cédula
                </button>
            </div>
            @endif

            {{-- Fila 1: Nombre | Apellido --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                        Nombres <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="firstName" type="text" placeholder="Ej. Juan Carlos"
                           class="w-full rounded-xl border text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-lime-500/40 transition-colors
                                  {{ $errors->has('firstName') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                    @error('firstName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                        Apellidos <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="lastName" type="text" placeholder="Ej. Pérez López"
                           class="w-full rounded-xl border text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-lime-500/40 transition-colors
                                  {{ $errors->has('lastName') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                    @error('lastName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Fila 2: Cédula | Teléfono --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">
                            {{ $documentoInternacional ? 'N.° de documento' : 'Cédula de identidad' }}
                            <span class="text-red-500">*</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer select-none">
                            <input wire:model.live="documentoInternacional" type="checkbox"
                                   class="w-3.5 h-3.5 rounded accent-lime-600 cursor-pointer">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Internacional</span>
                        </label>
                    </div>
                    <input wire:model="cedula" type="text"
                           placeholder="{{ $documentoInternacional ? 'N.° de pasaporte u otro' : '0000000000' }}"
                           class="w-full rounded-xl border text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-lime-500/40 transition-colors
                                  {{ $errors->has('cedula') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                    @error('cedula') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    @if(!$documentoInternacional && !$errors->has('cedula'))
                        <p class="text-xs text-slate-400 mt-1">10 dígitos · validación automática</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                        Teléfono <span class="text-slate-400">(opcional)</span>
                    </label>
                    <input wire:model="telefono" type="text" placeholder="0987654321"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                  text-slate-800 dark:text-slate-200 text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-lime-500/40 transition-colors">
                </div>
            </div>

            {{-- Fila 3: Correo --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input wire:model="email" type="email" placeholder="aspirante@correo.com"
                       class="w-full rounded-xl border text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-lime-500/40 transition-colors
                              {{ $errors->has('email') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Fila 4: Carrera | Cohorte --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                        Carrera <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="carrera_id"
                            class="w-full rounded-xl border text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-lime-500/40 transition-colors
                                   {{ $errors->has('carrera_id') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                        <option value="">Selecciona una carrera</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
                        @endforeach
                    </select>
                    @error('carrera_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                        Cohorte <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="cohorte_id"
                            class="w-full rounded-xl border text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-lime-500/40 transition-colors
                                   {{ $errors->has('cohorte_id') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                        <option value="">Selecciona una cohorte</option>
                        @foreach($cohortes as $cohorte)
                            <option value="{{ $cohorte->id }}">{{ $cohorte->nombre }}</option>
                        @endforeach
                    </select>
                    @error('cohorte_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    @if($cohortes->isEmpty())
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                            No hay cohortes abiertas.
                            <a href="{{ route('administracion.administrativa.cohortes.index') }}" class="underline">Crear una</a>.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Tipo de proceso --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">
                    Tipo de proceso <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-colors
                                  {{ $tipo_proceso === 'regular' ? 'border-lime-500 bg-lime-50 dark:bg-lime-900/20' : 'border-slate-200 dark:border-slate-600 hover:border-slate-300' }}">
                        <input wire:model.live="tipo_proceso" type="radio" value="regular" class="mt-0.5 accent-lime-600 flex-shrink-0">
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-tight">Regular</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">Cédula, bachiller y comprobante de pago</p>
                        </div>
                    </label>
                    <label class="relative flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-colors
                                  {{ $tipo_proceso === 'validacion_conocimientos' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-600 hover:border-slate-300' }}">
                        <input wire:model.live="tipo_proceso" type="radio" value="validacion_conocimientos" class="mt-0.5 accent-indigo-600 flex-shrink-0">
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-tight">Validación de conocimientos</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">Requiere documentos adicionales de experiencia</p>
                        </div>
                    </label>
                </div>
                @if($tipo_proceso === 'validacion_conocimientos')
                <div class="mt-2 flex items-start gap-2 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700">
                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    <p class="text-xs text-indigo-700 dark:text-indigo-300 leading-relaxed">
                        El aspirante deberá subir adicionalmente: <strong>hoja de vida</strong>, <strong>certificados laborales</strong> (PDF unificado) y <strong>certificados de cursos o capacitaciones</strong> (PDF unificado). El mecanizado del IESS es opcional.
                    </p>
                </div>
                @endif
                @error('tipo_proceso') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Acciones --}}
            <div class="flex items-center justify-end gap-3 pt-1">
                <a href="{{ route('administracion.administrativa.aspirantes.index') }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Cancelar
                </a>
                <button wire:click="guardar" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-lime-600 hover:bg-lime-700 disabled:opacity-60 text-white text-sm font-medium transition-colors shadow-sm">
                    <svg wire:loading.remove wire:target="guardar" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                    </svg>
                    <span wire:loading.remove wire:target="guardar">Registrar aspirante</span>
                    <span wire:loading wire:target="guardar">Registrando…</span>
                </button>
            </div>
        </div>
    </div>
</div>
