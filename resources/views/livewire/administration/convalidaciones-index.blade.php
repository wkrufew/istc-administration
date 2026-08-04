<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ══ CABECERA ══ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Validación de Conocimientos
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Registro de materias aprobadas por examen de validación institucional
            </p>
        </div>
        <button type="button"
                wire:click="abrirModal"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700
                       text-white text-sm font-semibold transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva convalidación
        </button>
    </div>

    {{-- ══ STATS ══ --}}
    @php
        $total       = $this->stats['total'];
        $confirmadas = $this->stats['confirmadas'];
        $borradores  = $this->stats['borradores'];
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $total }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total registros</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $confirmadas }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Confirmadas</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-500 dark:text-amber-400">{{ $borradores }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Borradores</p>
        </div>
    </div>

    {{-- ══ FILTROS ══ --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <select wire:model.live="filtroCarrera"
                class="rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100
                       shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
            <option value="">Todas las carreras</option>
            @foreach ($this->carreras as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtroEstado"
                class="rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100
                       shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
            <option value="">Todos los estados</option>
            <option value="Confirmada">Confirmada</option>
            <option value="Borrador">Borrador</option>
        </select>
    </div>

    {{-- ══ LISTADO ══ --}}
    @if ($this->convalidaciones->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700
                    shadow-sm p-14 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="font-semibold text-gray-500 dark:text-gray-400">No hay convalidaciones registradas</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                Usa el botón "Nueva convalidación" para registrar la primera
            </p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-750 border-b border-gray-100 dark:border-gray-700">
                        <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide px-5 py-3">
                            Estudiante
                        </th>
                        <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide px-4 py-3 hidden sm:table-cell">
                            Carrera
                        </th>
                        <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide px-4 py-3 hidden md:table-cell">
                            Período
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide px-4 py-3">
                            Materias
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide px-4 py-3">
                            Estado
                        </th>
                        <th class="text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide px-5 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @foreach ($this->convalidaciones as $conv)
                        @php
                            $aprobadas  = $conv->detalles->where('estado','Aprobado')->count();
                            $reprobadas = $conv->detalles->where('estado','Reprobado')->count();
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                            {{-- Estudiante --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center
                                                text-green-700 dark:text-green-300 font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($conv->estudiante->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 leading-tight">
                                            {{ $conv->estudiante->name ?? '—' }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            C.C.: {{ $conv->estudiante->cedula ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            {{-- Carrera --}}
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <p class="text-gray-700 dark:text-gray-300 text-xs leading-tight">
                                    {{ $conv->carrera->name ?? '—' }}
                                </p>
                            </td>
                            {{-- Período --}}
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $conv->periodo->code ?? '—' }}
                                </span>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                    {{ $conv->created_at->format('d/m/Y') }}
                                </p>
                            </td>
                            {{-- Materias --}}
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md
                                                 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                                                 text-xs font-semibold">
                                        ✓ {{ $aprobadas }}
                                    </span>
                                    @if ($reprobadas > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md
                                                     bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400
                                                     text-xs font-semibold">
                                            ✗ {{ $reprobadas }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            {{-- Estado --}}
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold
                                    {{ $conv->estado === 'Confirmada'
                                        ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                                        : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                    {{ $conv->estado }}
                                </span>
                            </td>
                            {{-- Acciones --}}
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('administracion.administrativa.convalidacion.conocimiento', $conv->user_id) }}"
                                   wire:navigate
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                          border border-gray-200 dark:border-gray-600 text-xs font-medium
                                          text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800
                                          hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                {{ $this->convalidaciones->links() }}
            </div>
        </div>
    @endif

    {{-- ══ MODAL BÚSQUEDA PREDICTIVA ══ --}}
    @if ($modalAbierto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data
             x-init="$el.querySelector('input')?.focus()">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm"
                 wire:click="cerrarModal"></div>

            {{-- Panel --}}
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                        border border-gray-100 dark:border-gray-700 overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            Seleccionar estudiante
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Busca por nombre, cédula o correo
                        </p>
                    </div>
                    <button type="button" wire:click="cerrarModal"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300
                                   hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Buscador --}}
                <div class="px-5 pt-4 pb-3">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               wire:model.live.debounce.300ms="busqueda"
                               placeholder="Ej: Juan Pérez, 0601234567..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-600
                                      dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-500
                                      focus:border-green-500 focus:ring-green-500 text-sm">
                        <div wire:loading wire:target="busqueda"
                             class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Resultados --}}
                <div class="px-3 pb-4 max-h-80 overflow-y-auto space-y-1">

                    @if (strlen(trim($busqueda)) < 2)
                        <div class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                            Escribe al menos 2 caracteres para buscar
                        </div>

                    @elseif ($this->resultadosBusqueda->isEmpty())
                        <div class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                            No se encontraron estudiantes con "{{ $busqueda }}"
                        </div>

                    @else
                        @foreach ($this->resultadosBusqueda as $est)
                            @php
                                $matriculaActiva    = $est->matriculas->first();
                                $tieneConvalidacion = $est->convalidaciones->isNotEmpty();
                            @endphp
                            <button type="button"
                                    wire:click="irAConvalidacion({{ $est->id }})"
                                    class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-left
                                           hover:bg-gray-50 dark:hover:bg-gray-700/60 transition-colors group">

                                {{-- Avatar --}}
                                <div class="w-9 h-9 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center
                                            text-green-700 dark:text-green-300 font-bold text-sm flex-shrink-0
                                            group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors">
                                    {{ strtoupper(substr($est->name, 0, 2)) }}
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $est->name }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                                        C.C.: {{ $est->cedula ?? '—' }}
                                        @if ($est->email) &nbsp;·&nbsp; {{ $est->email }} @endif
                                    </p>
                                </div>

                                {{-- Badges estado --}}
                                <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                    @if ($tieneConvalidacion)
                                        <span class="px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-900/30
                                                     text-blue-600 dark:text-blue-400 text-xs font-semibold whitespace-nowrap">
                                            Ya convalidado
                                        </span>
                                    @endif
                                    @if ($matriculaActiva)
                                        <span class="px-2 py-0.5 rounded-md bg-green-100 dark:bg-green-900/30
                                                     text-green-600 dark:text-green-400 text-xs whitespace-nowrap">
                                            {{ $matriculaActiva->carrera->name ?? 'Matriculado' }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700
                                                     text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap">
                                            Sin matrícula
                                        </span>
                                    @endif
                                </div>

                                {{-- Arrow --}}
                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-green-500
                                            dark:group-hover:text-green-400 transition-colors flex-shrink-0"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
