<div class="max-w-4xl mx-auto space-y-4 p-4 sm:p-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4
                bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $mppId ? 'Editar Módulo' : 'Crear Módulo' }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Gestión académica · asociación materia – período – paralelo
            </p>
        </div>
        <a href="{{ route('administracion.administrativa.materia_periodo_paralelo.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                   bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300
                   hover:bg-gray-200 dark:hover:bg-gray-700 transition text-sm font-medium">
            ← Volver al listado
        </a>
    </div>

    {{-- STEP 1: Período --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-center gap-3 mb-1">
            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-bold shrink-0">1</span>
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

            {{-- Period date hint --}}
            @if ($periodoCurrent)
                <div class="mt-2 flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>
                        Rango del período:
                        <strong>{{ $periodoCurrent->fecha_inicio->format('d/m/Y') }}</strong>
                        al
                        <strong>{{ $periodoCurrent->fecha_fin->format('d/m/Y') }}</strong>
                        — el módulo debe estar dentro de este rango.
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- STEP 2: Materia (combobox) --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-center gap-3 mb-1">
            <span class="flex items-center justify-center w-7 h-7 rounded-full
                         {{ $periodoId ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-700' }}
                         text-white text-xs font-bold shrink-0">2</span>
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Seleccionar Materia</h2>
        </div>

        @if (!$periodoId)
            <p class="text-sm text-gray-400 dark:text-gray-500 italic">Primero selecciona un período.</p>
        @else
            @php
                $comboItems = $materias->map(fn ($m) => [
                    'id'    => $m->id,
                    'label' => ($m->semestre->carrera->name ?? '?') . ' › ' . ($m->semestre->name ?? '?') . ' · ' . $m->name,
                ])->values();
                $comboInit = $materiaActual
                    ? ($materiaActual->semestre->carrera->name ?? '?') . ' › ' . ($materiaActual->semestre->name ?? '?') . ' · ' . $materiaActual->name
                    : '';
            @endphp

            <div
                wire:key="materia-combo-{{ $periodoId }}"
                x-data="{
                    open: false,
                    query: {{ json_encode($comboInit) }},
                    selectedId: {{ $materiaId ?? 'null' }},
                    items: {{ $comboItems->toJson() }},
                    get filtered() {
                        if (!this.query) return this.items;
                        const q = this.query.toLowerCase();
                        return this.items.filter(i => i.label.toLowerCase().includes(q));
                    },
                    select(item) {
                        this.selectedId = item.id;
                        this.query = item.label;
                        this.open = false;
                        $wire.seleccionarMateria(item.id);
                    },
                    clear() {
                        this.selectedId = null;
                        this.query = '';
                        $wire.seleccionarMateria(null);
                    }
                }"
                x-on:click.outside="open = false"
                class="relative"
            >
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Carrera › Semestre · Materia
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        x-model="query"
                        x-on:focus="open = true"
                        x-on:input="open = true"
                        placeholder="Buscar por carrera, semestre o materia…"
                        class="w-full pl-9 pr-9 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100
                               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                        autocomplete="off"
                    >
                    <button type="button" x-show="query" x-on:click="clear()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div
                    x-show="open && filtered.length > 0"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-30 mt-1 w-full max-h-64 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700
                           bg-white dark:bg-gray-900 shadow-xl"
                    style="display:none"
                >
                    <template x-for="item in filtered" :key="item.id">
                        <button
                            type="button"
                            x-on:click="select(item)"
                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 dark:hover:bg-indigo-900/30
                                   text-gray-800 dark:text-gray-200 transition"
                            :class="{ 'bg-indigo-50 dark:bg-indigo-900/30 font-semibold': selectedId === item.id }"
                            x-text="item.label"
                        ></button>
                    </template>
                </div>

                <div x-show="open && filtered.length === 0 && query.length > 0"
                     class="absolute z-30 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-900 shadow-xl px-4 py-3 text-sm text-gray-400 dark:text-gray-500"
                     style="display:none">
                    Sin resultados para "<span x-text="query"></span>".
                </div>
            </div>

            @error('materiaId')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            @if ($materiaActual)
                <div class="mt-3 rounded-xl border border-indigo-200 dark:border-indigo-800
                            bg-indigo-50 dark:bg-indigo-950/40 p-4">
                    <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide mb-2">
                        Materia seleccionada
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Carrera</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $materiaActual->semestre->carrera->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Semestre</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $materiaActual->semestre->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Materia</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $materiaActual->name }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- STEP 3: Paralelo --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-center gap-3 mb-1">
            <span class="flex items-center justify-center w-7 h-7 rounded-full
                         {{ $materiaId ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-700' }}
                         text-white text-xs font-bold shrink-0">3</span>
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Seleccionar Paralelo</h2>
        </div>

        @if (!$materiaId)
            <p class="text-sm text-gray-400 dark:text-gray-500 italic">Primero selecciona una materia.</p>
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Paralelo</label>
                <select wire:model.live="paraleloId"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                           bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                           text-gray-900 dark:text-gray-100
                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                    <option value="">— Selecciona un paralelo —</option>
                    @foreach ($paralelos as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (cupo: {{ $p->cupo_maximo }})</option>
                    @endforeach
                </select>
                @error('paraleloId')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            @if ($paraleloId)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Cupo máximo de esta sección
                </label>
                <input type="number"
                       wire:model.live="cupoMaximo"
                       min="1" max="500"
                       class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                              text-gray-900 dark:text-gray-100
                              focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                @error('cupoMaximo')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                    Pre-llenado con el cupo del paralelo. Ajústalo si esta sección necesita un límite distinto.
                </p>
            </div>
            @endif
        @endif
    </div>

    {{-- STEP 4: Fechas del módulo --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-center gap-3 mb-1">
            <span class="flex items-center justify-center w-7 h-7 rounded-full
                         {{ $paraleloId ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-700' }}
                         text-white text-xs font-bold shrink-0">4</span>
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Fechas del Módulo</h2>
        </div>

        @if ($periodoCurrent)
            <p class="text-xs text-gray-500 dark:text-gray-400">
                El módulo puede durar menos que el período. Rango permitido:
                <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                    {{ $periodoCurrent->fecha_inicio->format('d/m/Y') }} — {{ $periodoCurrent->fecha_fin->format('d/m/Y') }}
                </span>
            </p>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha de inicio</label>
                <input type="date"
                       wire:model.blur="fechaInicio"
                       {{ !$paraleloId ? 'disabled' : '' }}
                       @if ($periodoCurrent)
                           min="{{ $periodoCurrent->fecha_inicio->format('Y-m-d') }}"
                           max="{{ $periodoCurrent->fecha_fin->format('Y-m-d') }}"
                       @endif
                       class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                              text-gray-900 dark:text-gray-100
                              focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition
                              disabled:opacity-50 disabled:cursor-not-allowed">
                @error('fechaInicio')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha de fin</label>
                <input type="date"
                       wire:model.blur="fechaFin"
                       {{ !$paraleloId ? 'disabled' : '' }}
                       @if ($periodoCurrent)
                           min="{{ $periodoCurrent->fecha_inicio->format('Y-m-d') }}"
                           max="{{ $periodoCurrent->fecha_fin->format('Y-m-d') }}"
                       @endif
                       class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                              text-gray-900 dark:text-gray-100
                              focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition
                              disabled:opacity-50 disabled:cursor-not-allowed">
                @error('fechaFin')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Estado --}}
        <div class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700
                    bg-gray-50 dark:bg-gray-800/50 p-4 mt-2">
            <div class="pt-0.5">
                <input type="checkbox" wire:model.live="isActive"
                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            </div>
            <div>
                <label class="font-semibold text-gray-800 dark:text-gray-100">Módulo activo</label>
                <p class="text-sm text-gray-500 dark:text-gray-400">Habilitado para uso académico y asignación de docentes.</p>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <button
            wire:click="guardar"
            wire:loading.attr="disabled"
            wire:target="guardar"
            class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                   px-6 py-2.5 rounded-xl transition font-semibold shadow-sm disabled:opacity-60">
            <span wire:loading.remove wire:target="guardar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            <span wire:loading wire:target="guardar">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </span>
            <span wire:loading.remove wire:target="guardar">{{ $mppId ? 'Actualizar Módulo' : 'Guardar Módulo' }}</span>
            <span wire:loading wire:target="guardar">Guardando…</span>
        </button>

        <a href="{{ route('administracion.administrativa.materia_periodo_paralelo.index') }}"
           class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700
                  dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200
                  px-6 py-2.5 rounded-xl transition font-semibold">
            Cancelar
        </a>
    </div>

</div>
