<x-slot name="header">Mis Solicitudes</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- NUEVA SOLICITUD --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-slate-800">Nueva Solicitud</h2>
            </div>
            <button wire:click="$toggle('showForm')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200
                       {{ $showForm ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                @if ($showForm)
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    Cerrar
                @else
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
                    Crear solicitud
                @endif
            </button>
        </div>

        @if ($showForm)
        <div class="px-6 py-5 space-y-5">
            {{-- Selector tipo --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Tipo de solicitud *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($tipos as $tipo)
                        <label
                            class="relative flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition-all duration-150
                                   {{ $tipoSolicitudId == $tipo->id ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/20' : 'border-slate-200 hover:border-blue-300 hover:bg-slate-50' }}">
                            <input type="radio" wire:model.live="tipoSolicitudId" value="{{ $tipo->id }}" class="mt-0.5 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 leading-none">{{ $tipo->nombre }}</p>
                                @if ($tipo->descripcion)
                                    <p class="text-xs text-slate-500 mt-0.5 leading-snug">{{ $tipo->descripcion }}</p>
                                @endif
                                <div class="mt-1.5 flex items-center gap-2 flex-wrap">
                                    @if ($tipo->precio > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-semibold bg-amber-50 border border-amber-200 text-amber-700">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/></svg>
                                            ${{ number_format($tipo->precio, 2) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-semibold bg-green-50 border border-green-200 text-green-700">Gratuito</span>
                                    @endif
                                    @if ($tipo->requiere_documento)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-slate-50 border border-slate-200 text-slate-500">Genera documento</span>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('tipoSolicitudId') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Precio aviso --}}
            @if ($this->tipoSeleccionado && $this->tipoSeleccionado->precio > 0)
                <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-3">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-amber-700">Esta solicitud tiene un costo de <strong>${{ number_format($this->tipoSeleccionado->precio, 2) }}</strong></p>
                        <p class="text-xs text-amber-600 mt-0.5">Una vez aprobada, se generará una obligación de pago que deberá cancelar para continuar el trámite.</p>
                    </div>
                </div>
            @endif

            {{-- Descripción --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Detalle / Motivo de la solicitud *</label>
                <textarea wire:model="descripcion" rows="4"
                    placeholder="Describa con detalle el motivo de su solicitud, información adicional relevante, etc."
                    class="w-full px-4 py-3 rounded-xl text-sm text-slate-700 placeholder-slate-400 bg-slate-50 border border-slate-200 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition resize-none"></textarea>
                <div class="flex justify-between mt-1">
                    @error('descripcion')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @else
                        <span></span>
                    @enderror
                    <span class="text-xs text-slate-400">{{ strlen($descripcion) }}/1000</span>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-2 pt-1">
                <button wire:click="$set('showForm', false)"
                    class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                    Cancelar
                </button>
                <button wire:click="enviarSolicitud" wire:loading.attr="disabled"
                    class="px-5 py-2 rounded-xl text-sm font-semibold bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-md shadow-blue-500/20 hover:shadow-blue-500/30 hover:brightness-105 disabled:opacity-60 transition">
                    <span wire:loading.remove wire:target="enviarSolicitud">Enviar Solicitud</span>
                    <span wire:loading wire:target="enviarSolicitud">Enviando…</span>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- MIS SOLICITUDES --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
            <h2 class="text-base font-semibold text-slate-800">Mis Solicitudes</h2>
            <select wire:model.live="filtroEstado" class="px-3 py-1.5 rounded-lg text-xs text-slate-600 bg-slate-50 border border-slate-200 focus:outline-none focus:border-blue-500 transition">
                <option value="">Todos los estados</option>
                @foreach (\App\Models\Solicitud::ESTADOS as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($solicitudes as $sol)
                @php
                    $badgeClass = match($sol->estado) {
                        'pendiente'      => 'bg-amber-50 border-amber-200 text-amber-700',
                        'aprobada'       => 'bg-sky-50 border-sky-200 text-sky-700',
                        'rechazada'      => 'bg-red-50 border-red-200 text-red-600',
                        'pendiente_pago' => 'bg-orange-50 border-orange-200 text-orange-700',
                        'pagada'         => 'bg-teal-50 border-teal-200 text-teal-700',
                        'en_proceso'     => 'bg-indigo-50 border-indigo-200 text-indigo-700',
                        'entregada'      => 'bg-slate-50 border-slate-200 text-slate-600',
                        'cancelada'      => 'bg-slate-50 border-slate-300 text-slate-500',
                        default          => 'bg-slate-50 border-slate-200 text-slate-600',
                    };
                    $estadoLabel = \App\Models\Solicitud::ESTADOS[$sol->estado] ?? $sol->estado;
                @endphp
                <div class="px-6 py-4 hover:bg-slate-50/50 transition-colors duration-150">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-slate-800">{{ $sol->tipoSolicitud->nombre ?? '—' }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.65rem] font-semibold border {{ $badgeClass }}">
                                    {{ $estadoLabel }}
                                </span>
                                @if ($sol->precio_aplicado > 0)
                                    <span class="text-xs font-semibold text-amber-600">${{ number_format($sol->precio_aplicado, 2) }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2">{{ $sol->descripcion }}</p>

                            {{-- Info de pago si está pendiente --}}
                            @if ($sol->estado === 'pendiente_pago')
                                <div class="flex items-center gap-1.5 mt-1">
                                    <svg class="w-3.5 h-3.5 text-orange-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span class="text-xs font-medium text-orange-600">Ir a Pagos para registrar el comprobante de cancelación.</span>
                                </div>
                            @endif

                            {{-- Notas del admin --}}
                            @if ($sol->notas_admin)
                                <div class="mt-1.5 flex items-start gap-1.5 rounded-lg bg-blue-50 border border-blue-100 px-3 py-2">
                                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs text-blue-700"><strong>Secretaría:</strong> {{ $sol->notas_admin }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-slate-400">{{ $sol->created_at->format('d/m/Y') }}</p>
                            <p class="text-[0.65rem] text-slate-300">{{ $sol->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Timeline de estados --}}
                    <div class="mt-3 flex items-center gap-0">
                        @php
                            $timeline = ['pendiente','aprobada','pendiente_pago','pagada','en_proceso','entregada'];
                            $currentIndex = array_search($sol->estado, $timeline);
                            $isRejected = $sol->estado === 'rechazada';
                            $isCanceled = $sol->estado === 'cancelada';
                        @endphp
                        @if ($isRejected || $isCanceled)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium {{ $isRejected ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                {{ $isRejected ? 'Solicitud rechazada' : 'Solicitud cancelada' }}
                            </span>
                        @else
                            @foreach ($timeline as $i => $step)
                                @php
                                    $isPast    = $currentIndex !== false && $i < $currentIndex;
                                    $isCurrent = $currentIndex !== false && $i === $currentIndex;
                                    $stepLabel = \App\Models\Solicitud::ESTADOS[$step] ?? $step;
                                    // Skip pendiente_pago if price is 0
                                    if ($step === 'pendiente_pago' && $sol->precio_aplicado == 0) continue;
                                    if ($step === 'pagada' && $sol->precio_aplicado == 0) continue;
                                @endphp
                                @if ($i > 0 && !($step === 'pendiente_pago' && $sol->precio_aplicado == 0) && !($step === 'pagada' && $sol->precio_aplicado == 0))
                                    <div class="h-px flex-1 {{ $isPast ? 'bg-blue-500' : 'bg-slate-200' }}"></div>
                                @endif
                                <div class="flex flex-col items-center gap-0.5 flex-shrink-0" title="{{ $stepLabel }}">
                                    <div class="w-3.5 h-3.5 rounded-full border-2 transition-colors
                                        {{ $isCurrent ? 'border-blue-600 bg-blue-600 ring-2 ring-blue-200' : ($isPast ? 'border-blue-500 bg-blue-500' : 'border-slate-300 bg-white') }}">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center gap-3 text-slate-400">
                        <svg class="w-12 h-12 opacity-25" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-slate-500">No tienes solicitudes aún.</p>
                        <button wire:click="$set('showForm', true)" class="text-xs font-medium text-blue-600 hover:underline">
                            Crear tu primera solicitud
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($solicitudes->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div>

</div>

@push('js')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('solicitud-enviada', () => {
            const Toast = Swal.mixin({
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 3500, timerProgressBar: true,
                didOpen: (toast) => { toast.onmouseenter = Swal.stopTimer; toast.onmouseleave = Swal.resumeTimer; }
            });
            Toast.fire({ icon: 'success', title: 'Solicitud enviada correctamente.' });
        });
    });
</script>
@endpush
