@php
    $estado = $aviso->estado;
    $borderColor = match($estado) {
        'hoy'    => 'border-l-amber-400 border-amber-100 bg-amber-50/50',
        'proximo'=> 'border-l-blue-400 border-blue-100 bg-blue-50/30',
        default  => 'border-l-gray-300 border-gray-100 bg-gray-50/50',
    };
@endphp

<div class="rounded-2xl border border-l-4 {{ $borderColor }} p-4 flex items-start justify-between gap-3 group">
    <div class="flex items-start gap-3 min-w-0">
        {{-- Ícono tipo --}}
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5
            {{ $estado === 'hoy' ? 'bg-amber-100' : ($estado === 'proximo' ? 'bg-blue-100' : 'bg-gray-100') }}">
            <svg class="w-4.5 h-4.5 {{ $estado === 'hoy' ? 'text-amber-600' : ($estado === 'proximo' ? 'text-blue-600' : 'text-gray-400') }}"
                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ \App\Models\Aviso::tipoIcono($aviso->tipo) }}"/>
            </svg>
        </div>

        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ \App\Models\Aviso::tipoColor($aviso->tipo) }}">
                    {{ \App\Models\Aviso::tipoLabel($aviso->tipo) }}
                </span>
                <span class="text-xs font-semibold
                    {{ $estado === 'hoy' ? 'text-amber-600' : ($estado === 'proximo' ? 'text-blue-600' : 'text-gray-400') }}">
                    {{ $aviso->tiempo_faltante }}
                </span>
                <span class="text-xs text-gray-400">
                    {{ $aviso->asignacionDocente?->materia?->name }}
                    @if($aviso->asignacionDocente?->paralelo) · {{ $aviso->asignacionDocente->paralelo->name }} @endif
                </span>
            </div>
            <p class="text-sm font-semibold text-gray-800 leading-snug truncate">{{ $aviso->titulo }}</p>
            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $aviso->descripcion }}</p>
            <p class="text-xs text-gray-300 mt-1">{{ $aviso->fecha_aviso->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Botón eliminar --}}
    <button wire:click="eliminar({{ $aviso->id }})"
        wire:confirm="¿Eliminar este aviso? Los estudiantes ya no podrán verlo."
        class="opacity-0 group-hover:opacity-100 flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center
               text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
    </button>
</div>
