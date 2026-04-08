<div>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900">
        <div class="max-w-2xl mx-auto px-4 py-6 space-y-5">

            {{-- ================================================================
             HEADER
             ================================================================ --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-800 dark:text-white">Asistencia</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ \Carbon\Carbon::now()->isoFormat('dddd D [de] MMMM, YYYY') }}
                    </p>
                </div>
                <a href="{{ route('administracion.docencia.asistencias.correccion') }}"
                    class="text-xs font-semibold px-3 py-2 rounded-xl bg-emerald-100 text-emerald-700
                       dark:bg-emerald-900/40 dark:text-emerald-400 hover:bg-emerald-200 transition">
                    Justificaciones
                </a>
            </div>

            {{-- MENSAJE --}}
            @if ($mensaje)
                <div
                    class="p-4 rounded-2xl text-sm font-semibold border
                {{ $tipo_mensaje === 'success'
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800'
                    : ($tipo_mensaje === 'warning'
                        ? 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800'
                        : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800') }}">
                    {{ $mensaje }}
                </div>
            @endif

            {{-- ================================================================
             MODO AUTOMÁTICO — CARDS DE HORARIOS DEL DÍA
             ================================================================ --}}
            @if (!$modoManual && empty($estudiantes))

                @if ($horariosDia->count() > 0)
                    <div class="space-y-3">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                            Tus clases de hoy
                        </p>

                        @php $ahora = \Carbon\Carbon::now(); @endphp

                        @foreach ($horariosDia as $h)
                            @php
                                $inicio = \Carbon\Carbon::parse($h->hora_inicio);
                                $fin = \Carbon\Carbon::parse($h->hora_fin);
                                $activa = $ahora->between($inicio, $fin);
                                $pasada = $ahora->gt($fin);
                                $proxima = $ahora->lt($inicio);

                                // Verificar si ya tiene asistencia registrada hoy
                                $yaGuardada = \App\Models\Asistencia::where('horario_id', $h->id)
                                    ->where('fecha', $ahora->toDateString())
                                    ->exists();
                            @endphp

                            <button wire:click="seleccionarHorario({{ $h->id }})"
                                class="w-full text-left rounded-2xl border-2 p-4 transition
                                {{ $activa
                                    ? 'border-indigo-500 bg-white dark:bg-gray-800 shadow-md shadow-indigo-100 dark:shadow-none'
                                    : 'border-slate-200 bg-white dark:bg-gray-800 dark:border-gray-700 hover:border-indigo-300' }}">

                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-slate-900 dark:text-white text-sm">
                                                {{ $h->materia?->name ?? '—' }}
                                            </span>
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full font-semibold
                                            bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                                {{ $h->paralelo?->name }}
                                            </span>
                                            @if ($yaGuardada)
                                                <span
                                                    class="text-xs px-2 py-0.5 rounded-full font-semibold
                                                bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                    ✓ Registrada
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                            {{ $inicio->format('H:i') }} – {{ $fin->format('H:i') }}
                                            @if ($h->aula)
                                                · Aula {{ $h->aula }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="flex-shrink-0 text-right">
                                        @if ($activa)
                                            <span
                                                class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                                Activa
                                            </span>
                                        @elseif ($pasada)
                                            <span class="text-xs text-slate-400">Finalizada</span>
                                        @else
                                            <span class="text-xs text-slate-400">en
                                                {{ $inicio->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($activa)
                                    <div class="mt-3 pt-3 border-t border-indigo-100 dark:border-indigo-900/40">
                                        <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ $yaGuardada ? 'Ver / editar asistencia →' : 'Tomar asistencia ahora →' }}
                                        </span>
                                    </div>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @else
                    {{-- Sin clases hoy --}}
                    <div
                        class="rounded-2xl bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 p-8 text-center">
                        <div
                            class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="font-bold text-slate-700 dark:text-slate-200">No tienes clases hoy</p>
                        <p class="text-sm text-slate-400 mt-1">Usa la búsqueda manual para otra fecha</p>
                    </div>
                @endif

                {{-- Botón modo manual --}}
                <button wire:click="activarModoManual"
                    class="w-full py-3 rounded-2xl border-2 border-dashed border-slate-300 dark:border-gray-600
                       text-sm font-semibold text-slate-500 dark:text-slate-400
                       hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    📅 Buscar otra fecha / clase anterior
                </button>
            @endif

            {{-- ================================================================
             MODO MANUAL — FILTROS
             ================================================================ --}}
            @if ($modoManual && empty($estudiantes))
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-5 space-y-4">

                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Búsqueda manual</p>
                        <button wire:click="$set('modoManual', false)"
                            class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            ← Volver
                        </button>
                    </div>

                    {{-- Periodo --}}
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Periodo</label>
                        <select wire:model.live="periodo_id_manual"
                            class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600 px-3 py-2.5
                               dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-0">
                            <option value="">Seleccionar periodo...</option>
                            @foreach ($periodos as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->code }}{{ $p->is_current ? ' (Actual)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Materia --}}
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Materia</label>
                        <select wire:model.live="materia_id_manual"
                            class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600 px-3 py-2.5
                               dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-0
                               disabled:opacity-40 disabled:cursor-not-allowed"
                            @disabled(!$periodo_id_manual)>
                            <option value="">Seleccionar materia...</option>
                            @foreach ($materiasAsignadas as $m)
                                <option value="{{ $m['materia']->id }}">{{ $m['materia']->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Paralelo --}}
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Paralelo</label>
                        <select wire:model.live="paralelo_id_manual"
                            class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600 px-3 py-2.5
                               dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-0
                               disabled:opacity-40 disabled:cursor-not-allowed"
                            @disabled(!$materia_id_manual)>
                            <option value="">Seleccionar paralelo...</option>
                            @foreach ($paralelos as $par)
                                <option value="{{ $par->id }}">{{ $par->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Horario --}}
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Horario</label>
                        <select wire:model.live="horario_id"
                            class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600 px-3 py-2.5
                               dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-0
                               disabled:opacity-40 disabled:cursor-not-allowed"
                            @disabled(!$paralelo_id_manual)>
                            <option value="">Seleccionar horario...</option>
                            @foreach ($horariosManual as $h)
                                <option value="{{ $h->id }}">
                                    {{ $h->dia_semana }} · {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                                    – {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Total clases info --}}
                    @if ($total_clases > 0)
                        <div
                            class="flex items-center gap-2 px-3 py-2 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-sm">
                            <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $total_clases }}</span>
                            <span class="text-indigo-600 dark:text-indigo-400">clases totales en el módulo</span>
                        </div>
                    @endif

                    {{-- Fecha --}}
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Fecha
                            de clase</label>
                        <input type="date" wire:model.live="fecha"
                            class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600 px-3 py-2.5
                               dark:bg-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-0
                               disabled:opacity-40 disabled:cursor-not-allowed"
                            @disabled(!$horario_id)>
                    </div>
                </div>
            @endif

            {{-- ================================================================
             FORMULARIO DE ASISTENCIA
             ================================================================ --}}
            @if (!empty($estudiantes))

                {{-- Header del formulario --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-bold text-slate-900 dark:text-white">
                                    {{ $horarioActual?->materia?->name ?? '—' }}
                                </p>
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full font-semibold
                                bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                    {{ $horarioActual?->paralelo?->name }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                {{ $horarioActual?->dia_semana }}
                                · {{ \Carbon\Carbon::parse($horarioActual?->hora_inicio)->format('H:i') }}
                                – {{ \Carbon\Carbon::parse($horarioActual?->hora_fin)->format('H:i') }}
                                · {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                {{ $total_clases }} clases totales del módulo
                            </p>
                        </div>
                        <button wire:click="$set('estudiantes', [])"
                            class="flex-shrink-0 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            ← Volver
                        </button>
                    </div>
                </div>

                {{-- BANNER YA REGISTRADA --}}
                @if ($yaRegistrada && !$modoEdicion)
                    <div
                        class="rounded-2xl border-2 border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20
                            dark:border-emerald-700 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40
                                        flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-emerald-800 dark:text-emerald-300 text-sm">
                                        Asistencia ya registrada
                                    </p>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">
                                        Esta clase ya tiene asistencia guardada. Los datos son de solo lectura.
                                    </p>
                                </div>
                            </div>
                            <button wire:click="desbloquearEdicion"
                                class="flex-shrink-0 text-xs font-bold px-3 py-2 rounded-xl
                                   bg-white dark:bg-gray-800 border border-emerald-300 dark:border-emerald-700
                                   text-emerald-700 dark:text-emerald-400
                                   hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition">
                                🔓 Editar
                            </button>
                        </div>
                    </div>
                @endif

                @if ($modoEdicion)
                    <div
                        class="rounded-2xl border-2 border-amber-400 bg-amber-50 dark:bg-amber-900/20
                            dark:border-amber-700 p-3 text-sm font-semibold text-amber-700 dark:text-amber-300
                            flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Modo edición activo — los cambios reemplazarán la asistencia guardada
                    </div>
                @endif

                {{-- Acciones masivas --}}
                @if (!$yaRegistrada || $modoEdicion)
                    <div class="flex gap-2 flex-wrap">
                        <button wire:click="marcarTodosPresentes"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-emerald-500 hover:bg-emerald-600 text-white transition">
                            ✓ Todos presentes
                        </button>
                        <button wire:click="marcarTodosAusentes"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold
                               bg-red-500 hover:bg-red-600 text-white transition">
                            ✗ Todos ausentes
                        </button>
                    </div>
                @endif

                {{-- CARDS DE ESTUDIANTES --}}
                <div class="space-y-3">
                    @foreach ($estudiantes as $index => $est)
                        @php
                            $estado = $est['estado'];
                            $sinMarcar = empty($estado);
                            $bloqueado = $yaRegistrada && !$modoEdicion;

                            $cardBorder = match ($estado) {
                                'Presente' => 'border-emerald-300 dark:border-emerald-700',
                                'Ausente' => 'border-red-300 dark:border-red-700',
                                'Tardanza' => 'border-amber-300 dark:border-amber-700',
                                'Justificado' => 'border-blue-300 dark:border-blue-700',
                                default => 'border-slate-200 dark:border-gray-700',
                            };
                            $estadoBadge = match ($estado) {
                                'Presente'
                                    => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'Ausente' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                'Tardanza' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                'Justificado' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                default => 'bg-slate-100 text-slate-400 dark:bg-gray-700 dark:text-gray-400',
                            };
                        @endphp

                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl border-2 {{ $cardBorder }} p-4 transition">

                            {{-- Fila superior: nombre + badge estado + barra --}}
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600
                                            flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                        {{ strtoupper(substr($est['nombre'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white text-sm leading-tight">
                                            {{ $est['nombre'] }}
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">
                                            {{ $est['matricula_numero'] }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 flex flex-col items-end gap-1">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $estadoBadge }}">
                                        {{ $sinMarcar ? 'Sin marcar' : $estado }}
                                    </span>
                                    @if ($est['critico'])
                                        <span class="text-xs font-bold text-red-500">🚨 Crítico</span>
                                    @elseif ($est['en_riesgo'])
                                        <span class="text-xs font-bold text-amber-500">⚠ En riesgo</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Barra de asistencia --}}
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                                    <span>{{ $est['asistidas'] }}/{{ $est['total_clases'] }} clases</span>
                                    <span class="font-semibold">{{ $est['pct_asistencia'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-500
                                    {{ $est['pct_asistencia'] >= 80
                                        ? 'bg-emerald-500'
                                        : ($est['pct_asistencia'] >= 60
                                            ? 'bg-amber-500'
                                            : 'bg-red-500') }}"
                                        style="width: {{ $est['pct_asistencia'] }}%">
                                    </div>
                                </div>
                            </div>

                            {{-- Botones de estado --}}
                            @if (!$bloqueado)
                                <div class="grid grid-cols-4 gap-2 mb-3">
                                    @foreach (['Presente' => ['bg-emerald-500', '✓'], 'Ausente' => ['bg-red-500', '✗'], 'Tardanza' => ['bg-amber-500', '⏱'], 'Justificado' => ['bg-blue-500', '📋']] as $op => [$color, $icon])
                                        <button
                                            wire:click="$set('estudiantes.{{ $index }}.estado', '{{ $op }}')"
                                            wire:click.then="actualizarContador"
                                            class="py-2 rounded-xl text-white text-xs font-bold transition
                                               {{ $estado === $op
                                                   ? $color . ' ring-2 ring-offset-1 ring-' . explode('-', $color)[1] . '-400 scale-105'
                                                   : 'bg-slate-200 dark:bg-gray-700 text-slate-600 dark:text-slate-300 hover:' . $color . ' hover:text-white' }}">
                                            {{ $icon }}<br>
                                            <span class="text-xs">{{ $op }}</span>
                                        </button>
                                    @endforeach
                                </div>

                                {{-- Hora + observaciones (solo si tiene estado) --}}
                                @if (!empty($estado))
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs text-slate-400 dark:text-slate-500 mb-1">Hora
                                                entrada</label>
                                            <input type="time"
                                                wire:model="estudiantes.{{ $index }}.hora_entrada"
                                                class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600
                                                   px-3 py-2 text-sm dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-0">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs text-slate-400 dark:text-slate-500 mb-1">Observación</label>
                                            <input type="text"
                                                wire:model="estudiantes.{{ $index }}.observaciones"
                                                placeholder="Opcional..."
                                                class="w-full rounded-xl border-2 border-slate-200 dark:border-gray-600
                                                   px-3 py-2 text-sm dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-0">
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{-- MODO SOLO LECTURA --}}
                                <div class="flex items-center gap-3 text-sm">
                                    @if (!empty($estado))
                                        @if ($est['hora_entrada'])
                                            <span class="text-slate-400 dark:text-slate-500 text-xs">
                                                🕐 {{ $est['hora_entrada'] }}
                                            </span>
                                        @endif
                                        @if ($est['observaciones'])
                                            <span class="text-slate-400 dark:text-slate-500 text-xs">
                                                💬 {{ $est['observaciones'] }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- BOTÓN GUARDAR FIJO --}}
                @if (!$yaRegistrada || $modoEdicion)
                    <div class="sticky bottom-4 pt-2">
                        <button wire:click="guardarAsistencias" wire:loading.attr="disabled"
                            class="w-full py-4 rounded-2xl font-bold text-white text-base shadow-xl transition
                               {{ $totalMarcados === count($estudiantes)
                                   ? 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 shadow-indigo-200 dark:shadow-none'
                                   : 'bg-gradient-to-r from-indigo-400 to-purple-500 shadow-indigo-100 dark:shadow-none' }}">
                            <span wire:loading.remove>
                                @if ($modoEdicion)
                                    💾 Actualizar asistencias
                                @else
                                    💾 Guardar
                                    ({{ $totalMarcados }}/{{ count($estudiantes) }})
                                @endif
                            </span>
                            <span wire:loading class="flex items-center justify-center gap-2">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                </svg>
                                Guardando...
                            </span>
                        </button>
                        @if ($totalMarcados < count($estudiantes))
                            <p class="text-center text-xs text-slate-400 dark:text-slate-500 mt-2">
                                {{ count($estudiantes) - $totalMarcados }} estudiantes sin marcar
                            </p>
                        @endif
                    </div>
                @endif

            @endif

        </div>
    </div>
</div>
