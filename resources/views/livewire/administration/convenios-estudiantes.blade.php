<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-violet-500 to-purple-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-violet-600 via-purple-500 to-fuchsia-400 opacity-50"></div>

        {{-- Fila 1: título + chips + botones --}}
        <div class="px-6 py-3.5 flex items-center justify-between gap-3 flex-wrap">

            {{-- Título --}}
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-semibold text-slate-800 dark:text-white/90 leading-none">Convenios por Estudiante</h1>
                    <p class="text-[0.6rem] text-violet-400/70 tracking-widest uppercase mt-0.5">Gestión · Convenios</p>
                </div>
            </div>

            {{-- Chips de métricas rápidas --}}
            <div class="flex items-center gap-2 flex-wrap flex-1 justify-center min-w-0">
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.07]">
                    <span class="text-[0.65rem] text-slate-500 dark:text-slate-400">Matriculados</span>
                    <span class="text-xs font-bold text-slate-700 dark:text-white/80">{{ $statsResumen['matriculados'] }}</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-violet-50 dark:bg-violet-950/30 border border-violet-200/60 dark:border-violet-500/20">
                    <span class="text-[0.65rem] text-violet-600 dark:text-violet-400">Con convenio</span>
                    <span class="text-xs font-bold text-violet-700 dark:text-violet-300">{{ $statsResumen['con_convenio'] }}</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/[0.06]">
                    <span class="text-[0.65rem] text-slate-400">Sin convenio</span>
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $statsResumen['sin_convenio'] }}</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-500/20">
                    <span class="text-[0.65rem] text-emerald-600 dark:text-emerald-400">Cobertura</span>
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300">{{ $statsResumen['cobertura'] }}%</span>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <button wire:click="$set('showIndicadores', true)"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium text-violet-600 dark:text-violet-400 border border-violet-200 dark:border-violet-500/30 bg-violet-50/60 dark:bg-violet-950/20 hover:bg-violet-100 dark:hover:bg-violet-900/30 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                    </svg>
                    Indicadores
                </button>
                <a href="{{ route('administracion.administrativa.tipos-convenio.index') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Tipos de Convenio
                </a>
            </div>
        </div>

        {{-- Fila 2: filtros --}}
        <div class="px-6 py-2.5 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50/60 dark:bg-black/10 flex items-center gap-3 flex-wrap">

            {{-- Selector de período --}}
            <div class="relative flex-shrink-0">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
                </div>
                <select wire:model.live="filtroPeriodo"
                    class="pl-9 pr-8 py-1.5 rounded-full text-xs text-slate-700 dark:text-white/80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition appearance-none cursor-pointer {{ $filtroPeriodo ? 'border-violet-400/60 dark:border-violet-500/40 bg-violet-50/40 dark:bg-violet-950/10 text-violet-700 dark:text-violet-300' : '' }}">
                    <option value="">Período activo{{ $periodoEfectivo ? ' ('.$periodoEfectivo->code.')' : '' }}</option>
                    @foreach($periodos as $p)
                        <option value="{{ $p->id }}" {{ $filtroPeriodo == $p->id ? 'selected' : '' }}>
                            {{ $p->code }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Buscador --}}
            <div class="relative flex-1 min-w-[180px] max-w-sm">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="search"
                    class="w-full pl-9 pr-4 py-1.5 rounded-full text-xs text-slate-700 dark:text-white/80 placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition-all {{ $search ? 'border-violet-400/60 dark:border-violet-500/40' : '' }}"
                    placeholder="Buscar por nombre, cédula o email…">
                @if($search)
                <button wire:click="$set('search','')" class="absolute inset-y-0 right-3 flex items-center text-slate-300 hover:text-slate-500 dark:hover:text-slate-300 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                @endif
            </div>

            {{-- Quitar filtros --}}
            @if($filtroPeriodo || $search)
            <button wire:click="limpiarFiltros"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] bg-white dark:bg-slate-800 hover:border-red-300 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 transition">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Quitar filtros
            </button>
            @endif

            {{-- Indicador de período activo --}}
            <div class="ml-auto flex items-center gap-1.5 text-[0.65rem] text-slate-400">
                @if($filtroPeriodo && $periodoEfectivo)
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-400 inline-block"></span>
                    Filtrando: <span class="font-medium text-violet-600 dark:text-violet-400">{{ $periodoEfectivo->code }}</span>
                @elseif($periodoEfectivo)
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $periodoEfectivo->code }}</span> (activo)
                @else
                    <span class="text-slate-300 dark:text-slate-600">Sin período activo</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ TABLA ═════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-white/[0.06] overflow-hidden shadow-xl shadow-slate-200/80 dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04]">
        <div class="h-px bg-gradient-to-r from-transparent via-violet-500/30 to-transparent"></div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/[0.05]">
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Estudiante</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Cédula</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Carrera</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Convenio Activo</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-20">% Desc.</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-36">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($estudiantes as $est)
                        @php
                            $convenioActivo = $est->conveniosAplicados->first();
                            $matriculaAct   = $est->matriculas->first();
                            $carreraNombre  = $matriculaAct?->carrera?->name ?? '—';
                        @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-slate-700 dark:text-white/75">{{ $est->name }}</div>
                                <div class="text-xs text-slate-400">{{ $est->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ $est->cedula ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-600 dark:text-slate-300 leading-tight">{{ $carreraNombre }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($convenioActivo)
                                    <div>
                                        <span class="text-xs font-medium text-violet-600 dark:text-violet-400">{{ $convenioActivo->tipoConvenio?->nombre ?? '—' }}</span>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[0.65rem] text-slate-400">
                                                Desde {{ $convenioActivo->fecha_inicio->format('d/m/Y') }}
                                                @if ($convenioActivo->fecha_fin)
                                                    · Hasta {{ $convenioActivo->fecha_fin->format('d/m/Y') }}
                                                @endif
                                            </span>
                                            @if ($convenioActivo->tipoConvenio->tipo_alcance === 'semestral')
                                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[0.6rem] font-medium bg-orange-500/10 border border-orange-500/20 text-orange-600 dark:text-orange-400">Semestral</span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[0.6rem] font-medium bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400">Permanente</span>
                                            @endif
                                        </div>
                                        @if ($convenioActivo->motivo)
                                            <div class="text-[0.65rem] text-slate-400 mt-0.5 italic">{{ $convenioActivo->motivo }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">Sin convenio</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($convenioActivo)
                                    <span class="text-sm font-bold text-violet-600 dark:text-violet-400">{{ number_format($convenioActivo->porcentaje_aplicado, 0) }}%</span>
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($convenioActivo)
                                        <button wire:click="abrirModalEditar({{ $convenioActivo->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.72rem] font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800 hover:text-violet-500 hover:border-violet-500/30 hover:bg-violet-500/[0.06] transition-all duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            Editar
                                        </button>
                                        <button wire:click="abrirRevocar({{ $convenioActivo->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.72rem] font-medium text-red-500/70 border border-red-200/40 dark:border-red-500/10 bg-red-50/60 dark:bg-red-900/[0.06] hover:text-red-600 hover:border-red-400/50 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Revocar
                                        </button>
                                    @else
                                        <button wire:click="abrirModalAsignar({{ $est->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.72rem] font-medium text-violet-600 border border-violet-200/60 dark:border-violet-500/20 bg-violet-50/60 dark:bg-violet-900/[0.06] hover:bg-violet-50 dark:hover:bg-violet-900/10 hover:border-violet-400/50 transition-all duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                            Asignar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="text-sm">No se encontraron estudiantes matriculados{{ $periodoEfectivo ? ' en '.$periodoEfectivo->code : '' }}.</p>
                                    @if($filtroPeriodo || $search)
                                        <button wire:click="limpiarFiltros" class="text-xs text-violet-500 hover:underline">Quitar filtros</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($estudiantes->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50 dark:bg-black/10">
                {{ $estudiantes->links() }}
            </div>
        @endif

        <div class="h-0.5 bg-gradient-to-r from-violet-600 via-purple-500 to-fuchsia-400 opacity-50"></div>
    </div>

    {{-- ══ MODAL ASIGNAR / EDITAR ══════════════════════════════════════════ --}}
    @if ($showModal)
        <div x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarModal"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-white/[0.08] max-h-[90vh] overflow-y-auto"
                x-on:keydown.escape.window="$wire.cerrarModal()">

                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center justify-between sticky top-0 bg-white dark:bg-slate-900 z-10">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">
                        {{ $convenioId ? 'Editar Convenio Asignado' : 'Asignar Convenio' }}
                    </h3>
                    <button wire:click="cerrarModal" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">

                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Tipo de Convenio *</label>
                        <select wire:model.live="tipoConvenioId"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            <option value="">— Seleccionar —</option>
                            @foreach ($tiposConvenio as $tipo)
                                <option value="{{ $tipo->id }}">
                                    {{ $tipo->nombre }} ({{ number_format($tipo->porcentaje_defecto, 0) }}% · {{ $tipo->tipo_alcance === 'anual' ? 'Anual' : 'Semestral' }})
                                </option>
                            @endforeach
                        </select>
                        @error('tipoConvenioId') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">% de Descuento Aplicado *</label>
                        <div class="relative">
                            <input wire:model="porcentajeAplicado" type="number" step="0.01" min="0" max="100" placeholder="15"
                                class="w-full pr-8 px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-sm font-medium">%</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Aplica al arancel (nunca a la cuota de matrícula).</p>
                        @error('porcentajeAplicado') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Motivo del Convenio</label>
                        <input wire:model="motivo" type="text" placeholder="Ej: Acuerdo institucional con empresa XYZ"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                        @error('motivo') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Fecha Inicio *</label>
                            <input wire:model="fechaInicio" type="date"
                                class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            @error('fechaInicio') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                                Fecha Fin
                                <span class="font-normal text-slate-400">(opcional)</span>
                            </label>
                            <input wire:model="fechaFin" type="date"
                                class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            @error('fechaFin') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                            Documento de Respaldo
                            <span class="font-normal text-slate-400">(JPG, PNG, PDF · máx. 4MB)</span>
                        </label>
                        <input wire:model="documento" type="file" accept="image/*,.pdf"
                            class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 file:transition-colors cursor-pointer">
                        @error('documento') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Observación</label>
                        <textarea wire:model="observacion" rows="2" placeholder="Detalles adicionales…"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition resize-none"></textarea>
                        @error('observacion') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @error('tipoConvenioId')
                        @if (str_contains($message, 'convenio activo'))
                            <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900 rounded-lg">
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            </div>
                        @endif
                    @enderror
                </div>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] flex justify-end gap-2 sticky bottom-0 bg-white dark:bg-slate-900">
                    <button wire:click="cerrarModal"
                        class="px-4 py-2 rounded-lg text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Cancelar
                    </button>
                    <button wire:click="guardar" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-gradient-to-r from-violet-500 to-purple-500 text-white shadow hover:brightness-110 disabled:opacity-50 transition">
                        <span wire:loading.remove>{{ $convenioId ? 'Actualizar' : 'Asignar Convenio' }}</span>
                        <span wire:loading>Guardando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══ MODAL REVOCAR ══════════════════════════════════════════════════ --}}
    @if ($showRevocarModal)
        <div x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarRevocar"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-white/[0.08]"
                x-on:keydown.escape.window="$wire.cerrarRevocar()">

                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">Revocar Convenio</h3>
                </div>

                <div class="px-6 py-5 space-y-3">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Esta acción marcará el convenio como inactivo. El historial se conserva.</p>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Motivo de revocación</label>
                        <textarea wire:model="motivoRevocacion" rows="2" placeholder="Indica el motivo…"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-red-500/50 focus:ring-2 focus:ring-red-500/10 transition resize-none"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] flex justify-end gap-2">
                    <button wire:click="cerrarRevocar"
                        class="px-4 py-2 rounded-lg text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Cancelar
                    </button>
                    <button wire:click="revocar" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-red-500 text-white shadow hover:bg-red-600 disabled:opacity-50 transition">
                        <span wire:loading.remove>Revocar Convenio</span>
                        <span wire:loading>Procesando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══ MODAL INDICADORES ══════════════════════════════════════════════ --}}
    @if($showIndicadores)
    @php $ind = $this->indicadores; @endphp
    <div class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
         x-data x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-5xl mb-10 border border-slate-200 dark:border-white/[0.07]"
             @click.outside="$wire.set('showIndicadores', false)">

            {{-- Header del modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-white/[0.07]">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800 dark:text-white/90">Indicadores de Convenios</h2>
                        <p class="text-xs text-slate-400">
                            {{ $ind['es_global'] ? 'Vista global — todos los períodos' : 'Período: ' }}
                            @if(!$ind['es_global'])
                                <span class="font-medium text-violet-500">{{ $ind['periodo_code'] }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Toggle período/global --}}
                    <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                        <button wire:click="$set('modoPeriodoInd', 'actual')"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $modoPeriodoInd === 'actual' ? 'bg-white dark:bg-slate-700 text-slate-700 dark:text-white/90 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            <span class="flex items-center gap-1">
                                @if($periodoEfectivo)
                                    <span class="w-1.5 h-1.5 rounded-full {{ $filtroPeriodo ? 'bg-violet-400' : 'bg-emerald-400' }} inline-block"></span>
                                @endif
                                {{ $periodoEfectivo?->code ?? 'Período' }}
                            </span>
                        </button>
                        <button wire:click="$set('modoPeriodoInd', 'global')"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $modoPeriodoInd === 'global' ? 'bg-white dark:bg-slate-700 text-slate-700 dark:text-white/90 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253"/></svg>
                                General
                            </span>
                        </button>
                    </div>
                    <button wire:click="$set('showIndicadores', false)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-6 py-5 space-y-6">

                {{-- ── SECCIÓN 1: KPI Cards ─────────────────────────────── --}}
                <div>
                    <p class="text-[0.65rem] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] mb-3">
                        {{ $ind['es_global'] ? 'Resumen global (histórico)' : 'Resumen — '.$ind['periodo_code'] }}
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="rounded-2xl border border-slate-200 dark:border-white/[0.07] p-4 bg-slate-50 dark:bg-slate-800/40">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $ind['es_global'] ? 'Estudiantes únicos' : 'Matriculados activos' }}</p>
                            <p class="text-3xl font-black text-slate-700 dark:text-slate-200">{{ $ind['total_matriculados'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-violet-200/60 dark:border-violet-500/20 p-4 bg-violet-50/60 dark:bg-violet-950/20">
                            <p class="text-xs text-violet-600 dark:text-violet-400 mb-1">Con convenio activo</p>
                            <p class="text-3xl font-black text-violet-700 dark:text-violet-300">{{ $ind['con_convenio'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-white/[0.07] p-4 bg-slate-50 dark:bg-slate-800/40">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Sin convenio</p>
                            <p class="text-3xl font-black text-slate-600 dark:text-slate-300">{{ $ind['sin_convenio'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-200/60 dark:border-emerald-500/20 p-4 bg-emerald-50/60 dark:bg-emerald-950/20">
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 mb-1">Cobertura</p>
                            <p class="text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $ind['cobertura'] }}%</p>
                        </div>
                    </div>
                </div>

                {{-- ── SECCIÓN 2: Cobertura visual + distribución ───────── --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Cobertura visual --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-white/[0.07] p-4 bg-white dark:bg-slate-800/20">
                        <p class="text-[0.65rem] font-semibold text-slate-400 uppercase tracking-widest mb-3">Cobertura de convenios</p>
                        <div class="flex items-end gap-2 mb-3">
                            <span class="text-4xl font-black text-emerald-600 dark:text-emerald-400">{{ $ind['cobertura'] }}%</span>
                            <span class="text-xs text-slate-400 mb-1">matriculados con convenio</span>
                        </div>
                        <div class="h-3 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden mb-2">
                            <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-emerald-500 transition-all"
                                 style="width: {{ min(100, $ind['cobertura']) }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-slate-400">
                            <span>{{ $ind['con_convenio'] }} con convenio</span>
                            <span>{{ $ind['total_matriculados'] }} total</span>
                        </div>
                    </div>

                    {{-- Distribución numérica --}}
                    <div class="rounded-2xl border border-violet-200/60 dark:border-violet-500/20 p-4 bg-violet-50/40 dark:bg-violet-950/10">
                        <p class="text-[0.65rem] font-semibold text-violet-500 uppercase tracking-widest mb-3">Distribución</p>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Con convenio activo</span>
                                <span class="text-xl font-bold text-violet-700 dark:text-violet-300">{{ $ind['con_convenio'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Sin convenio</span>
                                <span class="text-xl font-bold text-slate-500 dark:text-slate-400">{{ $ind['sin_convenio'] }}</span>
                            </div>
                            <div class="pt-2 border-t border-violet-100 dark:border-violet-900/50">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                        <div class="h-full rounded-full bg-violet-500" style="width: {{ min(100, $ind['cobertura']) }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-violet-600 dark:text-violet-400 w-12 text-right">{{ $ind['cobertura'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── SECCIÓN 3: Por tipo de convenio ─────────────────── --}}
                @if(count($ind['por_tipo']) > 0)
                <div>
                    <p class="text-[0.65rem] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] mb-3">Distribución por tipo de convenio</p>
                    <div class="rounded-2xl border border-slate-200 dark:border-white/[0.07] overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-white/[0.06]">
                                        <th class="text-left px-4 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">Tipo de Convenio</th>
                                        <th class="text-center px-3 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">Alcance</th>
                                        <th class="text-center px-3 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">% Desc.</th>
                                        <th class="text-center px-3 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">Estudiantes</th>
                                        <th class="px-4 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">Distribución</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                                    @foreach($ind['por_tipo'] as $tipo)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-white/[0.02]">
                                        <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-200">{{ $tipo['nombre'] }}</td>
                                        <td class="px-3 py-3 text-center">
                                            @if($tipo['alcance'] === 'semestral')
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[0.65rem] font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">Semestral</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[0.65rem] font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Permanente</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center font-bold text-violet-600 dark:text-violet-400">{{ number_format($tipo['porcentaje'], 0) }}%</td>
                                        <td class="px-3 py-3 text-center font-bold text-slate-700 dark:text-slate-200">{{ $tipo['cantidad'] }}</td>
                                        <td class="px-4 py-3 w-44">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                                    <div class="h-full rounded-full bg-violet-500" style="width: {{ $tipo['pct_dist'] }}%"></div>
                                                </div>
                                                <span class="text-xs text-slate-400 w-9 text-right">{{ $tipo['pct_dist'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ── SECCIÓN 4: Por carrera ───────────────────────────── --}}
                @if(count($ind['por_carrera']) > 0)
                <div>
                    <p class="text-[0.65rem] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] mb-3">Distribución por carrera</p>
                    <div class="rounded-2xl border border-slate-200 dark:border-white/[0.07] overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-white/[0.06]">
                                        <th class="text-left px-4 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">Carrera</th>
                                        <th class="text-center px-3 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">Matriculados</th>
                                        <th class="text-center px-3 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-violet-500">Con Convenio</th>
                                        <th class="text-center px-3 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">Sin Convenio</th>
                                        <th class="px-4 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-emerald-500 w-36">Cobertura</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                                    @foreach($ind['por_carrera'] as $fila)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-white/[0.02]">
                                        <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-200 max-w-[200px]">
                                            <span class="block truncate" title="{{ $fila['nombre'] }}">{{ $fila['nombre'] }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-center font-bold text-slate-700 dark:text-slate-200">{{ $fila['matriculados'] }}</td>
                                        <td class="px-3 py-3 text-center font-bold text-violet-600 dark:text-violet-400">{{ $fila['con_convenio'] ?: '—' }}</td>
                                        <td class="px-3 py-3 text-center text-slate-400">{{ $fila['sin_convenio'] ?: '—' }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $fila['cobertura'] >= 50 ? 'bg-emerald-500' : ($fila['cobertura'] >= 20 ? 'bg-violet-400' : 'bg-slate-300 dark:bg-slate-600') }}"
                                                         style="width: {{ min(100, $fila['cobertura']) }}%"></div>
                                                </div>
                                                <span class="text-xs text-slate-400 w-10 text-right">{{ $fila['cobertura'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ── SECCIÓN 5: Histórico por período ────────────────── --}}
                @if(count($ind['historico']) > 0)
                <div>
                    <p class="text-[0.65rem] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] mb-3">
                        Histórico por período
                        <span class="normal-case tracking-normal font-normal text-slate-300 dark:text-slate-600">(últimos 6)</span>
                    </p>
                    <div class="rounded-2xl border border-slate-200 dark:border-white/[0.07] overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-white/[0.06]">
                                        <th class="text-left px-4 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">Período</th>
                                        <th class="text-center px-3 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-slate-400">Matriculados</th>
                                        <th class="text-center px-3 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-violet-500">Con Convenio</th>
                                        <th class="px-4 py-2.5 text-[0.65rem] font-medium tracking-wider uppercase text-emerald-500 w-36">Cobertura</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                                    @foreach($ind['historico'] as $fila)
                                    @php
                                        $esActual = $periodoEfectivo && $fila['periodo'] === $periodoEfectivo->code;
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-white/[0.02] {{ $esActual ? 'bg-violet-50/30 dark:bg-violet-950/10' : '' }}">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $fila['periodo'] }}</span>
                                                @if($esActual)
                                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[0.6rem] font-semibold bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">Actual</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center font-bold text-slate-700 dark:text-slate-200">{{ $fila['matriculados'] }}</td>
                                        <td class="px-3 py-3 text-center font-bold text-violet-600 dark:text-violet-400">{{ $fila['con_convenio'] ?: '—' }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $fila['cobertura'] >= 50 ? 'bg-emerald-500' : ($fila['cobertura'] >= 20 ? 'bg-violet-400' : 'bg-slate-300 dark:bg-slate-600') }}"
                                                         style="width: {{ min(100, $fila['cobertura']) }}%"></div>
                                                </div>
                                                <span class="text-xs text-slate-400 w-10 text-right">{{ $fila['cobertura'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

            </div>{{-- /body --}}

            <div class="flex justify-end px-6 py-4 border-t border-slate-200 dark:border-white/[0.07]">
                <button wire:click="$set('showIndicadores', false)"
                        class="px-5 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
