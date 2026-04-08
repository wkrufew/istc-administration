<div>
    <div>

        {{-- TOAST --}}
        <div x-data="{ toasts: [] }"
            x-on:toast.window="toasts.push($event.detail[0]); setTimeout(() => toasts.shift(), 4000)"
            class="fixed top-4 right-4 z-50 space-y-2" style="z-index:99999">
            <template x-for="(t, i) in toasts" :key="i">
                <div x-show="true" x-transition
                    :class="{ 'bg-green-600': t.tipo==='success', 'bg-red-600': t.tipo==='error', 'bg-amber-500': t.tipo==='warning' }"
                    class="text-white px-5 py-3 rounded-lg shadow-lg text-sm min-w-72">
                    <span x-text="t.mensaje"></span>
                </div>
            </template>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

            {{-- HEADER --}}
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Mis Pagos y Obligaciones</h2>
                <p class="text-gray-500 text-sm mt-1">Consulta el estado de tus obligaciones y registra tus pagos</p>
            </div>

            {{-- TABLERO DE MÉTRICAS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Deuda del Periodo</p>
                        <span class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </span>
                    </div>
                    <p class="text-3xl font-bold {{ $deudaPeriodoActual > 0 ? 'text-red-600' : 'text-green-600' }}">
                        ${{ number_format($deudaPeriodoActual, 2) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Saldo pendiente por pagar</p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pagado este Periodo</p>
                        <span class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="flex space-x-4">
                        <p class="text-3xl font-bold text-green-600">${{ number_format($totalPagadoPeriodo, 2) }}</p>
                        {{-- tambien cuales  --}}
                    </div>
                    {{-- <p class="text-3xl font-bold text-green-600">${{ number_format($totalPagadoPeriodo, 2) }}</p> --}}
                    <p class="text-xs text-gray-400 mt-1">Total abonado en pagos aprobados</p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Saldo de Carrera</p>
                        <span class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-blue-600">${{ number_format($saldoCarrera, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Costo de carrera menos lo abonado</p>
                </div>

            </div>

            {{-- FILTROS --}}
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Periodo</label>
                        <select wire:model.live="filtroPeriodo"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Todos los periodos</option>
                            @foreach ($periodos as $periodo)
                                <option value="{{ $periodo->id }}">
                                    {{ $periodo->code }}{{ $periodo->is_current ? ' (Actual)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Tipo</label>
                        <select wire:model.live="filtroTipo"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Todos</option>
                            @foreach (['MATRICULA', 'COLEGIATURA', 'ARRASTRE', 'MULTA', 'OTROS'] as $tipo)
                                <option value="{{ $tipo }}">{{ ucfirst(strtolower($tipo)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Estado</label>
                        <select wire:model.live="filtroEstado"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Todos</option>
                            @foreach (['Pendiente', 'Parcial', 'Pagado', 'Vencido'] as $estado)
                                <option value="{{ $estado }}">{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- LISTA DE OBLIGACIONES EN CARDS --}}
            <div class="space-y-3">
                @forelse ($obligaciones as $ob)
                    @php
                        $tipoColors = [
                            'MATRICULA' => 'bg-blue-100 text-blue-800',
                            'COLEGIATURA' => 'bg-purple-100 text-purple-800',
                            'ARRASTRE' => 'bg-yellow-100 text-yellow-800',
                            'MULTA' => 'bg-red-100 text-red-800',
                            'OTROS' => 'bg-gray-100 text-gray-700',
                        ];
                        $estadoColors = [
                            'Pendiente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'Parcial' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'Pagado' => 'bg-green-100 text-green-800 border-green-200',
                            'Vencido' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                        $porcentaje = min(100, $ob->monto_final > 0 ? ($ob->total_pagado / $ob->monto_final) * 100 : 0);
                    @endphp

                    <div
                        class="bg-white rounded-2xl border {{ $ob->estado === 'Vencido' ? 'border-red-200' : 'border-gray-200' }} shadow-sm p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                            {{-- Info --}}
                            <div class="flex-1 space-y-2">
                                <div class="flex items-center flex-wrap gap-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $tipoColors[$ob->tipo] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $ob->tipo }}
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $estadoColors[$ob->estado] ?? '' }}">
                                        {{ $ob->estado }}
                                    </span>
                                    @if ($ob->fecha_vencimiento && $ob->fecha_vencimiento->isPast() && $ob->estado !== 'Pagado')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-600 text-white">
                                            ¡Vencida!
                                        </span>
                                    @endif
                                </div>
                                <p class="font-semibold text-gray-900">
                                    {{ $ob->periodo->code }}
                                    @if ($ob->matricula)
                                        <span class="text-gray-400 font-normal text-sm">—
                                            {{ $ob->matricula->code }}</span>
                                    @endif
                                </p>
                                @if ($ob->descripcion)
                                    <p class="text-sm text-gray-500">{{ $ob->descripcion }}</p>
                                @endif
                                @if ($ob->fecha_vencimiento)
                                    <p class="text-xs text-gray-400">Vence:
                                        {{ $ob->fecha_vencimiento->format('d/m/Y') }}</p>
                                @endif
                            </div>

                            {{-- Montos --}}
                            <div class="flex sm:flex-col items-center sm:items-end gap-4 sm:gap-1 sm:min-w-32">
                                <div class="text-center sm:text-right">
                                    <p class="text-xs text-gray-400">Total</p>
                                    <p class="font-semibold text-gray-700">${{ number_format($ob->monto_final, 2) }}
                                    </p>
                                </div>
                                <div class="text-center sm:text-right">
                                    <p class="text-xs text-gray-400">Pagado</p>
                                    <p class="font-semibold text-green-600">${{ number_format($ob->total_pagado, 2) }}
                                    </p>
                                </div>
                                <div class="text-center sm:text-right">
                                    <p class="text-xs text-gray-400">Saldo</p>
                                    <p
                                        class="font-bold text-lg {{ $ob->saldo > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ${{ number_format(max(0, $ob->saldo), 2) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Acciones --}}
                            <div class="flex sm:flex-col gap-2 sm:min-w-28">
                                @if ($ob->estado !== 'Pagado')
                                    <button wire:click="abrirModalPago({{ $ob->id }})"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2
                                           text-sm font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white transition shadow-sm">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Pagar
                                    </button>
                                @endif
                                @if ($ob->pagos->count() > 0)
                                    <button wire:click="abrirHistorial({{ $ob->id }})"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2
                                           text-sm font-semibold rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        Cuotas ({{ $ob->pagos->count() }})
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Barra de progreso --}}
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>Progreso de pago</span>
                                <span>{{ number_format($porcentaje, 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500
                                        {{ $porcentaje >= 100 ? 'bg-green-500' : ($porcentaje > 0 ? 'bg-blue-500' : 'bg-gray-300') }}"
                                    style="width: {{ $porcentaje }}%"></div>
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-12 text-center text-gray-400">
                        <svg class="w-14 h-14 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="font-semibold text-gray-500">No tienes obligaciones registradas</p>
                        <p class="text-sm mt-1">Cuando tengas obligaciones pendientes aparecerán aquí</p>
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            <div>{{ $obligaciones->links() }}</div>


            {{-- MODAL: REGISTRAR PAGO --}}
            @if ($showModalPago && $obligacionSeleccionada)
                <div class="fixed inset-0 z-50 overflow-y-auto" style="z-index:99999">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-gray-900 bg-opacity-60" wire:click="cerrarModalPago"></div>
                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg">

                            <div class="bg-gray-800 px-6 py-4 rounded-t-2xl flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold">Registrar Pago</h3>
                                    <p class="text-gray-400 text-xs mt-0.5">
                                        {{ $obligacionSeleccionada->tipo }} —
                                        {{ $obligacionSeleccionada->periodo->code }}
                                    </p>
                                </div>
                                <button wire:click="cerrarModalPago"
                                    class="text-gray-400 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-6 space-y-5">

                                {{-- Resumen --}}
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <div class="grid grid-cols-3 gap-3 text-center text-sm">
                                        <div>
                                            <p class="text-gray-400 text-xs">Total</p>
                                            <p class="font-bold text-gray-800">
                                                ${{ number_format($obligacionSeleccionada->monto_final, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs">Pagado</p>
                                            <p class="font-bold text-green-600">
                                                ${{ number_format($obligacionSeleccionada->total_pagado, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs">Saldo</p>
                                            <p class="font-bold text-red-600">
                                                ${{ number_format($obligacionSeleccionada->saldo, 2) }}</p>
                                        </div>
                                    </div>
                                </div>

                                @error('pago_general')
                                    <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg p-3">
                                        {{ $message }}</p>
                                @enderror

                                {{-- Monto --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Monto a Pagar <span class="text-red-500">*</span>
                                        <span class="font-normal text-gray-400 text-xs">(puede ser parcial)</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute inset-y-0 left-3 flex items-center text-gray-500 font-medium">$</span>
                                        <input type="number" wire:model="montoPago" step="0.01" min="0.01"
                                            max="{{ $obligacionSeleccionada->saldo }}"
                                            class="w-full pl-8 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    @error('montoPago')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Método de pago --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Método de Pago <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach (['Transferencia', 'Deposito', 'Efectivo', 'Tarjeta', 'Payphone'] as $metodo)
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="metodoPago"
                                                    value="{{ $metodo }}" class="sr-only peer">
                                                <div
                                                    class="border-2 rounded-xl p-2.5 text-center text-xs font-semibold transition-all
                                                        peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700
                                                        hover:border-blue-300 text-gray-600 border-gray-200">
                                                    {{ $metodo }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Referencia --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        N° Referencia / Transacción
                                        <span class="font-normal text-gray-400 text-xs">(opcional)</span>
                                    </label>
                                    <input type="text" wire:model="referencia"
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="Ej: 00012345678">
                                </div>

                                {{-- Comprobante con drag area --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Comprobante de Pago <span class="text-red-500">*</span>
                                        <span class="font-normal text-gray-400 text-xs">(PDF o imagen, máx. 5MB)</span>
                                    </label>
                                    <div x-data
                                        class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center
                                           hover:border-blue-400 transition cursor-pointer"
                                        @click="$refs.fileInput.click()">
                                        @if ($comprobante)
                                            <div class="text-sm">
                                                <svg class="w-8 h-8 mx-auto text-green-500 mb-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="font-semibold text-green-600">
                                                    {{ $comprobante->getClientOriginalName() }}</p>
                                                <p class="text-gray-400 text-xs mt-1">
                                                    {{ round($comprobante->getSize() / 1024, 1) }} KB — clic para
                                                    cambiar</p>
                                            </div>
                                        @else
                                            <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="text-sm text-gray-500">
                                                <span class="text-blue-600 font-medium">Seleccionar archivo</span> o
                                                arrastra aquí
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG</p>
                                        @endif
                                        <input x-ref="fileInput" type="file" wire:model="comprobante"
                                            accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                    </div>
                                    <div wire:loading wire:target="comprobante"
                                        class="mt-1 text-xs text-blue-600 flex items-center">
                                        <svg class="animate-spin w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        Cargando archivo...
                                    </div>
                                    @error('comprobante')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Observaciones --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Observaciones <span class="font-normal text-gray-400 text-xs">(opcional)</span>
                                    </label>
                                    <textarea wire:model="descripcionPago" rows="2"
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="Información adicional sobre tu pago..."></textarea>
                                </div>

                                {{-- Aviso de verificación --}}
                                <div
                                    class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start space-x-2 text-sm text-amber-800">
                                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p>Tu pago quedará <strong>pendiente de verificación</strong>. La secretaría
                                        revisará tu comprobante y lo aprobará en breve.</p>
                                </div>

                                {{-- Footer --}}
                                <div class="flex justify-end space-x-3 pt-2 border-t border-gray-100">
                                    <button wire:click="cerrarModalPago"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                                        Cancelar
                                    </button>
                                    <button wire:click="guardarPago" wire:loading.attr="disabled"
                                        class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition disabled:opacity-50">
                                        <span wire:loading.remove wire:target="guardarPago">Enviar Pago</span>
                                        <span wire:loading wire:target="guardarPago" class="flex items-center">
                                            <svg class="animate-spin w-4 h-4 mr-2" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4" />
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                            </svg>
                                            Enviando...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            {{-- MODAL: HISTORIAL DE CUOTAS --}}
            @if ($showModalHistorial && $obligacionHistorial)
                <div class="fixed inset-0 z-50 overflow-y-auto" style="z-index:99999">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-gray-900 bg-opacity-60" wire:click="cerrarHistorial"></div>
                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl">

                            <div class="bg-gray-800 px-6 py-4 rounded-t-2xl flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold">Historial de Cuotas</h3>
                                    <p class="text-gray-400 text-xs mt-0.5">
                                        {{ $obligacionHistorial->tipo }} — {{ $obligacionHistorial->periodo->code }}
                                    </p>
                                </div>
                                <button wire:click="cerrarHistorial"
                                    class="text-gray-400 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Resumen con barra --}}
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total</p>
                                        <p class="text-xl font-bold text-gray-900">
                                            ${{ number_format($obligacionHistorial->monto_final, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Pagado</p>
                                        <p class="text-xl font-bold text-green-600">
                                            ${{ number_format($obligacionHistorial->total_pagado, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Saldo</p>
                                        <p
                                            class="text-xl font-bold {{ $obligacionHistorial->saldo > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            ${{ number_format(max(0, $obligacionHistorial->saldo), 2) }}
                                        </p>
                                    </div>
                                </div>
                                @php $pct = min(100, $obligacionHistorial->monto_final > 0 ? ($obligacionHistorial->total_pagado / $obligacionHistorial->monto_final) * 100 : 0); @endphp
                                <div class="mt-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $pct >= 100 ? 'bg-green-500' : 'bg-blue-500' }} transition-all"
                                            style="width: {{ $pct }}%"></div>
                                    </div>
                                    <p class="text-right text-xs text-gray-400 mt-1">{{ number_format($pct, 0) }}%
                                        pagado</p>
                                </div>
                            </div>

                            {{-- Lista con scroll --}}
                            <div class="overflow-y-auto max-h-96 px-6 py-4 space-y-3">
                                @forelse ($obligacionHistorial->pagos as $pago)
                                    @php
                                        $estilos = [
                                            'Pendiente' => [
                                                'bg' => 'bg-yellow-50 border-yellow-200',
                                                'badge' => 'bg-yellow-100 text-yellow-700',
                                                'icono' => '⏳',
                                            ],
                                            'Aprobado' => [
                                                'bg' => 'bg-green-50 border-green-200',
                                                'badge' => 'bg-green-100 text-green-700',
                                                'icono' => '✓',
                                            ],
                                            'Rechazado' => [
                                                'bg' => 'bg-red-50 border-red-200',
                                                'badge' => 'bg-red-100 text-red-700',
                                                'icono' => '✗',
                                            ],
                                            'Procesando' => [
                                                'bg' => 'bg-blue-50 border-blue-200',
                                                'badge' => 'bg-blue-100 text-blue-700',
                                                'icono' => '🔄',
                                            ],
                                        ][$pago->estado] ?? [
                                            'bg' => 'bg-gray-50 border-gray-200',
                                            'badge' => 'bg-gray-100 text-gray-700',
                                            'icono' => '—',
                                        ];
                                    @endphp
                                    <div class="border rounded-xl p-4 {{ $estilos['bg'] }}">
                                        <div class="flex items-start justify-between">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    @if ($pago->numero_cuota)
                                                        <span
                                                            class="text-xs font-bold bg-gray-800 text-white px-2 py-0.5 rounded-full">
                                                            Cuota #{{ $pago->numero_cuota }}
                                                        </span>
                                                    @endif
                                                    <span
                                                        class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $estilos['badge'] }}">
                                                        {{ $estilos['icono'] }} {{ $pago->estado }}
                                                    </span>
                                                </div>
                                                <p class="font-mono text-xs text-gray-500">
                                                    {{ $pago->numero_comprobante }}</p>
                                                <p class="text-xs text-gray-600">
                                                    <span class="font-medium">{{ $pago->metodo_pago }}</span>
                                                    — {{ $pago->fecha_pago->format('d/m/Y H:i') }}
                                                </p>
                                                @if ($pago->codigo_referencia)
                                                    <p class="text-xs text-gray-400">Ref:
                                                        {{ $pago->codigo_referencia }}</p>
                                                @endif
                                                @if ($pago->descripcion)
                                                    <p class="text-xs text-gray-400 italic">{{ $pago->descripcion }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right space-y-2 ml-4 flex-shrink-0">
                                                <p class="text-xl font-bold text-gray-900">
                                                    ${{ number_format($pago->monto, 2) }}</p>
                                                @if ($pago->comprobante_path)
                                                    <a href="{{ asset('storage/' . $pago->comprobante_path) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        Ver comprobante
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-400 text-sm">No hay cuotas registradas aún
                                    </div>
                                @endforelse
                            </div>

                            <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                                <p class="text-xs text-gray-400">{{ $obligacionHistorial->pagos->count() }} cuota(s)
                                    registrada(s)</p>
                                <button wire:click="cerrarHistorial"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
