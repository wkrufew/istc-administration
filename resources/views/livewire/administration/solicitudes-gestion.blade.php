<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Gestión de Solicitudes</h1>
                    <p class="text-xs text-lime-400/70 tracking-widest uppercase mt-1">Administración · Solicitudes</p>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="flex items-center gap-2 flex-wrap">
                <div class="relative min-w-[160px]">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </div>
                    <input wire:model.live="search" class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-slate-700 dark:text-white/80 placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all" placeholder="Buscar estudiante…">
                </div>

                <select wire:model.live="filtroEstado" class="px-3 py-2 rounded-full text-xs text-slate-700 dark:text-white/80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 transition-all">
                    <option value="">Todos los estados</option>
                    @foreach (\App\Models\Solicitud::ESTADOS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filtroTipo" class="px-3 py-2 rounded-full text-xs text-slate-700 dark:text-white/80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 transition-all">
                    <option value="">Todos los tipos</option>
                    @foreach ($this->tiposSolicitudes as $t)
                        <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- CONTADORES --}}
    <div class="grid grid-cols-3 gap-4">
        @php
            $conteos = $this->conteos;
            $cards = [
                ['label' => 'Pendientes', 'value' => $conteos['pendientes'], 'color' => 'amber'],
                ['label' => 'En trámite', 'value' => $conteos['en_proceso'],  'color' => 'sky'],
                ['label' => 'Hoy',        'value' => $conteos['total_hoy'],   'color' => 'lime'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-white/[0.06] px-5 py-4 shadow-sm">
                <p class="text-[0.65rem] font-semibold tracking-[0.12em] uppercase text-slate-400 dark:text-slate-500 mb-1">{{ $card['label'] }}</p>
                <p class="text-2xl font-semibold text-slate-800 dark:text-white/90">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- TABLA --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-white/[0.06] overflow-hidden shadow-xl shadow-slate-200/80 dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04]">
        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/[0.05]">
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Estudiante</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Tipo</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-28">Precio</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-36">Estado</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-36">Fecha</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-44">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($solicitudes as $solicitud)
                        @php
                            $badgeClass = match($solicitud->estado) {
                                'pendiente'      => 'bg-amber-500/10 border-amber-500/20 text-amber-500',
                                'aprobada'       => 'bg-sky-500/10 border-sky-500/20 text-sky-500',
                                'rechazada'      => 'bg-red-500/10 border-red-500/20 text-red-400',
                                'pendiente_pago' => 'bg-orange-500/10 border-orange-500/20 text-orange-500',
                                'pagada'         => 'bg-teal-500/10 border-teal-500/20 text-teal-400',
                                'en_proceso'     => 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400',
                                'entregada'      => 'bg-slate-500/10 border-slate-500/20 text-slate-400',
                                'cancelada'      => 'bg-slate-500/10 border-slate-500/20 text-slate-500',
                                default          => 'bg-slate-500/10 border-slate-500/20 text-slate-400',
                            };
                            $estadoLabel = \App\Models\Solicitud::ESTADOS[$solicitud->estado] ?? $solicitud->estado;
                        @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-green-700/60 to-sky-700/60 border border-white/[0.08] flex items-center justify-center flex-shrink-0">
                                        <span class="text-[0.6rem] font-semibold text-white/70 uppercase">{{ substr($solicitud->estudiante->name ?? '?', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-white/75 leading-none">{{ $solicitud->estudiante->name ?? '—' }}</p>
                                        <p class="text-[0.65rem] text-slate-400 dark:text-slate-500 mt-0.5">{{ $solicitud->estudiante->cedula ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-600 dark:text-slate-300">{{ $solicitud->tipoSolicitud->nombre ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($solicitud->precio_aplicado > 0)
                                    <span class="text-sm font-semibold text-slate-700 dark:text-white/80">${{ number_format($solicitud->precio_aplicado, 2) }}</span>
                                @else
                                    <span class="text-xs text-slate-400">Gratuito</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium border {{ $badgeClass }}">
                                    {{ $estadoLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $solicitud->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1 flex-wrap">
                                    @if ($solicitud->estado === 'pendiente')
                                        <button wire:click="abrirGestion({{ $solicitud->id }}, 'aprobar')"
                                            class="px-2.5 py-1 rounded-lg text-[0.65rem] font-medium bg-lime-500/10 border border-lime-500/20 text-lime-600 hover:bg-lime-500/20 transition">
                                            Aprobar
                                        </button>
                                        <button wire:click="abrirGestion({{ $solicitud->id }}, 'rechazar')"
                                            class="px-2.5 py-1 rounded-lg text-[0.65rem] font-medium bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500/20 transition">
                                            Rechazar
                                        </button>
                                    @elseif (in_array($solicitud->estado, ['pendiente_pago', 'pagada', 'en_proceso']))
                                        @php
                                            $siguienteLabel = match($solicitud->estado) {
                                                'pendiente_pago' => 'Confirmar Pago',
                                                'pagada'         => 'En Proceso',
                                                'en_proceso'     => 'Marcar Entregado',
                                                default          => 'Avanzar',
                                            };
                                        @endphp
                                        <button wire:click="abrirAvanzar({{ $solicitud->id }})"
                                            class="px-2.5 py-1 rounded-lg text-[0.65rem] font-medium bg-sky-500/10 border border-sky-500/20 text-sky-500 hover:bg-sky-500/20 transition">
                                            {{ $siguienteLabel }}
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 italic">—</span>
                                    @endif

                                    {{-- Ver descripción --}}
                                    @if ($solicitud->descripcion)
                                        <button title="{{ $solicitud->descripcion }}"
                                            class="w-6 h-6 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="text-sm">No hay solicitudes que coincidan con los filtros.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($solicitudes->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50 dark:bg-black/10">
                {{ $solicitudes->links() }}
            </div>
        @endif

        <div class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50"></div>
    </div>

    {{-- MODAL APROBAR / RECHAZAR --}}
    @if ($showModal && $solicitudId)
        @php $sol = \App\Models\Solicitud::with(['estudiante', 'tipoSolicitud'])->find($solicitudId); @endphp
        @if ($sol)
        <div x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarModal"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-white/[0.08]">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">
                        {{ $accion === 'aprobar' ? 'Aprobar Solicitud' : 'Rechazar Solicitud' }}
                    </h3>
                    <button wire:click="cerrarModal" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    {{-- Resumen solicitud --}}
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/[0.06] p-4 space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Estudiante</span>
                            <span class="font-medium text-slate-700 dark:text-white/80">{{ $sol->estudiante->name }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Tipo</span>
                            <span class="font-medium text-slate-700 dark:text-white/80">{{ $sol->tipoSolicitud->nombre }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Precio</span>
                            <span class="font-semibold {{ $sol->precio_aplicado > 0 ? 'text-amber-500' : 'text-lime-500' }}">
                                {{ $sol->precio_aplicado > 0 ? '$'.number_format($sol->precio_aplicado,2) : 'Gratuito' }}
                            </span>
                        </div>
                        <div class="pt-1 border-t border-slate-200 dark:border-white/[0.06]">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Descripción del estudiante:</p>
                            <p class="text-xs text-slate-600 dark:text-slate-300 italic">{{ $sol->descripcion }}</p>
                        </div>
                    </div>

                    @if ($accion === 'aprobar' && $sol->precio_aplicado > 0)
                        <div class="rounded-lg bg-amber-500/8 border border-amber-500/20 px-4 py-2.5 text-xs text-amber-600 dark:text-amber-400">
                            Al aprobar se generará automáticamente una obligación de pago de <strong>${{ number_format($sol->precio_aplicado,2) }}</strong>.
                        </div>
                    @endif

                    {{-- Notas --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                            Notas / Observaciones {{ $accion === 'rechazar' ? '(obligatorio)' : '(opcional)' }}
                        </label>
                        <textarea wire:model="notas" rows="3"
                            placeholder="{{ $accion === 'rechazar' ? 'Motivo del rechazo…' : 'Observaciones adicionales…' }}"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition resize-none"></textarea>
                        @error('notas') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] flex justify-end gap-2">
                    <button wire:click="cerrarModal" class="px-4 py-2 rounded-lg text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Cancelar
                    </button>
                    <button wire:click="procesarAccion" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-xs font-semibold text-white shadow transition disabled:opacity-50
                               {{ $accion === 'aprobar' ? 'bg-gradient-to-r from-lime-500 to-green-600 hover:brightness-110' : 'bg-gradient-to-r from-red-500 to-rose-600 hover:brightness-110' }}">
                        <span wire:loading.remove>{{ $accion === 'aprobar' ? 'Aprobar' : 'Rechazar' }}</span>
                        <span wire:loading>Procesando…</span>
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif

    {{-- MODAL AVANZAR ESTADO --}}
    @if ($showModalAvanzar && $avanzarId)
        @php
            $solAv = \App\Models\Solicitud::with(['estudiante','tipoSolicitud'])->find($avanzarId);
            $flujo = ['pendiente_pago'=>'pagada','pagada'=>'en_proceso','en_proceso'=>'entregada'];
            $sig = $flujo[$solAv?->estado ?? ''] ?? null;
        @endphp
        @if ($solAv && $sig)
        <div x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarAvanzar"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-white/[0.08]">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">Avanzar Estado</h3>
                    <button wire:click="cerrarAvanzar" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Confirmar cambio de estado de la solicitud de
                        <strong class="text-slate-800 dark:text-white/90">{{ $solAv->estudiante->name }}</strong>:
                    </p>
                    <div class="flex items-center gap-3 justify-center py-3">
                        <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/[0.08]">
                            {{ \App\Models\Solicitud::ESTADOS[$solAv->estado] }}
                        </span>
                        <svg class="w-4 h-4 text-lime-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-lime-500/10 border border-lime-500/20 text-lime-600">
                            {{ \App\Models\Solicitud::ESTADOS[$sig] }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Notas (opcional)</label>
                        <textarea wire:model="notasAvance" rows="2" placeholder="Observaciones…"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition resize-none"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] flex justify-end gap-2">
                    <button wire:click="cerrarAvanzar" class="px-4 py-2 rounded-lg text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Cancelar
                    </button>
                    <button wire:click="confirmarAvance" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-gradient-to-r from-lime-500 to-green-600 text-white shadow hover:brightness-110 disabled:opacity-50 transition">
                        <span wire:loading.remove>Confirmar</span>
                        <span wire:loading>Procesando…</span>
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif

</div>
