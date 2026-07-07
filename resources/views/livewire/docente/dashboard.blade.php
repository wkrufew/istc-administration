<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ── Banner bienvenida ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-6 py-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mb-1">Portal Docente</p>
                <h1 class="text-2xl font-extrabold text-slate-800 leading-tight">
                    Bienvenido(a), {{ auth()->user()->name }}
                </h1>
                <p class="text-sm text-slate-400 mt-1">
                    {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
            </div>
            <div class="flex flex-col gap-1.5 shrink-0">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    Período Académico
                </label>
                <select wire:model.live="periodo_id"
                    class="appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-2.5 text-sm
                           text-slate-700 font-semibold focus:border-emerald-500 focus:outline-none
                           min-w-64 cursor-pointer">
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id }}">
                            {{ $periodo->code }} — {{ $periodo->description }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Stat cards ─────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 border-l-4 border-l-emerald-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Materias Asignadas</p>
            <p class="text-5xl font-extrabold text-emerald-600 mt-3 leading-none">{{ $totalMaterias }}</p>
            <p class="text-xs text-slate-400 mt-2">en el período seleccionado</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 border-l-4 border-l-teal-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estudiantes Matriculados</p>
            <p class="text-5xl font-extrabold text-teal-600 mt-3 leading-none">{{ $totalEstudiantes }}</p>
            <p class="text-xs text-slate-400 mt-2">estudiantes únicos activos</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 border-l-4 border-l-cyan-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Período Activo</p>
            <p class="text-2xl font-extrabold text-slate-800 mt-3 leading-tight">
                {{ optional($periodos->firstWhere('id', $periodo_id))->code ?? '—' }}
            </p>
            <p class="text-xs text-slate-400 mt-1 truncate">
                {{ optional($periodos->firstWhere('id', $periodo_id))->description ?? '' }}
            </p>
        </div>

    </div>

    {{-- ── Tabla detalle por materia ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Resumen por Materia</h2>
                <p class="text-xs text-slate-400 mt-0.5">Asignaciones en el período seleccionado</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                         bg-emerald-100 text-emerald-700">
                {{ count($detalleMaterias) }} asignaciones
            </span>
        </div>

        @if (count($detalleMaterias) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Código
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Materia
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Paralelo
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Estudiantes
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($detalleMaterias as $item)
                            <tr class="hover:bg-emerald-50/40 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100
                                                 text-slate-700 text-xs font-mono font-bold">
                                        {{ $item['codigo'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-800 font-semibold">
                                    {{ $item['materia'] }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-teal-50
                                                 text-teal-700 text-xs font-bold border border-teal-100">
                                        {{ $item['paralelo'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-10 h-7 px-2
                                                 rounded-lg bg-emerald-100 text-emerald-800 text-sm font-extrabold">
                                        {{ $item['estudiantes'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center">
                <p class="text-slate-500 font-semibold">Sin materias asignadas</p>
                <p class="text-slate-400 text-sm mt-1">No hay asignaciones en este período.</p>
            </div>
        @endif

    </div>

    {{-- ── Recursos Institucionales ────────────────────────────────────────────── --}}
    @if ($recursos->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Recursos Institucionales</h2>
                <p class="text-xs text-slate-400 mt-0.5">Documentos oficiales disponibles para descarga</p>
            </div>

            <div class="divide-y divide-slate-50">
                @foreach ($recursos as $recurso)
                    @php
                        $badges = [
                            'silabo'  => ['label' => 'SÍL', 'bg' => 'bg-blue-100',    'text' => 'text-blue-700'],
                            'rubrica' => ['label' => 'RÚB', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
                            'acta'    => ['label' => 'ACT', 'bg' => 'bg-violet-100',  'text' => 'text-violet-700'],
                            'guia'    => ['label' => 'GUÍ', 'bg' => 'bg-amber-100',   'text' => 'text-amber-700'],
                            'otro'    => ['label' => 'DOC', 'bg' => 'bg-slate-100',   'text' => 'text-slate-700'],
                        ];
                        $badge = $badges[$recurso->tipo] ?? $badges['otro'];
                    @endphp
                    <a href="{{ asset('storage/' . $recurso->path) }}" target="_blank"
                       class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-colors group">

                        <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl shrink-0
                                     text-xs font-extrabold {{ $badge['bg'] }} {{ $badge['text'] }}">
                            {{ $badge['label'] }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700 transition-colors">
                                {{ $recurso->nombre }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Actualizado {{ $recurso->updated_at->isoFormat('D MMM YYYY') }}
                            </p>
                        </div>

                        <span class="text-xs font-bold text-emerald-600 whitespace-nowrap
                                     group-hover:text-emerald-700 transition-colors shrink-0">
                            Descargar &rarr;
                        </span>

                    </a>
                @endforeach
            </div>

        </div>
    @endif

</div>
