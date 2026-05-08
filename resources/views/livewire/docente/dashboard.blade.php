<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- ── Bienvenida + Selector de período ──────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5
                bg-white rounded-2xl shadow-sm border border-slate-100 px-6 py-5">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600
                        flex items-center justify-center shadow-md shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Portal Docente</p>
                <h1 class="text-xl font-bold text-slate-800 leading-tight">
                    Bienvenido(a), {{ auth()->user()->name }}
                </h1>
            </div>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Período Académico</label>
            <div class="relative">
                <select wire:model.live="periodo_id"
                    class="appearance-none w-full sm:w-72 bg-white border-2 border-slate-200 rounded-xl px-4 py-2.5 pr-10
                           text-slate-700 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100
                           transition-all">
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id }}">
                            {{ $periodo->code }} — {{ $periodo->description }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stat cards ─────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        {{-- Materias asignadas --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600
                    rounded-2xl shadow-lg p-6 text-white">
            <div class="relative z-10">
                <p class="text-xs font-semibold text-emerald-100 uppercase tracking-wider mb-3">
                    Materias Asignadas
                </p>
                <p class="text-5xl font-extrabold leading-none">{{ $totalMaterias }}</p>
                <p class="text-xs text-emerald-200 mt-2">en el período seleccionado</p>
            </div>
            <svg class="absolute -right-2 -bottom-2 w-24 h-24 text-white/10" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 14l9-5-9-5-9 5 9 5zm0 0v7"/>
            </svg>
        </div>

        {{-- Estudiantes matriculados --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-teal-500 to-cyan-600
                    rounded-2xl shadow-lg p-6 text-white">
            <div class="relative z-10">
                <p class="text-xs font-semibold text-teal-100 uppercase tracking-wider mb-3">
                    Estudiantes Matriculados
                </p>
                <p class="text-5xl font-extrabold leading-none">{{ $totalEstudiantes }}</p>
                <p class="text-xs text-teal-200 mt-2">estudiantes únicos activos</p>
            </div>
            <svg class="absolute -right-2 -bottom-2 w-24 h-24 text-white/10" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
            </svg>
        </div>

        {{-- Período activo --}}
        <div class="relative overflow-hidden bg-white border-2 border-emerald-100 rounded-2xl shadow-sm p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">
                        Período Activo
                    </p>
                    <p class="text-2xl font-extrabold text-slate-800 leading-tight truncate">
                        {{ optional($periodos->firstWhere('id', $periodo_id))->code ?? '—' }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1 truncate">
                        {{ optional($periodos->firstWhere('id', $periodo_id))->description ?? '' }}
                    </p>
                </div>
                <div class="h-11 w-11 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabla detalle por materia ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2.5">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h2 class="text-base font-semibold text-slate-800">Resumen por Materia</h2>
            <span class="ml-auto inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                         bg-emerald-100 text-emerald-700">
                {{ count($detalleMaterias) }} asignaciones
            </span>
        </div>

        @if (count($detalleMaterias) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                                Código
                            </th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                                Materia
                            </th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                                Paralelo
                            </th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider">
                                Estudiantes
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($detalleMaterias as $item)
                            <tr class="hover:bg-emerald-50/40 transition-colors">
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100
                                                 text-slate-700 text-xs font-mono font-semibold">
                                        {{ $item['codigo'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-slate-800 font-medium">
                                    {{ $item['materia'] }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-teal-50
                                                 text-teal-700 text-xs font-semibold border border-teal-100">
                                        {{ $item['paralelo'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                 rounded-xl bg-emerald-100 text-emerald-800 text-sm font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                                        </svg>
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
                <div class="mx-auto h-14 w-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-slate-600 font-semibold">Sin materias asignadas</p>
                <p class="text-slate-400 text-sm mt-1">No hay asignaciones registradas en este período.</p>
            </div>
        @endif
    </div>

</div>
