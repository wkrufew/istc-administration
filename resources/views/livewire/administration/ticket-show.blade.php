<div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

    {{-- ═══════════════════════ HEADER ══════════════════════════════════════ --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-6 py-4 flex items-start justify-between gap-4 flex-wrap">
        <div class="flex items-start gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-lime-600 to-sky-700 flex items-center justify-center shadow flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $ticket->numero }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \App\Models\Ticket::estadoColor($ticket->estado) }}">
                        {{ \App\Models\Ticket::estadoLabel($ticket->estado) }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \App\Models\Ticket::prioridadColor($ticket->prioridad) }}">
                        {{ \App\Models\Ticket::prioridadLabel($ticket->prioridad) }}
                    </span>
                </div>
                <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-0.5 leading-snug">{{ $ticket->titulo }}</h1>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    Creado por <strong class="text-gray-600 dark:text-gray-300">{{ $ticket->creador?->name }}</strong>
                    · {{ $ticket->created_at->format('d/m/Y H:i') }}
                    @if($ticket->fecha_limite)
                        · Fecha límite: <strong class="text-orange-600 dark:text-orange-400">{{ $ticket->fecha_limite->format('d/m/Y') }}</strong>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <button wire:click="toggleEdicion"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl border
                       {{ $editandoTicket ? 'bg-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-750' }}
                       transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Editar
            </button>
            <a href="{{ route('administracion.administrativa.tickets.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- ═══════════ COLUMNA PRINCIPAL ══════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Formulario de edición --}}
            @if($editandoTicket)
            <div class="rounded-2xl border border-lime-200 dark:border-lime-900/60 bg-lime-50 dark:bg-lime-950/20 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-lime-800 dark:text-lime-300">Editar ticket</h3>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Título</label>
                    <input wire:model="editTitulo" type="text"
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-lime-500/30 focus:border-lime-500 transition-colors @error('editTitulo') border-red-400 @enderror">
                    @error('editTitulo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Prioridad</label>
                        <select wire:model="editPrioridad"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-lime-500/30 transition-colors">
                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha límite</label>
                        <input wire:model="editFechaLimite" type="date"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-lime-500/30 transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Observaciones</label>
                    <textarea wire:model="editObservaciones" rows="2"
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-lime-500/30 transition-colors resize-none"></textarea>
                </div>

                <div class="flex gap-2 justify-end">
                    <button wire:click="toggleEdicion" class="px-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Cancelar</button>
                    <button wire:click="guardarEdicion" class="px-4 py-2 text-xs font-semibold rounded-xl bg-lime-600 hover:bg-lime-700 text-white transition-colors">Guardar cambios</button>
                </div>
            </div>
            @endif

            {{-- Descripción original --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Descripción</h3>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 ticket-content">
                    {!! $ticket->descripcion !!}
                </div>
            </div>

            {{-- Conversación --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden" wire:poll.20000ms.visible>
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Conversación ({{ $this->mensajes->count() }})
                    </h3>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->mensajes as $msg)
                    <div class="p-5 {{ $msg->es_nota_interna ? 'bg-yellow-50/60 dark:bg-yellow-950/10' : '' }}">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($msg->autor?->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-2">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $msg->autor?->name ?? 'Usuario' }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                                    @if($msg->es_nota_interna)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">
                                        Nota interna
                                    </span>
                                    @endif
                                </div>
                                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 ticket-content">
                                    {!! $msg->mensaje !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-sm text-gray-400 dark:text-gray-600">
                        Sin mensajes aún. Sé el primero en responder.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Responder --}}
            @if($ticket->estaAbierto())
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 space-y-3">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Responder</h3>

                @error('mensaje')
                <p class="text-xs text-red-500 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 px-3 py-2 rounded-xl">{{ $message }}</p>
                @enderror

                <div wire:ignore
                     x-data="{
                         quill: null,
                         init() {
                             this.quill = new Quill(this.$refs.editor, {
                                 theme: 'snow',
                                 placeholder: 'Escribe tu respuesta...',
                                 modules: { toolbar: [['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['link'],['clean']] }
                             });
                             this.quill.on('text-change', () => {
                                 const html = this.quill.root.innerHTML;
                                 $wire.set('mensaje', html === '<p><br></p>' ? '' : html, false);
                             });
                             $wire.on('mensaje-enviado', () => this.quill.setContents([]));
                         }
                     }"
                     class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden
                            focus-within:ring-2 focus-within:ring-lime-500/30 focus-within:border-lime-500 transition-all">
                    <div x-ref="editor" style="min-height:128px;"></div>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-3">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input wire:model="esNotaInterna" type="checkbox"
                            class="rounded border-gray-300 dark:border-gray-600 text-yellow-500 focus:ring-yellow-500">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Nota interna (solo visible para administración)</span>
                    </label>

                    <button wire:click="responder"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-lime-600 hover:bg-lime-700 transition-colors disabled:opacity-50"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="responder">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="responder">Enviar respuesta</span>
                        <span wire:loading wire:target="responder">Enviando...</span>
                    </button>
                </div>
            </div>
            @else
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 p-5 text-center text-sm text-gray-500 dark:text-gray-400">
                Este ticket está <strong>{{ \App\Models\Ticket::estadoLabel($ticket->estado) }}</strong> y no admite más respuestas.
            </div>
            @endif
        </div>

        {{-- ═══════════ COLUMNA LATERAL ════════════════════════════════════ --}}
        <div class="space-y-4">

            {{-- Cambiar estado --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 space-y-3">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</h3>
                <select wire:model="nuevoEstado"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-lime-500/30 transition-colors">
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En proceso</option>
                    <option value="cerrado">Cerrado</option>
                </select>
                <button wire:click="cambiarEstado"
                    class="w-full px-4 py-2 rounded-xl text-sm font-semibold text-white bg-lime-600 hover:bg-lime-700 transition-colors">
                    Actualizar estado
                </button>
            </div>

            {{-- Asignados --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 space-y-3">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Asignados</h3>

                {{-- Lista actual --}}
                @if($this->asignadosActuales->isEmpty())
                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">Sin asignados aún.</p>
                @else
                    <div class="space-y-2">
                        @foreach($this->asignadosActuales as $u)
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-lime-500 to-sky-600
                                                flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">{{ $u->name }}</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $u->roles->first()?->name ?? 'Admin' }}</p>
                                    </div>
                                </div>
                                @can('asignar_tickets')
                                    <button wire:click="quitarAsignado({{ $u->id }})"
                                            wire:confirm="¿Quitar la asignación de {{ $u->name }}?"
                                            class="p-1 rounded-lg text-gray-300 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400
                                                   hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Agregar nuevo --}}
                @if($this->usuariosDisponibles->isNotEmpty())
                    @can('asignar_tickets')
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-2">
                            <select wire:model="nuevoAsignadoId"
                                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800
                                       px-3 py-2 text-xs text-gray-900 dark:text-gray-100
                                       focus:outline-none focus:ring-2 focus:ring-lime-500/30 transition-colors">
                                <option value="">Agregar asignado...</option>
                                @foreach($this->usuariosDisponibles as $u)
                                    <option value="{{ $u['id'] }}">{{ $u['name'] }} — {{ $u['rol'] }}</option>
                                @endforeach
                            </select>
                            <button wire:click="agregarAsignado"
                                    @disabled(!$nuevoAsignadoId)
                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold text-white
                                           bg-sky-600 hover:bg-sky-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                Agregar
                            </button>
                        </div>
                    @endcan
                @endif
            </div>

            {{-- Detalles del ticket --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 space-y-3">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Detalles</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Creado</dt>
                        <dd class="text-gray-700 dark:text-gray-300 text-right">{{ $ticket->created_at->format('d/m/Y') }}</dd>
                    </div>
                    @if($ticket->assigned_at)
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Asignado</dt>
                        <dd class="text-gray-700 dark:text-gray-300 text-right">{{ $ticket->assigned_at->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                    @if($ticket->first_response_at)
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">1ª respuesta</dt>
                        <dd class="text-gray-700 dark:text-gray-300 text-right">{{ $ticket->first_response_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    @if($ticket->resolved_at)
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Resuelto</dt>
                        <dd class="text-green-600 dark:text-green-400 text-right">{{ $ticket->resolved_at->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                    @if($ticket->fecha_limite)
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Límite</dt>
                        <dd class="text-orange-600 dark:text-orange-400 font-medium text-right">{{ $ticket->fecha_limite->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                    @if($ticket->observaciones)
                    <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                        <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Observaciones</dt>
                        <dd class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $ticket->observaciones }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

    </div>

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
