<div>
    <div class="max-w-7xl mx-auto px-1 sm:px-2 lg:px-4 py-2"
        x-data="{ _scroll: 0 }"
        x-init="
            document.addEventListener('livewire:request', () => {
                if (!document.body.style.position) {
                    this._scroll = window.scrollY;
                }
            });
        "
        x-on:modal-opened.window="
            document.body.style.position = 'fixed';
            document.body.style.top = `-${_scroll}px`;
            document.body.style.width = '100%';
        "
        x-on:modal-closed.window="
            const sy = _scroll;
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            window.scrollTo(0, sy);
        ">

        {{-- ======================================================================
             HEADER
             ====================================================================== --}}
        <div
            class="flex justify-between items-center bg-gray-100 dark:bg-slate-900 border border-gray-300 dark:border-slate-700/60 rounded-xl p-4 mb-6 shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Obligaciones Financieras</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gestión de pagos y verificación de comprobantes</p>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="abrirModalObligacion"
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl
                           font-medium text-sm shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Obligación
                </button>
                <a href="{{ route('administracion.administrativa.matriculacion.index') }}"
                    class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Matrículas
                </a>
            </div>
        </div>

        {{-- Alerta: pagos pendientes de verificación --}}
        @if ($this->totalPendientesVerificacion > 0 && ! $filtroPendientesVerif)
            <div class="mb-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-300 dark:border-amber-700/60 rounded-lg px-4 py-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>
                        <span class="font-bold">{{ $this->totalPendientesVerificacion }}</span>
                        pago{{ $this->totalPendientesVerificacion > 1 ? 's' : '' }} pendiente{{ $this->totalPendientesVerificacion > 1 ? 's' : '' }} de verificación.
                    </span>
                </div>
                <button wire:click="toggleFiltroPendientes"
                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg
                           bg-amber-500 hover:bg-amber-600 text-white transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                    Filtrar pendientes
                </button>
            </div>
        @endif

        {{-- Alerta: viene de una matrícula recién creada --}}
        @if ($filtroMatricula)
            <div class="mb-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700/60 rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-blue-800 dark:text-blue-300 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Mostrando obligaciones de la matrícula recién creada.</span>
                </div>
                <button wire:click="limpiarFiltroMatricula"
                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-xs underline ml-4">
                    Ver todas
                </button>
            </div>
        @endif

        {{-- ======================================================================
             FILTROS
             ====================================================================== --}}
        <div
            class="bg-gray-100 dark:bg-slate-900 border border-gray-300 dark:border-slate-700/60 rounded-xl p-4 mb-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="relative">
                    <label
                        class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Estudiante</label>
                    <div class="relative">
                        <input type="text"
                            wire:model.live.debounce.300ms="filtroBusquedaTexto"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-100
                                   shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm pr-8
                                   {{ $filtroEstudiante ? 'border-blue-400 dark:border-blue-600 bg-blue-50 dark:bg-blue-900/20' : '' }}"
                            placeholder="Buscar por nombre o cédula..."
                            autocomplete="off">

                        @if ($filtroEstudiante)
                            <button wire:click="limpiarFiltroEstudiante"
                                class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-red-500 transition"
                                title="Limpiar filtro">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @else
                            <span class="absolute inset-y-0 right-2 flex items-center text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        @endif
                    </div>

                    {{-- Dropdown resultados --}}
                    @if ($showDropdownFiltro && $resultadosFiltro->count() > 0)
                        <div class="absolute z-30 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200
                                    dark:border-slate-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            @foreach ($resultadosFiltro as $resultado)
                                <button type="button"
                                    wire:click="seleccionarFiltroEstudiante({{ $resultado->id }}, '{{ addslashes($resultado->name) }}', '{{ $resultado->cedula }}')"
                                    class="w-full text-left px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-slate-700
                                           text-sm transition border-b border-gray-100 dark:border-slate-700 last:border-0">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $resultado->name }}</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-xs">{{ $resultado->cedula }}</p>
                                </button>
                            @endforeach
                        </div>
                    @elseif ($showDropdownFiltro && strlen($filtroBusquedaTexto) >= 3 && $resultadosFiltro->count() === 0)
                        <div class="absolute z-30 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200
                                    dark:border-slate-600 rounded-lg shadow-lg p-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                            No se encontraron estudiantes
                        </div>
                    @endif
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Tipo</label>
                    <select wire:model.live="filtroTipo"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm dark:bg-slate-800 dark:text-gray-100">
                        <option value="">Todos los tipos</option>
                        @foreach ([
                            'MATRICULA'   => 'Matrícula',
                            'INSCRIPCION' => 'Inscripción',
                            'COLEGIATURA' => 'Colegiatura',
                            'ARRASTRE'    => 'Arrastre',
                            'CERTIFICADO' => 'Certificado',
                            'MULTA'       => 'Multa',
                            'OTROS'       => 'Otros',
                        ] as $val => $etiqueta)
                            <option value="{{ $val }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Estado</label>
                    <select wire:model.live="filtroEstado"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm dark:bg-slate-800 dark:text-gray-100">
                        <option value="">Todos los estados</option>
                        @foreach (['Pendiente', 'Parcial', 'Pagado', 'Vencido'] as $estado)
                            <option value="{{ $estado }}">{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Chip: filtrar solo obligaciones con pagos pendientes de verificación --}}
                <div class="flex items-end">
                    <button wire:click="toggleFiltroPendientes"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold border-2 transition
                            {{ $filtroPendientesVerif
                                ? 'bg-amber-500 border-amber-500 text-white shadow-sm'
                                : 'bg-white dark:bg-slate-800 border-amber-400 dark:border-amber-600 text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Por verificar
                        @if ($this->totalPendientesVerificacion > 0)
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold
                                {{ $filtroPendientesVerif ? 'bg-white/30 text-white' : 'bg-amber-500 text-white' }}">
                                {{ $this->totalPendientesVerificacion }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>
        </div>

        {{-- ======================================================================
             TABLA DE OBLIGACIONES
             ====================================================================== --}}
        <div
            class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-700/60 shadow-sm overflow-hidden">
            <div class="bg-gray-800 dark:bg-slate-800 px-6 py-3">
                <h3 class="text-white font-semibold uppercase tracking-wide text-sm">Listado de Obligaciones</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-gray-400 uppercase text-xs">
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
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/60">
                        @forelse ($obligaciones as $ob)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/60 transition">

                                {{-- Estudiante --}}
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $ob->estudiante->name }}</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-xs">{{ $ob->estudiante->cedula }}</p>
                                </td>

                                {{-- Tipo --}}
                                <td class="px-4 py-3">
                                    @php
                                        $tipoColors = [
                                            'MATRICULA'   => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                            'INSCRIPCION' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
                                            'COLEGIATURA' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                                            'ARRASTRE'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                            'CERTIFICADO' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                            'MULTA'       => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                            'OTROS'       => 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $tipoColors[$ob->tipo] ?? 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300' }}">
                                        {{ $ob->tipo }}
                                    </span>
                                    @if ($ob->descripcion)
                                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-0.5 max-w-xs truncate">
                                            {{ $ob->descripcion }}</p>
                                    @endif
                                </td>

                                {{-- Período / Matrícula --}}
                                <td class="px-4 py-3">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $ob->periodo->code }}</p>
                                    @if ($ob->matricula)
                                        <p class="text-gray-400 dark:text-gray-500 text-xs">{{ $ob->matricula->code }}</p>
                                    @endif
                                </td>

                                {{-- Monto --}}
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-gray-100">
                                    ${{ number_format($ob->monto_final, 2) }}
                                    @if ($ob->descuento > 0)
                                        <p class="text-green-600 dark:text-green-400 text-xs">-${{ number_format($ob->descuento, 2) }} desc.</p>
                                    @endif
                                </td>

                                {{-- Total pagado --}}
                                <td class="px-4 py-3 text-right font-medium text-green-700 dark:text-green-400">
                                    ${{ number_format($ob->total_pagado, 2) }}
                                </td>

                                {{-- Saldo --}}
                                <td
                                    class="px-4 py-3 text-right font-bold {{ $ob->saldo > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    ${{ number_format(max(0, $ob->saldo), 2) }}
                                </td>

                                {{-- Vencimiento --}}
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                    @if ($ob->fecha_vencimiento)
                                        <span
                                            class="{{ $ob->fecha_vencimiento->isPast() && $ob->estado !== 'Pagado' ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                                            {{ $ob->fecha_vencimiento->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-600">—</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $estadoColors = [
                                            'Pendiente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                            'Parcial'   => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                                            'Pagado'    => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                            'Vencido'   => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $estadoColors[$ob->estado] ?? 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300' }}">
                                        {{ $ob->estado }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-4 py-3">
                                    @php
                                        $pagosPendientes = $ob->pagos->where('estado', 'Pendiente');
                                        $pagoParaVerificar = $pagosPendientes->last();
                                    @endphp
                                    <div class="flex items-center justify-center flex-wrap gap-1.5">

                                        {{-- Botón registrar pago (INSCRIPCION se liquida automáticamente con MATRICULA) --}}
                                        @if ($ob->estado !== 'Pagado' && $ob->tipo !== 'INSCRIPCION')
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

                                        {{-- Botón verificar pago pendiente (acceso directo sin abrir historial) --}}
                                        @if ($pagoParaVerificar)
                                            <button wire:click="abrirVerificacion({{ $pagoParaVerificar->id }})"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg
                                                       bg-amber-500 hover:bg-amber-600 text-white transition"
                                                title="Verificar comprobante pendiente">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Verificar
                                                @if ($pagosPendientes->count() > 1)
                                                    <span class="ml-1 bg-white/30 rounded-full px-1.5 text-xs font-bold">
                                                        {{ $pagosPendientes->count() }}
                                                    </span>
                                                @endif
                                            </button>
                                        @endif

                                        {{-- Ver historial de cuotas --}}
                                        @if ($ob->pagos->count() > 0)
                                            <button wire:click="abrirHistorial({{ $ob->id }})"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg
                                                       bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600
                                                       text-gray-700 dark:text-gray-300 transition"
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
                                <td colspan="9" class="text-center py-12 text-gray-400 dark:text-gray-500">
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
            <div class="px-4 py-3 bg-gray-50 dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700/60">
                {{ $obligaciones->links() }}
            </div>
        </div>


        {{-- ======================================================================
             MODAL: CREAR OBLIGACIÓN MANUAL (MULTA / OTROS)
             ====================================================================== --}}
        @if ($showModalObligacion)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="z-index:99999">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-60 dark:bg-opacity-80" wire:click="cerrarModalObligacion"></div>

                <div class="relative bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]">

                    {{-- Header --}}
                    <div class="flex-shrink-0 bg-gray-800 dark:bg-slate-800 px-6 py-4 rounded-t-xl flex justify-between items-center">
                        <h3 class="text-white font-semibold">Nueva Obligación Manual</h3>
                        <button wire:click="cerrarModalObligacion"
                            class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6 space-y-5">

                            @error('obligacion_general')
                                <p class="text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/60 rounded p-2">
                                    {{ $message }}</p>
                            @enderror

                            {{-- Buscador predictivo de estudiante --}}
                            <div x-data="{ open: @entangle('showDropdown') }" class="relative">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Estudiante <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" wire:model.live.debounce.300ms="busquedaEstudiante"
                                        class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm pr-10
                                               {{ $estudianteSeleccionado ? 'border-green-400 dark:border-green-600 bg-green-50 dark:bg-green-900/20' : '' }}"
                                        placeholder="Buscar por nombre o cédula (mín. 3 caracteres)..."
                                        autocomplete="off">

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
                                        class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200
                                                dark:border-slate-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        @foreach ($resultadosBusqueda as $resultado)
                                            <button type="button"
                                                wire:click="seleccionarEstudiante({{ $resultado->id }}, '{{ addslashes($resultado->name) }}', '{{ $resultado->cedula }}')"
                                                class="w-full text-left px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-slate-700
                                                       text-sm transition border-b border-gray-100 dark:border-slate-700 last:border-0">
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $resultado->name }}</p>
                                                <p class="text-gray-400 dark:text-gray-500 text-xs">Cédula: {{ $resultado->cedula }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif ($showDropdown && strlen($busquedaEstudiante) >= 3 && $resultadosBusqueda->count() === 0)
                                    <div
                                        class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200
                                                dark:border-slate-600 rounded-lg shadow-lg p-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                        No se encontraron estudiantes
                                    </div>
                                @endif

                                @error('estudianteSeleccionado')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
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
                                                        peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 peer-checked:text-blue-700 dark:peer-checked:text-blue-300
                                                        hover:border-blue-300 dark:hover:border-blue-700
                                                        text-gray-600 dark:text-gray-400 border-gray-200 dark:border-slate-600">
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
                                        class="absolute inset-y-0 left-3 flex items-center text-gray-500 dark:text-gray-400 font-medium">$</span>
                                    <input type="number" wire:model="obligacionMonto" step="0.01" min="0.01"
                                        class="w-full pl-8 rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="0.00">
                                </div>
                                @error('obligacionMonto')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Descripción --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Descripción <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="obligacionDescripcion" rows="3"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Ej: Multa por ausencia en evaluación parcial del 15/02/2026..."></textarea>
                                @error('obligacionDescripcion')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Fecha vencimiento --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Fecha de Vencimiento <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="obligacionVencimiento"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    min="{{ now()->format('Y-m-d') }}">
                                @error('obligacionVencimiento')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Footer --}}
                            <div class="flex justify-end space-x-3 pt-2 border-t border-gray-100 dark:border-slate-700">
                                <button wire:click="cerrarModalObligacion"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition">
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
        @endif


        {{-- ======================================================================
             MODAL: REGISTRAR PAGO
             ====================================================================== --}}
        @if ($showModalPago && $obligacionSeleccionada)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="z-index:99999">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-60 dark:bg-opacity-80" wire:click="cerrarModalPago"></div>

                <div class="relative bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]">
                    <div class="flex-shrink-0 bg-gray-800 dark:bg-slate-800 px-6 py-4 rounded-t-xl flex justify-between items-center">
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

                    <div class="flex-1 overflow-y-auto p-6 space-y-5">

                            {{-- Resumen de la obligación --}}
                            <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-4 text-sm space-y-1">
                                <p>
                                    <span class="font-semibold text-gray-800 dark:text-gray-300">Estudiante:</span>
                                    <span class="font-semibold text-gray-700 dark:text-gray-200">
                                        {{ $obligacionSeleccionada->estudiante->name }}
                                    </span>
                                </p>
                                @if ($obligacionSeleccionada->descripcion)
                                    <p>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">Descripción:</span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $obligacionSeleccionada->descripcion }}</span>
                                    </p>
                                @endif
                                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-200 dark:border-slate-700 mt-2">
                                    <div class="text-center">
                                        <p class="text-gray-500 dark:text-gray-400 text-xs">Total</p>
                                        <p class="font-bold text-gray-900 dark:text-gray-100">
                                            ${{ number_format($obligacionSeleccionada->monto_final, 2) }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-gray-500 dark:text-gray-400 text-xs">Pagado</p>
                                        <p class="font-bold text-green-600 dark:text-green-400">
                                            ${{ number_format($obligacionSeleccionada->total_pagado, 2) }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-gray-500 dark:text-gray-400 text-xs">Saldo</p>
                                        <p class="font-bold text-red-600 dark:text-red-400">
                                            ${{ number_format($obligacionSeleccionada->saldo, 2) }}</p>
                                    </div>
                                </div>

                                {{-- Desglose inscripción + total acumulado --}}
                                @if ($obligInscripcionModal)
                                    @php $totalACobrar = $obligacionSeleccionada->saldo + $obligInscripcionModal->monto_final; @endphp
                                    <div class="mt-3 pt-3 border-t border-dashed border-amber-300 dark:border-amber-700 space-y-1.5 text-xs">
                                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                            <span>Saldo matrícula</span>
                                            <span class="font-medium">${{ number_format($obligacionSeleccionada->saldo, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-amber-700 dark:text-amber-400">
                                            <span>Inscripción (primera matrícula)</span>
                                            <span class="font-medium">${{ number_format($obligInscripcionModal->monto_final, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between font-bold text-sm text-gray-900 dark:text-gray-100 pt-1 border-t border-gray-200 dark:border-slate-600">
                                            <span>Total a cobrar</span>
                                            <span class="text-blue-700 dark:text-blue-400">${{ number_format($totalACobrar, 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Inscripción auto-liquidada (solo si es MATRICULA con inscripción pendiente) --}}
                            @if ($obligInscripcionModal)
                                <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-lg p-3 text-sm">
                                    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-amber-800 dark:text-amber-300">
                                            Incluye liquidación de Inscripción
                                        </p>
                                        <p class="text-amber-700 dark:text-amber-400 mt-0.5">
                                            Al registrar este pago se liquidará automáticamente el valor de inscripción de
                                            <span class="font-bold">${{ number_format($obligInscripcionModal->monto_final, 2) }}</span>.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @error('pago_general')
                                <p class="text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/60 rounded p-2">
                                    {{ $message }}</p>
                            @enderror

                            {{-- Monto de la cuota --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Monto a Pagar <span class="text-red-500">*</span>
                                    <span class="font-normal text-gray-400 dark:text-gray-500">(puede ser parcial)</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-3 flex items-center text-gray-500 dark:text-gray-400 font-medium">$</span>
                                    <input type="number" wire:model="montoPago" step="0.01" min="0.01"
                                        max="{{ $obligacionSeleccionada->saldo + ($obligInscripcionModal?->monto_final ?? 0) }}"
                                        class="w-full pl-8 rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                @error('montoPago')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Método de pago --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Método de Pago <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-3 gap-2"><!-- 'Tarjeta', 'Payphone' -->
                                    @foreach (['Transferencia', 'Deposito', 'Efectivo'] as $metodo)
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="metodoPago"
                                                value="{{ $metodo }}" class="sr-only peer">
                                            <div
                                                class="border-2 rounded-lg p-2 text-center text-xs font-medium transition-all
                                                        peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 peer-checked:text-blue-700 dark:peer-checked:text-blue-300
                                                        hover:border-blue-300 dark:hover:border-blue-700
                                                        text-gray-600 dark:text-gray-400 border-gray-200 dark:border-slate-600">
                                                {{ $metodo }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Referencia --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    N° Referencia <span class="text-gray-400 dark:text-gray-500 font-normal">(opcional)</span>
                                </label>
                                <input type="text" wire:model="referencia"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Ej: 00012345678">
                            </div>

                            {{-- Comprobante --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Comprobante <span class="text-gray-400 dark:text-gray-500 font-normal">(PDF / imagen, máx. 5MB)</span>
                                </label>
                                <input type="file" wire:model="comprobante" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-sm text-gray-600 dark:text-gray-400
                                           file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                                           file:text-xs file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/40 file:text-blue-700 dark:file:text-blue-300
                                           hover:file:bg-blue-100 dark:hover:file:bg-blue-900/60 transition">
                                @error('comprobante')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Observaciones --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Observaciones <span class="text-gray-400 dark:text-gray-500 font-normal">(opcional)</span>
                                </label>
                                <textarea wire:model="descripcionPago" rows="2"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Información adicional sobre el pago..."></textarea>
                            </div>
                        </div>

                    {{-- Footer fijo --}}
                    <div class="flex-shrink-0 flex justify-end space-x-3 px-6 py-4 border-t border-gray-100 dark:border-slate-700 rounded-b-xl bg-white dark:bg-slate-900">
                        <button wire:click="cerrarModalPago"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition">
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
        @endif


        {{-- ======================================================================
             MODAL: VERIFICAR PAGO (pagos subidos por estudiante)
             ====================================================================== --}}
        @if ($showModalVerificacion && $pagoSeleccionado)
            <div class="fixed inset-0 z-50 overflow-y-auto" style="z-index:99999">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 dark:bg-opacity-80" wire:click="cerrarVerificacion"></div>

                    <div class="relative bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-full max-w-md overscroll-contain">
                        <div class="bg-gray-800 dark:bg-slate-800 px-6 py-4 rounded-t-xl flex justify-between items-center">
                            <h3 class="text-white font-semibold">Verificar Comprobante</h3>
                            <button wire:click="cerrarVerificacion" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-4 text-sm space-y-1">
                                <p>
                                    <span class="font-semibold text-gray-600 dark:text-gray-400">Comprobante:</span>
                                    <span class="font-mono text-gray-900 dark:text-gray-100">{{ $pagoSeleccionado->numero_comprobante }}</span>
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-600 dark:text-gray-400">Estudiante:</span>
                                    <span class="text-gray-800 dark:text-gray-200">{{ $pagoSeleccionado->obligacion->estudiante->name }}</span>
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-600 dark:text-gray-400">Tipo:</span>
                                    <span class="text-gray-800 dark:text-gray-200">{{ $pagoSeleccionado->obligacion->tipo }}</span>
                                </p>
                                @if ($pagoSeleccionado->numero_cuota)
                                    <p>
                                        <span class="font-semibold text-gray-600 dark:text-gray-400">Cuota N°:</span>
                                        <span class="text-gray-800 dark:text-gray-200">{{ $pagoSeleccionado->numero_cuota }}</span>
                                    </p>
                                @endif
                                <p>
                                    <span class="font-semibold text-gray-600 dark:text-gray-400">Método:</span>
                                    <span class="text-gray-800 dark:text-gray-200">{{ $pagoSeleccionado->metodo_pago }}</span>
                                </p>
                                @if ($pagoSeleccionado->codigo_referencia)
                                    <p>
                                        <span class="font-semibold text-gray-600 dark:text-gray-400">Referencia:</span>
                                        <span class="text-gray-800 dark:text-gray-200">{{ $pagoSeleccionado->codigo_referencia }}</span>
                                    </p>
                                @endif
                                <div class="flex justify-between border-t border-gray-200 dark:border-slate-700 pt-2 mt-2">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">Monto:</span>
                                    <span class="font-bold text-gray-900 dark:text-gray-100 text-base">${{ number_format($pagoSeleccionado->monto, 2) }}</span>
                                </div>
                                @if ($pagoSeleccionado->comprobante_path)
                                    <div class="pt-1">
                                        <a href="{{ asset('storage/' . $pagoSeleccionado->comprobante_path) }}"
                                            target="_blank"
                                            class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs font-medium underline">
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
                                    <span class="font-normal text-gray-400 dark:text-gray-500">(requerida al rechazar)</span>
                                </label>
                                <textarea wire:model="observacionVerificacion" rows="3"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Ingrese el motivo si rechaza, o una nota al aprobar..."></textarea>
                                @error('observacionVerificacion')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-between pt-2 border-t border-gray-100 dark:border-slate-700">
                                <button wire:click="cerrarVerificacion"
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition">
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
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 dark:bg-opacity-80" wire:click="cerrarHistorial"></div>

                    <div class="relative bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-full max-w-2xl overscroll-contain">

                        {{-- Header fijo --}}
                        <div class="bg-gray-800 dark:bg-slate-800 px-6 py-4 rounded-t-xl flex justify-between items-center">
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
                            class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Total Obligación</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        ${{ number_format($obligacionHistorial->monto_final, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Total Pagado</p>
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                        ${{ number_format($obligacionHistorial->total_pagado, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Saldo Pendiente</p>
                                    <p
                                        class="text-lg font-bold {{ $obligacionHistorial->saldo > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                        ${{ number_format(max(0, $obligacionHistorial->saldo), 2) }}
                                    </p>
                                </div>
                            </div>

                            @if ($obligacionHistorial->descripcion)
                                <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3">
                                    {{ $obligacionHistorial->descripcion }}
                                </p>
                            @endif
                        </div>

                        {{-- Lista de cuotas con scroll --}}
                        <div class="overflow-y-auto max-h-96 px-6 py-4 space-y-3">

                            @forelse ($obligacionHistorial->pagos as $pago)
                                @php
                                    $pagoEstadoColors = [
                                        'Pendiente'   => 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/40 dark:text-yellow-300 dark:border-yellow-800/60',
                                        'Procesando'  => 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800/60',
                                        'Aprobado'    => 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/40 dark:text-green-300 dark:border-green-800/60',
                                        'Rechazado'   => 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/40 dark:text-red-300 dark:border-red-800/60',
                                        'Reembolsado' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-slate-700 dark:text-gray-300 dark:border-slate-600',
                                    ];
                                    $iconos = [
                                        'Aprobado'  => '✓',
                                        'Rechazado' => '✗',
                                        'Pendiente' => '⏳',
                                    ];
                                @endphp
                                <div
                                    class="flex items-start justify-between bg-white dark:bg-slate-800
                                            border border-gray-100 dark:border-slate-700/60 rounded-xl p-4 shadow-sm">

                                    {{-- Lado izquierdo --}}
                                    <div class="space-y-1 flex-1">
                                        <div class="flex items-center space-x-2">
                                            @if ($pago->numero_cuota)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-gray-800 dark:bg-slate-700 text-white">
                                                    Cuota #{{ $pago->numero_cuota }}
                                                </span>
                                            @endif
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                                         border {{ $pagoEstadoColors[$pago->estado] ?? 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-slate-700 dark:text-gray-300 dark:border-slate-600' }}">
                                                {{ $iconos[$pago->estado] ?? '' }} {{ $pago->estado }}
                                            </span>
                                        </div>
                                        <p class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $pago->numero_comprobante }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $pago->metodo_pago }}</span>
                                            — {{ $pago->fecha_pago->format('d/m/Y H:i') }}
                                        </p>
                                        @if ($pago->codigo_referencia)
                                            <p class="text-xs text-gray-400 dark:text-gray-500">Ref: {{ $pago->codigo_referencia }}</p>
                                        @endif
                                        @if ($pago->descripcion)
                                            <p class="text-xs text-gray-400 dark:text-gray-500 italic">{{ $pago->descripcion }}</p>
                                        @endif
                                    </div>

                                    {{-- Lado derecho --}}
                                    <div class="text-right space-y-2 ml-4 flex-shrink-0">
                                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            ${{ number_format($pago->monto, 2) }}
                                        </p>

                                        <div class="flex flex-col space-y-1 items-end">
                                            @if ($pago->comprobante_path)
                                                <a href="{{ asset('storage/' . $pago->comprobante_path) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    Comprobante
                                                </a>
                                            @endif

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
                                <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                                    <p class="text-sm">No hay cuotas registradas</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Footer --}}
                        <div
                            class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex justify-between items-center">
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $obligacionHistorial->pagos->count() }} cuota(s) registrada(s)
                            </p>
                            <button wire:click="cerrarHistorial"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
