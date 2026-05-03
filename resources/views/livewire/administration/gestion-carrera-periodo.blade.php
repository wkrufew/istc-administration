<div>
    @if ($isOpen && $periodo)
        {{-- Modal overlay --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 overflow-y-auto py-8 px-4">
            <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 bg-gray-800 rounded-t-2xl">
                    <div>
                        <h2 class="text-lg font-bold text-white">Gestionar Carreras del Cohorte</h2>
                        <p class="text-sm text-gray-400 mt-0.5">
                            {{ $periodo->code }}
                            @if ($periodo->description)
                                — {{ $periodo->description }}
                            @endif
                        </p>
                    </div>
                    <button wire:click="cerrar"
                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/10 hover:bg-white/20 text-white transition text-lg leading-none">
                        ×
                    </button>
                </div>

                {{-- Flash --}}
                @if (session('gestion_success'))
                    <div class="mx-6 mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm font-medium">
                        ✔ {{ session('gestion_success') }}
                    </div>
                @endif

                {{-- Body --}}
                <div class="p-6">

                    {{-- Toolbar --}}
                    @if (!$mostrarFormVincular)
                        <div class="flex justify-end mb-5">
                            <button wire:click="abrirFormVincular"
                                class="inline-flex items-center gap-2 bg-lime-600 text-white px-4 py-2 rounded-xl
                                       hover:bg-lime-700 text-sm font-semibold transition shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Vincular carrera
                            </button>
                        </div>
                    @else
                        {{-- Vincular form --}}
                        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-5 mb-5">
                            <h4 class="font-bold text-gray-700 text-sm mb-4">Nueva vinculación</h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                                {{-- Carrera --}}
                                <div class="sm:col-span-2 lg:col-span-1">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Carrera <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="nuevaCarreraId"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:border-lime-500 focus:ring-lime-500">
                                        <option value="">— Seleccionar —</option>
                                        @foreach ($carrerasDisponibles as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                        @endforeach
                                    </select>
                                    @error('nuevaCarreraId')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    @if ($carrerasDisponibles->isEmpty())
                                        <p class="text-xs text-amber-600 mt-1">Todas las carreras activas ya están vinculadas.</p>
                                    @endif
                                </div>

                                {{-- Fecha inicio --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Fecha inicio <span class="font-normal text-gray-400">(override)</span>
                                    </label>
                                    <input type="date" wire:model="nuevaFechaInicio"
                                        class="w-full rounded-xl border-gray-200 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">Vacío = hereda del cohorte</p>
                                </div>

                                {{-- Fecha fin --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Fecha fin <span class="font-normal text-gray-400">(override)</span>
                                    </label>
                                    <input type="date" wire:model="nuevaFechaFin"
                                        class="w-full rounded-xl border-gray-200 text-sm">
                                </div>

                                {{-- Límite matrícula --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Lím. matrícula <span class="font-normal text-gray-400">(override)</span>
                                    </label>
                                    <input type="date" wire:model="nuevaFechaLimiteMatricula"
                                        class="w-full rounded-xl border-gray-200 text-sm">
                                </div>

                                {{-- Límite pago --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Lím. pago <span class="font-normal text-gray-400">(override)</span>
                                    </label>
                                    <input type="date" wire:model="nuevaFechaLimitePago"
                                        class="w-full rounded-xl border-gray-200 text-sm">
                                </div>

                                {{-- Checkboxes --}}
                                <div class="flex flex-col gap-3 justify-center">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" wire:model="nuevaIsActive" class="rounded text-lime-600 focus:ring-lime-500">
                                        <span class="text-sm font-medium text-gray-700">Habilitado</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" wire:model="nuevaIsCurrent" class="rounded text-lime-600 focus:ring-lime-500">
                                        <span class="text-sm font-medium text-gray-700">Periodo actual de la carrera</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex gap-3 mt-5">
                                <button wire:click="vincular"
                                    class="px-5 py-2 bg-lime-600 text-white text-sm font-semibold rounded-xl hover:bg-lime-700 transition">
                                    Vincular
                                </button>
                                <button wire:click="cerrarFormVincular"
                                    class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Empty state --}}
                    @if ($carrerasVinculadas->isEmpty())
                        <div class="py-14 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 font-semibold">No hay carreras vinculadas a este cohorte.</p>
                            <p class="text-sm text-gray-400 mt-1">Usa "Vincular carrera" para agregar una.</p>
                        </div>
                    @else

                        {{-- Desktop table --}}
                        <div class="hidden sm:block rounded-xl border border-gray-200 overflow-hidden">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr class="text-xs text-gray-500 uppercase tracking-wide">
                                        <th class="px-5 py-3 text-left font-semibold">Carrera</th>
                                        <th class="px-4 py-3 text-center font-semibold">Estado</th>
                                        <th class="px-4 py-3 text-left font-semibold">Fechas efectivas</th>
                                        <th class="px-4 py-3 text-center font-semibold">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($carrerasVinculadas as $carrera)
                                        @php $cp = $carrera->pivot; @endphp

                                        @if ((int) $editandoId === (int) $cp->id)
                                            {{-- Fila de edición --}}
                                            <tr class="bg-blue-50/40">
                                                <td colspan="4" class="px-5 py-5">
                                                    <p class="font-semibold text-sm text-gray-800 mb-4">
                                                        Editando:
                                                        <span class="text-blue-700">{{ $carrera->name }}</span>
                                                    </p>
                                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                                        <div>
                                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha inicio</label>
                                                            <input type="date" wire:model="editFechaInicio"
                                                                class="w-full rounded-xl border-gray-200 text-sm">
                                                            <p class="text-xs text-gray-400 mt-0.5">Vacío = hereda</p>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha fin</label>
                                                            <input type="date" wire:model="editFechaFin"
                                                                class="w-full rounded-xl border-gray-200 text-sm">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Lím. matrícula</label>
                                                            <input type="date" wire:model="editFechaLimiteMatricula"
                                                                class="w-full rounded-xl border-gray-200 text-sm">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Lím. pago</label>
                                                            <input type="date" wire:model="editFechaLimitePago"
                                                                class="w-full rounded-xl border-gray-200 text-sm">
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-6 mt-3">
                                                        <label class="flex items-center gap-2 cursor-pointer">
                                                            <input type="checkbox" wire:model="editIsActive"
                                                                class="rounded text-blue-600 focus:ring-blue-500">
                                                            <span class="text-sm text-gray-700">Habilitado</span>
                                                        </label>
                                                        <label class="flex items-center gap-2 cursor-pointer">
                                                            <input type="checkbox" wire:model="editIsCurrent"
                                                                class="rounded text-blue-600 focus:ring-blue-500">
                                                            <span class="text-sm text-gray-700">Periodo actual de la carrera</span>
                                                        </label>
                                                    </div>
                                                    <div class="flex gap-3 mt-4">
                                                        <button wire:click="guardarEdicion"
                                                            class="px-4 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">
                                                            Guardar cambios
                                                        </button>
                                                        <button wire:click="cancelarEdicion"
                                                            class="px-4 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                                                            Cancelar
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            {{-- Fila normal --}}
                                            <tr class="hover:bg-gray-50/60 transition">
                                                <td class="px-5 py-4">
                                                    <p class="font-bold text-gray-900">{{ $carrera->name }}</p>
                                                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $carrera->code }}</p>
                                                </td>

                                                <td class="px-4 py-4 text-center">
                                                    <div class="flex flex-col items-center gap-1.5">
                                                        @if ($cp->is_current)
                                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                                bg-green-100 text-green-700 border border-green-200">
                                                                ● Activo actual
                                                            </span>
                                                        @else
                                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                                bg-gray-100 text-gray-500 border border-gray-200">
                                                                ○ No actual
                                                            </span>
                                                        @endif
                                                        @if ($cp->is_active)
                                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                                Habilitado
                                                            </span>
                                                        @else
                                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">
                                                                Deshabilitado
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td class="px-4 py-4 text-xs text-gray-600 space-y-0.5">
                                                    @php
                                                        $fi = $cp->fecha_inicio ?? $periodo->fecha_inicio;
                                                        $ff = $cp->fecha_fin     ?? $periodo->fecha_fin;
                                                        $fm = $cp->fecha_limite_matricula ?? $periodo->fecha_limite_matricula;
                                                        $fp = $cp->fecha_limite_pago      ?? $periodo->fecha_limite_pago;
                                                    @endphp
                                                    <p>
                                                        <span class="text-gray-400">Periodo:</span>
                                                        {{ $fi->isoFormat('D MMM Y') }} → {{ $ff->isoFormat('D MMM Y') }}
                                                        @if ($cp->fecha_inicio)
                                                            <span class="text-purple-500 font-semibold">(propio)</span>
                                                        @endif
                                                    </p>
                                                    <p>
                                                        <span class="text-gray-400">Matrícula:</span>
                                                        {{ $fm->isoFormat('D MMM Y') }}
                                                        @if ($cp->fecha_limite_matricula)
                                                            <span class="text-purple-500 font-semibold">(propio)</span>
                                                        @endif
                                                    </p>
                                                    <p>
                                                        <span class="text-gray-400">Pago:</span>
                                                        {{ $fp->isoFormat('D MMM Y') }}
                                                        @if ($cp->fecha_limite_pago)
                                                            <span class="text-purple-500 font-semibold">(propio)</span>
                                                        @endif
                                                    </p>
                                                </td>

                                                <td class="px-4 py-4 text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button wire:click="editar({{ $cp->id }})"
                                                            class="px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-200
                                                                   text-blue-700 text-xs font-semibold hover:bg-blue-100 transition">
                                                            Editar
                                                        </button>
                                                        <button
                                                            wire:click="desvincular({{ $cp->id }})"
                                                            wire:confirm="¿Desvincular '{{ $carrera->name }}' de este cohorte? Esta acción no elimina la carrera."
                                                            class="px-3 py-1.5 rounded-lg bg-red-50 border border-red-200
                                                                   text-red-700 text-xs font-semibold hover:bg-red-100 transition">
                                                            Desvincular
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile cards --}}
                        <div class="sm:hidden space-y-3">
                            @foreach ($carrerasVinculadas as $carrera)
                                @php $cp = $carrera->pivot; @endphp
                                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $carrera->name }}</p>
                                            <p class="text-xs text-gray-400 font-mono">{{ $carrera->code }}</p>
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            @if ($cp->is_current)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Actual</span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                                {{ $cp->is_active ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-600' }}">
                                                {{ $cp->is_active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                    </div>

                                    @if ((int) $editandoId === (int) $cp->id)
                                        {{-- Edición mobile --}}
                                        <div class="space-y-3 pt-3 border-t border-gray-100">
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha inicio</label>
                                                    <input type="date" wire:model="editFechaInicio"
                                                        class="w-full rounded-xl border-gray-200 text-xs">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha fin</label>
                                                    <input type="date" wire:model="editFechaFin"
                                                        class="w-full rounded-xl border-gray-200 text-xs">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Lím. matrícula</label>
                                                    <input type="date" wire:model="editFechaLimiteMatricula"
                                                        class="w-full rounded-xl border-gray-200 text-xs">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Lím. pago</label>
                                                    <input type="date" wire:model="editFechaLimitePago"
                                                        class="w-full rounded-xl border-gray-200 text-xs">
                                                </div>
                                            </div>
                                            <div class="flex gap-5">
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" wire:model="editIsActive" class="rounded">
                                                    <span class="text-xs text-gray-700">Habilitado</span>
                                                </label>
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" wire:model="editIsCurrent" class="rounded">
                                                    <span class="text-xs text-gray-700">Periodo actual</span>
                                                </label>
                                            </div>
                                            <div class="flex gap-2">
                                                <button wire:click="guardarEdicion"
                                                    class="flex-1 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl hover:bg-blue-700 transition">
                                                    Guardar
                                                </button>
                                                <button wire:click="cancelarEdicion"
                                                    class="flex-1 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-200 transition">
                                                    Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex gap-2 pt-3 border-t border-gray-100">
                                            <button wire:click="editar({{ $cp->id }})"
                                                class="flex-1 py-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold">
                                                Editar
                                            </button>
                                            <button
                                                wire:click="desvincular({{ $cp->id }})"
                                                wire:confirm="¿Desvincular '{{ $carrera->name }}' de este cohorte?"
                                                class="flex-1 py-2 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold">
                                                Desvincular
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t bg-gray-50 rounded-b-2xl flex justify-end">
                    <button wire:click="cerrar"
                        class="px-5 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-300 transition">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
