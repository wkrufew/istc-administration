<div class="max-w-7xl mx-auto bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl p-4 mb-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Periodos</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Administración de periodos académicos</p>
        </div>
        <a href="{{ route('administracion.administrativa.periodos.create') }}"
            class="inline-flex items-center justify-center gap-2 bg-lime-600 text-white px-5 py-2.5 rounded-xl
            hover:bg-lime-700 transition font-medium shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Crear Periodo
        </a>
    </div>

    {{-- Buscador --}}
    <div class="mb-5">
        <div class="relative max-w-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
            </div>
            <input wire:model.live.debounce.350ms="search"
                type="text"
                placeholder="Buscar por código o descripción…"
                class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                    bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100
                    pl-9 pr-4 py-2.5 shadow-sm
                    focus:border-lime-500 focus:ring-lime-500 dark:focus:border-lime-500
                    placeholder:text-gray-400 dark:placeholder:text-gray-500">
            @if ($search)
                <button wire:click="$set('search', '')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- ===== TABLA DESKTOP ===== --}}
    <div class="hidden md:block rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-900 dark:bg-black text-white text-xs uppercase tracking-wide">
                    <th class="w-8 px-3 py-3"></th>
                    <th class="px-4 py-3 text-left">Código</th>
                    <th class="px-4 py-3 text-left">Descripción</th>
                    <th class="px-4 py-3 text-left">Fecha periodo</th>
                    <th class="px-4 py-3 text-left">Límite matrícula / pago</th>
                    <th class="px-4 py-3 text-center">Carreras</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($periodos as $periodo)
                    @php
                        $activas  = $periodo->carreras->filter(fn($c) => $c->pivot->is_current);
                        $expanded = $expandedId === $periodo->id;
                    @endphp

                    {{-- Fila principal --}}
                    <tr wire:click="toggle({{ $periodo->id }})"
                        class="cursor-pointer transition
                            {{ $expanded
                                ? 'bg-lime-50 dark:bg-lime-900/20'
                                : 'bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800/60' }}">

                        {{-- Chevron --}}
                        <td class="px-3 py-3 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200
                                    {{ $expanded ? 'rotate-90' : '' }}"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </td>

                        {{-- Código --}}
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-lg
                                {{ $activas->isNotEmpty()
                                    ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}
                                text-xs font-bold tracking-wide">
                                {{ $periodo->code }}
                            </span>
                        </td>

                        {{-- Descripción --}}
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200 max-w-xs truncate">
                            {{ $periodo->description }}
                        </td>

                        {{-- Fecha período --}}
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $periodo->fecha_inicio->isoFormat('D MMM Y') }}</span>
                                <span class="text-xs text-gray-400">→ {{ $periodo->fecha_fin->isoFormat('D MMM Y') }}</span>
                            </div>
                        </td>

                        {{-- Límites --}}
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                            <div class="flex flex-col">
                                <span class="text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Matrícula:</span>
                                    {{ $periodo->fecha_limite_matricula->isoFormat('D MMM Y') }}
                                </span>
                                <span class="text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Pago:</span>
                                    {{ $periodo->fecha_limite_pago->isoFormat('D MMM Y') }}
                                </span>
                            </div>
                        </td>

                        {{-- Resumen carreras --}}
                        <td class="px-4 py-3 text-center" wire:click.stop>
                            @php
                                $total   = $periodo->carreras->count();
                                $activas = $periodo->carreras->filter(fn($c) => $c->pivot->is_current)->count();
                            @endphp
                            @if ($total === 0)
                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Sin carreras</span>
                            @else
                                <div class="flex flex-col items-center gap-0.5">
                                    <span class="text-lg font-bold
                                        {{ $activas > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                                        {{ $total }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $activas > 0 ? $activas . ' activa' . ($activas > 1 ? 's' : '') : 'cerradas' }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        {{-- Acciones principales --}}
                        <td class="px-4 py-3" wire:click.stop>
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('administracion.administrativa.periodos.edit', $periodo) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                    bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800
                                    hover:bg-green-100 dark:hover:bg-green-900/50 transition"
                                    title="Editar">
                                    <svg class="w-4 h-4 fill-green-600 dark:fill-green-400"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                        <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/>
                                    </svg>
                                </a>

                                <button
                                    onclick="Swal.fire({
                                        title: '¿Eliminar periodo?',
                                        text: 'Esta acción no se puede deshacer.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, eliminar',
                                        cancelButtonText: 'Cancelar',
                                    }).then(r => {
                                        if (r.isConfirmed) document.getElementById('del-{{ $periodo->id }}').submit();
                                    })"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                    bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800
                                    hover:bg-red-100 dark:hover:bg-red-900/50 transition"
                                    title="Eliminar">
                                    <svg class="w-4 h-4 fill-red-600 dark:fill-red-400"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                        <path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm96 64c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16z"/>
                                    </svg>
                                </button>
                                <form id="del-{{ $periodo->id }}"
                                    action="{{ route('administracion.administrativa.periodos.destroy', $periodo) }}"
                                    method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Panel expandido con carreras --}}
                    @if ($expanded)
                        <tr class="bg-lime-50/60 dark:bg-lime-900/10">
                            <td colspan="7" class="px-6 py-4">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-xs font-bold uppercase tracking-widest text-lime-700 dark:text-lime-400">
                                        Carreras vinculadas al periodo {{ $periodo->code }}
                                    </p>
                                    <button wire:click.stop="$dispatch('openGestionCarreras', { periodoId: {{ $periodo->id }} })"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg
                                        bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                        Gestionar carreras
                                    </button>
                                </div>

                                @if ($periodo->carreras->isEmpty())
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic text-center py-4">
                                        No hay carreras vinculadas a este periodo.
                                    </p>
                                @else
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach ($periodo->carreras as $carrera)
                                            @php $esCurrent = $carrera->pivot->is_current; @endphp
                                            <div class="flex items-center justify-between gap-3 rounded-xl border px-4 py-3
                                                {{ $esCurrent
                                                    ? 'border-green-200 dark:border-green-800 bg-white dark:bg-gray-900'
                                                    : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40' }}">
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate">
                                                        {{ $carrera->name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $carrera->code }}
                                                    </p>
                                                </div>

                                                <div class="flex items-center gap-2 shrink-0">
                                                    {{-- Badge estado --}}
                                                    @if ($esCurrent)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                                            bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                                            Activo
                                                        </span>
                                                        {{-- Botón cerrar --}}
                                                        <button
                                                            wire:click.stop="$dispatch('confirmCerrar', { periodoId: {{ $periodo->id }}, carreraId: {{ $carrera->id }}, periodoCod: '{{ $periodo->code }}', carreraNombre: '{{ addslashes($carrera->name) }}' })"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg
                                                            bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300
                                                            border border-orange-200 dark:border-orange-800
                                                            hover:bg-orange-200 dark:hover:bg-orange-900/50 transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                            </svg>
                                                            Cerrar
                                                        </button>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                                            bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                                            Cerrado
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif

                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                            @if ($search)
                                No se encontraron periodos que coincidan con "<strong>{{ $search }}</strong>".
                            @else
                                No hay ningún periodo registrado.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación desktop --}}
    @if ($periodos->hasPages())
        <div class="hidden md:flex items-center justify-between mt-4 px-1">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Mostrando {{ $periodos->firstItem() }}–{{ $periodos->lastItem() }}
                de {{ $periodos->total() }} periodos
            </p>
            <div>
                {{ $periodos->links() }}
            </div>
        </div>
    @endif

    {{-- ===== CARDS MOBILE ===== --}}
    <div class="grid grid-cols-1 gap-4 md:hidden mt-4">
        @forelse ($periodos as $periodo)
            @php
                $activas  = $periodo->carreras->filter(fn($c) => $c->pivot->is_current);
                $expanded = $expandedId === $periodo->id;
            @endphp

            <div class="border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                {{-- Cabecera del card (siempre visible) --}}
                <div wire:click="toggle({{ $periodo->id }})"
                    class="flex items-start justify-between gap-3 p-4 cursor-pointer
                    {{ $expanded ? 'bg-lime-50 dark:bg-lime-900/20' : 'bg-white dark:bg-gray-900' }}">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                                {{ $activas->isNotEmpty()
                                    ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
                                {{ $periodo->code }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $periodo->description }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            {{ $periodo->fecha_inicio->isoFormat('D MMM Y') }} → {{ $periodo->fecha_fin->isoFormat('D MMM Y') }}
                        </p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-400 shrink-0 transition-transform duration-200 {{ $expanded ? 'rotate-90' : '' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>

                {{-- Panel expandido mobile --}}
                @if ($expanded)
                    <div class="border-t border-gray-100 dark:border-gray-800 p-4 bg-white dark:bg-gray-900 space-y-4">

                        {{-- Fechas --}}
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-semibold mb-0.5">Límite matrícula</p>
                                <p class="text-gray-700 dark:text-gray-200">{{ $periodo->fecha_limite_matricula->isoFormat('D MMM Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-semibold mb-0.5">Límite pago</p>
                                <p class="text-gray-700 dark:text-gray-200">{{ $periodo->fecha_limite_pago->isoFormat('D MMM Y') }}</p>
                            </div>
                        </div>

                        {{-- Carreras --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-bold uppercase tracking-widest text-lime-700 dark:text-lime-400">Carreras</p>
                                <button wire:click.stop="$dispatch('openGestionCarreras', { periodoId: {{ $periodo->id }} })"
                                    class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Gestionar →
                                </button>
                            </div>

                            @if ($periodo->carreras->isEmpty())
                                <p class="text-sm text-gray-400 italic">Sin carreras vinculadas</p>
                            @else
                                <div class="space-y-2">
                                    @foreach ($periodo->carreras as $carrera)
                                        @php $esCurrent = $carrera->pivot->is_current; @endphp
                                        <div class="flex items-center justify-between gap-2 rounded-xl border px-3 py-2
                                            {{ $esCurrent
                                                ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20'
                                                : 'border-gray-200 dark:border-gray-700' }}">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $carrera->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $carrera->code }}</p>
                                            </div>
                                            @if ($esCurrent)
                                                <button
                                                    wire:click.stop="$dispatch('confirmCerrar', { periodoId: {{ $periodo->id }}, carreraId: {{ $carrera->id }}, periodoCod: '{{ $periodo->code }}', carreraNombre: '{{ addslashes($carrera->name) }}' })"
                                                    class="shrink-0 px-2.5 py-1 text-xs font-semibold rounded-lg
                                                    bg-orange-500 text-white hover:bg-orange-600 transition">
                                                    Cerrar
                                                </button>
                                            @else
                                                <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">Cerrado</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Acciones principales --}}
                        <div class="flex gap-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                            <a href="{{ route('administracion.administrativa.periodos.edit', $periodo) }}"
                                class="flex-1 text-center px-4 py-2 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 transition text-sm">
                                Editar
                            </a>
                            <button
                                onclick="Swal.fire({
                                    title: '¿Eliminar periodo?',
                                    text: 'Esta acción no se puede deshacer.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#dc2626',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Sí, eliminar',
                                    cancelButtonText: 'Cancelar',
                                }).then(r => {
                                    if (r.isConfirmed) document.getElementById('del-m-{{ $periodo->id }}').submit();
                                })"
                                class="flex-1 px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition text-sm">
                                Eliminar
                            </button>
                            <form id="del-m-{{ $periodo->id }}"
                                action="{{ route('administracion.administrativa.periodos.destroy', $periodo) }}"
                                method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center text-gray-500 dark:text-gray-400 py-10">
                @if ($search)
                    No se encontraron periodos que coincidan con "<strong>{{ $search }}</strong>".
                @else
                    No hay ningún periodo registrado.
                @endif
            </div>
        @endforelse
    </div>

    {{-- Paginación mobile --}}
    @if ($periodos->hasPages())
        <div class="md:hidden mt-4">
            {{ $periodos->links() }}
        </div>
    @endif

    {{-- Modal gestión carreras --}}
    @livewire('administration.gestion-carrera-periodo')

</div>

{{-- Confirmación SweetAlert para cerrar periodo --}}
<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('confirmCerrar', (params) => {
            const d = Array.isArray(params) ? params[0] : params;
            Swal.fire({
                title: `¿Cerrar periodo ${d.periodoCod}?`,
                html: `El periodo se cerrará para <strong>${d.carreraNombre}</strong>.<br>Los saldos COLEGIATURA pendientes se arrastrarán al siguiente periodo.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, cerrar',
                cancelButtonText: 'Cancelar',
                ...{background: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
                    color:      document.documentElement.classList.contains('dark') ? '#f9fafb'  : '#111827'},
            }).then(r => {
                if (r.isConfirmed) {
                    Livewire.dispatch('cerrarPeriodo', { periodoId: d.periodoId, carreraId: d.carreraId });
                }
            });
        });

        Livewire.on('cerrarPeriodo', (params) => {
            const d = Array.isArray(params) ? params[0] : params;
            @this.cerrar(d.periodoId, d.carreraId);
        });

        Livewire.on('toast', (params) => {
            const d = Array.isArray(params) ? params[0] : params;
            const Toast = Swal.mixin({
                toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true,
            });
            Toast.fire({ icon: d.tipo, title: d.mensaje });
        });
    });
</script>
