<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- ═══════════════════════════════════════
         BLOQUE 1 — HEADER FUSIONADO
    ═══════════════════════════════════════ --}}
    <div class="bg-slate-900 border border-slate-700/50 relative overflow-hidden rounded-xl">

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
                    <h1 class="text-xl font-semibold text-white/90 leading-none">Gestión de Matrículas</h1>
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
                    class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-white/80 placeholder-slate-500
                           bg-slate-800 border border-white/[0.08]
                           focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10
                           transition-all duration-200">
            </div>

        </div>
    </div>


    {{-- ═══════════════════════════════════════
         BLOQUE 2 — FILTROS
    ═══════════════════════════════════════ --}}
    <div class="bg-slate-900 border border-white/[0.06] rounded-xl overflow-hidden ring-1 ring-inset ring-white/[0.04]">

        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/20 to-transparent"></div>

        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Período --}}
            <div>
                <label class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400 mb-1.5">
                    Período
                </label>
                <select wire:model.live="selectedPeriodo"
                    class="w-full px-4 py-2 rounded-xl text-xs text-white/80
                           bg-slate-800 border border-white/[0.08]
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
                <label class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400 mb-1.5">
                    Carrera
                </label>
                <select wire:model.live="selectedCarrera"
                    class="w-full px-4 py-2 rounded-xl text-xs text-white/80
                           bg-slate-800 border border-white/[0.08]
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
         BLOQUE 3 — TABLA
    ═══════════════════════════════════════ --}}
    <div
        class="bg-slate-900 rounded-2xl border border-white/[0.06] overflow-hidden shadow-2xl shadow-black/40 ring-1 ring-inset ring-white/[0.04]">

        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/[0.05]">
                        <th
                            class="px-5 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">
                            Estudiante</th>
                        <th
                            class="px-5 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">
                            Cédula</th>
                        <th
                            class="px-5 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">
                            Email</th>
                        <th
                            class="px-5 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">
                            Matrícula Actual</th>
                        <th
                            class="px-5 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">
                            Estado</th>
                        <th
                            class="px-5 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400 w-24">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @forelse($estudiantes as $student)
                        @php $matriculaActual = $student->matriculas->first(); @endphp
                        <tr class="group hover:bg-white/[0.02] transition-colors duration-150">

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
                                        <p class="text-sm font-medium text-white/80">{{ $student->name }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $student->matricula_numero }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Cédula --}}
                            <td class="px-5 py-3.5 text-center">
                                <span class="text-xs text-slate-400 font-mono">{{ $student->cedula }}</span>
                            </td>

                            {{-- Email --}}
                            <td class="px-5 py-3.5">
                                <span class="text-xs text-slate-400">{{ $student->email }}</span>
                            </td>

                            {{-- Matrícula --}}
                            <td class="px-5 py-3.5 text-center">
                                @if ($matriculaActual)
                                    <p class="text-xs font-semibold text-white/75">{{ $matriculaActual->code }}</p>
                                    <p class="text-[0.65rem] text-slate-500 mt-0.5">
                                        {{ $matriculaActual->carrera->name }}</p>
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
                                    <button type="button" wire:click="editarMatricula({{ $matriculaActual->id }})"
                                        title="Editar matrícula"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                               text-slate-400 border border-white/[0.06] bg-slate-800
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
                                @else
                                    <button type="button" wire:click="iniciarMatricula({{ $student->id }})"
                                        title="Nueva matrícula"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium
                                               text-white/90
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
                                @endif
                            </td>

                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
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
            <div class="px-6 py-4 border-t border-white/[0.05] bg-black/10">
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
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="cerrarModal"></div>

                {{-- Panel --}}
                <div
                    class="relative w-full max-w-5xl bg-slate-900 rounded-2xl border border-white/[0.08] shadow-2xl shadow-black/60 overflow-hidden flex flex-col max-h-[90vh]">

                    {{-- Shimmer top --}}
                    <div class="h-px bg-gradient-to-r from-transparent via-lime-500/40 to-transparent flex-shrink-0"></div>

                    {{-- Modal Header --}}
                    <div
                        class="px-6 py-4 border-b border-white/[0.06] flex items-center justify-between gap-4 flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-700/60 to-sky-700/60 border border-white/[0.08] flex items-center justify-center">
                                <span class="text-xs font-semibold text-white/80 uppercase">
                                    {{ strtoupper(substr($estudiante->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-white/90 leading-none">
                                    {{ $matriculaId ? 'Editar' : 'Nueva' }} Matrícula
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $estudiante->name }} · #{{ $estudiante->id }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="cerrarModal"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 border border-white/[0.06] bg-slate-800 hover:text-white hover:bg-slate-700 transition-all duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Progress Steps --}}
                    <div class="px-6 py-4 border-b border-white/[0.05] flex-shrink-0">
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
                                            : 'bg-slate-800 border border-white/[0.06] text-slate-500' }}">
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
                                        {{ $paso >= $step['num'] ? 'text-lime-400/80' : 'text-slate-500' }}">
                                            {{ $step['label'] }}
                                        </span>
                                    </div>
                                    @if ($i < count($steps) - 1)
                                        <div
                                            class="flex-1 mx-2 h-px {{ $paso > $step['num'] ? 'bg-lime-500/30' : 'bg-white/[0.05]' }}">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <div
                        class="flex-1 overflow-y-auto px-6 py-5
                    [&::-webkit-scrollbar]:w-1.5
                    [&::-webkit-scrollbar-track]:bg-slate-800/50
                    [&::-webkit-scrollbar-thumb]:bg-slate-600
                    [&::-webkit-scrollbar-thumb]:rounded-full">

                        {{-- ── PASO 1: Información ── --}}
                        @if ($paso == 1)
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                                {{-- Info estudiante --}}
                                <div class="bg-slate-800 border border-white/[0.06] rounded-xl p-4">
                                    <h4 class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400 mb-3">
                                        Información del Estudiante
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach ([['Nombre', $estudiante->name], ['Email', $estudiante->email], ['Cédula', $estudiante->cedula], ['Celular', $estudiante->phone], ['Matrícula', $estudiante->matricula_numero]] as [$key, $val])
                                            <div class="flex items-start gap-2">
                                                <span
                                                    class="text-[0.65rem] text-slate-500 uppercase tracking-wide w-16 flex-shrink-0 mt-0.5">{{ $key }}</span>
                                                <span class="text-xs text-white/70">{{ $val }}</span>
                                            </div>
                                        @endforeach
                                        <div class="flex items-start gap-2">
                                            <span
                                                class="text-[0.65rem] text-slate-500 uppercase tracking-wide w-16 flex-shrink-0 mt-0.5">Edad</span>
                                            <span class="text-xs text-white/70">
                                                {{ $estudiante->fecha_nacimiento }}
                                                <span
                                                    class="text-slate-400">({{ \Carbon\Carbon::parse($estudiante->fecha_nacimiento)->age }}
                                                    años)</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Formulario --}}
                                <div class="space-y-4">

                                    {{-- Carrera --}}
                                    <div>
                                        <label
                                            class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-400/70 mb-1.5">
                                            Carrera <span class="text-red-400">*</span>
                                        </label>
                                        <select wire:model="carrera_id"
                                            class="w-full px-4 py-2.5 rounded-xl text-sm text-white/80 bg-slate-800 border border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all @error('carrera_id') border-red-500/50 @enderror">
                                            <option value="">Seleccionar carrera</option>
                                            @foreach ($carreras as $carrera)
                                                <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('carrera_id')
                                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Período --}}
                                    <div>
                                        <label
                                            class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-400/70 mb-1.5">
                                            Período Académico <span class="text-red-400">*</span>
                                        </label>
                                        <select wire:model="periodo_id"
                                            class="w-full px-4 py-2.5 rounded-xl text-sm text-white/80 bg-slate-800 border border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all @error('periodo_id') border-red-500/50 @enderror">
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

                                    {{-- Tipo --}}
                                    <div>
                                        <label
                                            class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-400/70 mb-1.5">
                                            Tipo de Matrícula
                                        </label>
                                        <select wire:model="tipo"
                                            class="w-full px-4 py-2.5 rounded-xl text-sm text-white/80 bg-slate-800 border border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all">
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
                                            class="w-full px-4 py-2.5 rounded-xl text-sm text-white/80 placeholder-slate-500 bg-slate-800 border border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all resize-none"></textarea>
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
                                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                                        <input type="checkbox" class="w-4 h-4 rounded accent-lime-500"
                                                            wire:model="materiasArrastradas.{{ $index }}.incluir"
                                                            id="arrastre_{{ $index }}">
                                                        <span class="text-xs text-white/70">
                                                            <strong
                                                                class="text-white/85">{{ $materiaArrastrada['materia']['name'] }}</strong>
                                                            <span class="text-slate-400"> — Nota:
                                                                {{ $materiaArrastrada['nota_obtenida'] }}</span>
                                                        </span>
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
                            <div>
                                <h4 class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400 mb-4">
                                    Materias Disponibles</h4>

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
                                    <div class="space-y-4">
                                        @foreach ($materiasDisponibles as $semestreNombre => $materias)
                                            <div
                                                class="bg-slate-800 border border-white/[0.06] rounded-xl overflow-hidden">
                                                <div class="px-4 py-2.5 border-b border-white/[0.05]">
                                                    <h5 class="text-xs font-medium text-white/60">{{ $semestreNombre }}
                                                    </h5>
                                                </div>
                                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    @foreach ($materias as $materia)
                                                        <label
                                                            class="cursor-pointer {{ !$materia['puede_inscribir'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                            <input type="checkbox" class="sr-only peer"
                                                                value="{{ $materia['id'] }}"
                                                                wire:model="materiasSeleccionadas"
                                                                {{ !$materia['puede_inscribir'] ? 'disabled' : '' }}>
                                                            <div
                                                                class="border border-white/[0.06] rounded-lg p-3 bg-slate-900 transition-all duration-150
                                                            {{ $materia['puede_inscribir'] ? 'hover:border-lime-500/30 hover:bg-lime-500/[0.04]' : '' }}
                                                            peer-checked:border-lime-500/40 peer-checked:bg-lime-500/[0.07]">
                                                                <p class="text-xs font-medium text-white/80 mb-0.5">
                                                                    {{ $materia['name'] }}</p>
                                                                <p class="text-[0.65rem] text-slate-500 mb-2">
                                                                    {{ $materia['code'] }}</p>
                                                                <div class="flex items-center justify-between">
                                                                    <span
                                                                        class="text-[0.62rem] text-slate-500">{{ $materia['credits'] }}
                                                                        créd.</span>
                                                                    <span
                                                                        class="px-1.5 py-0.5 rounded-full text-[0.6rem] font-medium
                                                                    {{ $materia['tipo'] == 'Obligatoria' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-slate-700 text-slate-400' }}">
                                                                        {{ $materia['tipo'] }}
                                                                    </span>
                                                                </div>
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
                                <h4 class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400 mb-4">
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
                                        <div class="bg-slate-800 border border-white/[0.06] rounded-xl overflow-hidden">
                                            <div class="px-4 py-2.5 border-b border-white/[0.05]">
                                                <h5 class="text-xs font-medium text-white/70">{{ $materia->name }}
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
                                                                class="cursor-pointer {{ !$paralelo['tiene_cupo'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                                <input type="radio" class="sr-only peer"
                                                                    name="paralelo_{{ $materiaId }}"
                                                                    value="{{ $paralelo['id'] }}"
                                                                    wire:model="paralelosSeleccionados.{{ $materiaId }}"
                                                                    {{ !$paralelo['tiene_cupo'] ? 'disabled' : '' }}>
                                                                <div
                                                                    class="border border-white/[0.06] rounded-lg p-3 bg-slate-900 transition-all duration-150
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
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                                {{-- Tablas de materias --}}
                                <div class="lg:col-span-2 space-y-4">

                                    {{-- Materias regulares --}}
                                    @if (!empty($materiasSeleccionadas))
                                        <div class="bg-slate-800 border border-white/[0.06] rounded-xl overflow-hidden">
                                            <div class="px-4 py-2.5 border-b border-white/[0.05]">
                                                <h5
                                                    class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">
                                                    Materias Regulares</h5>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="w-full">
                                                    <thead>
                                                        <tr class="border-b border-white/[0.05]">
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
                                                    <tbody class="divide-y divide-white/[0.04]">
                                                        @foreach ($materiasSeleccionadas as $materiaId)
                                                            @php
                                                                $materia = \App\Models\Materia::find($materiaId);
                                                                $carrera = \App\Models\Carrera::find($carrera_id);
                                                                $paralelo = isset($paralelosSeleccionados[$materiaId])
                                                                    ? \App\Models\Paralelo::find(
                                                                        $paralelosSeleccionados[$materiaId],
                                                                    )
                                                                    : null;
                                                                $costo = $materia->credits * $carrera->costo_credito;
                                                            @endphp
                                                            <tr>
                                                                <td class="px-4 py-2.5">
                                                                    <p class="text-xs text-white/75">{{ $materia->name }}
                                                                    </p>
                                                                    <p class="text-[0.65rem] text-slate-500">
                                                                        {{ $materia->code }}</p>
                                                                </td>
                                                                <td class="px-4 py-2.5 text-center text-xs text-slate-400">
                                                                    {{ $materia->credits }}</td>
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
                                                        <tr class="border-b border-white/[0.05]">
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
                                                    <tbody class="divide-y divide-white/[0.04]">
                                                        @foreach ($materiasArrastradasIncluidas as $materiaArrastrada)
                                                            @php
                                                                $materia = \App\Models\Materia::find(
                                                                    $materiaArrastrada['materia_id'],
                                                                );
                                                                $paralelo = isset(
                                                                    $paralelosSeleccionados[
                                                                        $materiaArrastrada['materia_id']
                                                                    ],
                                                                )
                                                                    ? \App\Models\Paralelo::find(
                                                                        $paralelosSeleccionados[
                                                                            $materiaArrastrada['materia_id']
                                                                        ],
                                                                    )
                                                                    : null;
                                                                $costo = $materiaArrastrada['costo_adicional'] ?? 0;
                                                            @endphp
                                                            <tr>
                                                                <td class="px-4 py-2.5">
                                                                    <p class="text-xs text-white/75">{{ $materia->name }}
                                                                    </p>
                                                                    <p class="text-[0.65rem] text-slate-500">
                                                                        {{ $materia->code }}</p>
                                                                </td>
                                                                <td class="px-4 py-2.5 text-center text-xs text-slate-400">
                                                                    {{ $materia->credits }}</td>
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
                                </div>

                                {{-- Panel costos --}}
                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-slate-800 border border-white/[0.06] rounded-xl overflow-hidden sticky top-0">
                                        <div class="px-4 py-2.5 border-b border-white/[0.05]">
                                            <h5
                                                class="text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-400">
                                                Resumen de Costos</h5>
                                        </div>
                                        <div class="p-4 space-y-4">

                                            {{-- Descuento --}}
                                            <div>
                                                <label
                                                    class="block text-[0.65rem] font-medium tracking-[0.12em] uppercase text-slate-400 mb-1.5">
                                                    Descuento ($)
                                                </label>
                                                <input type="number" wire:model.blur="descuento" min="0"
                                                    max="{{ $costoTotal }}" step="0.01" placeholder="0.00"
                                                    class="w-full px-3 py-2 rounded-lg text-sm text-white/80 bg-slate-900 border border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all @error('descuento') border-red-500/50 @enderror">
                                                @error('descuento')
                                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="h-px bg-white/[0.05]"></div>

                                            <div class="space-y-2.5">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-xs text-slate-400">Total Créditos</span>
                                                    <span
                                                        class="text-xs font-medium text-white/70">{{ $totalCreditos }}</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-xs text-slate-400">Costo Total</span>
                                                    <span
                                                        class="text-xs font-medium text-white/70">${{ number_format($costoTotal, 2) }}</span>
                                                </div>
                                                @if ($descuento > 0)
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-xs text-lime-400/70">Descuento</span>
                                                        <span
                                                            class="text-xs font-medium text-lime-400">-${{ number_format($descuento, 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="h-px bg-white/[0.05]"></div>

                                            <div class="flex justify-between items-center">
                                                <span class="text-xs font-medium text-white/60">Total a Pagar</span>
                                                <span
                                                    class="text-lg font-semibold text-lime-400">${{ number_format($totalPagar, 2) }}</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endif

                    </div>

                    {{-- Modal Footer --}}
                    <div
                        class="px-6 py-4 border-t border-white/[0.05] bg-black/15 flex items-center justify-between gap-3 flex-shrink-0">

                        {{-- Anterior --}}
                        <div>
                            @if ($paso > 1)
                                <button type="button" wire:click="pasoAnterior"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium
                                       text-slate-400 border border-white/[0.08] bg-transparent
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
                                class="px-4 py-2 rounded-full text-xs font-medium text-slate-400 border border-white/[0.08] hover:bg-white/5 hover:text-slate-200 transition-all duration-200">
                                Cancelar
                            </button>

                            @if ($paso < $totalPasos)
                                <button type="button" wire:click="siguientePaso"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-full text-xs font-medium tracking-wide uppercase
                                       text-white/90
                                       bg-gradient-to-r from-green-800/70 via-sky-800/60 to-purple-900/55
                                       border border-lime-500/25
                                       hover:from-green-700/80 hover:via-sky-700/70 hover:to-purple-800/65
                                       hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-900/30
                                       active:translate-y-0 transition-all duration-200">
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

    </div>

    @push('js')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('matricula-guardada', (event) => {
                    const data = event[0] ?? event;
                    if (data.redirigir && data.matricula_id) {
                        Swal.fire({
                            title: '¡Matrícula creada!',
                            text: data.mensaje,
                            icon: 'success',
                            confirmButtonText: 'Registrar Pago',
                            showCancelButton: true,
                            cancelButtonText: 'Ir a Obligaciones',
                            confirmButtonColor: '#2563eb',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href =
                                    `{{ url('administracion/administrativa/matriculas') }}/${data.matricula_id}/pago`;
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location.href =
                                    `{{ url('administracion/administrativa/obligaciones') }}`;
                            }
                        });
                    } else {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.mensaje,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#2563eb'
                        });
                    }
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
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') Livewire.dispatch('cerrarModal');
            });
        </script>
    @endpush
    {{-- <div>
    <style>
        .peer:checked+div {
            @apply border-blue-500 bg-blue-50;
        }

        .peer:disabled+div {
            @apply opacity-50 cursor-not-allowed;
        }

        * {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }

        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-center items-center mb-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-0">Gestión de Matrículas</h2>
                <p class="text-lg text-gray-600 font-semibold">Administrar matrículas de estudiantes</p>
            </div>
        </div>


        <div class="bg-gray-200 border border-gray-300 rounded-lg p-4 mb-4 shadow-lg">
            <div class="max-w-6xl mx-auto mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="w-full">
                        <label class="block text-center text-sm text-gray-700 font-semibold pl-4 mb-2">Buscar
                            Estudiante</label>
                        <input type="text" class="w-full rounded-full border border-gray-400 shadow-lg px-4 py-2"
                            wire:model.live="search" placeholder="Nombre, email, cédula o matrícula...">
                    </div>
                    <div class="w-full">
                        <label class="block text-center text-sm text-gray-700 font-semibold pl-4 mb-2">Período</label>
                        <select class="w-full rounded-full border border-gray-400 shadow-lg px-4 py-2"
                            wire:model.live="selectedPeriodo">
                            <option value="">Todos los períodos</option>
                            @foreach ($periodos as $periodo)
                                <option value="{{ $periodo->id }}">{{ $periodo->code }} - {{ $periodo->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full">
                        <label class="block text-center text-sm text-gray-700 font-semibold pl-4 mb-2">Carrera</label>
                        <select class="w-full rounded-full border border-gray-400 shadow-lg px-4 py-2"
                            wire:model.live="selectedCarrera">
                            <option value="">Todas las carreras</option>
                            @foreach ($carreras as $carrera)
                                <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>


        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gray-800 py-3 w-full">
                <h6 class="text-center font-bold text-white uppercase text-2xl">Estudiantes</h6>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Estudiante</th>
                            <th class="px-4 py-3 text-center">Cédula</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-center">Matrícula Actual</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estudiantes as $student)
                            <tr class="hover:bg-gray-100 transition duration-200 border-b text-sm font-medium">
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="bg-gray-200 rounded-full w-10 h-10 flex items-center justify-center text-gray-700 font-bold">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="font-semibold">{{ $student->name }}</h6>
                                            <small class="text-gray-500">{{ $student->matricula_numero }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ $student->cedula }}</td>
                                <td class="px-4 py-3">{{ $student->email }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $matriculaActual = $student->matriculas->first();
                                    @endphp
                                    @if ($matriculaActual)
                                        <div>
                                            <span class="font-bold">{{ $matriculaActual->code }}</span>
                                            <br>
                                            <small class="text-gray-500">{{ $matriculaActual->carrera->name }}</small>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center">
                                        @if ($matriculaActual)
                                            @switch($matriculaActual->estado)
                                                @case('Habilitada')
                                                    <span
                                                        class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Pagada</span>
                                                @break

                                                @case('Pendiente_Pago')
                                                    <span
                                                        class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Pendiente
                                                        Pago</span>
                                                @break

                                                @case('Borrador')
                                                    <span
                                                        class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">Borrador</span>
                                                @break

                                                @case('Cancelada')
                                                    <span
                                                        class="bg-gray-300 text-gray-800 px-3 py-1 rounded-full text-sm">Cancelada</span>
                                                @break

                                                @default
                                                    <span
                                                        class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ $matriculaActual->estado }}</span>
                                            @endswitch
                                        @else
                                            <span
                                                class="bg-purple-300 text-purple-800 px-3 py-1 rounded-full text-sm">Sin
                                                matrícula</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        @if ($matriculaActual)
                                            <button type="button"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm transition duration-200"
                                                wire:click="editarMatricula({{ $matriculaActual->id }})"
                                                title="Editar matrícula">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                    </path>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm transition duration-200"
                                                wire:click="iniciarMatricula({{ $student->id }})"
                                                title="Nueva matrícula">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8">
                                        <div class="text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <p class="text-lg">No se encontraron estudiantes</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 bg-gray-50">
                    {{ $estudiantes->links() }}
                </div>
            </div>


            @if ($showModal)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                    style="z-index:99999" aria-modal="true">

                    <div
                        class="flex items-center justify-center min-h-screen  mt-10 pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                        <span class="hidden sm:inline-block sm:align-middle h-screen" aria-hidden="true">&#8203;</span>


                        <div
                            class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">

                            <div class="bg-white px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        {{ $matriculaId ? 'Editar' : 'Nueva' }} Matrícula - {{ $estudiante->name }} -
                                        {{ $estudiante->id }}
                                    </h3>
                                    <button type="button"
                                        class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        wire:click="cerrarModal">
                                        <span class="sr-only">Cerrar</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div
                                class="bg-white px-4 py-4 sm:px-6 h-96 md:h-[300px] lg:h-[470px] max-h-96 md:max-h-[300px] lg:max-h-[470px] overflow-y-auto">

                                <div class="mb-6">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                            style="width: {{ ($paso / $totalPasos) * 100 }}%"></div>
                                    </div>
                                    <div class="flex justify-between mt-3">
                                        <div
                                            class="flex items-center space-x-1 {{ $paso >= 1 ? 'text-blue-600' : 'text-gray-400' }}">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Información</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1 {{ $paso >= 2 ? 'text-blue-600' : 'text-gray-400' }}">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Materias</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1 {{ $paso >= 3 ? 'text-blue-600' : 'text-gray-400' }}">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z">
                                                </path>
                                            </svg>
                                            <span class="text-sm font-medium">Paralelos</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1 {{ $paso >= 4 ? 'text-blue-600' : 'text-gray-400' }}">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Resumen</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($paso == 1)
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div>
                                            <div class="bg-gray-100 rounded-lg p-4 border border-gray-200">
                                                <h4 class="font-semibold text-gray-900 mb-3">Información del Estudiante
                                                </h4>
                                                <div class="space-y-2 text-sm">
                                                    <p><span class="font-medium">Nombre:</span> {{ $estudiante->name }}
                                                    </p>
                                                    <p><span class="font-medium">Email:</span> {{ $estudiante->email }}
                                                    </p>
                                                    <p><span class="font-medium">Cédula:</span> {{ $estudiante->cedula }}
                                                    </p>
                                                    <p><span class="font-medium">Celular:</span> {{ $estudiante->phone }}
                                                    </p>
                                                    <p><span class="font-medium">Fecha de nacimiento:</span>
                                                        {{ $estudiante->fecha_nacimiento }}
                                                        ({{ \Carbon\Carbon::parse($estudiante->fecha_nacimiento)->age }}
                                                        años)
                                                    </p>
                                                    <p><span class="font-medium">Matrícula:</span>
                                                        {{ $estudiante->matricula_numero }}</p>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Carrera <span class="text-red-500">*</span>
                                                </label>
                                                <select
                                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('carrera_id') border-red-500 @enderror"
                                                    wire:model="carrera_id">
                                                    <option value="">Seleccionar carrera</option>
                                                    @foreach ($carreras as $carrera)
                                                        <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('carrera_id')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Período Académico <span class="text-red-500">*</span>
                                                </label>
                                                <select
                                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('periodo_id') border-red-500 @enderror"
                                                    wire:model="periodo_id">
                                                    <option value="">Seleccionar período</option>
                                                    @foreach ($periodos as $periodo)
                                                        <option value="{{ $periodo->id }}">
                                                            {{ $periodo->code }} - {{ $periodo->description }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('periodo_id')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de
                                                    Matrícula</label>
                                                <select
                                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    wire:model="tipo">
                                                    <option value="Nueva">Nueva</option>
                                                    <option value="Renovacion">Renovación</option>
                                                    <option value="Arrastre">Arrastre</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                                <textarea class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    wire:model="observaciones" rows="3" placeholder="Observaciones adicionales..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    @if (!empty($materiasArrastradas))
                                        <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-yellow-800">Materias Arrastradas
                                                    </h3>
                                                    <div class="mt-2 text-sm text-yellow-700">
                                                        <p class="mb-2">Este estudiante tiene las siguientes materias
                                                            arrastradas:</p>
                                                        <div class="space-y-2">
                                                            @foreach ($materiasArrastradas as $index => $materiaArrastrada)
                                                                <div class="flex items-center">
                                                                    <input type="checkbox"
                                                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                                        wire:model="materiasArrastradas.{{ $index }}.incluir"
                                                                        id="arrastre_{{ $index }}">
                                                                    <label class="ml-2 text-sm"
                                                                        for="arrastre_{{ $index }}">
                                                                        <strong>{{ $materiaArrastrada['materia']['name'] }}</strong>
                                                                        - Nota obtenida:
                                                                        {{ $materiaArrastrada['nota_obtenida'] }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                @if ($paso == 2)
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Materias Disponibles</h4>
                                        @if (empty($materiasDisponibles))
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm text-blue-700">
                                                            No hay materias disponibles para inscribir en esta carrera y
                                                            período.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="space-y-4">
                                                @foreach ($materiasDisponibles as $semestreNombre => $materias)
                                                    <div class="bg-white border border-gray-200 rounded-lg">
                                                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                            <h5 class="text-md font-medium text-gray-900">
                                                                {{ $semestreNombre }}</h5>
                                                        </div>
                                                        <div class="p-4">
                                                            <div
                                                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                                @foreach ($materias as $materia)
                                                                    <div class="relative">
                                                                        <label class="cursor-pointer">
                                                                            <input type="checkbox" class="sr-only peer"
                                                                                value="{{ $materia['id'] }}"
                                                                                wire:model="materiasSeleccionadas"
                                                                                {{ !$materia['puede_inscribir'] ? 'disabled' : '' }}>
                                                                            <div
                                                                                class="border-2 rounded-lg p-3 transition-all duration-200 
                                                                            {{ !$materia['puede_inscribir'] ? 'opacity-50 cursor-not-allowed' : 'hover:border-blue-500' }}
                                                                            peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                                                                <h6
                                                                                    class="font-semibold text-gray-900 mb-1">
                                                                                    {{ $materia['name'] }}</h6>
                                                                                <p class="text-sm text-gray-600 mb-2">
                                                                                    {{ $materia['code'] }}</p>
                                                                                <div
                                                                                    class="flex justify-between items-center">
                                                                                    <span
                                                                                        class="text-xs text-gray-500">{{ $materia['credits'] }}
                                                                                        créditos</span>
                                                                                    <span
                                                                                        class="px-2 py-1 text-xs rounded-full 
                                                                                    {{ $materia['tipo'] == 'Obligatoria' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                                                        {{ $materia['tipo'] }}
                                                                                    </span>
                                                                                </div>
                                                                                @if (!$materia['puede_inscribir'] && !empty($materia['prerequisitos_faltantes']))
                                                                                    <div
                                                                                        class="mt-2 p-2 bg-red-50 rounded text-xs">
                                                                                        <p
                                                                                            class="text-red-700 font-medium">
                                                                                            Prerequisitos faltantes:</p>
                                                                                        <ul class="text-red-600 mt-1">
                                                                                            @foreach ($materia['prerequisitos_faltantes'] as $prerequisito)
                                                                                                <li>• {{ $prerequisito }}
                                                                                                </li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @error('materias')
                                            <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-4">
                                                <p class="text-sm text-red-700">{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                @endif

                                @if ($paso == 3)
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Selección de Paralelos</h4>
                                        @php
                                            $todasLasMaterias = collect($materiasSeleccionadas)
                                                ->merge(
                                                    collect($materiasArrastradas)
                                                        ->where('incluir', true)
                                                        ->pluck('materia_id'),
                                                )
                                                ->unique();
                                        @endphp

                                        <div class="space-y-4">
                                            @foreach ($todasLasMaterias as $materiaId)
                                                @php
                                                    $materia = \App\Models\Materia::find($materiaId);
                                                    $paralelos = isset($paralelosDisponibles[$materiaId])
                                                        ? $paralelosDisponibles[$materiaId]
                                                        : [];
                                                @endphp

                                                <div class="bg-white border border-gray-200 rounded-lg">
                                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                        <h5 class="text-md font-medium text-gray-900">{{ $materia->name }}
                                                            ({{ $materia->code }})
                                                        </h5>
                                                    </div>
                                                    <div class="p-4">
                                                        @if (empty($paralelos))
                                                            <div
                                                                class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                                                <p class="text-sm text-yellow-700">No hay paralelos
                                                                    disponibles para esta materia.</p>
                                                            </div>
                                                        @else
                                                            <div
                                                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                                @foreach ($paralelos as $paralelo)
                                                                    <div class="relative">
                                                                        <label class="cursor-pointer">
                                                                            <input type="radio" class="sr-only peer"
                                                                                name="paralelo_{{ $materiaId }}"
                                                                                value="{{ $paralelo['id'] }}"
                                                                                wire:model="paralelosSeleccionados.{{ $materiaId }}"
                                                                                {{ !$paralelo['tiene_cupo'] ? 'disabled' : '' }}>
                                                                            <div
                                                                                class="border-2 rounded-lg p-3 transition-all duration-200 
                                                                            {{ !$paralelo['tiene_cupo'] ? 'opacity-50 cursor-not-allowed' : 'hover:border-blue-500' }}
                                                                            peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                                                                <h6
                                                                                    class="font-semibold text-gray-900 mb-2">
                                                                                    {{ $paralelo['name'] }}</h6>
                                                                                <div
                                                                                    class="flex justify-between items-center">
                                                                                    <span
                                                                                        class="text-sm {{ $paralelo['tiene_cupo'] ? 'text-green-600' : 'text-red-600' }}">
                                                                                        Cupo:
                                                                                        {{ $paralelo['cupo_disponible'] }}
                                                                                        disponibles
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @error('paralelos')
                                            <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-4">
                                                <p class="text-sm text-red-700">{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                @endif

                                @if ($paso == 4)
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                        <div class="lg:col-span-2">
                                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Matrícula</h4>

                                            @if (!empty($materiasSeleccionadas))
                                                <div class="bg-white border border-gray-200 rounded-lg mb-4">
                                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                        <h5 class="text-md font-medium text-gray-900">Materias Regulares
                                                        </h5>
                                                    </div>
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                                        Materia</th>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                                        Código</th>
                                                                    <th
                                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                                        Créditos</th>
                                                                    <th
                                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                                        Paralelo</th>
                                                                    <th
                                                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                                        Costo</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                @foreach ($materiasSeleccionadas as $materiaId)
                                                                    @php
                                                                        $materia = \App\Models\Materia::find(
                                                                            $materiaId,
                                                                        );
                                                                        $carrera = \App\Models\Carrera::find(
                                                                            $carrera_id,
                                                                        );
                                                                        $paralelo = isset(
                                                                            $paralelosSeleccionados[$materiaId],
                                                                        )
                                                                            ? \App\Models\Paralelo::find(
                                                                                $paralelosSeleccionados[$materiaId],
                                                                            )
                                                                            : null;
                                                                        $costo =
                                                                            $materia->credits * $carrera->costo_credito;
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="px-4 py-3 text-sm text-gray-900">
                                                                            {{ $materia->name }}</td>
                                                                        <td class="px-4 py-3 text-sm text-gray-500">
                                                                            {{ $materia->code }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-center text-gray-900">
                                                                            {{ $materia->credits }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-center text-gray-500">
                                                                            {{ $paralelo ? $paralelo->name : 'No asignado' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-right font-medium text-gray-900">
                                                                            ${{ number_format($costo, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif

                                            @php
                                                $materiasArrastradasIncluidas = collect($materiasArrastradas)->where(
                                                    'incluir',
                                                    true,
                                                );
                                            @endphp
                                            @if ($materiasArrastradasIncluidas->count() > 0)
                                                <div class="bg-white border border-gray-200 rounded-lg mb-4">
                                                    <div class="bg-yellow-50 px-4 py-3 border-b border-gray-200">
                                                        <h5 class="text-md font-medium text-gray-900">Materias de Arrastre
                                                        </h5>
                                                    </div>
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                                        Materia</th>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                                        Código</th>
                                                                    <th
                                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                                        Créditos</th>
                                                                    <th
                                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                                        Paralelo</th>
                                                                    <th
                                                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                                        Costo</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                @foreach ($materiasArrastradasIncluidas as $materiaArrastrada)
                                                                    @php
                                                                        $materia = \App\Models\Materia::find(
                                                                            $materiaArrastrada['materia_id'],
                                                                        );
                                                                        $paralelo = isset(
                                                                            $paralelosSeleccionados[
                                                                                $materiaArrastrada['materia_id']
                                                                            ],
                                                                        )
                                                                            ? \App\Models\Paralelo::find(
                                                                                $paralelosSeleccionados[
                                                                                    $materiaArrastrada['materia_id']
                                                                                ],
                                                                            )
                                                                            : null;
                                                                        $costo =
                                                                            $materiaArrastrada['costo_adicional'] ?? 0;
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="px-4 py-3 text-sm text-gray-900">
                                                                            {{ $materia->name }}</td>
                                                                        <td class="px-4 py-3 text-sm text-gray-500">
                                                                            {{ $materia->code }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-center text-gray-900">
                                                                            {{ $materia->credits }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-center text-gray-500">
                                                                            {{ $paralelo ? $paralelo->name : 'No asignado' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-right font-medium text-gray-900">
                                                                            ${{ number_format($costo, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="lg:col-span-1">
                                            <div class="bg-white border border-gray-200 rounded-lg sticky top-4">
                                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                    <h5 class="text-md font-medium text-gray-900">Resumen de Costos</h5>
                                                </div>
                                                <div class="p-4">
                                                    <div class="mb-4">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 mb-2">Descuento
                                                            ($)</label>
                                                        <input type="number"
                                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('descuento') border-red-500 @enderror"
                                                            wire:model.blur="descuento" min="0"
                                                            max="{{ $costoTotal }}" step="0.01" placeholder="0.00">
                                                        @error('descuento')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <hr class="my-4">

                                                    <div class="space-y-3">
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-sm text-gray-600">Total Créditos:</span>
                                                            <span
                                                                class="font-medium text-gray-900">{{ $totalCreditos }}</span>
                                                        </div>
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-sm text-gray-600">Costo Total:</span>
                                                            <span
                                                                class="font-medium text-gray-900">${{ number_format($costoTotal, 2) }}</span>
                                                        </div>
                                                        @if ($descuento > 0)
                                                            <div class="flex justify-between items-center text-green-600">
                                                                <span class="text-sm">Descuento:</span>
                                                                <span
                                                                    class="font-medium">-${{ number_format($descuento, 2) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <hr class="my-4">

                                                    <div class="flex justify-between items-center">
                                                        <span class="text-lg font-semibold text-gray-900">Total a
                                                            Pagar:</span>
                                                        <span
                                                            class="text-xl font-bold text-blue-600">${{ number_format($totalPagar, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div>
                                        @if ($paso > 1)
                                            <button type="button"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                wire:click="pasoAnterior">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                                Anterior
                                            </button>
                                        @endif
                                    </div>

                                    <div class="flex space-x-3">
                                        <button type="button"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            wire:click="cerrarModal">
                                            Cancelar
                                        </button>

                                        @if ($paso < $totalPasos)
                                            <button type="button"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                wire:click="siguientePaso">
                                                Siguiente
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                wire:click="guardarMatricula">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                {{ $matriculaId ? 'Actualizar' : 'Guardar' }} Matrícula
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>

    @push('js')
        <script>
            document.addEventListener('livewire:init', () => {

                Livewire.on('matricula-guardada', (event) => {
                    const data = event[0] ?? event;

                    if (data.redirigir && data.matricula_id) {
                        Swal.fire({
                            title: '¡Matrícula creada!',
                            text: data.mensaje,
                            icon: 'success',
                            confirmButtonText: 'Registrar Pago',
                            showCancelButton: true,
                            cancelButtonText: 'Ir a Obligaciones',
                            confirmButtonColor: '#2563eb',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href =
                                    `{{ url('administracion/administrativa/matriculas') }}/${data.matricula_id}/pago`;
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location.href =
                                    `{{ url('administracion/administrativa/obligaciones') }}`;
                            }
                        });
                    } else {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.mensaje,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#2563eb',
                        });
                    }
                });

                Livewire.on('error', (event) => {
                    const data = event[0] ?? event;
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje,
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    Livewire.dispatch('cerrarModal');
                }
            });
        </script>
    @endpush
 --}}
