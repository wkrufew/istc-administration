<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-lg p-5">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Reporte de Asistencias</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Análisis gráfico por materia y paralelo</p>
        </div>

        {{-- FILTROS --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Filtros</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div>
                    <label
                        class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-1.5">Periodo</label>
                    <select wire:model.live="periodoId"
                        class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600 px-3 py-2.5 dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-0">
                        @foreach ($this->periodos as $p)
                            <option value="{{ $p->id }}">{{ $p->code }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-1.5">Materia</label>
                    <select wire:model.live="materiaId"
                        class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600 px-3 py-2.5 dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-0 disabled:opacity-40"
                        @disabled(!$periodoId)>
                        <option value="">Seleccionar...</option>
                        @foreach ($this->materiasAsignadas as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-1.5">Paralelo</label>
                    <select wire:model.live="paraleloId"
                        class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600 px-3 py-2.5 dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-0 disabled:opacity-40"
                        @disabled(!$materiaId)>
                        <option value="">Seleccionar...</option>
                        @foreach ($this->paralelos as $par)
                            <option value="{{ $par->id }}">{{ $par->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-1.5">
                        Horario <span class="text-slate-400 font-normal">(opcional)</span>
                    </label>
                    <select wire:model.live="horarioId"
                        class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600 px-3 py-2.5 dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-0 disabled:opacity-40"
                        @disabled(!$paraleloId)>
                        <option value="">Todos los horarios</option>
                        @foreach ($this->horarios as $h)
                            <option value="{{ $h->id }}">
                                {{ $h->dia_semana }} · {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- LOADING --}}
        <div wire:loading wire:target="periodoId,materiaId,paraleloId,horarioId"
            class="flex items-center justify-center py-10">
            <div
                class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-2xl shadow px-6 py-4 border border-slate-200 dark:border-gray-700">
                <svg class="animate-spin w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                </svg>
                <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Generando reporte...</span>
            </div>
        </div>

        @if (!$paraleloId)
            <div wire:loading.remove
                class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-slate-200 dark:border-gray-700 py-16 text-center">
                <svg class="w-14 h-14 mx-auto mb-4 text-slate-200 dark:text-gray-600" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="font-semibold text-slate-400 dark:text-slate-500">Selecciona periodo, materia y paralelo para
                    ver el reporte</p>
            </div>
        @endif

        @if ($paraleloId)
            @php $r = $this->reporte; @endphp

            @if (!empty($r))
                <div wire:loading.remove wire:target="periodoId,materiaId,paraleloId,horarioId" class="space-y-6">

                    {{-- STATS GLOBALES --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-5">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Asistencia general
                            </p>
                            <p
                                class="text-3xl font-extrabold mt-1 {{ $r['pct_asistencia_general'] >= 80 ? 'text-emerald-600' : ($r['pct_asistencia_general'] >= 60 ? 'text-amber-500' : 'text-red-600') }}">
                                {{ $r['pct_asistencia_general'] }}%
                            </p>
                            <div class="w-full bg-slate-100 dark:bg-gray-700 rounded-full h-2 mt-2">
                                <div class="h-2 rounded-full {{ $r['pct_asistencia_general'] >= 80 ? 'bg-emerald-500' : ($r['pct_asistencia_general'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                                    style="width: {{ $r['pct_asistencia_general'] }}%"></div>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-5">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Clases tomadas</p>
                            <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">
                                {{ $r['clases_tomadas'] }}<span
                                    class="text-lg font-semibold text-slate-400">/{{ $r['total_clases'] }}</span>
                            </p>
                            <p class="text-xs text-slate-400 mt-1">{{ $r['total_estudiantes'] }} estudiantes</p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-5">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">En riesgo</p>
                            <p class="text-3xl font-extrabold text-amber-500 mt-1">{{ $r['en_riesgo'] }}</p>
                            <p class="text-xs text-slate-400 mt-1">entre 60% y 79%</p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-5">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Críticos</p>
                            <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $r['criticos'] }}</p>
                            <p class="text-xs text-slate-400 mt-1">menos del 60%</p>
                        </div>
                    </div>

                    {{-- DISTRIBUCIÓN DE ESTADOS --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-5">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4">Distribución total de
                            estados</p>
                        @php
                            $dist = [
                                [
                                    'label' => 'Presentes',
                                    'val' => $r['total_presentes'],
                                    'color' => 'bg-emerald-500',
                                    'text' => 'text-emerald-600',
                                ],
                                [
                                    'label' => 'Ausentes',
                                    'val' => $r['total_ausentes'],
                                    'color' => 'bg-red-500',
                                    'text' => 'text-red-600',
                                ],
                                [
                                    'label' => 'Tardanzas',
                                    'val' => $r['total_tardanzas'],
                                    'color' => 'bg-amber-500',
                                    'text' => 'text-amber-600',
                                ],
                                [
                                    'label' => 'Justificados',
                                    'val' => $r['total_justif'],
                                    'color' => 'bg-blue-500',
                                    'text' => 'text-blue-600',
                                ],
                            ];
                            $totalDist = max($r['total_registros'], 1);
                        @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ($dist as $d)
                                <div class="rounded-xl bg-slate-50 dark:bg-gray-900/40 p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span
                                            class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $d['label'] }}</span>
                                        <span
                                            class="text-sm font-extrabold {{ $d['text'] }}">{{ $d['val'] }}</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $d['color'] }}"
                                            style="width: {{ round(($d['val'] / $totalDist) * 100) }}%"></div>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1">{{ round(($d['val'] / $totalDist) * 100) }}%
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- GRÁFICOS --}}
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                        {{-- BARRAS POR ESTUDIANTE --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">% Asistencia por
                                    estudiante</p>
                                <div class="flex items-center gap-3 text-xs text-slate-400">
                                    <span class="flex items-center gap-1"><span
                                            class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>OK</span>
                                    <span class="flex items-center gap-1"><span
                                            class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>Riesgo</span>
                                    <span class="flex items-center gap-1"><span
                                            class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>Crítico</span>
                                </div>
                            </div>
                            <div wire:ignore class="relative"
                                style="height: {{ max(count($r['por_estudiante']) * 36 + 40, 200) }}px">
                                <canvas id="chartBarras"
                                    data-estudiantes="{{ json_encode($r['por_estudiante'], JSON_HEX_QUOT | JSON_HEX_APOS) }}">
                                </canvas>
                            </div>
                        </div>

                        {{-- LÍNEA DE TENDENCIA --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Tendencia por fecha</p>
                                @if (count($r['tendencia']) > 0)
                                    <span class="text-xs text-slate-400">{{ count($r['tendencia']) }} clases
                                        registradas</span>
                                @endif
                            </div>
                            @if (count($r['tendencia']) >= 1)
                                <div wire:ignore class="relative" style="height: 300px">
                                    <canvas id="chartTendencia"
                                        data-tendencia="{{ json_encode($r['tendencia'], JSON_HEX_QUOT | JSON_HEX_APOS) }}">
                                    </canvas>
                                </div>
                            @elseif (count($r['tendencia']) === 1)
                                <div class="flex flex-col items-center justify-center h-48 text-center">
                                    <p
                                        class="text-4xl font-extrabold {{ $r['tendencia'][0]['pct'] >= 80 ? 'text-emerald-600' : ($r['tendencia'][0]['pct'] >= 60 ? 'text-amber-500' : 'text-red-600') }}">
                                        {{ $r['tendencia'][0]['pct'] }}%
                                    </p>
                                    <p class="text-sm text-slate-400 mt-2">Primera clase —
                                        {{ $r['tendencia'][0]['fecha'] }}</p>
                                    <p class="text-xs text-slate-400 mt-1">Necesitas más clases para ver la tendencia
                                    </p>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center h-48 text-center">
                                    <p class="text-slate-400 text-sm font-semibold">Sin clases registradas aún</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- TABLA SEMÁFORO --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
                        <div
                            class="px-5 py-4 border-b border-slate-100 dark:border-gray-700 flex items-center justify-between">
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Resumen por estudiante</p>
                            <div class="flex items-center gap-4 text-xs text-slate-500">
                                <span class="flex items-center gap-1.5"><span
                                        class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>OK (>=
                                    80%)</span>
                                <span class="flex items-center gap-1.5"><span
                                        class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>Riesgo
                                    (60-79%)</span>
                                <span class="flex items-center gap-1.5"><span
                                        class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>Critico (<
                                        60%)</span>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr
                                        class="bg-slate-50 dark:bg-gray-900/40 border-b border-slate-100 dark:border-gray-700">
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                            #</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                            Estudiante</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                            Presentes</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                            Ausentes</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                            Tardanzas</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                            Justificados</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                            % Asistencia</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                            Nota</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                                    @foreach ($r['por_estudiante'] as $i => $est)
                                        @php
                                            $semaforo =
                                                $est['estado'] === 'ok'
                                                    ? 'bg-emerald-500'
                                                    : ($est['estado'] === 'riesgo'
                                                        ? 'bg-amber-500'
                                                        : 'bg-red-500');
                                            $fila =
                                                $est['estado'] === 'critico'
                                                    ? 'bg-red-50/40 dark:bg-red-900/10'
                                                    : ($est['estado'] === 'riesgo'
                                                        ? 'bg-amber-50/40 dark:bg-amber-900/10'
                                                        : '');
                                        @endphp
                                        <tr
                                            class="hover:bg-slate-50 dark:hover:bg-gray-900/20 transition {{ $fila }}">
                                            <td class="px-5 py-3">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-2.5 h-2.5 rounded-full {{ $semaforo }} flex-shrink-0">
                                                    </div>
                                                    <span class="text-xs text-slate-400">{{ $i + 1 }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3">
                                                <p class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                                    {{ $est['nombre'] }}</p>
                                                <p class="text-xs text-slate-400">{{ $est['matricula'] }}</p>
                                            </td>
                                            <td class="px-5 py-3 text-center font-semibold text-emerald-600">
                                                {{ $est['presentes'] }}</td>
                                            <td class="px-5 py-3 text-center font-semibold text-red-600">
                                                {{ $est['ausentes'] }}</td>
                                            <td class="px-5 py-3 text-center font-semibold text-amber-600">
                                                {{ $est['tardanzas'] }}</td>
                                            <td class="px-5 py-3 text-center font-semibold text-blue-600">
                                                {{ $est['justificados'] }}</td>
                                            <td class="px-5 py-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 bg-slate-100 dark:bg-gray-700 rounded-full h-2">
                                                        <div class="h-2 rounded-full {{ $semaforo }}"
                                                            style="width: {{ $est['pct'] }}%"></div>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold text-slate-700 dark:text-slate-300 w-10 text-right">{{ $est['pct'] }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3 text-center">
                                                <span
                                                    class="text-sm font-extrabold {{ $est['nota'] >= 8 ? 'text-emerald-600' : ($est['nota'] >= 6 ? 'text-amber-500' : 'text-red-600') }}">
                                                    {{ number_format($est['nota'], 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            @else
                <div wire:loading.remove wire:target="periodoId,materiaId,paraleloId,horarioId"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-slate-200 dark:border-gray-700 py-16 text-center">
                    <p class="font-semibold text-slate-400">Sin datos de asistencia registrados para esta selección</p>
                </div>
            @endif
        @endif

        {{-- SCRIPTS: datos via data-attributes para evitar conflicto con Alpine/Livewire --}}
        @script
            <script>
                window.dibujarGraficos = function() {
                    const canvasB = document.getElementById('chartBarras');
                    const canvasL = document.getElementById('chartTendencia');

                    ['chartBarras', 'chartTendencia'].forEach(id => {
                        const inst = Chart.getChart(id);
                        if (inst) inst.destroy();
                    });

                    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
                    Chart.defaults.font.size = 11;

                    // ── BARRAS HORIZONTALES ─────────────────────────────────────
                    if (canvasB && canvasB.dataset.estudiantes) {
                        //const datos = JSON.parse(canvasB.dataset.estudiantes);
                        const datos = JSON.parse(canvasB.dataset.estudiantes || '[]');
                        const colores = datos.map(e =>
                            e.pct >= 80 ? 'rgba(16,185,129,0.85)' :
                            e.pct >= 60 ? 'rgba(245,158,11,0.85)' :
                            'rgba(239,68,68,0.85)'
                        );
                        const nombres = datos.map(e => {
                            const p = e.nombre.trim().split(/\s+/);
                            return p.length >= 2 ? p[0] + ' ' + p[p.length - 1] : e.nombre;
                        });

                        new Chart(canvasB, {
                            type: 'bar',
                            data: {
                                labels: nombres,
                                datasets: [{
                                    label: '% Asistencia',
                                    data: datos.map(e => e.pct),
                                    backgroundColor: colores,
                                    borderRadius: 6,
                                    borderSkipped: false,
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: ctx => {
                                                const d = datos[ctx.dataIndex];
                                                return [
                                                    ' ' + d.pct + '% asistencia',
                                                    ' ' + d.presentes + '/' + d.total_clases +
                                                    ' clases asistidas',
                                                    ' Nota: ' + d.nota + '/10',
                                                ];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        min: 0,
                                        max: 100,
                                        grid: {
                                            color: 'rgba(0,0,0,0.04)'
                                        },
                                        ticks: {
                                            callback: v => v + '%',
                                            color: '#94a3b8',
                                            maxTicksLimit: 6
                                        }
                                    },
                                    y: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            color: '#475569',
                                            font: {
                                                size: 11,
                                                weight: '500'
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // ── LÍNEA DE TENDENCIA ──────────────────────────────────────
                    if (canvasL && canvasL.dataset.tendencia) {
                        //const tendencia = JSON.parse(canvasL.dataset.tendencia);
                        const tendencia = JSON.parse(canvasL.dataset.tendencia || '[]');
                        if (tendencia.length < 2) return;

                        const pcts = tendencia.map(d => d.pct);
                        const promedio = Math.round(pcts.reduce((a, b) => a + b, 0) / pcts.length);
                        const ctx2d = canvasL.getContext('2d');
                        const grad = ctx2d.createLinearGradient(0, 0, 0, 280);
                        grad.addColorStop(0, 'rgba(99,102,241,0.25)');
                        grad.addColorStop(1, 'rgba(99,102,241,0.00)');

                        new Chart(canvasL, {
                            type: 'line',
                            data: {
                                labels: tendencia.map(d => d.fecha),
                                datasets: [{
                                        label: '% Asistencia',
                                        data: pcts,
                                        borderColor: 'rgb(99,102,241)',
                                        backgroundColor: grad,
                                        borderWidth: 2.5,
                                        pointRadius: 5,
                                        pointHoverRadius: 7,
                                        pointBackgroundColor: pcts.map(p =>
                                            p >= 80 ? 'rgb(16,185,129)' :
                                            p >= 60 ? 'rgb(245,158,11)' :
                                            'rgb(239,68,68)'
                                        ),
                                        pointBorderColor: '#fff',
                                        pointBorderWidth: 2,
                                        fill: true,
                                        tension: 0.35,
                                    },
                                    {
                                        label: 'Promedio (' + promedio + '%)',
                                        data: tendencia.map(() => promedio),
                                        borderColor: 'rgba(148,163,184,0.7)',
                                        borderWidth: 1.5,
                                        borderDash: [6, 4],
                                        pointRadius: 0,
                                        fill: false,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            color: '#94a3b8',
                                            boxWidth: 12,
                                            padding: 12
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: ctx => {
                                                if (ctx.datasetIndex === 0) {
                                                    const d = tendencia[ctx.dataIndex];
                                                    return [
                                                        ' ' + d.pct + '% asistencia',
                                                        ' ' + d.presentes + ' presentes / ' + d.ausentes +
                                                        ' ausentes',
                                                        ' Total: ' + d.total + ' estudiantes',
                                                    ];
                                                }
                                                return ' Promedio: ' + ctx.raw + '%';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        min: 0,
                                        max: 100,
                                        grid: {
                                            color: 'rgba(0,0,0,0.04)'
                                        },
                                        ticks: {
                                            callback: v => v + '%',
                                            color: '#94a3b8',
                                            maxTicksLimit: 6
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            color: '#94a3b8'
                                        }
                                    }
                                }
                            }
                        });
                    }
                };

                // ── Escuchar actualizaciones de Livewire ────────────────────────
                $wire.$watch('paraleloId', () => setTimeout(window.dibujarGraficos, 150));
                $wire.$watch('horarioId', () => setTimeout(window.dibujarGraficos, 150));
                $wire.$watch('materiaId', () => setTimeout(window.dibujarGraficos, 150));

                // Primer render (si ya hay datos al montar)
                setTimeout(window.dibujarGraficos, 150);
            </script>
        @endscript
    </div>
