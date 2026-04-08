<div class="max-w-3xl mx-auto py-10 sm:px-6 lg:px-8 border border-gray-200 rounded-lg mb-4">

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

    {{-- Formulario para crear o editar un periodo --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="">
            <label for="name" class="form-label text-sm font-medium">Nombre</label>
            <input type="text" name="name" id="name"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('name', $semestre->name ?? '') }}" required>
        </div>

        <div class="">
            <label for="code" class="form-label text-sm font-medium">Codigo</label>
            <input type="text" name="code" id="code"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('code', $semestre->code ?? '') }}" required>
        </div>
        <div class="">
            <label for="order" class="form-label text-sm font-medium">Orden</label>
            <input type="number" name="order" id="order"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('order', $semestre->order ?? '') }}" required>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 my-4">
        <div>
            <label for="creditos_minimos" class="form-label text-sm font-medium">Créditos Mínimos</label>
            <input type="number" name="creditos_minimos" id="creditos_minimos" min="0"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('creditos_minimos', $semestre->creditos_minimos ?? 15) }}" required>
        </div>

        <div>
            <label for="creditos_maximos" class="form-label text-sm font-medium">Créditos Máximos</label>
            <input type="number" name="creditos_maximos" id="creditos_maximos" min="0"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('creditos_maximos', $semestre->creditos_maximos ?? 25) }}" required>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label for="carrera_id" class="form-label text-sm font-medium">Carrera</label>
            <select name="carrera_id" id="carrera_id"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                required>
                <option value="">Seleccione una carrera</option>
                @foreach ($carreras as $id => $nombre)
                    <option value="{{ $id }}"
                        {{ old('carrera_id', $semestre->carrera_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-check flex items-center justify-center">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input checked:bg-verdeclaro" type="checkbox" name="is_active" id="is_active"
                value="1" {{ old('is_active', $semestre->is_active ?? false) ? 'checked' : '' }}>
            <label class="form-check-label ml-1" for="is_active"> ¿Está activo?</label>
        </div>
    </div>
</div>
