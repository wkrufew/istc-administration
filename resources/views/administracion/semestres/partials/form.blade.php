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

{{-- Formulario para crear o editar un semestre --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
    <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
        <input type="text" name="name" id="name"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('name', $semestre->name ?? '') }}" required>
    </div>

    <div>
        <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Código</label>
        <input type="text" name="code" id="code"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('code', $semestre->code ?? '') }}" required>
    </div>

    <div>
        <label for="order" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Orden</label>
        <input type="number" name="order" id="order"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('order', $semestre->order ?? '') }}" required>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label for="creditos_minimos" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Créditos Mínimos</label>
        <input type="number" name="creditos_minimos" id="creditos_minimos" min="0"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('creditos_minimos', $semestre->creditos_minimos ?? 15) }}" required>
    </div>

    <div>
        <label for="creditos_maximos" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Créditos Máximos</label>
        <input type="number" name="creditos_maximos" id="creditos_maximos" min="0"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('creditos_maximos', $semestre->creditos_maximos ?? 25) }}" required>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label for="carrera_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Carrera</label>
        <select name="carrera_id" id="carrera_id"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
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

    <div class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4">
        <div class="pt-0.5">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                {{ old('is_active', $semestre->is_active ?? false) ? 'checked' : '' }}
                class="h-5 w-5 rounded border-gray-300 text-verdeclaro focus:ring-verdeclaro">
        </div>
        <div>
            <label for="is_active" class="font-semibold text-gray-800 dark:text-gray-100">¿Semestre activo?</label>
            <p class="text-sm text-gray-500 dark:text-gray-400">Si está activo aparecerá en matrículas.</p>
        </div>
    </div>
</div>
