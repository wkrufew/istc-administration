<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-violet-500 to-purple-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-violet-600 via-purple-500 to-fuchsia-400 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Convenios por Estudiante</h1>
                    <p class="text-xs text-violet-400/70 tracking-widest uppercase mt-1">Gestión · Convenios</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('administracion.administrativa.tipos-convenio.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Tipos de Convenio
                </a>
                <div class="relative min-w-[200px] max-w-sm">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search"
                        class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-slate-700 dark:text-white/80 placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition-all duration-200"
                        placeholder="Buscar estudiante…">
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-white/[0.06] overflow-hidden shadow-xl shadow-slate-200/80 dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04]">
        <div class="h-px bg-gradient-to-r from-transparent via-violet-500/30 to-transparent"></div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/[0.05]">
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Estudiante</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Cédula</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Convenio Activo</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-20">% Desc.</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-36">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($estudiantes as $est)
                        @php $convenioActivo = $est->conveniosAplicados->first(); @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-slate-700 dark:text-white/75">{{ $est->name }}</div>
                                <div class="text-xs text-slate-400">{{ $est->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ $est->cedula ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($convenioActivo)
                                    <div>
                                        <span class="text-xs font-medium text-violet-600 dark:text-violet-400">{{ $convenioActivo->tipoConvenio?->nombre ?? '—' }}</span>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[0.65rem] text-slate-400">
                                                Desde {{ $convenioActivo->fecha_inicio->format('d/m/Y') }}
                                                @if ($convenioActivo->fecha_fin)
                                                    · Hasta {{ $convenioActivo->fecha_fin->format('d/m/Y') }}
                                                @endif
                                            </span>
                                            @if ($convenioActivo->tipoConvenio->tipo_alcance === 'semestral')
                                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[0.6rem] font-medium bg-orange-500/10 border border-orange-500/20 text-orange-600 dark:text-orange-400">Semestral</span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[0.6rem] font-medium bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400">Anual</span>
                                            @endif
                                        </div>
                                        @if ($convenioActivo->motivo)
                                            <div class="text-[0.65rem] text-slate-400 mt-0.5 italic">{{ $convenioActivo->motivo }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">Sin convenio</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($convenioActivo)
                                    <span class="text-sm font-bold text-violet-600 dark:text-violet-400">{{ number_format($convenioActivo->porcentaje_aplicado, 0) }}%</span>
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($convenioActivo)
                                        <button wire:click="abrirModalEditar({{ $convenioActivo->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.72rem] font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800 hover:text-violet-500 hover:border-violet-500/30 hover:bg-violet-500/[0.06] transition-all duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            Editar
                                        </button>
                                        <button wire:click="abrirRevocar({{ $convenioActivo->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.72rem] font-medium text-red-500/70 border border-red-200/40 dark:border-red-500/10 bg-red-50/60 dark:bg-red-900/[0.06] hover:text-red-600 hover:border-red-400/50 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Revocar
                                        </button>
                                    @else
                                        <button wire:click="abrirModalAsignar({{ $est->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[0.72rem] font-medium text-violet-600 border border-violet-200/60 dark:border-violet-500/20 bg-violet-50/60 dark:bg-violet-900/[0.06] hover:bg-violet-50 dark:hover:bg-violet-900/10 hover:border-violet-400/50 transition-all duration-150">
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

        <div class="h-0.5 bg-gradient-to-r from-violet-600 via-purple-500 to-fuchsia-400 opacity-50"></div>
    </div>

    {{-- MODAL ASIGNAR / EDITAR --}}
    @if ($showModal)
        <div x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarModal"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-white/[0.08] max-h-[90vh] overflow-y-auto"
                x-on:keydown.escape.window="$wire.cerrarModal()">

                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center justify-between sticky top-0 bg-white dark:bg-slate-900 z-10">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">
                        {{ $convenioId ? 'Editar Convenio Asignado' : 'Asignar Convenio' }}
                    </h3>
                    <button wire:click="cerrarModal" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">

                    {{-- Tipo de convenio --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Tipo de Convenio *</label>
                        <select wire:model.live="tipoConvenioId"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            <option value="">— Seleccionar —</option>
                            @foreach ($tiposConvenio as $tipo)
                                <option value="{{ $tipo->id }}">
                                    {{ $tipo->nombre }} ({{ number_format($tipo->porcentaje_defecto, 0) }}% · {{ $tipo->tipo_alcance === 'anual' ? 'Anual' : 'Semestral' }})
                                </option>
                            @endforeach
                        </select>
                        @error('tipoConvenioId') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- % Aplicado --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">% de Descuento Aplicado *</label>
                        <div class="relative">
                            <input wire:model="porcentajeAplicado" type="number" step="0.01" min="0" max="100" placeholder="15"
                                class="w-full pr-8 px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-sm font-medium">%</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Aplica al arancel (nunca a la cuota de matrícula).</p>
                        @error('porcentajeAplicado') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Motivo --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Motivo del Convenio</label>
                        <input wire:model="motivo" type="text" placeholder="Ej: Acuerdo institucional con empresa XYZ"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                        @error('motivo') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Fecha inicio y fin --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Fecha Inicio *</label>
                            <input wire:model="fechaInicio" type="date"
                                class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            @error('fechaInicio') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                                Fecha Fin
                                <span class="font-normal text-slate-400">(opcional)</span>
                            </label>
                            <input wire:model="fechaFin" type="date"
                                class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            @error('fechaFin') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Documento --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                            Documento de Respaldo
                            <span class="font-normal text-slate-400">(JPG, PNG, PDF · máx. 4MB)</span>
                        </label>
                        <input wire:model="documento" type="file" accept="image/*,.pdf"
                            class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 file:transition-colors cursor-pointer">
                        @error('documento') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Observación --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Observación</label>
                        <textarea wire:model="observacion" rows="2" placeholder="Detalles adicionales…"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition resize-none"></textarea>
                        @error('observacion') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @error('tipoConvenioId')
                        @if (str_contains($message, 'convenio activo'))
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
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-gradient-to-r from-violet-500 to-purple-500 text-white shadow hover:brightness-110 disabled:opacity-50 transition">
                        <span wire:loading.remove>{{ $convenioId ? 'Actualizar' : 'Asignar Convenio' }}</span>
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
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">Revocar Convenio</h3>
                </div>

                <div class="px-6 py-5 space-y-3">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Esta acción marcará el convenio como inactivo. El historial se conserva.</p>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Motivo de revocación</label>
                        <textarea wire:model="motivoRevocacion" rows="2" placeholder="Indica el motivo…"
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
                        <span wire:loading.remove>Revocar Convenio</span>
                        <span wire:loading>Procesando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
