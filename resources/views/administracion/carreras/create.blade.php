<x-admin-layout>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Formulario de Carreras</h2>

            <form action="{{ route('administracion.administrativa.carreras.store') }}" method="POST">
                @csrf

                @include('administracion.carreras.partials.form')

                <!-- Botón Crear -->
                <div class="flex justify-center space-x-4">
                    <button type="submit"
                        class="inline-flex justify-center rounded-full border border-transparent bg-lime-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2">
                        Guardar Carrera
                    </button>
                    <a href="{{ route('administracion.administrativa.carreras.index') }}"
                        class="inline-flex justify-center rounded-full border border-transparent bg-neutral-700 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
