<x-admin-layout>

    <div class="p-6 space-y-6">

        {{-- ================= HEADER ================= --}}
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 
                rounded-2xl shadow-sm p-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Asignación de Docentes
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Gestión académica y asignación de materias
                </p>
            </div>

            <a href="{{ route('administracion.administrativa.docentes.index') }}"
                class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-gray-700 
                  text-gray-800 dark:text-gray-200 
                  hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                ← Volver
            </a>
        </div>

        {{-- ================= PERFIL DOCENTE ================= --}}
        <div
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 
                rounded-2xl shadow-sm p-6">

            <div class="flex flex-col md:flex-row md:items-center gap-6">

                {{-- Avatar --}}
                <div
                    class="h-20 w-20 rounded-full bg-indigo-600 text-white 
                        flex items-center justify-center text-2xl font-bold shadow-lg">
                    {{ strtoupper(substr($docente->first_name, 0, 1)) }}
                </div>

                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $docente->first_name }} {{ $docente->last_name }}
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm text-gray-600 dark:text-gray-400">

                        <div><b>Cédula:</b> {{ $docente->cedula ?? 'No registrado' }}</div>
                        <div><b>Nacimiento:</b>
                            {{ $docente->fecha_nacimiento ? $docente->fecha_nacimiento->isoFormat('D MMM Y') : 'No registrado' }}
                        </div>
                        <div><b>Celular:</b> {{ $docente->phone ?? 'No registrado' }}</div>
                        <div><b>Género:</b> {{ $docente->genero ?? 'No registrado' }}</div>
                        <div><b>Tipo Sangre:</b> {{ $docente->tipo_sangre ?? 'No registrado' }}</div>
                        <div><b>Estado Civil:</b> {{ $docente->estado_civil ?? 'No registrado' }}</div>
                        <div><b>Correo:</b> {{ $docente->email ?? 'No registrado' }}</div>
                        <div class="md:col-span-2"><b>Dirección:</b> {{ $docente->address ?? 'No registrado' }}</div>

                    </div>
                </div>

            </div>
        </div>

        {{-- ================= MENSAJES ================= --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-600 dark:text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400">
                <strong>¡Ups! Algo salió mal.</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================= FORMULARIO ================= --}}
        <div
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 
                rounded-2xl shadow-sm p-6">

            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                Nueva Asignación
            </h2>

            <form method="POST"
                action="{{ route('administracion.administrativa.docentes.asignar.store', $docente->id) }}"
                class="space-y-6">
                @csrf

                {{-- Materia --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Materia
                    </label>
                    <select name="materia_id"
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-800 
                               text-gray-800 dark:text-gray-200
                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione una materia</option>
                        @foreach ($materias as $materia)
                            <option value="{{ $materia->id }}">
                                {{ $materia->name }} - {{ $materia->code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Paralelo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Paralelo
                        </label>
                        <select name="paralelo_id"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-800 
                                   text-gray-800 dark:text-gray-200
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Seleccione un paralelo</option>
                            @foreach ($paralelos as $paralelo)
                                <option value="{{ $paralelo->id }}">{{ $paralelo->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Periodo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Período
                        </label>
                        <select name="periodo_id"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-800 
                                   text-gray-800 dark:text-gray-200
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Seleccione un período</option>
                            @foreach ($periodos as $periodo)
                                <option value="{{ $periodo->id }}">
                                    {{ $periodo->description }} - {{ $periodo->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-4 pt-4">
                    <a href="{{ route('administracion.administrativa.docentes.index') }}"
                        class="px-5 py-2 rounded-xl bg-gray-200 dark:bg-gray-700 
                          text-gray-800 dark:text-gray-200 
                          hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </a>

                    <button type="submit"
                        class="px-6 py-2 rounded-xl bg-indigo-600 text-white
                               hover:bg-indigo-700 transition shadow-md">
                        Asignar
                    </button>
                </div>

            </form>
        </div>

        {{-- ================= ASIGNACIONES ================= --}}
        <div
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 
                rounded-2xl shadow-sm p-6">

            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 text-center">
                Materias Asignadas
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                @forelse ($asignaciones as $asignacion)
                    <div
                        class="relative p-5 rounded-2xl border border-gray-200 dark:border-gray-700 
                            bg-gray-50 dark:bg-gray-800 shadow-sm">

                        {{-- Badge periodo actual --}}
                        @if ($asignacion->periodo->is_current)
                            <span
                                class="absolute top-3 right-3 
                                     px-3 py-1 text-xs rounded-full 
                                     bg-emerald-500/10 text-emerald-600">
                                Periodo actual
                            </span>
                        @endif

                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p><b>Materia:</b> {{ $asignacion->materia->name ?? '-' }}</p>
                            <p><b>Código:</b> {{ $asignacion->materia->code ?? '-' }}</p>
                            <p><b>Paralelo:</b> {{ $asignacion->paralelo->name ?? '-' }}</p>
                            <p><b>Periodo:</b> {{ $asignacion->periodo->description ?? '-' }}</p>
                        </div>

                        <form
                            action="{{ route('administracion.administrativa.docentes.asignar.destroy', $asignacion->id) }}"
                            method="POST" class="mt-4 text-right">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Eliminar esta asignación?')"
                                class="text-sm text-red-500 hover:text-red-700 transition">
                                Eliminar
                            </button>
                        </form>

                    </div>

                @empty
                    <div class="col-span-2 text-center text-gray-500 dark:text-gray-400">
                        No tiene materias asignadas.
                    </div>
                @endforelse

            </div>

        </div>

    </div>

</x-admin-layout>
