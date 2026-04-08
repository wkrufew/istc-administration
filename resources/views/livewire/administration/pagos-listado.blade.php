<div {{-- class="p-4 sm:p-6" --}}>
    {{-- Header --}}
    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Gestión de Pagos
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Busca matrículas, revisa saldos y procesa pagos de forma rápida.
                </p>
            </div>

            {{-- Estado filtro (mini badge arriba) --}}
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    bg-gray-100 text-gray-700 border border-gray-200
                    dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700">
                    Filtro:
                    <span class="ml-2 font-bold text-lime-600 dark:text-lime-400">
                        {{ $estado_filtro }}
                    </span>
                </span>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-3">
            {{-- Buscar --}}
            <div class="md:col-span-2">
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                    Buscar
                </label>
                <div class="relative mt-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.75 3.75a7.5 7.5 0 0012.9 12.9z" />
                        </svg>
                    </span>

                    <input type="text" wire:model.live="busqueda" placeholder="Buscar estudiante o matrícula..."
                        class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                        bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                        placeholder:text-gray-400 dark:placeholder:text-gray-500
                        focus:outline-none focus:ring-2 focus:ring-lime-500/60 focus:border-lime-500 transition">
                </div>
            </div>

            {{-- Estado --}}
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                    Estado del pago
                </label>
                <select wire:model.live="estado_filtro"
                    class="mt-1 w-full py-2.5 px-3 rounded-xl border border-gray-200 dark:border-gray-700
                    bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                    focus:outline-none focus:ring-2 focus:ring-lime-500/60 focus:border-lime-500 transition">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Procesando">Procesando</option>
                    <option value="Aprobado">Aprobado</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Tabla Desktop --}}
    <div class="hidden lg:block">
        <div
            class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr class="text-gray-700 dark:text-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Código</th>
                        <th class="px-4 py-3 text-left font-semibold">Estudiante</th>
                        <th class="px-4 py-3 text-left font-semibold">Carrera</th>
                        <th class="px-4 py-3 text-right font-semibold">Total</th>
                        <th class="px-4 py-3 text-right font-semibold">Pagado</th>
                        <th class="px-4 py-3 text-right font-semibold">Saldo</th>
                        <th class="px-4 py-3 text-center font-semibold">Estado</th>
                        <th class="px-4 py-3 text-center font-semibold">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($matriculas as $matricula)
                        @php
                            $totalPagado = $matricula->pagos->where('estado', 'Aprobado')->sum('monto');
                            $saldo = $matricula->total_pagar - $totalPagado;
                        @endphp

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">
                                {{ $matricula->code }}
                            </td>

                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                {{ $matricula->estudiante->name }}
                            </td>

                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $matricula->carrera->name ?? '—' }}
                            </td>

                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-gray-100">
                                ${{ number_format($matricula->total_pagar, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right font-semibold text-green-600 dark:text-green-400">
                                ${{ number_format($totalPagado, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right font-semibold text-red-600 dark:text-red-400">
                                ${{ number_format($saldo, 2) }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($matricula->status === 'Pagada')
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold
                                        bg-green-100 text-green-800 border border-green-200
                                        dark:bg-green-900/40 dark:text-green-200 dark:border-green-800">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        Pagada
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold
                                        bg-yellow-100 text-yellow-800 border border-yellow-200
                                        dark:bg-yellow-900/40 dark:text-yellow-200 dark:border-yellow-800">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                        {{ $matricula->status }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <button wire:click="abrirModalPago({{ $matricula->id }})"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl
                                    bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v12m6-6H6" />
                                    </svg>
                                    Procesar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                                No hay matrículas encontradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Cards Mobile (mejor UX que tabla en celular) --}}
    <div class="grid grid-cols-1 gap-4 lg:hidden">
        @forelse($matriculas as $matricula)
            @php
                $totalPagado = $matricula->pagos->where('estado', 'Aprobado')->sum('monto');
                $saldo = $matricula->total_pagar - $totalPagado;
            @endphp

            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Matrícula</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ $matricula->code }}
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            {{ $matricula->estudiante->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $matricula->carrera->name ?? '—' }}
                        </div>
                    </div>

                    <div>
                        @if ($matricula->status === 'Pagada')
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold
                                bg-green-100 text-green-800 border border-green-200
                                dark:bg-green-900/40 dark:text-green-200 dark:border-green-800">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                Pagada
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold
                                bg-yellow-100 text-yellow-800 border border-yellow-200
                                dark:bg-yellow-900/40 dark:text-yellow-200 dark:border-yellow-800">
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                {{ $matricula->status }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div
                        class="rounded-xl bg-gray-50 dark:bg-gray-800/60 p-3 border border-gray-200 dark:border-gray-700">
                        <div class="text-[11px] text-gray-500 dark:text-gray-400">Total</div>
                        <div class="font-bold text-gray-900 dark:text-gray-100">
                            ${{ number_format($matricula->total_pagar, 2) }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl bg-green-50 dark:bg-green-900/20 p-3 border border-green-200 dark:border-green-800">
                        <div class="text-[11px] text-green-700 dark:text-green-300">Pagado</div>
                        <div class="font-bold text-green-700 dark:text-green-300">
                            ${{ number_format($totalPagado, 2) }}
                        </div>
                    </div>

                    <div class="rounded-xl bg-red-50 dark:bg-red-900/20 p-3 border border-red-200 dark:border-red-800">
                        <div class="text-[11px] text-red-700 dark:text-red-300">Saldo</div>
                        <div class="font-bold text-red-700 dark:text-red-300">
                            ${{ number_format($saldo, 2) }}
                        </div>
                    </div>
                </div>

                <button wire:click="abrirModalPago({{ $matricula->id }})"
                    class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl
                    bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                    </svg>
                    Procesar Pago
                </button>
            </div>
        @empty
            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 text-center text-gray-500 dark:text-gray-400">
                No hay matrículas encontradas.
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if ($matriculas->hasPages())
        <div class="mt-5">
            {{ $matriculas->links() }}
        </div>
    @endif

    {{-- MODAL --}}
    @if ($mostrarModal)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4 py-6"
            style="margin:0 !important;">
            <div
                class="w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                        Registrar Pago
                    </h2>

                    <button wire:click="$set('mostrarModal', false)"
                        class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition">
                        ✕
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-3 max-h-[75vh] overflow-y-auto">
                    <div class="text-sm text-gray-700 dark:text-gray-200 space-y-1">
                        <p><span class="font-semibold">Estudiante:</span>
                            {{ $matriculaSeleccionada->estudiante->name }}</p>
                        <p><span class="font-semibold">Total a pagar:</span>
                            ${{ number_format($matriculaSeleccionada->total_pagar, 2) }}</p>
                        <p><span class="font-semibold">Total pagado:</span> ${{ number_format($total_pagado, 2) }}</p>
                        <p><span class="font-semibold">Saldo pendiente:</span>
                            ${{ number_format($saldo_pendiente, 2) }}</p>
                    </div>

                    @error('monto')
                        <div
                            class="bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-200 px-3 py-2 rounded-lg text-sm font-medium">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <input type="number" wire:model="monto" step="0.01" placeholder="Monto"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">

                        <select wire:model="concepto"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="MATRICULA">MATRÍCULA</option>
                            <option value="MATERIA">MATERIA</option>
                            <option value="SUPLETORIO">SUPLETORIO</option>
                        </select>

                        <select wire:model="metodo_pago"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="Transferencia">Transferencia</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Deposito">Depósito</option>
                            <option value="Payphone">Payphone</option>
                        </select>

                        <input type="text" wire:model="numero_comprobante" placeholder="Número de comprobante"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">

                        <input type="number" wire:model="numero_cuota" placeholder="N° de cuota (opcional)"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <textarea wire:model="descripcion" placeholder="Descripción del pago"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500"></textarea>

                    <input type="file" wire:model="comprobante"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">

                    @if ($comprobante)
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Archivo cargado: <span
                                class="font-semibold">{{ $comprobante->getClientOriginalName() }}</span>
                        </p>
                    @endif
                </div>

                {{-- Footer --}}
                <div
                    class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <button wire:click="$set('mostrarModal', false)"
                        class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white hover:opacity-90 transition">
                        Cancelar
                    </button>

                    <button wire:click="guardarPago"
                        class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                        Guardar Pago
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Alerts --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('alerta', ({
                type,
                message
            }) => {

                // Si ya usas SweetAlert en tu proyecto, aquí queda PERFECTO
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        title: type === 'success' ? 'Éxito' : 'Atención',
                        text: message,
                        confirmButtonColor: '#4f46e5'
                    })
                    return;
                }

                // Fallback
                alert(message);
            });
        });
    </script>
</div>
