<div>
    {{-- ── HEADER ─────────────────────────────────────────────────────────────── --}}
    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Auditoría del Sistema
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Registro de cambios en calificaciones y en la estructura académica del sistema.
                </p>
            </div>

            {{-- Tab pills --}}
            <div class="flex gap-2 self-start shrink-0">
                <button wire:click="$set('tab', 'calificaciones')"
                    class="px-4 py-2 rounded-xl text-sm font-semibold transition
                        {{ $tab === 'calificaciones'
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    Calificaciones
                </button>
                <button wire:click="$set('tab', 'general')"
                    class="px-4 py-2 rounded-xl text-sm font-semibold transition
                        {{ $tab === 'general'
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    General
                </button>
            </div>
        </div>

        {{-- ── FILTROS CALIFICACIONES ─────────────────────────────────────────── --}}
        @if ($tab === 'calificaciones')
            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">

                {{-- Búsqueda --}}
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Buscar</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
                            </svg>
                        </span>
                        <input type="text" wire:model.live.debounce.350ms="busquedaCali"
                            placeholder="Docente, estudiante o cédula…"
                            class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                placeholder:text-gray-400 dark:placeholder:text-gray-500
                                focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition text-sm">
                    </div>
                </div>

                {{-- Campo modificado --}}
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Campo</label>
                    <select wire:model.live="filtroCampo"
                        class="mt-1 w-full py-2.5 px-3 rounded-xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                            focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition text-sm">
                        <option value="">Todos los campos</option>
                        @foreach ($camposDisponibles as $campo)
                            <option value="{{ $campo }}">{{ ucfirst(str_replace('_', ' ', $campo)) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Desde --}}
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Desde</label>
                    <input type="date" wire:model.live="fechaDesdeCali"
                        class="mt-1 w-full py-2.5 px-3 rounded-xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                            focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition text-sm">
                </div>

                {{-- Hasta --}}
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Hasta</label>
                    <input type="date" wire:model.live="fechaHastaCali"
                        class="mt-1 w-full py-2.5 px-3 rounded-xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                            focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition text-sm">
                </div>

                {{-- Reset --}}
                <div class="flex items-end sm:col-span-2 xl:col-span-5">
                    <button wire:click="resetFiltrosCali"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                            bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300
                            hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15" />
                        </svg>
                        Limpiar filtros
                    </button>
                </div>
            </div>

        {{-- ── FILTROS GENERAL ───────────────────────────────────────────────── --}}
        @else
            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">

                {{-- Búsqueda --}}
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Buscar usuario</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
                            </svg>
                        </span>
                        <input type="text" wire:model.live.debounce.350ms="busquedaGeneral"
                            placeholder="Nombre o cédula del usuario…"
                            class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                placeholder:text-gray-400 dark:placeholder:text-gray-500
                                focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition text-sm">
                    </div>
                </div>

                {{-- Modelo --}}
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Modelo</label>
                    <select wire:model.live="filtroModelo"
                        class="mt-1 w-full py-2.5 px-3 rounded-xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                            focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition text-sm">
                        <option value="">Todos los modelos</option>
                        <option value="App\Models\Pago">Pago</option>
                        <option value="App\Models\ObligacionesFinanciera">Obligación Fin.</option>
                        <option value="App\Models\Matricula">Matrícula</option>
                        <option value="App\Models\Carrera">Carrera</option>
                        <option value="App\Models\Semestre">Semestre</option>
                        <option value="App\Models\Materia">Materia</option>
                    </select>
                </div>

                {{-- Evento --}}
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Evento</label>
                    <select wire:model.live="filtroEvento"
                        class="mt-1 w-full py-2.5 px-3 rounded-xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                            focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition text-sm">
                        <option value="">Todos los eventos</option>
                        <option value="created">Creación</option>
                        <option value="updated">Modificación</option>
                        <option value="deleted">Eliminación</option>
                    </select>
                </div>

                {{-- Desde --}}
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Desde</label>
                    <input type="date" wire:model.live="fechaDesdeGen"
                        class="mt-1 w-full py-2.5 px-3 rounded-xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                            focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition text-sm">
                </div>

                {{-- Hasta --}}
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Hasta</label>
                    <input type="date" wire:model.live="fechaHastaGen"
                        class="mt-1 w-full py-2.5 px-3 rounded-xl border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                            focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition text-sm">
                </div>

                {{-- Reset --}}
                <div class="flex items-end sm:col-span-2 xl:col-span-5">
                    <button wire:click="resetFiltrosGeneral"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                            bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300
                            hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15" />
                        </svg>
                        Limpiar filtros
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- ── TABLA CALIFICACIONES ────────────────────────────────────────────────── --}}
    @if ($tab === 'calificaciones')

        {{-- Desktop --}}
        <div class="hidden lg:block overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr class="text-gray-600 dark:text-gray-300">
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Fecha</th>
                        <th class="px-4 py-3 text-left font-semibold">Docente</th>
                        <th class="px-4 py-3 text-left font-semibold">Estudiante</th>
                        <th class="px-4 py-3 text-left font-semibold">Materia</th>
                        <th class="px-4 py-3 text-left font-semibold">Campo</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Anterior → Nuevo</th>
                        <th class="px-4 py-3 text-left font-semibold">Motivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($this->auditoriaCalificaciones as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs">
                                {{ $r->fecha_modificacion?->format('d/m/Y') }}<br>
                                <span class="text-gray-400 dark:text-gray-500">{{ $r->fecha_modificacion?->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 dark:text-gray-100">{{ $r->docente->name ?? '—' }}</div>
                                @if ($r->docente)
                                    <div class="text-xs text-gray-400 dark:text-gray-500">CI: {{ $r->docente->cedula }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php $est = $r->calificacion?->detalleMatricula?->estudiante @endphp
                                <div class="font-medium text-gray-800 dark:text-gray-100">{{ $est?->name ?? '—' }}</div>
                                @if ($est)
                                    <div class="text-xs text-gray-400 dark:text-gray-500">CI: {{ $est->cedula }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $r->calificacion?->detalleMatricula?->materia?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold
                                    bg-blue-50 text-blue-700 border border-blue-200
                                    dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $r->campo_modificado ?? '—')) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <span class="text-red-600 dark:text-red-400 font-medium">{{ $r->valor_anterior ?? '—' }}</span>
                                <span class="text-gray-400 dark:text-gray-500 mx-1">→</span>
                                <span class="text-green-600 dark:text-green-400 font-medium">{{ $r->valor_nuevo ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm max-w-[200px] truncate"
                                title="{{ $r->motivo_modificacion }}">
                                {{ $r->motivo_modificacion ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                No se encontraron registros de auditoría.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="grid grid-cols-1 gap-4 lg:hidden">
            @forelse ($this->auditoriaCalificaciones as $r)
                @php $est = $r->calificacion?->detalleMatricula?->estudiante @endphp
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-4">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $r->fecha_modificacion?->format('d/m/Y H:i') }}
                            </div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm mt-0.5">
                                {{ $est?->name ?? '—' }}
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold shrink-0
                            bg-blue-50 text-blue-700 border border-blue-200
                            dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                            {{ ucfirst(str_replace('_', ' ', $r->campo_modificado ?? '—')) }}
                        </span>
                    </div>
                    <div class="space-y-1 text-sm">
                        <div class="flex gap-2">
                            <span class="text-gray-500 dark:text-gray-400 w-20 shrink-0">Docente</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $r->docente?->name ?? '—' }}</span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-gray-500 dark:text-gray-400 w-20 shrink-0">Materia</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $r->calificacion?->detalleMatricula?->materia?->name ?? '—' }}</span>
                        </div>
                        <div class="flex gap-2 items-center">
                            <span class="text-gray-500 dark:text-gray-400 w-20 shrink-0">Cambio</span>
                            <span class="text-red-500 dark:text-red-400 font-medium">{{ $r->valor_anterior ?? '—' }}</span>
                            <span class="text-gray-400 mx-1">→</span>
                            <span class="text-green-500 dark:text-green-400 font-medium">{{ $r->valor_nuevo ?? '—' }}</span>
                        </div>
                        @if ($r->motivo_modificacion)
                            <div class="flex gap-2">
                                <span class="text-gray-500 dark:text-gray-400 w-20 shrink-0">Motivo</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $r->motivo_modificacion }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8 text-center text-gray-400 dark:text-gray-500">
                    No se encontraron registros de auditoría.
                </div>
            @endforelse
        </div>

        @if ($this->auditoriaCalificaciones->hasPages())
            <div class="mt-5">{{ $this->auditoriaCalificaciones->links() }}</div>
        @endif

    {{-- ── TABLA GENERAL ───────────────────────────────────────────────────────── --}}
    @else

        @php
            // Pre-cargar nombres de usuarios referenciados en user_id de los cambios
            $pageRecords = $this->auditoriaGeneral->items();
            $userIds = collect($pageRecords)->flatMap(fn($r) => array_filter([
                $r->valores_antes['user_id']  ?? null,
                $r->valores_despues['user_id'] ?? null,
            ]))->unique()->filter()->values()->all();
            $usersMap = !empty($userIds)
                ? \App\Models\User::whereIn('id', $userIds)->pluck('name', 'id')
                : collect();

            // Para 'updated': mostrar solo campos que realmente cambiaron (claves de valores_despues).
            // Para 'created': todos los campos nuevos. Para 'deleted': todos los campos eliminados.
            $camposDe = fn($r) => match($r->evento) {
                'updated' => array_keys($r->valores_despues ?? []),
                'created' => array_keys($r->valores_despues ?? []),
                'deleted' => array_keys($r->valores_antes   ?? []),
                default   => [],
            };

            // Resolver valor legible: user_id → nombre, fechas → formato corto, resto truncado
            $resolveVal = fn($campo, $valor) =>
                $valor === null || $valor === ''
                    ? '—'
                    : ($campo === 'user_id' && $usersMap->has((int) $valor)
                        ? $usersMap[(int) $valor]
                        : (in_array($campo, ['created_at','updated_at','fecha_vencimiento','fecha_pago'])
                            ? \Carbon\Carbon::parse($valor)->format('d/m/Y')
                            : \Str::limit((string) $valor, 22)));

            // Etiquetas legibles para nombres de campo
            $labelCampo = fn($campo) => match($campo) {
                'estado'             => 'Estado',
                'tipo'               => 'Tipo',
                'monto'              => 'Monto',
                'monto_final'        => 'Monto total',
                'monto_original'     => 'Monto orig.',
                'descuento'          => 'Descuento',
                'user_id'            => 'Estudiante',
                'matricula_id'       => 'Matrícula',
                'obligacion_id'      => 'Obligación',
                'periodo_id'         => 'Período',
                'fecha_vencimiento'  => 'Vencimiento',
                'fecha_pago'         => 'Fecha pago',
                'descripcion'        => 'Descripción',
                'metodo_pago'        => 'Método pago',
                'codigo_referencia'  => 'Referencia',
                'numero_cuota'       => 'N° cuota',
                'comprobante_path'   => 'Comprobante',
                'name'               => 'Nombre',
                'code'               => 'Código',
                'is_active'          => 'Activo',
                'costo_carrera'      => 'Costo carrera',
                default              => ucfirst(str_replace('_', ' ', $campo)),
            };
        @endphp

        {{-- Desktop --}}
        <div class="hidden lg:block overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr class="text-gray-600 dark:text-gray-300">
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Fecha</th>
                        <th class="px-4 py-3 text-left font-semibold">Usuario</th>
                        <th class="px-4 py-3 text-left font-semibold">Modelo</th>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Evento</th>
                        <th class="px-4 py-3 text-left font-semibold">IP</th>
                        <th class="px-4 py-3 text-left font-semibold">Cambios</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($this->auditoriaGeneral as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition align-top"
                            x-data="{ open: false }">
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs">
                                {{ $r->created_at->format('d/m/Y') }}<br>
                                <span class="text-gray-400 dark:text-gray-500">{{ $r->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ $r->usuario?->name ?? 'Sistema' }}
                                </div>
                                @if ($r->usuario)
                                    <div class="text-xs text-gray-400 dark:text-gray-500">CI: {{ $r->usuario->cedula }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold
                                    bg-purple-50 text-purple-700 border border-purple-200
                                    dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800">
                                    {{ $this->modeloLabel($r->auditable_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                #{{ $r->auditable_id }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($r->evento === 'created')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold
                                        bg-green-50 text-green-700 border border-green-200
                                        dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Creación
                                    </span>
                                @elseif ($r->evento === 'updated')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold
                                        bg-yellow-50 text-yellow-700 border border-yellow-200
                                        dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>Modificación
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold
                                        bg-red-50 text-red-700 border border-red-200
                                        dark:bg-red-900/30 dark:text-red-300 dark:border-red-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Eliminación
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400 dark:text-gray-500 text-xs font-mono">
                                {{ $r->ip_address ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @php $campos = $camposDe($r); @endphp
                                @if (count($campos) > 0)
                                    <button @click="open = !open"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        <svg class="w-3.5 h-3.5 transition-transform" :class="open && 'rotate-90'"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                        {{ count($campos) }} campo(s)
                                    </button>
                                    <div x-show="open" x-transition class="mt-2 space-y-1">
                                        @foreach ($campos as $campo)
                                            @php
                                                $valAntes  = $resolveVal($campo, $r->valores_antes[$campo]  ?? null);
                                                $valDespues = $resolveVal($campo, $r->valores_despues[$campo] ?? null);
                                            @endphp
                                            <div class="flex items-center gap-1.5 text-xs">
                                                <span class="text-gray-500 dark:text-gray-400 min-w-[80px] shrink-0">
                                                    {{ $labelCampo($campo) }}
                                                </span>
                                                @if ($r->evento !== 'created')
                                                    <span class="text-red-500 dark:text-red-400 line-through truncate max-w-[90px]"
                                                        title="{{ $r->valores_antes[$campo] ?? '' }}">
                                                        {{ $valAntes }}
                                                    </span>
                                                    <span class="text-gray-400 shrink-0">→</span>
                                                @endif
                                                <span class="text-green-500 dark:text-green-400 truncate max-w-[90px] font-medium"
                                                    title="{{ $r->valores_despues[$campo] ?? '' }}">
                                                    {{ $valDespues }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                No se encontraron registros de auditoría.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="grid grid-cols-1 gap-4 lg:hidden">
            @forelse ($this->auditoriaGeneral as $r)
                @php $campos = $camposDe($r); @endphp
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-4"
                    x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $r->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm mt-0.5">
                                {{ $r->usuario?->name ?? 'Sistema' }}
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold
                                bg-purple-50 text-purple-700 border border-purple-200
                                dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800">
                                {{ $this->modeloLabel($r->auditable_type) }}
                            </span>
                            @if ($r->evento === 'created')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold
                                    bg-green-50 text-green-700 border border-green-200
                                    dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                                    Creación
                                </span>
                            @elseif ($r->evento === 'updated')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold
                                    bg-yellow-50 text-yellow-700 border border-yellow-200
                                    dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800">
                                    Modificación
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold
                                    bg-red-50 text-red-700 border border-red-200
                                    dark:bg-red-900/30 dark:text-red-300 dark:border-red-800">
                                    Eliminación
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 mb-2">
                        ID #{{ $r->auditable_id }} · IP: {{ $r->ip_address ?? '—' }}
                    </div>
                    @if (count($campos) > 0)
                        <button @click="open = !open"
                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 transition-transform" :class="open && 'rotate-90'"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            Ver {{ count($campos) }} campo(s) modificado(s)
                        </button>
                        <div x-show="open" x-transition class="mt-2 space-y-1.5">
                            @foreach ($campos as $campo)
                                @php
                                    $valAntes   = $resolveVal($campo, $r->valores_antes[$campo]  ?? null);
                                    $valDespues = $resolveVal($campo, $r->valores_despues[$campo] ?? null);
                                @endphp
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="text-gray-500 dark:text-gray-400 w-24 shrink-0 truncate">
                                        {{ $labelCampo($campo) }}
                                    </span>
                                    @if ($r->evento !== 'created')
                                        <span class="text-red-500 dark:text-red-400 line-through truncate">{{ $valAntes }}</span>
                                        <span class="text-gray-400 shrink-0">→</span>
                                    @endif
                                    <span class="text-green-500 dark:text-green-400 font-medium truncate">{{ $valDespues }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8 text-center text-gray-400 dark:text-gray-500">
                    No se encontraron registros de auditoría.
                </div>
            @endforelse
        </div>

        @if ($this->auditoriaGeneral->hasPages())
            <div class="mt-5">{{ $this->auditoriaGeneral->links() }}</div>
        @endif

    @endif
</div>
