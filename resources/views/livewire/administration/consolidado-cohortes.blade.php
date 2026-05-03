<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- ── HEADER ────────────────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700/60 rounded-2xl shadow-sm p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Consolidado de Cohortes
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Resumen académico y financiero acumulado por ciclo lectivo.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap">
                        Cohorte
                    </label>
                    <select wire:model.live="periodoId"
                        class="rounded-xl border border-gray-300 dark:border-slate-600
                               bg-white dark:bg-slate-800
                               text-gray-900 dark:text-gray-100
                               text-sm font-semibold min-w-52 py-2.5 px-3
                               focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500
                               transition shadow-sm">
                        <option value="">Seleccionar cohorte…</option>
                        @foreach ($this->periodos as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} — {{ $p->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── SIN COHORTE SELECCIONADO ───────────────────────────────────────── --}}
        @if (!$periodoId)
            <div class="bg-white dark:bg-slate-900 border border-dashed border-gray-200 dark:border-slate-700 rounded-2xl py-20 text-center">
                <div class="mx-auto mb-4 h-16 w-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-semibold text-base">
                    Selecciona un cohorte para ver el consolidado
                </p>
                <p class="text-gray-400 dark:text-gray-600 text-sm mt-1">
                    Verás el resumen de todas las carreras vinculadas al ciclo
                </p>
            </div>

        @else
            {{-- ── LOADING ───────────────────────────────────────────────────── --}}
            <div wire:loading wire:target="periodoId" class="flex items-center justify-center py-12">
                <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
                    <svg class="animate-spin w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <span class="text-sm font-semibold">Generando consolidado…</span>
                </div>
            </div>

            @php
                $resumen = $this->resumenCohorte;
                $totales = $this->totales;
            @endphp

            @if (empty($resumen))
                <div class="bg-white dark:bg-slate-900 border border-dashed border-gray-200 dark:border-slate-700 rounded-2xl py-16 text-center">
                    <p class="text-gray-400 dark:text-gray-500 font-semibold">Este cohorte no tiene carreras vinculadas.</p>
                </div>
            @else
                <div wire:loading.remove wire:target="periodoId" class="space-y-6">

                    {{-- ── CARDS DE TOTALES ──────────────────────────────────── --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                        {{-- Carreras --}}
                        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700/60 rounded-2xl shadow-sm p-5">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Carreras</p>
                            <p class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mt-2">{{ $totales['carreras'] }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">vinculadas al cohorte</p>
                        </div>

                        {{-- Estudiantes --}}
                        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700/60 rounded-2xl shadow-sm p-5">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Estudiantes</p>
                            <p class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mt-2">{{ $totales['estudiantes'] }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">matriculados habilitados</p>
                        </div>

                        {{-- Recaudado --}}
                        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700/60 rounded-2xl shadow-sm p-5">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Recaudado</p>
                            <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2">
                                ${{ number_format($totales['recaudado'], 2) }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                de ${{ number_format($totales['monto_total'], 2) }} total
                            </p>
                        </div>

                        {{-- Mora --}}
                        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700/60 rounded-2xl shadow-sm p-5">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Mora total</p>
                            <p class="text-3xl font-extrabold text-red-500 dark:text-red-400 mt-2">
                                ${{ number_format($totales['mora'], 2) }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                ${{ number_format($totales['pendiente'], 2) }} pendiente
                            </p>
                        </div>
                    </div>

                    {{-- ── TABLA POR CARRERA ─────────────────────────────────── --}}
                    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700/60 rounded-2xl shadow-sm overflow-hidden">

                        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700/60">
                            <p class="font-bold text-gray-800 dark:text-gray-100">Desglose por carrera</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-slate-800/60 border-b border-gray-200 dark:border-slate-700/60
                                               text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-bold">
                                        <th class="px-5 py-3.5 text-left">Carrera</th>
                                        <th class="px-5 py-3.5 text-center">Estado</th>
                                        <th class="px-5 py-3.5 text-right">Estudiantes</th>
                                        <th class="px-5 py-3.5 text-right">Aprobados</th>
                                        <th class="px-5 py-3.5 text-right">Reprobados</th>
                                        <th class="px-5 py-3.5 text-right">Recaudado</th>
                                        <th class="px-5 py-3.5 text-right">Pendiente</th>
                                        <th class="px-5 py-3.5 text-right">Mora</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100 dark:divide-slate-700/40">
                                    @foreach ($resumen as $fila)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40 transition">
                                            <td class="px-5 py-3.5">
                                                <p class="font-semibold text-gray-800 dark:text-gray-100">
                                                    {{ $fila['carrera']->name }}
                                                </p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                                    {{ $fila['carrera']->code }}
                                                </p>
                                            </td>

                                            <td class="px-5 py-3.5 text-center">
                                                @if ($fila['es_activa'])
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                                                 bg-green-100 dark:bg-green-950/60 text-green-700 dark:text-green-400
                                                                 border border-green-200 dark:border-green-800/50">
                                                        ● Activo
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                                                 bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-gray-400
                                                                 border border-gray-200 dark:border-slate-600">
                                                        ● Cerrado
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-5 py-3.5 text-right font-semibold text-gray-700 dark:text-gray-200">
                                                {{ $fila['estudiantes'] }}
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                                {{ $fila['aprobados'] }}
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-semibold text-red-500 dark:text-red-400">
                                                {{ $fila['reprobados'] }}
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                                ${{ number_format($fila['recaudado'], 2) }}
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-semibold text-amber-500 dark:text-amber-400">
                                                ${{ number_format($fila['pendiente'], 2) }}
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-semibold text-red-500 dark:text-red-400">
                                                ${{ number_format($fila['mora'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                {{-- Fila de totales --}}
                                <tfoot>
                                    <tr class="bg-slate-800 dark:bg-slate-950 text-white text-sm font-bold border-t border-slate-700">
                                        <td class="px-5 py-3.5 text-gray-200" colspan="2">TOTALES</td>
                                        <td class="px-5 py-3.5 text-right text-gray-200">{{ $totales['estudiantes'] }}</td>
                                        <td class="px-5 py-3.5 text-right text-emerald-300">{{ $totales['aprobados'] }}</td>
                                        <td class="px-5 py-3.5 text-right text-red-300">{{ $totales['reprobados'] }}</td>
                                        <td class="px-5 py-3.5 text-right text-emerald-300">${{ number_format($totales['recaudado'], 2) }}</td>
                                        <td class="px-5 py-3.5 text-right text-amber-300">${{ number_format($totales['pendiente'], 2) }}</td>
                                        <td class="px-5 py-3.5 text-right text-red-300">${{ number_format($totales['mora'], 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>{{-- end wire:loading.remove --}}
            @endif
        @endif

    </div>
</div>
