<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="mb-6">
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('administracion.administrativa.matriculacion.index') }}"
                    class="text-slate-400 hover:text-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-white">Pago de Matrícula</h2>
                    <p class="text-slate-400 text-sm mt-0.5">Registre el comprobante de pago para habilitar la matrícula</p>
                </div>
            </div>
        </div>

        {{-- ALERTA DE ERROR GENERAL --}}
        @error('general')
            <div class="mb-4 bg-red-500/10 border border-red-500/30 rounded-xl p-4">
                <p class="text-sm text-red-400">{{ $message }}</p>
            </div>
        @enderror

        @if (session('info'))
            <div class="mb-4 bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
                <p class="text-sm text-blue-400">{{ session('info') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- ================================================================
             COLUMNA IZQUIERDA: Resumen
             ================================================================ --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Datos del Estudiante --}}
                <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden shadow-lg">
                    <div class="bg-gradient-to-r from-slate-700 to-slate-600 px-4 py-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="text-slate-200 font-semibold text-sm uppercase tracking-wide">Estudiante</h3>
                    </div>
                    <div class="p-4 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nombre</span>
                            <span class="font-semibold text-white text-right">{{ $matricula->estudiante->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Cédula</span>
                            <span class="font-medium text-slate-200">{{ $matricula->estudiante->cedula }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">N° Matrícula</span>
                            <span class="font-medium text-slate-200">{{ $matricula->estudiante->matricula_numero }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Carrera</span>
                            <span class="font-medium text-slate-200 text-right">{{ $matricula->carrera->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Período</span>
                            <span class="font-medium text-slate-200">{{ $matricula->periodo->code }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-700 pt-3">
                            <span class="text-slate-400">Código</span>
                            <span class="font-mono text-xs font-semibold text-blue-400 bg-blue-500/10 px-2 py-1 rounded-lg">
                                {{ $matricula->code }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Desglose de Costos --}}
                <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden shadow-lg">
                    <div class="bg-gradient-to-r from-slate-700 to-slate-600 px-4 py-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-slate-200 font-semibold text-sm uppercase tracking-wide">Desglose de Pago</h3>
                    </div>
                    <div class="p-4 space-y-2.5 text-sm">

                        {{-- Matrícula base --}}
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Matrícula base</span>
                            <span class="font-medium text-slate-200">${{ number_format($montoMatricula, 2) }}</span>
                        </div>

                        {{-- Materias de arrastre --}}
                        @if (!empty($materiasArrastre))
                            <div class="border-t border-slate-700 pt-2.5 space-y-1.5">
                                <p class="text-xs font-semibold text-amber-400/80 uppercase tracking-wide mb-1">Arrastres</p>
                                @foreach ($materiasArrastre as $arrastre)
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-slate-300 text-xs">{{ $arrastre['nombre'] }}</p>
                                            <p class="text-slate-500 text-xs">{{ $arrastre['creditos'] }} créd.</p>
                                        </div>
                                        <span class="font-medium text-amber-400 text-xs">
                                            +${{ number_format($arrastre['costo_adicional'], 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Inscripción (primera matrícula) --}}
                        @if ($montoInscripcion > 0)
                            <div class="flex justify-between items-center border-t border-slate-700 pt-2.5">
                                <span class="text-orange-400/80">Inscripción (1ª matrícula)</span>
                                <span class="font-medium text-orange-400">+${{ number_format($montoInscripcion, 2) }}</span>
                            </div>
                        @endif

                        {{-- Descuento --}}
                        @if ($descuento > 0)
                            <div class="flex justify-between items-center border-t border-slate-700 pt-2.5">
                                <span class="text-emerald-400">Descuento</span>
                                <span class="font-medium text-emerald-400">-${{ number_format($descuento, 2) }}</span>
                            </div>
                        @endif

                        {{-- Total --}}
                        <div class="flex justify-between items-center bg-blue-500/[0.08] border border-blue-500/20 rounded-xl px-3 py-3 mt-1">
                            <span class="text-slate-300 font-bold text-sm">TOTAL A PAGAR</span>
                            <span class="text-2xl font-bold text-blue-400">${{ number_format($montoFinal, 2) }}</span>
                        </div>

                        {{-- Badge estado --}}
                        <div class="pt-1">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/25">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                                Pendiente de pago
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ================================================================
             COLUMNA DERECHA: Formulario de pago
             ================================================================ --}}
            <div class="lg:col-span-3">
                <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden shadow-lg">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-5 py-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-white font-semibold text-sm uppercase tracking-wide">Registrar Pago</h3>
                    </div>
                    <div class="p-6 space-y-6">

                        {{-- Método de pago --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">
                                Método de Pago <span class="text-red-400">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach (['Transferencia', 'Deposito', 'Efectivo'] as $metodo)
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model="metodoPago" value="{{ $metodo }}" class="sr-only peer">
                                        <div class="border-2 rounded-xl p-3 text-center text-xs font-semibold transition-all
                                                peer-checked:border-blue-500 peer-checked:bg-blue-500/15 peer-checked:text-blue-400
                                                hover:border-slate-500 text-slate-400 border-slate-600 bg-slate-700/50">
                                            {{ $metodo }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('metodoPago')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Referencia --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-1">
                                N° Referencia / Transacción
                                <span class="font-normal text-slate-500 text-xs">(opcional)</span>
                            </label>
                            <input type="text" wire:model="referencia"
                                class="w-full rounded-xl bg-slate-700 border-slate-600 text-slate-200 placeholder-slate-500
                                       focus:border-blue-500 focus:ring-blue-500/20 text-sm"
                                placeholder="Ej: 00012345678">
                            @error('referencia')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Comprobante --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-1">
                                Comprobante de Pago
                                <span class="font-normal text-slate-500 text-xs">(PDF o imagen, máx. 5MB)</span>
                            </label>
                            <div class="mt-1 border-2 border-dashed border-slate-600 rounded-xl p-6 text-center
                                    hover:border-blue-500/50 transition-colors cursor-pointer bg-slate-700/30"
                                x-data @click="$refs.fileInput.click()">
                                @if ($comprobante)
                                    <svg class="w-8 h-8 mx-auto text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="font-semibold text-emerald-400 text-sm">{{ $comprobante->getClientOriginalName() }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ round($comprobante->getSize() / 1024, 1) }} KB — clic para cambiar</p>
                                @else
                                    <svg class="mx-auto h-10 w-10 text-slate-500 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="text-sm text-slate-400">
                                        <span class="text-blue-400 font-medium">Seleccionar archivo</span>
                                        <span> o arrastrar aquí</span>
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">PDF, JPG, PNG, WEBP</p>
                                @endif
                                <input x-ref="fileInput" type="file" wire:model="comprobante"
                                    accept=".pdf,.jpg,.jpeg,.png,.webp" class="hidden">
                            </div>
                            <div wire:loading wire:target="comprobante" class="mt-1 text-xs text-blue-400 flex items-center gap-1">
                                <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Cargando archivo...
                            </div>
                            @error('comprobante')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Observaciones --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-1">
                                Observaciones
                                <span class="font-normal text-slate-500 text-xs">(opcional)</span>
                            </label>
                            <textarea wire:model="descripcion" rows="3"
                                class="w-full rounded-xl bg-slate-700 border-slate-600 text-slate-200 placeholder-slate-500
                                       focus:border-blue-500 focus:ring-blue-500/20 text-sm"
                                placeholder="Información adicional sobre el pago..."></textarea>
                        </div>

                        {{-- Aviso --}}
                        <div class="bg-blue-500/[0.08] border border-blue-500/20 rounded-xl p-3.5 flex items-start gap-2.5 text-sm text-blue-300">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p>El pago quedará en estado <strong class="text-white">Aprobado</strong> inmediatamente ya que la administración está registrando el pago directamente.</p>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-between pt-2 border-t border-slate-700">
                            <a href="{{ route('administracion.administrativa.matriculacion.index') }}"
                                class="text-sm text-slate-400 hover:text-slate-200 transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Volver
                            </a>
                            <button type="button" wire:click="guardarPago" wire:loading.attr="disabled"
                                class="inline-flex items-center px-6 py-2.5 text-sm font-semibold rounded-xl
                                       text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500
                                       shadow-lg shadow-blue-500/20 transition disabled:opacity-50">
                                <span wire:loading.remove wire:target="guardarPago" class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Registrar Pago
                                </span>
                                <span wire:loading wire:target="guardarPago" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                    </svg>
                                    Procesando...
                                </span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
