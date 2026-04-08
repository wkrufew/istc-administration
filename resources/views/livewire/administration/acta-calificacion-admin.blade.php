<div>
    @if (empty($acta))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="font-semibold text-gray-500">Este estudiante no tiene matrícula habilitada</p>
            {{-- <a href="{{ route('administracion.administrativa.proceso-titulacion.index') }}"
                class="inline-flex items-center gap-2 mt-4 text-sm text-blue-600 hover:underline">
                ← Volver al listado
            </a> --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('administracion.administrativa.proceso-titulacion.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Volver al listado
                </a>
                <button wire:click="exportarPdf" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                   bg-red-600 hover:bg-red-700 text-white text-sm font-semibold
                   shadow-sm transition disabled:opacity-60">
                    <span wire:loading.remove wire:target="exportarPdf">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar PDF
                    </span>
                    <span wire:loading wire:target="exportarPdf" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                        Generando...
                    </span>
                </button>
            </div>
        </div>
    @else
        @php
            $carrera = $acta['carrera'];
            $matricula = $acta['matricula'];
            $semestres = $acta['semestres'];
            $promedioMalla = $acta['promedio_malla'];
            $titulacion = $acta['titulacion'];
            $practica = $acta['practica'];
            $intentos = $acta['intentos'];
            $mallaCompleta = $acta['malla_completa'];

            $semConDatos = collect($semestres)->where('tiene_datos', true)->count();
            $totalSem = count($semestres);
            $pct = $totalSem > 0 ? round(($semConDatos / $totalSem) * 100) : 0;

            $totalAprobadas = collect($semestres)->sum('aprobadas');
            $totalReprobadas = collect($semestres)->sum('reprobadas');
            $totalMaterias = collect($semestres)->sum('total_materias');

            $insumoLabels = [
                'insumo1' => 'Asistencia',
                'insumo2' => 'Act. Autónomas',
                'insumo3' => 'Act. Prácticas',
                'insumo4' => 'Act. con Docente',
                'insumo5' => 'Ética',
            ];
        @endphp

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 space-y-4">

            {{-- BOTON VOLVER --}}
            {{-- <div>
                <a href="{{ route('administracion.administrativa.proceso-titulacion.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Volver al listado
                </a>
            </div> --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('administracion.administrativa.proceso-titulacion.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Volver al listado
                </a>
                <button wire:click="exportarPdf" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                   bg-red-600 hover:bg-red-700 text-white text-sm font-semibold
                   shadow-sm transition disabled:opacity-60">
                    <span wire:loading.remove wire:target="exportarPdf" class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar PDF
                    </span>
                    <span wire:loading wire:target="exportarPdf" class="flex items-center space-x-2 flex-shrink-0">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="2" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                        Generando...
                    </span>
                </button>
            </div>

            {{-- ================================================================
                 ENCABEZADO
                 ================================================================ --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-widest mb-1">
                                Acta de Calificaciones — Administración
                            </p>
                            <h2 class="text-white text-xl font-bold">{{ $estudiante->name }}</h2>
                            <p class="text-gray-300 text-sm mt-1">{{ $carrera->name }}</p>
                        </div>
                        <div class="flex flex-col items-start sm:items-end gap-2">
                            @if ($mallaCompleta)
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                             bg-green-500/20 border border-green-500/30 text-green-400 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Malla Completa
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                             bg-blue-500/20 border border-blue-500/30 text-blue-400 text-xs font-semibold">
                                    En Curso
                                </span>
                            @endif
                            <p class="text-gray-400 text-xs">Generado: {{ now()->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Datos del estudiante --}}
                <div class="px-6 py-4 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Cédula</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $estudiante->cedula ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">N° Matrícula</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $estudiante->matricula_numero ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Email</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5 truncate">{{ $estudiante->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Teléfono</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $estudiante->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Carrera</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $carrera->code }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Modalidad</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $carrera->modalidad }}</p>
                    </div>
                </div>

                {{-- Stats rápidas + barra de progreso --}}
                <div class="px-6 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 border-b border-gray-100">
                    <div class="text-center p-3 rounded-xl bg-blue-50">
                        <p class="text-xl font-bold text-blue-600">{{ $totalMaterias }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Total materias</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-green-50">
                        <p class="text-xl font-bold text-green-600">{{ $totalAprobadas }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Aprobadas</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-red-50">
                        <p class="text-xl font-bold text-red-500">{{ $totalReprobadas }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Reprobadas</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-indigo-50">
                        <p class="text-xl font-bold text-indigo-600">
                            {{ $promedioMalla ? number_format($promedioMalla, 2) : '—' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">Promedio malla</p>
                    </div>
                </div>

                {{-- Barra de progreso --}}
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-500">Avance de malla curricular</p>
                        <p class="text-xs font-bold text-gray-700">{{ $semConDatos }}/{{ $totalSem }} semestres
                        </p>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-500"
                            style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 SEMESTRES
                 ================================================================ --}}
            <div class="space-y-4">
                @foreach ($semestres as $semestre)
                    @if (!$semestre['tiene_datos'])
                        <div
                            class="bg-white rounded-2xl border border-dashed border-gray-200 px-5 py-4
                                    flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center
                                            text-xs font-bold text-gray-400">
                                    {{ $semestre['semestre_order'] ?? '—' }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500">{{ $semestre['semestre_nombre'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $semestre['total_materias'] }} materias</p>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                         bg-gray-100 text-gray-500">
                                Pendiente
                            </span>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                            {{-- Header semestre --}}
                            <div
                                class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-gray-800 flex items-center justify-center
                                                text-xs font-bold text-white">
                                        {{ $semestre['semestre_order'] ?? '—' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $semestre['semestre_nombre'] }}
                                        </p>
                                        <div class="flex items-center gap-3 mt-0.5">
                                            <span class="text-xs text-green-600 font-semibold">
                                                {{ $semestre['aprobadas'] }} aprobadas
                                            </span>
                                            @if ($semestre['reprobadas'] > 0)
                                                <span class="text-xs text-red-500 font-semibold">
                                                    {{ $semestre['reprobadas'] }} reprobadas
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400">Promedio</p>
                                    <p
                                        class="text-lg font-bold
                                        {{ $semestre['promedio'] === null
                                            ? 'text-gray-400'
                                            : ($semestre['promedio'] >= 7
                                                ? 'text-green-600'
                                                : 'text-red-500') }}">
                                        {{ $semestre['promedio'] !== null ? number_format($semestre['promedio'], 2) : '—' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Tabla --}}
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-100">
                                            <th
                                                class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Materia</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Paralelo</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide w-24">
                                                Insumos ⓘ</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide w-20">
                                                Parcial</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide w-20">
                                                Final</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide w-20">
                                                Nota</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide w-24">
                                                Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach ($semestre['materias'] as $m)
                                            <tr class="hover:bg-gray-50 transition">
                                                {{-- Materia --}}
                                                <td class="px-4 py-3">
                                                    <div class="flex items-start gap-2">
                                                        @if ($m['es_arrastre'])
                                                            <span
                                                                class="mt-0.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold
                                                                         bg-yellow-100 text-yellow-700 flex-shrink-0">A</span>
                                                        @endif
                                                        <div>
                                                            <p
                                                                class="font-semibold text-gray-800 text-xs leading-snug">
                                                                {{ $m['materia_nombre'] }}</p>
                                                            <p class="text-xs text-gray-400 font-mono">
                                                                {{ $m['materia_code'] }}</p>
                                                            @if ($m['periodo'])
                                                                <p class="text-xs text-gray-400 mt-0.5">
                                                                    {{ $m['periodo'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Paralelo --}}
                                                <td class="px-4 py-3 text-center">
                                                    <span class="text-xs font-semibold text-gray-500">
                                                        {{ $m['paralelo'] ?? '—' }}
                                                    </span>
                                                </td>

                                                {{-- Insumos con toggle --}}
                                                <td class="px-4 py-3 text-center">
                                                    @if (!$m['tiene_calificacion'])
                                                        <span class="text-gray-300 text-xs">—</span>
                                                    @else
                                                        <button wire:click="toggleInsumos({{ $m['materia_id'] }})"
                                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold transition-all duration-200
                                                                   {{ $expandedMateria === $m['materia_id']
                                                                       ? 'bg-indigo-100 text-indigo-700'
                                                                       : 'bg-gray-100 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                                            {{ $m['promedio_insumos'] !== null ? number_format($m['promedio_insumos'], 2) : '—' }}
                                                            <svg class="w-3 h-3 transition-transform {{ $expandedMateria === $m['materia_id'] ? 'rotate-180' : '' }}"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </button>
                                                        @if ($expandedMateria === $m['materia_id'])
                                                            <div
                                                                class="mt-2 text-left bg-indigo-50 border border-indigo-100 rounded-xl p-2.5 space-y-1 min-w-44">
                                                                @foreach ($insumoLabels as $key => $label)
                                                                    <div
                                                                        class="flex items-center justify-between gap-3">
                                                                        <span
                                                                            class="text-xs text-gray-500">{{ $label }}</span>
                                                                        <span
                                                                            class="text-xs font-semibold
                                                                            {{ $m[$key] === null ? 'text-gray-400' : ($m[$key] >= 7 ? 'text-green-600' : 'text-red-500') }}">
                                                                            {{ $m[$key] !== null ? number_format($m[$key], 2) : '—' }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    @endif
                                                </td>

                                                {{-- Parcial --}}
                                                <td class="px-4 py-3 text-center">
                                                    <span
                                                        class="text-xs font-semibold {{ $m['examen_parcial'] === null ? 'text-gray-300' : ($m['examen_parcial'] >= 7 ? 'text-gray-700' : 'text-red-500') }}">
                                                        {{ $m['examen_parcial'] !== null ? number_format($m['examen_parcial'], 2) : '—' }}
                                                    </span>
                                                </td>

                                                {{-- Final --}}
                                                <td class="px-4 py-3 text-center">
                                                    <span
                                                        class="text-xs font-semibold {{ $m['examen_final'] === null ? 'text-gray-300' : ($m['examen_final'] >= 7 ? 'text-gray-700' : 'text-red-500') }}">
                                                        {{ $m['examen_final'] !== null ? number_format($m['examen_final'], 2) : '—' }}
                                                    </span>
                                                </td>

                                                {{-- Nota final --}}
                                                <td class="px-4 py-3 text-center">
                                                    @if ($m['nota_final'] === null)
                                                        <span class="text-gray-300 text-xs">—</span>
                                                    @else
                                                        <span
                                                            class="text-sm font-bold {{ $m['nota_final'] >= 7 ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ number_format($m['nota_final'], 2) }}
                                                        </span>
                                                        @if ($m['nota_suspenso'] !== null)
                                                            <p class="text-xs text-amber-600 font-semibold mt-0.5">
                                                                Sus: {{ number_format($m['nota_suspenso'], 2) }}
                                                            </p>
                                                        @endif
                                                    @endif
                                                </td>

                                                {{-- Estado --}}
                                                <td class="px-4 py-3 text-center">
                                                    @if (!$m['tiene_calificacion'])
                                                        <span
                                                            class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-400">
                                                            Pendiente
                                                        </span>
                                                    @else
                                                        @php
                                                            $cfg = match ($m['estado_final']) {
                                                                'Aprobado' => [
                                                                    'bg-green-100 text-green-700',
                                                                    'Aprobado',
                                                                ],
                                                                'Reprobado' => ['bg-red-100 text-red-700', 'Reprobado'],
                                                                'Retirado' => ['bg-gray-100 text-gray-600', 'Retirado'],
                                                                'Incompleto' => [
                                                                    'bg-amber-100 text-amber-700',
                                                                    'Incompleto',
                                                                ],
                                                                'Suspenso_Pendiente' => [
                                                                    'bg-orange-100 text-orange-700',
                                                                    'Suspenso',
                                                                ],
                                                                default => ['bg-gray-100 text-gray-500', 'Pendiente'],
                                                            };
                                                        @endphp
                                                        <span
                                                            class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $cfg[0] }}">
                                                            {{ $cfg[1] }}
                                                        </span>
                                                        @if ($m['numero_intento'] > 1)
                                                            <p class="text-xs text-gray-400 mt-0.5">
                                                                Intento #{{ $m['numero_intento'] }}
                                                            </p>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- ================================================================
                 PRÁCTICAS PREPROFESIONALES
                 ================================================================ --}}
            @if ($practica)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800 text-sm">Prácticas Preprofesionales</h3>
                        @php
                            $practicaConfig = match ($practica->estado) {
                                'Completada' => 'bg-green-100 text-green-700',
                                'En_Curso' => 'bg-blue-100 text-blue-700',
                                'Reprobada' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $practicaConfig }}">
                            {{ str_replace('_', ' ', $practica->estado) }}
                        </span>
                    </div>
                    <div class="p-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Empresa</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $practica->empresa }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Sector</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $practica->sector ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Tutor empresa</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $practica->tutor_empresa }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Cargo estudiante</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $practica->cargo_estudiante }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Fecha inicio</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">
                                {{ $practica->fecha_inicio?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Fecha fin</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">
                                {{ $practica->fecha_fin?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total horas</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $practica->total_horas ?? '—' }} hrs
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Nota</p>
                            <p
                                class="text-lg font-bold mt-0.5 {{ $practica->nota >= 7 ? 'text-green-600' : ($practica->nota ? 'text-red-500' : 'text-gray-400') }}">
                                {{ $practica->nota ? number_format($practica->nota, 2) : '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ================================================================
                 RESUMEN DE EGRESO + HISTORIAL DE INTENTOS
                 ================================================================ --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Notas de egreso --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800 text-sm">Resumen de Egreso</h3>
                    </div>
                    <div class="p-6 grid grid-cols-3 gap-4">
                        <div class="text-center p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Promedio Malla
                            </p>
                            <p
                                class="text-3xl font-bold {{ $promedioMalla === null ? 'text-gray-400' : ($promedioMalla >= 7 ? 'text-green-600' : 'text-red-500') }}">
                                {{ $promedioMalla !== null ? number_format($promedioMalla, 2) : '—' }}
                            </p>
                        </div>
                        <div class="text-center p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Titulación</p>
                            @if ($titulacion)
                                <p
                                    class="text-3xl font-bold {{ $titulacion->nota_final_egreso >= 7 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ number_format($titulacion->nota_final_egreso, 2) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $titulacion->tipo_titulacion_label }}</p>
                            @else
                                <p class="text-3xl font-bold text-gray-300">—</p>
                                <p class="text-xs text-gray-400 mt-1">Pendiente</p>
                            @endif
                        </div>
                        <div
                            class="text-center p-4 rounded-2xl {{ $titulacion?->estado === 'Aprobado' ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-100' }}">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Estado Final
                            </p>
                            @if ($titulacion?->estado === 'Aprobado')
                                <p class="text-2xl font-bold text-green-600">Egresado</p>
                            @elseif ($titulacion?->estado === 'Reprobado')
                                <p class="text-2xl font-bold text-red-500">Reprobado</p>
                            @elseif ($mallaCompleta)
                                <p class="text-lg font-bold text-amber-600">En titulación</p>
                            @else
                                <p class="text-lg font-bold text-blue-600">En curso</p>
                            @endif
                        </div>
                    </div>

                    {{-- Tribunal --}}
                    @if ($titulacion?->presidente_tribunal)
                        <div class="px-6 pb-5 border-t border-gray-100 pt-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Tribunal</p>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-gray-400">Presidente</p>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $titulacion->presidente_tribunal }}</p>
                                </div>
                                @if ($titulacion->miembro_tribunal_1)
                                    <div>
                                        <p class="text-xs text-gray-400">Miembro 1</p>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $titulacion->miembro_tribunal_1 }}</p>
                                    </div>
                                @endif
                                @if ($titulacion->miembro_tribunal_2)
                                    <div>
                                        <p class="text-xs text-gray-400">Miembro 2</p>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $titulacion->miembro_tribunal_2 }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Historial de intentos --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 bg-gray-50 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800 text-sm">Historial de Intentos</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Proceso de titulación</p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse ($intentos as $intento)
                            <div class="px-5 py-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-6 h-6 rounded-lg {{ $intento->estado === 'Aprobado' ? 'bg-green-100 text-green-700' : ($intento->estado === 'Reprobado' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') }}
                                                     flex items-center justify-center text-xs font-bold">
                                            {{ $intento->numero_intento }}
                                        </span>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-700">
                                                {{ $intento->tipo_titulacion_label }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $intento->fecha_evaluacion?->format('d/m/Y') ?? 'Sin fecha' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p
                                            class="text-sm font-bold {{ $intento->nota_final_egreso >= 7 ? 'text-green-600' : ($intento->nota_final_egreso ? 'text-red-500' : 'text-gray-400') }}">
                                            {{ $intento->nota_final_egreso ? number_format($intento->nota_final_egreso, 2) : '—' }}
                                        </p>
                                        <span
                                            class="text-xs {{ $intento->estado === 'Aprobado' ? 'text-green-600' : ($intento->estado === 'Reprobado' ? 'text-red-500' : 'text-amber-500') }}">
                                            {{ $intento->estado }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center text-gray-400 text-xs">
                                Sin intentos registrados
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    @endif
</div>
