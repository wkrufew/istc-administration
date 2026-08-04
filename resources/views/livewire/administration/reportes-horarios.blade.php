<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ═══ HEADER ═══ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reporte de Horarios</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Visualización por período, detección de conflictos y exportación</p>
            </div>
            @if ($periodoId)
                <div class="flex gap-2 flex-wrap">
                    <button wire:click="exportarPDF"
                            wire:loading.attr="disabled"
                            wire:target="exportarPDF"
                            class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 disabled:opacity-50
                                   text-white font-semibold px-4 py-2 rounded-xl text-sm transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span wire:loading.remove wire:target="exportarPDF">Exportar PDF</span>
                        <span wire:loading wire:target="exportarPDF">Generando…</span>
                    </button>
                    <button wire:click="exportarExcel"
                            wire:loading.attr="disabled"
                            wire:target="exportarExcel"
                            class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 disabled:opacity-50
                                   text-white font-semibold px-4 py-2 rounded-xl text-sm transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span wire:loading.remove wire:target="exportarExcel">Exportar Excel</span>
                        <span wire:loading wire:target="exportarExcel">Generando…</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ FILTROS ═══ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                    Período <span class="text-red-400">*</span>
                </label>
                <select wire:model.live="periodoId"
                        class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200
                               shadow-sm text-sm focus:border-lime-500 focus:ring-lime-500 transition">
                    <option value="">— Seleccionar período —</option>
                    @foreach ($this->periodos as $p)
                        <option value="{{ $p->id }}">{{ $p->code }}{{ $p->description ? ' — ' . $p->description : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                    Carrera <span class="text-gray-400 dark:text-gray-500 font-normal">(opcional)</span>
                </label>
                <select wire:model.live="carreraId"
                        @disabled(!$periodoId)
                        class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200
                               shadow-sm text-sm focus:border-lime-500 focus:ring-lime-500 transition
                               disabled:bg-gray-50 dark:disabled:bg-gray-700/50 disabled:text-gray-400 disabled:cursor-not-allowed">
                    <option value="">Todas las carreras</option>
                    @foreach ($this->carreras as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                    Materia <span class="text-gray-400 dark:text-gray-500 font-normal">(opcional)</span>
                </label>
                <select wire:model.live="materiaId"
                        @disabled(!$periodoId)
                        class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200
                               shadow-sm text-sm focus:border-lime-500 focus:ring-lime-500 transition
                               disabled:bg-gray-50 dark:disabled:bg-gray-700/50 disabled:text-gray-400 disabled:cursor-not-allowed">
                    <option value="">Todas las materias</option>
                    @foreach ($this->materias as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ═══ CONTENIDO ═══ --}}
    @if ($periodoId)
        @php
            $stats      = $this->stats;
            $conflictos = $this->conflictos;
            $grilla     = $this->grillaData;
            $dias       = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
            $hayConflictos = $stats['total_conflictos'] > 0;
        @endphp

        {{-- TARJETAS DE ESTADÍSTICAS --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_clases'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-tight">Clases<br>por semana</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['docentes_unicos'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-tight">Docentes<br>activos</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-purple-600">{{ $stats['paralelos_unicos'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-tight">Paralelos<br>con clases</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['horas_semana'] }}h</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-tight">Horas<br>semanales</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-teal-600">{{ $stats['total_creditos'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-tight">Créditos<br>totales</p>
            </div>

            <div class="rounded-2xl border shadow-sm p-4 text-center
                        {{ $hayConflictos
                            ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'
                            : 'bg-green-50 dark:bg-green-900/20 border-green-100 dark:border-green-800' }}">
                @if ($hayConflictos)
                    <div class="flex items-center justify-center gap-1 mb-0.5">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01M12 3C6.477 3 2 7.477 2 12s4.477 9 10 9 10-4.477 10-9S17.523 3 12 3z"/>
                        </svg>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['total_conflictos'] }}</p>
                    </div>
                    <p class="text-xs text-red-500 dark:text-red-400 leading-tight">Conflictos<br>detectados</p>
                @else
                    <div class="flex items-center justify-center gap-1 mb-0.5">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">0</p>
                    </div>
                    <p class="text-xs text-green-600 dark:text-green-400 leading-tight">Sin<br>conflictos</p>
                @endif
            </div>
        </div>

        {{-- PANEL DE CONFLICTOS --}}
        @if ($hayConflictos)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-red-800 dark:text-red-300 text-sm mb-2">
                            {{ count($conflictos) }} conflicto(s) de horario detectado(s) en este período
                        </h3>
                        <div class="space-y-2">
                            @foreach ($conflictos as $conf)
                                @php
                                    $tipoLabel = ['aula' => 'Aula', 'docente' => 'Docente', 'paralelo' => 'Paralelo'][$conf['tipo']] ?? $conf['tipo'];
                                    $tipoBg    = [
                                        'aula'     => 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300',
                                        'docente'  => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300',
                                        'paralelo' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-800 dark:text-orange-300',
                                    ][$conf['tipo']] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                                @endphp
                                <div class="flex flex-wrap items-start gap-2 text-xs text-red-700 dark:text-red-300
                                            bg-white/60 dark:bg-gray-800/60 rounded-xl px-3 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg font-bold text-xs uppercase tracking-wide {{ $tipoBg }}">
                                        {{ $tipoLabel }}
                                    </span>
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $conf['dia'] }}</span>
                                    <span class="text-gray-400">·</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $conf['descripcion'] }}</span>
                                    <span class="text-gray-400">·</span>
                                    <span>
                                        <span class="font-medium">{{ $conf['horario_a']['materia'] }}</span>
                                        <span class="text-gray-400"> ({{ $conf['horario_a']['hora_ini'] }}–{{ $conf['horario_a']['hora_fin'] }})</span>
                                    </span>
                                    <span class="font-bold text-red-400">vs</span>
                                    <span>
                                        <span class="font-medium">{{ $conf['horario_b']['materia'] }}</span>
                                        <span class="text-gray-400"> ({{ $conf['horario_b']['hora_ini'] }}–{{ $conf['horario_b']['hora_fin'] }})</span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-green-800 dark:text-green-300">Sin conflictos detectados — todos los horarios son compatibles</p>
            </div>
        @endif

        {{-- ═══ GRILLA POR CARRERA → SEMESTRE ═══ --}}
        @if (empty($grilla))
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-semibold">No hay horarios registrados con los filtros seleccionados</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Prueba cambiando el período o ajusta los filtros opcionales</p>
            </div>
        @else
            @foreach ($grilla as $carreraData)
                <div class="space-y-4">

                    {{-- Separador carrera --}}
                    <div class="flex items-center gap-3 mt-2">
                        <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-xs uppercase tracking-widest
                                   px-4 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-2xl whitespace-nowrap">
                            {{ $carreraData['nombre'] }}
                        </h3>
                        <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                    </div>

                    @foreach ($carreraData['semestres'] as $semestreData)
                        @php
                            $totalClasesSem = collect($semestreData['dias'])->sum(fn($c) => count($c));
                            $diasOcupados   = collect($semestreData['dias'])->filter(fn($c) => count($c) > 0)->count();
                        @endphp

                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

                            {{-- Cabecera semestre --}}
                            <div class="px-5 py-3 bg-gray-800 dark:bg-gray-900 flex items-center justify-between">
                                <h4 class="font-bold text-white text-sm">{{ $semestreData['nombre'] }}</h4>
                                <span class="text-xs text-gray-400 tabular-nums">
                                    {{ $totalClasesSem }} clase(s) · {{ $diasOcupados }} día(s) activo(s)
                                </span>
                            </div>

                            {{-- Columnas de días (igual que horario estudiante) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 dark:divide-gray-700">
                                @foreach ($dias as $dia)
                                    @php $clases = $semestreData['dias'][$dia] ?? []; $tieneClases = count($clases) > 0; @endphp

                                    <div class="flex flex-col">
                                        {{-- Cabecera del día --}}
                                        <div class="px-4 py-3 flex items-center justify-between min-h-[48px]
                                                    {{ $tieneClases ? 'bg-gray-700 dark:bg-gray-700' : 'bg-gray-50 dark:bg-gray-700/30' }}">
                                            <p class="text-sm font-bold {{ $tieneClases ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                                {{ $dia }}
                                            </p>
                                            @if ($tieneClases)
                                                <span class="w-6 h-6 rounded-lg bg-white/15 flex items-center justify-center text-xs font-bold text-white">
                                                    {{ count($clases) }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Clases --}}
                                        <div class="p-1.5 space-y-2.5 flex-1">
                                            @if (!$tieneClases)
                                                <div class="flex flex-col items-center justify-center py-8 text-gray-300 dark:text-gray-600">
                                                    <svg class="w-7 h-7 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4"/>
                                                    </svg>
                                                    <p class="text-xs">Libre</p>
                                                </div>
                                            @else
                                                @foreach ($clases as $clase)
                                                    @php
                                                        $badgeModalidad = [
                                                            'Presencial'     => ['bg' => '#16a34a', 'label' => 'Presencial'],
                                                            'Virtual'        => ['bg' => '#2563eb', 'label' => 'Virtual'],
                                                            'Híbrida'        => ['bg' => '#7c3aed', 'label' => 'Híbrida'],
                                                            'Semipresencial' => ['bg' => '#ea580c', 'label' => 'Semi'],
                                                        ][$clase['modalidad']] ?? ['bg' => '#6b7280', 'label' => $clase['modalidad'] ?? ''];
                                                    @endphp

                                                    {{-- Tarjeta de clase --}}
                                                    <div class="relative rounded-xl overflow-hidden border shadow-sm
                                                                hover:shadow-md hover:-translate-y-0.5 transition-all duration-200
                                                                {{ $clase['conflicto']
                                                                    ? 'border-red-300 dark:border-red-700 ring-1 ring-red-300/60'
                                                                    : 'border-gray-100 dark:border-gray-700' }}">

                                                        {{-- Badge modalidad: esquina superior derecha --}}
                                                        @if ($clase['modalidad'])
                                                            <div class="absolute top-0 right-0 rounded-bl-xl z-10
                                                                        px-2 py-1 flex items-center justify-center
                                                                        text-white text-xs font-bold leading-none"
                                                                 style="background-color: {{ $badgeModalidad['bg'] }}">
                                                                {{ $badgeModalidad['label'] }}
                                                            </div>
                                                        @endif

                                                        {{-- Badge conflicto --}}
                                                        @if ($clase['conflicto'])
                                                            <div class="absolute top-6 right-1 z-10">
                                                                <span class="flex items-center justify-center w-5 h-5
                                                                             rounded-full bg-red-500 text-white text-xs font-bold
                                                                             shadow-sm leading-none">!</span>
                                                            </div>
                                                        @endif

                                                        <div class="flex">
                                                            {{-- Barra de color --}}
                                                            <div style="background-color: {{ $clase['color'] }}; width: 4px;" class="flex-shrink-0"></div>

                                                            <div class="flex-1 p-3 space-y-2 min-w-0">

                                                                {{-- Hora --}}
                                                                <div class="flex items-center gap-1.5 pr-16">
                                                                    <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 tabular-nums">
                                                                        {{ $clase['hora_inicio'] }}<span class="text-gray-400 font-normal mx-0.5">-</span>{{ $clase['hora_fin'] }}
                                                                    </span>
                                                                </div>

                                                                {{-- Materia --}}
                                                                <div>
                                                                    <p class="text-xs font-bold text-gray-900 dark:text-gray-100 leading-snug break-words">
                                                                        {{ $clase['materia'] }}
                                                                    </p>
                                                                    @if ($clase['materia_code'])
                                                                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $clase['materia_code'] }}</p>
                                                                    @endif
                                                                </div>

                                                                {{-- Docente con avatar --}}
                                                                @if ($clase['docente'])
                                                                    <div class="flex items-start gap-1.5">
                                                                        <div class="w-5 h-5 rounded-full flex items-center justify-center
                                                                                    text-white text-xs font-bold flex-shrink-0 mt-0.5"
                                                                             style="background-color: {{ $clase['color'] }}">
                                                                            {{ strtoupper(substr($clase['docente'], 0, 1)) }}
                                                                        </div>
                                                                        <span class="text-xs text-gray-600 dark:text-gray-400 leading-snug break-words min-w-0">
                                                                            {{ $clase['docente'] }}
                                                                        </span>
                                                                    </div>
                                                                @endif

                                                                {{-- Footer: aula izquierda, paralelo derecha --}}
                                                                @if ($clase['aula'] || $clase['paralelo'])
                                                                    <div class="flex items-center justify-between pt-1.5 border-t border-gray-100 dark:border-gray-700 gap-1">
                                                                        @if ($clase['aula'])
                                                                            <span class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                                                </svg>
                                                                                Aula {{ $clase['aula'] }}
                                                                            </span>
                                                                        @else
                                                                            <span></span>
                                                                        @endif
                                                                        @if ($clase['paralelo'])
                                                                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 flex-shrink-0">
                                                                                {{ $clase['paralelo'] }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif

    @else
        {{-- Estado inicial vacío --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-lime-50 dark:bg-lime-900/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-700 dark:text-gray-300 font-semibold text-lg">Selecciona un período para comenzar</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Los horarios, estadísticas y conflictos se cargarán automáticamente</p>
        </div>
    @endif

    {{-- Indicador de carga --}}
    <div wire:loading.delay
         class="fixed bottom-5 right-5 flex items-center gap-2 bg-gray-900 text-white
                text-xs font-semibold px-4 py-2.5 rounded-2xl shadow-xl z-50">
        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Calculando…
    </div>
</div>
