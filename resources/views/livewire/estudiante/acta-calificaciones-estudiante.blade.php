<div>
    @if (empty($acta))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="font-semibold text-gray-500">No tienes una matrícula habilitada actualmente</p>
            <p class="text-sm text-gray-400 mt-1">Contacta a secretaría para más información</p>
        </div>
    @else
        @php
            $est = $acta['estudiante'];
            $carrera = $acta['carrera'];
            $matricula = $acta['matricula'];
            $semestres = $acta['semestres'];
            $promedioMalla = $acta['promedio_malla'];
            $titulacion = $acta['titulacion'];
            $mallaCompleta = $acta['malla_completa'];

            $insumoLabels = [
                'insumo1' => 'Asistencia',
                'insumo2' => 'Act. Autónomas',
                'insumo3' => 'Act. Prácticas',
                'insumo4' => 'Act. con Docente',
                'insumo5' => 'Ética',
            ];
        @endphp

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

            {{-- ================================================================
                 ENCABEZADO DEL ACTA
                 ================================================================ --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-blue-950 to-blue-900 px-6 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            {{-- <p class="text-gray-400 text-xs font-semibold uppercase tracking-widest mb-1">
                                Instituto Superior Tecnológico Cumandá
                            </p> --}}
                            <h2 class="text-white text-xl font-bold">Acta de Calificaciones</h2>
                            <p class="text-gray-300 text-sm mt-1">{{ $carrera->name }}</p>
                        </div>
                        <div class="flex flex-col items-start sm:items-end gap-1">
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
                                             bg-blue-600/20 border border-blue-100 text-blue-100 text-xs font-semibold">
                                    En Curso
                                </span>
                            @endif
                            <p class="text-gray-300 text-xs">{{ now()->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Datos del estudiante --}}
                <div class="px-6 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Estudiante</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $est->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Cédula</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $est->cedula ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">N° Matrícula</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $est->matricula_numero ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Modalidad</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $carrera->modalidad }}</p>
                    </div>
                </div>

                {{-- Barra de progreso de malla --}}
                @php
                    $semConDatos = collect($semestres)->where('tiene_datos', true)->count();
                    $totalSem = count($semestres);
                    $porcentajeSem = $totalSem > 0 ? round(($semConDatos / $totalSem) * 100) : 0;
                @endphp
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-500">Avance de malla curricular</p>
                        <p class="text-xs font-bold text-gray-700">{{ $semConDatos }}/{{ $totalSem }} semestres</p>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-500"
                            style="width: {{ $porcentajeSem }}%"></div>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 SEMESTRES
                 ================================================================ --}}
            <div class="space-y-4">
                @foreach ($semestres as $semestre)
                    @if (!$semestre['tiene_datos'])
                        {{-- SEMESTRE SIN DATOS — versión compacta --}}
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
                        {{-- SEMESTRE CON DATOS — tabla completa --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                            {{-- Header semestre --}}
                            <div
                                class="px-5 py-3 bg-gray-50 border-b border-gray-200
                                        flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-gray-800 flex items-center justify-center
                                                text-xs font-bold text-white">
                                        {{ $semestre['semestre_order'] ?? '—' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $semestre['semestre_nombre'] }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $semestre['aprobadas'] }}/{{ $semestre['total_materias'] }} materias
                                            aprobadas
                                        </p>
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

                            {{-- Tabla de materias --}}
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-100">
                                            <th
                                                class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Materia</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide w-24">
                                                <span title="Haz clic en el valor para ver el detalle">Insumos ⓘ</span>
                                            </th>
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
                                                                class="mt-0.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold
                                                                         bg-yellow-100 text-yellow-700 flex-shrink-0">A</span>
                                                        @endif
                                                        <div>
                                                            <p class="font-semibold text-gray-800 text-xs leading-snug">
                                                                {{ $m['materia_nombre'] }}</p>
                                                            <p class="text-xs text-gray-400 font-mono">
                                                                {{ $m['materia_code'] }}</p>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Insumos con toggle --}}
                                                <td class="px-4 py-3 text-center">
                                                    @if (!$m['tiene_calificacion'])
                                                        <span class="text-gray-300 text-xs">—</span>
                                                    @else
                                                        <button wire:click="toggleInsumos({{ $m['materia_id'] }})"
                                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold
                                                                   transition-all duration-200
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

                                                        {{-- Detalle insumos --}}
                                                        @if ($expandedMateria === $m['materia_id'])
                                                            <div
                                                                class="mt-2 text-left bg-indigo-50 border border-indigo-100 rounded-xl p-2.5 space-y-1 min-w-40">
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
                                                    @if (!$m['tiene_calificacion'])
                                                        <span class="text-gray-300 text-xs">—</span>
                                                    @else
                                                        <span
                                                            class="text-xs font-semibold
                                                            {{ $m['examen_parcial'] === null
                                                                ? 'text-gray-400'
                                                                : ($m['examen_parcial'] >= 7
                                                                    ? 'text-gray-700'
                                                                    : 'text-red-500') }}">
                                                            {{ $m['examen_parcial'] !== null ? number_format($m['examen_parcial'], 2) : '—' }}
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- Final --}}
                                                <td class="px-4 py-3 text-center">
                                                    @if (!$m['tiene_calificacion'])
                                                        <span class="text-gray-300 text-xs">—</span>
                                                    @else
                                                        <span
                                                            class="text-xs font-semibold
                                                            {{ $m['examen_final'] === null ? 'text-gray-400' : ($m['examen_final'] >= 7 ? 'text-gray-700' : 'text-red-500') }}">
                                                            {{ $m['examen_final'] !== null ? number_format($m['examen_final'], 2) : '—' }}
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- Nota final --}}
                                                <td class="px-4 py-3 text-center">
                                                    @if (!$m['tiene_calificacion'] || $m['nota_final'] === null)
                                                        <span class="text-gray-300 text-xs">—</span>
                                                    @else
                                                        <span
                                                            class="text-sm font-bold
                                                            {{ $m['nota_final'] >= 7 ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ number_format($m['nota_final'], 2) }}
                                                        </span>
                                                        {{-- Suspenso --}}
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
                                                            $estadoConfig = match ($m['estado_final']) {
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
                                                            class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $estadoConfig[0] }}">
                                                            {{ $estadoConfig[1] }}
                                                        </span>
                                                        @if ($m['numero_intento'] > 1)
                                                            <p class="text-xs text-gray-400 mt-0.5">Intento
                                                                #{{ $m['numero_intento'] }}</p>
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
                 RESUMEN FINAL
                 ================================================================ --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm">Resumen de Egreso</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

                        {{-- Promedio malla --}}
                        <div class="text-center p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Promedio de
                                Malla</p>
                            <p
                                class="text-3xl font-bold
                                {{ $promedioMalla === null ? 'text-gray-400' : ($promedioMalla >= 7 ? 'text-green-600' : 'text-red-500') }}">
                                {{ $promedioMalla !== null ? number_format($promedioMalla, 2) : '—' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">sobre 10.00</p>
                        </div>

                        {{-- Titulación --}}
                        <div
                            class="text-center p-4 rounded-2xl
                                    {{ $titulacion ? 'bg-gray-50 border border-gray-100' : 'bg-gray-50 border border-dashed border-gray-200' }}">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Titulación</p>
                            @if ($titulacion)
                                <p
                                    class="text-3xl font-bold
                                    {{ $titulacion->nota_final_egreso >= 7 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ number_format($titulacion->nota_final_egreso, 2) }}
                                </p>
                                <p
                                    class="text-xs mt-1 font-semibold
                                    {{ $titulacion->estado === 'Aprobado' ? 'text-green-600' : ($titulacion->estado === 'Reprobado' ? 'text-red-500' : 'text-amber-500') }}">
                                    {{ $titulacion->estado }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $titulacion->tipo_titulacion_label }}</p>
                            @else
                                <p class="text-3xl font-bold text-gray-300">—</p>
                                <p class="text-xs text-gray-400 mt-1">Pendiente</p>
                            @endif
                        </div>

                        {{-- Estado general --}}
                        <div
                            class="text-center p-4 rounded-2xl
                                    {{ $titulacion?->estado === 'Aprobado' ? 'bg-green-50 border border-green-100' : 'bg-gray-50 border border-gray-100' }}">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Estado General
                            </p>
                            @if ($titulacion?->estado === 'Aprobado')
                                <div
                                    class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-green-700">Egresado</p>
                            @elseif ($mallaCompleta)
                                <div
                                    class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-amber-700">Malla completa</p>
                                <p class="text-xs text-gray-400 mt-0.5">Proceso de titulación pendiente</p>
                            @else
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-blue-700">En curso</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $semConDatos }}/{{ $totalSem }}
                                    semestres</p>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

        </div>
    @endif
</div>
