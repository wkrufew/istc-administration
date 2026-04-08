<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-center items-center mb-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-0">Gestión de Matrículas</h2>
                <p class="text-lg text-gray-600 font-semibold">Administrar matrículas de estudiantes</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-gray-200 border border-gray-300 rounded-lg p-4 mb-4 shadow-lg">
            <div class="max-w-6xl mx-auto mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="w-full">
                        <label class="block text-center text-sm text-gray-700 font-semibold pl-4 mb-2">Buscar
                            Estudiante</label>
                        <input type="text" class="w-full rounded-full border border-gray-400 shadow-lg px-4 py-2"
                            wire:model.live="search" placeholder="Nombre, email, cédula o matrícula...">
                    </div>
                    <div class="w-full">
                        <label class="block text-center text-sm text-gray-700 font-semibold pl-4 mb-2">Período</label>
                        <select class="w-full rounded-full border border-gray-400 shadow-lg px-4 py-2"
                            wire:model.live="selectedPeriodo">
                            <option value="">Todos los períodos</option>
                            @foreach ($periodos as $periodo)
                                <option value="{{ $periodo->id }}">{{ $periodo->code }} - {{ $periodo->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full">
                        <label class="block text-center text-sm text-gray-700 font-semibold pl-4 mb-2">Carrera</label>
                        <select class="w-full rounded-full border border-gray-400 shadow-lg px-4 py-2"
                            wire:model.live="selectedCarrera">
                            <option value="">Todas las carreras</option>
                            @foreach ($carreras as $carrera)
                                <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Estudiantes -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gray-800 py-3 w-full">
                <h6 class="text-center font-bold text-white uppercase text-2xl">Estudiantes</h6>
            </div>
            {{-- @dd($estudiante) --}}
            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Estudiante</th>
                            <th class="px-4 py-3 text-center">Cédula</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-center">Matrícula Actual</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estudiantes as $student)
                            <tr class="hover:bg-gray-100 transition duration-200 border-b text-sm font-medium">
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="bg-gray-200 rounded-full w-10 h-10 flex items-center justify-center text-gray-700 font-bold">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="font-semibold">{{ $student->name }}</h6>
                                            <small class="text-gray-500">{{ $student->matricula_numero }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ $student->cedula }}</td>
                                <td class="px-4 py-3">{{ $student->email }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $matriculaActual = $student->matriculas->first();
                                    @endphp
                                    @if ($matriculaActual)
                                        <div>
                                            <span class="font-bold">{{ $matriculaActual->code }}</span>
                                            <br>
                                            <small class="text-gray-500">{{ $matriculaActual->carrera->name }}</small>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center">
                                        @if ($matriculaActual)
                                            @switch($matriculaActual->status)
                                                @case('Pagada')
                                                    <span
                                                        class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Pagada</span>
                                                @break

                                                @case('Pendiente_Pago')
                                                    <span
                                                        class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Pendiente
                                                        Pago</span>
                                                @break

                                                @case('Vencida')
                                                    <span
                                                        class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">Vencida</span>
                                                @break

                                                @case('Cancelada')
                                                    <span
                                                        class="bg-gray-300 text-gray-800 px-3 py-1 rounded-full text-sm">Cancelada</span>
                                                @break

                                                @default
                                                    <span
                                                        class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ $matriculaActual->status }}</span>
                                            @endswitch
                                        @else
                                            <span
                                                class="bg-purple-300 text-purple-800 px-3 py-1 rounded-full text-sm">Sin
                                                matrícula</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    {{-- {{ $student->id }} --}}
                                    <div class="flex space-x-2">
                                        @if ($matriculaActual)
                                            <button type="button"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm transition duration-200"
                                                wire:click="editarMatricula({{ $matriculaActual->id }})"
                                                title="Editar matrícula">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                    </path>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm transition duration-200"
                                                wire:click="iniciarMatricula({{ $student->id }})"
                                                title="Nueva matrícula">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        @endif

                                        <button type="button"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm transition duration-200"
                                            title="Ver historial">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8">
                                        <div class="text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <p class="text-lg">No se encontraron estudiantes</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-4 py-3 bg-gray-50">
                    {{ $estudiantes->links() }}
                </div>
            </div>

            <!-- Modal de Matrícula -->
            @if ($showModal)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                    style="z-index:99999" aria-modal="true">
                    <!-- Overlay -->
                    <div
                        class="flex items-center justify-center min-h-screen  mt-10 pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                        <!-- This element is to trick the browser into centering the modal contents. -->
                        <span class="hidden sm:inline-block sm:align-middle h-screen" aria-hidden="true">&#8203;</span>

                        <!-- Modal panel -->
                        <div
                            class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
                            <!-- Header -->
                            <div class="bg-white px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        {{ $matriculaId ? 'Editar' : 'Nueva' }} Matrícula - {{ $estudiante->name }} -
                                        {{ $estudiante->id }}
                                    </h3>
                                    <button type="button"
                                        class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        wire:click="cerrarModal">
                                        <span class="sr-only">Cerrar</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Body -->
                            <div
                                class="bg-white px-4 py-4 sm:px-6 h-96 md:h-[300px] lg:h-[470px] max-h-96 md:max-h-[300px] lg:max-h-[470px] overflow-y-auto">
                                <!-- Progress Steps -->
                                <div class="mb-6">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                            style="width: {{ ($paso / $totalPasos) * 100 }}%"></div>
                                    </div>
                                    <div class="flex justify-between mt-3">
                                        <div
                                            class="flex items-center space-x-1 {{ $paso >= 1 ? 'text-blue-600' : 'text-gray-400' }}">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Información</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1 {{ $paso >= 2 ? 'text-blue-600' : 'text-gray-400' }}">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Materias</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1 {{ $paso >= 3 ? 'text-blue-600' : 'text-gray-400' }}">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z">
                                                </path>
                                            </svg>
                                            <span class="text-sm font-medium">Paralelos</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1 {{ $paso >= 4 ? 'text-blue-600' : 'text-gray-400' }}">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Resumen</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Paso 1: Información General -->
                                @if ($paso == 1)
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div>
                                            <div class="bg-gray-100 rounded-lg p-4 border border-gray-200">
                                                <h4 class="font-semibold text-gray-900 mb-3">Información del Estudiante
                                                </h4>
                                                <div class="space-y-2 text-sm">
                                                    <p><span class="font-medium">Nombre:</span> {{ $estudiante->name }}
                                                    </p>
                                                    <p><span class="font-medium">Email:</span> {{ $estudiante->email }}
                                                    </p>
                                                    <p><span class="font-medium">Cédula:</span> {{ $estudiante->cedula }}
                                                    </p>
                                                    <p><span class="font-medium">Celular:</span> {{ $estudiante->phone }}
                                                    </p>
                                                    <p><span class="font-medium">Fecha de nacimiento:</span>
                                                        {{ $estudiante->fecha_nacimiento }}
                                                        {{-- deseo poner la edad en base a la fecha --}}
                                                        ({{ \Carbon\Carbon::parse($estudiante->fecha_nacimiento)->age }}
                                                        años)
                                                    </p>
                                                    <p><span class="font-medium">Matrícula:</span>
                                                        {{ $estudiante->matricula_numero }}</p>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Carrera <span class="text-red-500">*</span>
                                                </label>
                                                <select
                                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('carrera_id') border-red-500 @enderror"
                                                    wire:model="carrera_id">
                                                    <option value="">Seleccionar carrera</option>
                                                    @foreach ($carreras as $carrera)
                                                        <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('carrera_id')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Período Académico <span class="text-red-500">*</span>
                                                </label>
                                                <select
                                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('periodo_id') border-red-500 @enderror"
                                                    wire:model="periodo_id">
                                                    <option value="">Seleccionar período</option>
                                                    @foreach ($periodos as $periodo)
                                                        <option value="{{ $periodo->id }}">
                                                            {{ $periodo->code }} - {{ $periodo->description }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('periodo_id')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de
                                                    Matrícula</label>
                                                <select
                                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    wire:model="tipo">
                                                    <option value="Nueva">Nueva</option>
                                                    <option value="Renovacion">Renovación</option>
                                                    <option value="Arrastre">Arrastre</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                                <textarea class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    wire:model="observaciones" rows="3" placeholder="Observaciones adicionales..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Materias Arrastradas -->
                                    @if (!empty($materiasArrastradas))
                                        <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-yellow-800">Materias Arrastradas
                                                    </h3>
                                                    <div class="mt-2 text-sm text-yellow-700">
                                                        <p class="mb-2">Este estudiante tiene las siguientes materias
                                                            arrastradas:</p>
                                                        <div class="space-y-2">
                                                            @foreach ($materiasArrastradas as $index => $materiaArrastrada)
                                                                <div class="flex items-center">
                                                                    <input type="checkbox"
                                                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                                        wire:model="materiasArrastradas.{{ $index }}.incluir"
                                                                        id="arrastre_{{ $index }}">
                                                                    <label class="ml-2 text-sm"
                                                                        for="arrastre_{{ $index }}">
                                                                        <strong>{{ $materiaArrastrada['materia']['name'] }}</strong>
                                                                        - Nota obtenida:
                                                                        {{ $materiaArrastrada['nota_obtenida'] }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- Paso 2: Selección de Materias -->
                                @if ($paso == 2)
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Materias Disponibles</h4>
                                        @if (empty($materiasDisponibles))
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm text-blue-700">
                                                            No hay materias disponibles para inscribir en esta carrera y
                                                            período.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="space-y-4">
                                                @foreach ($materiasDisponibles as $semestreNombre => $materias)
                                                    <div class="bg-white border border-gray-200 rounded-lg">
                                                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                            <h5 class="text-md font-medium text-gray-900">
                                                                {{ $semestreNombre }}</h5>
                                                        </div>
                                                        <div class="p-4">
                                                            <div
                                                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                                @foreach ($materias as $materia)
                                                                    <div class="relative">
                                                                        <label class="cursor-pointer">
                                                                            <input type="checkbox" class="sr-only peer"
                                                                                value="{{ $materia['id'] }}"
                                                                                wire:model="materiasSeleccionadas"
                                                                                {{ !$materia['puede_inscribir'] ? 'disabled' : '' }}>
                                                                            <div
                                                                                class="border-2 rounded-lg p-3 transition-all duration-200 
                                                                            {{ !$materia['puede_inscribir'] ? 'opacity-50 cursor-not-allowed' : 'hover:border-blue-500' }}
                                                                            peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                                                                <h6
                                                                                    class="font-semibold text-gray-900 mb-1">
                                                                                    {{ $materia['name'] }}</h6>
                                                                                <p class="text-sm text-gray-600 mb-2">
                                                                                    {{ $materia['code'] }}</p>
                                                                                <div
                                                                                    class="flex justify-between items-center">
                                                                                    <span
                                                                                        class="text-xs text-gray-500">{{ $materia['credits'] }}
                                                                                        créditos</span>
                                                                                    <span
                                                                                        class="px-2 py-1 text-xs rounded-full 
                                                                                    {{ $materia['tipo'] == 'Obligatoria' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                                                        {{ $materia['tipo'] }}
                                                                                    </span>
                                                                                </div>
                                                                                @if (!$materia['puede_inscribir'] && !empty($materia['prerequisitos_faltantes']))
                                                                                    <div
                                                                                        class="mt-2 p-2 bg-red-50 rounded text-xs">
                                                                                        <p
                                                                                            class="text-red-700 font-medium">
                                                                                            Prerequisitos faltantes:</p>
                                                                                        <ul class="text-red-600 mt-1">
                                                                                            @foreach ($materia['prerequisitos_faltantes'] as $prerequisito)
                                                                                                <li>• {{ $prerequisito }}
                                                                                                </li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @error('materias')
                                            <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-4">
                                                <p class="text-sm text-red-700">{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                @endif

                                <!-- Paso 3: Selección de Paralelos -->
                                @if ($paso == 3)
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Selección de Paralelos</h4>
                                        @php
                                            $todasLasMaterias = collect($materiasSeleccionadas)
                                                ->merge(
                                                    collect($materiasArrastradas)
                                                        ->where('incluir', true)
                                                        ->pluck('materia_id'),
                                                )
                                                ->unique();
                                        @endphp

                                        <div class="space-y-4">
                                            @foreach ($todasLasMaterias as $materiaId)
                                                @php
                                                    $materia = \App\Models\Materia::find($materiaId);
                                                    $paralelos = isset($paralelosDisponibles[$materiaId])
                                                        ? $paralelosDisponibles[$materiaId]
                                                        : [];
                                                @endphp

                                                <div class="bg-white border border-gray-200 rounded-lg">
                                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                        <h5 class="text-md font-medium text-gray-900">{{ $materia->name }}
                                                            ({{ $materia->code }})
                                                        </h5>
                                                    </div>
                                                    <div class="p-4">
                                                        @if (empty($paralelos))
                                                            <div
                                                                class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                                                <p class="text-sm text-yellow-700">No hay paralelos
                                                                    disponibles para esta materia.</p>
                                                            </div>
                                                        @else
                                                            <div
                                                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                                @foreach ($paralelos as $paralelo)
                                                                    <div class="relative">
                                                                        <label class="cursor-pointer">
                                                                            <input type="radio" class="sr-only peer"
                                                                                name="paralelo_{{ $materiaId }}"
                                                                                value="{{ $paralelo['id'] }}"
                                                                                wire:model="paralelosSeleccionados.{{ $materiaId }}"
                                                                                {{ !$paralelo['tiene_cupo'] ? 'disabled' : '' }}>
                                                                            <div
                                                                                class="border-2 rounded-lg p-3 transition-all duration-200 
                                                                            {{ !$paralelo['tiene_cupo'] ? 'opacity-50 cursor-not-allowed' : 'hover:border-blue-500' }}
                                                                            peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                                                                <h6
                                                                                    class="font-semibold text-gray-900 mb-2">
                                                                                    {{ $paralelo['name'] }}</h6>
                                                                                <div
                                                                                    class="flex justify-between items-center">
                                                                                    <span
                                                                                        class="text-sm {{ $paralelo['tiene_cupo'] ? 'text-green-600' : 'text-red-600' }}">
                                                                                        Cupo:
                                                                                        {{ $paralelo['cupo_disponible'] }}
                                                                                        disponibles
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @error('paralelos')
                                            <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-4">
                                                <p class="text-sm text-red-700">{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                @endif

                                <!-- Paso 4: Resumen y Costos -->
                                @if ($paso == 4)
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                        <div class="lg:col-span-2">
                                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Matrícula</h4>

                                            <!-- Materias Seleccionadas -->
                                            @if (!empty($materiasSeleccionadas))
                                                <div class="bg-white border border-gray-200 rounded-lg mb-4">
                                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                        <h5 class="text-md font-medium text-gray-900">Materias Regulares
                                                        </h5>
                                                    </div>
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                                        Materia</th>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                                        Código</th>
                                                                    <th
                                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                                        Créditos</th>
                                                                    <th
                                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                                        Paralelo</th>
                                                                    <th
                                                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                                        Costo</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                @foreach ($materiasSeleccionadas as $materiaId)
                                                                    @php
                                                                        $materia = \App\Models\Materia::find(
                                                                            $materiaId,
                                                                        );
                                                                        $carrera = \App\Models\Carrera::find(
                                                                            $carrera_id,
                                                                        );
                                                                        $paralelo = isset(
                                                                            $paralelosSeleccionados[$materiaId],
                                                                        )
                                                                            ? \App\Models\Paralelo::find(
                                                                                $paralelosSeleccionados[$materiaId],
                                                                            )
                                                                            : null;
                                                                        $costo =
                                                                            $materia->credits * $carrera->costo_credito;
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="px-4 py-3 text-sm text-gray-900">
                                                                            {{ $materia->name }}</td>
                                                                        <td class="px-4 py-3 text-sm text-gray-500">
                                                                            {{ $materia->code }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-center text-gray-900">
                                                                            {{ $materia->credits }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-center text-gray-500">
                                                                            {{ $paralelo ? $paralelo->name : 'No asignado' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-right font-medium text-gray-900">
                                                                            ${{ number_format($costo, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Materias Arrastradas -->
                                            @php
                                                $materiasArrastradasIncluidas = collect($materiasArrastradas)->where(
                                                    'incluir',
                                                    true,
                                                );
                                            @endphp
                                            @if ($materiasArrastradasIncluidas->count() > 0)
                                                <div class="bg-white border border-gray-200 rounded-lg mb-4">
                                                    <div class="bg-yellow-50 px-4 py-3 border-b border-gray-200">
                                                        <h5 class="text-md font-medium text-gray-900">Materias de Arrastre
                                                        </h5>
                                                    </div>
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                                        Materia</th>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                                        Código</th>
                                                                    <th
                                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                                        Créditos</th>
                                                                    <th
                                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                                        Paralelo</th>
                                                                    <th
                                                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                                        Costo</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                @foreach ($materiasArrastradasIncluidas as $materiaArrastrada)
                                                                    @php
                                                                        $materia = \App\Models\Materia::find(
                                                                            $materiaArrastrada['materia_id'],
                                                                        );
                                                                        $paralelo = isset(
                                                                            $paralelosSeleccionados[
                                                                                $materiaArrastrada['materia_id']
                                                                            ],
                                                                        )
                                                                            ? \App\Models\Paralelo::find(
                                                                                $paralelosSeleccionados[
                                                                                    $materiaArrastrada['materia_id']
                                                                                ],
                                                                            )
                                                                            : null;
                                                                        $costo =
                                                                            $materiaArrastrada['costo_adicional'] ?? 0;
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="px-4 py-3 text-sm text-gray-900">
                                                                            {{ $materia->name }}</td>
                                                                        <td class="px-4 py-3 text-sm text-gray-500">
                                                                            {{ $materia->code }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-center text-gray-900">
                                                                            {{ $materia->credits }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-center text-gray-500">
                                                                            {{ $paralelo ? $paralelo->name : 'No asignado' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-4 py-3 text-sm text-right font-medium text-gray-900">
                                                                            ${{ number_format($costo, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Panel de Costos -->
                                        <div class="lg:col-span-1">
                                            <div class="bg-white border border-gray-200 rounded-lg sticky top-4">
                                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                    <h5 class="text-md font-medium text-gray-900">Resumen de Costos</h5>
                                                </div>
                                                <div class="p-4">
                                                    <div class="mb-4">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 mb-2">Descuento
                                                            ($)</label>
                                                        <input type="number"
                                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('descuento') border-red-500 @enderror"
                                                            wire:model.blur="descuento" min="0"
                                                            max="{{ $costoTotal }}" step="0.01" placeholder="0.00">
                                                        @error('descuento')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <hr class="my-4">

                                                    <div class="space-y-3">
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-sm text-gray-600">Total Créditos:</span>
                                                            <span
                                                                class="font-medium text-gray-900">{{ $totalCreditos }}</span>
                                                        </div>
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-sm text-gray-600">Costo Total:</span>
                                                            <span
                                                                class="font-medium text-gray-900">${{ number_format($costoTotal, 2) }}</span>
                                                        </div>
                                                        @if ($descuento > 0)
                                                            <div class="flex justify-between items-center text-green-600">
                                                                <span class="text-sm">Descuento:</span>
                                                                <span
                                                                    class="font-medium">-${{ number_format($descuento, 2) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <hr class="my-4">

                                                    <div class="flex justify-between items-center">
                                                        <span class="text-lg font-semibold text-gray-900">Total a
                                                            Pagar:</span>
                                                        <span
                                                            class="text-xl font-bold text-blue-600">${{ number_format($totalPagar, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Footer -->
                            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div>
                                        @if ($paso > 1)
                                            <button type="button"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                wire:click="pasoAnterior">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                                Anterior
                                            </button>
                                        @endif
                                    </div>

                                    <div class="flex space-x-3">
                                        <button type="button"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            wire:click="cerrarModal">
                                            Cancelar
                                        </button>

                                        @if ($paso < $totalPasos)
                                            <button type="button"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                wire:click="siguientePaso">
                                                Siguiente
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                wire:click="guardarMatricula">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                {{ $matriculaId ? 'Actualizar' : 'Guardar' }} Matrícula
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Scripts adicionales -->
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('matricula-guardada', (event) => {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: event.mensaje,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                });

                Livewire.on('error', (event) => {
                    Swal.fire({
                        title: 'Error',
                        text: event.mensaje,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });

            // Cerrar modal con tecla ESC
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    Livewire.dispatch('cerrarModal');
                }
            });
        </script>

        <style>
            /* Custom styles for better UX */
            .peer:checked+div {
                @apply border-blue-500 bg-blue-50;
            }

            .peer:disabled+div {
                @apply opacity-50 cursor-not-allowed;
            }

            /* Smooth transitions */
            * {
                transition-property: all;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 200ms;
            }

            /* Scrollbar styling */
            .overflow-y-auto::-webkit-scrollbar {
                width: 6px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 3px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    </div>
