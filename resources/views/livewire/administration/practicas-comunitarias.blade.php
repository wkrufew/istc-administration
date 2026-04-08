<div>
    {{-- SweetAlert2 listener --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('swal', (params) => {
                const p = params[0];
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: p.tipo,
                    title: p.mensaje,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        });
    </script>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Prácticas Comunitarias</h2>
                <p class="text-gray-500 text-sm mt-1">Gestión de prácticas comunitarias de estudiantes con malla completa
                </p>
            </div>
            <button wire:click="abrirModalCrear"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-xl transition shadow-sm shadow-blue-500/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Práctica Comunitaria
            </button>
        </div>

        {{-- FILTROS --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Buscar</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="busqueda"
                            placeholder="Nombre del estudiante o cédula..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 shadow-sm
                                   focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div class="sm:w-52">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Estado</label>
                    <select wire:model.live="filtroEstado"
                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Todos los estados</option>
                        <option value="En_Curso">En Curso</option>
                        <option value="Completada">Completada</option>
                        <option value="Reprobada">Reprobada</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Estudiante</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Empresa</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Período</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Horas</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Nota</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Estado</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($comunitarias as $p)
                            @php
                                $estadoConfig = match ($p->estado) {
                                    'Completada' => ['bg-green-100 text-green-700', 'Completada'],
                                    'En_Curso' => ['bg-blue-100 text-blue-700', 'En Curso'],
                                    'Reprobada' => ['bg-red-100 text-red-700', 'Reprobada'],
                                    default => ['bg-gray-100 text-gray-600', $p->estado],
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                {{-- Estudiante --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                                                    flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                            {{ strtoupper(substr($p->estudiante?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">
                                                {{ $p->estudiante?->name ?? '—' }}</p>
                                            <p class="text-xs text-gray-400">{{ $p->estudiante?->cedula ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Empresa --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-800 text-sm">{{ $p->empresa }}</p>
                                    @if ($p->sector)
                                        <p class="text-xs text-gray-400">{{ $p->sector }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">Tutor: {{ $p->tutor_empresa }}</p>
                                    {{-- Indicadores de documentos --}}
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <span title="Carta de aceptación"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold
                                                   {{ $p->carta_aceptacion_path ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                            CA
                                        </span>
                                        <span title="Informe final"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold
                                                   {{ $p->informe_final_path ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                            IF
                                        </span>
                                        <span title="Certificado de empresa"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold
                                                   {{ $p->certificado_empresa_path ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                            CE
                                        </span>
                                    </div>
                                </td>

                                {{-- Período --}}
                                <td class="px-5 py-4 text-center">
                                    <p class="text-xs font-semibold text-gray-700">
                                        {{ $p->fecha_inicio?->format('d/m/Y') ?? '—' }}
                                    </p>
                                    @if ($p->fecha_fin)
                                        <p class="text-xs text-gray-400">{{ $p->fecha_fin->format('d/m/Y') }}</p>
                                    @endif
                                </td>

                                {{-- Horas --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="text-sm font-bold text-gray-700">
                                        {{ $p->total_horas ?? '—' }}
                                    </span>
                                    @if ($p->total_horas)
                                        <p class="text-xs text-gray-400">hrs</p>
                                    @endif
                                </td>

                                {{-- Nota --}}
                                <td class="px-5 py-4 text-center">
                                    @if ($p->nota !== null)
                                        <span
                                            class="text-lg font-bold {{ $p->nota >= 7 ? 'text-green-600' : 'text-red-500' }}">
                                            {{ number_format($p->nota, 2) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $estadoConfig[0] }}">
                                        {{ $estadoConfig[1] }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Editar --}}
                                        <button wire:click="abrirModalEditar({{ $p->id }})" title="Editar"
                                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-blue-100 text-gray-500
                                                   hover:text-blue-700 flex items-center justify-center transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        {{-- Eliminar --}}
                                        <button title="Eliminar" onclick="confirmEliminar({{ $p->id }})"
                                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-red-100 text-gray-500
                                                   hover:text-red-600 flex items-center justify-center transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="font-semibold">No se encontraron prácticas comunitarias</p>
                                    <p class="text-sm mt-1">Intenta con otro criterio o crea una nueva</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($comunitarias->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $comunitarias->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ========================================================================
         MODAL CREAR / EDITAR
         ======================================================================== --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="cerrarModal"></div>

                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl">

                    {{-- Header modal --}}
                    <div class="bg-gray-800 px-6 py-4 rounded-t-2xl flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold">
                                {{ $modoEdicion ? 'Editar Práctica Comunitaria' : 'Nueva Práctica Comunitaria' }}
                            </h3>
                            @if ($estudianteNombre)
                                <p class="text-gray-400 text-xs mt-0.5">{{ $estudianteNombre }} ·
                                    {{ $carreraNombre }}</p>
                            @else
                                <p class="text-gray-400 text-xs mt-0.5">Selecciona el estudiante para continuar</p>
                            @endif
                        </div>
                        <button wire:click="cerrarModal" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Cuerpo --}}
                    <div class="p-6 space-y-5 max-h-[72vh] overflow-y-auto">

                        @error('general')
                            <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-xs text-red-600">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- BUSCADOR ESTUDIANTE (solo en crear) --}}
                        @if (!$modoEdicion)
                            <div x-data="{ open: @entangle('showDropdownEstudiante') }">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">
                                    Estudiante <span class="text-red-500">*</span>
                                    <span class="font-normal text-gray-400">(solo malla completa)</span>
                                </label>
                                <div class="relative">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <input type="text" wire:model.live.debounce.300ms="busquedaEstudiante"
                                            placeholder="Buscar por nombre o cédula..." autocomplete="off"
                                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 shadow-sm
                                                   focus:border-blue-500 focus:ring-blue-500 text-sm
                                                   {{ $estudianteId ? 'border-green-400 bg-green-50' : '' }}">
                                        @if ($estudianteId)
                                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-green-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </div>

                                    {{-- Dropdown sugerencias --}}
                                    @if ($showDropdownEstudiante && $this->estudiantesSugeridos->count() > 0)
                                        <div
                                            class="absolute z-20 w-full mt-1 bg-white border border-gray-200
                                                    rounded-xl shadow-xl overflow-hidden">
                                            @foreach ($this->estudiantesSugeridos as $sug)
                                                @php
                                                    $mat = $sug->matriculas->first();
                                                @endphp
                                                <button wire:click="seleccionarEstudiante({{ $sug->id }})"
                                                    type="button"
                                                    class="w-full flex items-center gap-3 px-4 py-3
                                                           hover:bg-blue-50 transition text-left border-b border-gray-100 last:border-0">
                                                    <div
                                                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                                                                flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                                        {{ strtoupper(substr($sug->name, 0, 1)) }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold text-gray-800 truncate">
                                                            {{ $sug->name }}</p>
                                                        <div class="flex items-center gap-2 mt-0.5">
                                                            <span
                                                                class="text-xs text-gray-400">{{ $sug->cedula ?? 'Sin cédula' }}</span>
                                                            @if ($mat?->carrera)
                                                                <span
                                                                    class="text-xs px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 font-semibold">
                                                                    {{ $mat->carrera->code }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs text-green-600 font-semibold flex-shrink-0">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Malla OK
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @elseif ($showDropdownEstudiante && $this->estudiantesSugeridos->count() === 0)
                                        <div
                                            class="absolute z-20 w-full mt-1 bg-white border border-gray-200
                                                    rounded-xl shadow-xl p-4 text-center text-sm text-gray-400">
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
                            {{-- <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Datos de la empresa
                            </p> --}}
                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">
                                    Programa de Vinculacion <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="programa_vinculacion"
                                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Nombre del programa de vinculacion">
                                @error('programa_vinculacion')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Datos de la empresa
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Empresa / Institución <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="empresa"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="Nombre de la empresa">
                                    @error('empresa')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sector</label>
                                    <select wire:model="sector"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">Seleccionar...</option>
                                        @foreach (['Público', 'Privado', 'ONG', 'Educativo', 'Salud', 'Tecnología', 'Otro'] as $s)
                                            <option value="{{ $s }}">{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dirección</label>
                                    <input type="text" wire:model="direccion"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Tutor en empresa <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="tutorEmpresa"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    @error('tutorEmpresa')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cargo del
                                        tutor</label>
                                    <input type="text" wire:model="cargoTutor"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Teléfono</label>
                                    <input type="text" wire:model="telefono"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                                    <input type="email" wire:model="email"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- DATOS DE LA PRÁCTICA --}}
                        <div class="border-t border-gray-100 pt-5">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Datos de la
                                práctica comunitaria</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Cargo del estudiante <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="cargoEstudiante"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    @error('cargoEstudiante')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Actividades
                                        realizadas</label>
                                    <textarea wire:model="actividades" rows="2"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Fecha inicio <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" wire:model="fechaInicio"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    @error('fechaInicio')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha fin</label>
                                    <input type="date" wire:model="fechaFin"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    @error('fechaFin')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Total horas</label>
                                    <input type="number" wire:model="totalHoras" min="1"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Estado</label>
                                    <select wire:model="estado"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="En_Curso">En Curso</option>
                                        <option value="Completada">Completada</option>
                                        <option value="Reprobada">Reprobada</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Nota
                                        <span class="font-normal text-gray-400">(sobre 10, mín. 7)</span>
                                    </label>
                                    <input type="number" wire:model="nota" step="0.01" min="0"
                                        max="10"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="0.00">
                                    @error('nota')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Observaciones</label>
                                    <input type="text" wire:model="observaciones"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- DOCUMENTOS --}}
                        <div class="border-t border-gray-100 pt-5">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Documentos</p>
                            <div class="space-y-4">

                                {{-- Carta de aceptación --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Carta de aceptación
                                        <span class="font-normal text-gray-400">(PDF, JPG, PNG — máx. 5MB)</span>
                                    </label>
                                    @if ($cartaAceptacionPath && !$cartaAceptacion)
                                        <div
                                            class="flex items-center gap-3 mb-2 p-2.5 bg-green-50 border border-green-200 rounded-xl">
                                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <a href="{{ Storage::url($cartaAceptacionPath) }}" target="_blank"
                                                class="text-xs text-green-700 font-semibold hover:underline truncate flex-1">
                                                Archivo actual — clic para ver
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" wire:model="cartaAceptacion" accept=".pdf,.jpg,.jpeg,.png"
                                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4
                                               file:rounded-xl file:border-0 file:text-xs file:font-semibold
                                               file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                               cursor-pointer border border-gray-200 rounded-xl p-1.5">
                                    @error('cartaAceptacion')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                    <div wire:loading wire:target="cartaAceptacion"
                                        class="mt-1 text-xs text-blue-600">Subiendo...</div>
                                </div>

                                {{-- Informe final --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Informe final del estudiante
                                        <span class="font-normal text-gray-400">(PDF, JPG, PNG — máx. 5MB)</span>
                                    </label>
                                    @if ($informeFinalPath && !$informeFinal)
                                        <div
                                            class="flex items-center gap-3 mb-2 p-2.5 bg-green-50 border border-green-200 rounded-xl">
                                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <a href="{{ Storage::url($informeFinalPath) }}" target="_blank"
                                                class="text-xs text-green-700 font-semibold hover:underline truncate flex-1">
                                                Archivo actual — clic para ver
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" wire:model="informeFinal" accept=".pdf,.jpg,.jpeg,.png"
                                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4
                                               file:rounded-xl file:border-0 file:text-xs file:font-semibold
                                               file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                               cursor-pointer border border-gray-200 rounded-xl p-1.5">
                                    @error('informeFinal')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                    <div wire:loading wire:target="informeFinal" class="mt-1 text-xs text-blue-600">
                                        Subiendo...</div>
                                </div>

                                {{-- Certificado de la empresa --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Certificado de la empresa
                                        <span class="font-normal text-gray-400">(PDF, JPG, PNG — máx. 5MB)</span>
                                    </label>
                                    @if ($certificadoPath && !$certificado)
                                        <div
                                            class="flex items-center gap-3 mb-2 p-2.5 bg-green-50 border border-green-200 rounded-xl">
                                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <a href="{{ Storage::url($certificadoPath) }}" target="_blank"
                                                class="text-xs text-green-700 font-semibold hover:underline truncate flex-1">
                                                Archivo actual — clic para ver
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" wire:model="certificado" accept=".pdf,.jpg,.jpeg,.png"
                                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4
                                               file:rounded-xl file:border-0 file:text-xs file:font-semibold
                                               file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                               cursor-pointer border border-gray-200 rounded-xl p-1.5">
                                    @error('certificado')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                    <div wire:loading wire:target="certificado" class="mt-1 text-xs text-blue-600">
                                        Subiendo...</div>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- Footer modal --}}
                    <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                        <button wire:click="cerrarModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button wire:click="guardar" wire:loading.attr="disabled"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700
                                   rounded-xl transition disabled:opacity-50 flex items-center gap-2">
                            <span wire:loading.remove wire:target="guardar">
                                {{ $modoEdicion ? 'Guardar cambios' : 'Registrar práctica comunitaria' }}
                            </span>
                            <span wire:loading wire:target="guardar" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- SweetAlert confirmar eliminación --}}
    <script>
        function confirmEliminar(id) {
            Swal.fire({
                title: '¿Eliminar práctica comunitaria?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                borderRadius: '12px',
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.eliminar(id);
                }
            });
        }
    </script>

</div>
