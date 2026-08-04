<div class="max-w-7xl mx-auto px-4 py-6 space-y-4" wire:poll.20000ms.visible>

    {{-- ═══════════════════════════════════════ HEADER ═══════════════════════ --}}
    <div class="bg-slate-900 border border-slate-700/50 relative overflow-hidden rounded-xl">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-lime-600 to-sky-700 flex items-center justify-center shadow-lg flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-white/90 leading-none">Tickets de Soporte</h1>
                    <p class="text-xs text-lime-400/70 tracking-widest uppercase mt-1">Administración · Soporte</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                {{-- Buscador --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" placeholder="Buscar por título o número..."
                        class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-white/80 placeholder-slate-500
                               bg-slate-800 border border-white/[0.08]
                               focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10
                               transition-all duration-200 min-w-52">
                </div>

                {{-- Toggle Vista --}}
                <div class="inline-flex rounded-xl overflow-hidden border border-slate-700 text-xs">
                    <button wire:click="setVista('lista')"
                        class="px-3 py-2 font-medium transition-colors flex items-center gap-1.5
                               {{ $vista === 'lista' ? 'bg-lime-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-slate-200' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Lista
                    </button>
                    <button wire:click="setVista('kanban')"
                        class="px-3 py-2 font-medium transition-colors flex items-center gap-1.5
                               {{ $vista === 'kanban' ? 'bg-lime-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-slate-200' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                        Kanban
                    </button>
                </div>

                {{-- Métricas --}}
                <a href="{{ route('administracion.administrativa.tickets.metricas') }}"
                   wire:navigate
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-xl hover:bg-slate-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Métricas
                </a>

                {{-- Nuevo ticket --}}
                <a href="{{ route('administracion.administrativa.tickets.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Ticket
                </a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════ STATS ════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'Abiertos',    'value' => $this->stats['abiertos'],   'color' => 'text-lime-600 dark:text-lime-400',   'bg' => 'bg-lime-50 dark:bg-lime-900/20'],
            ['label' => 'En proceso',  'value' => $this->stats['en_proceso'], 'color' => 'text-blue-600 dark:text-blue-400',   'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
            ['label' => 'Esperando',   'value' => $this->stats['esperando'],  'color' => 'text-yellow-600 dark:text-yellow-400','bg' => 'bg-yellow-50 dark:bg-yellow-900/20'],
            ['label' => 'Cerrados',    'value' => $this->stats['cerrados'],   'color' => 'text-gray-500 dark:text-gray-400',   'bg' => 'bg-gray-50 dark:bg-gray-800/50'],
        ] as $stat)
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 flex flex-col gap-1">
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
            <p class="text-2xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════ FILTROS ══════════════════════ --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 flex flex-wrap gap-3">
        @if($vista === 'lista')
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Estado</label>
            <select wire:model.live="filtroEstado"
                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-lime-500/30">
                <option value="">Todos</option>
                <option value="abierto">Abierto</option>
                <option value="en_proceso">En proceso</option>
                <option value="esperando">Esperando</option>
                <option value="resuelto">Resuelto</option>
                <option value="cerrado">Cerrado</option>
            </select>
        </div>
        @endif
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Prioridad</label>
            <select wire:model.live="filtroPrioridad"
                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-lime-500/30">
                <option value="">Todas</option>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
            </select>
        </div>
        @if($filtroEstado || $filtroPrioridad || $search)
        <div class="flex items-end">
            <button wire:click="$set('filtroEstado',''); $set('filtroPrioridad',''); $set('search','')"
                class="px-3 py-2 rounded-xl text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                Limpiar filtros
            </button>
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════ VISTA LISTA ══════════════════ --}}
    @if($vista === 'lista')
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Número</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Título</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prioridad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Creado por</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Asignado a</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->tickets as $ticket)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $ticket->numero }}</span>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $ticket->titulo }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \App\Models\Ticket::prioridadColor($ticket->prioridad) }}">
                                {{ \App\Models\Ticket::prioridadLabel($ticket->prioridad) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \App\Models\Ticket::estadoColor($ticket->estado) }}">
                                {{ \App\Models\Ticket::estadoLabel($ticket->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $ticket->creador?->name ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($ticket->asignados->isEmpty())
                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Sin asignar</span>
                            @else
                                <div class="flex items-center gap-1 flex-wrap">
                                    @foreach($ticket->asignados->take(3) as $a)
                                        <span title="{{ $a->name }}"
                                              class="inline-flex w-6 h-6 rounded-lg bg-gradient-to-br from-lime-500 to-sky-600
                                                     items-center justify-center text-white text-[10px] font-bold">
                                            {{ strtoupper(substr($a->name, 0, 2)) }}
                                        </span>
                                    @endforeach
                                    @if($ticket->asignados->count() > 3)
                                        <span class="text-xs text-gray-400 dark:text-gray-500">+{{ $ticket->asignados->count() - 3 }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('administracion.administrativa.tickets.show', $ticket) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-lime-700 dark:text-lime-400 bg-lime-50 dark:bg-lime-900/20 rounded-lg hover:bg-lime-100 dark:hover:bg-lime-900/40 transition-colors">
                                Ver
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm">No hay tickets que coincidan con los filtros.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->tickets->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
            {{ $this->tickets->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- ═══════════════════════════════════════ VISTA KANBAN ═════════════════ --}}
    @if($vista === 'kanban')
    @php
    $colColors = [
        'abierto'    => ['bar' => 'bg-lime-500',   'hdr' => 'text-lime-700 dark:text-lime-300',   'count' => 'bg-lime-100 text-lime-700 dark:bg-lime-900/40 dark:text-lime-300',  'col' => 'bg-lime-50/50 dark:bg-lime-950/20 border-lime-200 dark:border-lime-800'],
        'en_proceso' => ['bar' => 'bg-blue-500',   'hdr' => 'text-blue-700 dark:text-blue-300',   'count' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',  'col' => 'bg-blue-50/50 dark:bg-blue-950/20 border-blue-200 dark:border-blue-800'],
        'esperando'  => ['bar' => 'bg-yellow-500', 'hdr' => 'text-yellow-700 dark:text-yellow-300','count' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300','col' => 'bg-yellow-50/50 dark:bg-yellow-950/20 border-yellow-200 dark:border-yellow-800'],
        'resuelto'   => ['bar' => 'bg-green-500',  'hdr' => 'text-green-700 dark:text-green-300',  'count' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300', 'col' => 'bg-green-50/50 dark:bg-green-950/20 border-green-200 dark:border-green-800'],
        'cerrado'    => ['bar' => 'bg-gray-400',   'hdr' => 'text-gray-600 dark:text-gray-400',   'count' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',    'col' => 'bg-gray-50/50 dark:bg-gray-900/60 border-gray-200 dark:border-gray-700'],
    ];
    $transiciones = [
        'abierto'    => [['key' => 'en_proceso', 'label' => 'En proceso']],
        'en_proceso' => [['key' => 'esperando', 'label' => 'Esperando'], ['key' => 'resuelto', 'label' => 'Resuelto']],
        'esperando'  => [['key' => 'en_proceso', 'label' => 'En proceso'], ['key' => 'resuelto', 'label' => 'Resuelto']],
        'resuelto'   => [['key' => 'cerrado', 'label' => 'Cerrar']],
        'cerrado'    => [['key' => 'resuelto', 'label' => 'Reabrir']],
    ];
    @endphp

    <div class="overflow-x-auto pb-2">
        <div class="flex gap-4 min-w-max">
            @foreach($this->kanbanColumnas as $estadoKey => $col)
            @php $cc = $colColors[$estadoKey]; @endphp
            <div class="w-72 flex flex-col rounded-2xl border {{ $cc['col'] }}">
                {{-- Cabecera de columna --}}
                <div class="flex items-center gap-2 px-4 pt-3 pb-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $cc['bar'] }} flex-shrink-0"></span>
                    <span class="text-sm font-semibold {{ $cc['hdr'] }} flex-1">{{ $col['label'] }}</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $cc['count'] }}">
                        {{ $col['items']->count() }}
                    </span>
                </div>
                <div class="h-px mx-4 {{ $cc['bar'] }} opacity-30 mb-2"></div>

                {{-- Cards --}}
                <div class="flex flex-col gap-2 px-3 pb-3 min-h-24">
                    @forelse($col['items'] as $ticket)
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-3 space-y-2.5">
                        {{-- Número + prioridad --}}
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-[10px] font-semibold text-gray-400 dark:text-gray-500">{{ $ticket->numero }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold {{ \App\Models\Ticket::prioridadColor($ticket->prioridad) }}">
                                {{ \App\Models\Ticket::prioridadLabel($ticket->prioridad) }}
                            </span>
                        </div>

                        {{-- Título --}}
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-snug line-clamp-2">{{ $ticket->titulo }}</p>

                        {{-- Asignados + fecha --}}
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1">
                                @forelse($ticket->asignados->take(3) as $a)
                                    <span title="{{ $a->name }}"
                                          class="inline-flex w-5 h-5 rounded-md bg-gradient-to-br from-lime-500 to-sky-600
                                                 items-center justify-center text-white text-[9px] font-bold">
                                        {{ strtoupper(substr($a->name, 0, 2)) }}
                                    </span>
                                @empty
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 italic">Sin asignar</span>
                                @endforelse
                                @if($ticket->asignados->count() > 3)
                                    <span class="text-[10px] text-gray-400">+{{ $ticket->asignados->count() - 3 }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $ticket->created_at->format('d/m/y') }}</span>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-1 flex-wrap">
                                @foreach($transiciones[$estadoKey] as $t)
                                <button wire:click="moverEstado({{ $ticket->id }}, '{{ $t['key'] }}')"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-medium
                                               text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700
                                               hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    {{ $t['label'] }}
                                </button>
                                @endforeach
                            </div>
                            <a href="{{ route('administracion.administrativa.tickets.show', $ticket) }}"
                               class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold
                                      text-lime-700 dark:text-lime-400 bg-lime-50 dark:bg-lime-900/20
                                      hover:bg-lime-100 dark:hover:bg-lime-900/40 transition-colors">
                                Ver
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-6 text-gray-400 dark:text-gray-600">
                        <svg class="w-8 h-8 mb-1 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-xs">Sin tickets</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
