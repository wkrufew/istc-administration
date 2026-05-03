<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-0.5 pb-4 space-y-4" x-data="{
    toasts: [],
    addToast(msg, type) {
        const id = Date.now();
        this.toasts.push({ id, msg, type });
        setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 3500);
    }
}"
    x-on:flash.window="addToast($event.detail.message, $event.detail.type)"
    x-on:modal-scroll-lock.window="
        const sy = window.scrollY;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${sy}px`;
        document.body.style.width = '100%';
    "
    x-on:modal-scroll-unlock.window="
        const sy = Math.abs(parseInt(document.body.style.top || '0'));
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        window.scrollTo(0, sy);
    ">

    {{-- ── TOASTS ──────────────────────────────────────────────────────────── --}}
    <div class="fixed top-5 right-5 z-[200] space-y-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-x-4"
                class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-sm font-semibold text-white pointer-events-auto"
                :class="toast.type === 'success' ? 'bg-emerald-600' : 'bg-red-600'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        x-show="toast.type === 'success'" d="M5 13l4 4L19 7" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        x-show="toast.type !== 'success'" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span x-text="toast.msg"></span>
            </div>
        </template>
    </div>

    {{-- ── HEADER ──────────────────────────────────────────────────────────── --}}
    <div
        class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700/60 rounded-2xl shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Gestión de Materias
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Administra el catálogo de materias del plan de estudios
                </p>
            </div>
            <button wire:click="abrirModalCrear"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                       bg-gray-800 dark:bg-slate-700 hover:bg-gray-700 dark:hover:bg-slate-600
                       text-white text-sm font-semibold shadow-sm transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Materia
            </button>
        </div>

        {{-- Filtros --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
            {{-- Búsqueda --}}
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o código…"
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm
                           border border-gray-200 dark:border-slate-600
                           bg-white dark:bg-slate-800
                           text-gray-900 dark:text-gray-100
                           placeholder-gray-400 dark:placeholder-slate-500
                           focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition">
            </div>

            {{-- Tipo --}}
            <select wire:model.live="tipoFilter"
                class="w-full py-2.5 px-3 rounded-xl text-sm
                       border border-gray-200 dark:border-slate-600
                       bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                       focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition">
                <option value="">Todos los tipos</option>
                <option value="Obligatoria">Obligatoria</option>
                <option value="Electiva">Electiva</option>
                <option value="Nivelacion">Nivelación</option>
            </select>

            {{-- Carrera --}}
            <select wire:model.live="carreraFilter"
                class="w-full py-2.5 px-3 rounded-xl text-sm
                       border border-gray-200 dark:border-slate-600
                       bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                       focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition">
                <option value="">Todas las carreras</option>
                @foreach ($this->carreras as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ── TABLA ───────────────────────────────────────────────────────────── --}}
    <div
        class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700/60 rounded-2xl shadow-sm overflow-hidden">

        {{-- Loading overlay --}}
        <div wire:loading wire:target="search,tipoFilter,carreraFilter"
            class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 flex items-center justify-center z-10 rounded-2xl">
            <svg class="animate-spin w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
            </svg>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="bg-gray-50 dark:bg-slate-800/60 border-b border-gray-200 dark:border-slate-700/60
                               text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-bold">
                        <th class="px-5 py-3.5 text-left">Materia</th>
                        <th class="px-5 py-3.5 text-left">Carrera / Semestre</th>
                        <th class="px-5 py-3.5 text-center">Tipo</th>
                        <th class="px-5 py-3.5 text-center">Horas T/P</th>
                        <th class="px-5 py-3.5 text-center">Créditos</th>
                        <th class="px-5 py-3.5 text-center">Nota mín.</th>
                        <th class="px-5 py-3.5 text-center">Estado</th>
                        <th class="px-5 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700/40">
                    @forelse ($this->materias as $mat)
                        @php
                            $tipoCfg = match ($mat->tipo) {
                                'Obligatoria' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
                                'Electiva' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
                                'Nivelacion' => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400',
                                default => 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40 transition">
                            {{-- Materia --}}
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $mat->name }}</p>
                                <p class="text-xs font-mono text-gray-400 mt-0.5">{{ $mat->code }}</p>
                            </td>

                            {{-- Carrera / Semestre --}}
                            <td class="px-5 py-3.5">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $mat->semestre?->carrera?->name ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $mat->semestre?->name ?? '—' }}
                                </p>
                            </td>

                            {{-- Tipo --}}
                            <td class="px-5 py-3.5 text-center">
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $tipoCfg }}">
                                    {{ $mat->tipo }}
                                </span>
                            </td>

                            {{-- Horas --}}
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5 text-xs">
                                    <span
                                        class="font-semibold text-indigo-600 dark:text-indigo-400">T:{{ $mat->horas_teoricas }}</span>
                                    <span class="text-gray-300 dark:text-slate-600">/</span>
                                    <span
                                        class="font-semibold text-emerald-600 dark:text-emerald-400">P:{{ $mat->horas_practicas }}</span>
                                </div>
                            </td>

                            {{-- Créditos --}}
                            <td class="px-5 py-3.5 text-center">
                                <span class="text-sm font-bold text-gray-800 dark:text-gray-100">
                                    {{ number_format($mat->credits, 2) }}
                                </span>
                            </td>

                            {{-- Nota mínima --}}
                            <td class="px-5 py-3.5 text-center">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    {{ number_format($mat->nota_minima_aprobacion, 2) }}
                                </span>
                            </td>

                            {{-- Estado --}}
                            <td class="px-5 py-3.5 text-center">
                                @if ($mat->is_active)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                                 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400
                                                 border border-green-200 dark:border-green-800/50">
                                        ● Activa
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                                 bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400
                                                 border border-gray-200 dark:border-slate-600">
                                        ● Inactiva
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="abrirModalEditar({{ $mat->id }})" title="Editar"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center
                                               bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                                               hover:bg-blue-100 dark:hover:bg-blue-900/60 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="eliminar({{ $mat->id }})"
                                        wire:confirm="¿Eliminar la materia '{{ $mat->name }}'? Esta acción no se puede deshacer."
                                        title="Eliminar"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center
                                               bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400
                                               hover:bg-red-100 dark:hover:bg-red-900/60 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-7 h-7 text-gray-300 dark:text-slate-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-semibold">No se encontraron materias
                                </p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Ajusta los filtros o crea una
                                    nueva materia</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->materias->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 dark:border-slate-700/60">
                {{ $this->materias->links() }}
            </div>
        @endif
    </div>

    {{-- ── MODAL CREAR / EDITAR ─────────────────────────────────────────────── --}}
    @if ($modalAbierto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="cerrarModal"></div>

            {{-- Panel --}}
            <div
                class="relative w-full max-w-2xl flex flex-col max-h-[90vh] overflow-hidden
                        bg-white dark:bg-slate-900 border border-white/10 dark:border-slate-600/50
                        rounded-2xl shadow-2xl">

                {{-- Header del modal --}}
                <div
                    class="flex items-center justify-between px-6 py-4 bg-gray-800 dark:bg-slate-800 rounded-t-2xl flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white">
                                {{ $editandoId ? 'Editar Materia' : 'Nueva Materia' }}
                            </h2>
                            <p class="text-xs text-gray-400">
                                {{ $editandoId ? 'Modifica los datos y prerequisitos' : 'Completa la información de la materia' }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="cerrarModal"
                        class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Tabs (solo al editar) --}}
                @if ($editandoId)
                    <div
                        class="flex-shrink-0 border-b border-gray-200 dark:border-slate-700/60 bg-white dark:bg-slate-900 px-6">
                        <nav class="flex gap-1 -mb-px">
                            <button wire:click="$set('tabActiva', 'datos')"
                                class="px-4 py-3 text-sm font-semibold border-b-2 transition
                                       {{ $tabActiva === 'datos'
                                           ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                           : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                                Datos generales
                            </button>
                            <button wire:click="$set('tabActiva', 'prerequisitos')"
                                class="px-4 py-3 text-sm font-semibold border-b-2 transition
                                       {{ $tabActiva === 'prerequisitos'
                                           ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                           : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                                Prerequisitos
                            </button>
                        </nav>
                    </div>
                @endif

                {{-- Cuerpo scrollable --}}
                <div class="flex-1 overflow-y-auto overscroll-contain min-h-0">

                    {{-- ── TAB: DATOS GENERALES ──────────────────────────────── --}}
                    @if ($tabActiva === 'datos')
                        <div class="p-6 space-y-5">

                            {{-- Nombre + Código --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="name" placeholder="Ej: Álgebra Lineal"
                                        class="w-full rounded-xl px-3 py-2.5 text-sm
                                               border border-gray-200 dark:border-slate-600
                                               bg-white dark:bg-slate-800
                                               text-gray-900 dark:text-gray-100
                                               placeholder-gray-400 dark:placeholder-slate-500
                                               focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition">
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                        Código <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="code" placeholder="Ej: MAT-101"
                                        class="w-full rounded-xl px-3 py-2.5 text-sm font-mono
                                               border border-gray-200 dark:border-slate-600
                                               bg-white dark:bg-slate-800
                                               text-gray-900 dark:text-gray-100
                                               placeholder-gray-400 dark:placeholder-slate-500
                                               focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition">
                                    @error('code')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Carrera + Semestre --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                        Carrera <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="carreraIdForm"
                                        class="w-full rounded-xl px-3 py-2.5 text-sm
                                               border border-gray-200 dark:border-slate-600
                                               bg-white dark:bg-slate-800
                                               text-gray-900 dark:text-gray-100
                                               focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition">
                                        <option value="">Seleccionar carrera…</option>
                                        @foreach ($this->carreras as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                        Semestre <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="semestre_id" @disabled(!$carreraIdForm)
                                        class="w-full rounded-xl px-3 py-2.5 text-sm
                                               border border-gray-200 dark:border-slate-600
                                               bg-white dark:bg-slate-800
                                               text-gray-900 dark:text-gray-100
                                               focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500
                                               disabled:opacity-50 disabled:cursor-not-allowed transition">
                                        <option value="">
                                            {{ $carreraIdForm ? 'Seleccionar semestre…' : 'Primero elige una carrera' }}
                                        </option>
                                        @foreach ($this->semestresForm as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('semestre_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Horas Teóricas + Prácticas + Créditos --}}
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                        Horas teóricas <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model.live="horas_teoricas" min="0"
                                        placeholder="0"
                                        class="w-full rounded-xl px-3 py-2.5 text-sm
                                               border border-gray-200 dark:border-slate-600
                                               bg-white dark:bg-slate-800
                                               text-gray-900 dark:text-gray-100
                                               focus:outline-none focus:ring-2 focus:ring-indigo-500/60 focus:border-indigo-500 transition">
                                    @error('horas_teoricas')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                        Horas prácticas <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model.live="horas_practicas" min="0"
                                        placeholder="0"
                                        class="w-full rounded-xl px-3 py-2.5 text-sm
                                               border border-gray-200 dark:border-slate-600
                                               bg-white dark:bg-slate-800
                                               text-gray-900 dark:text-gray-100
                                               focus:outline-none focus:ring-2 focus:ring-emerald-500/60 focus:border-emerald-500 transition">
                                    @error('horas_practicas')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-xs font-semibold text-gray-600 dark:text-slate-300">
                                            Créditos <span class="text-red-500">*</span>
                                        </label>
                                        @if ($creditosOverride && ($horas_teoricas || $horas_practicas))
                                            <button wire:click="resetCreditos" type="button"
                                                class="text-xs text-blue-500 dark:text-blue-400 hover:underline flex items-center gap-1">
                                                ↺ Recalcular
                                            </button>
                                        @elseif (!$creditosOverride && ($horas_teoricas || $horas_practicas))
                                            <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                                                Auto ✓
                                            </span>
                                        @endif
                                    </div>
                                    <input type="number" wire:model.blur="credits" min="0" max="20"
                                        step="0.01" placeholder="0.00"
                                        class="w-full rounded-xl px-3 py-2.5 text-sm font-semibold
                                               border transition
                                               {{ $creditosOverride
                                                   ? 'border-amber-300 dark:border-amber-600 bg-amber-50 dark:bg-amber-950/20'
                                                   : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800' }}
                                               text-gray-900 dark:text-gray-100
                                               focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500">
                                    @if ($horas_teoricas || $horas_practicas)
                                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">
                                            ({{ (int) $horas_teoricas }} + {{ (int) $horas_practicas }}) / 48
                                            =
                                            {{ number_format(((int) $horas_teoricas + (int) $horas_practicas) / 48, 2) }}
                                        </p>
                                    @endif
                                    @error('credits')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Nota mínima + Tipo + Estado --}}
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                        Nota mínima <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model="nota_minima_aprobacion" min="0"
                                        max="10" step="0.01" placeholder="7.00"
                                        class="w-full rounded-xl px-3 py-2.5 text-sm
                                               border border-gray-200 dark:border-slate-600
                                               bg-white dark:bg-slate-800
                                               text-gray-900 dark:text-gray-100
                                               focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition">
                                    @error('nota_minima_aprobacion')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                        Tipo <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="tipo"
                                        class="w-full rounded-xl px-3 py-2.5 text-sm
                                               border border-gray-200 dark:border-slate-600
                                               bg-white dark:bg-slate-800
                                               text-gray-900 dark:text-gray-100
                                               focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition">
                                        <option value="Obligatoria">Obligatoria</option>
                                        <option value="Electiva">Electiva</option>
                                        <option value="Nivelacion">Nivelación</option>
                                    </select>
                                    @error('tipo')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex flex-col justify-center" x-data="{ isActive: $wire.entangle('is_active') }">
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-3">
                                        Estado
                                    </label>
                                    <div class="flex items-center gap-2.5">
                                        <button type="button" @click="isActive = !isActive"
                                            class="relative w-10 h-6 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/40 flex-shrink-0"
                                            :class="isActive ? 'bg-blue-500 dark:bg-blue-600' : 'bg-gray-200 dark:bg-slate-700'">
                                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-200"
                                                :class="isActive ? 'translate-x-4' : 'translate-x-0'"></div>
                                        </button>
                                        <span class="text-sm font-semibold"
                                            :class="isActive ? 'text-green-600 dark:text-green-400' :
                                                'text-gray-400 dark:text-slate-500'"
                                            x-text="isActive ? 'Activa' : 'Inactiva'"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Descripción --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                                    Descripción
                                </label>
                                <textarea wire:model="description" rows="3" placeholder="Descripción opcional de la materia…"
                                    class="w-full rounded-xl px-3 py-2.5 text-sm resize-none
                                           border border-gray-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800
                                           text-gray-900 dark:text-gray-100
                                           placeholder-gray-400 dark:placeholder-slate-500
                                           focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition">
                                </textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    @endif

                    {{-- ── TAB: PREREQUISITOS ────────────────────────────────── --}}
                    @if ($tabActiva === 'prerequisitos' && $editandoId)
                        <div class="p-6">
                            @livewire('administration.gestion-prerequisitos', ['materia_id' => $editandoId], key('prereq-' . $editandoId))
                        </div>
                    @endif

                </div>{{-- fin cuerpo scrollable --}}

                {{-- Footer (solo en tab datos) --}}
                @if ($tabActiva === 'datos')
                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4
                                border-t border-gray-200 dark:border-slate-700/60
                                bg-white dark:bg-slate-900 rounded-b-2xl">
                        <button wire:click="cerrarModal" type="button"
                            class="px-4 py-2 rounded-xl text-sm font-semibold
                                   border border-gray-200 dark:border-slate-600
                                   text-gray-600 dark:text-slate-300
                                   hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                            Cancelar
                        </button>
                        <button wire:click="guardar" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold
                                   bg-gray-800 dark:bg-slate-700 hover:bg-gray-700 dark:hover:bg-slate-600
                                   text-white shadow-sm transition disabled:opacity-60">
                            <span wire:loading.remove wire:target="guardar">
                                {{ $editandoId ? 'Actualizar materia' : 'Crear materia' }}
                            </span>
                            <span wire:loading wire:target="guardar" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                </svg>
                                Guardando…
                            </span>
                        </button>
                    </div>
                @endif

            </div>{{-- fin panel --}}
        </div>{{-- fin overlay --}}
    @endif

</div>
