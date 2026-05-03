<div class="">
    <div class="">

        {{-- Header --}}
        {{-- <div class="mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
                Información de la Materia
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Completa los datos para registrar o editar la materia.
            </p>
        </div> --}}

        {{-- Errores --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 p-4">
                <div class="font-semibold text-red-700 dark:text-red-300 mb-2">
                    Ups… hay errores en el formulario:
                </div>
                <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- GRID PRINCIPAL --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            {{-- Nombre --}}
            <div class="lg:col-span-2">
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Nombre de la Materia
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $materia->name ?? '') }}"
                    required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            </div>

            {{-- Código --}}
            <div>
                <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Código
                </label>
                <input type="text" name="code" id="code" value="{{ old('code', $materia->code ?? '') }}"
                    required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            </div>

            {{-- Créditos --}}
            <div>
                <label for="credits" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Créditos
                </label>
                <input type="number" name="credits" id="credits" min="0.1" step="0.01" max="10.0"
                    value="{{ old('credits', $materia->credits ?? 0.0) }}" required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            </div>

            {{-- Cupo --}}
            <div>
                <label for="cupo_maximo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Cupo Máximo
                </label>
                <input type="number" name="cupo_maximo" id="cupo_maximo" min="0"
                    value="{{ old('cupo_maximo', $materia->cupo_maximo ?? 30) }}" required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            </div>

            {{-- Nota mínima --}}
            <div>
                <label for="nota_minima_aprobacion"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Nota mínima para aprobar
                </label>
                <input type="number" name="nota_minima_aprobacion" id="nota_minima_aprobacion" min="0.1"
                    step="0.01" max="10.0"
                    value="{{ old('nota_minima_aprobacion', $materia->nota_minima_aprobacion ?? 0.0) }}" required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            </div>

            {{-- Semestre --}}
            <div>
                <label for="semestre_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Semestre
                </label>
                <select name="semestre_id" id="semestre_id" required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
                    <option value="">Seleccione un semestre</option>
                    @foreach ($semestres as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('semestre_id', $materia->semestre_id ?? '') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Horas teóricas --}}
            <div>
                <label for="horas_teoricas" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Horas Teóricas
                </label>
                <input type="number" name="horas_teoricas" id="horas_teoricas" min="0"
                    value="{{ old('horas_teoricas', $materia->horas_teoricas ?? 0) }}" required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            </div>

            {{-- Horas prácticas --}}
            <div>
                <label for="horas_practicas" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Horas Prácticas
                </label>
                <input type="number" name="horas_practicas" id="horas_practicas" min="0"
                    value="{{ old('horas_practicas', $materia->horas_practicas ?? 0) }}" required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            </div>

            {{-- Tipo --}}
            <div>
                <label for="tipo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Tipo de Materia
                </label>
                <select name="tipo" id="tipo" required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
                    @foreach (['Obligatoria', 'Electiva', 'Nivelacion'] as $tipo)
                        <option value="{{ $tipo }}"
                            {{ old('tipo', $materia->tipo ?? 'Obligatoria') == $tipo ? 'selected' : '' }}>
                            {{ $tipo }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        {{-- Sección descripción + estado --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-6">

            {{-- Descripción --}}
            <div class="lg:col-span-2">
                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Descripción
                </label>
                <textarea name="description" id="description" rows="4"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">{{ old('description', $materia->description ?? '') }}</textarea>
            </div>

            {{-- Activo --}}
            <div
                class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4">
                <div class="pt-1">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $materia->is_active ?? false) ? 'checked' : '' }}
                        class="h-5 w-5 rounded border-gray-300 text-verdeclaro focus:ring-verdeclaro">
                </div>

                <div>
                    <label for="is_active" class="font-semibold text-gray-800 dark:text-gray-100">
                        ¿Materia activa?
                    </label>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Si está activa, se mostrará para matrículas.
                    </p>
                </div>
            </div>

        </div>

    </div>
</div>
