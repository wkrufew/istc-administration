<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ══ CABECERA ══ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('administracion.administrativa.convalidaciones.index') }}"
                   wire:navigate
                   class="hover:text-green-600 dark:hover:text-green-400 transition-colors">
                    ← Volver
                </a>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Validación de Conocimientos
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Registro de materias aprobadas por examen de validación
            </p>
        </div>

        {{-- Chip del estudiante --}}
        @if ($this->estudiante)
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                        rounded-2xl px-4 py-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center
                            text-green-700 dark:text-green-300 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($this->estudiante->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-tight">
                        {{ $this->estudiante->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        C.C.: {{ $this->estudiante->cedula ?? '—' }}
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- ══ INDICADOR DE PASOS ══ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4">
        <div class="flex items-center justify-between">
            @foreach ([1 => 'Datos generales', 2 => 'Materias y notas', 3 => 'Confirmación'] as $num => $label)
                <div class="flex items-center {{ $num < 3 ? 'flex-1' : '' }}">
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $paso >= $num
                                ? 'bg-green-600 text-white'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500' }}">
                            @if ($paso > $num)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <span class="text-xs font-medium hidden sm:block
                            {{ $paso >= $num ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $label }}
                        </span>
                    </div>
                    @if ($num < 3)
                        <div class="flex-1 h-px mx-3 {{ $paso > $num ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══ PASO 1 — DATOS GENERALES ══ --}}
    @if ($paso === 1)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 space-y-5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                Datos generales de la validación
            </h3>

            {{-- Carrera --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Carrera <span class="text-red-500">*</span>
                </label>
                <select wire:model.live="carreraId"
                        class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                               shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                    <option value="">— Selecciona una carrera —</option>
                    @foreach ($this->carreras as $carrera)
                        <option value="{{ $carrera->id }}">{{ $carrera->name }}</option>
                    @endforeach
                </select>
                @error('carreraId')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Documento validante --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Documento validante
                    <span class="text-xs font-normal text-gray-400 dark:text-gray-500 ml-1">(PDF, opcional)</span>
                </label>
                <div class="flex items-center gap-3">
                    <label class="cursor-pointer flex items-center gap-2 px-4 py-2 rounded-xl border border-dashed
                                  border-gray-300 dark:border-gray-600 hover:border-green-400 dark:hover:border-green-500
                                  text-sm text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400
                                  transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Subir PDF
                        <input type="file" wire:model="documento" accept=".pdf" class="hidden">
                    </label>
                    @if ($documento)
                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">
                            ✓ {{ $documento->getClientOriginalName() }}
                        </span>
                    @else
                        <span class="text-xs text-gray-400 dark:text-gray-500">Sin archivo</span>
                    @endif
                </div>
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Observaciones
                    <span class="text-xs font-normal text-gray-400 dark:text-gray-500 ml-1">(opcional)</span>
                </label>
                <textarea wire:model="observaciones" rows="3"
                          placeholder="Ej: Estudiante aprobó examen de validación el 15/07/2026 con tribunal conformado por..."
                          class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                 dark:placeholder-gray-500 shadow-sm focus:border-green-500 focus:ring-green-500
                                 text-sm resize-none"></textarea>
            </div>
        </div>
    @endif

    {{-- ══ PASO 2 — MATERIAS Y NOTAS ══ --}}
    @if ($paso === 2)
        <div class="space-y-4">

            {{-- Info --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-4
                        flex items-start gap-3 text-sm text-blue-800 dark:text-blue-300">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Marca las materias que el estudiante aprobó en el examen de validación e ingresa la nota obtenida (0–10).
                Si la nota es ≥ a la nota mínima de la materia se marca como <strong>Aprobado</strong>, de lo contrario <strong>Reprobado</strong>.</span>
            </div>

            @error('seleccionadas')
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl
                            px-4 py-3 text-sm text-red-700 dark:text-red-300">
                    {{ $message }}
                </div>
            @enderror

            {{-- Semestres --}}
            @foreach ($this->semestres as $semestre)
                @php
                    $totalSem      = $semestre->materias->count();
                    $selecSem      = $semestre->materias->filter(fn($m) => isset($this->seleccionadas[(string)$m->id]))->count();
                    $completo      = $this->semestreCompleto($semestre);
                @endphp

                <div x-data="{ open: true }"
                     class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

                    {{-- Header semestre --}}
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-5 py-4 text-left
                                   hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center
                                        text-green-700 dark:text-green-400 font-bold text-sm">
                                {{ $semestre->order ?? $loop->iteration }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ strtoupper($semestre->name) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $totalSem }} materia{{ $totalSem !== 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($completo)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg
                                             bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                                             text-xs font-semibold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Semestre completo
                                </span>
                            @elseif ($selecSem > 0)
                                <span class="px-2 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/30
                                             text-amber-700 dark:text-amber-300 text-xs font-semibold">
                                    {{ $selecSem }}/{{ $totalSem }} seleccionadas
                                </span>
                            @endif
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200"
                                 :class="{ 'rotate-180': open }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>

                    {{-- Materias --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="border-t border-gray-100 dark:border-gray-700">
                        <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach ($semestre->materias as $materia)
                                @php
                                    $key       = (string) $materia->id;
                                    $marcada   = isset($this->seleccionadas[$key]);
                                    $notaMin   = floatval($materia->nota_minima_aprobacion ?? 7.00);
                                    $nota      = floatval($this->seleccionadas[$key]['nota'] ?? 0);
                                    $estadoM   = $marcada && $nota > 0
                                                    ? ($nota >= $notaMin ? 'Aprobado' : 'Reprobado')
                                                    : null;
                                @endphp
                                <div class="flex items-center gap-4 px-5 py-3
                                            {{ $marcada ? 'bg-green-50/50 dark:bg-green-900/10' : '' }}
                                            transition-colors">

                                    {{-- Checkbox --}}
                                    <button type="button"
                                            wire:click="toggleMateria({{ $materia->id }})"
                                            class="w-5 h-5 rounded flex-shrink-0 border-2 flex items-center justify-center transition-colors
                                                   {{ $marcada
                                                       ? 'bg-green-600 border-green-600'
                                                       : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:border-green-400' }}">
                                        @if ($marcada)
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </button>

                                    {{-- Nombre materia --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $materia->name }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $materia->code }}
                                            &nbsp;·&nbsp; Nota mín.: {{ number_format($notaMin, 2) }}
                                            &nbsp;·&nbsp; {{ round(($materia->horas_teoricas + $materia->horas_practicas) / 48, 2) }} créditos
                                        </p>
                                    </div>

                                    {{-- Input nota --}}
                                    @if ($marcada)
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <input type="number"
                                                   wire:model.blur="seleccionadas.{{ $materia->id }}.nota"
                                                   min="0" max="10" step="0.01"
                                                   placeholder="0.00"
                                                   class="w-20 text-center rounded-lg border-gray-200 dark:border-gray-600
                                                          dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm
                                                          focus:border-green-500 focus:ring-green-500">

                                            {{-- Badge estado --}}
                                            @if ($estadoM === 'Aprobado')
                                                <span class="px-2 py-1 rounded-lg bg-green-100 dark:bg-green-900/40
                                                             text-green-700 dark:text-green-300 text-xs font-semibold whitespace-nowrap">
                                                    Aprobado
                                                </span>
                                            @elseif ($estadoM === 'Reprobado')
                                                <span class="px-2 py-1 rounded-lg bg-red-100 dark:bg-red-900/40
                                                             text-red-700 dark:text-red-300 text-xs font-semibold whitespace-nowrap">
                                                    Reprobado
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-700
                                                             text-gray-400 dark:text-gray-500 text-xs font-semibold whitespace-nowrap">
                                                    Sin nota
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ══ PASO 3 — CONFIRMACIÓN ══ --}}
    @if ($paso === 3)
        @php $res = $this->resumen(); @endphp
        <div class="space-y-4">

            {{-- Resumen de números --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700
                            shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $res['total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Materias seleccionadas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700
                            shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $res['aprobadas'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aprobadas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700
                            shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold {{ $res['reprobadas'] > 0 ? 'text-red-500 dark:text-red-400' : 'text-gray-300 dark:text-gray-600' }}">
                        {{ $res['reprobadas'] }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Reprobadas</p>
                </div>
            </div>

            {{-- Detalle por semestre --}}
            @foreach ($this->semestres as $semestre)
                @php
                    $materiasSelec = $semestre->materias->filter(fn($m) => isset($this->seleccionadas[(string)$m->id]));
                @endphp
                @if ($materiasSelec->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-750 border-b border-gray-100 dark:border-gray-700
                                    flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                {{ strtoupper($semestre->name) }}
                            </span>
                            @if ($this->semestreCompleto($semestre))
                                <span class="text-xs font-semibold text-green-600 dark:text-green-400">
                                    ✓ Semestre completo
                                </span>
                            @endif
                        </div>
                        <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach ($materiasSelec as $materia)
                                @php
                                    $nota    = floatval($this->seleccionadas[(string)$materia->id]['nota'] ?? 0);
                                    $notaMin = floatval($materia->nota_minima_aprobacion ?? 7.00);
                                    $ap      = $nota >= $notaMin;
                                @endphp
                                <div class="flex items-center justify-between px-5 py-2.5">
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $materia->name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $materia->code }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-bold {{ $ap ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                            {{ number_format($nota, 2) }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold
                                            {{ $ap
                                                ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                                                : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">
                                            {{ $ap ? 'Aprobado' : 'Reprobado' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Advertencia --}}
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800
                        rounded-2xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-sm text-amber-800 dark:text-amber-300">
                    <p class="font-semibold mb-1">Al confirmar se generará lo siguiente:</p>
                    <ul class="space-y-0.5 text-amber-700 dark:text-amber-400">
                        <li>• Una matrícula de tipo <strong>Validación</strong> en el período activo</li>
                        <li>• Registros de calificación para cada materia seleccionada</li>
                        <li>• El estudiante podrá matricularse normalmente en las materias restantes</li>
                    </ul>
                    <p class="mt-2 font-medium">Esta acción no puede deshacerse fácilmente.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ══ BOTONES DE NAVEGACIÓN ══ --}}
    <div class="flex items-center justify-between">
        <button type="button"
                wire:click="{{ $paso > 1 ? 'anteriorPaso' : '' }}"
                {{ $paso === 1 ? 'disabled' : '' }}
                class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-sm font-medium
                       text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800
                       hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors
                       disabled:opacity-40 disabled:cursor-not-allowed">
            ← Anterior
        </button>

        @if ($paso < $totalPasos)
            <button type="button"
                    wire:click="siguientePaso"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-semibold
                           transition-colors shadow-sm disabled:opacity-60">
                <span wire:loading.remove wire:target="siguientePaso">Siguiente →</span>
                <span wire:loading wire:target="siguientePaso">Procesando...</span>
            </button>
        @else
            <button type="button"
                    wire:click="confirmar"
                    wire:loading.attr="disabled"
                    wire:confirm="¿Confirmas la generación de registros de validación? Esta acción creará calificaciones permanentes."
                    class="px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-semibold
                           transition-colors shadow-sm disabled:opacity-60">
                <span wire:loading.remove wire:target="confirmar">✓ Confirmar y generar registros</span>
                <span wire:loading wire:target="confirmar">Generando...</span>
            </button>
        @endif
    </div>

    {{-- ══ HISTORIAL DE CONVALIDACIONES ══ --}}
    @if ($this->historial->isNotEmpty())
        <div class="mt-8 space-y-3">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Historial de convalidaciones
            </h3>

            @foreach ($this->historial as $conv)
                <div x-data="{ open: false }"
                     class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-5 py-4 text-left
                                   hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                        <div class="flex items-center gap-3">
                            {{-- Estado badge --}}
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold
                                {{ $conv->estado === 'Confirmada'
                                    ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                {{ $conv->estado }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $conv->carrera->name ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Período: {{ $conv->periodo->code ?? '—' }}
                                    &nbsp;·&nbsp; {{ $conv->detalles->count() }} materia{{ $conv->detalles->count() !== 1 ? 's' : '' }}
                                    &nbsp;·&nbsp; Registrado por: {{ $conv->registradoPor->name ?? '—' }}
                                    &nbsp;·&nbsp; {{ $conv->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($conv->documento_path)
                                <a href="{{ Storage::url($conv->documento_path) }}"
                                   target="_blank"
                                   wire:click.stop
                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline px-2 py-1
                                          rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    Ver PDF
                                </a>
                            @endif
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                 :class="{ 'rotate-180': open }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="border-t border-gray-100 dark:border-gray-700">
                        @if ($conv->observaciones)
                            <p class="px-5 pt-3 text-xs text-gray-500 dark:text-gray-400 italic">
                                {{ $conv->observaciones }}
                            </p>
                        @endif
                        <div class="divide-y divide-gray-50 dark:divide-gray-700/50 px-2 pb-2 pt-2">
                            @foreach ($conv->detalles->sortBy('materia.name') as $det)
                                <div class="flex items-center justify-between px-3 py-2">
                                    <div>
                                        <p class="text-sm text-gray-800 dark:text-gray-200">{{ $det->materia->name ?? '—' }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $det->materia->code ?? '—' }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold
                                            {{ $det->estado === 'Aprobado' ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                            {{ number_format($det->nota, 2) }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold
                                            {{ $det->estado === 'Aprobado'
                                                ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                                                : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">
                                            {{ $det->estado }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
