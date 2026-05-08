

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-5" wire:poll.30000ms.visible>

        @php
            $grupoHoy     = $this->avisos['hoy']    ?? collect();
            $grupoProximo = $this->avisos['proximo'] ?? collect();
            $grupoPasado  = $this->avisos['pasado']  ?? collect();
            $hayNoLeidos  = ($grupoHoy->merge($grupoProximo))->filter(fn($a) => ! $a->leido)->count();
        @endphp

        {{-- ══ HEADER ═══════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                                flex items-center justify-center shadow-sm shadow-blue-500/20 flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-slate-800 leading-tight">Mis Avisos</h1>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Exámenes, tareas y comunicados de tus materias del período activo.
                        </p>
                    </div>
                </div>

                @if($hayNoLeidos > 0)
                <button wire:click="marcarTodosLeidos"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold
                           text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200
                           transition-colors shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Marcar todo como leído
                    <span class="ml-0.5 inline-flex items-center justify-center w-4 h-4 rounded-full
                                 bg-blue-600 text-white text-[10px] font-bold">
                        {{ $hayNoLeidos }}
                    </span>
                </button>
                @endif
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
            <p class="text-amber-700 font-semibold text-sm">No hay un período académico activo.</p>
            <p class="text-amber-500 text-xs mt-1">Consulta con tu secretaría para más información.</p>
        </div>

        {{-- ══ SIN AVISOS ═══════════════════════════════════════════════════════ --}}
        @elseif($grupoHoy->isEmpty() && $grupoProximo->isEmpty() && $grupoPasado->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-14 text-center">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-slate-500 font-semibold text-sm">Sin avisos por el momento</p>
            <p class="text-slate-400 text-xs mt-1">Cuando tus docentes publiquen avisos aparecerán aquí.</p>
        </div>

        @else

        {{-- ══ HOY ══════════════════════════════════════════════════════════════ --}}
        @if($grupoHoy->isNotEmpty())
        <section class="space-y-3">
            <div class="flex items-center gap-2.5 px-1">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                </span>
                <h2 class="text-xs font-bold text-amber-600 uppercase tracking-widest">Hoy</h2>
                <span class="text-xs text-amber-400 font-medium">{{ now()->format('d/m/Y') }}</span>
                <span class="text-xs text-amber-400/70 font-medium ml-auto">
                    {{ $grupoHoy->count() }} {{ $grupoHoy->count() === 1 ? 'aviso' : 'avisos' }}
                </span>
            </div>
            @foreach($grupoHoy->sortByDesc('created_at') as $aviso)
                @include('livewire.estudiante.partials.aviso-card-estudiante', ['aviso' => $aviso, 'estado' => 'hoy'])
            @endforeach
        </section>
        @endif

        {{-- ══ PRÓXIMOS ═════════════════════════════════════════════════════════ --}}
        @if($grupoProximo->isNotEmpty())
        <section class="space-y-3">
            <div class="flex items-center gap-2.5 px-1">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                <h2 class="text-xs font-bold text-blue-600 uppercase tracking-widest">Próximos</h2>
                <span class="text-xs text-blue-400/70 font-medium ml-auto">
                    {{ $grupoProximo->count() }} {{ $grupoProximo->count() === 1 ? 'aviso' : 'avisos' }}
                </span>
            </div>
            @foreach($grupoProximo->sortBy('fecha_aviso') as $aviso)
                @include('livewire.estudiante.partials.aviso-card-estudiante', ['aviso' => $aviso, 'estado' => 'proximo'])
            @endforeach
        </section>
        @endif

        {{-- ══ PASADOS ═══════════════════════════════════════════════════════════ --}}
        @if($grupoPasado->isNotEmpty() || $this->totalPasados > 0)
        <div>
            <button wire:click="toggleSeccionPasados"
                    class="flex items-center gap-2.5 px-1 cursor-pointer select-none
                           hover:opacity-80 transition-opacity py-1 w-full text-left">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pasados</h2>
                <span class="text-xs text-slate-300 font-medium">
                    {{ $grupoPasado->count() }}
                    @if($this->totalPasados > $grupoPasado->count())
                        de {{ $this->totalPasados }}
                    @endif
                </span>
                <svg class="w-3.5 h-3.5 text-slate-400 ml-auto transition-transform duration-200
                            {{ $seccionPasadosAbierta ? 'rotate-180' : '' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            @if($seccionPasadosAbierta)
            <div class="mt-3 space-y-3">
                @foreach($grupoPasado as $aviso)
                    @include('livewire.estudiante.partials.aviso-card-estudiante', ['aviso' => $aviso, 'estado' => 'pasado'])
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

        @once
        <style>
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
        @endonce

    </div>
