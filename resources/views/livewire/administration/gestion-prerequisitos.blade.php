<div class="mt-8 bg-white dark:bg-gray-900 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 p-5 sm:p-6">

    <div class="mb-5">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Prerequisitos</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Define qué materias debe haber aprobado el estudiante antes de inscribir esta.
        </p>
    </div>

    {{-- Agregar prerequisito --}}
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-5">
        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Agregar prerequisito</p>

        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Búsqueda --}}
            <div class="flex-1">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="busqueda"
                    placeholder="Buscar materia por nombre o código..."
                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm text-sm focus:ring-lime-500 focus:border-lime-500"
                />
            </div>

            {{-- Select de resultado --}}
            <div class="flex-1">
                <select
                    wire:model="prerequisitoSeleccionado"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm text-sm focus:ring-lime-500 focus:border-lime-500"
                >
                    <option value="">— Seleccionar materia —</option>
                    @foreach ($materiasDisponibles as $m)
                        <option value="{{ $m['id'] }}">
                            [{{ $m['semestre'] }}] {{ $m['name'] }} ({{ $m['code'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Toggle obligatorio --}}
            <div class="flex items-center gap-2 shrink-0">
                <label class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                    <input type="checkbox" wire:model="esObligatorio"
                        class="rounded border-gray-300 text-lime-600 focus:ring-lime-500 mr-1" />
                    Obligatorio
                </label>
            </div>

            {{-- Botón agregar --}}
            <button
                wire:click="agregar"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-2 bg-lime-600 hover:bg-lime-700 disabled:opacity-50 text-white px-4 py-2 rounded-xl transition font-semibold text-sm shadow-sm shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Agregar
            </button>
        </div>

        @if (empty($materiasDisponibles) && strlen($busqueda) > 0)
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                No se encontraron materias con "{{ $busqueda }}".
            </p>
        @endif
    </div>

    {{-- Listado de prerequisitos actuales --}}
    @if (empty($prerequisitos))
        <div class="text-center py-8 text-gray-400 dark:text-gray-600">
            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <p class="text-sm">Esta materia no tiene prerequisitos configurados.</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400">Materia</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400">Código</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400">Semestre</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">Obligatorio</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($prerequisitos as $prereq)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">
                                {{ $prereq['name'] }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                {{ $prereq['code'] }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                {{ $prereq['semestre'] }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    wire:click="toggleObligatorio({{ $prereq['id'] }})"
                                    title="{{ $prereq['es_obligatorio'] ? 'Marcar como opcional' : 'Marcar como obligatorio' }}"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition
                                        {{ $prereq['es_obligatorio']
                                            ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400'
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300' }}"
                                >
                                    @if ($prereq['es_obligatorio'])
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Obligatorio
                                    @else
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        Opcional
                                    @endif
                                </button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    wire:click="eliminar({{ $prereq['id'] }})"
                                    wire:confirm="¿Eliminar este prerequisito?"
                                    class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 dark:hover:text-red-400 text-xs font-medium transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
