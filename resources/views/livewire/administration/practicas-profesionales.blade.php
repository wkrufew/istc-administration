<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
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

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Prácticas Preprofesionales</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gestión de prácticas de estudiantes con malla completa</p>
            </div>
            <button wire:click="abrirModalCrear"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-xl transition shadow-sm shadow-blue-500/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Práctica
            </button>
        </div>

        {{-- FILTROS --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700/60 shadow-sm p-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Buscar</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-slate-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="busqueda"
                            placeholder="Nombre del estudiante o cédula..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-600
                                   bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                   placeholder-gray-400 dark:placeholder-slate-500
                                   shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div class="sm:w-52">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Estado</label>
                    <select wire:model.live="filtroEstado"
                        class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                               bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                        <option value="">Todos los estados</option>
                        <option value="En_Curso">En Curso</option>
                        <option value="Completada">Completada</option>
                        <option value="Reprobada">Reprobada</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700/60 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800/60 border-b border-gray-200 dark:border-slate-700/60">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Estudiante</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Empresa</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Período</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Horas</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Nota</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Estado</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/40">
                        @forelse ($practicas as $p)
                            @php
                                $estadoConfig = match ($p->estado) {
                                    'Completada' => ['bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400', 'Completada'],
                                    'En_Curso'   => ['bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400', 'En Curso'],
                                    'Reprobada'  => ['bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400', 'Reprobada'],
                                    default      => ['bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300', $p->estado],
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40 transition">

                                {{-- Estudiante --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                                                    flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                            {{ strtoupper(substr($p->estudiante?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100 text-sm">
                                                {{ $p->estudiante?->name ?? '—' }}</p>
                                            <p class="text-xs text-gray-400 dark:text-slate-500">{{ $p->estudiante?->cedula ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Empresa --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $p->empresa }}</p>
                                    @if ($p->sector)
                                        <p class="text-xs text-gray-400 dark:text-slate-500">{{ $p->sector }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">Tutor: {{ $p->tutor_empresa }}</p>
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <span title="Carta de aceptación"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold
                                                   {{ $p->carta_aceptacion_path
                                                       ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400'
                                                       : 'bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-slate-500' }}">
                                            CA
                                        </span>
                                        <span title="Informe final"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold
                                                   {{ $p->informe_final_path
                                                       ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400'
                                                       : 'bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-slate-500' }}">
                                            IF
                                        </span>
                                        <span title="Certificado de empresa"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold
                                                   {{ $p->certificado_empresa_path
                                                       ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400'
                                                       : 'bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-slate-500' }}">
                                            CE
                                        </span>
                                    </div>
                                </td>

                                {{-- Período --}}
                                <td class="px-5 py-4 text-center">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-slate-200">
                                        {{ $p->fecha_inicio?->format('d/m/Y') ?? '—' }}
                                    </p>
                                    @if ($p->fecha_fin)
                                        <p class="text-xs text-gray-400 dark:text-slate-500">{{ $p->fecha_fin->format('d/m/Y') }}</p>
                                    @endif
                                </td>

                                {{-- Horas --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="text-sm font-bold text-gray-700 dark:text-slate-200">
                                        {{ $p->total_horas ?? '—' }}
                                    </span>
                                    @if ($p->total_horas)
                                        <p class="text-xs text-gray-400 dark:text-slate-500">hrs</p>
                                    @endif
                                </td>

                                {{-- Nota --}}
                                <td class="px-5 py-4 text-center">
                                    @if ($p->nota !== null)
                                        <span class="text-lg font-bold {{ $p->nota >= 7 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                            {{ number_format($p->nota, 2) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-slate-500 text-sm">—</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $estadoConfig[0] }}">
                                        {{ $estadoConfig[1] }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="abrirModalEditar({{ $p->id }})" title="Editar"
                                            class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-slate-700 hover:bg-blue-100 dark:hover:bg-blue-900/40
                                                   text-gray-500 dark:text-slate-400 hover:text-blue-700 dark:hover:text-blue-400
                                                   flex items-center justify-center transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <button title="Eliminar" onclick="confirmEliminar({{ $p->id }})"
                                            class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-slate-700 hover:bg-red-100 dark:hover:bg-red-900/40
                                                   text-gray-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400
                                                   flex items-center justify-center transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center text-gray-400 dark:text-slate-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200 dark:text-slate-700" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="font-semibold">No se encontraron prácticas</p>
                                    <p class="text-sm mt-1">Intenta con otro criterio o crea una nueva</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($practicas->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-slate-700/60">
                    {{ $practicas->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ========================================================================
         MODAL CREAR / EDITAR
         ======================================================================== --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="cerrarModal"></div>

            {{-- Panel del modal --}}
            <div class="relative w-full max-w-2xl flex flex-col max-h-[90vh]
                        bg-white dark:bg-slate-900 rounded-2xl shadow-2xl">

                {{-- Header (siempre visible) --}}
                <div class="bg-gray-800 dark:bg-slate-800 px-6 py-4 rounded-t-2xl flex items-center justify-between flex-shrink-0">
                    <div>
                        <h3 class="text-white font-bold">
                            {{ $modoEdicion ? 'Editar Práctica' : 'Nueva Práctica Preprofesional' }}
                        </h3>
                        @if ($estudianteNombre)
                            <p class="text-gray-400 text-xs mt-0.5">{{ $estudianteNombre }} · {{ $carreraNombre }}</p>
                        @else
                            <p class="text-gray-400 text-xs mt-0.5">Selecciona el estudiante para continuar</p>
                        @endif
                    </div>
                    <button wire:click="cerrarModal" class="text-gray-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Cuerpo (scrollable independiente) --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-5">

                    @error('general')
                        <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-xl p-3 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- BUSCADOR ESTUDIANTE (solo en crear) --}}
                    @if (!$modoEdicion)
                        <div x-data="{ open: @entangle('showDropdownEstudiante') }">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                Estudiante <span class="text-red-500">*</span>
                                <span class="font-normal text-gray-400 dark:text-slate-500">(solo malla completa)</span>
                            </label>
                            <div class="relative">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-slate-500"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input type="text" wire:model.live.debounce.300ms="busquedaEstudiante"
                                        placeholder="Buscar por nombre o cédula..." autocomplete="off"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border shadow-sm
                                               bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                               placeholder-gray-400 dark:placeholder-slate-500
                                               focus:border-blue-500 focus:ring-blue-500 text-sm
                                               {{ $estudianteId
                                                   ? 'border-green-400 dark:border-green-600 bg-green-50 dark:bg-green-950/20'
                                                   : 'border-gray-200 dark:border-slate-600' }}">
                                    @if ($estudianteId)
                                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-green-500"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </div>

                                {{-- Dropdown sugerencias --}}
                                @if ($showDropdownEstudiante && $this->estudiantesSugeridos->count() > 0)
                                    <div class="absolute z-20 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700
                                                rounded-xl shadow-xl overflow-hidden">
                                        @foreach ($this->estudiantesSugeridos as $sug)
                                            @php $mat = $sug->matriculas->first(); @endphp
                                            <button wire:click="seleccionarEstudiante({{ $sug->id }})"
                                                type="button"
                                                class="w-full flex items-center gap-3 px-4 py-3
                                                       hover:bg-blue-50 dark:hover:bg-blue-950/40 transition text-left
                                                       border-b border-gray-100 dark:border-slate-700 last:border-0">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                                                            flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                                    {{ strtoupper(substr($sug->name, 0, 1)) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">
                                                        {{ $sug->name }}</p>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <span class="text-xs text-gray-400 dark:text-slate-500">{{ $sug->cedula ?? 'Sin cédula' }}</span>
                                                        @if ($mat?->carrera)
                                                            <span class="text-xs px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400 font-semibold">
                                                                {{ $mat->carrera->code }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400 font-semibold flex-shrink-0">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Malla OK
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif ($showDropdownEstudiante && $this->estudiantesSugeridos->count() === 0)
                                    <div class="absolute z-20 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700
                                                rounded-xl shadow-xl p-4 text-center text-sm text-gray-400 dark:text-slate-500">
                                        No se encontraron estudiantes con malla completa
                                    </div>
                                @endif
                            </div>
                            @error('estudianteId')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- DATOS DE LA EMPRESA --}}
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-3">Datos de la empresa</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                    Empresa / Institución <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="empresa"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3"
                                    placeholder="Nombre de la empresa">
                                @error('empresa')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Sector</label>
                                <select wire:model="sector"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                                    <option value="">Seleccionar...</option>
                                    @foreach (['Público', 'Privado', 'ONG', 'Educativo', 'Salud', 'Tecnología', 'Otro'] as $s)
                                        <option value="{{ $s }}">{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Dirección</label>
                                <input type="text" wire:model="direccion"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                    Tutor en empresa <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="tutorEmpresa"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                                @error('tutorEmpresa')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Cargo del tutor</label>
                                <input type="text" wire:model="cargoTutor"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Teléfono</label>
                                <input type="text" wire:model="telefono"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Email</label>
                                <input type="email" wire:model="email"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                            </div>
                        </div>
                    </div>

                    {{-- DATOS DE LA PRÁCTICA --}}
                    <div class="border-t border-gray-100 dark:border-slate-700/60 pt-5">
                        <p class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-3">Datos de la práctica</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                    Cargo del estudiante <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="cargoEstudiante"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                                @error('cargoEstudiante')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Actividades realizadas</label>
                                <textarea wire:model="actividades" rows="2"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm resize-none px-3 py-2.5"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                    Fecha inicio <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="fechaInicio"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                                @error('fechaInicio')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Fecha fin</label>
                                <input type="date" wire:model="fechaFin"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                                @error('fechaFin')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Total horas</label>
                                <input type="number" wire:model="totalHoras" min="1"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3"
                                    placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Estado</label>
                                <select wire:model="estado"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                                    <option value="En_Curso">En Curso</option>
                                    <option value="Completada">Completada</option>
                                    <option value="Reprobada">Reprobada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                    Nota
                                    <span class="font-normal text-gray-400 dark:text-slate-500">(sobre 10, mín. 7)</span>
                                </label>
                                <input type="number" wire:model="nota" step="0.01" min="0" max="10"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3"
                                    placeholder="0.00">
                                @error('nota')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Observaciones</label>
                                <input type="text" wire:model="observaciones"
                                    class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                           shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-3">
                            </div>
                        </div>
                    </div>

                    {{-- DOCUMENTOS --}}
                    <div class="border-t border-gray-100 dark:border-slate-700/60 pt-5">
                        <p class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-3">Documentos</p>
                        <div class="space-y-4">

                            {{-- Carta de aceptación --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                    Carta de aceptación
                                    <span class="font-normal text-gray-400 dark:text-slate-500">(PDF, JPG, PNG — máx. 5MB)</span>
                                </label>
                                @if ($cartaAceptacionPath && !$cartaAceptacion)
                                    <div class="flex items-center gap-3 mb-2 p-2.5 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/50 rounded-xl">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <a href="{{ Storage::url($cartaAceptacionPath) }}" target="_blank"
                                            class="text-xs text-green-700 dark:text-green-400 font-semibold hover:underline truncate flex-1">
                                            Archivo actual — clic para ver
                                        </a>
                                    </div>
                                @endif
                                <input type="file" wire:model="cartaAceptacion" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-sm text-gray-600 dark:text-slate-400
                                           file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                                           file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100 cursor-pointer
                                           border border-gray-200 dark:border-slate-600 rounded-xl p-1.5
                                           bg-white dark:bg-slate-800">
                                @error('cartaAceptacion')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                                <div wire:loading wire:target="cartaAceptacion" class="mt-1 text-xs text-blue-600 dark:text-blue-400">Subiendo...</div>
                            </div>

                            {{-- Informe final --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                    Informe final del estudiante
                                    <span class="font-normal text-gray-400 dark:text-slate-500">(PDF, JPG, PNG — máx. 5MB)</span>
                                </label>
                                @if ($informeFinalPath && !$informeFinal)
                                    <div class="flex items-center gap-3 mb-2 p-2.5 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/50 rounded-xl">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <a href="{{ Storage::url($informeFinalPath) }}" target="_blank"
                                            class="text-xs text-green-700 dark:text-green-400 font-semibold hover:underline truncate flex-1">
                                            Archivo actual — clic para ver
                                        </a>
                                    </div>
                                @endif
                                <input type="file" wire:model="informeFinal" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-sm text-gray-600 dark:text-slate-400
                                           file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                                           file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100 cursor-pointer
                                           border border-gray-200 dark:border-slate-600 rounded-xl p-1.5
                                           bg-white dark:bg-slate-800">
                                @error('informeFinal')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                                <div wire:loading wire:target="informeFinal" class="mt-1 text-xs text-blue-600 dark:text-blue-400">Subiendo...</div>
                            </div>

                            {{-- Certificado de la empresa --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                    Certificado de la empresa
                                    <span class="font-normal text-gray-400 dark:text-slate-500">(PDF, JPG, PNG — máx. 5MB)</span>
                                </label>
                                @if ($certificadoPath && !$certificado)
                                    <div class="flex items-center gap-3 mb-2 p-2.5 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/50 rounded-xl">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <a href="{{ Storage::url($certificadoPath) }}" target="_blank"
                                            class="text-xs text-green-700 dark:text-green-400 font-semibold hover:underline truncate flex-1">
                                            Archivo actual — clic para ver
                                        </a>
                                    </div>
                                @endif
                                <input type="file" wire:model="certificado" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-sm text-gray-600 dark:text-slate-400
                                           file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                                           file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100 cursor-pointer
                                           border border-gray-200 dark:border-slate-600 rounded-xl p-1.5
                                           bg-white dark:bg-slate-800">
                                @error('certificado')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                                <div wire:loading wire:target="certificado" class="mt-1 text-xs text-blue-600 dark:text-blue-400">Subiendo...</div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- Footer (siempre visible) --}}
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700/60 flex justify-between items-center
                            flex-shrink-0 bg-white dark:bg-slate-900 rounded-b-2xl">
                    <button wire:click="cerrarModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200
                               bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600
                               rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="guardar" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700
                               rounded-xl transition disabled:opacity-50 flex items-center gap-2">
                        <span wire:loading.remove wire:target="guardar">
                            {{ $modoEdicion ? 'Guardar cambios' : 'Registrar práctica' }}
                        </span>
                        <span wire:loading wire:target="guardar" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- SweetAlert confirmar eliminación --}}
    <script>
        function confirmEliminar(id) {
            Swal.fire({
                title: '¿Eliminar práctica?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.eliminar(id);
                }
            });
        }
    </script>

</div>
