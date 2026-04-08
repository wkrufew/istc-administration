<x-admin-layout>
    <div class="p-4 rounded-lg bg-gray-100 dark:bg-gray-800 shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            Crear Módulo
        </h1>

        <form class="mt-6" action="{{ route('administracion.administrativa.materia_periodo_paralelo.store') }}"
            method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Periodo</label>
                    <select name="periodo_id"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <option value="">-- Seleccione --</option>
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo->id }}" @selected(old('periodo_id') == $periodo->id)>
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
                        <option value="">-- Seleccione --</option>
                        @foreach ($paralelos as $paralelo)
                            <option value="{{ $paralelo->id }}" @selected(old('paralelo_id') == $paralelo->id)>
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
                        <option value="">-- Seleccione --</option>
                        @foreach ($materias as $materia)
                            <option value="{{ $materia->id }}" @selected(old('materia_id') == $materia->id)>
                                {{ $materia->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('materia_id')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                    @error('fecha_inicio')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                    @error('fecha_fin')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- <div>
                    <label class="text-gray-700 dark:text-gray-200 font-semibold">Estado</label>
                    <select name="estado"
                        class="mt-1 block w-full rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <option value="activo" @selected(old('estado') == 'activo')>Activo</option>
                        <option value="inactivo" @selected(old('estado') == 'inactivo')>Inactivo</option>
                    </select>
                    @error('estado')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div> --}}

                {{-- estado --}}
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="is_active" value="1"
                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        @checked(old('is_active', true))>

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
                    Guardar
                </button>
            </div>

        </form>
    </div>
</x-admin-layout>
