<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- HEADER --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Reportes Académicos</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Consulta detallada por periodo, carrera o materia</p>
        </div>

        {{-- ================================================================
             PANEL DE FILTROS
             ================================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700/60 shadow-sm p-6">
            <p class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-4">Parámetros del reporte</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                {{-- 1. Carrera (siempre visible, requerida) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                        Carrera <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="carreraId"
                        class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                               bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Seleccionar carrera...</option>
                        @foreach ($this->carreras as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. Periodo (filtrado por carrera, auto-seleccionado) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                        Periodo <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="periodoId"
                        class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                               bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        @disabled(! $carreraId)>
                        <option value="">Seleccionar periodo...</option>
                        @foreach ($this->periodos as $p)
                            <option value="{{ $p->id }}">{{ $p->code }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Tipo de reporte --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                        Tipo de reporte <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="tipoReporte"
                        class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                               bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                               shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        @disabled(! $carreraId || ! $periodoId)>
                        <option value="">Seleccionar tipo...</option>
                        <option value="carrera">Por Carrera</option>
                        <option value="materia">Por Materia</option>
                    </select>
                </div>

                {{-- 4. Materia (solo si tipo = materia) --}}
                @if ($tipoReporte === 'materia')
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">
                            Materia <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="materiaId"
                            class="w-full rounded-xl border border-gray-200 dark:border-slate-600
                                   bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100
                                   shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Seleccionar materia...</option>
                            @foreach ($this->materias as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div></div>
                @endif

            </div>
        </div>

        {{-- Loading --}}
        <div wire:loading wire:target="carreraId,periodoId,tipoReporte,materiaId"
            class="flex items-center justify-center py-12">
            <div class="flex items-center gap-3 text-gray-500 dark:text-slate-400">
                <svg class="animate-spin w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                </svg>
                <span class="text-sm font-medium">Generando reporte...</span>
            </div>
        </div>

        {{-- Estado inicial --}}
        @if (! $carreraId || ! $periodoId || ! $tipoReporte)
            <div wire:loading.remove
                class="bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-gray-200 dark:border-slate-700 py-16 text-center">
                <svg class="w-14 h-14 mx-auto mb-4 text-gray-200 dark:text-slate-700" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-400 dark:text-gray-500 font-semibold">
                    @if (! $carreraId)
                        Selecciona una carrera para comenzar
                    @elseif (! $periodoId)
                        Selecciona el periodo
                    @else
                        Selecciona el tipo de reporte
                    @endif
                </p>
            </div>
        @endif

        {{-- ================================================================
             REPORTE POR CARRERA
             ================================================================ --}}
        @if ($tipoReporte === 'carrera' && $carreraId)
            @php $rc = $this->reporteCarrera; @endphp

            @if (!empty($rc))
                <div wire:loading.remove wire:target="periodoId,tipoReporte,carreraId" class="space-y-6">

                    {{-- Encabezado del reporte --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700/60 shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-5">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-widest mb-1">
                                        Reporte por Carrera · {{ $rc['periodo']->code }}
                                    </p>
                                    <h3 class="text-white text-xl font-bold">{{ $rc['carrera']->name }}</h3>
                                    <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                        <span class="text-gray-300 text-xs">Código: {{ $rc['carrera']->code }}</span>
                                        <span class="text-gray-300 text-xs">·</span>
                                        <span class="text-gray-300 text-xs">{{ $rc['carrera']->modalidad }}</span>
                                        <span class="text-gray-300 text-xs">·</span>
                                        <span class="text-gray-300 text-xs">{{ $rc['carrera']->duracion_semestres }}
                                            semestres</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-4">
                                    <div class="flex justify-end">
                                        <button wire:click="exportarPDF"
                                            class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow">
                                            Exportar PDF
                                        </button>
                                    </div>
                                    <div class="flex gap-4">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-white">{{ $rc['total_materias'] }}</p>
                                            <p class="text-gray-400 text-xs">Materias</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-white">{{ $rc['total_estudiantes'] }}</p>
                                            <p class="text-gray-400 text-xs">Estudiantes</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-white">{{ $rc['total_horas_semana'] }}
                                            </p>
                                            <p class="text-gray-400 text-xs">Horas/sem</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Semestres --}}
                    @foreach ($rc['semestres'] as $sem)
                        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700/60 shadow-sm overflow-hidden">

                            {{-- Header semestre --}}
                            <div
                                class="px-5 py-3 bg-gray-50 dark:bg-slate-800/60 border-b border-gray-200 dark:border-slate-700/60 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-gray-800 flex items-center justify-center text-white text-xs font-bold">
                                        {{ $sem['semestre']->order }}
                                    </div>
                                    <p class="font-bold text-gray-800 dark:text-gray-100">{{ $sem['semestre']->name }}</p>
                                </div>
                                <div class="flex items-center gap-5 text-xs text-gray-500 dark:text-slate-400">
                                    <span>{{ $sem['total_materias'] }} materias</span>
                                    <span>{{ $sem['total_estudiantes'] }} estudiantes</span>
                                    <span>{{ $sem['total_horas_semana'] }} hrs/sem</span>
                                </div>
                            </div>

                            {{-- Materias --}}
                            <div class="divide-y divide-gray-100 dark:divide-slate-700/40">
                                @foreach ($sem['materias'] as $mat)
                                    <div class="p-5">

                                        {{-- Info materia --}}
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="font-bold text-gray-800 dark:text-gray-100">{{ $mat['materia']->name }}
                                                    </h4>
                                                    <span
                                                        class="text-xs px-2 py-0.5 rounded-full font-mono bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300">
                                                        {{ $mat['materia']->code }}
                                                    </span>
                                                    <span
                                                        class="text-xs px-2 py-0.5 rounded-full font-semibold
                                                        {{ $mat['materia']->tipo === 'Obligatoria' ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400' : 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400' }}">
                                                        {{ $mat['materia']->tipo }}
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center gap-4 mt-1.5 text-xs text-gray-500 dark:text-slate-400 flex-wrap">
                                                    <span>{{ $mat['materia']->credits }} créditos</span>
                                                    <span>{{ $mat['materia']->horas_teoricas }}h teóricas</span>
                                                    <span>{{ $mat['materia']->horas_practicas }}h prácticas</span>
                                                    <span>Nota mín:
                                                        {{ number_format($mat['materia']->nota_minima_aprobacion, 1) }}</span>
                                                </div>
                                            </div>
                                            @if ($mat['sin_asignacion'])
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400">
                                                    Sin docente asignado
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Paralelos --}}
                                        @if (!empty($mat['paralelos']))
                                            <div class="space-y-4">
                                                @foreach ($mat['paralelos'] as $par)
                                                    <div class="bg-gray-50 dark:bg-slate-800/60 rounded-xl p-4">
                                                        <div
                                                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">

                                                            {{-- Paralelo + Docente --}}
                                                            <div class="flex items-center gap-3">
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400">
                                                                    {{ $par['paralelo']?->name }}
                                                                </span>
                                                                @if ($par['docente'])
                                                                    <div class="flex items-center gap-2">
                                                                        <div
                                                                            class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600
                                                                                    flex items-center justify-center text-white text-xs font-bold">
                                                                            {{ strtoupper(substr($par['docente']->name, 0, 1)) }}
                                                                        </div>
                                                                        <div>
                                                                            <p
                                                                                class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                                                                {{ $par['docente']->name }}</p>
                                                                            <p class="text-xs text-gray-400">Docente
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            {{-- Stats paralelo --}}
                                                            <div class="flex items-center gap-4 text-xs flex-wrap">
                                                                <div class="text-center">
                                                                    <p class="font-bold text-gray-800 dark:text-gray-100">
                                                                        {{ $par['estudiantes'] }}</p>
                                                                    <p class="text-gray-400">Inscritos</p>
                                                                </div>
                                                                <div class="text-center">
                                                                    <p class="font-bold text-gray-800 dark:text-gray-100">
                                                                        {{ $par['cupo_actual'] }}/{{ $par['cupo_maximo'] }}
                                                                    </p>
                                                                    <p class="text-gray-400">Cupo</p>
                                                                </div>
                                                                @if ($par['promedio_grupo'])
                                                                    <div class="text-center">
                                                                        <p
                                                                            class="font-bold {{ $par['promedio_grupo'] >= 7 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                                                            {{ number_format($par['promedio_grupo'], 2) }}
                                                                        </p>
                                                                        <p class="text-gray-400">Promedio</p>
                                                                    </div>
                                                                @endif
                                                                @if ($par['horas_semana'])
                                                                    <div class="text-center">
                                                                        <p class="font-bold text-gray-800 dark:text-gray-100">
                                                                            {{ $par['horas_semana'] }}h</p>
                                                                        <p class="text-gray-400">Hrs/sem</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        {{-- Horarios --}}
                                                        @if ($par['horarios']->count() > 0)
                                                            <div class="flex flex-wrap gap-2 mt-2">
                                                                @foreach ($par['horarios'] as $h)
                                                                    <div
                                                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg
                                                                                bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-600 text-xs">
                                                                        <span
                                                                            class="font-semibold text-gray-700 dark:text-gray-200">{{ $h->dia_semana }}</span>
                                                                        <span class="text-gray-400">
                                                                            {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                                                                            –
                                                                            {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                                                                        </span>
                                                                        @if ($h->aula)
                                                                            <span class="text-gray-400">·
                                                                                {{ $h->aula }}</span>
                                                                        @endif
                                                                        @php
                                                                            $mc = match ($h->modalidad_clase) {
                                                                                'Presencial'
                                                                                    => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400',
                                                                                'Virtual'
                                                                                    => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
                                                                                'Híbrida'
                                                                                    => 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400',
                                                                                'Semipresencial'
                                                                                    => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400',
                                                                                default => 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300',
                                                                            };
                                                                        @endphp
                                                                        <span
                                                                            class="px-1.5 py-0.5 rounded text-xs font-semibold {{ $mc }}">
                                                                            {{ $h->modalidad_clase }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <p class="text-xs text-gray-400 italic mt-2">Sin horarios
                                                                registrados</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
            @endif
        @endif

        {{-- ================================================================
             REPORTE POR MATERIA
             ================================================================ --}}
        @if ($tipoReporte === 'materia' && $materiaId)
            @php $rm = $this->reporteMateria; @endphp

            @if (!empty($rm))
                <div wire:loading.remove wire:target="periodoId,tipoReporte,materiaId" class="space-y-6">

                    {{-- Encabezado --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700/60 shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-5">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-widest mb-1">
                                        Reporte por Materia · {{ $rm['periodo']->code }}
                                    </p>
                                    <h3 class="text-white text-xl font-bold">{{ $rm['materia']->name }}</h3>
                                    <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                        <span
                                            class="text-gray-300 text-xs font-mono">{{ $rm['materia']->code }}</span>
                                        <span class="text-gray-300 text-xs">·</span>
                                        <span
                                            class="text-gray-300 text-xs">{{ $rm['materia']->semestre?->name }}</span>
                                        <span class="text-gray-300 text-xs">·</span>
                                        <span
                                            class="text-gray-300 text-xs">{{ $rm['materia']->semestre?->carrera?->name }}</span>
                                        <span class="text-gray-300 text-xs">·</span>
                                        <span class="text-gray-300 text-xs">{{ $rm['materia']->credits }}
                                            créditos</span>
                                        <span class="text-gray-300 text-xs">·</span>
                                        <span class="text-gray-300 text-xs">{{ $rm['materia']->horas_teoricas }}h
                                            teóricas / {{ $rm['materia']->horas_practicas }}h prácticas</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-4">
                                    <div class="flex justify-end">
                                        <button wire:click="exportarPDF"
                                            class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow">
                                            Exportar PDF
                                        </button>
                                    </div>

                                    <div class="flex flex-row gap-4">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-white">{{ $rm['total_inscritos'] }}</p>
                                            <p class="text-gray-400 text-xs">Inscritos</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-green-400">{{ $rm['total_aprobados'] }}
                                            </p>
                                            <p class="text-gray-400 text-xs">Aprobados</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-red-400">{{ $rm['total_reprobados'] }}
                                            </p>
                                            <p class="text-gray-400 text-xs">Reprobados</p>
                                        </div>
                                        @if ($rm['promedio_general'])
                                            <div class="text-center">
                                                <p
                                                    class="text-2xl font-bold {{ $rm['promedio_general'] >= 7 ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ number_format($rm['promedio_general'], 2) }}
                                                </p>
                                                <p class="text-gray-400 text-xs">Promedio</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($rm['sin_asignacion'])
                    <div
                        class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-2xl p-5 text-amber-700 dark:text-amber-400 text-sm font-semibold text-center">
                        Esta materia no tiene docente asignado en el periodo seleccionado
                    </div>
                @endif

                {{-- Paralelos --}}
                @foreach ($rm['paralelos'] as $par)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700/60 shadow-sm overflow-hidden">

                        {{-- Header paralelo --}}
                        <div class="px-5 py-4 bg-gray-50 dark:bg-slate-800/60 border-b border-gray-200 dark:border-slate-700/60">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-bold bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400">
                                        Paralelo {{ $par['paralelo']?->name }}
                                    </span>
                                    @if ($par['docente'])
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600
                                                            flex items-center justify-center text-white text-sm font-bold">
                                                {{ strtoupper(substr($par['docente']->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                    {{ $par['docente']->name }}</p>
                                                <p class="text-xs text-gray-400">Docente ·
                                                    {{ $par['docente']->email }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Stats paralelo --}}
                                <div class="flex items-center gap-5 text-center">
                                    <div>
                                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $par['inscritos'] }}</p>
                                        <p class="text-xs text-gray-400">Inscritos</p>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $par['aprobados'] }}</p>
                                        <p class="text-xs text-gray-400">Aprobados</p>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-red-500 dark:text-red-400">{{ $par['reprobados'] }}</p>
                                        <p class="text-xs text-gray-400">Reprobados</p>
                                    </div>
                                    @if ($par['promedio'])
                                        <div>
                                            <p
                                                class="text-lg font-bold {{ $par['promedio'] >= 7 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                                {{ number_format($par['promedio'], 2) }}
                                            </p>
                                            <p class="text-xs text-gray-400">Promedio</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Horarios --}}
                            @if ($par['horarios']->count() > 0)
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach ($par['horarios'] as $h)
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg
                                                        bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-600 text-xs">
                                            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $h->dia_semana }}</span>
                                            <span class="text-gray-400">
                                                {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                                                –
                                                {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                                            </span>
                                            @if ($h->aula)
                                                <span class="text-gray-400">· Aula {{ $h->aula }}</span>
                                            @endif
                                            @php
                                                $mc = match ($h->modalidad_clase) {
                                                    'Presencial' => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400',
                                                    'Virtual' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
                                                    'Híbrida' => 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400',
                                                    'Semipresencial' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400',
                                                    default => 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300',
                                                };
                                            @endphp
                                            <span
                                                class="px-1.5 py-0.5 rounded text-xs font-semibold {{ $mc }}">
                                                {{ $h->modalidad_clase }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Tabla de estudiantes --}}
                        @if (!empty($par['estudiantes']))
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-100 dark:border-slate-700/60">
                                            <th
                                                class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                #</th>
                                            <th
                                                class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Estudiante</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Tipo</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Insumos</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Parcial</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Final</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Nota</th>
                                            <th
                                                class="px-4 py-2.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-slate-700/40">
                                        @foreach ($par['estudiantes'] as $i => $est)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40 transition">
                                                <td class="px-4 py-3 text-xs text-gray-400">{{ $i + 1 }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <p class="font-semibold text-gray-800 dark:text-gray-100 text-xs">
                                                        {{ $est['nombre'] }}</p>
                                                    <p class="text-xs text-gray-400">{{ $est['cedula'] }} ·
                                                        {{ $est['matricula_num'] }}</p>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span
                                                        class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                                            {{ $est['tipo'] === 'Arrastre'
                                                                ? 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400'
                                                                : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300' }}">
                                                        {{ $est['tipo'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300">
                                                    {{ $est['promedio_insumos'] !== null ? number_format($est['promedio_insumos'], 2) : '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300">
                                                    {{ $est['examen_parcial'] !== null ? number_format($est['examen_parcial'], 2) : '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300">
                                                    {{ $est['examen_final'] !== null ? number_format($est['examen_final'], 2) : '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ($est['nota_final'] !== null)
                                                        <span
                                                            class="text-sm font-bold {{ $est['nota_final'] >= 7 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                            {{ number_format($est['nota_final'], 2) }}
                                                        </span>
                                                        @if ($est['nota_suspenso'] !== null)
                                                            <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold">
                                                                Sus: {{ number_format($est['nota_suspenso'], 2) }}
                                                            </p>
                                                        @endif
                                                    @else
                                                        <span class="text-gray-300 dark:text-slate-600 text-xs">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @php
                                                        $cfg = match ($est['estado_final']) {
                                                            'Aprobado' => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400',
                                                            'Reprobado' => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
                                                            'Retirado' => 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400',
                                                            'Incompleto' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
                                                            'Suspenso_Pendiente' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400',
                                                            default => 'bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-slate-500',
                                                        };
                                                        $lbl = $est['estado_final'] ?? 'Pendiente';
                                                    @endphp
                                                    <span
                                                        class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $cfg }}">
                                                        {{ $lbl === 'Suspenso_Pendiente' ? 'Suspenso' : $lbl }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="px-5 py-8 text-center text-gray-400 text-sm">
                                No hay estudiantes matriculados en este paralelo
                            </div>
                        @endif
                    </div>
                @endforeach
    </div>
    @endif
    @endif
</div>
</div>
