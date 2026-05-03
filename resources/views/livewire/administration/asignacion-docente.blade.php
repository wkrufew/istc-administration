<div class="space-y-6">

    {{-- ================================================================
         FORMULARIO — NUEVA ASIGNACIÓN
         ================================================================ --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6">

        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Nueva Asignación</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            Selecciona período → materia → paralelo en orden. Solo aparecen combinaciones configuradas.
        </p>

        {{-- Error general --}}
        @error('general')
            <div class="mb-4 flex items-start gap-3 rounded-xl border border-red-200 dark:border-red-800
                bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ $message }}
            </div>
        @enderror

        {{-- PASO 1 — PERÍODO --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">

            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-600 text-white text-xs font-bold mr-1">1</span>
                    Período
                </label>
                <select wire:model.live="periodoId"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600
                        bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm
                        focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-3">
                    <option value="">— Seleccionar período —</option>
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id }}">
                            {{ $periodo->code }} · {{ Str::limit($periodo->description, 35) }}
                        </option>
                    @endforeach
                </select>
                @if ($periodos->isEmpty())
                    <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                        No hay períodos activos configurados.
                    </p>
                @endif
            </div>

            {{-- PASO 2 — MATERIA (combobox con búsqueda) --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full
                        {{ $periodoId ? 'bg-indigo-600 text-white' : 'bg-gray-300 dark:bg-gray-700 text-gray-500' }}
                        text-xs font-bold mr-1">2</span>
                    Materia
                </label>

                @php
                    $materiasData = $materias->map(fn($m) => [
                        'id'    => $m->id,
                        'name'  => $m->name,
                        'code'  => $m->code,
                        'label' => $m->name . ' · ' . $m->code,
                    ])->values()->toArray();

                    $selectedLabel = $materiaId
                        ? ($materias->firstWhere('id', $materiaId)?->name . ' · ' . $materias->firstWhere('id', $materiaId)?->code)
                        : '';
                @endphp

                <div wire:key="materia-combo-{{ $periodoId }}"
                     x-data="{
                         open: false,
                         search: '{{ addslashes($selectedLabel) }}',
                         items: @js($materiasData),
                         get filtered() {
                             const s = this.search.toLowerCase().trim();
                             if (!s) return this.items;
                             return this.items.filter(i =>
                                 i.name.toLowerCase().includes(s) || i.code.toLowerCase().includes(s)
                             );
                         },
                         select(item) {
                             this.search = item.label;
                             this.open   = false;
                             $wire.seleccionarMateria(item.id);
                         },
                         clear() {
                             this.search = '';
                             this.open   = false;
                             $wire.seleccionarMateria(null);
                         }
                     }"
                     @click.outside="open = false"
                     class="relative">

                    <div class="relative">
                        <input
                            type="text"
                            x-model="search"
                            @focus="open = true; search = ''"
                            @input="open = true"
                            placeholder="{{ $periodoId ? 'Escribir para buscar materia…' : 'Primero selecciona un período' }}"
                            :disabled="{{ $periodoId ? 'false' : 'true' }}"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600
                                bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm
                                focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 pl-3 pr-9
                                disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:cursor-not-allowed
                                disabled:text-gray-400 dark:disabled:text-gray-500">

                        {{-- Ícono lupa / X --}}
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg x-show="!search" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                            </svg>
                        </div>
                        <button type="button" x-show="search" @click="clear()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Dropdown --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute z-30 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-700
                             bg-white dark:bg-gray-800 shadow-xl overflow-hidden max-h-60 overflow-y-auto">

                        <template x-if="filtered.length === 0">
                            <div class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500 text-center italic">
                                Sin resultados
                            </div>
                        </template>

                        <template x-for="item in filtered" :key="item.id">
                            <div @click="select(item)"
                                class="flex items-center justify-between px-4 py-2.5 cursor-pointer
                                    hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                                <span class="text-sm text-gray-800 dark:text-gray-200 font-medium" x-text="item.name"></span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-mono ml-2 shrink-0" x-text="item.code"></span>
                            </div>
                        </template>
                    </div>
                </div>

                @if ($periodoId && $materias->isEmpty())
                    <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                        Este período no tiene materias en Materia-Período-Paralelo.
                    </p>
                @endif
            </div>

            {{-- PASO 3 — PARALELO --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full
                        {{ $materiaId ? 'bg-indigo-600 text-white' : 'bg-gray-300 dark:bg-gray-700 text-gray-500' }}
                        text-xs font-bold mr-1">3</span>
                    Paralelo
                </label>
                <select wire:model.live="paraleloId"
                    @disabled(! $materiaId)
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600
                        bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm
                        focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-3
                        disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:cursor-not-allowed
                        disabled:text-gray-400">
                    <option value="">— Seleccionar paralelo —</option>
                    @foreach ($paralelos as $paralelo)
                        <option value="{{ $paralelo->id }}">{{ $paralelo->name }}</option>
                    @endforeach
                </select>
                @if ($materiaId && $paralelos->isEmpty())
                    <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                        No hay paralelos configurados para esta materia en el período.
                    </p>
                @endif
            </div>

        </div>

        {{-- Alerta duplicado --}}
        @if ($yaExiste)
            <div class="mt-4 flex items-center gap-2 rounded-xl bg-amber-50 dark:bg-amber-900/20
                border border-amber-200 dark:border-amber-800 px-4 py-3 text-sm text-amber-700 dark:text-amber-300">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Este docente ya tiene esta asignación registrada.
            </div>
        @endif

        {{-- Botón asignar --}}
        <div class="mt-6 flex justify-end">
            <button wire:click="asignar"
                wire:loading.attr="disabled"
                @disabled($yaExiste || !$periodoId || !$materiaId || !$paraleloId)
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm
                    {{ ($yaExiste || !$periodoId || !$materiaId || !$paraleloId)
                        ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'
                        : 'bg-indigo-600 hover:bg-indigo-700 text-white' }}">
                <span wire:loading.remove wire:target="asignar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </span>
                <span wire:loading wire:target="asignar">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="asignar">Asignar</span>
                <span wire:loading wire:target="asignar">Asignando…</span>
            </button>
        </div>
    </div>

    {{-- ================================================================
         TABLA — ASIGNACIONES ACTUALES
         ================================================================ --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Asignaciones registradas</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $asignaciones->count() }} {{ $asignaciones->count() === 1 ? 'asignación' : 'asignaciones' }}
                    {{ $filtroPeriodoId ? 'en el período seleccionado' : 'en total' }}
                </p>
            </div>

            {{-- Filtro por período --}}
            <div class="flex items-center gap-2">
                <label class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Filtrar período:</label>
                <select wire:model.live="filtroPeriodoId"
                    class="rounded-xl border border-gray-300 dark:border-gray-600
                        bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm
                        focus:ring-2 focus:ring-indigo-500 py-1.5 px-3">
                    <option value="">Todos</option>
                    @foreach ($periodos as $p)
                        <option value="{{ $p->id }}">{{ $p->code }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if ($asignaciones->isEmpty())
            <div class="py-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">No hay asignaciones para mostrar.</p>
            </div>
        @else
            {{-- Desktop --}}
            <div class="hidden md:block rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-900 dark:bg-black text-white text-xs uppercase tracking-wide">
                            <th class="px-4 py-3 text-left">Período</th>
                            <th class="px-4 py-3 text-left">Materia</th>
                            <th class="px-4 py-3 text-left">Paralelo</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($asignaciones as $asignacion)
                            @php
                                $esActivo = $asignacion->periodo?->carreras
                                    ->where('pivot.is_current', true)->isNotEmpty();
                            @endphp
                            <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-lg text-xs font-bold
                                        {{ $esActivo
                                            ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                                            : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' }}">
                                        {{ $asignacion->periodo?->code ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $asignacion->materia?->name ?? '—' }}
                                    </p>
                                    <p class="text-xs text-gray-400 font-mono">
                                        {{ $asignacion->materia?->code ?? '' }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $asignacion->paralelo?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($esActivo)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                            Histórico
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        wire:click="$dispatch('confirmarEliminar', { id: {{ $asignacion->id }} })"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                            bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800
                                            hover:bg-red-100 dark:hover:bg-red-900/50 transition"
                                        title="Eliminar asignación">
                                        <svg class="w-4 h-4 fill-red-600 dark:fill-red-400"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm96 64c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="md:hidden space-y-3">
                @foreach ($asignaciones as $asignacion)
                    @php $esActivo = $asignacion->periodo?->carreras->where('pivot.is_current', true)->isNotEmpty(); @endphp
                    <div class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white dark:bg-gray-900">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 dark:text-gray-100 truncate">
                                    {{ $asignacion->materia?->name ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-400 font-mono">{{ $asignacion->materia?->code ?? '' }}</p>
                            </div>
                            @if ($esActivo)
                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                    bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                    Activo
                                </span>
                            @else
                                <span class="shrink-0 px-2 py-0.5 rounded-full text-xs font-semibold
                                    bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                    Histórico
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-0.5 mb-3">
                            <p><span class="font-medium">Período:</span> {{ $asignacion->periodo?->code ?? '—' }}</p>
                            <p><span class="font-medium">Paralelo:</span> {{ $asignacion->paralelo?->name ?? '—' }}</p>
                        </div>
                        <button wire:click="$dispatch('confirmarEliminar', { id: {{ $asignacion->id }} })"
                            class="w-full py-1.5 rounded-xl text-xs font-semibold
                                bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800
                                text-red-600 dark:text-red-400 hover:bg-red-100 transition">
                            Eliminar asignación
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- SweetAlert para confirmar eliminación y toasts --}}
<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('confirmarEliminar', (params) => {
            const d = Array.isArray(params) ? params[0] : params;
            Swal.fire({
                title: '¿Eliminar asignación?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                ...window.swalTheme?.() ?? {},
            }).then(r => {
                if (r.isConfirmed) @this.eliminar(d.id);
            });
        });

        Livewire.on('toast', (params) => {
            const d = Array.isArray(params) ? params[0] : params;
            Swal.mixin({
                toast: true, position: 'top-end', showConfirmButton: false,
                timer: 3500, timerProgressBar: true,
            }).fire({ icon: d.tipo, title: d.mensaje });
        });

    });
</script>
