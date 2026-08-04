<div class="max-w-7xl mx-auto px-4 py-6 space-y-5">

    {{-- ═══════════════════════════════════════ HEADER ═══════════════════════ --}}
    <div class="bg-slate-900 border border-slate-700/50 relative overflow-hidden rounded-xl">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-lime-600 to-sky-700 flex items-center justify-center shadow-lg flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-white/90 leading-none">Métricas de Tickets</h1>
                    <p class="text-xs text-lime-400/70 tracking-widest uppercase mt-1">Administración · Soporte</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                {{-- Selector de periodo --}}
                <div class="inline-flex rounded-xl overflow-hidden border border-slate-700 text-xs">
                    @foreach(['7' => '7 días', '30' => '30 días', '90' => '90 días', 'all' => 'Todo'] as $val => $lbl)
                    <button wire:click="$set('periodo', '{{ $val }}')"
                        class="px-3 py-2 font-medium transition-colors
                               {{ $periodo === $val ? 'bg-lime-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-slate-200' }}">
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>

                <a href="{{ route('administracion.administrativa.tickets.index') }}"
                   wire:navigate
                   class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a tickets
                </a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════ TILES PRINCIPALES ═══════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
        $tiles = [
            ['label' => 'Total',       'value' => $this->resumen['total'],      'color' => 'text-gray-800 dark:text-gray-100',    'icon_bg' => 'bg-gray-100 dark:bg-gray-800',     'icon_color' => 'text-gray-500 dark:text-gray-400'],
            ['label' => 'Pendientes',  'value' => $this->resumen['pendientes'], 'color' => 'text-yellow-700 dark:text-yellow-400', 'icon_bg' => 'bg-yellow-100 dark:bg-yellow-900/30','icon_color' => 'text-yellow-600 dark:text-yellow-400'],
            ['label' => 'En proceso',  'value' => $this->resumen['en_proceso'], 'color' => 'text-blue-700 dark:text-blue-400',    'icon_bg' => 'bg-blue-100 dark:bg-blue-900/30',  'icon_color' => 'text-blue-600 dark:text-blue-400'],
            ['label' => 'Cerrados',    'value' => $this->resumen['cerrados'],   'color' => 'text-gray-600 dark:text-gray-400',    'icon_bg' => 'bg-gray-100 dark:bg-gray-800',     'icon_color' => 'text-gray-500 dark:text-gray-400'],
            ['label' => 'Urgentes',    'value' => $this->resumen['urgentes'],   'color' => 'text-red-700 dark:text-red-400',      'icon_bg' => 'bg-red-100 dark:bg-red-900/30',    'icon_color' => 'text-red-600 dark:text-red-400'],
        ];
        @endphp
        @foreach($tiles as $tile)
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 flex flex-col gap-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $tile['label'] }}</p>
            <p class="text-3xl font-bold {{ $tile['color'] }}">{{ $tile['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════ TIEMPOS PROMEDIO ════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @php
        $formatHoras = function(?float $h): string {
            if ($h === null) return '—';
            if ($h < 1)  return round($h * 60) . ' min';
            if ($h < 24) return number_format($h, 1) . ' h';
            return number_format($h / 24, 1) . ' días';
        };
        @endphp

        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tiempo prom. primera respuesta</p>
                <p class="text-2xl font-bold text-sky-700 dark:text-sky-400 mt-0.5">{{ $formatHoras($this->tiempos['respuesta']) }}</p>
                @if($this->tiempos['respuesta'] === null)
                    <p class="text-xs text-gray-400 dark:text-gray-500">Sin datos en el periodo</p>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tiempo prom. resolución</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-400 mt-0.5">{{ $formatHoras($this->tiempos['resolucion']) }}</p>
                @if($this->tiempos['resolucion'] === null)
                    <p class="text-xs text-gray-400 dark:text-gray-500">Sin datos en el periodo</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════ GRÁFICAS DONUT + BARRAS ══════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Donut — por estado --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Distribución por estado</h3>
            <div class="flex items-center gap-6">
                <div class="relative flex-shrink-0" style="width:160px;height:160px">
                    <canvas id="chart-donut" width="160" height="160"></canvas>
                    @if(array_sum($this->chartData['porEstado']) === 0)
                    <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400">Sin datos</div>
                    @endif
                </div>
                <div class="flex flex-col gap-2 flex-1 min-w-0">
                    @php
                    $estadoMeta = [
                        'pendiente'  => ['Pendiente',  'bg-yellow-400'],
                        'en_proceso' => ['En proceso', 'bg-blue-500'],
                        'cerrado'    => ['Cerrado',    'bg-gray-400'],
                    ];
                    $totalEstado = max(1, array_sum($this->chartData['porEstado']));
                    @endphp
                    @foreach($this->chartData['porEstado'] as $key => $val)
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $estadoMeta[$key][1] }} flex-shrink-0"></span>
                        <span class="text-xs text-gray-600 dark:text-gray-400 flex-1 truncate">{{ $estadoMeta[$key][0] }}</span>
                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200 tabular-nums">{{ $val }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 w-8 text-right tabular-nums">{{ $totalEstado > 0 ? round($val / $totalEstado * 100) : 0 }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Barras — por prioridad --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Tickets por prioridad</h3>
            <div style="position:relative;height:160px">
                <canvas id="chart-barras"></canvas>
                @if(array_sum($this->chartData['porPrioridad']) === 0)
                <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400">Sin datos</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════ LÍNEA — EVOLUCIÓN ═══════════ --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Tickets creados en el tiempo
            @if($periodo === 'all')
                <span class="text-xs font-normal text-gray-400 ml-1">(últimos 90 días)</span>
            @endif
        </h3>
        <div style="position:relative;height:200px">
            <canvas id="chart-linea"></canvas>
            @if(array_sum($this->chartData['values']) === 0)
            <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400">Sin datos en el periodo</div>
            @endif
        </div>
    </div>

</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const instances = {};

    function isDark() {
        return document.documentElement.classList.contains('dark');
    }

    function gridColor()  { return isDark() ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.07)'; }
    function labelColor() { return isDark() ? '#9ca3af' : '#6b7280'; }

    function destroyAll() {
        ['donut','barras','linea'].forEach(k => {
            if (instances[k]) { instances[k].destroy(); delete instances[k]; }
        });
    }

    function buildCharts() {
        const data = @js($this->chartData);

        destroyAll();

        // ── Donut ────────────────────────────────────────────────────────────
        const cDonut = document.getElementById('chart-donut');
        if (cDonut) {
            instances.donut = new Chart(cDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Pendiente', 'En proceso', 'Cerrado'],
                    datasets: [{
                        data: Object.values(data.porEstado),
                        backgroundColor: ['#facc15','#3b82f6','#9ca3af'],
                        borderWidth: 0,
                        hoverOffset: 4,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: { legend: { display: false }, tooltip: { boxPadding: 4 } },
                },
            });
        }

        // ── Barras ───────────────────────────────────────────────────────────
        const cBars = document.getElementById('chart-barras');
        if (cBars) {
            instances.barras = new Chart(cBars, {
                type: 'bar',
                data: {
                    labels: ['Baja', 'Media', 'Alta', 'Urgente'],
                    datasets: [{
                        label: 'Tickets',
                        data: Object.values(data.porPrioridad),
                        backgroundColor: ['#d1d5db','#60a5fa','#fb923c','#f87171'],
                        borderRadius: 6,
                        borderSkipped: false,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: gridColor() }, ticks: { color: labelColor(), font: { size: 11 } } },
                        y: { grid: { color: gridColor() }, ticks: { color: labelColor(), font: { size: 11 }, stepSize: 1 }, beginAtZero: true },
                    },
                },
            });
        }

        // ── Línea ────────────────────────────────────────────────────────────
        const cLine = document.getElementById('chart-linea');
        if (cLine) {
            instances.linea = new Chart(cLine, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Tickets',
                        data: data.values,
                        borderColor: '#84cc16',
                        backgroundColor: 'rgba(132,204,22,0.12)',
                        borderWidth: 2,
                        pointRadius: data.labels.length > 30 ? 0 : 3,
                        pointHoverRadius: 4,
                        fill: true,
                        tension: 0.3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: gridColor() }, ticks: { color: labelColor(), font: { size: 10 }, maxRotation: 45, autoSkip: true, maxTicksLimit: 20 } },
                        y: { grid: { color: gridColor() }, ticks: { color: labelColor(), font: { size: 11 }, stepSize: 1 }, beginAtZero: true },
                    },
                },
            });
        }
    }

    document.addEventListener('DOMContentLoaded', buildCharts);
    document.addEventListener('livewire:navigated', buildCharts);
    document.addEventListener('livewire:updated', buildCharts);
})();
</script>
@endpush
