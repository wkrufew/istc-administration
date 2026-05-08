@php
    $estado = $aviso->estado;
    $accent = match($estado) {
        'hoy'     => ['border' => 'border-l-amber-400',  'icon_bg' => 'bg-amber-100',  'icon_text' => 'text-amber-600',  'badge_bg' => 'bg-amber-50',  'time_text' => 'text-amber-600'],
        'proximo' => ['border' => 'border-l-emerald-400','icon_bg' => 'bg-emerald-100','icon_text' => 'text-emerald-600','badge_bg' => 'bg-emerald-50','time_text' => 'text-emerald-600'],
        default   => ['border' => 'border-l-slate-300',  'icon_bg' => 'bg-slate-100',  'icon_text' => 'text-slate-400',  'badge_bg' => 'bg-slate-50',  'time_text' => 'text-slate-400'],
    };
@endphp

<div class="group bg-white rounded-2xl border border-slate-200/80 border-l-4 {{ $accent['border'] }}
            shadow-sm hover:shadow-md transition-all duration-200 p-4 flex items-start justify-between gap-3">

    <div class="flex items-start gap-3.5 min-w-0">
        {{-- Ícono tipo --}}
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $accent['icon_bg'] }}">
            <svg class="w-5 h-5 {{ $accent['icon_text'] }}"
                 fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="{{ \App\Models\Aviso::tipoIcono($aviso->tipo) }}"/>
            </svg>
        </div>

        <div class="min-w-0 flex-1">
            {{-- Badges superiores --}}
            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border
                             {{ \App\Models\Aviso::tipoColor($aviso->tipo) }}">
                    {{ \App\Models\Aviso::tipoLabel($aviso->tipo) }}
                </span>
                <span class="text-xs font-semibold {{ $accent['time_text'] }}">
                    {{ $aviso->tiempo_faltante }}
                </span>
            </div>

            {{-- Título --}}
            <p class="text-sm font-bold text-slate-800 leading-snug truncate">{{ $aviso->titulo }}</p>

            {{-- Descripción --}}
            <div class="aviso-content text-xs text-slate-500 mt-1 leading-relaxed">
                {!! $aviso->descripcion !!}
            </div>

            {{-- Meta inferior --}}
            <div class="flex items-center gap-3 mt-2">
                <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ $aviso->asignacionDocente?->materia?->name ?? '—' }}
                    @if($aviso->asignacionDocente?->paralelo)
                        <span class="text-slate-300">·</span>
                        {{ $aviso->asignacionDocente->paralelo->name }}
                    @endif
                </span>
                <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $aviso->fecha_aviso->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Botón eliminar --}}
    <button wire:click="eliminar({{ $aviso->id }})"
            wire:confirm="¿Eliminar este aviso? Los estudiantes ya no podrán verlo."
            class="opacity-0 group-hover:opacity-100 flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center
                   text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all duration-200 mt-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
    </button>
</div>
