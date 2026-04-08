<x-admin-layout>
    <div class="p-4 rounded-lg bg-gray-100 dark:bg-gray-800 shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            Editar Módulo
        </h1>

        <form class="mt-6"
            action="{{ route('administracion.administrativa.materia_periodo_paralelo.update', $materia_periodo_paralelo) }}"
            method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Periodo</label>
                    <select name="periodo_id"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo->id }}" @selected($materia_periodo_paralelo->periodo_id == $periodo->id)>
                                {{ $periodo->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('periodo_id')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Paralelo</label>
                    <select name="paralelo_id"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        @foreach ($paralelos as $paralelo)
                            <option value="{{ $paralelo->id }}" @selected($materia_periodo_paralelo->paralelo_id == $paralelo->id)>
                                {{ $paralelo->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('paralelo_id')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Materia</label>
                    <select name="materia_id"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        @foreach ($materias as $materia)
                            <option value="{{ $materia->id }}" @selected($materia_periodo_paralelo->materia_id == $materia->id)>
                                {{ $materia->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('materia_id')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ $materia_periodo_paralelo->fecha_inicio }}"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                    @error('fecha_inicio')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ $materia_periodo_paralelo->fecha_fin }}"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                    @error('fecha_fin')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div> --}}
                {{-- fecha inicio --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Fecha inicio del módulo
                    </label>
                    <input type="date" name="fecha_inicio"
                        value="{{ old('fecha_inicio', $materia_periodo_paralelo->fecha_inicio->format('Y-m-d')) }}"
                        class="w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200 text-gray-800 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('fecha_inicio')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- fecha fin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Fecha fin del módulo
                    </label>
                    <input type="date" name="fecha_fin"
                        value="{{ old('fecha_fin', $materia_periodo_paralelo->fecha_fin->format('Y-m-d')) }}"
                        class="w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200 text-gray-800 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('fecha_fin')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Estado</label>
                    <select name="estado"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <option value="activo" @selected($materia_periodo_paralelo->estado == 'activo')>Activo</option>
                        <option value="inactivo" @selected($materia_periodo_paralelo->estado == 'inactivo')>Inactivo</option>
                    </select>
                    @error('estado')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div> --}}

                {{-- estado --}}
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="is_active" value="1"
                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        @checked(old('is_active', $materia_periodo_paralelo->is_active))>

                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                        Módulo activo
                    </span>
                </div>

            </div>

            <div class="mt-6 flex items-center gap-2">
                <a href="{{ route('administracion.administrativa.materia_periodo_paralelo.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </a>

                <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">
                    Actualizar
                </button>
            </div>

        </form>
    </div>
</x-admin-layout>
