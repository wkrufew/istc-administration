<div>
 <x-slot name="header">Avisos</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Intro --}}
        {{-- <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 leading-tight">
                        Sistema de Asistencias
                    </h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Selecciona la materia o si deseas elegir una fecha anterior pulsa en Buscar otra fecha para gestionar las asistencias de estduiantes.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-xl shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium text-emerald-700">
                    {{ now()->format('d/m/Y') }}
                </span>
            </div>
        </div> --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-5">

        {{-- ══ HEADER ═══════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600
                                flex items-center justify-center shadow-sm shadow-emerald-500/20 flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-slate-800 leading-tight">Avisos a Estudiantes</h1>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Publica exámenes, tareas y comunicados para tus materias del período activo.
                        </p>
                    </div>
                </div>

                <button wire:click="$toggle('mostrarFormulario')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                           {{ $mostrarFormulario
                                ? 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                : 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm shadow-emerald-500/20' }}">
                    @if($mostrarFormulario)
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    @else
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo aviso
                    @endif
                </button>
            </div>
        </div>

        {{-- ══ SIN PERIODO ══════════════════════════════════════════════════════ --}}
        @if(! $this->periodoActivo)
        <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-6 text-center">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <p class="text-amber-700 font-semibold text-sm">No hay un período académico activo en este momento.</p>
            <p class="text-amber-500 text-xs mt-1">Comunícate con administración para activar un período.</p>
        </div>
        @else

        {{-- ══ SIN MATERIAS ═════════════════════════════════════════════════════ --}}
        @if($this->asignaciones->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-12 text-center">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p class="text-slate-500 font-medium text-sm">No tienes materias asignadas en el período activo.</p>
            <p class="text-slate-400 text-xs mt-1">Contacta a administración si crees que esto es un error.</p>
        </div>
        @else

        {{-- ══ FORMULARIO NUEVO AVISO ════════════════════════════════════════════ --}}
        @if($mostrarFormulario)
        <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <h3 class="text-sm font-bold text-white tracking-wide">Nuevo aviso</h3>
            </div>

            <div class="p-6 space-y-4">
                @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Materia --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Materia <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="asignacionId"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-900
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500
                                   focus:bg-white transition-all">
                            @foreach($this->asignaciones as $asig)
                                <option value="{{ $asig->id }}">
                                    {{ $asig->materia?->name ?? '—' }}
                                    @if($asig->paralelo) — {{ $asig->paralelo->name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Título --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Título <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="titulo" type="text"
                            placeholder="Ej: Examen parcial unidad 3..."
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-900
                                   placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30
                                   focus:border-emerald-500 focus:bg-white transition-all">
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="tipo"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-900
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500
                                   focus:bg-white transition-all">
                            <!-- <option value="examen">Examen</option> -->
                            <option value="evaluacion">Evaluación</option>
                            <option value="tarea">Tarea</option>
                            <option value="general">General</option>
                        </select>
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Fecha del evento <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="fechaAviso" type="date"
                            min="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-900
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500
                                   focus:bg-white transition-all">
                    </div>

                    {{-- Descripción (Quill editor) --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Descripción <span class="text-red-500">*</span>
                        </label>
                        <div
                            wire:ignore
                            x-data="{
                                quill: null,
                                init() {
                                    this.quill = new Quill(this.$refs.quillEditor, {
                                        theme: 'snow',
                                        placeholder: 'Detalla el tema, instrucciones o información relevante...',
                                        modules: {
                                            toolbar: [
                                                ['bold', 'italic', 'underline'],
                                                [{ list: 'ordered' }, { list: 'bullet' }],
                                                ['link'],
                                                ['clean']
                                            ]
                                        }
                                    });
                                    const initial = @js($descripcion);
                                    if (initial) this.quill.root.innerHTML = initial;
                                    this.quill.on('text-change', () => {
                                        const html = this.quill.root.innerHTML;
                                        $wire.set('descripcion', html === '<p><br></p>' ? '' : html, false);
                                    });
                                }
                            }"
                            class="rounded-xl border border-slate-300 bg-white overflow-hidden
                                   focus-within:ring-2 focus-within:ring-emerald-500/30 focus-within:border-emerald-500 transition-all">
                            <div x-ref="quillEditor" style="min-height:120px;font-size:0.875rem;"></div>
                        </div>
                        @error('descripcion')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-1">
                    <button wire:click="$toggle('mostrarFormulario')"
                        class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-slate-100
                               hover:bg-slate-200 transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="guardar"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600
                               hover:bg-emerald-700 transition-colors shadow-sm disabled:opacity-50"
                        wire:loading.attr="disabled" wire:target="guardar">
                        <span wire:loading.remove wire:target="guardar">Publicar aviso</span>
                        <span wire:loading wire:target="guardar">Publicando...</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ══ FILTRO POR MATERIA ════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-4 py-3.5">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider mr-1">Filtrar:</span>
                <button wire:click="$set('asignacionId', null)"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-200
                           {{ is_null($asignacionId)
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                    Todas
                </button>
                @foreach($this->asignaciones as $asig)
                <button wire:click="$set('asignacionId', {{ $asig->id }})"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-200
                           {{ $asignacionId === $asig->id
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                    {{ $asig->materia?->name }}
                    @if($asig->paralelo)
                        <span class="opacity-60">· {{ $asig->paralelo->name }}</span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        {{-- ══ LISTA DE AVISOS ══════════════════════════════════════════════════ --}}
        @php
            $grupoHoy     = $this->avisos['hoy']    ?? collect();
            $grupoProximo = $this->avisos['proximo'] ?? collect();
            $grupoPasado  = $this->avisos['pasado']  ?? collect();
        @endphp

        @if($grupoHoy->isEmpty() && $grupoProximo->isEmpty() && $grupoPasado->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-14 text-center">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <p class="text-slate-500 font-semibold text-sm">No hay avisos publicados aún.</p>
            <p class="text-slate-400 text-xs mt-1">Crea el primero con el botón "Nuevo aviso".</p>
        </div>
        @else

        {{-- HOY --}}
        @if($grupoHoy->isNotEmpty())
        <div class="space-y-3">
            <div class="flex items-center gap-2.5 px-1">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <h2 class="text-xs font-bold text-amber-600 uppercase tracking-widest">Hoy</h2>
                <span class="text-xs text-amber-400/70 font-medium">{{ $grupoHoy->count() }} {{ $grupoHoy->count() === 1 ? 'aviso' : 'avisos' }}</span>
            </div>
            @foreach($grupoHoy->sortByDesc('created_at') as $aviso)
                @include('livewire.docente.partials.aviso-card-docente', ['aviso' => $aviso])
            @endforeach
        </div>
        @endif

        {{-- PRÓXIMOS --}}
        @if($grupoProximo->isNotEmpty())
        <div class="space-y-3">
            <div class="flex items-center gap-2.5 px-1">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <h2 class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Próximos</h2>
                <span class="text-xs text-emerald-400/70 font-medium">{{ $grupoProximo->count() }} {{ $grupoProximo->count() === 1 ? 'aviso' : 'avisos' }}</span>
            </div>
            @foreach($grupoProximo->sortBy('fecha_aviso') as $aviso)
                @include('livewire.docente.partials.aviso-card-docente', ['aviso' => $aviso])
            @endforeach
        </div>
        @endif

        {{-- PASADOS --}}
        @if($grupoPasado->isNotEmpty() || $this->totalPasados > 0)
        <div>
            <button wire:click="toggleSeccionPasados"
                    class="flex items-center gap-2.5 px-1 cursor-pointer select-none
                           hover:opacity-80 transition-opacity w-full text-left">
                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pasados</h2>
                <span class="text-xs text-slate-300 font-medium">
                    {{ $grupoPasado->count() }}
                    @if($this->totalPasados > $grupoPasado->count())
                        de {{ $this->totalPasados }}
                    @endif
                </span>
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 ml-auto
                            {{ $seccionPasadosAbierta ? 'rotate-180' : '' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            @if($seccionPasadosAbierta)
            <div class="mt-3 space-y-3">
                @foreach($grupoPasado as $aviso)
                    @include('livewire.docente.partials.aviso-card-docente', ['aviso' => $aviso])
                @endforeach

                @if($this->totalPasados > $grupoPasado->count())
                <div class="text-center pt-1">
                    <button wire:click="cargarMasPasados"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                                   text-slate-500 bg-white border border-slate-200 hover:bg-slate-50
                                   hover:border-slate-300 shadow-sm transition-all duration-200 disabled:opacity-50">
                        <span wire:loading.remove wire:target="cargarMasPasados">
                            Ver más
                            <span class="text-slate-400 font-normal">
                                ({{ $this->totalPasados - $grupoPasado->count() }} restantes)
                            </span>
                        </span>
                        <span wire:loading wire:target="cargarMasPasados">
                            <svg class="w-4 h-4 animate-spin inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Cargando...
                        </span>
                    </button>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        @endif
        @endif
        @endif

    </div>
    </div>
</div>

@assets
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<style>
    .ql-toolbar.ql-snow {
        border: none;
        border-bottom: 1px solid #e2e8f0;
        background-color: #f8fafc;
        padding: 8px 12px;
    }
    .ql-container.ql-snow {
        border: none;
        font-family: 'Plus Jakarta Sans', 'Figtree', sans-serif;
    }
    .ql-editor {
        padding: 12px 16px;
        font-size: 0.875rem;
        color: #1e293b;
        line-height: 1.6;
        min-height: 120px;
    }
    .ql-editor.ql-blank::before {
        color: #94a3b8;
        font-style: normal;
        font-size: 0.875rem;
    }
    .ql-toolbar.ql-snow .ql-stroke { stroke: #64748b; }
    .ql-toolbar.ql-snow .ql-fill  { fill:   #64748b; }
    .ql-toolbar.ql-snow button:hover .ql-stroke,
    .ql-toolbar.ql-snow button.ql-active .ql-stroke { stroke: #059669; }
    .ql-toolbar.ql-snow button:hover .ql-fill,
    .ql-toolbar.ql-snow button.ql-active .ql-fill   { fill:   #059669; }
    .ql-toolbar.ql-snow .ql-picker-label { color: #64748b; }
    /* Estilos para el contenido renderizado en las cards */
    .aviso-content strong, .aviso-content b { font-weight: 700; }
    .aviso-content em, .aviso-content i { font-style: italic; }
    .aviso-content u { text-decoration: underline; }
    .aviso-content a { color: #2563eb; text-decoration: underline; }
    .aviso-content a:hover { color: #1d4ed8; }
    .aviso-content ul { list-style-type: disc; padding-left: 1.25rem; margin-top: 0.25rem; }
    .aviso-content ol { list-style-type: decimal; padding-left: 1.25rem; margin-top: 0.25rem; }
    .aviso-content li { margin-top: 0.125rem; }
    .aviso-content p:not(:last-child) { margin-bottom: 0.25rem; }
</style>
@endassets
