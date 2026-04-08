<x-admin-layout>
    <div class="max-w-3xl mx-auto py-8">
        <h2 class="text-2xl font-bold mb-4">Editar Documento</h2>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('administracion.administrativa.normas-aprobadas.update', $document->id) }}" method="POST"
            enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium mb-1">Título</label>
                <input type="text" name="title" value="{{ old('title', $document->title) }}"
                    class="w-full border rounded px-3 py-2" required>
                @error('title')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Descripción</label>
                <textarea name="description" rows="4" class="w-full border rounded px-3 py-2" required>{{ old('description', $document->description) }}</textarea>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Archivo PDF</label>
                <input type="file" name="file" accept="application/pdf" class="w-full">
                @if ($document->file)
                    <p class="text-sm mt-1 flex items-center font-semibold">Archivo actual: <a
                            href="{{ asset('storage/' . $document->file) }}" target="_blank"
                            class="text-blue-600 underline flex items-center space-x-2 ml-2">
                            <svg class="size-4 fill-verdeclaro" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 512 512">
                                <path
                                    d="M0 64C0 28.7 28.7 0 64 0L224 0l0 128c0 17.7 14.3 32 32 32l128 0 0 144-208 0c-35.3 0-64 28.7-64 64l0 144-48 0c-35.3 0-64-28.7-64-64L0 64zm384 64l-128 0L256 0 384 128zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                            </svg>
                            <span>
                                Ver documento
                            </span>
                        </a>
                    </p>
                @endif
                @error('file')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="mr-2"
                    {{ old('is_active', $document->is_active) ? 'checked' : '' }}>
                <label>¿Activo?</label>
            </div>
            @error('is_active')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

</x-admin-layout>
