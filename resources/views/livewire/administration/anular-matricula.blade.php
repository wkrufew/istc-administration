<div>
    @if($mostrar)
    {{-- ═══════════ BACKDROP ═══════════ --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data
         x-init="document.body.style.overflow='hidden'"
         x-on:keydown.escape.window="$wire.cerrar()">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrar"></div>

        {{-- ═══════════ MODAL ═══════════ --}}
        <div class="relative z-10 w-full max-w-2xl bg-white dark:bg-slate-900 rounded-2xl shadow-2xl shadow-black/50
                    border border-slate-200 dark:border-white/[0.08] overflow-hidden"
             x-data x-init="document.body.style.overflow='hidden'"
             x-on:click.stop>

            {{-- Barra superior roja --}}
            <div class="h-1 bg-gradient-to-r from-red-600 via-red-500 to-rose-600"></div>

            {{-- ─── HEADER ─── --}}
            <div class="px-6 py-4 flex items-start justify-between gap-3 border-b border-slate-100 dark:border-white/[0.06]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-rose-700 flex items-center justify-center shadow-lg shadow-red-900/30 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800 dark:text-white/90 leading-none">
                            Anular Matrícula
                        </h2>
                        @if($estudiante)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            {{ $estudiante['nombre'] }}
                            @if($estudiante['cedula'] !== '-')
                                · C.I. {{ $estudiante['cedula'] }}
                            @endif
                        </p>
                        @endif
                    </div>
                </div>
                <button wire:click="cerrar" class="text-slate-400 hover:text-slate-600 dark:hover:text-white/70 transition-colors mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- ─── PASO 1: LISTA DE MATRÍCULAS ─── --}}
            @if($paso === 1)
            <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

                {{-- Indicador de paso --}}
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500/10 text-red-500 font-semibold text-[0.65rem]">1</span>
                    <span>Selecciona la matrícula a anular</span>
                    <span class="text-slate-300 dark:text-slate-600 mx-1">›</span>
                    <span class="opacity-50">2 · Confirmar eliminación</span>
                </div>

                {{-- Warning --}}
                <div class="flex items-start gap-3 p-3.5 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
                        Esta acción <strong>elimina permanentemente</strong> la matrícula y todos sus registros asociados
                        (materias, obligaciones, pagos y archivos). Es irreversible.
                    </p>
                </div>

                {{-- Lista de matrículas --}}
                @if(count($matriculas) === 0)
                    <div class="py-10 text-center text-slate-500 text-sm">
                        Este estudiante no tiene matrículas registradas.
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($matriculas as $m)
                        <div class="group relative flex items-center gap-4 p-4 rounded-xl border
                                    border-slate-200 dark:border-white/[0.08]
                                    bg-slate-50 dark:bg-slate-800/60
                                    hover:border-red-400/50 dark:hover:border-red-500/30
                                    hover:bg-red-50/60 dark:hover:bg-red-900/[0.08]
                                    transition-all duration-150 cursor-pointer"
                             wire:click="seleccionarMatricula({{ $m['id'] }})"
                             wire:loading.class="opacity-50 pointer-events-none">

                            {{-- Tipo badge --}}
                            <div class="flex-shrink-0">
                                @php
                                    $tipoColor = match($m['tipo']) {
                                        'Nueva'      => 'from-sky-600 to-blue-700',
                                        'Renovacion' => 'from-emerald-600 to-green-700',
                                        'Arrastre'   => 'from-amber-500 to-orange-600',
                                        'Validacion' => 'from-purple-600 to-violet-700',
                                        default      => 'from-slate-500 to-slate-600',
                                    };
                                @endphp
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br {{ $tipoColor }} flex items-center justify-center shadow">
                                    <span class="text-[0.55rem] font-bold text-white uppercase">
                                        {{ substr($m['tipo'], 0, 3) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Info principal --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-semibold text-slate-700 dark:text-white/85 font-mono">
                                        {{ $m['code'] }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[0.6rem] font-medium
                                                 bg-slate-200/70 dark:bg-white/[0.07] text-slate-600 dark:text-slate-400">
                                        {{ $m['tipo'] }}
                                    </span>
                                    @if($m['tiene_pagos'])
                                    <span class="px-1.5 py-0.5 rounded text-[0.6rem] font-medium
                                                 bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400
                                                 border border-emerald-200/60 dark:border-emerald-500/20">
                                        Pagos: ${{ number_format($m['total_pagado'], 2) }}
                                    </span>
                                    @endif
                                </div>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[0.68rem] text-slate-500 dark:text-slate-400">
                                    <span>{{ $m['periodo_code'] }} {{ $m['periodo_desc'] ? '· ' . $m['periodo_desc'] : '' }}</span>
                                    <span class="text-slate-300 dark:text-slate-600">·</span>
                                    <span>{{ $m['carrera_nombre'] }}</span>
                                    <span class="text-slate-300 dark:text-slate-600">·</span>
                                    <span>{{ $m['total_materias'] }} {{ $m['total_materias'] === 1 ? 'materia' : 'materias' }}</span>
                                    @if($m['fecha_matricula'])
                                    <span class="text-slate-300 dark:text-slate-600">·</span>
                                    <span>{{ $m['fecha_matricula'] }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Flecha --}}
                            <div class="flex-shrink-0 text-slate-300 dark:text-slate-600 group-hover:text-red-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 18l6-6-6-6"/>
                                </svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Footer paso 1 --}}
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] bg-slate-50 dark:bg-black/10 flex justify-end">
                <button wire:click="cerrar"
                    class="px-4 py-2 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-400
                           border border-slate-200 dark:border-white/[0.08] hover:bg-slate-100 dark:hover:bg-white/[0.04]
                           transition-all duration-150">
                    Cancelar
                </button>
            </div>
            @endif

            {{-- ─── PASO 2: CONFIRMACIÓN ─── --}}
            @if($paso === 2 && $matriculaSeleccionada)
            <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

                {{-- Indicador de paso --}}
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <button wire:click="volverPaso1" class="inline-flex items-center justify-center w-5 h-5 rounded-full
                            bg-slate-200 dark:bg-slate-700 text-slate-500 hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                    </button>
                    <span class="opacity-50">1 · Seleccionar</span>
                    <span class="text-slate-300 dark:text-slate-600 mx-1">›</span>
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500/10 text-red-500 font-semibold text-[0.65rem]">2</span>
                    <span class="text-red-500 font-medium">Confirmar eliminación</span>
                </div>

                {{-- Alerta de peligro --}}
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-500/30">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <div class="space-y-1">
                            <p class="text-xs font-semibold text-red-700 dark:text-red-400">Eliminación permanente e irreversible</p>
                            <p class="text-xs text-red-600/80 dark:text-red-400/70 leading-relaxed">
                                Se eliminarán todos los registros: materias, obligaciones financieras, pagos y archivos.
                                Esta acción <strong>no se puede deshacer</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Resumen de la matrícula a eliminar --}}
                <div class="rounded-xl border border-slate-200 dark:border-white/[0.08] overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-white/[0.06]">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="text-xs font-mono font-semibold text-slate-700 dark:text-white/85">
                                {{ $matriculaSeleccionada['code'] }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[0.6rem] font-medium
                                             bg-slate-200/70 dark:bg-white/[0.07] text-slate-600 dark:text-slate-400">
                                    {{ $matriculaSeleccionada['tipo'] }}
                                </span>
                                <span class="text-[0.68rem] text-slate-500">
                                    {{ $matriculaSeleccionada['periodo_code'] }} · {{ $matriculaSeleccionada['carrera_nombre'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">

                        {{-- Materias --}}
                        @if(count($matriculaSeleccionada['materias']) > 0)
                        <div class="px-4 py-3">
                            <p class="text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500 mb-2">
                                Materias ({{ count($matriculaSeleccionada['materias']) }})
                            </p>
                            <div class="space-y-1">
                                @foreach($matriculaSeleccionada['materias'] as $materia)
                                <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
                                    <span>{{ $materia['nombre'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[0.6rem] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700/50 text-slate-500">
                                            {{ $materia['tipo'] }}
                                        </span>
                                        <span class="text-[0.6rem] text-slate-400">Par. {{ $materia['paralelo'] }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Obligaciones --}}
                        @if(count($matriculaSeleccionada['obligaciones']) > 0)
                        <div class="px-4 py-3">
                            <p class="text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500 mb-2">
                                Obligaciones financieras ({{ count($matriculaSeleccionada['obligaciones']) }})
                            </p>
                            <div class="space-y-1">
                                @foreach($matriculaSeleccionada['obligaciones'] as $oblig)
                                <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
                                    <span>{{ $oblig['tipo'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <span>${{ number_format($oblig['monto'], 2) }}</span>
                                        <span class="text-[0.6rem] px-1.5 py-0.5 rounded
                                            {{ $oblig['estado'] === 'Pendiente' ? 'bg-amber-100 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' }}">
                                            {{ $oblig['estado'] }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Pagos --}}
                        @if($matriculaSeleccionada['pagos_count'] > 0)
                        <div class="px-4 py-3">
                            <div class="flex items-center justify-between">
                                <p class="text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500">
                                    Pagos registrados
                                </p>
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="text-slate-500">{{ $matriculaSeleccionada['pagos_count'] }} pago(s)</span>
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                        ${{ number_format($matriculaSeleccionada['total_pagado'], 2) }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-[0.65rem] text-red-500/80 mt-1">Los archivos de comprobante serán eliminados del servidor.</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Input de confirmación --}}
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-700 dark:text-slate-300">
                        Para confirmar, escribe el código de matrícula:
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <input wire:model.live="codigoIngresado"
                               type="text"
                               placeholder="{{ $matriculaSeleccionada['code'] }}"
                               spellcheck="false"
                               autocomplete="off"
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs font-mono tracking-wide
                                      text-slate-700 dark:text-white/85
                                      bg-white dark:bg-slate-800
                                      border {{ $errors->has('codigo') ? 'border-red-400' : ($codigoIngresado === $matriculaSeleccionada['code'] && $codigoIngresado !== '' ? 'border-emerald-400' : 'border-slate-200 dark:border-white/[0.08]') }}
                                      focus:outline-none focus:border-red-400/60 focus:ring-2 focus:ring-red-400/10
                                      transition-all duration-200">
                    </div>
                    @error('codigo')
                    <p class="text-[0.65rem] text-red-500">{{ $message }}</p>
                    @enderror
                    @if($codigoIngresado !== '' && $codigoIngresado !== $matriculaSeleccionada['code'])
                    <p class="text-[0.65rem] text-red-500">
                        El código no coincide con
                        <span class="font-mono">{{ $matriculaSeleccionada['code'] }}</span>
                    </p>
                    @endif
                    @if($codigoIngresado === $matriculaSeleccionada['code'] && $codigoIngresado !== '')
                    <p class="text-[0.65rem] text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Código verificado. Puedes confirmar la eliminación.
                    </p>
                    @endif
                </div>
            </div>

            {{-- Footer paso 2 --}}
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] bg-slate-50 dark:bg-black/10 flex items-center justify-between gap-3">
                <button wire:click="volverPaso1"
                    class="px-4 py-2 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-400
                           border border-slate-200 dark:border-white/[0.08] hover:bg-slate-100 dark:hover:bg-white/[0.04]
                           transition-all duration-150 flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    Volver
                </button>

                <button wire:click="anular"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-wait"
                    @disabled($codigoIngresado !== ($matriculaSeleccionada['code'] ?? '') || $procesando)
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-xs font-semibold text-white
                           bg-gradient-to-r from-red-600 to-rose-700 border border-red-500/30
                           hover:from-red-500 hover:to-rose-600
                           disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:from-red-600 disabled:hover:to-rose-700
                           transition-all duration-200 shadow-md shadow-red-900/30">
                    <span wire:loading.remove wire:target="anular">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="anular">Anular permanentemente</span>
                    <span wire:loading wire:target="anular" class="flex items-center gap-2">
                        <svg class="animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        Anulando…
                    </span>
                </button>
            </div>
            @endif

        </div>
    </div>
    @endif
</div>
