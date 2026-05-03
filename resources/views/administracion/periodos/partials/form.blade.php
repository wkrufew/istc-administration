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

{{-- Formulario para crear o editar un periodo --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Detalle</label>
        <input type="text" name="description" id="description"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('description', $periodo->description ?? '') }}" required>
    </div>

    <div>
        <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Código</label>
        <input type="text" name="code" id="code"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('code', $periodo->code ?? '') }}" required>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label for="fecha_inicio" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Fecha de Inicio</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('fecha_inicio', $periodo->fecha_inicio?->format('Y-m-d') ?? '') }}" required>
    </div>

    <div>
        <label for="fecha_fin" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Fecha de Fin</label>
        <input type="date" name="fecha_fin" id="fecha_fin"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('fecha_fin', $periodo->fecha_fin?->format('Y-m-d') ?? '') }}" required>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label for="fecha_limite_matricula" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Fecha límite de matrícula</label>
        <input type="date" name="fecha_limite_matricula" id="fecha_limite_matricula"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('fecha_limite_matricula', $periodo->fecha_limite_matricula?->format('Y-m-d') ?? '') }}"
            required>
    </div>

    <div>
        <label for="fecha_limite_pago" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Fecha límite de pago</label>
        <input type="date" name="fecha_limite_pago" id="fecha_limite_pago"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('fecha_limite_pago', $periodo->fecha_limite_pago?->format('Y-m-d') ?? '') }}" required>
    </div>
</div>
