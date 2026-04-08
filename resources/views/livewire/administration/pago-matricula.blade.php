<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="mb-6">
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('administracion.administrativa.matriculacion.index') }}"
                    class="text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Pago de Matrícula</h2>
            </div>
            <p class="text-gray-500 text-sm">
                Registre el comprobante de pago para completar la matrícula. La administración verificará y aprobará el
                pago.
            </p>
        </div>

        {{-- ALERTA DE ERROR GENERAL --}}
        @error('general')
            <div class="mb-4 bg-red-50 border border-red-300 rounded-lg p-4">
                <p class="text-sm text-red-700">{{ $message }}</p>
            </div>
        @enderror

        @if (session('info'))
            <div class="mb-4 bg-blue-50 border border-blue-300 rounded-lg p-4">
                <p class="text-sm text-blue-700">{{ session('info') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- ================================================================
             COLUMNA IZQUIERDA: Resumen de la matrícula
             ================================================================ --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Datos del Estudiante --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3">
                        <h3 class="text-white font-semibold text-sm uppercase tracking-wide">Estudiante</h3>
                    </div>
                    <div class="p-4 space-y-2 text-sm">
                        <div>
                            <p class="text-gray-500">Nombre</p>
                            <p class="font-semibold text-gray-900">{{ $matricula->estudiante->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Cédula</p>
                            <p class="font-medium text-gray-900">{{ $matricula->estudiante->cedula }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">N° Matrícula</p>
                            <p class="font-medium text-gray-900">{{ $matricula->estudiante->matricula_numero }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Carrera</p>
                            <p class="font-medium text-gray-900">{{ $matricula->carrera->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Período</p>
                            <p class="font-medium text-gray-900">{{ $matricula->periodo->code }} -
                                {{ $matricula->periodo->description }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Código Matrícula</p>
                            <p class="font-mono font-semibold text-blue-700">{{ $matricula->code }}</p>
                        </div>
                    </div>
                </div>

                {{-- Desglose de Costos --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3">
                        <h3 class="text-white font-semibold text-sm uppercase tracking-wide">Desglose de Pago</h3>
                    </div>
                    <div class="p-4 space-y-3 text-sm">

                        {{-- Matrícula base --}}
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Matrícula</span>
                            <span class="font-medium text-gray-900">${{ number_format($montoMatricula, 2) }}</span>
                        </div>

                        {{-- Materias de arrastre --}}
                        @if (!empty($materiasArrastre))
                            <div class="border-t border-gray-100 pt-3">
                                <p class="text-gray-500 text-xs font-semibold uppercase mb-2">Materias de Arrastre
                                    (+10%)</p>
                                @foreach ($materiasArrastre as $arrastre)
                                    <div class="flex justify-between items-center py-1">
                                        <div>
                                            <p class="text-gray-700">{{ $arrastre['nombre'] }}</p>
                                            <p class="text-gray-400 text-xs">{{ $arrastre['creditos'] }} créditos</p>
                                        </div>
                                        <span class="font-medium text-yellow-700">
                                            ${{ number_format($arrastre['costo_adicional'], 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Descuento --}}
                        @if ($descuento > 0)
                            <div class="flex justify-between items-center border-t border-gray-100 pt-3">
                                <span class="text-green-600">Descuento</span>
                                <span class="font-medium text-green-600">-${{ number_format($descuento, 2) }}</span>
                            </div>
                        @endif

                        {{-- Total --}}
                        <div class="flex justify-between items-center border-t-2 border-gray-300 pt-3">
                            <span class="text-gray-900 font-bold text-base">TOTAL A PAGAR</span>
                            <span class="text-xl font-bold text-blue-700">${{ number_format($montoFinal, 2) }}</span>
                        </div>

                        {{-- Badge estado --}}
                        <div class="pt-1">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
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
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gray-800 px-4 py-3">
                        <h3 class="text-white font-semibold text-sm uppercase tracking-wide">Registrar Pago</h3>
                    </div>
                    <div class="p-6 space-y-5">

                        {{-- Método de pago --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Método de Pago <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach (['Transferencia', 'Deposito', 'Efectivo', /* 'Tarjeta', */ 'Payphone'] as $metodo)
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model="metodoPago" value="{{ $metodo }}"
                                            class="sr-only peer">
                                        <div
                                            class="border-2 rounded-lg p-3 text-center text-sm font-medium transition-all
                                                peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700
                                                hover:border-blue-300 text-gray-600 border-gray-200">
                                            {{ $metodo }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('metodoPago')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Referencia / N° Comprobante externo --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Número de Referencia / Transacción
                                <span class="font-normal text-gray-400">(opcional)</span>
                            </label>
                            <input type="text" wire:model="referencia"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="Ej: 00012345678">
                            @error('referencia')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Comprobante --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Comprobante de Pago
                                <span class="font-normal text-gray-400">(PDF o imagen, máx. 5MB)</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg
                                    hover:border-blue-400 transition-colors cursor-pointer"
                                x-data @click="$refs.fileInput.click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="text-sm text-gray-600">
                                        @if ($comprobante)
                                            <p class="font-semibold text-blue-600">
                                                {{ $comprobante->getClientOriginalName() }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ round($comprobante->getSize() / 1024, 1) }} KB</p>
                                        @else
                                            <span class="text-blue-600 font-medium">Seleccionar archivo</span>
                                            <span class="text-gray-500"> o arrastrar aquí</span>
                                        @endif
                                    </div>
                                </div>
                                <input x-ref="fileInput" type="file" wire:model="comprobante"
                                    accept=".pdf,.jpg,.jpeg,.png.,.webp" class="hidden">
                            </div>
                            @error('comprobante')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descripción adicional --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Observaciones
                                <span class="font-normal text-gray-400">(opcional)</span>
                            </label>
                            <textarea wire:model="descripcion" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="Información adicional sobre el pago..."></textarea>
                        </div>

                        {{-- Aviso --}}
                        <div
                            class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800 flex items-start space-x-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p>El pago quedará en estado <strong>Aprobado</strong> ya que la adiministracion es la que
                                esta habilitando el pago y la matricula</p>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <a href="{{ route('administracion.administrativa.matriculacion.index') }}"
                                class="text-sm text-gray-500 hover:text-gray-700 transition">
                                ← Volver a matrículas
                            </a>
                            <button type="button" wire:click="guardarPago" wire:loading.attr="disabled"
                                class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-lg
                                       text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                       disabled:opacity-50 disabled:cursor-not-allowed transition">
                                <span wire:loading.remove wire:target="guardarPago">
                                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Registrar Pago
                                </span>
                                <span wire:loading wire:target="guardarPago" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
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
