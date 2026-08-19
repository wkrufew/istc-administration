{{-- Mostrar errores de validación --}}
@if ($errors->any())
    <div class="mb-5 rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 p-4">
        <div class="font-semibold text-red-700 dark:text-red-300 mb-2">¡Ups! Algo salió mal.</div>
        <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Formulario para crear o editar una carrera --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
        <input type="text" name="name" id="name"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('name', $carrera->name ?? '') }}" required>
    </div>

    <div>
        <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Código</label>
        <input type="text" name="code" id="code"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('code', $carrera->code ?? '') }}" required>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
    <div>
        <label for="costo_credito" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Costo por Crédito</label>
        <input type="number" name="costo_credito" id="costo_credito" step="0.01" min="0"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('costo_credito', $carrera->costo_credito ?? '0.00') }}" required>
    </div>
    <div>
        <label for="costo_carrera" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Costo Carrera (Regular)</label>
        <input type="number" name="costo_carrera" id="costo_carrera" step="0.01" min="0"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('costo_carrera', $carrera->costo_carrera ?? '0.00') }}" required>
    </div>
    <div>
        <label for="costo_convalidacion" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
            Arancel Convalidación <span class="text-xs font-normal text-gray-400">(por semestre)</span>
        </label>
        <input type="number" name="costo_convalidacion" id="costo_convalidacion" step="0.01" min="0"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('costo_convalidacion', $carrera->costo_convalidacion ?? '') }}"
            placeholder="Vacío = usa costo_carrera">
        <p class="mt-1 text-xs text-gray-400">Si se deja vacío, se calcula sobre el costo regular.</p>
    </div>

    <div>
        <label for="duracion_semestres" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Duración (Semestres)</label>
        <input type="number" name="duracion_semestres" id="duracion_semestres" min="1"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('duracion_semestres', $carrera->duracion_semestres ?? '6') }}" required>
    </div>

    <div>
        <label for="modalidad" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Modalidad</label>
        <select name="modalidad" id="modalidad"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            required>
            @foreach (['Presencial', 'Virtual', 'Híbrida', 'Semipresencial'] as $modo)
                <option value="{{ $modo }}"
                    {{ old('modalidad', $carrera->modalidad ?? 'Presencial') == $modo ? 'selected' : '' }}>
                    {{ $modo }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
            Tipo de carrera <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-3">
            @foreach (['Tecnologica' => 'Tecnológica (240 / 120 h)', 'Tecnicatura' => 'Tecnicatura (192 / 60 h)'] as $val => $label)
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="tipo" value="{{ $val }}" class="sr-only peer"
                        {{ old('tipo', $carrera->tipo ?? 'Tecnologica') === $val ? 'checked' : '' }}>
                    <div class="rounded-xl border-2 px-4 py-3 text-center text-sm font-semibold transition-all
                                peer-checked:border-verdeclaro peer-checked:bg-verdeclaro/10 peer-checked:text-verdeclaro
                                border-gray-300 text-gray-600 dark:border-gray-600 dark:text-gray-300 hover:border-verdeclaro/60">
                        {{ $label }}
                    </div>
                </label>
            @endforeach
        </div>
        <p class="mt-1 text-xs text-gray-400">Horas mínimas: preprofesionales / comunitarias</p>
        @error('tipo')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2">
        <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
        <textarea name="description" id="description" rows="3"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">{{ old('description', $carrera->description ?? '') }}</textarea>
    </div>

    <div class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4">
        <div class="pt-0.5">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                {{ old('is_active', $carrera->is_active ?? false) ? 'checked' : '' }}
                class="h-5 w-5 rounded border-gray-300 text-verdeclaro focus:ring-verdeclaro">
        </div>
        <div>
            <label for="is_active" class="font-semibold text-gray-800 dark:text-gray-100">¿Carrera activa?</label>
            <p class="text-sm text-gray-500 dark:text-gray-400">Si está activa se mostrará para matrículas.</p>
        </div>
    </div>
</div>
