<div>
    @if ($abierto)
        <div class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto">
            <div class="absolute inset-0" wire:click="cerrar"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-5xl mb-10 border border-slate-200 dark:border-white/[0.08]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-white/[0.06]">
                    <div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">Historial de beneficios</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $estudiante?->name }} @if ($estudiante?->cedula) · {{ $estudiante->cedula }} @endif
                        </p>
                    </div>
                    <button wire:click="cerrar" type="button"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5">
                    @if ($registros->isEmpty())
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-8">Este estudiante no ha tenido becas ni convenios.</p>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-white/[0.07]">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-white/[0.06] text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">
                                        <th class="text-left px-4 py-2.5">Beneficio</th>
                                        <th class="text-center px-3 py-2.5">%</th>
                                        <th class="text-left px-3 py-2.5">Vigencia</th>
                                        <th class="text-left px-3 py-2.5">Períodos usados</th>
                                        <th class="text-left px-4 py-2.5">Estado / motivo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                                    @foreach ($registros as $fila)
                                        <tr class="align-top">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="px-1.5 py-0.5 rounded text-[0.6rem] font-semibold
                                                        {{ $fila['clase'] === 'Beca' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300' }}">
                                                        {{ $fila['clase'] }}
                                                    </span>
                                                    @if ($fila['alcance'])
                                                        <span class="text-[0.6rem] text-slate-400">{{ $fila['alcance'] }}</span>
                                                    @endif
                                                </div>
                                                <p class="mt-1 font-medium text-slate-700 dark:text-slate-200">{{ $fila['nombre'] }}</p>
                                            </td>
                                            <td class="px-3 py-3 text-center font-bold text-slate-700 dark:text-slate-200 tabular-nums">{{ number_format($fila['porcentaje'], 0) }}%</td>
                                            <td class="px-3 py-3 text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap tabular-nums">
                                                {{ $fila['desde']?->format('d/m/Y') ?? '—' }}
                                                <span class="text-slate-400">→</span>
                                                {{ $fila['activo'] ? 'vigente' : ($fila['hasta']?->format('d/m/Y') ?? 'sin fecha') }}
                                            </td>
                                            <td class="px-3 py-3 text-xs text-slate-600 dark:text-slate-300">
                                                {{ count($fila['periodos']) ? implode(', ', $fila['periodos']) : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-xs">
                                                @if ($fila['activo'])
                                                    <span class="inline-flex px-2 py-0.5 rounded-full font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Activo</span>
                                                @else
                                                    <span class="inline-flex px-2 py-0.5 rounded-full font-semibold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $fila['motivo'] }}</span>
                                                    @if ($fila['detalle'])
                                                        <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $fila['detalle'] }}</p>
                                                    @endif
                                                    @if ($fila['por'])
                                                        <p class="mt-0.5 text-slate-400">Por: {{ $fila['por'] }}</p>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
