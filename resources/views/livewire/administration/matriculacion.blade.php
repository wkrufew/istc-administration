<div class="max-w-7xl mx-auto px-4 pb-2 space-y-4"
    x-data="{ _scroll: 0 }"
    x-init="
        document.addEventListener('livewire:request', () => {
            if (!document.body.style.position) {
                this._scroll = window.scrollY;
            }
        });
    "
    x-on:modal-opened.window="
        document.body.style.position = 'fixed';
        document.body.style.top = `-${_scroll}px`;
        document.body.style.width = '100%';
    "
    x-on:modal-closed.window="
        const sy = _scroll;
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        window.scrollTo(0, sy);
    ">

    {{-- ═══════════════════════════════════════
         BLOQUE 1 — HEADER FUSIONADO
    ═══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">

        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
        <div
            class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50">
        </div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">

            {{-- Ícono + Título --}}
            <div class="flex items-center gap-3 flex-shrink-0">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Gestión de Matrículas</h1>
                    <p class="text-xs text-lime-400/70 tracking-widest uppercase mt-1">Administración · Estudiantes</p>
                </div>
            </div>

            {{-- Buscador --}}
            <div class="relative flex-1 min-w-[180px] max-w-xs">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </div>
                <input wire:model.live="search" type="text" placeholder="Nombre, email, cédula…"
                    class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-slate-700 dark:text-white/80 placeholder-slate-400 dark:placeholder-slate-500
                           bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08]
                           focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10
                           transition-all duration-200">
            </div>

        </div>
    </div>


    {{-- ═══════════════════════════════════════
         BLOQUE 2 — FILTROS
    ═══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/[0.06] rounded-xl overflow-hidden ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04]">

        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/20 to-transparent"></div>

        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Período --}}
            <div>
                <label class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 mb-1.5">
                    Período
                </label>
                <select wire:model.live="selectedPeriodo"
                    class="w-full px-4 py-2 rounded-xl text-xs text-slate-700 dark:text-white/80
                           bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08]
                           focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10
                           transition-all duration-200">
                    <option value="">Todos los períodos</option>
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id }}">{{ $periodo->code }} - {{ $periodo->description }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Carrera --}}
            <div>
                <label class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 mb-1.5">
                    Carrera
                </label>
                <select wire:model.live="selectedCarrera"
                    class="w-full px-4 py-2 rounded-xl text-xs text-slate-700 dark:text-white/80
                           bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08]
                           focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10
                           transition-all duration-200">
                    <option value="">Todas las carreras</option>
                    @foreach ($carreras as $carrera)
                        <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>


    {{-- ═══════════════════════════════════════
         BLOQUE 3 — TABLA DE ESTUDIANTES
    ═══════════════════════════════════════ --}}
    <div
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-white/[0.06] overflow-hidden shadow-xl shadow-slate-200/80 dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04]">

        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/[0.05]">
                        <th
                            class="px-5 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Estudiante</th>
                        <th
                            class="px-5 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Email / C.I.</th>
                        <th
                            class="px-5 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Matrícula Actual</th>
                        <th
                            class="px-5 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                            Estado</th>
                        <th
                            class="px-5 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-24">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse($estudiantes as $student)
                        @php $matriculaActual = $student->matriculas->first(fn($m) => $m->tipo !== 'Validacion'); @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">

                            {{-- Estudiante --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-green-700/70 to-sky-700/70 border border-white/[0.08] flex items-center justify-center flex-shrink-0">
                                        <span class="text-[0.65rem] font-semibold text-white/80 uppercase">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-white/80">{{ $student->name }}</p>
                                        @if ($student->aspirante?->estado === 'aprobado')
                                            @php $esValidacion = $student->aspirante->tipo_proceso === 'validacion_conocimientos'; @endphp
                                            <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[0.6rem] font-semibold border
                                                {{ $esValidacion
                                                    ? 'bg-violet-50 dark:bg-violet-900/20 border-violet-200 dark:border-violet-500/30 text-violet-700 dark:text-violet-300'
                                                    : 'bg-sky-50 dark:bg-sky-900/20 border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300' }}">
                                                Aspirante · {{ $esValidacion ? 'Validación' : 'Regular' }}
                                            </span>
                                        @else
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $student->matricula_numero }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Email + C.I. --}}
                            <td class="px-5 py-3.5">
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $student->email }}</p>
                                @if($student->cedula)
                                <p class="text-[0.65rem] text-slate-400 dark:text-slate-500 mt-0.5 font-mono">C.I.: {{ $student->cedula }}</p>
                                @endif
                            </td>

                            {{-- Matrícula --}}
                            <td class="px-5 py-3.5 text-center">
                                @if ($matriculaActual)
                                    <p class="text-xs font-semibold text-slate-700 dark:text-white/75">{{ $matriculaActual->code }}</p>
                                    <p class="text-[0.65rem] text-slate-400 dark:text-slate-500 mt-0.5">
                                        {{ $matriculaActual->carrera->name }}</p>
                                @elseif ($selectedPeriodo && $student->ultimaMatricula)
                                    <p class="text-[0.65rem] text-slate-400 dark:text-slate-500 italic">Sin matrícula en este período</p>
                                    <p class="text-[0.6rem] text-slate-500 dark:text-slate-600 mt-0.5">
                                        Último: {{ $student->ultimaMatricula->periodo?->code ?? '—' }}
                                    </p>
                                @else
                                    <span class="text-slate-600 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="px-5 py-3.5 text-center">
                                @if ($matriculaActual)
                                    @switch($matriculaActual->estado)
                                        @case('Habilitada')
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-lime-500/10 border border-lime-500/20 text-lime-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-lime-400 animate-pulse"></span>Pagada
                                            </span>
                                        @break

                                        @case('Pendiente_Pago')
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-amber-500/10 border border-amber-500/20 text-amber-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Pendiente Pago
                                            </span>
                                        @break

                                        @case('Borrador')
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-red-500/10 border border-red-500/20 text-red-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>Borrador
                                            </span>
                                        @break

                                        @case('Cancelada')
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-slate-500/20 border border-slate-500/20 text-slate-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Cancelada
                                            </span>
                                        @break

                                        @case('Retirada')
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-orange-500/10 border border-orange-500/20 text-orange-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>Retirada
                                            </span>
                                        @break

                                        @default
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-sky-500/10 border border-sky-500/20 text-sky-400">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>{{ $matriculaActual->estado }}
                                            </span>
                                    @endswitch
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-purple-500/10 border border-purple-500/20 text-purple-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>Sin matrícula
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-5 py-3.5 text-center">
                                @if ($matriculaActual)
                                    <div class="flex flex-col items-center gap-1">
                                        <button type="button" wire:click="editarMatricula({{ $matriculaActual->id }})"
                                            title="Editar matrícula"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                                   text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800
                                                   hover:text-sky-400 hover:border-sky-500/30 hover:bg-sky-500/[0.06]
                                                   transition-all duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                            Editar
                                        </button>
                                        @role('Administrador')
                                        <button type="button" wire:click="abrirAnulacion({{ $student->id }})"
                                            title="Anular matrícula"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[0.65rem] font-medium
                                                   text-red-500/80 border border-red-200/40 dark:border-red-500/10 bg-red-50/60 dark:bg-red-900/[0.06]
                                                   hover:text-red-600 hover:border-red-400/50 hover:bg-red-50 dark:hover:bg-red-900/10
                                                   transition-all duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6"/><path d="M14 11v6"/>
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                            </svg>
                                            Anular
                                        </button>
                                        @endrole
                                        @can('cancelar_matriculas')
                                        @if (!in_array($matriculaActual->estado, ['Cancelada', 'Retirada']))
                                        <button type="button" wire:click="abrirRetiro({{ $matriculaActual->id }})"
                                            title="Registrar retiro del estudiante"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[0.65rem] font-medium
                                                   text-orange-500/80 border border-orange-200/40 dark:border-orange-500/10 bg-orange-50/60 dark:bg-orange-900/[0.06]
                                                   hover:text-orange-600 hover:border-orange-400/50 hover:bg-orange-50 dark:hover:bg-orange-900/10
                                                   transition-all duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                            </svg>
                                            Retirar
                                        </button>
                                        @endif
                                        @endcan
                                    </div>
                                @else
                                    <div class="flex flex-col items-center gap-1">
                                        <button type="button" wire:click="iniciarMatricula({{ $student->id }})"
                                            title="Nueva matrícula"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                                   text-white
                                                   bg-gradient-to-r from-green-800/70 via-sky-800/60 to-purple-900/55
                                                   border border-lime-500/25
                                                   hover:from-green-700/80 hover:via-sky-700/70 hover:to-purple-800/65
                                                   hover:-translate-y-0.5 hover:shadow-md hover:shadow-green-900/30
                                                   active:translate-y-0 transition-all duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 4v16m8-8H4" />
                                            </svg>
                                            Nueva
                                        </button>
                                        {{-- Contexto: si tiene matrícula en otro periodo, mostrar enlace para editar --}}
                                        @if ($selectedPeriodo && $student->ultimaMatricula)
                                            <button type="button" wire:click="editarMatricula({{ $student->ultimaMatricula->id }})"
                                                title="Ver matrícula anterior"
                                                class="text-[0.6rem] text-slate-500 dark:text-slate-500 hover:text-sky-400 transition-colors underline underline-offset-2">
                                                Ver período anterior
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>

                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-slate-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8" />
                                            <path d="m21 21-4.35-4.35" />
                                        </svg>
                                        <p class="text-sm">No se encontraron estudiantes.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50 dark:bg-black/10">
                {{ $estudiantes->links() }}
            </div>

            <div
                class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50">
            </div>

        </div>


        {{-- ═══════════════════════════════════════
         MODAL DE MATRÍCULA
    ═══════════════════════════════════════ --}}
        @if ($showModal)
            <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4" role="dialog" aria-modal="true">

                {{-- Overlay --}}
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

                {{-- Panel --}}
                <div class="relative w-full max-w-5xl bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-white/[0.08] shadow-2xl shadow-black/60 overflow-hidden flex flex-col max-h-[90vh]"
                    x-data="{
                        paralelosLocales: {},
                        init() {
                            this.paralelosLocales = Object.assign({}, $wire.paralelosSeleccionados ?? {});
                        },
                        async avanzarPaso() {
                            if ($wire.paso == 3) {
                                const p = {};
                                for (const [k, v] of Object.entries(this.paralelosLocales)) p[k] = v;
                                await $wire.siguientePasoConParalelos(p);
                            } else {
                                await $wire.siguientePaso();
                            }
                        }
                    }">

                    {{-- Shimmer top --}}
                    <div class="h-px bg-gradient-to-r from-transparent via-lime-500/40 to-transparent flex-shrink-0"></div>

                    {{-- Modal Header --}}
                    <div
                        class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center justify-between gap-4 flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-700/60 to-sky-700/60 border border-white/[0.08] flex items-center justify-center">
                                <span class="text-xs font-semibold text-white/80 uppercase">
                                    {{ strtoupper(substr($estudiante->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-800 dark:text-white/90 leading-none">
                                    {{ $matriculaId ? 'Editar' : 'Nueva' }} Matrícula
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $estudiante->name }} · #{{ $estudiante->id }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="cerrarModal"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Progress Steps --}}
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.05] flex-shrink-0">
                        <div class="flex items-center gap-2">
                            @php
                                $steps = [
                                    ['num' => 1, 'label' => 'Información'],
                                    ['num' => 2, 'label' => 'Materias'],
                                    ['num' => 3, 'label' => 'Paralelos'],
                                    ['num' => 4, 'label' => 'Resumen'],
                                ];
                            @endphp
                            @foreach ($steps as $i => $step)
                                <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <div
                                            class="w-6 h-6 rounded-full flex items-center justify-center text-[0.65rem] font-semibold flex-shrink-0
                                        {{ $paso >= $step['num']
                                            ? 'bg-lime-500/20 border border-lime-500/40 text-lime-400'
                                            : 'bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.06] text-slate-400 dark:text-slate-500' }}">
                                            @if ($paso > $step['num'])
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                {{ $step['num'] }}
                                            @endif
                                        </div>
                                        <span
                                            class="text-[0.65rem] font-medium hidden sm:block
                                        {{ $paso >= $step['num'] ? 'text-lime-400/80' : 'text-slate-400 dark:text-slate-500' }}"
>
                                            {{ $step['label'] }}
                                        </span>
                                    </div>
                                    @if ($i < count($steps) - 1)
                                        <div
                                            class="flex-1 mx-2 h-px {{ $paso > $step['num'] ? 'bg-lime-500/30' : 'bg-slate-200 dark:bg-white/[0.05]' }}">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <div
                        class="flex-1 overflow-y-auto overscroll-contain px-6 pb-5
                    [&::-webkit-scrollbar]:w-1.5
                    [&::-webkit-scrollbar-track]:bg-slate-100 dark:[&::-webkit-scrollbar-track]:bg-slate-800/50
                    [&::-webkit-scrollbar-thumb]:bg-slate-300 dark:[&::-webkit-scrollbar-thumb]:bg-slate-600
                    [&::-webkit-scrollbar-thumb]:rounded-full">

                        {{-- ── PASO 1: Información ── --}}
                        @if ($paso == 1)

                            <div class="pt-5 grid grid-cols-1 lg:grid-cols-2 gap-5">

                                <div class="space-y-3">
                                {{-- Info estudiante --}}
                                <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.06] rounded-xl p-4">
                                    <h4 class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 mb-3">
                                        Datos del Estudiante
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach ([['Nombre', $estudiante->name], ['Email', $estudiante->email], ['Cédula', $estudiante->cedula], ['Celular', $estudiante->phone], ['Matrícula', $estudiante->matricula_numero]] as [$key, $val])
                                            <div class="flex items-start gap-2">
                                                <span
                                                    class="text-[0.65rem] text-slate-400 dark:text-slate-500 uppercase tracking-wide w-16 flex-shrink-0 mt-0.5">{{ $key }}</span>
                                                <span class="text-xs text-slate-600 dark:text-white/70">{{ $val }}</span>
                                            </div>
                                        @endforeach
                                        <div class="flex items-start gap-2">
                                            <span
                                                class="text-[0.65rem] text-slate-500 uppercase tracking-wide w-16 flex-shrink-0 mt-0.5">Edad</span>
                                            <span class="text-xs text-slate-600 dark:text-white/70">
                                                {{ \Carbon\Carbon::parse($estudiante->fecha_nacimiento)->isoFormat('D [de] MMMM [de] YYYY') }}
                                                <span class="text-slate-400 dark:text-slate-400">({{ \Carbon\Carbon::parse($estudiante->fecha_nacimiento)->age }} años)</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Aviso (no bloquea): deudas pendientes del estudiante --}}
                                @php $deuda = $this->deudaPendiente; @endphp
                                @if ($deuda)
                                    <div class="flex items-start gap-3 rounded-xl border border-amber-300/60 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-900/15 px-4 py-3">
                                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                                                Tiene ${{ number_format($deuda['total'], 2) }} pendientes de pago
                                            </p>
                                            <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5 leading-relaxed">
                                                {{ $deuda['cantidad'] }} {{ $deuda['cantidad'] === 1 ? 'obligación' : 'obligaciones' }}@if ($deuda['vencidas'] > 0), {{ $deuda['vencidas'] }} {{ $deuda['vencidas'] === 1 ? 'vencida' : 'vencidas' }}@endif
                                                @if (count($deuda['periodos'])) · Períodos: {{ implode(', ', $deuda['periodos']) }}@endif.
                                                Puedes continuar con la matrícula.
                                                <a href="{{ route('administracion.administrativa.obligaciones.index', ['estudiante' => $estudiante->id]) }}"
                                                   target="_blank"
                                                   class="font-semibold underline underline-offset-2 hover:text-amber-900 dark:hover:text-amber-200">Ver sus obligaciones</a>
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Bloqueo: aspirante de validación sin convalidación confirmada --}}
                                @if ($pendienteConvalidacion && ! $matriculaId)
                                    <div class="flex items-start gap-3 rounded-xl border border-red-300/60 dark:border-red-500/30 bg-red-50 dark:bg-red-900/15 px-4 py-3">
                                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-red-700 dark:text-red-300">No se puede matricular todavía</p>
                                            <p class="text-xs text-red-600 dark:text-red-400 mt-0.5 leading-relaxed">
                                                Este estudiante ingresó por <strong>validación de conocimientos</strong> y aún no tiene su validación registrada.
                                                Sus valores de matrícula y arancel dependen de los semestres que se le exoneren.
                                                Regístrala primero en
                                                <a href="{{ route('administracion.administrativa.convalidacion.conocimiento', ['userId' => $estudiante->id]) }}"
                                                   class="font-semibold underline underline-offset-2 hover:text-red-800 dark:hover:text-red-200"
                                                   wire:navigate>Validación de Conocimientos</a>
                                                y luego vuelve a matricularlo.
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                </div>

                                {{-- Formulario --}}
                                <div class="space-y-4">

                                    {{-- Período --}}
                                    <div>
                                        <label
                                            class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-400/70 mb-1.5">
                                            Período Académico <span class="text-red-400">*</span>
                                        </label>
                                        <select wire:model="periodo_id"
                                            class="w-full px-4 py-2.5 rounded-xl text-sm text-slate-700 dark:text-white/80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all @error('periodo_id') border-red-500/50 @enderror">
                                            <option value="">Seleccionar período</option>
                                            @foreach ($periodos as $periodo)
                                                <option value="{{ $periodo->id }}">{{ $periodo->code }} -
                                                    {{ $periodo->description }}</option>
                                            @endforeach
                                        </select>
                                        @error('periodo_id')
                                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Carrera --}}
                                    <div>
                                        <label
                                            class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-400/70 mb-1.5">
                                            Carrera <span class="text-red-400">*</span>
                                        </label>
                                        <select wire:model="carrera_id"
                                            class="w-full px-4 py-2.5 rounded-xl text-sm text-slate-700 dark:text-white/80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all @error('carrera_id') border-red-500/50 @enderror">
                                            <option value="">Seleccionar carrera</option>
                                            @foreach ($carreras as $carrera)
                                                <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('carrera_id')
                                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Tipo --}}
                                    <div>
                                        <label
                                            class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-400/70 mb-1.5">
                                            Tipo de Matrícula
                                        </label>
                                        <select wire:model="tipo"
                                            class="w-full px-4 py-2.5 rounded-xl text-sm text-slate-700 dark:text-white/80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all">
                                            <option value="Nueva">Nueva</option>
                                            <option value="Renovacion">Renovación</option>
                                            <option value="Arrastre">Arrastre</option>
                                        </select>
                                    </div>

                                    {{-- Observaciones --}}
                                    <div>
                                        <label
                                            class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-400/70 mb-1.5">
                                            Observaciones
                                        </label>
                                        <textarea wire:model="observaciones" rows="3" placeholder="Observaciones adicionales…"
                                            class="w-full px-4 py-2.5 rounded-xl text-sm text-slate-700 dark:text-white/80 placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all resize-none"></textarea>
                                    </div>

                                </div>
                            </div>

                            {{-- Materias arrastradas --}}
                            @if (!empty($materiasArrastradas))
                                <div class="mt-4 bg-amber-500/[0.06] border border-amber-500/20 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                        </svg>
                                        <div class="flex-1">
                                            <h5 class="text-xs font-medium text-amber-400 mb-2">Materias Arrastradas</h5>
                                            <div class="space-y-2">
                                                @foreach ($materiasArrastradas as $index => $materiaArrastrada)
                                                    @php
                                                        $intento             = $materiaArrastrada['numero_intento'] ?? 1;
                                                        $sigIntento          = $intento + 1;
                                                        $porcentaje          = $materiaArrastrada['porcentaje_penalizacion'] ?? 30;
                                                        $costoExtra          = $materiaArrastrada['costo_adicional'] ?? 0;
                                                        $esUltimo            = $sigIntento >= 3;
                                                        $bloqueadoPorLimite  = $totalMateriasSeleccionadas >= 8 && !($materiaArrastrada['incluir'] ?? false);
                                                    @endphp
                                                    <label class="flex items-start gap-2.5 {{ $bloqueadoPorLimite ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }} group">
                                                        <input type="checkbox" class="w-4 h-4 rounded accent-amber-500 mt-0.5 flex-shrink-0"
                                                            wire:model="materiasArrastradas.{{ $index }}.incluir"
                                                            id="arrastre_{{ $index }}"
                                                            {{ $bloqueadoPorLimite ? 'disabled' : '' }}>
                                                        <div class="flex-1 border-l-2 {{ $esUltimo ? 'border-red-500/60' : 'border-amber-500/50' }} pl-2">
                                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                                <strong class="text-xs text-slate-700 dark:text-white/85">
                                                                    {{ $materiaArrastrada['materia']['name'] }}
                                                                </strong>
                                                                <span class="px-1.5 py-0.5 rounded text-[0.6rem] font-bold
                                                                    {{ $esUltimo ? 'bg-red-500/15 text-red-400' : 'bg-amber-500/15 text-amber-400' }}">
                                                                    {{ $sigIntento }}° intento{{ $esUltimo ? ' (último)' : '' }}
                                                                </span>
                                                                <span class="px-1.5 py-0.5 rounded text-[0.6rem] font-semibold bg-orange-500/15 text-orange-400">
                                                                    +{{ number_format($porcentaje, 0) }}%
                                                                </span>
                                                            </div>
                                                            <p class="text-[0.65rem] text-slate-500 dark:text-slate-400 mt-0.5">
                                                                Nota obtenida: {{ $materiaArrastrada['nota_obtenida'] }}
                                                                &nbsp;·&nbsp;
                                                                Costo extra: <span class="text-amber-400 font-semibold">${{ number_format($costoExtra, 2) }}</span>
                                                            </p>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- ── PASO 2: Materias ── --}}
                        @if ($paso == 2)
                            <div class="pt-5">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                                        Materias Disponibles</h4>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.65rem] font-semibold
                                        {{ $totalMateriasSeleccionadas >= 8
                                            ? 'bg-red-500/10 text-red-400 border border-red-500/20'
                                            : ($totalMateriasSeleccionadas >= 6
                                                ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                                                : 'bg-slate-100 text-slate-500 border border-slate-200 dark:bg-slate-700/60 dark:text-slate-400 dark:border-transparent') }}">
                                        {{ $totalMateriasSeleccionadas }} / 8 materias
                                    </span>
                                </div>

                                @if (empty($materiasDisponibles))
                                    <div
                                        class="bg-sky-500/[0.06] border border-sky-500/20 rounded-xl p-4 flex items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-400 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 16v-4m0-4h.01" />
                                        </svg>
                                        <p class="text-xs text-sky-300">No hay materias disponibles para esta carrera y
                                            período.</p>
                                    </div>
                                @else
                                    @if ($semestreSugerido)
                                    <div class="bg-sky-500/[0.06] border border-sky-500/20 rounded-xl p-3 flex items-center gap-2 mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" /><path d="M12 16v-4m0-4h.01" />
                                        </svg>
                                        <p class="text-xs text-sky-300">Se han pre-seleccionado las materias del <strong>{{ $semestreSugerido }}</strong> según el historial del estudiante. Puedes ajustar la selección.</p>
                                    </div>
                                    @endif
                                    <div class="space-y-4">
                                        @foreach ($materiasDisponibles as $semestreNombre => $materias)
                                            <div
                                                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.06] rounded-xl overflow-hidden">
                                                <div class="px-4 py-2.5 border-b border-slate-100 dark:border-white/[0.05] flex items-center justify-between">
                                                    <h5 class="text-xs font-medium text-slate-500 dark:text-white/60">{{ $semestreNombre }}</h5>
                                                    @if ($semestreSugerido && $semestreNombre === $semestreSugerido)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6rem] font-semibold bg-lime-500/10 border border-lime-500/20 text-lime-400">
                                                            ✓ Sugerido
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    @foreach ($materias as $materia)
                                                        @php
                                                            $bloqueadoPorLimite = $totalMateriasSeleccionadas >= 8 && !in_array($materia['id'], $materiasSeleccionadas);
                                                            $deshabilitada = !$materia['puede_inscribir'] || $bloqueadoPorLimite;
                                                            $noOfertada = !($materia['ofertada'] ?? true);
                                                        @endphp
                                                        <label
                                                            class="cursor-pointer {{ $deshabilitada ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                            <input type="checkbox" class="sr-only peer"
                                                                value="{{ $materia['id'] }}"
                                                                wire:model="materiasSeleccionadas"
                                                                {{ $deshabilitada ? 'disabled' : '' }}>
                                                            <div
                                                                class="border rounded-lg p-3 bg-white dark:bg-slate-900 transition-all duration-150
                                                            {{ $noOfertada ? 'border-slate-200 dark:border-white/[0.04] opacity-60' : 'border-slate-100 dark:border-white/[0.06]' }}
                                                            {{ !$deshabilitada && !$noOfertada ? 'hover:border-lime-500/30 hover:bg-lime-500/[0.04]' : '' }}
                                                            peer-checked:border-lime-500/40 peer-checked:bg-lime-500/[0.07]">
                                                                <p class="text-xs font-medium text-slate-700 dark:text-white/80 mb-0.5">
                                                                    {{ $materia['name'] }}</p>
                                                                <p class="text-[0.65rem] text-slate-400 dark:text-slate-500 mb-2">
                                                                    {{ $materia['code'] }}</p>
                                                                <div class="flex items-center justify-between">
                                                                    <span
                                                                        class="text-[0.62rem] text-slate-500">{{ number_format($materia['credits'], 2) }}
                                                                        créd.</span>
                                                                    <span
                                                                        class="px-1.5 py-0.5 rounded-full text-[0.6rem] font-medium
                                                                    {{ $materia['tipo'] == 'Obligatoria' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-slate-700 text-slate-400' }}">
                                                                        {{ $materia['tipo'] }}
                                                                    </span>
                                                                </div>
                                                                @if ($materia['credits_inconsistente'])
                                                                    <div class="mt-1.5 flex items-center gap-1 text-[0.6rem] text-amber-400">
                                                                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                        </svg>
                                                                        Créditos desactualizados — corregir en Materias
                                                                    </div>
                                                                @endif
                                                                @if (!$materia['puede_inscribir'] && !empty($materia['prerequisitos_faltantes']))
                                                                    <div class="mt-2 pt-2 border-t border-white/[0.05]">
                                                                        <p
                                                                            class="text-[0.6rem] text-red-400 font-medium mb-1">
                                                                            Prerequisitos:</p>
                                                                        @foreach ($materia['prerequisitos_faltantes'] as $pre)
                                                                            <p class="text-[0.6rem] text-red-400/70">•
                                                                                {{ $pre }}</p>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                                @if ($noOfertada)
                                                                    <div class="mt-1.5 flex items-center gap-1 text-[0.6rem] text-slate-400 dark:text-slate-500">
                                                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                                        </svg>
                                                                        No ofertada este período
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @error('materias')
                                    <div class="mt-3 bg-red-500/[0.06] border border-red-500/20 rounded-xl p-3">
                                        <p class="text-xs text-red-400">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        @endif

                        {{-- ── PASO 3: Paralelos ── --}}
                        @if ($paso == 3)
                            <div>
                                <h4
                                    class="sticky top-0 z-10 -mx-6 px-6 pt-5 pb-3 mb-4
                                           bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-white/[0.04]
                                           text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                                    Selección de Paralelos</h4>
                                @php
                                    $todasLasMaterias = collect($materiasSeleccionadas)
                                        ->merge(
                                            collect($materiasArrastradas)->where('incluir', true)->pluck('materia_id'),
                                        )
                                        ->unique();
                                @endphp
                                <div class="space-y-4">
                                    @foreach ($todasLasMaterias as $materiaId)
                                        @php
                                            $materia = \App\Models\Materia::find($materiaId);
                                            $paralelos = $paralelosDisponibles[$materiaId] ?? [];
                                        @endphp
                                        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.06] rounded-xl overflow-hidden">
                                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-white/[0.05]">
                                                <h5 class="text-xs font-medium text-slate-700 dark:text-white/70">{{ $materia->name }}
                                                    <span class="text-slate-500">({{ $materia->code }})</span>
                                                </h5>
                                            </div>
                                            <div class="p-4">
                                                @if (empty($paralelos))
                                                    <div
                                                        class="bg-amber-500/[0.06] border border-amber-500/20 rounded-lg p-3">
                                                        <p class="text-xs text-amber-400">No hay paralelos disponibles.</p>
                                                    </div>
                                                @else
                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                        @foreach ($paralelos as $paralelo)
                                                            <label
                                                                class="relative cursor-pointer {{ !$paralelo['tiene_cupo'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                                <input type="radio" class="sr-only peer" tabindex="-1"
                                                                    name="paralelo_{{ $materiaId }}"
                                                                    value="{{ $paralelo['id'] }}"
                                                                    x-model="paralelosLocales[{{ $materiaId }}]"
                                                                    {{ !$paralelo['tiene_cupo'] ? 'disabled' : '' }}>
                                                                <div
                                                                    class="border border-slate-100 dark:border-white/[0.06] rounded-lg p-3 bg-white dark:bg-slate-900 transition-all duration-150
                                                                {{ $paralelo['tiene_cupo'] ? 'hover:border-lime-500/30' : '' }}
                                                                peer-checked:border-lime-500/40 peer-checked:bg-lime-500/[0.07]">
                                                                    <p class="text-xs font-medium text-white/80 mb-1">
                                                                        {{ $paralelo['name'] }}</p>
                                                                    <p
                                                                        class="text-[0.65rem] {{ $paralelo['tiene_cupo'] ? 'text-lime-400' : 'text-red-400' }}">
                                                                        {{ $paralelo['cupo_disponible'] }} cupos
                                                                        disponibles
                                                                    </p>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('paralelos')
                                    <div class="mt-3 bg-red-500/[0.06] border border-red-500/20 rounded-xl p-3">
                                        <p class="text-xs text-red-400">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        @endif

                        {{-- ── PASO 4: Resumen ── --}}
                        @if ($paso == 4)
                            <div class="pt-5 grid grid-cols-1 lg:grid-cols-3 gap-5">

                                {{-- Tablas de materias --}}
                                <div class="lg:col-span-2 space-y-4">

                                    {{-- Proviene de admisión --}}
                                    @php $admision = $this->infoAdmision; @endphp
                                    @if ($admision)
                                        <div class="rounded-xl border border-sky-200 dark:border-sky-500/30 bg-sky-50 dark:bg-sky-900/15 p-4 space-y-3">
                                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                                <h5 class="text-[0.65rem] font-semibold tracking-[0.15em] uppercase text-sky-700 dark:text-sky-300">Proviene de admisión</h5>
                                                <span class="px-2 py-0.5 rounded-full text-[0.6rem] font-semibold border
                                                    {{ $admision['es_validacion'] ? 'bg-violet-50 dark:bg-violet-900/20 border-violet-200 dark:border-violet-500/30 text-violet-700 dark:text-violet-300' : 'bg-white dark:bg-slate-800 border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300' }}">
                                                    {{ $admision['es_validacion'] ? 'Validación de conocimientos' : 'Regular' }}
                                                </span>
                                            </div>

                                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                                                @if ($admision['es_validacion'])
                                                    <div class="flex justify-between sm:block">
                                                        <dt class="text-slate-500 dark:text-slate-400">Semestres validados</dt>
                                                        <dd class="font-semibold text-slate-700 dark:text-white/80">{{ $semestresExonerados }}</dd>
                                                    </div>
                                                @endif
                                                <div class="flex justify-between sm:block">
                                                    <dt class="text-slate-500 dark:text-slate-400">Convenio</dt>
                                                    <dd class="font-semibold text-slate-700 dark:text-white/80">
                                                        {{ $infoConvenio ? $infoConvenio['nombre'] . ' · ' . number_format($infoConvenio['porcentaje'], 0) . '% (arancel)' : 'Sin convenio' }}
                                                    </dd>
                                                </div>
                                                @if ($admision['aplicable'])
                                                    <div class="flex justify-between sm:block">
                                                        <dt class="text-slate-500 dark:text-slate-400">Pago verificado en admisión</dt>
                                                        <dd class="font-semibold text-slate-700 dark:text-white/80">
                                                            ${{ number_format($admision['monto'], 2) }}
                                                            <span class="font-normal text-slate-500 dark:text-slate-400">· {{ $admision['metodo'] ?? 'Transferencia' }} · {{ $admision['fecha'] }}</span>
                                                        </dd>
                                                    </div>
                                                @endif
                                            </dl>

                                            @if ($admision['aplicable'] && $admision['faltante'] <= 0)
                                                <p class="text-xs rounded-lg px-3 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300">
                                                    Al guardar, el pago de admisión cubre la matrícula: quedará <strong>pagada y habilitada</strong>.
                                                </p>
                                            @elseif ($admision['aplicable'])
                                                <p class="text-xs rounded-lg px-3 py-2 bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-300">
                                                    Al guardar se aplicarán <strong>${{ number_format($admision['aplicado'], 2) }}</strong> del pago de admisión.
                                                    Faltarán <strong>${{ number_format($admision['faltante'], 2) }}</strong>: la matrícula se habilitará cuando se complete el pago.
                                                </p>
                                            @elseif ($admision['sin_monto'])
                                                <p class="text-xs rounded-lg px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                                    Su pago se verificó sin registrar monto ni método, así que no se aplicará automáticamente. Regístralo al cobrar la matrícula.
                                                </p>
                                            @else
                                                <p class="text-xs rounded-lg px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                                    No tiene un pago de matrícula verificado en admisión: se cobra normalmente.
                                                </p>
                                            @endif

                                            @if ($admision['excedente'] > 0)
                                                <p class="text-[0.7rem] text-amber-700 dark:text-amber-400">
                                                    El pago de admisión supera la matrícula en ${{ number_format($admision['excedente'], 2) }}; ese excedente no se aplica automáticamente.
                                                </p>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Materias regulares --}}
                                    @if (!empty($materiasSeleccionadas))
                                        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.06] rounded-xl overflow-hidden">
                                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-white/[0.05]">
                                                <h5
                                                    class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                                                    Materias Regulares</h5>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="w-full">
                                                    <thead>
                                                        <tr class="border-b border-slate-100 dark:border-white/[0.05]">
                                                            <th
                                                                class="px-4 py-2.5 text-left text-[0.6rem] uppercase tracking-wider text-slate-500">
                                                                Materia</th>
                                                            <th
                                                                class="px-4 py-2.5 text-center text-[0.6rem] uppercase tracking-wider text-slate-500">
                                                                Créd.</th>
                                                            <th
                                                                class="px-4 py-2.5 text-center text-[0.6rem] uppercase tracking-wider text-slate-500">
                                                                Paralelo</th>
                                                            <th
                                                                class="px-4 py-2.5 text-right text-[0.6rem] uppercase tracking-wider text-slate-500">
                                                                Costo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                                                        @foreach ($materiasSeleccionadas as $materiaId)
                                                            @php
                                                                $materia = \App\Models\Materia::find($materiaId);
                                                                $carrera = \App\Models\Carrera::find($carrera_id);
                                                                $paralelo = isset($paralelosSeleccionados[$materiaId])
                                                                    ? \App\Models\Paralelo::find(
                                                                        $paralelosSeleccionados[$materiaId],
                                                                    )
                                                                    : null;
                                                                $creditosVivos = ($materia->horas_teoricas + $materia->horas_practicas) / 48;
                                                                $costo = $creditosVivos * $carrera->costo_credito;
                                                            @endphp
                                                            <tr>
                                                                <td class="px-4 py-2.5">
                                                                    <p class="text-xs text-slate-700 dark:text-white/75">{{ $materia->name }}
                                                                    </p>
                                                                    <p class="text-[0.65rem] text-slate-500">
                                                                        {{ $materia->code }}</p>
                                                                </td>
                                                                <td class="px-4 py-2.5 text-center text-xs text-slate-400">
                                                                    {{ number_format(($materia->horas_teoricas + $materia->horas_practicas) / 48, 2) }}
                                                                </td>
                                                                <td class="px-4 py-2.5 text-center text-xs text-slate-400">
                                                                    {{ $paralelo ? $paralelo->name : '—' }}</td>
                                                                <td
                                                                    class="px-4 py-2.5 text-right text-xs font-medium text-white/70">
                                                                    ${{ number_format($costo, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Materias arrastre --}}
                                    @php $materiasArrastradasIncluidas = collect($materiasArrastradas)->where('incluir', true); @endphp
                                    @if ($materiasArrastradasIncluidas->count() > 0)
                                        <div class="bg-slate-800 border border-amber-500/15 rounded-xl overflow-hidden">
                                            <div class="px-4 py-2.5 border-b border-amber-500/10 bg-amber-500/[0.04]">
                                                <h5
                                                    class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-amber-400/70">
                                                    Materias de Arrastre</h5>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="w-full">
                                                    <thead>
                                                        <tr class="border-b border-slate-100 dark:border-white/[0.05]">
                                                            <th
                                                                class="px-4 py-2.5 text-left text-[0.6rem] uppercase tracking-wider text-slate-500">
                                                                Materia</th>
                                                            <th
                                                                class="px-4 py-2.5 text-center text-[0.6rem] uppercase tracking-wider text-slate-500">
                                                                Créd.</th>
                                                            <th
                                                                class="px-4 py-2.5 text-center text-[0.6rem] uppercase tracking-wider text-slate-500">
                                                                Paralelo</th>
                                                            <th
                                                                class="px-4 py-2.5 text-right text-[0.6rem] uppercase tracking-wider text-slate-500">
                                                                Costo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                                                        @foreach ($materiasArrastradasIncluidas as $materiaArrastrada)
                                                            @php
                                                                $materia    = \App\Models\Materia::find($materiaArrastrada['materia_id']);
                                                                $paralelo   = isset($paralelosSeleccionados[$materiaArrastrada['materia_id']])
                                                                    ? \App\Models\Paralelo::find($paralelosSeleccionados[$materiaArrastrada['materia_id']])
                                                                    : null;
                                                                $costo      = $materiaArrastrada['costo_adicional'] ?? 0;
                                                                $intento    = $materiaArrastrada['numero_intento'] ?? 1;
                                                                $sigIntento = $intento + 1;
                                                                $porcentaje = $materiaArrastrada['porcentaje_penalizacion'] ?? 30;
                                                                $esUltimo   = $sigIntento >= 3;
                                                            @endphp
                                                            <tr>
                                                                <td class="px-4 py-2.5">
                                                                    <p class="text-xs text-slate-700 dark:text-white/75">{{ $materia->name }}</p>
                                                                    <p class="text-[0.65rem] text-slate-500">{{ $materia->code }}</p>
                                                                    <div class="flex items-center gap-1 mt-0.5">
                                                                        <span class="px-1.5 py-0.5 rounded text-[0.58rem] font-bold
                                                                            {{ $esUltimo ? 'bg-red-500/15 text-red-400' : 'bg-amber-500/15 text-amber-400' }}">
                                                                            {{ $sigIntento }}° intento{{ $esUltimo ? ' (último)' : '' }}
                                                                        </span>
                                                                        <span class="px-1.5 py-0.5 rounded text-[0.58rem] font-semibold bg-orange-500/15 text-orange-400">
                                                                            +{{ number_format($porcentaje, 0) }}%
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-2.5 text-center text-xs text-slate-400">
                                                                    {{ number_format(($materia->horas_teoricas + $materia->horas_practicas) / 48, 2) }}
                                                                </td>
                                                                <td class="px-4 py-2.5 text-center text-xs text-slate-400">
                                                                    {{ $paralelo ? $paralelo->name : '—' }}</td>
                                                                <td class="px-4 py-2.5 text-right text-xs font-medium text-amber-400">
                                                                    +${{ number_format($costo, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Panel costos --}}
                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.06] rounded-xl overflow-hidden sticky top-0">
                                        <div class="px-4 py-2.5 border-b border-slate-100 dark:border-white/[0.05]">
                                            <h5
                                                class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">
                                                Resumen de Costos</h5>
                                        </div>
                                        <div class="p-4 space-y-4">

                                            {{-- ── SECCIÓN CRÉDITOS (informativo) ── --}}
                                            @php $costoPorCredito = $totalCreditos > 0 ? $montoArancel / $totalCreditos : 0; @endphp
                                            <div
                                                class="bg-sky-500/[0.06] border border-sky-500/20 rounded-lg p-3 space-y-2">
                                                <p
                                                    class="text-[0.6rem] font-semibold tracking-[0.15em] uppercase text-sky-400/80 mb-2.5">
                                                    Créditos
                                                </p>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">Total Créditos</span>
    <span class="text-xs font-semibold text-sky-300">
                                                        {{ number_format($totalCreditos, 2) }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">Costo por Crédito</span>
                                                    <span class="text-xs font-semibold text-sky-300">
                                                        ${{ number_format($costoPorCredito, 2) }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- ── SECCIÓN MATRÍCULA (pago inmediato) ── --}}
                                            <div class="space-y-3">
                                                <p
                                                    class="text-[0.6rem] font-semibold tracking-[0.15em] uppercase text-lime-400/80">
                                                    Costo Matrícula
                                                </p>

                                                <div class="space-y-2">
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-xs text-slate-500 dark:text-slate-400">Costo Matrícula</span>
                                                        <span class="text-xs font-medium text-slate-700 dark:text-white/70">
                                                            ${{ number_format($montoMatricula, 2) }}
                                                        </span>
                                                    </div>
                                                    @if ($costoArrastres > 0)
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-xs text-amber-400/80">Costo Arrastres</span>
                                                            <span class="text-xs font-medium text-amber-400">
                                                                +${{ number_format($costoArrastres, 2) }}
                                                            </span>
                                                        </div>
                                                        @foreach (collect($materiasArrastradas)->where('incluir', true) as $ma)
                                                            <div class="flex justify-between items-center pl-3 border-l border-amber-500/20">
                                                                <span class="text-[0.6rem] text-slate-500 dark:text-slate-400 truncate max-w-[110px]">
                                                                    {{ $ma['materia']['name'] }}
                                                                    <span class="text-orange-400">(+{{ number_format($ma['porcentaje_penalizacion'] ?? 30, 0) }}%)</span>
                                                                </span>
                                                                <span class="text-[0.6rem] text-amber-400/70">+${{ number_format($ma['costo_adicional'] ?? 0, 2) }}</span>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    @if ($valorInscripcion > 0)
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-xs text-amber-300/80">Inscripción (1ª matrícula)</span>
                                                            <span class="text-xs font-medium text-amber-300">
                                                                ${{ number_format($valorInscripcion, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($tieneReintegro)
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-xs text-orange-400/90">Reintegro por retiro (10%, sin descuento)</span>
                                                            <span class="text-xs font-medium text-orange-400">
                                                                +${{ number_format($montoReintegro, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div
                                                    class="flex justify-between items-center bg-lime-500/[0.07] border border-lime-500/20 rounded-lg px-3 py-2">
                                                    <span class="text-xs font-semibold text-slate-700 dark:text-white/70">Total a Pagar</span>
                                                    <span class="text-base font-bold text-lime-400">
                                                        ${{ number_format($totalPagar + $valorInscripcion, 2) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="h-px bg-slate-200 dark:bg-white/[0.05]"></div>

                                            {{-- ── SECCIÓN VALOR PENDIENTE (pago futuro) ── --}}
                                            <div
                                                class="bg-amber-500/[0.06] border border-amber-500/20 rounded-lg p-3 space-y-2">
                                                <p
                                                    class="text-[0.6rem] font-semibold tracking-[0.15em] uppercase text-amber-400/80 mb-2.5">
                                                    Valor Pendiente
                                                </p>

                                                {{-- Arancel base --}}
                                                <div class="flex justify-between items-center">
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                                        Arancel base{{ $tipo === 'Validacion' ? ' (Convalidación)' : '' }}
                                                    </span>
                                                    <span class="text-xs font-medium text-slate-700 dark:text-white/70">
                                                        ${{ number_format($montoArancelBruto, 2) }}
                                                    </span>
                                                </div>

                                                {{-- Beca --}}
                                                @if ($infoBeca)
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-xs text-lime-400/90">
                                                            Beca {{ $infoBeca['nombre'] }}
                                                            <span class="text-lime-500">({{ number_format($infoBeca['porcentaje'], 0) }}%)</span>
                                                        </span>
                                                        <span class="text-xs font-medium text-lime-400">
                                                            -${{ number_format($descuentoBeca, 2) }}
                                                        </span>
                                                    </div>
                                                @endif

                                                {{-- Convenio --}}
                                                @if ($infoConvenio)
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-xs text-violet-400/90">
                                                            Convenio {{ $infoConvenio['nombre'] }}
                                                            <span class="text-violet-500">({{ number_format($infoConvenio['porcentaje'], 0) }}%)</span>
                                                        </span>
                                                        <span class="text-xs font-medium text-violet-400">
                                                            -${{ number_format($descuentoConvenio, 2) }}
                                                        </span>
                                                    </div>
                                                @endif

                                                @if ($esGratuidad)
                                                    <div class="flex items-center justify-center gap-1.5 py-1">
                                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[0.65rem] font-bold bg-lime-500/15 border border-lime-500/30 text-lime-400 tracking-wide uppercase">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                            Gratuidad — Arancel $0.00
                                                        </span>
                                                    </div>
                                                @endif

                                                <div class="border-t border-amber-500/20 pt-2 flex justify-between items-center">
                                                    <span class="text-xs font-semibold text-slate-400">Arancel Neto</span>
                                                    <span class="text-xs font-bold text-amber-300">
                                                        ${{ number_format($montoArancel, 2) }}
                                                    </span>
                                                </div>

                                                {{-- Cuotas --}}
                                                <div class="pt-1">
                                                    <span class="text-[0.6rem] text-amber-400/60 uppercase tracking-wide block mb-1">
                                                        Cuotas del arancel
                                                    </span>
                                                    @if($esGratuidad)
                                                        <span class="inline-block text-xs font-bold bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded">
                                                            GRATUIDAD — $0.00
                                                        </span>
                                                    @else
                                                        <p class="text-xs font-semibold text-amber-300">
                                                            5 cuotas — ${{ number_format($montoArancel / 5, 2) }} c/u
                                                        </p>
                                                    @endif
                                                </div>

                                                <p class="text-[0.6rem] text-amber-400/50 leading-relaxed">
                                                    Distribuidas proporcionalmente en el período de la carrera.
                                                </p>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endif

                    </div>

                    {{-- Modal Footer --}}
                    <div
                        class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50 dark:bg-black/15 flex items-center justify-between gap-3 flex-shrink-0">

                        {{-- Anterior --}}
                        <div>
                            @if ($paso > 1)
                                <button type="button" wire:click="pasoAnterior"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium
                                       text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] bg-transparent
                                       hover:bg-white/5 hover:text-slate-200 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Anterior
                                </button>
                            @endif
                        </div>

                        {{-- Cancelar + Siguiente/Guardar --}}
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="cerrarModal"
                                class="px-4 py-2 rounded-full text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-100 dark:hover:bg-white/5 hover:text-slate-700 dark:hover:text-slate-200 transition-all duration-200">
                                Cancelar
                            </button>

                            @if ($paso < $totalPasos)
                                <button type="button" x-on:click="avanzarPaso()"
                                    @disabled($paso == 1 && $pendienteConvalidacion && ! $matriculaId)
                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-full text-xs font-medium tracking-wide uppercase
                                       text-white/90
                                       bg-gradient-to-r from-green-800/70 via-sky-800/60 to-purple-900/55
                                       border border-lime-500/25
                                       hover:from-green-700/80 hover:via-sky-700/70 hover:to-purple-800/65
                                       hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-900/30
                                       active:translate-y-0 transition-all duration-200
                                       disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none">
                                    Siguiente
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @else
                                <button type="button" wire:click="guardarMatricula"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-full text-xs font-medium tracking-wide uppercase
                                       text-white/90
                                       bg-gradient-to-r from-green-800/70 via-sky-800/60 to-purple-900/55
                                       border border-lime-500/25
                                       hover:from-green-700/80 hover:via-sky-700/70 hover:to-purple-800/65
                                       hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-900/30
                                       active:translate-y-0 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $matriculaId ? 'Actualizar' : 'Guardar' }} Matrícula
                                </button>
                            @endif
                        </div>

                    </div>

                </div>
            </div>
        @endif

    {{-- ══════════════════════════════════════════════════════
         MODAL: RETIRO DE ESTUDIANTE
    ═══════════════════════════════════════════════════════ --}}
    @if ($showRetiroModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarRetiro"></div>
            <div class="relative z-10 w-full max-w-md mx-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-orange-200/30 dark:border-orange-500/20 overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center gap-3 px-6 py-4 bg-orange-50 dark:bg-orange-900/20 border-b border-orange-200/40 dark:border-orange-500/20">
                        <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-orange-900 dark:text-orange-100">Registrar Retiro</h3>
                            <p class="text-xs text-orange-700/70 dark:text-orange-300/60 truncate max-w-xs">{{ $retiroEstudianteNombre }}</p>
                        </div>
                        <button type="button" wire:click="cerrarRetiro" class="ml-auto text-orange-400 hover:text-orange-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5 space-y-4">
                        <div class="bg-orange-50 dark:bg-orange-900/10 border border-orange-200/50 dark:border-orange-500/20 rounded-lg px-4 py-3">
                            <p class="text-xs text-orange-700 dark:text-orange-300/80 leading-relaxed">
                                El retiro cambiará el estado de la matrícula a <strong>Retirada</strong> y marcará todas las materias como <strong>Retirado</strong>.
                                Si el estudiante se re-matrícula en el futuro, se aplicará un recargo de 10% (reintegro).
                            </p>
                        </div>

                        {{-- Tipo de retiro --}}
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Tipo de retiro <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" wire:click="$set('retiroTipo', 'Voluntario')"
                                    class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg border text-xs font-medium transition-all
                                        {{ $retiroTipo === 'Voluntario'
                                            ? 'bg-orange-500 border-orange-500 text-white shadow-sm'
                                            : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:border-orange-300 dark:hover:border-orange-500/40' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Voluntario
                                </button>
                                <button type="button" wire:click="$set('retiroTipo', 'Administrativo')"
                                    class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg border text-xs font-medium transition-all
                                        {{ $retiroTipo === 'Administrativo'
                                            ? 'bg-orange-500 border-orange-500 text-white shadow-sm'
                                            : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:border-orange-300 dark:hover:border-orange-500/40' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Administrativo
                                </button>
                            </div>
                            @error('retiroTipo')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Fecha de retiro <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="retiroFecha"
                                class="w-full text-sm rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-800 text-slate-900 dark:text-white/80 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-500/50 @error('retiroFecha') border-red-400 @enderror">
                            @error('retiroFecha')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Solicitud / Certificado de retiro <span class="text-red-500">*</span>
                            </label>
                            <label class="flex flex-col items-center justify-center w-full h-24 rounded-lg border-2 border-dashed cursor-pointer transition-colors
                                          @error('retiroDocumento') border-red-400 bg-red-50 dark:bg-red-900/10 @else border-orange-200 dark:border-orange-500/30 bg-orange-50/50 dark:bg-orange-900/10 hover:bg-orange-50 dark:hover:bg-orange-900/20 @enderror">
                                @if($retiroDocumento)
                                    <div class="flex items-center gap-2 text-orange-700 dark:text-orange-300">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                        <span class="text-xs font-medium truncate max-w-[220px]">{{ $retiroDocumento->getClientOriginalName() }}</span>
                                    </div>
                                    <p class="text-[10px] text-orange-500 dark:text-orange-400 mt-1">Clic para cambiar</p>
                                @else
                                    <svg class="w-6 h-6 text-orange-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                    </svg>
                                    <p class="text-xs text-orange-600 dark:text-orange-300 font-medium">Subir documento</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">PDF, JPG o PNG — máx. 5 MB</p>
                                @endif
                                <input type="file" wire:model="retiroDocumento" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                            </label>
                            <div wire:loading wire:target="retiroDocumento" class="mt-1 text-xs text-slate-400">Cargando archivo…</div>
                            @error('retiroDocumento')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Motivo <span class="text-slate-400">(opcional)</span>
                            </label>
                            <textarea wire:model="retiroMotivo" rows="2" placeholder="Indique el motivo del retiro..."
                                class="w-full text-sm rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-800 text-slate-900 dark:text-white/80 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-500/50 resize-none @error('retiroMotivo') border-red-400 @enderror"></textarea>
                            @error('retiroMotivo')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-2 px-6 py-4 bg-slate-50 dark:bg-black/15 border-t border-slate-100 dark:border-white/[0.05]">
                        <button type="button" wire:click="cerrarRetiro"
                            class="px-4 py-2 text-xs font-medium rounded-full text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="confirmarRetiro" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-semibold rounded-full text-white
                                bg-orange-600 hover:bg-orange-700 disabled:opacity-60 transition-all shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                            <span wire:loading.remove wire:target="confirmarRetiro">Confirmar Retiro</span>
                            <span wire:loading wire:target="confirmarRetiro">Procesando...</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    </div>

    @push('js')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('matricula-guardada', (event) => {
                    const data = event[0] ?? event;

                    if (!data.redirigir || !data.matricula_id) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.mensaje,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#2563eb'
                        });
                        return;
                    }

                    // Viene de admisión y su pago cubrió la matrícula: nada que cobrar
                    if (data.caso === 'admision_pagada') {
                        Swal.fire({
                            title: '¡Matrícula habilitada!',
                            text: data.mensaje,
                            icon: 'success',
                            confirmButtonText: 'Volver a matriculación',
                            confirmButtonColor: '#2563eb',
                        });
                        return;
                    }

                    // Validación con semestres exonerados: falta completar la diferencia
                    if (data.caso === 'admision_parcial') {
                        Swal.fire({
                            title: 'Matrícula registrada',
                            text: data.mensaje,
                            icon: 'info',
                            confirmButtonText: 'Volver a matriculación',
                            showDenyButton: true,
                            denyButtonText: 'Cobrar ahora',
                            confirmButtonColor: '#2563eb',
                            denyButtonColor: '#d97706',
                        }).then((result) => {
                            if (result.isDenied) window.location.href = data.url_cobrar;
                        });
                        return;
                    }

                    Swal.fire({
                        title: '¡Matrícula creada!',
                        text: data.mensaje,
                        icon: 'success',
                        confirmButtonText: 'Registrar Pago',
                        showDenyButton: true,
                        denyButtonText: 'Ir a Obligaciones',
                        showCancelButton: true,
                        cancelButtonText: 'Volver a matriculación',
                        confirmButtonColor: '#2563eb',
                        denyButtonColor: '#475569',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = data.url_pago;
                        } else if (result.isDenied) {
                            window.location.href = data.url_obligaciones;
                        }
                    });
                });
                Livewire.on('error', (event) => {
                    const data = event[0] ?? event;
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });
            Livewire.on('modal-opened', () => {
                document.documentElement.style.overflow = 'hidden';
            });
            Livewire.on('modal-closed', () => {
                document.documentElement.style.overflow = '';
            });
        </script>
    @endpush
