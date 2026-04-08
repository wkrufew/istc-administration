<x-admin-layout>
    <div class="max-w-3xl mx-auto mt-8 p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-semibold mb-4">Crear Acta</h2>

        {{-- aqui mostrar todos los errores del formulario en un for que recorra todos los errores --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('administracion.administrativa.actas-colegiado.store') }}" method="POST"
            enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium">Título</label>
                <input type="text" name="title" class="w-full border rounded px-3 py-2" value="{{ old('title') }}"
                    required>
                @error('title')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block font-medium">Descripción</label>
                <textarea name="description" class="w-full border rounded px-3 py-2" rows="4" required>{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block font-medium">Archivo (PDF)</label>
                <input type="file" name="file" accept="application/pdf" class="w-full border rounded px-3 py-2"
                    required>
                @error('file')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="mr-2" checked>
                <label>¿Activo?</label>
                <br>
                @error('is_active')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Guardar
            </button>
        </form>
    </div>
</x-admin-layout>
