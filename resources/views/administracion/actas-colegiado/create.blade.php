<x-admin-layout>
    <div
        class="max-w-3xl mx-auto bg-white dark:bg-gray-900 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 p-5 sm:p-6 mb-6">

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Crear Acta Colegiado
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Sube el documento PDF del acta.
                </p>
            </div>
            <a href="{{ route('administracion.administrativa.actas-colegiado.index') }}"
                class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200 px-4 py-2.5 rounded-xl transition font-semibold shadow-sm w-full sm:w-auto">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>
        </div>

        {{-- Errores --}}
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

        <form action="{{ route('administracion.administrativa.actas-colegiado.store') }}" method="POST"
            enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Título <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
                    value="{{ old('title') }}" required>
                @error('title')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Descripción <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="4"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition"
                    required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Archivo (PDF) <span class="text-red-500">*</span>
                </label>
                <input type="file" name="file" accept="application/pdf"
                    class="block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-lime-600 file:text-white hover:file:bg-lime-700 cursor-pointer"
                    required>
                @error('file')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4">
                <div class="pt-0.5">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-gray-300 text-verdeclaro focus:ring-verdeclaro" checked>
                </div>
                <div>
                    <label class="font-semibold text-gray-800 dark:text-gray-100">¿Activo?</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400">El documento será visible públicamente.</p>
                </div>
            </div>
            @error('is_active')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-lime-600 hover:bg-lime-700 text-white px-5 py-2.5 rounded-xl transition font-semibold shadow-sm">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar Acta
                </button>
                <a href="{{ route('administracion.administrativa.actas-colegiado.index') }}"
                    class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200 px-5 py-2.5 rounded-xl transition font-semibold shadow-sm">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
