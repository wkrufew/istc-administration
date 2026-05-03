<div class="max-w-3xl mx-auto px-4 py-6 space-y-4">

    {{-- ═══════════════════════ HEADER ══════════════════════════════════════ --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-6 py-4 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Nuevo Ticket</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Describe tu solicitud para que el equipo administrativo pueda atenderte.</p>
        </div>
        <a href="{{ route('administracion.administrativa.tickets.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    {{-- ═══════════════════════ FORMULARIO ══════════════════════════════════ --}}
    <form wire:submit="guardar" class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 space-y-5">

        {{-- Errores globales --}}
        @if($errors->any())
        <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 p-4">
            <ul class="list-disc list-inside space-y-1 text-sm text-red-600 dark:text-red-400">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Título --}}
        <div>
            <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Título <span class="text-red-500">*</span>
            </label>
            <input id="titulo" wire:model="titulo" type="text" placeholder="Describe brevemente el problema..."
                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800
                       px-4 py-2.5 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                       focus:outline-none focus:ring-2 focus:ring-lime-500/30 focus:border-lime-500
                       transition-colors @error('titulo') border-red-400 dark:border-red-600 @enderror">
            @error('titulo')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Prioridad + Fecha límite --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="prioridad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Prioridad <span class="text-red-500">*</span>
                </label>
                <select id="prioridad" wire:model="prioridad"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800
                           px-4 py-2.5 text-gray-900 dark:text-gray-100
                           focus:outline-none focus:ring-2 focus:ring-lime-500/30 focus:border-lime-500 transition-colors">
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="urgente">Urgente</option>
                </select>
                @error('prioridad')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fecha_limite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Fecha límite <span class="text-xs text-gray-400 font-normal">(opcional)</span>
                </label>
                <input id="fecha_limite" wire:model="fecha_limite" type="date"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800
                           px-4 py-2.5 text-gray-900 dark:text-gray-100
                           focus:outline-none focus:ring-2 focus:ring-lime-500/30 focus:border-lime-500 transition-colors">
                @error('fecha_limite')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Descripción — CKEditor --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Descripción <span class="text-red-500">*</span>
            </label>
            <div wire:ignore>
                <div id="ck-descripcion" class="rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 min-h-40 text-gray-900 dark:text-gray-100"></div>
            </div>
            @error('descripcion')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Observaciones --}}
        <div>
            <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Observaciones internas <span class="text-xs text-gray-400 font-normal">(opcional)</span>
            </label>
            <textarea id="observaciones" wire:model="observaciones" rows="3"
                placeholder="Notas adicionales..."
                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800
                       px-4 py-2.5 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                       focus:outline-none focus:ring-2 focus:ring-lime-500/30 focus:border-lime-500 transition-colors resize-none">
            </textarea>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('administracion.administrativa.tickets.index') }}"
               class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300
                      bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                      hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white
                       bg-lime-600 hover:bg-lime-700 transition-colors disabled:opacity-50"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Crear Ticket</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Guardando...
                </span>
            </button>
        </div>
    </form>
</div>

@push('js')
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener('livewire:navigated', () => initCKEditor());
    document.addEventListener('DOMContentLoaded', () => initCKEditor());

    let ckDescripcion;
    function initCKEditor() {
        const el = document.getElementById('ck-descripcion');
        if (!el || el._ckInitialized) return;
        el._ckInitialized = true;

        ClassicEditor.create(el, {
            toolbar: ['bold','italic','underline','|','bulletedList','numberedList','|','blockQuote','|','undo','redo'],
        }).then(editor => {
            ckDescripcion = editor;
            editor.model.document.on('change:data', () => {
                @this.set('descripcion', editor.getData());
            });
        });
    }
</script>
@endpush
