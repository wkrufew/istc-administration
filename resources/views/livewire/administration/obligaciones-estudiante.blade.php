<div>
    <div class="max-w-7xl mx-auto px-1 sm:px-2 lg:px-4 py-2">

        {{-- ======================================================================
             TOAST NOTIFICATIONS
             ====================================================================== --}}
        <div x-data="{ toasts: [] }"
            x-on:toast.window="toasts.push($event.detail[0]); setTimeout(() => toasts.shift(), 4000)"
            class="fixed top-4 right-4 z-50 space-y-2" style="z-index:99999">
            <template x-for="(t, i) in toasts" :key="i">
                <div x-show="true" x-transition
                    :class="{
                        'bg-green-600': t.tipo === 'success',
                        'bg-red-600': t.tipo === 'error',
                        'bg-amber-500': t.tipo === 'warning',
                        'bg-blue-600': t.tipo === 'info',
                    }"
                    class="text-white px-5 py-3 rounded-lg shadow-lg text-sm flex items-center space-x-2 min-w-64">
                    <span x-text="t.mensaje"></span>
                </div>
            </template>
        </div>

        {{-- ======================================================================
             HEADER
             ====================================================================== --}}
        <div
            class="flex justify-between items-center bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl p-4 mb-6 shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Obligaciones Financieras</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gestión de pagos y verificación de comprobantes
                </p>
            </div>
            <div class="flex items-center space-x-3">
                {{-- Botón nueva obligación manual --}}
                <button wire:click="abrirModalObligacion"
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl
                           font-medium text-sm shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Obligación
                </button>
                <a href="{{ route('administracion.administrativa.matriculacion.index') }}"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Matrículas
                </a>
            </div>
        </div>

        {{-- Alerta: viene de una matrícula recién creada --}}
        @if ($filtroMatricula)
            <div class="mb-4 bg-blue-50 border border-blue-300 rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-blue-800 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Mostrando obligaciones de la matrícula recién creada.</span>
                </div>
                <button wire:click="limpiarFiltroMatricula"
                    class="text-blue-600 hover:text-blue-800 text-xs underline ml-4">
                    Ver todas
                </button>
            </div>
        @endif

        {{-- ======================================================================
             FILTROS
             ====================================================================== --}}
        <div
            class="bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl p-4 mb-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Estudiante</label>
                    <select wire:model.live="filtroEstudiante"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Todos los estudiantes</option>
                        @foreach ($estudiantes as $est)
                            <option value="{{ $est->id }}">{{ $est->name }} — {{ $est->cedula }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Tipo</label>
                    <select wire:model.live="filtroTipo"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Todos los tipos</option>
                        @foreach (['MATRICULA', 'COLEGIATURA', 'ARRASTRE', 'MULTA', 'OTROS'] as $tipo)
                            <option value="{{ $tipo }}">{{ ucfirst(strtolower($tipo)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Estado</label>
                    <select wire:model.live="filtroEstado"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Todos los estados</option>
                        @foreach (['Pendiente', 'Parcial', 'Pagado', 'Vencido'] as $estado)
                            <option value="{{ $estado }}">{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ======================================================================
             TABLA DE OBLIGACIONES
             ====================================================================== --}}
        <div
            class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="bg-gray-800 px-6 py-3">
                <h3 class="text-white font-semibold uppercase tracking-wide text-sm">Listado de Obligaciones</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Estudiante</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-left">Período / Matrícula</th>
                            <th class="px-4 py-3 text-right">Monto</th>
                            <th class="px-4 py-3 text-right">Pagado</th>
                            <th class="px-4 py-3 text-right">Saldo</th>
                            <th class="px-4 py-3 text-center">Vencimiento</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($obligaciones as $ob)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">

                                {{-- Estudiante --}}
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $ob->estudiante->name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $ob->estudiante->cedula }}</p>
                                </td>

                                {{-- Tipo --}}
                                <td class="px-4 py-3">
                                    @php
                                        $tipoColors = [
                                            'MATRICULA' => 'bg-blue-100 text-blue-800',
                                            'COLEGIATURA' => 'bg-purple-100 text-purple-800',
                                            'ARRASTRE' => 'bg-yellow-100 text-yellow-800',
                                            'MULTA' => 'bg-red-100 text-red-800',
                                            'OTROS' => 'bg-gray-100 text-gray-700',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $tipoColors[$ob->tipo] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $ob->tipo }}
                                    </span>
                                    @if ($ob->descripcion)
                                        <p class="text-gray-400 text-xs mt-0.5 max-w-xs truncate">
                                            {{ $ob->descripcion }}</p>
                                    @endif
                                </td>

                                {{-- Período / Matrícula --}}
                                <td class="px-4 py-3">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $ob->periodo->code }}</p>
                                    @if ($ob->matricula)
                                        <p class="text-gray-400 text-xs">{{ $ob->matricula->code }}</p>
                                    @endif
                                </td>

                                {{-- Monto --}}
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-gray-100">
                                    ${{ number_format($ob->monto_final, 2) }}
                                    @if ($ob->descuento > 0)
                                        <p class="text-green-600 text-xs">-${{ number_format($ob->descuento, 2) }}
                                            desc.</p>
                                    @endif
                                </td>

                                {{-- Total pagado --}}
                                <td class="px-4 py-3 text-right font-medium text-green-700">
                                    ${{ number_format($ob->total_pagado, 2) }}
                                </td>

                                {{-- Saldo --}}
                                <td
                                    class="px-4 py-3 text-right font-bold {{ $ob->saldo > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    ${{ number_format(max(0, $ob->saldo), 2) }}
                                </td>

                                {{-- Vencimiento --}}
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                    @if ($ob->fecha_vencimiento)
                                        <span
                                            class="{{ $ob->fecha_vencimiento->isPast() && $ob->estado !== 'Pagado' ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $ob->fecha_vencimiento->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $estadoColors = [
                                            'Pendiente' => 'bg-yellow-100 text-yellow-800',
                                            'Parcial' => 'bg-orange-100 text-orange-800',
                                            'Pagado' => 'bg-green-100 text-green-800',
                                            'Vencido' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $estadoColors[$ob->estado] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $ob->estado }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center space-x-2">

                                        {{-- Botón registrar pago --}}
                                        @if ($ob->estado !== 'Pagado')
                                            <button wire:click="abrirModalPago({{ $ob->id }})"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg
                                                       bg-blue-600 hover:bg-blue-700 text-white transition"
                                                title="Registrar pago">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Pagar
                                            </button>
                                        @endif

                                        {{-- Ver historial de cuotas --}}
                                        @if ($ob->pagos->count() > 0)
                                            <button wire:click="abrirHistorial({{ $ob->id }})"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg
                                                       bg-gray-200 hover:bg-gray-300 text-gray-700 transition"
                                                title="Ver historial de cuotas">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                Cuotas ({{ $ob->pagos->count() }})
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-12 text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium">No se encontraron obligaciones</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                {{ $obligaciones->links() }}
            </div>
        </div>


        {{-- ======================================================================
             MODAL: CREAR OBLIGACIÓN MANUAL (MULTA / OTROS)
             ====================================================================== --}}
        @if ($showModalObligacion)
            <div class="fixed inset-0 z-50 overflow-y-auto" style="z-index:99999">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-60" wire:click="cerrarModalObligacion"></div>

                    <div class="relative bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-lg">

                        {{-- Header --}}
                        <div class="bg-gray-800 px-6 py-4 rounded-t-xl flex justify-between items-center">
                            <h3 class="text-white font-semibold">Nueva Obligación Manual</h3>
                            <button wire:click="cerrarModalObligacion"
                                class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-5">

                            @error('obligacion_general')
                                <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded p-2">
                                    {{ $message }}</p>
                            @enderror

                            {{-- Buscador predictivo de estudiante --}}
                            <div x-data="{ open: @entangle('showDropdown') }" class="relative">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Estudiante <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" wire:model.live.debounce.300ms="busquedaEstudiante"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm pr-10
                                               {{ $estudianteSeleccionado ? 'border-green-400 bg-green-50' : '' }}"
                                        placeholder="Buscar por nombre o cédula (mín. 3 caracteres)..."
                                        autocomplete="off">

                                    {{-- Ícono check si ya está seleccionado --}}
                                    @if ($estudianteSeleccionado)
                                        <span class="absolute inset-y-0 right-3 flex items-center text-green-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>

                                {{-- Dropdown resultados --}}
                                @if ($showDropdown && $resultadosBusqueda->count() > 0)
                                    <div
                                        class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200
                                                dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        @foreach ($resultadosBusqueda as $resultado)
                                            <button type="button"
                                                wire:click="seleccionarEstudiante({{ $resultado->id }}, '{{ addslashes($resultado->name) }}', '{{ $resultado->cedula }}')"
                                                class="w-full text-left px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-gray-700
                                                       text-sm transition border-b border-gray-100 dark:border-gray-700 last:border-0">
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $resultado->name }}</p>
                                                <p class="text-gray-400 text-xs">Cédula: {{ $resultado->cedula }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif ($showDropdown && strlen($busquedaEstudiante) >= 3 && $resultadosBusqueda->count() === 0)
                                    <div
                                        class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200
                                                dark:border-gray-600 rounded-lg shadow-lg p-3 text-sm text-gray-500 text-center">
                                        No se encontraron estudiantes
                                    </div>
                                @endif

                                @error('estudianteSeleccionado')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tipo de obligación --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Tipo <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach (['MULTA' => 'bg-red-100 text-red-800', 'OTROS' => 'bg-gray-100 text-gray-700'] as $tipo => $color)
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="obligacionTipo"
                                                value="{{ $tipo }}" class="sr-only peer">
                                            <div
                                                class="border-2 rounded-lg p-3 text-center text-sm font-semibold transition-all
                                                        peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700
                                                        hover:border-blue-300 text-gray-600 border-gray-200">
                                                {{ $tipo }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Monto --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Monto <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-3 flex items-center text-gray-500 font-medium">$</span>
                                    <input type="number" wire:model="obligacionMonto" step="0.01" min="0.01"
                                        class="w-full pl-8 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="0.00">
                                </div>
                                @error('obligacionMonto')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Descripción --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Descripción <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="obligacionDescripcion" rows="3"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Ej: Multa por ausencia en evaluación parcial del 15/02/2026..."></textarea>
                                @error('obligacionDescripcion')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Fecha vencimiento --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Fecha de Vencimiento <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="obligacionVencimiento"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    min="{{ now()->format('Y-m-d') }}">
                                @error('obligacionVencimiento')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Footer --}}
                            <div class="flex justify-end space-x-3 pt-2 border-t border-gray-100">
                                <button wire:click="cerrarModalObligacion"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    Cancelar
                                </button>
                                <button wire:click="guardarObligacion" wire:loading.attr="disabled"
                                    class="px-5 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="guardarObligacion">Crear Obligación</span>
                                    <span wire:loading wire:target="guardarObligacion">Guardando...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        {{-- ======================================================================
             MODAL: REGISTRAR PAGO
             ====================================================================== --}}
        @if ($showModalPago && $obligacionSeleccionada)
            <div class="fixed inset-0 z-50 overflow-y-auto" style="z-index:99999">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-60" wire:click="cerrarModalPago"></div>

                    <div class="relative bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-lg">
                        <div class="bg-gray-800 px-6 py-4 rounded-t-xl flex justify-between items-center">
                            <h3 class="text-white font-semibold">
                                Registrar Pago —
                                <span class="text-blue-300">{{ $obligacionSeleccionada->tipo }}</span>
                            </h3>
                            <button wire:click="cerrarModalPago" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-5">

                            {{-- Resumen de la obligación --}}
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-sm space-y-1">
                                <p>
                                    <span class="font-semibold text-gray-800 dark:text-gray-300">Estudiante:</span>
                                    <span class="font-semibold text-gray-700 dark:text-gray-400">
                                        {{ $obligacionSeleccionada->estudiante->name }}
                                    </span>
                                </p>
                                @if ($obligacionSeleccionada->descripcion)
                                    <p>
                                        <span
                                            class="font-semibold text-gray-700 dark:text-gray-300">Descripción:</span>
                                        <span
                                            class="font-semibold text-gray-700 dark:text-gray-400">{{ $obligacionSeleccionada->descripcion }}</span>
                                    </p>
                                @endif
                                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-200 mt-2">
                                    <div class="text-center">
                                        <p class="text-gray-500 text-xs">Total</p>
                                        <p class="font-bold text-gray-900 dark:text-gray-300">
                                            ${{ number_format($obligacionSeleccionada->monto_final, 2) }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-gray-500 text-xs">Pagado</p>
                                        <p class="font-bold text-green-600">
                                            ${{ number_format($obligacionSeleccionada->total_pagado, 2) }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-gray-500 text-xs">Saldo</p>
                                        <p class="font-bold text-red-600">
                                            ${{ number_format($obligacionSeleccionada->saldo, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                            @error('pago_general')
                                <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded p-2">
                                    {{ $message }}</p>
                            @enderror

                            {{-- Monto de la cuota --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Monto a Pagar <span class="text-red-500">*</span>
                                    <span class="font-normal text-gray-400">(puede ser parcial)</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-3 flex items-center text-gray-500 font-medium">$</span>
                                    <input type="number" wire:model="montoPago" step="0.01" min="0.01"
                                        max="{{ $obligacionSeleccionada->saldo }}"
                                        class="w-full pl-8 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                @error('montoPago')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Método de pago --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Método de Pago <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach (['Transferencia', 'Deposito', 'Efectivo', 'Tarjeta', 'Payphone'] as $metodo)
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="metodoPago"
                                                value="{{ $metodo }}" class="sr-only peer">
                                            <div
                                                class="border-2 rounded-lg p-2 text-center text-xs font-medium transition-all
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
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    N° Referencia <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <input type="text" wire:model="referencia"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Ej: 00012345678">
                            </div>

                            {{-- Comprobante --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Comprobante <span class="text-gray-400 font-normal">(PDF / imagen, máx. 5MB)</span>
                                </label>
                                <input type="file" wire:model="comprobante" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-sm text-gray-600
                                           file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                                           file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100 transition">
                                @error('comprobante')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Observaciones --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Observaciones <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <textarea wire:model="descripcionPago" rows="2"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Información adicional sobre el pago..."></textarea>
                            </div>

                            {{-- Footer --}}
                            <div class="flex justify-end space-x-3 pt-2 border-t border-gray-100">
                                <button wire:click="cerrarModalPago"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    Cancelar
                                </button>
                                <button wire:click="guardarPago" wire:loading.attr="disabled"
                                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="guardarPago">Registrar Pago</span>
                                    <span wire:loading wire:target="guardarPago">Procesando...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        {{-- ======================================================================
             MODAL: VERIFICAR PAGO (pagos subidos por estudiante)
             ====================================================================== --}}
        @if ($showModalVerificacion && $pagoSeleccionado)
            <div class="fixed inset-0 z-50 overflow-y-auto" style="z-index:99999">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-60" wire:click="cerrarVerificacion"></div>

                    <div class="relative bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-md">
                        <div class="bg-gray-800 px-6 py-4 rounded-t-xl flex justify-between items-center">
                            <h3 class="text-white font-semibold">Verificar Comprobante</h3>
                            <button wire:click="cerrarVerificacion" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-sm space-y-1">
                                <p>
                                    <span class="font-semibold text-gray-600">Comprobante:</span>
                                    <span
                                        class="font-mono text-gray-900">{{ $pagoSeleccionado->numero_comprobante }}</span>
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-600">Estudiante:</span>
                                    {{ $pagoSeleccionado->obligacion->estudiante->name }}
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-600">Tipo:</span>
                                    {{ $pagoSeleccionado->obligacion->tipo }}
                                </p>
                                @if ($pagoSeleccionado->numero_cuota)
                                    <p>
                                        <span class="font-semibold text-gray-600">Cuota N°:</span>
                                        {{ $pagoSeleccionado->numero_cuota }}
                                    </p>
                                @endif
                                <p>
                                    <span class="font-semibold text-gray-600">Método:</span>
                                    {{ $pagoSeleccionado->metodo_pago }}
                                </p>
                                @if ($pagoSeleccionado->codigo_referencia)
                                    <p>
                                        <span class="font-semibold text-gray-600">Referencia:</span>
                                        {{ $pagoSeleccionado->codigo_referencia }}
                                    </p>
                                @endif
                                <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                                    <span class="font-semibold text-gray-700">Monto:</span>
                                    <span
                                        class="font-bold text-gray-900 text-base">${{ number_format($pagoSeleccionado->monto, 2) }}</span>
                                </div>
                                @if ($pagoSeleccionado->comprobante_path)
                                    <div class="pt-1">
                                        <a href="{{ asset('storage/' . $pagoSeleccionado->comprobante_path) }}"
                                            target="_blank"
                                            class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs font-medium underline">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Ver comprobante adjunto
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Observación
                                    <span class="font-normal text-gray-400">(requerida al rechazar)</span>
                                </label>
                                <textarea wire:model="observacionVerificacion" rows="3"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Ingrese el motivo si rechaza, o una nota al aprobar..."></textarea>
                                @error('observacionVerificacion')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <button wire:click="cerrarVerificacion"
                                    class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    Cancelar
                                </button>
                                <div class="flex space-x-2">
                                    <button wire:click="rechazarPago" wire:loading.attr="disabled"
                                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition disabled:opacity-50">
                                        <span wire:loading.remove wire:target="rechazarPago">✗ Rechazar</span>
                                        <span wire:loading wire:target="rechazarPago">...</span>
                                    </button>
                                    <button wire:click="aprobarPago" wire:loading.attr="disabled"
                                        class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg transition disabled:opacity-50">
                                        <span wire:loading.remove wire:target="aprobarPago">✓ Aprobar</span>
                                        <span wire:loading wire:target="aprobarPago">...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ======================================================================
             MODAL: HISTORIAL DE CUOTAS
             ====================================================================== --}}
        @if ($showModalHistorial && $obligacionHistorial)
            <div class="fixed inset-0 z-50 overflow-y-auto" style="z-index:99999">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-60" wire:click="cerrarHistorial"></div>

                    <div class="relative bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-2xl">

                        {{-- Header fijo --}}
                        <div class="bg-gray-800 px-6 py-4 rounded-t-xl flex justify-between items-center">
                            <div>
                                <h3 class="text-white font-semibold text-base">Historial de Cuotas</h3>
                                <p class="text-gray-400 text-xs mt-0.5">
                                    {{ $obligacionHistorial->estudiante->name }}
                                    — {{ $obligacionHistorial->tipo }}
                                    — {{ $obligacionHistorial->periodo->code }}
                                </p>
                            </div>
                            <button wire:click="cerrarHistorial" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Resumen financiero --}}
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Obligación</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        ${{ number_format($obligacionHistorial->monto_final, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Pagado</p>
                                    <p class="text-lg font-bold text-green-600">
                                        ${{ number_format($obligacionHistorial->total_pagado, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Saldo Pendiente</p>
                                    <p
                                        class="text-lg font-bold {{ $obligacionHistorial->saldo > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ${{ number_format(max(0, $obligacionHistorial->saldo), 2) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Descripción si existe --}}
                            @if ($obligacionHistorial->descripcion)
                                <p class="text-xs text-gray-500 text-center mt-3">
                                    {{ $obligacionHistorial->descripcion }}
                                </p>
                            @endif
                        </div>

                        {{-- Lista de cuotas con scroll --}}
                        <div class="overflow-y-auto max-h-96 px-6 py-4 space-y-3">

                            @forelse ($obligacionHistorial->pagos as $pago)
                                @php
                                    $pagoEstadoColors = [
                                        'Pendiente' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'Procesando' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'Aprobado' => 'bg-green-100 text-green-700 border-green-200',
                                        'Rechazado' => 'bg-red-100 text-red-700 border-red-200',
                                        'Reembolsado' => 'bg-gray-100 text-gray-700 border-gray-200',
                                    ];
                                    $iconos = [
                                        'Aprobado' => '✓',
                                        'Rechazado' => '✗',
                                        'Pendiente' => '⏳',
                                    ];
                                @endphp
                                <div
                                    class="flex items-start justify-between bg-white dark:bg-gray-800
                                            border border-gray-100 dark:border-gray-700 rounded-xl p-4 shadow-sm">

                                    {{-- Lado izquierdo --}}
                                    <div class="space-y-1 flex-1">
                                        <div class="flex items-center space-x-2">
                                            @if ($pago->numero_cuota)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-gray-800 text-white">
                                                    Cuota #{{ $pago->numero_cuota }}
                                                </span>
                                            @endif
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                                         border {{ $pagoEstadoColors[$pago->estado] ?? 'bg-gray-100 text-gray-700' }}">
                                                {{ $iconos[$pago->estado] ?? '' }} {{ $pago->estado }}
                                            </span>
                                        </div>
                                        <p class="font-mono text-xs text-gray-500">{{ $pago->numero_comprobante }}</p>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium text-gray-700">{{ $pago->metodo_pago }}</span>
                                            — {{ $pago->fecha_pago->format('d/m/Y H:i') }}
                                        </p>
                                        @if ($pago->codigo_referencia)
                                            <p class="text-xs text-gray-400">Ref: {{ $pago->codigo_referencia }}</p>
                                        @endif
                                        @if ($pago->descripcion)
                                            <p class="text-xs text-gray-400 italic">{{ $pago->descripcion }}</p>
                                        @endif
                                    </div>

                                    {{-- Lado derecho --}}
                                    <div class="text-right space-y-2 ml-4 flex-shrink-0">
                                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            ${{ number_format($pago->monto, 2) }}
                                        </p>

                                        <div class="flex flex-col space-y-1 items-end">
                                            {{-- Ver comprobante --}}
                                            @if ($pago->comprobante_path)
                                                <a href="{{ asset('storage/' . $pago->comprobante_path) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 font-medium transition">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    Comprobante
                                                </a>
                                            @endif

                                            {{-- Verificar si está pendiente (viene del portal estudiantil) --}}
                                            @if ($pago->estado === 'Pendiente')
                                                <button wire:click="abrirVerificacion({{ $pago->id }})"
                                                    class="inline-flex items-center px-3 py-1 text-xs font-semibold
                                                           bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition">
                                                    Verificar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-400">
                                    <p class="text-sm">No hay cuotas registradas</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Footer --}}
                        <div
                            class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <p class="text-xs text-gray-400">
                                {{ $obligacionHistorial->pagos->count() }} cuota(s) registrada(s)
                            </p>
                            <button wire:click="cerrarHistorial"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
