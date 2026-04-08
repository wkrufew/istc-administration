<x-admin-layout>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Formulario para editar un Semestre</h2>

            <form action="{{ route('administracion.administrativa.semestres.update', $semestre) }}" method="POST">
                @csrf
                @method('PUT')

                @include('administracion.semestres.partials.form')

                {{-- <div class="flex justify-center space-x-4">
                    <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-verde py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-verdeclaro focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Actualizar Semestre
                    </button>
                    <a href="{{ route('administracion.administrativa.semestres.index') }}"
                        class="inline-flex justify-center rounded-md border border-transparent bg-neutral-800 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2">Cancelar</a>
                </div> --}}
                <div class="flex justify-center space-x-4">
                    <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Actualizar Semestre
                    </button>
                    <a href="{{ route('administracion.administrativa.semestres.index') }}"
                        class="inline-flex justify-center rounded-md border border-transparent bg-neutral-800 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
