<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Encabezado --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 tracking-tight">
                    📘 Acta de Calificaciones
                </h2>
                <p class="text-sm md:text-base text-gray-500 mt-1">
                    Visualiza tus materias matriculadas, notas y estado final de forma clara.
                </p>
            </div>

            {{-- Select periodo --}}
            <div class="w-full md:w-80">
                <label class="text-sm font-semibold text-gray-700">Periodo</label>

                <div class="mt-1 relative">
                    <select wire:model.live="periodo_id"
                        class="w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @foreach ($periodos as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->code }}
                                ({{ \Carbon\Carbon::parse($p->fecha_inicio)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($p->fecha_fin)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Resumen --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">

            {{-- Total --}}
            <div class="rounded-2xl p-4 border bg-white shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Total Materias
                    </p>
                    <span class="text-xl">📚</span>
                </div>
                <p class="text-3xl font-extrabold text-gray-800 mt-2">
                    {{ $resumen['total_materias'] }}
                </p>
            </div>

            {{-- Aprobadas --}}
            <div
                class="rounded-2xl p-4 border bg-gradient-to-br from-green-50 to-white shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-green-700 uppercase tracking-wide">
                        Aprobadas
                    </p>
                    <span class="text-xl">✅</span>
                </div>
                <p class="text-3xl font-extrabold text-green-700 mt-2">
                    {{ $resumen['aprobadas'] }}
                </p>
            </div>

            {{-- Reprobadas --}}
            <div
                class="rounded-2xl p-4 border bg-gradient-to-br from-red-50 to-white shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-red-700 uppercase tracking-wide">
                        Reprobadas
                    </p>
                    <span class="text-xl">❌</span>
                </div>
                <p class="text-3xl font-extrabold text-red-700 mt-2">
                    {{ $resumen['reprobadas'] }}
                </p>
            </div>

            {{-- Promedio --}}
            <div
                class="rounded-2xl p-4 border bg-gradient-to-br from-blue-50 to-white shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">
                        Promedio General
                    </p>
                    <span class="text-xl">📊</span>
                </div>
                <p class="text-3xl font-extrabold text-blue-700 mt-2">
                    {{ number_format($resumen['promedio_general'], 2) }}
                </p>
            </div>
        </div>

        {{-- Tabla / Contenedor --}}
        <div class="mt-7 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="p-5 border-b bg-gradient-to-r from-blue-50 via-white to-slate-50">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    🧾 Materias matriculadas
                </h3>

                @if ($matricula)
                    <div class="mt-2 flex flex-col md:flex-row md:items-center md:gap-4 text-sm text-gray-600">
                        <p>
                            Código Matrícula:
                            <span class="font-bold text-gray-800">{{ $matricula->code }}</span>
                        </p>

                        <p class="hidden md:block text-gray-400">•</p>

                        <p>
                            Fecha:
                            <span class="font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($matricula->fecha_matricula)->format('d/m/Y') }}
                            </span>
                        </p>
                    </div>
                @endif
            </div>

            {{-- Mensajes --}}
            @if (!$matricula)
                <div class="p-10 text-center">
                    <div class="text-5xl mb-2">📭</div>
                    <p class="text-gray-700 font-semibold">
                        No tienes matrícula registrada en este periodo.
                    </p>
                    <p class="text-gray-500 text-sm mt-1">
                        Selecciona otro periodo si deseas revisar historiales anteriores.
                    </p>
                </div>
            @elseif(count($filas) === 0)
                <div class="p-10 text-center">
                    <div class="text-5xl mb-2">🗂️</div>
                    <p class="text-gray-700 font-semibold">
                        No hay materias inscritas en este periodo.
                    </p>
                    <p class="text-gray-500 text-sm mt-1">
                        Si esto es un error, consulta con secretaría académica.
                    </p>
                </div>
            @else
                {{-- TABLA (Desktop) --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                            <tr>
                                <th class="px-1 py-6 text-left font-bold">Materia</th>
                                <th class="px-1 py-6 text-left font-bold -rotate-45">Paralelo</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Asistencia</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Actividades Autonomas</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Actividades Practicas</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Actividades con el docente</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Etica</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Promedio</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">T. Parcial</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">E. Final</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Nota</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Suspenso</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Estado</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Docente</th>
                                <th class="px-1 py-6 text-center font-bold -rotate-45">Fecha</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($filas as $f)
                                @php
                                    $estado = $f['estado_final'];

                                    $badge = match ($estado) {
                                        'Aprobado' => 'bg-green-100 text-green-800 ring-1 ring-green-200',
                                        'Reprobado' => 'bg-red-100 text-red-800 ring-1 ring-red-200',
                                        'Suspenso_Pendiente' => 'bg-yellow-100 text-yellow-900 ring-1 ring-yellow-200',
                                        'Retirado' => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200',
                                        'Incompleto' => 'bg-orange-100 text-orange-800 ring-1 ring-orange-200',
                                        default => 'bg-blue-100 text-blue-800 ring-1 ring-blue-200',
                                    };

                                    $notaFinal = $f['nota_final'];
                                    $notaMin = $f['nota_minima'] ?? 7;

                                    $notaClass =
                                        $notaFinal !== null && $notaFinal >= $notaMin
                                            ? 'text-green-700'
                                            : ($notaFinal !== null
                                                ? 'text-red-700'
                                                : 'text-gray-700');
                                @endphp

                                <tr class="hover:bg-blue-50/40 transition {{ $badge }} {{-- {{ $f['estado_final'] == 'Aprobado' ? 'bg-blue-200/50' : 'bg-red-200/50' }} --}}">
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-gray-900">
                                            {{ $f['materia'] }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <span class="font-semibold">{{ $f['materia_code'] }}</span>
                                            · Repetición: <span class="font-semibold">{{ $f['es_repeticion'] }}</span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-gray-700 font-semibold">
                                        {{ $f['paralelo'] }}
                                    </td>

                                    {{-- Insumos --}}
                                    @foreach (['insumo1', 'insumo2', 'insumo3', 'insumo4', 'insumo5'] as $campo)
                                        <td class="px-3 py-4 text-center text-gray-700">
                                            {{ $f[$campo] !== null ? number_format($f[$campo], 2) : '—' }}
                                        </td>
                                    @endforeach

                                    <td class="px-3 py-4 text-center font-bold text-gray-900">
                                        {{ $f['promedio_insumos'] !== null ? number_format($f['promedio_insumos'], 2) : '—' }}
                                    </td>

                                    <td class="px-3 py-4 text-center text-gray-700">
                                        {{ $f['examen_parcial'] !== null ? number_format($f['examen_parcial'], 2) : '—' }}
                                    </td>

                                    <td class="px-3 py-4 text-center text-gray-700">
                                        {{ $f['examen_final'] !== null ? number_format($f['examen_final'], 2) : '—' }}
                                    </td>

                                    <td class="px-3 py-4 text-center font-extrabold {{ $notaClass }}">
                                        {{ $f['nota_final'] !== null ? number_format($f['nota_final'], 2) : '—' }}
                                    </td>

                                    <td class="px-3 py-4 text-center text-gray-700">
                                        {{ $f['nota_suspenso'] !== null ? number_format($f['nota_suspenso'], 2) : '—' }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                            {{ str_replace('_', ' ', $estado) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-gray-700">
                                        {{ $f['docente'] }}
                                    </td>

                                    <td class="px-4 py-4 text-gray-500">
                                        {{ $f['fecha_calificada'] ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE (Cards) --}}
                <div class="md:hidden p-4 space-y-4 bg-gray-50">
                    @foreach ($filas as $f)
                        @php
                            $estado = $f['estado_final'];

                            $badge = match ($estado) {
                                'Aprobado' => 'bg-green-100 text-green-800',
                                'Reprobado' => 'bg-red-100 text-red-800',
                                'Suspenso_Pendiente' => 'bg-yellow-100 text-yellow-900',
                                'Retirado' => 'bg-gray-200 text-gray-700',
                                'Incompleto' => 'bg-orange-100 text-orange-800',
                                default => 'bg-blue-100 text-blue-800',
                            };
                        @endphp

                        <div class="bg-white border rounded-2xl shadow-sm p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-extrabold text-gray-900">
                                        {{ $f['materia'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $f['materia_code'] }} · Paralelo:
                                        <span class="font-semibold text-gray-700">{{ $f['paralelo'] }}</span>
                                    </p>
                                </div>

                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                    {{ str_replace('_', ' ', $estado) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
                                <div class="bg-gray-50 rounded-xl p-3 border">
                                    <p class="text-xs text-gray-500">Nota Final</p>
                                    <p class="text-lg font-extrabold text-gray-900">
                                        {{ $f['nota_final'] !== null ? number_format($f['nota_final'], 2) : '—' }}
                                    </p>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-3 border">
                                    <p class="text-xs text-gray-500">Promedio Insumos</p>
                                    <p class="text-lg font-bold text-gray-800">
                                        {{ $f['promedio_insumos'] !== null ? number_format($f['promedio_insumos'], 2) : '—' }}
                                    </p>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-3 border">
                                    <p class="text-xs text-gray-500">Parcial</p>
                                    <p class="font-semibold text-gray-800">
                                        {{ $f['examen_parcial'] !== null ? number_format($f['examen_parcial'], 2) : '—' }}
                                    </p>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-3 border">
                                    <p class="text-xs text-gray-500">Final</p>
                                    <p class="font-semibold text-gray-800">
                                        {{ $f['examen_final'] !== null ? number_format($f['examen_final'], 2) : '—' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 text-xs text-gray-500">
                                Docente: <span class="font-semibold text-gray-700">{{ $f['docente'] }}</span>
                                <br>
                                Fecha: <span
                                    class="font-semibold text-gray-700">{{ $f['fecha_calificada'] ?? '—' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="p-4 border-t bg-white text-xs text-gray-500">
                    Nota: Si una materia aparece como “Pendiente”, significa que aún no se han registrado
                    calificaciones.
                </div>
            @endif
        </div>
    </div>
</div>
