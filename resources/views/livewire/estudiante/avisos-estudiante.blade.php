<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" wire:poll.30000ms.visible>

    {{-- ═══════════════════════ HEADER ══════════════════════════════════════ --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Mis Avisos</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Exámenes, tareas y comunicados de tus materias del período activo.
            </p>
        </div>

        @php
            $grupoHoy     = $this->avisos['hoy']     ?? collect();
            $grupoProximo = $this->avisos['proximo']  ?? collect();
            $grupoPasado  = $this->avisos['pasado']   ?? collect();
            $hayNoLeidos  = ($grupoHoy->merge($grupoProximo))->filter(fn($a) => ! $a->leido)->count();
        @endphp

        @if($hayNoLeidos > 0)
        <button wire:click="marcarTodosLeidos"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold
                   text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Marcar todo como leído
        </button>
        @endif
    </div>

    {{-- ═══════════════════════ SIN PERIODO ════════════════════════════════ --}}
    @if(! $this->periodoActivo)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center">
        <p class="text-amber-700 font-medium">No hay un período académico activo.</p>
    </div>

    {{-- ═══════════════════════ SIN AVISOS ════════════════════════════════ --}}
    @elseif($grupoHoy->isEmpty() && $grupoProximo->isEmpty() && $grupoPasado->isEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white py-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <p class="text-slate-400 font-medium">Sin avisos por el momento</p>
        <p class="text-slate-300 text-sm mt-1">Cuando tus docentes publiquen avisos aparecerán aquí.</p>
    </div>

    @else

    {{-- ═══════════════════════ HOY ════════════════════════════════════════ --}}
    @if($grupoHoy->isNotEmpty())
    <section class="space-y-3">
        <div class="flex items-center gap-2.5">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
            </span>
            <h2 class="text-xs font-bold text-amber-600 uppercase tracking-widest">Hoy</h2>
            <span class="text-xs text-amber-500 font-medium">{{ now()->format('d/m/Y') }}</span>
        </div>

        @foreach($grupoHoy->sortBy('created_at') as $aviso)
        @include('livewire.estudiante.partials.aviso-card-estudiante', ['aviso' => $aviso, 'estado' => 'hoy'])
        @endforeach
    </section>
    @endif

    {{-- ═══════════════════════ PRÓXIMOS ══════════════════════════════════ --}}
    @if($grupoProximo->isNotEmpty())
    <section class="space-y-3">
        <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
            <h2 class="text-xs font-bold text-blue-600 uppercase tracking-widest">Próximos</h2>
        </div>

        @foreach($grupoProximo->sortBy('fecha_aviso') as $aviso)
        @include('livewire.estudiante.partials.aviso-card-estudiante', ['aviso' => $aviso, 'estado' => 'proximo'])
        @endforeach
    </section>
    @endif

    {{-- ═══════════════════════ PASADOS ════════════════════════════════════ --}}
    @if($grupoPasado->isNotEmpty())
    <details class="group">
        <summary class="flex items-center gap-2.5 cursor-pointer list-none select-none py-1">
            <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                Pasados ({{ $grupoPasado->count() }})
            </h2>
            <svg class="w-3.5 h-3.5 text-slate-400 ml-1 transition-transform group-open:rotate-180"
                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </summary>
        <div class="mt-3 space-y-3">
            @foreach($grupoPasado->sortByDesc('fecha_aviso') as $aviso)
            @include('livewire.estudiante.partials.aviso-card-estudiante', ['aviso' => $aviso, 'estado' => 'pasado'])
            @endforeach
        </div>
    </details>
    @endif

    @endif

</div>
