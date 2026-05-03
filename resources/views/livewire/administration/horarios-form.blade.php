<div class="max-w-4xl mx-auto space-y-4 p-4 sm:p-4">

    {{-- Header --}}
    <div
        class="flex flex-col md:flex-row md:items-center md:justify-between gap-4
                bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $horarioId ? 'Editar Horario' : 'Crear Horario' }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Gestión académica · asignación de horarios por asignación docente
            </p>
        </div>
        <a href="{{ route('administracion.administrativa.horarios.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                  bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300
                  hover:bg-gray-200 dark:hover:bg-gray-700 transition text-sm font-medium">
            ← Volver al listado
        </a>
    </div>

    {{-- Errors general --}}
    @if ($errors->has('conflicto'))
        <div
            class="flex items-start gap-3 rounded-2xl border border-amber-200 dark:border-amber-800
                    bg-amber-50 dark:bg-amber-950/40 px-4 py-3 text-sm text-amber-700 dark:text-amber-300">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
            <span>{{ $errors->first('conflicto') }}</span>
        </div>
    @endif

    {{-- Conflict warning (real-time) --}}
    @if ($conflicto && !$errors->has('conflicto'))
        <div
            class="flex items-start gap-3 rounded-2xl border border-amber-200 dark:border-amber-800
                    bg-amber-50 dark:bg-amber-950/40 px-4 py-3 text-sm text-amber-700 dark:text-amber-300">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
            <span><strong>Conflicto detectado:</strong> {{ $conflicto }}</span>
        </div>
    @endif

    {{-- STEP 1: Período --}}
    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-center gap-3 mb-1">
            <span
                class="flex items-center justify-center w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-bold shrink-0">1</span>
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Seleccionar Período</h2>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Período académico</label>
            <select wire:model.live="periodoId"
                class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                           bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                           text-gray-900 dark:text-gray-100
                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                <option value="">— Selecciona un período —</option>
                @foreach ($periodos as $p)
                    <option value="{{ $p->id }}">{{ $p->code }} — {{ $p->description }}</option>
                @endforeach
            </select>
            @error('periodoId')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- STEP 2: Asignación Docente (combobox) --}}
    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-center gap-3 mb-1">
            <span
                class="flex items-center justify-center w-7 h-7 rounded-full
                         {{ $periodoId ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-700' }}
                         text-white text-xs font-bold shrink-0">2</span>
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Seleccionar Asignación Docente</h2>
        </div>

        @if (!$periodoId)
            <p class="text-sm text-gray-400 dark:text-gray-500 italic">Primero selecciona un período.</p>
        @else
            {{-- Alpine combobox --}}
            <div wire:key="asignacion-combo-{{ $periodoId }}" x-data="{
                open: false,
                query: {{ json_encode(
                    $asignacionActual
                        ? ($asignacionActual->docente->name ?? '?') .
                            ' — ' .
                            ($asignacionActual->materia->name ?? '?') .
                            ' — ' .
                            ($asignacionActual->paralelo->name ?? '?')
                        : '',
                ) }},
                selectedId: {{ $asignacionId ?? 'null' }},
                items: {{ $asignaciones->map(
                        fn($a) => [
                            'id' => $a->id,
                            'label' =>
                                ($a->docente->name ?? '?') . ' — ' . ($a->materia->name ?? '?') . ' — ' . ($a->paralelo->name ?? '?'),
                        ],
                    )->toJson() }},
                get filtered() {
                    if (!this.query) return this.items;
                    const q = this.query.toLowerCase();
                    return this.items.filter(i => i.label.toLowerCase().includes(q));
                },
                select(item) {
                    this.selectedId = item.id;
                    this.query = item.label;
                    this.open = false;
                    $wire.seleccionarAsignacion(item.id);
                },
                clear() {
                    this.selectedId = null;
                    this.query = '';
                    $wire.seleccionarAsignacion(null);
                }
            }"
                x-on:click.outside="open = false" class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Docente — Materia — Paralelo
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                        </svg>
                    </div>
                    <input type="text" x-model="query" x-on:focus="open = true" x-on:input="open = true"
                        placeholder="Buscar por docente, materia o paralelo…"
                        class="w-full pl-9 pr-9 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100
                               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                        autocomplete="off">
                    <button type="button" x-show="query" x-on:click="clear()"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Dropdown --}}
                <div x-show="open && filtered.length > 0" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-30 mt-1 w-full max-h-64 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700
                           bg-white dark:bg-gray-900 shadow-xl"
                    style="display:none">
                    <template x-for="item in filtered" :key="item.id">
                        <button type="button" x-on:click="select(item)"
                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 dark:hover:bg-indigo-900/30
                                   text-gray-800 dark:text-gray-200 transition"
                            :class="{ 'bg-indigo-50 dark:bg-indigo-900/30 font-semibold': selectedId === item.id }"
                            x-text="item.label"></button>
                    </template>
                </div>

                <div x-show="open && filtered.length === 0 && query.length > 0"
                    class="absolute z-30 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-900 shadow-xl px-4 py-3 text-sm text-gray-400 dark:text-gray-500"
                    style="display:none">
                    Sin resultados para "<span x-text="query"></span>".
                </div>
            </div>

            @error('asignacionId')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            {{-- Info panel for selected asignacion --}}
            @if ($asignacionActual)
                <div
                    class="mt-3 rounded-xl border border-indigo-200 dark:border-indigo-800
                            bg-indigo-50 dark:bg-indigo-950/40 p-4">
                    <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide mb-2">
                        Asignación seleccionada
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Docente</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $asignacionActual->docente->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Materia</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $asignacionActual->materia->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Paralelo</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $asignacionActual->paralelo->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Período</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $asignacionActual->periodo->code ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- STEP 3: Schedule details --}}
    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 space-y-5">
        <div class="flex items-center gap-3 mb-1">
            <span
                class="flex items-center justify-center w-7 h-7 rounded-full
                         {{ $asignacionId ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-700' }}
                         text-white text-xs font-bold shrink-0">3</span>
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Detalles del Horario</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Día --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Día de la
                    semana</label>
                <select wire:model.live="diaSemana" {{ !$asignacionId ? 'disabled' : '' }}
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                               text-gray-900 dark:text-gray-100
                               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    @foreach ($dias as $dia)
                        <option value="{{ $dia }}">{{ $dia }}</option>
                    @endforeach
                </select>
                @error('diaSemana')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Modalidad --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Modalidad de
                    clase</label>
                <select wire:model.live="modalidadClase" {{ !$asignacionId ? 'disabled' : '' }}
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                               text-gray-900 dark:text-gray-100
                               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    @foreach ($modalidades as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
                @error('modalidadClase')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hora inicio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Hora de inicio</label>
                <input type="time" wire:model.blur="horaInicio" {{ !$asignacionId ? 'disabled' : '' }}
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                              text-gray-900 dark:text-gray-100
                              focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition
                              disabled:opacity-50 disabled:cursor-not-allowed">
                @error('horaInicio')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hora fin --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Hora de fin</label>
                <input type="time" wire:model.blur="horaFin" {{ !$asignacionId ? 'disabled' : '' }}
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                              text-gray-900 dark:text-gray-100
                              focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition
                              disabled:opacity-50 disabled:cursor-not-allowed">
                @error('horaFin')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Aula --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Aula <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <input type="text" wire:model.blur="aula" {{ !$asignacionId ? 'disabled' : '' }}
                    placeholder="Ej. Aula 102, Lab. Cómputo…"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                              text-gray-900 dark:text-gray-100
                              focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition
                              disabled:opacity-50 disabled:cursor-not-allowed">
                @error('aula')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <button wire:click="guardar" wire:loading.attr="disabled" wire:target="guardar"
            class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                   px-6 py-2.5 rounded-xl transition font-semibold shadow-sm disabled:opacity-60">
            <span wire:loading.remove wire:target="guardar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </span>
            <span wire:loading wire:target="guardar">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                    </path>
                </svg>
            </span>
            <span wire:loading.remove
                wire:target="guardar">{{ $horarioId ? 'Actualizar Horario' : 'Guardar Horario' }}</span>
            <span wire:loading wire:target="guardar">Guardando…</span>
        </button>

        <a href="{{ route('administracion.administrativa.horarios.index') }}"
            class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700
                  dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200
                  px-6 py-2.5 rounded-xl transition font-semibold">
            Cancelar
        </a>
    </div>

</div>
