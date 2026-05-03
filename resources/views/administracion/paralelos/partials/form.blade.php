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

{{-- Formulario para crear o editar un paralelo --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
    <div class="sm:col-span-2">
        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
        <input type="text" name="name" id="name"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('name', $paralelo->name ?? '') }}" required>
    </div>

    <div>
        <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Código</label>
        <input type="text" name="code" id="code"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('code', $paralelo->code ?? '') }}" required>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
    <div>
        <label for="cupo_maximo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Cupo Máximo</label>
        <input type="number" name="cupo_maximo" id="cupo_maximo" min="0"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('cupo_maximo', $paralelo->cupo_maximo ?? 30) }}" required>
    </div>

    <div>
        <label for="cupo_actual" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Cupo Actual</label>
        <input type="number" name="cupo_actual" id="cupo_actual" min="0"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
            value="{{ old('cupo_actual', $paralelo->cupo_actual ?? 0) }}" required>
    </div>

    <div class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4">
        <div class="pt-0.5">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                {{ old('is_active', $paralelo->is_active ?? true) ? 'checked' : '' }}
                class="h-5 w-5 rounded border-gray-300 text-verdeclaro focus:ring-verdeclaro">
        </div>
        <div>
            <label for="is_active" class="font-semibold text-gray-800 dark:text-gray-100">¿Activo?</label>
            <p class="text-sm text-gray-500 dark:text-gray-400">Paralelo habilitado para uso.</p>
        </div>
    </div>
</div>
