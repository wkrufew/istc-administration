<div>
    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('administracion.administrativa.aspirantes.index') }}"
               class="p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Papelera de aspirantes</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Aspirantes eliminados — restáuralos o elimínalos definitivamente</p>
            </div>
        </div>
        <div class="w-full sm:w-72">
            <input wire:model.live.debounce.350ms="buscar" type="search"
                   placeholder="Buscar por nombre, cédula o correo…"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900
                          text-slate-800 dark:text-slate-200 placeholder-slate-400 text-sm px-3 py-2
                          focus:outline-none focus:ring-2 focus:ring-lime-500/40">
        </div>
    </div>

    {{-- ══ TABLA ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        @if($eliminados->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-slate-400 dark:text-slate-600">
                <svg class="w-12 h-12 mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
                <p class="text-sm font-medium">La papelera está vacía</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Aspirante</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Cohorte</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400 whitespace-nowrap">Estado al eliminar</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400 whitespace-nowrap">Eliminado</th>
                            <th class="text-right px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($eliminados as $asp)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ $asp->user->name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $asp->user->cedula }} · {{ $asp->user->email }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                <p>{{ $asp->cohorte->nombre ?? '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $asp->cohorte->carrera->name ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold
                                             bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">
                                    {{ \App\Models\Aspirante::ESTADOS[$asp->estado] ?? $asp->estado }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-400 whitespace-nowrap">
                                {{ $asp->deleted_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Restaurar --}}
                                    <button
                                        x-on:click="Swal.fire({
                                            title: '¿Restaurar aspirante?',
                                            text: 'Volverá a aparecer en la lista activa.',
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonText: 'Sí, restaurar',
                                            cancelButtonText: 'Cancelar',
                                            confirmButtonColor: '#16a34a',
                                            cancelButtonColor: '#64748b',
                                        }).then(r => { if (r.isConfirmed) $wire.restaurar({{ $asp->id }}) })"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                               bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400
                                               hover:bg-emerald-100 dark:hover:bg-emerald-800/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                                        </svg>
                                        Restaurar
                                    </button>
                                    {{-- Eliminar permanentemente --}}
                                    <a href="{{ route('administracion.administrativa.aspirantes.eliminar', $asp->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                              bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400
                                              hover:bg-red-100 dark:hover:bg-red-800/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                        </svg>
                                        Eliminar definitivo
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                {{ $eliminados->links() }}
            </div>
        @endif
    </div>
</div>
