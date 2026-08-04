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

        {{-- Destinatarios --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Para quién <span class="text-red-500">*</span>
                <span class="text-xs font-normal text-gray-400 dark:text-gray-500 ml-1">(selecciona uno o varios)</span>
            </label>
            <div class="flex flex-wrap gap-2">
                @forelse ($this->usuariosAdmin as $u)
                    @php $sel = in_array($u['id'], $this->asignadosIds); @endphp
                    <button type="button"
                            wire:click="toggleAsignado({{ $u['id'] }})"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-sm transition-colors
                                   {{ $sel
                                       ? 'bg-lime-600 border-lime-600 text-white'
                                       : 'bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-lime-400 dark:hover:border-lime-500' }}">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0
                                    {{ $sel ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                            {{ strtoupper(substr($u['name'], 0, 2)) }}
                        </div>
                        <div class="text-left">
                            <p class="font-medium leading-tight text-xs">{{ $u['name'] }}</p>
                            <p class="text-[10px] {{ $sel ? 'text-lime-100' : 'text-gray-400 dark:text-gray-500' }}">{{ $u['rol'] }}</p>
                        </div>
                        @if($sel)
                            <svg class="w-3.5 h-3.5 text-white/80 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </button>
                @empty
                    <p class="text-sm text-gray-400 dark:text-gray-500 italic">No hay otros usuarios administrativos disponibles.</p>
                @endforelse
            </div>
            @error('asignadosIds')
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

        {{-- Descripción — Quill --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Descripción <span class="text-red-500">*</span>
            </label>
            <div wire:ignore
                 x-data="{
                     quill: null,
                     init() {
                         this.quill = new Quill(this.$refs.editor, {
                             theme: 'snow',
                             placeholder: 'Describe detalladamente el problema o solicitud...',
                             modules: { toolbar: [['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['link'],['clean']] }
                         });
                         const initial = @js($descripcion);
                         if (initial) this.quill.root.innerHTML = initial;
                         this.quill.on('text-change', () => {
                             const html = this.quill.root.innerHTML;
                             $wire.set('descripcion', html === '<p><br></p>' ? '' : html, false);
                         });
                     }
                 }"
                 class="rounded-xl bg-white dark:bg-gray-800 overflow-hidden focus-within:ring-2 focus-within:ring-lime-500/30 transition-all
                        {{ $errors->has('descripcion') ? 'border border-red-400 dark:border-red-600' : 'border border-gray-300 dark:border-gray-700 focus-within:border-lime-500' }}">
                <div x-ref="editor" style="min-height:140px;"></div>
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

@assets
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<style>
    .ql-toolbar.ql-snow { border: none; border-bottom: 1px solid #e2e8f0; background: #f8fafc; padding: 8px 12px; }
    .dark .ql-toolbar.ql-snow { border-bottom-color: #374151; background: #1f2937; }
    .ql-container.ql-snow { border: none; font-family: inherit; }
    .ql-editor { padding: 12px 16px; font-size: 0.875rem; line-height: 1.6; color: #1e293b; }
    .dark .ql-editor { color: #f1f5f9; background: #1f2937; }
    .ql-editor.ql-blank::before { color: #94a3b8; font-style: normal; font-size: 0.875rem; }
    .dark .ql-editor.ql-blank::before { color: #6b7280; }
    .ql-toolbar.ql-snow .ql-stroke { stroke: #64748b; }
    .ql-toolbar.ql-snow .ql-fill  { fill: #64748b; }
    .dark .ql-toolbar.ql-snow .ql-stroke { stroke: #9ca3af; }
    .dark .ql-toolbar.ql-snow .ql-fill  { fill: #9ca3af; }
    .ql-toolbar.ql-snow button:hover .ql-stroke, .ql-toolbar.ql-snow button.ql-active .ql-stroke { stroke: #84cc16; }
    .ql-toolbar.ql-snow button:hover .ql-fill,   .ql-toolbar.ql-snow button.ql-active .ql-fill   { fill:   #84cc16; }
    .ql-toolbar.ql-snow .ql-picker-label { color: #64748b; }
    .dark .ql-toolbar.ql-snow .ql-picker-label { color: #9ca3af; }
    /* Estilos para HTML renderizado en tickets */
    .ticket-content strong, .ticket-content b { font-weight: 700; }
    .ticket-content em, .ticket-content i { font-style: italic; }
    .ticket-content u { text-decoration: underline; }
    .ticket-content a { color: #2563eb; text-decoration: underline; }
    .ticket-content ul { list-style-type: disc; padding-left: 1.25rem; margin-top: 0.25rem; }
    .ticket-content ol { list-style-type: decimal; padding-left: 1.25rem; margin-top: 0.25rem; }
    .ticket-content li { margin-top: 0.125rem; }
    .ticket-content p:not(:last-child) { margin-bottom: 0.25rem; }
</style>
@endassets
