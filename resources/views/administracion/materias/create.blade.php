<x-admin-layout>
    <div class="max-w-5xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">

            <form action="{{ route('administracion.administrativa.materias.store') }}" method="POST">
                @csrf

                @include('administracion.materias.partials.form')

                <!-- Botón Crear -->
                <div class="flex justify-center space-x-4">
                    <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Guardar Materia
                    </button>
                    <a href="{{ route('administracion.administrativa.materias.index') }}"
                        class="inline-flex justify-center rounded-md border border-transparent bg-neutral-800 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
