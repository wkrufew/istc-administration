<div>
    <div>
        {{-- SELECTOR DE PERIODO --}}
        <div class="w-full px-4 sm:px-6 lg:px-8 pt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Panel de Administración</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Resumen general del instituto, métricas y alertas.</p>
                </div>
                <div class="flex items-center gap-3">
                    <label
                        class="text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Periodo</label>
                    <select wire:model.live="periodoId"
                        class="rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-semibold min-w-40">
                        @foreach ($this->periodos as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->code }}{{ $p->is_current ? ' (Actual)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- LOADING OVERLAY --}}
        <div wire:loading wire:target="periodoId"
            class="fixed inset-0 z-50 bg-white/60 backdrop-blur-sm flex items-center justify-center">
            <div class="flex items-center gap-3 bg-white rounded-2xl shadow-xl px-6 py-4 border border-slate-200">
                <svg class="animate-spin w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                </svg>
                <span class="text-sm font-semibold text-slate-700">Actualizando dashboard...</span>
            </div>
        </div>

        <div class="w-full px-4 sm:px-6 lg:px-8 pb-8 space-y-6">

            {{-- ================================================================
             FILA 1 — STATS GLOBALES
             ================================================================ --}}
            @php
                $sg = $this->statsGlobales;
                $sp = $this->statsPeriodo;
                $sf = $this->statsFinancieros;
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Estudiantes --}}
                <div
                    class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm hover:shadow-md transition group">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Estudiantes</p>
                            <p class="mt-1 text-3xl font-bold text-slate-900">
                                {{ number_format($sg['total_estudiantes']) }}</p>
                        </div>
                        <div
                            class="h-11 w-11 rounded-xl bg-sky-500/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    @if ($sg['nuevos_este_mes'] > 0)
                        <p class="mt-3 text-xs font-semibold text-emerald-600">▲ +{{ $sg['nuevos_este_mes'] }} este mes
                        </p>
                    @else
                        <p class="mt-3 text-xs text-slate-400">Total registrados</p>
                    @endif
                </div>

                {{-- Docentes --}}
                <div
                    class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm hover:shadow-md transition group">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Docentes</p>
                            <p class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($sg['total_docentes']) }}
                            </p>
                        </div>
                        <div
                            class="h-11 w-11 rounded-xl bg-violet-500/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">
                        @if (!empty($sp))
                            <span class="text-violet-600 font-semibold">{{ $sp['docentes_asignados'] }}</span> asignados
                            este periodo
                        @else
                            Total registrados
                        @endif
                    </p>
                </div>

                {{-- Carreras --}}
                <div
                    class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm hover:shadow-md transition group">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Carreras</p>
                            <p class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($sg['total_carreras']) }}
                            </p>
                        </div>
                        <div
                            class="h-11 w-11 rounded-xl bg-amber-500/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">Programas activos</p>
                </div>

                {{-- Materias --}}
                <div
                    class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm hover:shadow-md transition group">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Materias</p>
                            <p class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($sg['total_materias']) }}
                            </p>
                        </div>
                        <div
                            class="h-11 w-11 rounded-xl bg-emerald-500/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">Incluye todas las carreras</p>
                </div>
            </div>

            {{-- ================================================================
             FILA 2 — STATS DEL PERIODO
             ================================================================ --}}
            @if (!empty($sp))
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Matrículas --}}
                    <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Matrículas</p>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">Periodo</span>
                        </div>
                        <p class="text-3xl font-bold text-slate-900">{{ $sp['matriculas_habilitadas'] }}</p>
                        <div class="mt-3 space-y-1 text-xs text-slate-500">
                            <p>Total: <span class="font-semibold text-slate-700">{{ $sp['matriculas_total'] }}</span>
                            </p>
                            <p>Pendientes pago: <span
                                    class="font-semibold text-amber-600">{{ $sp['matriculas_pendientes'] }}</span></p>
                            <p>Borrador: <span
                                    class="font-semibold text-slate-500">{{ $sp['matriculas_borrador'] }}</span></p>
                        </div>
                    </div>

                    {{-- Docentes asignados --}}
                    <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Docentes</p>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">Periodo</span>
                        </div>
                        <p class="text-3xl font-bold text-slate-900">{{ $sp['docentes_asignados'] }}</p>
                        <div class="mt-3 space-y-1 text-xs text-slate-500">
                            <p>Paralelos activos: <span
                                    class="font-semibold text-slate-700">{{ $sp['paralelos_activos'] }}</span></p>
                            @if ($sp['docentes_sin_asig'] > 0)
                                <p>Sin asignar: <span
                                        class="font-semibold text-red-500">{{ $sp['docentes_sin_asig'] }}</span></p>
                            @else
                                <p>Sin asignar: <span class="font-semibold text-emerald-600">0</span></p>
                            @endif
                            @if ($sp['materias_sin_horario'] > 0)
                                <p>Materias sin horario: <span
                                        class="font-semibold text-amber-600">{{ $sp['materias_sin_horario'] }}</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Tasa de aprobación --}}
                    <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Aprobación</p>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 font-semibold">Periodo</span>
                        </div>
                        <p
                            class="text-3xl font-bold {{ $sp['tasa_aprobacion'] >= 70 ? 'text-emerald-600' : ($sp['tasa_aprobacion'] ? 'text-amber-600' : 'text-slate-400') }}">
                            {{ $sp['tasa_aprobacion'] !== null ? $sp['tasa_aprobacion'] . '%' : '—' }}
                        </p>
                        <div class="mt-3 space-y-1 text-xs text-slate-500">
                            <p>Aprobados: <span class="font-semibold text-emerald-600">{{ $sp['aprobados'] }}</span>
                            </p>
                            <p>Total evaluados: <span
                                    class="font-semibold text-slate-700">{{ $sp['total_calificaciones'] }}</span></p>
                        </div>
                    </div>

                    {{-- Ocupación paralelos --}}
                    <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Ocupación</p>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">Paralelos</span>
                        </div>
                        <p class="text-3xl font-bold text-slate-900">
                            {{ $sp['ocupacion_promedio'] !== null ? $sp['ocupacion_promedio'] . '%' : '—' }}
                        </p>
                        <div class="mt-3">
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="h-2 rounded-full bg-amber-500 transition-all duration-500"
                                    style="width: {{ $sp['ocupacion_promedio'] ?? 0 }}%"></div>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Promedio de {{ $sp['paralelos_activos'] }}
                                paralelos</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ================================================================
             LAYOUT PRINCIPAL: 2/3 + 1/3
             ================================================================ --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- COLUMNA IZQUIERDA (2/3) --}}
                <div class="xl:col-span-2 space-y-6">

                    {{-- CARD FINANCIERO GRANDE --}}
                    @if (!empty($sf))
                        <div class="rounded-2xl bg-gradient-to-br from-sky-600 to-indigo-700 text-white shadow-lg p-6">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                                <div>
                                    <p class="text-white/70 text-sm">Recaudación del periodo</p>
                                    <p class="text-4xl font-extrabold mt-1">
                                        ${{ number_format($sf['total_pagado'], 2) }}</p>
                                    <p class="text-white/70 text-xs mt-1">
                                        de ${{ number_format($sf['total_obligaciones'], 2) }} en obligaciones totales
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="inline-flex items-center gap-2 bg-white/15 rounded-xl px-4 py-2">
                                        <span class="text-2xl font-bold">{{ $sf['pct_recaudado'] }}%</span>
                                        <span class="text-white/70 text-xs">recaudado</span>
                                    </div>
                                    {{-- Barra de progreso --}}
                                    <div class="w-full bg-white/20 rounded-full h-2 mt-3">
                                        <div class="h-2 rounded-full bg-white transition-all duration-700"
                                            style="width: {{ $sf['pct_recaudado'] }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="rounded-xl bg-white/10 p-3">
                                    <p class="text-white/60 text-xs">Pagado</p>
                                    <p class="text-lg font-bold mt-0.5">${{ number_format($sf['total_pagado'], 2) }}
                                    </p>
                                    <p class="text-white/60 text-xs mt-0.5">{{ $sf['count_pagado'] }} obligaciones</p>
                                </div>
                                <div class="rounded-xl bg-white/10 p-3">
                                    <p class="text-white/60 text-xs">Pendiente</p>
                                    <p class="text-lg font-bold mt-0.5">
                                        ${{ number_format($sf['total_pendiente'], 2) }}</p>
                                    <p class="text-white/60 text-xs mt-0.5">{{ $sf['count_pendiente'] }} obligaciones
                                    </p>
                                </div>
                                <div class="rounded-xl bg-white/10 p-3">
                                    <p class="text-white/60 text-xs">Vencido</p>
                                    <p
                                        class="text-lg font-bold mt-0.5 {{ $sf['total_vencido'] > 0 ? 'text-red-300' : '' }}">
                                        ${{ number_format($sf['total_vencido'], 2) }}
                                    </p>
                                    <p class="text-white/60 text-xs mt-0.5">{{ $sf['count_vencido'] }} obligaciones
                                    </p>
                                </div>
                                <div class="rounded-xl bg-white/10 p-3">
                                    <p class="text-white/60 text-xs">En revisión</p>
                                    <p class="text-lg font-bold mt-0.5">{{ $sf['pagos_en_revision'] }}</p>
                                    <p class="text-white/60 text-xs mt-0.5">comprobantes</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ESTADO ACADÉMICO (barras) --}}
                    @if (!empty($this->estadoAcademico))
                        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                            <h3 class="font-bold text-slate-900 text-base mb-5">Estado académico del periodo</h3>
                            <div class="space-y-4">
                                @foreach ($this->estadoAcademico as $bar)
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-sm text-slate-600">{{ $bar['label'] }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-slate-400">{{ $bar['valor'] }}</span>
                                                <span
                                                    class="text-sm font-bold text-slate-900">{{ $bar['pct'] }}%</span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2.5">
                                            <div class="h-2.5 rounded-full {{ $bar['color'] }} transition-all duration-700"
                                                style="width: {{ $bar['pct'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ÚLTIMAS MATRÍCULAS --}}
                    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-5 flex items-center justify-between border-b border-slate-100">
                            <div>
                                <h3 class="font-bold text-slate-900">Últimas matrículas</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Registros más recientes del periodo</p>
                            </div>
                            <a href="{{ route('administracion.administrativa.matriculacion.index') ?? '#' }}"
                                class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition">
                                Ver todas →
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100">
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                            Estudiante</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                            Carrera</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                            Estado</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                            Fecha</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($this->ultimasMatriculas as $mat)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-5 py-3">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600
                                                            flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                        {{ strtoupper(substr($mat->estudiante?->name ?? '?', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-slate-800 text-xs">
                                                            {{ $mat->estudiante?->name ?? '—' }}</p>
                                                        <p class="text-xs text-slate-400">
                                                            {{ $mat->estudiante?->cedula ?? '' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3">
                                                <span class="text-xs font-semibold text-slate-600">
                                                    {{ $mat->carrera?->code ?? '—' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-center">
                                                @php
                                                    $cfg = match ($mat->estado) {
                                                        'Habilitada' => 'bg-emerald-100 text-emerald-700',
                                                        'Pendiente_Pago' => 'bg-amber-100 text-amber-700',
                                                        'Borrador' => 'bg-slate-100 text-slate-600',
                                                        'Cancelada' => 'bg-red-100 text-red-600',
                                                        default => 'bg-slate-100 text-slate-500',
                                                    };
                                                    $lbl = match ($mat->estado) {
                                                        'Pendiente_Pago' => 'Pend. Pago',
                                                        default => $mat->estado,
                                                    };
                                                @endphp
                                                <span
                                                    class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $cfg }}">
                                                    {{ $lbl }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-center text-xs text-slate-400">
                                                {{ $mat->created_at?->diffForHumans() ?? '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-xs">
                                                No hay matrículas en este periodo
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- COLUMNA DERECHA (1/3) --}}
                <div class="space-y-6">

                    {{-- ESTADO DEL PERIODO --}}
                    @if ($this->periodoActual)
                        @php $pa = $this->periodoActual; @endphp
                        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-slate-900">Estado del periodo</h3>
                                @if ($pa->is_current)
                                    <span
                                        class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">Actual</span>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-500">Código</p>
                                    <span class="text-sm font-bold text-slate-900">{{ $pa->code }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-500">Inicio</p>
                                    <span
                                        class="text-sm font-bold text-slate-900">{{ $pa->fecha_inicio?->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-500">Fin</p>
                                    <span
                                        class="text-sm font-bold text-slate-900">{{ $pa->fecha_fin?->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-500">Límite matrícula</p>
                                    <span
                                        class="text-sm font-bold {{ now()->gt($pa->fecha_limite_matricula) ? 'text-red-500' : 'text-slate-900' }}">
                                        {{ $pa->fecha_limite_matricula?->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-500">Límite pago</p>
                                    <span
                                        class="text-sm font-bold {{ now()->gt($pa->fecha_limite_pago) ? 'text-red-500' : 'text-slate-900' }}">
                                        {{ $pa->fecha_limite_pago?->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                                    <p class="text-sm text-slate-500">Matrículas abiertas</p>
                                    @if (now()->lte($pa->fecha_limite_matricula))
                                        <span class="text-sm font-bold text-emerald-600">Sí</span>
                                    @else
                                        <span class="text-sm font-bold text-red-500">Cerradas</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ALERTAS --}}
                    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                        <h3 class="font-bold text-slate-900 mb-4">Alertas del sistema</h3>
                        <div class="space-y-3">
                            @foreach ($this->alertas as $alerta)
                                @php
                                    $alertConfig = match ($alerta['tipo']) {
                                        'error' => ['bg-red-50 border-red-200', 'text-red-700', 'text-red-500'],
                                        'warning' => [
                                            'bg-amber-50 border-amber-200',
                                            'text-amber-700',
                                            'text-amber-500',
                                        ],
                                        'info' => ['bg-sky-50 border-sky-200', 'text-sky-700', 'text-sky-500'],
                                        'success' => [
                                            'bg-emerald-50 border-emerald-200',
                                            'text-emerald-700',
                                            'text-emerald-500',
                                        ],
                                        default => [
                                            'bg-slate-50 border-slate-200',
                                            'text-slate-700',
                                            'text-slate-500',
                                        ],
                                    };
                                    $iconPath = match ($alerta['icono']) {
                                        'exclamation'
                                            => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                                        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'eye'
                                            => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                                        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                        'calendar'
                                            => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                        default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                    };
                                @endphp
                                <div class="rounded-xl border p-3 {{ $alertConfig[0] }}">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 {{ $alertConfig[2] }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $iconPath }}" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold {{ $alertConfig[1] }}">
                                                {{ $alerta['mensaje'] }}</p>
                                            <p class="text-xs {{ $alertConfig[2] }} mt-0.5">{{ $alerta['detalle'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ESTUDIANTES POR CARRERA --}}
                    @if (!empty($this->estudiantesPorCarrera))
                        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                            <h3 class="font-bold text-slate-900 mb-4">Estudiantes por carrera</h3>
                            <div class="space-y-3">
                                @foreach ($this->estudiantesPorCarrera as $ec)
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-lg">
                                                    {{ $ec['carrera'] }}
                                                </span>
                                                <span class="text-xs text-slate-500 truncate max-w-28"
                                                    title="{{ $ec['nombre'] }}">
                                                    {{ $ec['nombre'] }}
                                                </span>
                                            </div>
                                            <span class="text-xs font-bold text-slate-900">{{ $ec['total'] }}</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full bg-gradient-to-r from-sky-500 to-indigo-500 transition-all duration-500"
                                                style="width: {{ $ec['pct'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ACCIONES RÁPIDAS --}}
                    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                        <h3 class="font-bold text-slate-900 mb-4">Acciones rápidas</h3>
                        <div class="space-y-2">
                            @php
                                $acciones = [
                                    [
                                        'label' => 'Registrar estudiante',
                                        'tag' => 'Nuevo',
                                        'color' => 'text-emerald-600',
                                        'route' => '#',
                                    ],
                                    [
                                        'label' => 'Nueva matrícula',
                                        'tag' => 'Académico',
                                        'color' => 'text-sky-600',
                                        'route' => '#',
                                    ],
                                    [
                                        'label' => 'Asignar docente',
                                        'tag' => 'Gestión',
                                        'color' => 'text-violet-600',
                                        'route' => '#',
                                    ],
                                    [
                                        'label' => 'Ver obligaciones',
                                        'tag' => 'Urgente',
                                        'color' => 'text-red-600',
                                        'route' => '#',
                                    ],
                                    [
                                        'label' => 'Reportes académicos',
                                        'tag' => 'Consulta',
                                        'color' => 'text-amber-600',
                                        'route' => '#',
                                    ],
                                ];
                            @endphp
                            @foreach ($acciones as $ac)
                                <a href="{{ $ac['route'] }}"
                                    class="flex items-center justify-between px-4 py-3 rounded-xl border border-slate-200
                                       hover:bg-slate-50 transition">
                                    <span class="text-sm font-semibold text-slate-800">{{ $ac['label'] }}</span>
                                    <span
                                        class="text-xs font-semibold {{ $ac['color'] }}">{{ $ac['tag'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
