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

    {{-- Formulario para crear o editar un periodo --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="">
            <label for="description" class="form-label text-sm font-medium">Detalle</label>
            <input type="text" name="description" id="description"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('description', $periodo->description ?? '') }}" required>
        </div>

        <div class="">
            <label for="code" class="form-label text-sm font-medium">Codigo</label>
            <input type="text" name="code" id="code"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('code', $periodo->code ?? '') }}" required>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 my-4">
        <div class="">
            <label for="fecha_inicio" class="form-label text-sm font-medium">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('fecha_inicio', $periodo->fecha_inicio?->format('Y-m-d') ?? '') }}" required>
        </div>

        <div class="">
            <label for="fecha_fin" class="form-label text-sm font-medium">Fecha de Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('fecha_fin', $periodo->fecha_fin?->format('Y-m-d') ?? '') }}" required>
        </div>

        <div class="form-check flex items-center justify-center">
            <input type="hidden" name="is_current" value="0">
            <input class="form-check-input checked:bg-verdeclaro" type="checkbox" name="is_current" id="is_current"
                value="1" {{ old('is_current', $periodo->is_current ?? false) ? 'checked' : '' }}>
            <label class="form-check-label ml-1" for="is_current"> ¿Está activo?</label>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label for="fecha_limite_matricula" class="form-label text-sm font-medium">Fecha límite de matrícula</label>
            <input type="date" name="fecha_limite_matricula" id="fecha_limite_matricula"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('fecha_limite_matricula', $periodo->fecha_limite_matricula?->format('Y-m-d') ?? '') }}"
                required>
        </div>

        <div>
            <label for="fecha_limite_pago" class="form-label text-sm font-medium">Fecha límite de pago</label>
            <input type="date" name="fecha_limite_pago" id="fecha_limite_pago"
                class="focus:ring-verdeclaro focus:border-verdeclaro block w-full shadow-md sm:text-sm rounded-full border border-verde"
                value="{{ old('fecha_limite_pago', $periodo->fecha_limite_pago?->format('Y-m-d') ?? '') }}" required>
        </div>
    </div>
</div>
