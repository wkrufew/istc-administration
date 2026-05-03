<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ═══════════════════════ HEADER ══════════════════════════════════════ --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Avisos a Estudiantes</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Publica exámenes, tareas y comunicados para tus materias del periodo activo.
            </p>
        </div>
        <button wire:click="$toggle('mostrarFormulario')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                   {{ $mostrarFormulario ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}
                   transition-colors">
            @if($mostrarFormulario)
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo aviso
            @endif
        </button>
    </div>

    {{-- ═══════════════════════ SIN PERIODO ════════════════════════════════ --}}
    @if(! $this->periodoActivo)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center">
        <p class="text-amber-700 font-medium">No hay un período académico activo en este momento.</p>
    </div>
    @else

    {{-- ═══════════════════════ SIN MATERIAS ══════════════════════════════ --}}
    @if($this->asignaciones->isEmpty())
    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-8 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-gray-500">No tienes materias asignadas en el período activo.</p>
    </div>
    @else

    {{-- ═══════════════════════ FORMULARIO NUEVO AVISO ═════════════════════ --}}
    @if($mostrarFormulario)
    <div class="rounded-2xl border border-indigo-200 bg-indigo-50/60 p-6 space-y-4">
        <h3 class="text-sm font-bold text-indigo-800 uppercase tracking-wider">Nuevo aviso</h3>

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
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Materia <span class="text-red-500">*</span></label>
                <select wire:model="asignacionId"
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
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
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Título <span class="text-red-500">*</span></label>
                <input wire:model="titulo" type="text" placeholder="Ej: Examen parcial unidad 3..."
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900
                           placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
            </div>

            {{-- Tipo --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tipo <span class="text-red-500">*</span></label>
                <select wire:model="tipo"
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                    <option value="examen">Examen</option>
                    <option value="evaluacion">Evaluación</option>
                    <option value="tarea">Tarea</option>
                    <option value="general">General</option>
                </select>
            </div>

            {{-- Fecha --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Fecha del evento <span class="text-red-500">*</span></label>
                <input wire:model="fechaAviso" type="date"
                    min="{{ now()->format('Y-m-d') }}"
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
            </div>

            {{-- Descripción --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Descripción <span class="text-red-500">*</span></label>
                <textarea wire:model="descripcion" rows="3" placeholder="Detalla el tema, instrucciones o información relevante..."
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900
                           placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors resize-none">
                </textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <button wire:click="$toggle('mostrarFormulario')"
                class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-colors">
                Cancelar
            </button>
            <button wire:click="guardar"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors disabled:opacity-50"
                wire:loading.attr="disabled" wire:target="guardar">
                <span wire:loading.remove wire:target="guardar">Publicar aviso</span>
                <span wire:loading wire:target="guardar">Publicando...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════ FILTRO POR MATERIA ═════════════════════════ --}}
    <div class="flex items-center gap-3 flex-wrap">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Filtrar:</span>
        <button wire:click="$set('asignacionId', null)"
            class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors
                   {{ is_null($asignacionId) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Todas
        </button>
        @foreach($this->asignaciones as $asig)
        <button wire:click="$set('asignacionId', {{ $asig->id }})"
            class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors
                   {{ $asignacionId === $asig->id ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $asig->materia?->name }}
            @if($asig->paralelo) · {{ $asig->paralelo->name }} @endif
        </button>
        @endforeach
    </div>

    {{-- ═══════════════════════ LISTA DE AVISOS ════════════════════════════ --}}
    @php
        $grupoHoy     = $this->avisos['hoy']     ?? collect();
        $grupoProximo = $this->avisos['proximo']  ?? collect();
        $grupoPasado  = $this->avisos['pasado']   ?? collect();
    @endphp

    @if($grupoHoy->isEmpty() && $grupoProximo->isEmpty() && $grupoPasado->isEmpty())
    <div class="rounded-2xl border border-gray-200 bg-gray-50 py-12 text-center">
        <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
        </svg>
        <p class="text-gray-400 text-sm">No hay avisos publicados aún.</p>
        <p class="text-gray-300 text-xs mt-1">Crea el primero con el botón "Nuevo aviso".</p>
    </div>
    @else

    {{-- HOY --}}
    @if($grupoHoy->isNotEmpty())
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            <h2 class="text-xs font-bold text-amber-600 uppercase tracking-widest">Hoy</h2>
        </div>
        @foreach($grupoHoy->sortBy('fecha_aviso') as $aviso)
            @include('livewire.docente.partials.aviso-card-docente', ['aviso' => $aviso])
        @endforeach
    </div>
    @endif

    {{-- PRÓXIMOS --}}
    @if($grupoProximo->isNotEmpty())
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
            <h2 class="text-xs font-bold text-blue-600 uppercase tracking-widest">Próximos</h2>
        </div>
        @foreach($grupoProximo->sortBy('fecha_aviso') as $aviso)
            @include('livewire.docente.partials.aviso-card-docente', ['aviso' => $aviso])
        @endforeach
    </div>
    @endif

    {{-- PASADOS --}}
    @if($grupoPasado->isNotEmpty())
    <details class="group">
        <summary class="flex items-center gap-2 cursor-pointer list-none select-none">
            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                Pasados ({{ $grupoPasado->count() }})
            </h2>
            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform group-open:rotate-180 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </summary>
        <div class="mt-3 space-y-3">
            @foreach($grupoPasado->sortByDesc('fecha_aviso') as $aviso)
                @include('livewire.docente.partials.aviso-card-docente', ['aviso' => $aviso])
            @endforeach
        </div>
    </details>
    @endif

    @endif
    @endif
    @endif

</div>
