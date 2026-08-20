<div>
    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Cohortes de Admisión</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Gestiona los grupos de admisión por carrera y periodo</p>
        </div>
        @can('gestionar_cohortes')
        <button wire:click="abrirCrear"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-lime-600 hover:bg-lime-700 text-white text-sm font-medium transition-colors duration-150 shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nueva cohorte
        </button>
        @endcan
    </div>

    {{-- ══ FILTROS ═════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 mb-5
                flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="buscar" type="search"
                   placeholder="Buscar por nombre…"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900
                          text-slate-800 dark:text-slate-200 placeholder-slate-400 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
        </div>
        <select wire:model.live="filtroCarrera"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900
                       text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
            <option value="">Todas las carreras</option>
            @foreach($carreras as $carrera)
                <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtroEstado"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900
                       text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
            <option value="">Todos los estados</option>
            <option value="abierto">Abierto</option>
            <option value="cerrado">Cerrado</option>
        </select>
    </div>

    {{-- ══ TABLA ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        @if($cohortes->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-slate-400 dark:text-slate-600">
                <svg class="w-12 h-12 mb-3 opacity-40" fill="currentColor" viewBox="0 0 640 512">
                    <path d="M72 88a56 56 0 1 1 112 0A56 56 0 1 1 72 88z"/>
                </svg>
                <p class="text-sm font-medium">No se encontraron cohortes</p>
                <p class="text-xs mt-1">Crea la primera cohorte para comenzar</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Nombre</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Inicio Matrícula</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Inicio Clases</th>
                            <th class="text-center px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Aspirantes</th>
                            <th class="text-center px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Estado</th>
                            <th class="text-right px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($cohortes as $cohorte)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $cohorte->nombre }}</span>
                                @if($cohorte->descripcion)
                                    <p class="text-xs text-slate-400 mt-0.5 truncate max-w-[200px]">{{ $cohorte->descripcion }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $cohorte->fecha_inicio_matriculacion?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $cohorte->fecha_inicio_clases?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('administracion.administrativa.aspirantes.index') }}?cohorte={{ $cohorte->id }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold
                                          bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300
                                          hover:bg-indigo-200 dark:hover:bg-indigo-800/60 transition-colors">
                                    {{ $cohorte->aspirantes_count }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @can('gestionar_cohortes')
                                <button wire:click="toggleEstado({{ $cohorte->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition-colors
                                               {{ $cohorte->estado === 'abierto'
                                                  ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-200'
                                                  : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $cohorte->estado === 'abierto' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ ucfirst($cohorte->estado) }}
                                </button>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                             {{ $cohorte->estado === 'abierto'
                                                ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300'
                                                : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $cohorte->estado === 'abierto' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ ucfirst($cohorte->estado) }}
                                </span>
                                @endcan
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('administracion.administrativa.aspirantes.index') }}?cohorte={{ $cohorte->id }}"
                                       title="Ver aspirantes"
                                       class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </a>
                                    @can('gestionar_cohortes')
                                    <button wire:click="editar({{ $cohorte->id }})" title="Editar"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-lime-600 hover:bg-lime-50 dark:hover:bg-lime-900/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                        </svg>
                                    </button>
                                    <button wire:click="eliminar({{ $cohorte->id }})"
                                            wire:confirm="¿Eliminar esta cohorte? Solo se puede eliminar si no tiene aspirantes."
                                            title="Eliminar"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ══ MODAL CREAR / EDITAR ════════════════════════════════════════════ --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-data x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg"
             @click.outside="$wire.set('showModal', false)">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-base font-semibold text-slate-800 dark:text-white">
                    {{ $isEditing ? 'Editar cohorte' : 'Nueva cohorte' }}
                </h2>
                <button wire:click="$set('showModal', false)"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                {{-- Nombre --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input wire:model="nombre" type="text" placeholder="Ej. Cohorte Marzo 2027"
                           class="w-full rounded-xl border text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40
                                  {{ $errors->has('nombre') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                    @error('nombre') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Carrera --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Carrera <span class="text-red-500">*</span></label>
                    <select wire:model="carrera_id"
                            class="w-full rounded-xl border text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40
                                   {{ $errors->has('carrera_id') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                        <option value="">Selecciona una carrera</option>
                        @foreach($carreras as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('carrera_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Fechas --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Inicio matrícula</label>
                        <input wire:model="fecha_inicio_matriculacion" type="date"
                               class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                      text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Inicio de clases</label>
                        <input wire:model="fecha_inicio_clases" type="date"
                               class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                      text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
                    </div>
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Estado</label>
                    <select wire:model="estado"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                   text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
                        <option value="abierto">Abierto</option>
                        <option value="cerrado">Cerrado</option>
                    </select>
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Descripción (opcional)</label>
                    <textarea wire:model="descripcion" rows="2" placeholder="Notas o descripción de la cohorte…"
                              class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                     text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40 resize-none"></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Cancelar
                </button>
                <button wire:click="guardar" wire:loading.attr="disabled"
                        class="px-5 py-2 rounded-xl bg-lime-600 hover:bg-lime-700 disabled:opacity-60 text-white text-sm font-medium transition-colors shadow-sm">
                    <span wire:loading.remove wire:target="guardar">{{ $isEditing ? 'Guardar cambios' : 'Crear cohorte' }}</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
