<div class="max-w-2xl mx-auto py-10 sm:px-6 lg:px-8 border border-gray-200  rounded-lg mb-4">

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>¡Ups! Algo salió mal.</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario para crear o editar una carrera --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="">
            <label for="name" class="form-label text-sm font-medium">Nombre</label>
            <input type="text" name="name" id="name"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('name', $carrera->name ?? '') }}" required>
        </div>

        <div class="">
            <label for="code" class="form-label text-sm font-medium">Codigo</label>
            <input type="text" name="code" id="code"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('code', $carrera->code ?? '') }}" required>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 my-4">
        <div>
            <label for="costo_credito" class="form-label text-sm font-medium">Costo por Crédito</label>
            <input type="number" name="costo_credito" id="costo_credito" step="0.01" min="0"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('costo_credito', $carrera->costo_credito ?? '0.00') }}" required>
        </div>
        <div>
            <label for="costo_carrera" class="form-label text-sm font-medium">Costo por Carrera</label>
            <input type="number" name="costo_carrera" id="costo_carrera" step="0.01" min="0"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('costo_carrera', $carrera->costo_carrera ?? '0.00') }}" required>
        </div>

        <div>
            <label for="duracion_semestres" class="form-label text-sm font-medium">Duración (Semestres)</label>
            <input type="number" name="duracion_semestres" id="duracion_semestres" min="1"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('duracion_semestres', $carrera->duracion_semestres ?? '6') }}" required>
        </div>

        <div>
            <label for="modalidad" class="form-label text-sm font-medium">Modalidad</label>
            <select name="modalidad" id="modalidad"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                required>
                @foreach (['Presencial', 'Virtual', 'Híbrida', 'Semipresencial'] as $modo)
                    <option value="{{ $modo }}"
                        {{ old('modalidad', $carrera->modalidad ?? 'Presencial') == $modo ? 'selected' : '' }}>
                        {{ $modo }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mt-4">
        <div class="">
            <label for="description" class="form-label text-sm font-medium">description</label>
            <textarea name="description" id="description"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-md border border-verde"
                rows="3">{{ old('description', $carrera->description ?? '') }}</textarea>
        </div>

        <div class="form-check flex items-center justify-center">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input checked:bg-verdeclaro" type="checkbox" name="is_active" id="is_active"
                value="1" {{ old('is_active', $carrera->is_active ?? false) ? 'checked' : '' }}>
            <label class="form-check-label ml-1" for="is_active"> ¿Está activo?</label>
        </div>
    </div>
</div>
