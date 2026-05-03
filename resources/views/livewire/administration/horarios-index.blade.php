<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Horarios</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ordenados por día de semana y hora de inicio.</p>
            </div>
            <a href="{{ route('administracion.administrativa.horarios.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-lime-600 hover:bg-lime-700 text-white
                      font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Horario
            </a>
        </div>
    </div>

    {{-- Search + Filters --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm p-4 sm:p-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            {{-- Search --}}
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.350ms="search"
                       type="text"
                       placeholder="Buscar materia, docente, aula, paralelo…"
                       class="w-full pl-9 pr-9 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100
                              focus:border-lime-500 focus:ring-2 focus:ring-lime-500/30 transition">
                @if ($search)
                    <button wire:click="$set('search','')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Period filter --}}
            <div>
                <select wire:model.live="filtroPeriodoId"
                        class="w-full py-2.5 px-3 text-sm rounded-xl border border-gray-300 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100
                               focus:border-lime-500 focus:ring-2 focus:ring-lime-500/30 transition">
                    <option value="">Todos los períodos</option>
                    @foreach ($periodos as $p)
                        <option value="{{ $p->id }}">{{ $p->code }} — {{ $p->description }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Day filter --}}
            <div>
                <select wire:model.live="filtroDia"
                        class="w-full py-2.5 px-3 text-sm rounded-xl border border-gray-300 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100
                               focus:border-lime-500 focus:ring-2 focus:ring-lime-500/30 transition">
                    <option value="">Todos los días</option>
                    @foreach ($dias as $dia)
                        <option value="{{ $dia }}">{{ $dia }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Loading bar --}}
    <div wire:loading.delay class="text-center text-sm text-gray-400 dark:text-gray-500 -mt-3">Cargando…</div>

    {{-- ===== MOBILE CARDS ===== --}}
    <div class="grid grid-cols-1 gap-4 lg:hidden">
        @forelse ($horarios as $horario)
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm p-4">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $horario->dia_semana }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }}
                            — {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                 {{ $horario->is_active ? 'text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-900/40' : 'text-red-700 bg-red-100 dark:text-red-200 dark:bg-red-900/40' }}">
                        {{ $horario->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Materia</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100 text-right">{{ $horario->materia->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Docente</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100 text-right">{{ $horario->asignacionDocente->docente->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Paralelo</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ $horario->paralelo->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Aula</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ $horario->aula ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Modalidad</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-blue-700 bg-blue-100 dark:text-blue-200 dark:bg-blue-900/40">
                            {{ $horario->modalidad_clase }}
                        </span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Período</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-sky-700 bg-sky-100 dark:text-sky-200 dark:bg-sky-900/40">
                            {{ $horario->periodo->code ?? '-' }}
                        </span>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <a href="{{ route('administracion.administrativa.horarios.edit', $horario) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold
                              bg-gray-100 hover:bg-gray-200 text-gray-800
                              dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100 transition">
                        ✏️ Editar
                    </a>
                    <button
                        x-on:click="Swal.fire({
                            title:'¿Eliminar horario?',
                            text:'Esta acción no se puede deshacer.',
                            icon:'warning',
                            showCancelButton:true,
                            confirmButtonColor:'#dc2626',
                            cancelButtonText:'Cancelar',
                            confirmButtonText:'Sí, eliminar'
                        }).then(r => r.isConfirmed && $wire.eliminar({{ $horario->id }}))"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition">
                        🗑 Eliminar
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 text-center">
                <p class="text-gray-500 dark:text-gray-400">No hay horarios registrados.</p>
            </div>
        @endforelse
    </div>

    {{-- ===== DESKTOP TABLE ===== --}}
    <div class="hidden lg:block bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr class="text-gray-700 dark:text-gray-200">
                        <th class="px-4 py-3 text-center font-bold w-12">#</th>
                        <th class="px-4 py-3 text-center font-bold w-40">Horario</th>
                        <th class="px-4 py-3 text-center font-bold">Materia</th>
                        <th class="px-4 py-3 text-center font-bold">Docente</th>
                        <th class="px-4 py-3 text-center font-bold">Aula / Paralelo</th>
                        <th class="px-4 py-3 text-center font-bold w-28">Período</th>
                        <th class="px-4 py-3 text-center font-bold w-24">Estado</th>
                        <th class="px-4 py-3 text-center font-bold w-24">Opciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($horarios as $i => $horario)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition text-gray-700 dark:text-gray-200">
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 font-medium">
                                {{ ($horarios->currentPage() - 1) * 10 + $i + 1 }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $horario->dia_semana }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }}
                                    — {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $horario->materia->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $horario->materia->semestre->carrera->name ?? '' }}
                                    @if ($horario->materia->semestre->name ?? null)
                                        · {{ $horario->materia->semestre->name }}
                                    @endif
                                </div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                                 text-blue-700 bg-blue-100 dark:text-blue-200 dark:bg-blue-900/40">
                                        {{ $horario->modalidad_clase }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $horario->asignacionDocente->docente->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $horario->aula ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $horario->paralelo->name ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                             text-sky-700 bg-sky-100 dark:text-sky-200 dark:bg-sky-900/40">
                                    {{ $horario->periodo->code ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                             {{ $horario->is_active ? 'text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-900/40' : 'text-red-700 bg-red-100 dark:text-red-200 dark:bg-red-900/40' }}">
                                    {{ $horario->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('administracion.administrativa.horarios.edit', $horario) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                              bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition"
                                       title="Editar">
                                        <svg class="w-5 h-5 fill-green-600 dark:fill-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zM172.4 241.7c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7z"/>
                                        </svg>
                                    </a>
                                    <button
                                        x-on:click="Swal.fire({
                                            title:'¿Eliminar horario?',
                                            text:'Esta acción no se puede deshacer.',
                                            icon:'warning',
                                            showCancelButton:true,
                                            confirmButtonColor:'#dc2626',
                                            cancelButtonText:'Cancelar',
                                            confirmButtonText:'Sí, eliminar'
                                        }).then(r => r.isConfirmed && $wire.eliminar({{ $horario->id }}))"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-600 hover:bg-red-700 transition"
                                        title="Eliminar">
                                        <svg class="w-5 h-5 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm96 64c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No hay horarios que coincidan con la búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($horarios->hasPages())
        <div>{{ $horarios->links() }}</div>
    @endif

</div>
