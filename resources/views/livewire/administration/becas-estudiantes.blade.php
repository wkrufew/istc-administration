<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-amber-500 to-orange-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-amber-600 via-orange-500 to-yellow-400 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Becas por Estudiante</h1>
                    <p class="text-xs text-amber-400/70 tracking-widest uppercase mt-1">Gestión · Becas</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('administracion.administrativa.tipos-beca.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Tipos de Beca
                </a>
                <div class="relative min-w-[200px] max-w-sm">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search"
                        class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-slate-700 dark:text-white/80 placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/10 transition-all duration-200"
                        placeholder="Buscar estudiante…">
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-white/[0.06] overflow-hidden shadow-xl shadow-slate-200/80 dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04]">
        <div class="h-px bg-gradient-to-r from-transparent via-amber-500/30 to-transparent"></div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/[0.05]">
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Estudiante</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Cédula</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Beca Activa</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-20">% Desc.</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-36">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($estudiantes as $est)
                        @php $becaActiva = $est->becasAplicadas->first(); @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-slate-700 dark:text-white/75">{{ $est->name }}</div>
                                <div class="text-xs text-slate-400">{{ $est->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ $est->cedula ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($becaActiva)
                                    <div>
                                        <span class="text-xs font-medium text-amber-600 dark:text-amber-400">{{ $becaActiva->tipoBeca?->nombre ?? '—' }}</span>
                                        <div class="text-[0.65rem] text-slate-400 mt-0.5">
                                            Desde {{ $becaActiva->fecha_asignacion->format('d/m/Y') }}
                                            @if ($becaActiva->porcentaje_discapacidad)
                                                · Disc. {{ $becaActiva->porcentaje_discapacidad }}%
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">Sin beca</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($becaActiva)
                                    <span class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ number_format($becaActiva->porcentaje_aplicado, 0) }}%</span>
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($becaActiva)
                                        <button wire:click="abrirModalEditar({{ $becaActiva->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.72rem] font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800 hover:text-amber-500 hover:border-amber-500/30 hover:bg-amber-500/[0.06] transition-all duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            Editar
                                        </button>
                                        <button wire:click="abrirRevocar({{ $becaActiva->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.72rem] font-medium text-red-500/70 border border-red-200/40 dark:border-red-500/10 bg-red-50/60 dark:bg-red-900/[0.06] hover:text-red-600 hover:border-red-400/50 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Revocar
                                        </button>
                                    @else
                                        <button wire:click="abrirModalAsignar({{ $est->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.72rem] font-medium text-amber-600 border border-amber-200/60 dark:border-amber-500/20 bg-amber-50/60 dark:bg-amber-900/[0.06] hover:bg-amber-50 dark:hover:bg-amber-900/10 hover:border-amber-400/50 transition-all duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                            Asignar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="text-sm">No se encontraron estudiantes.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($estudiantes->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50 dark:bg-black/10">
                {{ $estudiantes->links() }}
            </div>
        @endif

        <div class="h-0.5 bg-gradient-to-r from-amber-600 via-orange-500 to-yellow-400 opacity-50"></div>
    </div>

    {{-- MODAL ASIGNAR / EDITAR --}}
    @if ($showModal)
        @php
            $esDiscapacidad = false;
            if ($tipoBecaId) {
                $tipoBecaSel = $tiposBeca->firstWhere('id', $tipoBecaId);
                $esDiscapacidad = $tipoBecaSel && $tipoBecaSel->categoria === 'Discapacidad';
            }
        @endphp
        <div x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarModal"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-white/[0.08] max-h-[90vh] overflow-y-auto"
                x-on:keydown.escape.window="$wire.cerrarModal()">

                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center justify-between sticky top-0 bg-white dark:bg-slate-900 z-10">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">
                        {{ $becaId ? 'Editar Beca Asignada' : 'Asignar Beca' }}
                    </h3>
                    <button wire:click="cerrarModal" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">

                    {{-- Tipo de beca --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Tipo de Beca *</label>
                        <select wire:model.live="tipoBecaId"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/10 transition">
                            <option value="">— Seleccionar —</option>
                            @foreach ($tiposBeca as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }} ({{ number_format($tipo->porcentaje_descuento, 0) }}%)</option>
                            @endforeach
                        </select>
                        @error('tipoBecaId') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if ($esDiscapacidad)
                        {{-- % Discapacidad CONADIS --}}
                        <div class="p-4 bg-teal-50/60 dark:bg-teal-950/20 border border-teal-200 dark:border-teal-900 rounded-xl space-y-3">
                            <p class="text-xs font-semibold text-teal-700 dark:text-teal-400">Beca por Discapacidad — Datos CONADIS</p>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                                    % de Discapacidad (según carnet CONADIS) *
                                </label>
                                <div class="relative">
                                    <input wire:model.live="porcentajeDiscapacidad" type="number" min="35" max="100" placeholder="Ej: 60"
                                        class="w-full pr-8 px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-teal-500/50 focus:ring-2 focus:ring-teal-500/10 transition">
                                    <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-sm font-medium">%</span>
                                </div>
                                <p class="text-xs text-teal-600 dark:text-teal-400 mt-1">
                                    35-49% → 50% beca · 50-74% → 75% · ≥75% → 100%
                                </p>
                                @error('porcentajeDiscapacidad') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                    {{-- % Aplicado --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                            % de Descuento Aplicado *
                            @if($esDiscapacidad)
                                <span class="text-teal-500 font-normal">(calculado automáticamente)</span>
                            @endif
                        </label>
                        <div class="relative">
                            <input wire:model="porcentajeAplicado" type="number" step="0.01" min="0" max="100" placeholder="30"
                                {{ $esDiscapacidad ? 'readonly' : '' }}
                                class="w-full pr-8 px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/10 transition {{ $esDiscapacidad ? 'opacity-70 cursor-not-allowed' : '' }}">
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-sm font-medium">%</span>
                        </div>
                        @error('porcentajeAplicado') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Fecha de Asignación *</label>
                        <input wire:model="fechaAsignacion" type="date"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/10 transition">
                        @error('fechaAsignacion') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Documento --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                            Documento de Respaldo
                            <span class="font-normal text-slate-400">(JPG, PNG, PDF · máx. 4MB)</span>
                        </label>
                        <input wire:model="documento" type="file" accept="image/*,.pdf"
                            class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 file:transition-colors cursor-pointer">
                        @error('documento') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Observación --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Observación</label>
                        <textarea wire:model="observacion" rows="2" placeholder="Detalles adicionales de la beca…"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/10 transition resize-none"></textarea>
                        @error('observacion') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @error('tipoBecaId')
                        @if (str_contains($message, 'beca activa'))
                            <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900 rounded-lg">
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            </div>
                        @endif
                    @enderror
                </div>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] flex justify-end gap-2 sticky bottom-0 bg-white dark:bg-slate-900">
                    <button wire:click="cerrarModal"
                        class="px-4 py-2 rounded-lg text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Cancelar
                    </button>
                    <button wire:click="guardar" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow hover:brightness-110 disabled:opacity-50 transition">
                        <span wire:loading.remove>{{ $becaId ? 'Actualizar' : 'Asignar Beca' }}</span>
                        <span wire:loading>Guardando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL REVOCAR --}}
    @if ($showRevocarModal)
        <div x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarRevocar"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-white/[0.08]"
                x-on:keydown.escape.window="$wire.cerrarRevocar()">

                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">Revocar Beca</h3>
                </div>

                <div class="px-6 py-5 space-y-3">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Esta acción marcará la beca como inactiva. El historial se conserva.</p>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Motivo de revocación</label>
                        <textarea wire:model="motivo" rows="2" placeholder="Indica el motivo…"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-red-500/50 focus:ring-2 focus:ring-red-500/10 transition resize-none"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] flex justify-end gap-2">
                    <button wire:click="cerrarRevocar"
                        class="px-4 py-2 rounded-lg text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Cancelar
                    </button>
                    <button wire:click="revocar" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-red-500 text-white shadow hover:bg-red-600 disabled:opacity-50 transition">
                        <span wire:loading.remove>Revocar Beca</span>
                        <span wire:loading>Procesando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
