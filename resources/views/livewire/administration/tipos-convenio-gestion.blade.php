<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-violet-500 to-purple-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-violet-600 via-purple-500 to-fuchsia-400 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Tipos de Convenio</h1>
                    <p class="text-xs text-violet-400/70 tracking-widest uppercase mt-1">Configuración · Convenios</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative min-w-[180px] max-w-xs">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </div>
                    <input wire:model.live="search" class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-slate-700 dark:text-white/80 placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition-all duration-200" placeholder="Buscar tipo…">
                </div>

                <button wire:click="abrirModalCrear"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold bg-gradient-to-r from-violet-500 to-purple-500 text-white shadow hover:shadow-violet-500/30 hover:brightness-110 transition-all duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Tipo
                </button>
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
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Nombre</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-28">% Desc. Def.</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-28">Alcance</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-24">Requiere Doc.</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Descripción</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-20">Estado</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($tipos as $tipo)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-slate-700 dark:text-white/75">{{ $tipo->nombre }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-bold text-violet-600 dark:text-violet-400">{{ number_format($tipo->porcentaje_defecto, 0) }}%</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($tipo->tipo_alcance === 'anual')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400">Anual</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-orange-500/10 border border-orange-500/20 text-orange-600 dark:text-orange-400">Semestral</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($tipo->requiere_documento)
                                    <svg class="w-4 h-4 text-violet-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">{{ $tipo->descripcion ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" class="peer sr-only"
                                        @if ($tipo->is_active) checked @endif
                                        wire:click="toggleActivo({{ $tipo->id }})">
                                    <div class="peer h-5 w-9 rounded-full bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-white/[0.06]
                                                after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full
                                                after:bg-white dark:after:bg-slate-400 after:transition-all after:content-['']
                                                peer-checked:bg-violet-500/70 peer-checked:border-violet-500/30
                                                peer-checked:after:translate-x-full peer-checked:after:bg-white
                                                peer-focus:ring-2 peer-focus:ring-violet-500/20">
                                    </div>
                                </label>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="abrirModalEditar({{ $tipo->id }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800 hover:text-violet-500 hover:border-violet-500/30 hover:bg-violet-500/[0.06] transition-all duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm">No hay tipos de convenio configurados. Crea el primero.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tipos->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50 dark:bg-black/10">
                {{ $tipos->links() }}
            </div>
        @endif

        <div class="h-0.5 bg-gradient-to-r from-violet-600 via-purple-500 to-fuchsia-400 opacity-50"></div>
    </div>

    {{-- MODAL CREAR / EDITAR --}}
    @if ($showModal)
        <div x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarModal"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-white/[0.08]"
                x-on:keydown.escape.window="$wire.cerrarModal()">

                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">
                        {{ $tipoId ? 'Editar Tipo de Convenio' : 'Nuevo Tipo de Convenio' }}
                    </h3>
                    <button wire:click="cerrarModal" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">

                    {{-- Nombre --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Nombre *</label>
                        <input wire:model="nombre" type="text" placeholder="Ej: Convenio Empresa Privada"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                        @error('nombre') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Porcentaje por defecto --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">% de Descuento por Defecto *</label>
                        <div class="relative">
                            <input wire:model="porcentaje_defecto" type="number" step="0.01" min="0" max="100" placeholder="15"
                                class="w-full pr-8 px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-sm font-medium">%</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Se puede ajustar al asignarlo a cada estudiante.</p>
                        @error('porcentaje_defecto') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Alcance --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Alcance *</label>
                        <select wire:model="tipo_alcance"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition">
                            <option value="anual">Anual (persiste todos los semestres)</option>
                            <option value="semestral">Semestral (expira al fin del semestre)</option>
                        </select>
                        @error('tipo_alcance') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Requiere documento --}}
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" wire:model="requiere_documento"
                            class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-violet-500 focus:ring-violet-500/20">
                        <span class="text-xs text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-white transition">
                            Requiere documento de respaldo al asignar
                        </span>
                    </label>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Descripción / Condiciones</label>
                        <textarea wire:model="descripcion" rows="2" placeholder="Condiciones del convenio…"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/10 transition resize-none"></textarea>
                        @error('descripcion') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Activo --}}
                    <label class="flex items-center gap-2.5 cursor-pointer group pt-1">
                        <input type="checkbox" wire:model="isActive"
                            class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-violet-500 focus:ring-violet-500/20">
                        <span class="text-xs text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-white transition">Tipo activo (disponible para asignar)</span>
                    </label>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] flex justify-end gap-2">
                    <button wire:click="cerrarModal"
                        class="px-4 py-2 rounded-lg text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Cancelar
                    </button>
                    <button wire:click="guardar" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-gradient-to-r from-violet-500 to-purple-500 text-white shadow hover:brightness-110 disabled:opacity-50 transition">
                        <span wire:loading.remove>{{ $tipoId ? 'Actualizar' : 'Guardar' }}</span>
                        <span wire:loading>Guardando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
