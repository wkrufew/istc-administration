<div>
    <div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

            {{-- HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Obligaciones Financieras</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Control y seguimiento de pagos por carrera y periodo</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">

                    {{-- Selector carrera --}}
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap">
                            Carrera
                        </label>
                        <select wire:model.live="carreraId"
                            class="rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800
                                   text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500
                                   text-sm font-semibold min-w-44">
                            <option value="">Seleccionar carrera...</option>
                            @foreach ($this->carreras as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Selector periodo (solo si hay carrera) --}}
                    @if ($carreraId)
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                Periodo
                            </label>
                            <select wire:model.live="periodoId"
                                class="rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800
                                       text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500
                                       text-sm font-semibold min-w-40">
                                @foreach ($this->periodos as $p)
                                    <option value="{{ $p->id }}">{{ $p->code }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Botón Consolidado Cohortes --}}
                    <a href="{{ route('administracion.administrativa.consolidado-cohortes') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-800 text-white text-sm font-semibold
                        hover:bg-gray-700 transition shadow-sm whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Consolidado Cohortes
                    </a>

                </div>
            </div>

            {{-- EMPTY STATE — sin carrera seleccionada --}}
            @if (! $carreraId)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-gray-200 dark:border-slate-700 py-20 text-center">
                    <svg class="w-14 h-14 mx-auto mb-4 text-gray-200 dark:text-slate-700" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-400 dark:text-gray-500 font-semibold text-base">Selecciona una carrera para ver las obligaciones</p>
                    <p class="text-gray-300 dark:text-gray-600 text-sm mt-1">Luego podrás filtrar por periodo, estado y tipo</p>
                </div>
            @else

            {{-- ================================================================
             STATS FINANCIEROS
             ================================================================ --}}
            @if (!empty($this->stats))
                @php $s = $this->stats; @endphp

                {{-- Card principal recaudación — gradiente oscuro, sin cambios --}}
                <div class="rounded-2xl bg-gradient-to-br from-gray-800 to-gray-700 text-white p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-widest">Resumen financiero
                                del periodo</p>
                            <p class="text-4xl font-extrabold mt-2">${{ number_format($s['monto_recaudado'], 2) }}</p>
                            <p class="text-gray-400 text-sm mt-1">
                                recaudado de ${{ number_format($s['monto_total'], 2) }} en obligaciones totales
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <div class="inline-flex items-center gap-2 bg-white/10 rounded-xl px-4 py-2">
                                <span class="text-3xl font-extrabold">{{ $s['pct_recaudado'] }}%</span>
                                <span class="text-gray-400 text-xs">recaudado</span>
                            </div>
                            <div class="w-52 bg-white/20 rounded-full h-2">
                                <div class="h-2 rounded-full bg-emerald-400 transition-all duration-700"
                                    style="width: {{ $s['pct_recaudado'] }}%"></div>
                            </div>
                            @if ($s['estudiantes_en_mora'] > 0)
                                <span class="text-xs font-semibold text-red-300">
                                    ⚠ {{ $s['estudiantes_en_mora'] }} estudiantes en mora
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="rounded-xl bg-white/10 p-4">
                            <p class="text-gray-400 text-xs uppercase tracking-wide">Pagado</p>
                            <p class="text-xl font-bold mt-1">${{ number_format($s['monto_recaudado'], 2) }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $s['count_pagado'] }} obligaciones</p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4">
                            <p class="text-gray-400 text-xs uppercase tracking-wide">Pendiente</p>
                            <p class="text-xl font-bold mt-1 text-amber-300">
                                ${{ number_format($s['monto_pendiente'], 2) }}</p>
                            <p class="text-gray-400 text-xs mt-1">
                                {{ $s['count_pendiente'] }} ({{ $s['count_parcial'] }} parcial)
                            </p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4">
                            <p class="text-gray-400 text-xs uppercase tracking-wide">Vencido</p>
                            <p class="text-xl font-bold mt-1 text-red-400">${{ number_format($s['monto_vencido'], 2) }}
                            </p>
                            <p class="text-gray-400 text-xs mt-1">{{ $s['count_vencido'] }} obligaciones</p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4">
                            <p class="text-gray-400 text-xs uppercase tracking-wide">Total</p>
                            <p class="text-xl font-bold mt-1">${{ number_format($s['monto_total'], 2) }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $s['total_obligaciones'] }} obligaciones</p>
                        </div>
                    </div>
                </div>

                {{-- Desglose por tipo --}}
                @if ($s['por_tipo']->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        @foreach ($s['por_tipo'] as $tipo => $data)
                            @php
                                $tipoCfg = match ($tipo) {
                                    'MATRICULA' => [
                                        'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800/50',
                                        'text-blue-700 dark:text-blue-400',
                                        'bg-blue-500',
                                    ],
                                    'COLEGIATURA' => [
                                        'bg-violet-50 dark:bg-violet-900/20 border-violet-200 dark:border-violet-800/50',
                                        'text-violet-700 dark:text-violet-400',
                                        'bg-violet-500',
                                    ],
                                    'ARRASTRE' => [
                                        'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800/50',
                                        'text-amber-700 dark:text-amber-400',
                                        'bg-amber-500',
                                    ],
                                    'INSCRIPCION' => [
                                        'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800/50',
                                        'text-orange-700 dark:text-orange-400',
                                        'bg-orange-500',
                                    ],
                                    'MULTA' => [
                                        'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800/50',
                                        'text-red-700 dark:text-red-400',
                                        'bg-red-500',
                                    ],
                                    default => [
                                        'bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700',
                                        'text-gray-700 dark:text-gray-300',
                                        'bg-gray-400',
                                    ],
                                };
                            @endphp
                            <div class="rounded-2xl border {{ $tipoCfg[0] }} p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full {{ $tipoCfg[2] }}"></div>
                                    <span class="text-xs font-bold {{ $tipoCfg[1] }}">{{ $tipo }}</span>
                                </div>
                                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">${{ number_format($data->total, 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $data->cantidad }} obligaciones</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            {{-- ================================================================
             TABS
             ================================================================ --}}
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-slate-800 rounded-xl p-1 w-fit">
                <button wire:click="setTab('obligaciones')"
                    class="px-5 py-2 rounded-lg text-sm font-semibold transition
                       {{ $tab === 'obligaciones'
                           ? 'bg-white dark:bg-slate-700 shadow text-gray-900 dark:text-gray-100'
                           : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                    Todas las obligaciones
                </button>
                <button wire:click="setTab('mora')"
                    class="px-5 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2
                       {{ $tab === 'mora'
                           ? 'bg-white dark:bg-slate-700 shadow text-gray-900 dark:text-gray-100'
                           : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                    Estudiantes en mora
                    @if (!empty($this->stats) && $this->stats['estudiantes_en_mora'] > 0)
                        <span
                            class="inline-flex items-center justify-center w-5 h-5 rounded-full
                                 bg-red-500 text-white text-xs font-bold">
                            {{ $this->stats['estudiantes_en_mora'] }}
                        </span>
                    @endif
                </button>
            </div>

            {{-- ================================================================
             TAB OBLIGACIONES
             ================================================================ --}}
            @if ($tab === 'obligaciones')

                {{-- Filtros --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700/60 shadow-sm p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-1">Buscar</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" wire:model.live.debounce.300ms="busqueda"
                                    placeholder="Nombre o cédula..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           placeholder-gray-400 dark:placeholder-slate-500
                                           focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-1">Estado</label>
                            <select wire:model.live="filtroEstado"
                                class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                       bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                       shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">Todos</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Parcial">Parcial</option>
                                <option value="Pagado">Pagado</option>
                                <option value="Vencido">Vencido</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-1">Tipo</label>
                            <select wire:model.live="filtroTipo"
                                class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                       bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                       shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">Todos</option>
                                <option value="MATRICULA">Matrícula</option>
                                <option value="COLEGIATURA">Colegiatura</option>
                                <option value="ARRASTRE">Arrastre</option>
                                <option value="INSCRIPCION">Inscripción</option>
                                <option value="MULTA">Multa</option>
                                <option value="OTROS">Otros</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700/60 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-slate-800/60 border-b border-gray-200 dark:border-slate-700/60">
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Estudiante</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Tipo</th>
                                    <th
                                        class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Monto orig.</th>
                                    <th
                                        class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Descuento</th>
                                    <th
                                        class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Monto final</th>
                                    <th
                                        class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Vencimiento</th>
                                    <th
                                        class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Estado</th>
                                    <th
                                        class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Pagos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700/40">
                                @forelse ($obligaciones as $ob)
                                    @php
                                        $estadoCfg = match ($ob->estado) {
                                            'Pagado' => ['bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400', 'Pagado'],
                                            'Pendiente' => ['bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400', 'Pendiente'],
                                            'Parcial' => ['bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400', 'Parcial'],
                                            'Vencido' => ['bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400', 'Vencido'],
                                            default => ['bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400', $ob->estado],
                                        };
                                        $tipoCfg = match ($ob->tipo) {
                                            'MATRICULA' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
                                            'COLEGIATURA' => 'bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-400',
                                            'ARRASTRE' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
                                            'INSCRIPCION' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400',
                                            'MULTA' => 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400',
                                            default => 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400',
                                        };
                                        $isVencido = $ob->estado === 'Vencido';
                                        $isExpanded = $expandedId === $ob->id;
                                        $totalPagado = $ob->pagos->where('estado', 'Aprobado')->sum('monto');
                                        $saldo = $ob->monto_final - $totalPagado;
                                    @endphp

                                    {{-- Fila principal --}}
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40 transition {{ $isVencido ? 'bg-red-50/40 dark:bg-red-950/20' : '' }}">
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-xl bg-gradient-to-br from-gray-700 to-gray-900
                                                        flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                    {{ strtoupper(substr($ob->estudiante?->name ?? '?', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900 dark:text-gray-100 text-xs">
                                                        {{ $ob->estudiante?->name ?? '—' }}</p>
                                                    <p class="text-xs text-gray-400">
                                                        {{ $ob->estudiante?->cedula ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $tipoCfg }}">
                                                {{ $ob->tipo }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-sm text-gray-600 dark:text-gray-300">
                                            ${{ number_format($ob->monto_original, 2) }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-sm">
                                            @if ($ob->descuento > 0)
                                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">
                                                    -${{ number_format($ob->descuento, 2) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            <p class="font-bold text-gray-900 dark:text-gray-100 text-sm">
                                                ${{ number_format($ob->monto_final, 2) }}</p>
                                            @if ($totalPagado > 0 && $ob->estado !== 'Pagado')
                                                <p class="text-xs text-emerald-600 dark:text-emerald-400">Pag:
                                                    ${{ number_format($totalPagado, 2) }}</p>
                                                <p class="text-xs text-red-500 dark:text-red-400">Saldo: ${{ number_format($saldo, 2) }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            @if ($ob->fecha_vencimiento)
                                                <p
                                                    class="text-xs font-semibold {{ $isVencido ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-200' }}">
                                                    {{ $ob->fecha_vencimiento->format('d/m/Y') }}
                                                </p>
                                                @if ($isVencido)
                                                    <p class="text-xs text-red-400">
                                                        {{ now()->diffInDays($ob->fecha_vencimiento) }}d de mora
                                                    </p>
                                                @elseif ($ob->estado !== 'Pagado')
                                                    <p class="text-xs text-gray-400">
                                                        <!-- en --> {{ $ob->fecha_vencimiento->diffForHumans(['parts' => 1]) }}
                                                    </p>
                                                @endif
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $estadoCfg[0] }}">
                                                {{ $estadoCfg[1] }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <button wire:click="toggleExpand({{ $ob->id }})" title="Ver pagos"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center transition
                                                   {{ $isExpanded
                                                       ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400'
                                                       : 'bg-gray-100 dark:bg-slate-700 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 text-gray-500 dark:text-slate-400 hover:text-indigo-700 dark:hover:text-indigo-400' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- FILA EXPANDIBLE — HISTORIAL DE PAGOS --}}
                                    @if ($isExpanded)
                                        <tr>
                                            <td colspan="8"
                                                class="px-0 py-0 bg-indigo-50/60 dark:bg-indigo-950/30 border-b border-indigo-100 dark:border-indigo-900/50">
                                                <div class="px-6 py-4">

                                                    {{-- Info descripción --}}
                                                    @if ($ob->descripcion)
                                                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-3 italic">
                                                            {{ $ob->descripcion }}
                                                        </p>
                                                    @endif

                                                    <p
                                                        class="text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wide mb-3">
                                                        Historial de pagos — {{ $ob->estudiante?->name }}
                                                    </p>

                                                    @if ($ob->pagos->count() > 0)
                                                        <div class="overflow-x-auto">
                                                            <table class="w-full text-xs">
                                                                <thead>
                                                                    <tr class="border-b border-indigo-100 dark:border-indigo-900/50">
                                                                        <th
                                                                            class="pb-2 text-left font-semibold text-indigo-500 dark:text-indigo-400 uppercase tracking-wide">
                                                                            Comprobante</th>
                                                                        <th
                                                                            class="pb-2 text-left font-semibold text-indigo-500 dark:text-indigo-400 uppercase tracking-wide">
                                                                            Método</th>
                                                                        <th
                                                                            class="pb-2 text-right font-semibold text-indigo-500 dark:text-indigo-400 uppercase tracking-wide">
                                                                            Monto</th>
                                                                        <th
                                                                            class="pb-2 text-center font-semibold text-indigo-500 dark:text-indigo-400 uppercase tracking-wide">
                                                                            Cuota</th>
                                                                        <th
                                                                            class="pb-2 text-center font-semibold text-indigo-500 dark:text-indigo-400 uppercase tracking-wide">
                                                                            Fecha</th>
                                                                        <th
                                                                            class="pb-2 text-center font-semibold text-indigo-500 dark:text-indigo-400 uppercase tracking-wide">
                                                                            Estado</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-indigo-100/60 dark:divide-indigo-900/40">
                                                                    @foreach ($ob->pagos as $pago)
                                                                        @php
                                                                            $pagoCfg = match ($pago->estado) {
                                                                                'Aprobado'
                                                                                    => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
                                                                                'Pendiente'
                                                                                    => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
                                                                                'Procesando'
                                                                                    => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
                                                                                'Rechazado'
                                                                                    => 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400',
                                                                                'Reembolsado'
                                                                                    => 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400',
                                                                                default => 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400',
                                                                            };
                                                                        @endphp
                                                                        <tr class="hover:bg-indigo-50 dark:hover:bg-indigo-950/20 transition">
                                                                            <td class="py-2 font-mono text-gray-700 dark:text-gray-200">
                                                                                {{ $pago->numero_comprobante }}
                                                                            </td>
                                                                            <td class="py-2">
                                                                                <span
                                                                                    class="inline-flex items-center gap-1">
                                                                                    @php
                                                                                        $metodoCfg = match (
                                                                                            $pago->metodo_pago
                                                                                        ) {
                                                                                            'Efectivo' => '💵',
                                                                                            'Tarjeta' => '💳',
                                                                                            'Transferencia' => '🏦',
                                                                                            'Deposito' => '🏧',
                                                                                            'Payphone' => '📱',
                                                                                            default => '💰',
                                                                                        };
                                                                                    @endphp
                                                                                    {{ $metodoCfg }}
                                                                                    {{ $pago->metodo_pago }}
                                                                                </span>
                                                                            </td>
                                                                            <td
                                                                                class="py-2 text-right font-bold text-gray-900 dark:text-gray-100">
                                                                                ${{ number_format($pago->monto, 2) }}
                                                                            </td>
                                                                            <td class="py-2 text-center text-gray-500 dark:text-slate-400">
                                                                                {{ $pago->numero_cuota ? "Cuota {$pago->numero_cuota}" : '—' }}
                                                                            </td>
                                                                            <td class="py-2 text-center text-gray-500 dark:text-slate-400">
                                                                                {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}
                                                                            </td>
                                                                            <td class="py-2 text-center">
                                                                                <span
                                                                                    class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $pagoCfg }}">
                                                                                    {{ $pago->estado }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot class="border-t-2 border-indigo-200 dark:border-indigo-800">
                                                                    <tr>
                                                                        <td colspan="2"
                                                                            class="pt-2 text-xs font-bold text-indigo-700 dark:text-indigo-400">
                                                                            Total aprobado</td>
                                                                        <td
                                                                            class="pt-2 text-right font-bold text-emerald-700 dark:text-emerald-400">
                                                                            ${{ number_format($ob->pagos->where('estado', 'Aprobado')->sum('monto'), 2) }}
                                                                        </td>
                                                                        <td colspan="3"></td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="flex items-center gap-2 text-gray-400 text-xs py-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            Sin pagos registrados para esta obligación
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="8" class="px-5 py-14 text-center text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200 dark:text-slate-700" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                            </svg>
                                            <p class="font-semibold">No se encontraron obligaciones</p>
                                            <p class="text-sm mt-1">Ajusta los filtros para ver resultados</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($obligaciones->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100 dark:border-slate-700/60">
                            {{ $obligaciones->links() }}
                        </div>
                    @endif
                </div>

            @endif

            {{-- ================================================================
             TAB MORA
             ================================================================ --}}
            @if ($tab === 'mora')
                @if ($this->estudiantesEnMora->count() > 0)
                    <div class="space-y-4">

                        {{-- Resumen de mora --}}
                        <div
                            class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-red-800 dark:text-red-300">{{ $this->estudiantesEnMora->count() }}
                                        estudiantes en mora</p>
                                    <p class="text-sm text-red-600 dark:text-red-400">
                                        Total adeudado:
                                        ${{ number_format($this->estudiantesEnMora->sum('total_adeudado'), 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Cards de estudiantes en mora --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach ($this->estudiantesEnMora as $em)
                                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-red-200 dark:border-red-800/50 shadow-sm overflow-hidden">
                                    <div class="p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-500 to-rose-600
                                                        flex items-center justify-center text-white font-bold flex-shrink-0">
                                                    {{ strtoupper(substr($em['user']?->name ?? '?', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 dark:text-gray-100">{{ $em['user']?->name ?? '—' }}
                                                    </p>
                                                    <p class="text-xs text-gray-400">CI:
                                                        {{ $em['user']?->cedula ?? '—' }}</p>
                                                    <p class="text-xs text-gray-400">{{ $em['user']?->email ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <p class="text-xl font-extrabold text-red-600 dark:text-red-400">
                                                    ${{ number_format($em['total_adeudado'], 2) }}
                                                </p>
                                                <p class="text-xs text-gray-400">total adeudado</p>
                                            </div>
                                        </div>

                                        <div class="mt-4 flex flex-wrap items-center gap-3">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                {{ $em['cantidad_vencidas'] }} obligación(es) vencida(s)
                                            </span>

                                            @if ($em['dias_mora'])
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                                         {{ $em['dias_mora'] > 30
                                                             ? 'bg-red-200 dark:bg-red-900/50 text-red-800 dark:text-red-300'
                                                             : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400' }}">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $em['dias_mora'] }} días de mora
                                                </span>
                                            @endif

                                            @foreach ($em['tipos'] as $tipo)
                                                @php
                                                    $tipoCfg2 = match ($tipo) {
                                                        'MATRICULA' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
                                                        'COLEGIATURA' => 'bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-400',
                                                        'ARRASTRE' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
                                                        'INSCRIPCION' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400',
                                                        'MULTA' => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
                                                        default => 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400',
                                                    };
                                                @endphp
                                                <span
                                                    class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $tipoCfg2 }}">
                                                    {{ $tipo }}
                                                </span>
                                            @endforeach
                                        </div>

                                        {{-- Detalle de obligaciones vencidas --}}
                                        <div class="mt-4 space-y-2">
                                            @foreach ($em['obligaciones'] as $obv)
                                                <div
                                                    class="flex items-center justify-between px-3 py-2
                                                        bg-red-50 dark:bg-red-950/20 rounded-xl border border-red-100 dark:border-red-900/40 text-xs">
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-800 dark:text-gray-100">{{ $obv->tipo }}</span>
                                                        @if ($obv->descripcion)
                                                            <span class="text-gray-400 ml-1">—
                                                                {{ Str::limit($obv->descripcion, 40) }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-right flex-shrink-0">
                                                        <p class="font-bold text-red-700 dark:text-red-400">
                                                            ${{ number_format($obv->monto_final, 2) }}</p>
                                                        @if ($obv->fecha_vencimiento)
                                                            <p class="text-gray-400">
                                                                {{ $obv->fecha_vencimiento->format('d/m/Y') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-gray-200 dark:border-slate-700 py-16 text-center">
                        <div
                            class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="font-bold text-gray-700 dark:text-gray-200">Sin estudiantes en mora</p>
                        <p class="text-sm text-gray-400 mt-1">No hay obligaciones vencidas en este periodo</p>
                    </div>
                @endif
            @endif
            @endif {{-- end @else (carreraId seleccionada) --}}

        </div>
    </div>
</div>
