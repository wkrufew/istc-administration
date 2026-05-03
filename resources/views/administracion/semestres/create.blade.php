<x-admin-layout>
    <div
        class="max-w-7xl mx-auto bg-white dark:bg-gray-900 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-800 p-5 sm:p-6 mb-6">

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Crear Semestre
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Completa los datos para registrar un nuevo semestre.
                </p>
            </div>
            <a href="{{ route('administracion.administrativa.semestres.index') }}"
                class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200 px-4 py-2.5 rounded-xl transition font-semibold shadow-sm w-full sm:w-auto">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>
        </div>

        <form action="{{ route('administracion.administrativa.semestres.store') }}" method="POST">
            @csrf

            @include('administracion.semestres.partials.form')

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-lime-600 hover:bg-lime-700 text-white px-5 py-2.5 rounded-xl transition font-semibold shadow-sm">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar Semestre
                </button>
                <a href="{{ route('administracion.administrativa.semestres.index') }}"
                    class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200 px-5 py-2.5 rounded-xl transition font-semibold shadow-sm">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
