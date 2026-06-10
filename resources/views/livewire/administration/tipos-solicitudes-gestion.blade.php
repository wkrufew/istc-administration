<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50"></div>

        <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-sky-700 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Tipos de Solicitudes</h1>
                    <p class="text-xs text-lime-400/70 tracking-widest uppercase mt-1">Configuración · Solicitudes</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                {{-- Buscador --}}
                <div class="relative min-w-[180px] max-w-xs">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </div>
                    <input wire:model.live="search" class="w-full pl-9 pr-4 py-2 rounded-full text-xs text-slate-700 dark:text-white/80 placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition-all duration-200" placeholder="Buscar tipo…">
                </div>

                {{-- Nuevo tipo --}}
                <button wire:click="abrirModalCrear"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold bg-gradient-to-r from-lime-500 to-green-600 text-white shadow hover:shadow-lime-500/30 hover:brightness-110 transition-all duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Tipo
                </button>
            </div>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-white/[0.06] overflow-hidden shadow-xl shadow-slate-200/80 dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-100 dark:ring-white/[0.04]">
        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/[0.05]">
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Nombre</th>
                        <th class="px-4 py-3.5 text-left text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400">Descripción</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-28">Precio</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-24">Req. Doc.</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-36">Certificado</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-28">Notif. Doc.</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-28">Estado</th>
                        <th class="px-4 py-3.5 text-center text-[0.65rem] font-medium tracking-[0.15em] uppercase text-slate-500 dark:text-slate-400 w-28">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                    @forelse ($tipos as $tipo)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-150">
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-slate-700 dark:text-white/75">{{ $tipo->nombre }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">{{ $tipo->descripcion ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($tipo->precio > 0)
                                    <span class="text-sm font-semibold text-slate-700 dark:text-white/80">${{ number_format($tipo->precio, 2) }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-lime-500/10 border border-lime-500/20 text-lime-500">Gratuito</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($tipo->requiere_documento)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-sky-500/10 border border-sky-500/20 text-sky-500">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Sí
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($tipo->tipo_certificado === 'cna')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-emerald-500/10 border border-emerald-500/20 text-emerald-600">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        No Adeudar
                                    </span>
                                @elseif ($tipo->tipo_certificado === 'matricula')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-sky-500/10 border border-sky-500/20 text-sky-600">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Matrícula
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($tipo->notifica_docente)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-medium bg-amber-500/10 border border-amber-500/20 text-amber-500">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                                        Sí
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" class="peer sr-only"
                                        @if ($tipo->is_active) checked @endif
                                        wire:click="toggleActivo({{ $tipo->id }})">
                                    <div class="peer h-5 w-9 rounded-full bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-white/[0.06]
                                                after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full
                                                after:bg-white dark:after:bg-slate-400 after:transition-all after:content-['']
                                                peer-checked:bg-lime-600/70 peer-checked:border-lime-500/30
                                                peer-checked:after:translate-x-full peer-checked:after:bg-white
                                                peer-focus:ring-2 peer-focus:ring-lime-500/20">
                                    </div>
                                </label>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="abrirModalEditar({{ $tipo->id }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[0.72rem] font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.06] bg-white dark:bg-slate-800 hover:text-lime-400 hover:border-lime-500/30 hover:bg-lime-500/[0.06] transition-all duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm">No hay tipos de solicitudes configurados.</p>
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

        <div class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50"></div>
    </div>

    {{-- MODAL CREAR / EDITAR --}}
    @if ($showModal)
        <div x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarModal"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-white/[0.08]">
                {{-- Header modal --}}
                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.06] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white/90">
                        {{ $tipoId ? 'Editar Tipo' : 'Nuevo Tipo de Solicitud' }}
                    </h3>
                    <button wire:click="cerrarModal" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Body modal --}}
                <div class="px-6 py-5 space-y-4">
                    {{-- Nombre --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Nombre *</label>
                        <input wire:model="nombre" type="text" placeholder="Ej: Certificado de no adeudar"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition">
                        @error('nombre') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Descripción</label>
                        <textarea wire:model="descripcion" rows="2" placeholder="Descripción opcional del tipo de solicitud…"
                            class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition resize-none"></textarea>
                        @error('descripcion') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Precio --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Precio (USD) *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm">$</span>
                            <input wire:model="precio" type="number" step="0.01" min="0" placeholder="0.00"
                                class="w-full pl-7 pr-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition">
                        </div>
                        @error('precio') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Checkboxes --}}
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" wire:model="requiereDocumento"
                                class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-lime-500 focus:ring-lime-500/20">
                            <span class="text-xs text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-white transition">Requiere documento</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" wire:model="isActive"
                                class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-lime-500 focus:ring-lime-500/20">
                            <span class="text-xs text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-white transition">Activo</span>
                        </label>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Genera certificado</label>
                            <select wire:model="tipoCertificado"
                                class="w-full px-3 py-2 rounded-lg text-sm text-slate-700 dark:text-white/80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.08] focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10 transition">
                                <option value="">Sin certificado</option>
                                <option value="cna">Certificado de No Adeudar</option>
                                <option value="matricula">Certificado de Matrícula</option>
                            </select>
                        </div>
                        <label class="flex items-start gap-2.5 cursor-pointer group">
                            <input type="checkbox" wire:model="notificaDocente"
                                class="w-4 h-4 mt-0.5 rounded border-slate-300 dark:border-slate-600 text-amber-500 focus:ring-amber-500/20">
                            <div>
                                <span class="text-xs text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-white transition block">Notificación por correo al docente</span>
                                <span class="text-[0.65rem] text-slate-400 dark:text-slate-500">Pide seleccionar docente al procesar</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Footer modal --}}
                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/[0.06] flex justify-end gap-2">
                    <button wire:click="cerrarModal"
                        class="px-4 py-2 rounded-lg text-xs font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Cancelar
                    </button>
                    <button wire:click="guardar" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-gradient-to-r from-lime-500 to-green-600 text-white shadow hover:brightness-110 disabled:opacity-50 transition">
                        <span wire:loading.remove>{{ $tipoId ? 'Actualizar' : 'Guardar' }}</span>
                        <span wire:loading>Guardando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
