<div>
<div class="max-w-7xl mx-auto bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl p-4 mb-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Días no lectivos</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Feriados y suspensiones de clase por período</p>
        </div>
        <button wire:click="abrirCrear"
            class="inline-flex items-center justify-center gap-2 bg-lime-600 text-white px-5 py-2.5 rounded-xl
                   hover:bg-lime-700 transition font-medium shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Agregar día no lectivo
        </button>
    </div>

    {{-- Filtro período --}}
    <div class="mb-5 max-w-xs">
        <select wire:model.live="periodo_filtro"
            class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                   bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100
                   px-3 py-2.5 focus:border-lime-500 focus:ring-lime-500">
            <option value="">Todos los períodos</option>
            @foreach ($this->periodos as $p)
                <option value="{{ $p->id }}">{{ $p->code }} — {{ $p->description }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-900 dark:bg-black text-white text-xs uppercase tracking-wide">
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Nombre / Motivo</th>
                    <th class="px-4 py-3 text-center">Tipo</th>
                    <th class="px-4 py-3 text-center">Alcance</th>
                    <th class="px-4 py-3 text-left">Horario afectado</th>
                    <th class="px-4 py-3 text-left">Período</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($this->diasNoLectivos as $dia)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                            {{ $dia->fecha->format('d/m/Y') }}
                            <div class="text-xs text-gray-400 dark:text-gray-500 capitalize">
                                {{ $dia->fecha->locale('es')->dayName }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $dia->nombre }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($dia->tipo === 'feriado')
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                             bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                    🎉 Feriado
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                             bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                    🔴 Suspensión
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($dia->alcance === 'global')
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                             bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                    Institución
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                             bg-slate-100 text-slate-600 dark:bg-gray-700 dark:text-gray-300">
                                    Específico
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                            @if ($dia->horario)
                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $dia->horario->materia?->name }}
                                </span>
                                · {{ $dia->horario->paralelo?->name }}
                                · {{ $dia->horario->dia_semana }}
                                {{ \Carbon\Carbon::parse($dia->horario->hora_inicio)->format('H:i') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ $dia->periodo?->code }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="abrirEditar({{ $dia->id }})"
                                    class="text-xs px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-900/30
                                           text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition">
                                    Editar
                                </button>
                                <button wire:click="eliminar({{ $dia->id }})"
                                    wire:confirm="¿Eliminar este día no lectivo?"
                                    class="text-xs px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-900/30
                                           text-red-700 dark:text-red-300 hover:bg-red-100 transition">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                            No hay días no lectivos registrados para este período.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================================================
     MODAL CREAR / EDITAR
     ============================================================ --}}
<div x-data x-show="$wire.mostrarModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-on:keydown.escape.window="$wire.cerrarModal()">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarModal"></div>

        <div class="relative z-10 w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl">

            {{-- Header modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white">
                    {{ $editandoId ? 'Editar día no lectivo' : 'Agregar día no lectivo' }}
                </h3>
                <button wire:click="cerrarModal"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400
                           hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition text-lg">
                    ✕
                </button>
            </div>

            {{-- Cuerpo --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Período --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">
                        Período
                    </label>
                    <select wire:model.live="periodo_id"
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm
                               text-gray-900 dark:text-gray-100 focus:border-lime-500 focus:ring-lime-500">
                        <option value="">Seleccionar período...</option>
                        @foreach ($this->periodos as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} — {{ $p->description }}</option>
                        @endforeach
                    </select>
                    @error('periodo_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Fecha + Nombre en grid --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">
                            Fecha
                        </label>
                        <input type="date" wire:model="fecha"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm
                                   text-gray-900 dark:text-gray-100 focus:border-lime-500 focus:ring-lime-500">
                        @error('fecha') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">
                            Tipo
                        </label>
                        <select wire:model="tipo"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm
                                   text-gray-900 dark:text-gray-100 focus:border-lime-500 focus:ring-lime-500">
                            <option value="feriado">🎉 Feriado</option>
                            <option value="suspension">🔴 Suspensión</option>
                        </select>
                    </div>
                </div>

                {{-- Nombre/Motivo --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">
                        Nombre / Motivo
                    </label>
                    <input type="text" wire:model="nombre"
                        placeholder="Ej: Día de la Independencia, Docente enfermo..."
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm
                               text-gray-900 dark:text-gray-100 focus:border-lime-500 focus:ring-lime-500">
                    @error('nombre') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Alcance --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2 uppercase tracking-wide">
                        Alcance
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition
                            {{ $alcance === 'global'
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="alcance" value="global" class="accent-indigo-600">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Institución completa</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Cancela todas las clases del día</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition
                            {{ $alcance === 'horario'
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="alcance" value="horario" class="accent-indigo-600">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Horario específico</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Solo una materia/paralelo</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Selector de horario específico (condicional) --}}
                @if ($alcance === 'horario')
                    <div class="space-y-3 p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            Seleccionar horario
                        </p>

                        {{-- Materia --}}
                        <select wire:model.live="filtro_materia"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-900 px-3 py-2 text-sm
                                   text-gray-900 dark:text-gray-100 focus:border-lime-500 focus:ring-lime-500
                                   disabled:opacity-40 disabled:cursor-not-allowed"
                            @disabled(!$periodo_id)>
                            <option value="">Seleccionar materia...</option>
                            @foreach ($this->materiasDisponibles as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>

                        {{-- Paralelo --}}
                        <select wire:model.live="filtro_paralelo"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-900 px-3 py-2 text-sm
                                   text-gray-900 dark:text-gray-100 focus:border-lime-500 focus:ring-lime-500
                                   disabled:opacity-40 disabled:cursor-not-allowed"
                            @disabled(!$filtro_materia)>
                            <option value="">Seleccionar paralelo...</option>
                            @foreach ($this->paralelosDisponibles as $par)
                                <option value="{{ $par->id }}">{{ $par->name }} ({{ $par->code }})</option>
                            @endforeach
                        </select>

                        {{-- Horario --}}
                        <select wire:model="horario_id"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-900 px-3 py-2 text-sm
                                   text-gray-900 dark:text-gray-100 focus:border-lime-500 focus:ring-lime-500
                                   disabled:opacity-40 disabled:cursor-not-allowed"
                            @disabled(!$filtro_paralelo)>
                            <option value="">Seleccionar horario...</option>
                            @foreach ($this->horariosDisponibles as $h)
                                <option value="{{ $h->id }}">
                                    {{ $h->dia_semana }}
                                    · {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                                    – {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                                </option>
                            @endforeach
                        </select>
                        @error('horario_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button wire:click="cerrarModal"
                    class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300
                           hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    Cancelar
                </button>
                <button wire:click="guardar" wire:loading.attr="disabled"
                    class="px-5 py-2 rounded-xl text-sm font-bold text-white
                           bg-lime-600 hover:bg-lime-700 transition shadow-sm
                           disabled:opacity-60">
                    <span wire:loading.remove>{{ $editandoId ? 'Actualizar' : 'Guardar' }}</span>
                    <span wire:loading>Guardando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
</div>
