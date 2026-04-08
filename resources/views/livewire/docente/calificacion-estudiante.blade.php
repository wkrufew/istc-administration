<div>
    <div class="min-h-screen rounded-2xl">
        <div class="">

            {{-- Mensajes --}}
            @if ($mensaje)
                <div class="mb-6 animate-fade-in">
                    <div
                        class="bg-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-50 border-l-4 border-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-400 p-4 rounded-r-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-400 mr-3"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    @if ($tipo_mensaje === 'success')
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    @else
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"></path>
                                    @endif
                                </svg>
                                <p class="text-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-800 font-medium">
                                    {{ $mensaje }}</p>
                            </div>
                            <button wire:click="$set('mensaje', '')"
                                class="text-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-400 hover:text-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Filtros --}}
            <div
                class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
                <h2 class="text-xl font-semibold text-slate-800 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                        </path>
                    </svg>
                    Filtros de Búsqueda
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Período Académico -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">Período Académico</label>
                        <div class="relative">
                            <select wire:model.live="periodo_id"
                                class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 appearance-none">
                                <option value="">Seleccione un período</option>
                                @foreach ($periodos as $periodo)
                                    <option value="{{ $periodo->id }}">
                                        {{ $periodo->code }} - {{ $periodo->description }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    @if ($periodo_id && count($materias_asignadas) > 0)
                        <!-- Materia -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Materia</label>
                            <div class="relative">
                                <select wire:model.live="materia_id"
                                    class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 appearance-none">
                                    <option value="">Seleccione una materia</option>
                                    @foreach ($materias_asignadas as $nombre_materia => $data)
                                        <option value="{{ $data['materia']->id }}">
                                            {{ $data['materia']->code }} - {{ $data['materia']->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($materia_id && count($paralelos) > 0)
                        <!-- Paralelo -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Paralelo</label>
                            <div class="relative">
                                <select wire:model.live="paralelo_id"
                                    class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 appearance-none">
                                    <option value="">Seleccione un paralelo</option>
                                    @foreach ($paralelos as $paralelo)
                                        <option value="{{ $paralelo->id }}">
                                            {{ $paralelo->name }} ({{ $paralelo->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Lista de Estudiantes --}}
            @if (count($estudiantes) > 0)
                <div
                    class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200">
                        <h2 class="text-xl font-semibold text-slate-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                            Lista de Estudiantes
                        </h2>
                        <p class="text-sm text-gray-600 mt-2">
                            Total estudiantes: {{ count($estudiantes) }}
                            | Arrastres: {{ collect($estudiantes)->where('estado_final', 'Reprobado')->count() }}
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                <tr>
                                    {{-- <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Código </th> --}}
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Estudiante</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Cédula</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tipo
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Nota
                                        Final</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Estado</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($estudiantes as $estudiante)
                                    <tr class="hover:bg-slate-50 transition-colors duration-200">
                                        {{-- <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                                {{ $estudiante['codigo_matricula'] }}
                                            </span>
                                        </td> --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center text-white font-semibold text-sm mr-4">
                                                    {{ substr($estudiante['estudiante']->name, 0, 1) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <div class="text-base font-semibold text-slate-900">
                                                        {{ $estudiante['estudiante']->name }}</div>
                                                    {{-- <div class="text-sm text-slate-500">
                                                        {{ $estudiante['estudiante']->first_name }}
                                                        {{ $estudiante['estudiante']->last_name }}
                                                    </div> --}} {{-- <br> --}}
                                                    <span
                                                        class="text-sm font-semibold">{{ $estudiante['codigo_matricula'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                            {{ $estudiante['estudiante']->cedula ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                            {{ $estudiante['estudiante']->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                            @if ($estudiante['tipo'] === 'Arrastre') bg-amber-100 text-amber-800 
                                            @elseif($estudiante['tipo'] === 'Validacion') bg-blue-100 text-blue-800 
                                            @else bg-emerald-100 text-emerald-800 @endif">
                                                {{ $estudiante['tipo'] }}
                                            </span>
                                            @if ($estudiante['es_repeticion'])
                                                <span
                                                    class="ml-1 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                                    Repetición
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($estudiante['nota_final'] !== null)
                                                <span
                                                    class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold
                                                @if ($estudiante['nota_final'] >= 7) bg-emerald-100 text-emerald-800 
                                                @elseif($estudiante['nota_final'] >= 5) bg-amber-100 text-amber-800 
                                                @else bg-red-100 text-red-800 @endif">
                                                    {{ number_format($estudiante['nota_final'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-slate-500 text-sm">Sin calificar</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            @if ($estudiante['estado_final'] === 'Aprobado') bg-emerald-100 text-emerald-800 
                                            @elseif($estudiante['estado_final'] === 'Reprobado') bg-red-100 text-red-800 
                                            @elseif($estudiante['estado_final'] === 'Retirado') bg-amber-100 text-amber-800 
                                            @else bg-slate-100 text-slate-800 @endif">
                                                {{ $estudiante['estado_final'] }}
                                            </span>
                                            {{-- Indicador visual si el estudiante está en arrastre o reprobado --}}
                                            @if ($estudiante['estado_final'] === 'Reprobado' || $estudiante['tipo'] === 'Arrastre')
                                                <span
                                                    class="ml-2 inline-flex items-center px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">
                                                    Arrastre
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button
                                                wire:click="abrirFormularioCalificacion({{ $estudiante['detalle_matricula_id'] }})"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 transform hover:scale-105 transition-all duration-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                @if ($estudiante['tiene_calificacion'])
                                                    Editar
                                                @else
                                                    Calificar
                                                @endif
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($paralelo_id)
                <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-r-2xl shadow-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-blue-800 font-medium">No hay estudiantes inscritos en este paralelo.</p>
                    </div>
                </div>
            @endif

            {{-- Modal de Calificación --}}
            @if ($mostrar_formulario && $estudiante_seleccionado)
                <div
                    class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl max-h-screen overflow-y-auto">
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-6 rounded-t-3xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-2xl font-bold">Calificación de Estudiante</h3>
                                        <p class="text-indigo-100 mt-1">
                                            {{ $estudiante_seleccionado['estudiante']->name }}</p>
                                    </div>
                                </div>
                                <button wire:click="cerrarFormulario"
                                    class="text-white hover:text-indigo-200 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-8 space-y-8">
                            <!-- Información del Estudiante -->
                            <div
                                class="bg-gradient-to-r from-slate-50 to-blue-50 rounded-2xl p-6 border border-slate-200">
                                <h4 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                    Información del Estudiante
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-sm font-medium text-slate-600">Estudiante:</span>
                                            <p class="text-slate-900 font-semibold">
                                                {{ $estudiante_seleccionado['estudiante']->name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-slate-600">Código:</span>
                                            <p class="text-slate-900 font-semibold">
                                                {{ $estudiante_seleccionado['codigo_matricula'] }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-slate-600">Cédula:</span>
                                            <p class="text-slate-900 font-semibold">
                                                {{ $estudiante_seleccionado['estudiante']->cedula ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-sm font-medium text-slate-600">Tipo:</span>
                                            <span
                                                class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            @if ($estudiante_seleccionado['tipo'] === 'Arrastre') bg-amber-100 text-amber-800 
                                            @elseif($estudiante_seleccionado['tipo'] === 'Validacion') bg-blue-100 text-blue-800 
                                            @else bg-emerald-100 text-emerald-800 @endif">
                                                {{ $estudiante_seleccionado['tipo'] }}
                                            </span>
                                        </div>
                                        @if ($estudiante_seleccionado['es_repeticion'])
                                            <div>
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                                    Repetición
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Insumos -->
                            <div class="bg-white rounded-2xl p-6 border-2 border-emerald-200">
                                <h4 class="text-lg font-semibold text-slate-800 mb-6 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    Insumos de Evaluación (60%)
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div class="space-y-2">
                                        {{-- <label class="block text-sm font-semibold text-slate-700">Insumo 1</label>
                                        <input type="number" step="0.01" min="0" max="10"
                                            wire:model.live="insumo1"
                                            class="w-full bg-white border-2 border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200"> --}}
                                        <div class="space-y-2">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 flex items-center justify-between">
                                                <span>Asistencia</span>

                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                                    {{ $asistencias_asistidas }}/{{ $asistencias_totales }}
                                                </span>
                                            </label>

                                            <input type="text" value="{{ number_format((float) $insumo1, 2) }}"
                                                readonly
                                                class="w-full bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-300 rounded-xl px-3 py-2 text-indigo-800 font-bold text-center cursor-not-allowed">

                                            <p class="text-xs text-slate-500">
                                                Este valor se calcula automáticamente desde las asistencias del
                                                estudiante (sobre 10).
                                            </p>
                                        </div>
                                        @error('insumo1')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Actvidades
                                            Autónomas</label>
                                        <input type="number" step="0.01" min="0" max="10"
                                            wire:model.live="insumo2"
                                            class="w-full bg-white border-2 border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200">
                                        @error('insumo2')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Actvidades
                                            Prácticas</label>
                                        <input type="number" step="0.01" min="0" max="10"
                                            wire:model.live="insumo3"
                                            class="w-full bg-white border-2 border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200">
                                        @error('insumo3')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Actividades con el
                                            Docente</label>
                                        <input type="number" step="0.01" min="0" max="10"
                                            wire:model.live="insumo4"
                                            class="w-full bg-white border-2 border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200">
                                        @error('insumo4')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Etica</label>
                                        <input type="number" step="0.01" min="0" max="10"
                                            wire:model.live="insumo5"
                                            class="w-full bg-white border-2 border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200">
                                        @error('insumo5')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Promedio
                                            Insumos</label>
                                        <input type="text" value="{{ number_format($promedio_insumos, 2) }}"
                                            class="w-full bg-gradient-to-r from-emerald-50 to-emerald-100 border-2 border-emerald-300 rounded-xl px-3 py-2 text-emerald-800 font-bold text-center"
                                            readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl p-6 border-2 border-blue-200">
                                <h4 class="text-lg font-semibold text-slate-800 mb-6 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Exámenes (40%)
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Examen Parcial
                                            (20%)</label>
                                        <input type="number" step="0.01" min="0" max="10"
                                            wire:model.live="examen_parcial"
                                            class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200">
                                        @error('examen_parcial')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Examen Final
                                            (20%)</label>
                                        <input type="number" step="0.01" min="0" max="10"
                                            wire:model.live="examen_final"
                                            class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200">
                                        @error('examen_final')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Sección de Suspenso (solo visible si aplica) -->
                                @if ($nota_final >= 4 && $nota_final < 7)
                                    <div class="mt-6 pt-6 border-t-2 border-amber-200">
                                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4 mb-4">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-amber-500 mr-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                    </path>
                                                </svg>
                                                <p class="text-amber-800 font-semibold">
                                                    El estudiante califica para examen de suspenso (Nota entre 4.00 y
                                                    6.99)
                                                </p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="space-y-2">
                                                <label
                                                    class="block text-sm font-semibold text-amber-700 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                    Nota de Suspenso
                                                </label>
                                                <input type="number" step="0.01" min="0" max="10"
                                                    wire:model.live="nota_suspenso"
                                                    class="w-full bg-white border-2 border-amber-300 rounded-xl px-4 py-3 text-slate-700 focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-200">
                                                @error('nota_suspenso')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                                <p class="text-xs text-amber-600">
                                                    Ingrese la nota del examen de suspenso (sobre 10 puntos)
                                                </p>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-sm font-semibold text-slate-700">Nota Final
                                                    (Con Suspenso)</label>
                                                <input type="text" value="{{ number_format($nota_final, 2) }}"
                                                    class="w-full border-0 rounded-xl px-4 py-3 font-bold text-xl text-center
                        @if ($nota_final >= 7) bg-gradient-to-r from-emerald-500 to-green-600 text-white
                        @else bg-gradient-to-r from-red-500 to-rose-600 text-white @endif
                        shadow-lg"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-6">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-semibold text-slate-700">Nota
                                                Final</label>
                                            <input type="text" value="{{ number_format($nota_final, 2) }}"
                                                class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white border-0 rounded-xl px-4 py-3 font-bold text-xl text-center shadow-lg"
                                                readonly>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Estado y Configuraciones -->
                            <div class="bg-white rounded-2xl p-6 border-2 border-purple-200">
                                <h4 class="text-lg font-semibold text-slate-800 mb-6 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Estado y Configuraciones
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Estado Final</label>
                                        <input type="text" value="{{ $estado_final }}"
                                            class="w-full border-0 rounded-xl px-4 py-3 font-bold text-center
               @if ($estado_final === 'Aprobado') bg-emerald-100 text-emerald-800 
               @elseif($estado_final === 'Reprobado') bg-red-100 text-red-800 
               @elseif($estado_final === 'Retirado') bg-amber-100 text-amber-800
               @else bg-slate-100 text-slate-800 @endif"
                                            readonly>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Número de
                                            Intento</label>
                                        <div class="relative">
                                            <select wire:model.live="numero_intento"
                                                class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 appearance-none">
                                                <option value="1">Primer Intento</option>
                                                <option value="2">Segundo Intento</option>
                                                <option value="3">Tercer Intento</option>
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                                <svg class="w-5 h-5 text-slate-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('numero_intento')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-slate-700">Es Arrastre</label>
                                        <div class="flex items-center justify-center h-full">
                                            <label
                                                class="flex items-center space-x-3 cursor-pointer bg-blue-50 px-4 py-3 rounded-xl border-2 border-blue-200 transition-all duration-200 hover:bg-blue-100">
                                                <input type="checkbox" wire:model.live="es_arrastre"
                                                    @if ($estudiante_seleccionado['tipo'] === 'Arrastre') checked disabled @endif
                                                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                <span class="text-sm font-semibold text-blue-700">
                                                    @if ($estudiante_seleccionado['tipo'] === 'Arrastre')
                                                        Marcado como Arrastre
                                                    @else
                                                        Marcar como Arrastre
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                @if ($estudiante_seleccionado['tipo'] === 'Arrastre' || $es_arrastre)
                                    <div
                                        class="mt-4 bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-400 p-4 rounded-r-xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-amber-500 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg>
                                            <p class="text-amber-800 font-semibold text-sm">
                                                ⚠️ Este estudiante está repitiendo esta materia (Arrastre)
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Resumen de Cálculo -->
                            <div
                                class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-400 p-6 rounded-r-2xl">
                                <h4 class="text-lg font-semibold text-indigo-800 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Fórmula de Cálculo
                                </h4>
                                <div class="bg-white rounded-xl p-4 border border-indigo-200 space-y-3">
                                    <div>
                                        <p class="text-indigo-900 font-bold text-lg mb-2">
                                            Nota Final = (Promedio Insumos × 60%) + (Examen Parcial × 20%) + (Examen
                                            Final × 20%)
                                        </p>
                                        <p class="text-indigo-700 text-sm">
                                            <span class="font-semibold">Detalle:</span>
                                            ({{ number_format($promedio_insumos, 2) }} × 0.6) +
                                            ({{ $examen_parcial ?: '0' }} × 0.2) +
                                            ({{ $examen_final ?: '0' }} × 0.2) =
                                            <span class="font-bold text-lg text-indigo-900">
                                                {{ number_format($promedio_insumos * 0.6 + ($examen_parcial ?: 0) * 0.2 + ($examen_final ?: 0) * 0.2, 2) }}
                                            </span>
                                        </p>
                                    </div>

                                    @if ($nota_final >= 4 && $nota_final < 7 && $nota_suspenso)
                                        <div class="pt-3 border-t border-amber-200">
                                            <p class="text-amber-800 font-bold text-lg mb-2">
                                                Cálculo con Suspenso:
                                            </p>
                                            <p class="text-amber-700 text-sm">
                                                <span class="font-semibold">Incremento:</span>
                                                ({{ $nota_suspenso }} ÷ 10) × 2.99 =
                                                <span
                                                    class="font-bold">{{ number_format(($nota_suspenso / 10) * 2.99, 2) }}</span>
                                            </p>
                                            <p class="text-amber-700 text-sm mt-1">
                                                <span class="font-semibold">Nota Final:</span>
                                                {{ number_format($promedio_insumos * 0.6 + ($examen_parcial ?: 0) * 0.2 + ($examen_final ?: 0) * 0.2, 2) }}
                                                +
                                                {{ number_format(($nota_suspenso / 10) * 2.99, 2) }} =
                                                <span
                                                    class="font-bold text-lg text-amber-900">{{ number_format($nota_final, 2) }}</span>
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- {{ $this->mensaje }} <br>
                        {{ $this->tipo_mensaje }} --}}
                        <!-- Modal Footer -->
                        <div class="bg-slate-50 px-8 py-6 rounded-b-3xl flex justify-end space-x-4">
                            <button wire:click="cerrarFormulario"
                                class="inline-flex items-center px-6 py-3 border-2 border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-100 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </button>
                            <button wire:click="guardarCalificacion"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                    </path>
                                </svg>
                                Guardar Calificación
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <style>
            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fade-in 0.3s ease-out;
            }

            /* Custom scrollbar */
            .overflow-y-auto::-webkit-scrollbar {
                width: 6px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 3px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: linear-gradient(to bottom, #6366f1, #8b5cf6);
                border-radius: 3px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(to bottom, #4f46e5, #7c3aed);
            }

            /* Smooth transitions for all interactive elements */
            button,
            input,
            select {
                transition: all 0.2s ease-in-out;
            }

            /* Enhanced focus states */
            button:focus,
            input:focus,
            select:focus {
                outline: none;
            }

            /* Hover effects for table rows */
            tbody tr:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            }

            /* Glass morphism effect for cards */
            .backdrop-blur-sm {
                backdrop-filter: blur(8px);
            }

            /* Custom gradient text */
            .bg-clip-text {
                -webkit-background-clip: text;
                background-clip: text;
            }

            /* Loading animation for buttons */
            button:active {
                transform: scale(0.98);
            }

            /* Responsive design improvements */
            @media (max-width: 768px) {
                .modal-dialog {
                    margin: 1rem;
                    max-width: calc(100% - 2rem);
                }

                .grid-cols-6 {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .px-8 {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }
            }

            /* Enhanced accessibility */
            @media (prefers-reduced-motion: reduce) {
                * {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                }
            }
        </style>
    </div>
</div>
