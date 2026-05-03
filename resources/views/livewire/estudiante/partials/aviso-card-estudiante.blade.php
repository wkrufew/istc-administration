@php
    $leido = $aviso->leido ?? false;
    $containerClass = match($estado) {
        'hoy'    => 'border border-amber-200 bg-white shadow-sm shadow-amber-100/60',
        'proximo'=> 'border border-slate-200 bg-white shadow-sm',
        default  => 'border border-slate-100 bg-slate-50/60',
    };
    $accentClass = match($estado) {
        'hoy'    => 'border-l-amber-400',
        'proximo'=> 'border-l-blue-400',
        default  => 'border-l-slate-300',
    };
    $iconoBg = match($estado) {
        'hoy'    => 'bg-amber-100',
        'proximo'=> 'bg-blue-100',
        default  => 'bg-slate-100',
    };
    $iconoColor = match($estado) {
        'hoy'    => 'text-amber-600',
        'proximo'=> 'text-blue-600',
        default  => 'text-slate-400',
    };
@endphp

<div class="rounded-2xl border-l-4 {{ $containerClass }} {{ $accentClass }} p-4 transition-all duration-200
            {{ $leido ? 'opacity-60' : '' }}">

    <div class="flex items-start gap-3">

        {{-- Ícono tipo --}}
        <div class="w-10 h-10 rounded-xl {{ $iconoBg }} flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-5 h-5 {{ $iconoColor }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ \App\Models\Aviso::tipoIcono($aviso->tipo) }}"/>
            </svg>
        </div>

        <div class="flex-1 min-w-0">

            {{-- Badges de cabecera --}}
            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ \App\Models\Aviso::tipoColor($aviso->tipo) }}">
                    {{ \App\Models\Aviso::tipoLabel($aviso->tipo) }}
                </span>

                {{-- Tiempo --}}
                <span class="text-xs font-bold
                    {{ $estado === 'hoy' ? 'text-amber-600' : ($estado === 'proximo' ? 'text-blue-600' : 'text-slate-400') }}">
                    {{ $aviso->tiempo_faltante }}
                </span>

                @if($leido)
                <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Leído
                </span>
                @endif
            </div>

            {{-- Título --}}
            <p class="text-sm font-bold text-slate-800 leading-snug">{{ $aviso->titulo }}</p>

            {{-- Descripción --}}
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">{{ $aviso->descripcion }}</p>

            {{-- Metadatos --}}
            <div class="flex items-center gap-3 mt-2.5 flex-wrap">
                <span class="flex items-center gap-1 text-xs text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ $aviso->asignacionDocente?->materia?->name ?? '—' }}
                    @if($aviso->asignacionDocente?->paralelo)
                        · {{ $aviso->asignacionDocente->paralelo->name }}
                    @endif
                </span>
                <span class="flex items-center gap-1 text-xs text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $aviso->asignacionDocente?->docente?->name ?? '—' }}
                </span>
                <span class="flex items-center gap-1 text-xs text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $aviso->fecha_aviso->format('d/m/Y') }}
                </span>
            </div>
        </div>

        {{-- Botón marcar leído --}}
        @if(! $leido && $estado !== 'pasado')
        <button wire:click="marcarLeido({{ $aviso->id }})"
            class="flex-shrink-0 mt-0.5 flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold
                   text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors whitespace-nowrap">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Marcar leído
        </button>
        @endif
    </div>
</div>
