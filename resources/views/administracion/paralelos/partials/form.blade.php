<div class="max-w-2xl mx-auto py-10 sm:px-6 lg:px-8 border border-gray-200 rounded-lg mb-4">
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

    {{-- Formulario para crear o editar un paralelo --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2">
            <label for="name" class="form-label text-sm font-medium">Nombre</label>
            <input type="text" name="name" id="name"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('name', $paralelo->name ?? '') }}" required>
        </div>

        <div>
            <label for="code" class="form-label text-sm font-medium">Código</label>
            <input type="text" name="code" id="code"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('code', $paralelo->code ?? '') }}" required>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 my-4">
        <div>
            <label for="cupo_maximo" class="form-label text-sm font-medium">Cupo Máximo</label>
            <input type="number" name="cupo_maximo" id="cupo_maximo" min="0"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('cupo_maximo', $paralelo->cupo_maximo ?? 30) }}" required>
        </div>

        <div>
            <label for="cupo_actual" class="form-label text-sm font-medium">Cupo Actual</label>
            <input type="number" name="cupo_actual" id="cupo_actual" min="0"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('cupo_actual', $paralelo->cupo_actual ?? 0) }}" required>
        </div>

        <div class="flex flex-col justify-center items-center">
            <div class="form-check flex items-center justify-center mt-6">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input checked:bg-verdeclaro" type="checkbox" name="is_active" id="is_active"
                    value="1" {{ old('is_active', $paralelo->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label ml-1" for="is_active"> ¿Está activo?</label>
            </div>
        </div>
    </div>
</div>
