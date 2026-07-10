<div
    x-data="{}"
    x-effect="document.body.style.overflow = $wire.mostrar_formulario ? 'hidden' : ''">

    {{-- Mensajes globales --}}
    @if ($mensaje)
        <div class="mb-6">
            <div class="bg-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-50 border-l-4 border-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-400 p-4 rounded-r-xl shadow-sm flex items-center justify-between">
                <p class="text-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-800 font-medium text-sm">{{ $mensaje }}</p>
                <button wire:click="$set('mensaje', '')" class="text-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-400 hover:text-{{ $tipo_mensaje === 'success' ? 'emerald' : 'red' }}-600 ml-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- ── Filtros ──────────────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-slate-50 via-emerald-50 to-teal-100 rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
        <h2 class="text-xl font-semibold text-slate-800 mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
            </svg>
            Filtros de Búsqueda
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Período Académico</label>
                <div class="relative">
                    <select wire:model.live="periodo_id" class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-2.5 text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all appearance-none">
                        <option value="">Seleccione un período</option>
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo->id }}">{{ $periodo->code }} - {{ $periodo->description }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            @if ($periodo_id && count($materias_asignadas) > 0)
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">Materia</label>
                    <div class="relative">
                        <select wire:model.live="materia_id" class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-2.5 text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all appearance-none">
                            <option value="">Seleccione una materia</option>
                            @foreach ($materias_asignadas as $data)
                                <option value="{{ $data['materia']->id }}">{{ $data['materia']->code }} - {{ $data['materia']->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            @endif

            @if ($materia_id && count($paralelos) > 0)
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">Paralelo</label>
                    <div class="relative">
                        <select wire:model.live="paralelo_id" class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-2.5 text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all appearance-none">
                            <option value="">Seleccione un paralelo</option>
                            @foreach ($paralelos as $paralelo)
                                <option value="{{ $paralelo->id }}">{{ $paralelo->name }} ({{ $paralelo->code }})</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Lista de Estudiantes ─────────────────────────────────────────────────── --}}
    @if (count($estudiantes) > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                        </svg>
                        Lista de Estudiantes
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ count($estudiantes) }} estudiantes · {{ collect($estudiantes)->where('tipo', 'Arrastre')->count() }} arrastres
                    </p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Estudiante</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Cédula</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Tipo</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Nota Final</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($estudiantes as $est)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-semibold text-sm shrink-0">
                                            {{ substr($est['estudiante']->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $est['estudiante']->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $est['codigo_matricula'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-slate-600">{{ $est['estudiante']->cedula ?? 'N/A' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                        @if ($est['tipo'] === 'Arrastre') bg-amber-100 text-amber-800
                                        @elseif($est['tipo'] === 'Validacion') bg-blue-100 text-blue-800
                                        @else bg-emerald-100 text-emerald-800 @endif">
                                        {{ $est['tipo'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($est['nota_final'] !== null)
                                        <span class="inline-flex px-3 py-1 rounded-lg text-sm font-bold
                                            @if ($est['nota_final'] >= $nota_minima_aprobacion) bg-emerald-100 text-emerald-800
                                            @elseif($est['nota_final'] >= $nota_minima_aprobacion - 3) bg-amber-100 text-amber-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ number_format($est['nota_final'], 2) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-sm">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                            @if ($est['estado_final'] === 'Aprobado') bg-emerald-100 text-emerald-800
                                            @elseif($est['estado_final'] === 'Reprobado') bg-red-100 text-red-800
                                            @elseif($est['estado_final'] === 'Borrador') bg-slate-100 text-slate-600
                                            @else bg-slate-100 text-slate-700 @endif">
                                            {{ $est['estado_final'] }}
                                        </span>
                                        @if ($est['es_borrador'] ?? false)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.207 2.207L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                                Borrador
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <button
                                        wire:click="abrirFormularioCalificacion({{ $est['detalle_matricula_id'] }})"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium rounded-xl text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-4 focus:ring-emerald-100 transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        {{ $est['tiene_calificacion'] ? 'Editar' : 'Calificar' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($paralelo_id)
        <div class="bg-emerald-50 border-l-4 border-emerald-400 p-5 rounded-r-2xl">
            <p class="text-emerald-800 font-medium text-sm">No hay estudiantes inscritos en este paralelo.</p>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════════
         MODAL DE CALIFICACIÓN
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if ($mostrar_formulario && $estudiante_seleccionado)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            {{-- Modal box: flex column, sticky header + footer --}}
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col animate-modal-in"
                 style="max-height: calc(100vh - 2rem);">

                {{-- ── Header fijo ──────────────────────────────────────────── --}}
                <div class="flex-none bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-5 rounded-t-2xl">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-lg shrink-0">
                                {{ substr($estudiante_seleccionado['estudiante']->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white leading-tight">
                                    {{ $estudiante_seleccionado['estudiante']->name }}
                                </h3>
                                <p class="text-indigo-200 text-sm mt-0.5">
                                    {{ $estudiante_seleccionado['codigo_matricula'] }}
                                    @if ($estudiante_seleccionado['estudiante']->cedula)
                                        · C.I. {{ $estudiante_seleccionado['estudiante']->cedula }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            {{-- Intento badge --}}
                            <div class="text-center">
                                <span class="block text-indigo-200 text-xs font-medium mb-0.5">Intento</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold
                                    @if ($numero_intento >= self::MAX_INTENTOS_PERMITIDOS) bg-red-500 text-white
                                    @elseif($numero_intento === 2) bg-amber-400 text-amber-900
                                    @else bg-white/20 text-white @endif">
                                    {{ $numero_intento }} / {{ self::MAX_INTENTOS_PERMITIDOS }}
                                </span>
                            </div>

                            {{-- Tipo badge --}}
                            <span class="inline-flex px-3 py-1.5 rounded-lg text-xs font-semibold
                                @if ($estudiante_seleccionado['tipo'] === 'Arrastre') bg-amber-400 text-amber-900
                                @elseif($estudiante_seleccionado['tipo'] === 'Validacion') bg-blue-300 text-blue-900
                                @else bg-emerald-400 text-emerald-900 @endif">
                                {{ $estudiante_seleccionado['tipo'] }}
                            </span>

                            <button wire:click="cerrarFormulario"
                                class="text-white/70 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Borrador notice --}}
                    @if ($es_borrador)
                        <div class="mt-3 flex items-center gap-2 bg-amber-400/20 border border-amber-400/30 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-amber-300 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.207 2.207L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                            <p class="text-amber-200 text-xs font-medium">En borrador — puedes seguir ingresando notas y guardar cuando estén completas.</p>
                        </div>
                    @endif

                    @if ($numero_intento >= self::MAX_INTENTOS_PERMITIDOS)
                        <div class="mt-3 flex items-center gap-2 bg-red-500/20 border border-red-400/30 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-red-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                            <p class="text-red-200 text-xs font-medium">Último intento permitido para esta materia.</p>
                        </div>
                    @endif
                </div>

                {{-- ── Cuerpo con scroll ────────────────────────────────────── --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-5 modal-scroll">

                    {{-- Insumos --}}
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 overflow-hidden">
                        <div class="px-5 py-3 bg-emerald-100/70 border-b border-emerald-200 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <h4 class="text-sm font-semibold text-emerald-800">Insumos de Evaluación <span class="font-normal text-emerald-600">({{ $nuevo_calculo ? '60%' : '30%' }})</span></h4>
                            <span class="ml-auto text-lg font-bold text-emerald-700">{{ number_format($promedio_insumos, 2) }}</span>
                            <span class="text-xs text-emerald-600">promedio</span>
                        </div>
                        <div class="p-5 grid grid-cols-2 md:grid-cols-3 gap-4">
                            {{-- Insumo 1 — Asistencia --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center justify-between text-sm font-semibold text-slate-700">
                                    <span>Asistencia</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                        {{ $asistencias_asistidas }}/{{ $asistencias_totales }}
                                    </span>
                                </label>
                                @if($insumo1_manual)
                                    <input type="number" step="0.01" min="0" max="10"
                                        wire:model.live="insumo1"
                                        class="w-full bg-white border-2 border-amber-400 rounded-xl px-3 py-2.5 text-slate-800 font-bold text-center text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                                    <p class="text-xs text-amber-600 font-medium">Nota ingresada manualmente</p>
                                @else
                                    <input type="text" value="{{ number_format((float) $insumo1, 2) }}" readonly
                                        class="w-full bg-indigo-50 border-2 border-indigo-200 rounded-xl px-3 py-2.5 text-indigo-800 font-bold text-center text-sm cursor-not-allowed">
                                    <p class="text-xs text-slate-400">Calculado automáticamente</p>
                                @endif
                                <label class="flex items-center gap-2 cursor-pointer select-none mt-1">
                                    <input type="checkbox" wire:model.live="insumo1_manual"
                                        class="w-3.5 h-3.5 rounded accent-amber-500 cursor-pointer">
                                    <span class="text-xs text-slate-500">Ingresar nota directa</span>
                                </label>
                                @error('insumo1')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                            </div>

                            {{-- Insumos 2–5 --}}
                            @foreach ([
                                ['insumo2', 'Actividades Autónomas'],
                                ['insumo3', 'Actividades Prácticas'],
                                ['insumo4', 'Actividades con Docente'],
                                ['insumo5', 'Ética'],
                            ] as [$field, $label])
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-semibold text-slate-700">{{ $label }}</label>
                                    <input type="number" step="0.01" min="0" max="10"
                                        wire:model.live="{{ $field }}"
                                        oninput="var v=parseFloat(this.value);if(!isNaN(v)){if(v>10)this.value='10';if(v<0)this.value='0';}"
                                        class="w-full bg-white border-2 rounded-xl px-3 py-2.5 text-slate-700 text-sm focus:ring-4 transition-all
                                            {{ $errors->has($field) ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-200 focus:border-emerald-500 focus:ring-emerald-100' }}">
                                    @error($field)
                                        <p class="text-red-500 text-xs flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Exámenes --}}
                    <div class="rounded-xl border border-blue-200 bg-blue-50/50 overflow-hidden">
                        <div class="px-5 py-3 bg-blue-100/70 border-b border-blue-200 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h4 class="text-sm font-semibold text-blue-800">Exámenes <span class="font-normal text-blue-600">({{ $nuevo_calculo ? '40%' : '70%' }})</span></h4>
                        </div>
                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-700">Examen Parcial <span class="font-normal text-slate-500">({{ $nuevo_calculo ? '20%' : '30%' }})</span></label>
                                <input type="number" step="0.01" min="0" max="10"
                                    wire:model.live="examen_parcial"
                                    oninput="var v=parseFloat(this.value);if(!isNaN(v)){if(v>10)this.value='10';if(v<0)this.value='0';}"
                                    class="w-full bg-white border-2 rounded-xl px-4 py-2.5 text-slate-700 text-sm focus:ring-4 transition-all
                                        {{ $errors->has('examen_parcial') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100' }}">
                                @error('examen_parcial')
                                    <p class="text-red-500 text-xs flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-700">Examen Final <span class="font-normal text-slate-500">({{ $nuevo_calculo ? '20%' : '40%' }})</span></label>
                                <input type="number" step="0.01" min="0" max="10"
                                    wire:model.live="examen_final"
                                    oninput="var v=parseFloat(this.value);if(!isNaN(v)){if(v>10)this.value='10';if(v<0)this.value='0';}"
                                    class="w-full bg-white border-2 rounded-xl px-4 py-2.5 text-slate-700 text-sm focus:ring-4 transition-all
                                        {{ $errors->has('examen_final') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100' }}">
                                @error('examen_final')
                                    <p class="text-red-500 text-xs flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Suspenso (condicional) --}}
                    @if ($suspenso)
                        <div class="rounded-xl border border-amber-300 bg-amber-50/60 overflow-hidden">
                            <div class="px-5 py-3 bg-amber-100 border-b border-amber-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                <h4 class="text-sm font-semibold text-amber-800">
                                    Examen de Suspenso
                                    <span class="font-normal text-amber-600">— nota base {{ number_format($nota_base, 2) }} (entre {{ number_format($nota_minima_aprobacion - 3, 2) }} y {{ number_format($nota_minima_aprobacion - 0.01, 2) }})</span>
                                </h4>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-semibold text-amber-800">Nota de Suspenso <span class="font-normal">(sobre 10)</span></label>
                                    <input type="number" step="0.01" min="0" max="10"
                                        wire:model.live="nota_suspenso"
                                        oninput="var v=parseFloat(this.value);if(!isNaN(v)){if(v>10)this.value='10';if(v<0)this.value='0';}"
                                        class="w-full bg-white border-2 rounded-xl px-4 py-2.5 text-slate-700 text-sm focus:ring-4 transition-all
                                            {{ $errors->has('nota_suspenso') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-amber-300 focus:border-amber-500 focus:ring-amber-100' }}">
                                    @error('nota_suspenso')
                                        <p class="text-red-500 text-xs flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="text-xs text-amber-600">Incremento = (nota ÷ 10) × 2.99</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-semibold text-slate-700">Nota Final con Suspenso</label>
                                    <input type="text" value="{{ number_format($nota_final, 2) }}" readonly
                                        class="w-full border-0 rounded-xl px-4 py-2.5 font-bold text-xl text-center shadow-sm
                                            @if ($nota_final >= $nota_minima_aprobacion) bg-gradient-to-r from-emerald-500 to-green-600 text-white
                                            @else bg-gradient-to-r from-red-500 to-rose-600 text-white @endif">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Resultado final --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Nota base --}}
                        <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 text-center">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Nota Base</p>
                            <p class="text-3xl font-extrabold text-slate-800">{{ number_format($nota_base, 2) }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ $nuevo_calculo ? '60% + 20% + 20%' : '30% + 30% + 40%' }}</p>
                        </div>
                        {{-- Nota final --}}
                        <div class="rounded-xl border p-4 text-center
                            @if ($nota_final >= $nota_minima_aprobacion) bg-emerald-50 border-emerald-200
                            @elseif($nota_final >= $nota_minima_aprobacion - 3) bg-amber-50 border-amber-200
                            @elseif($nota_final > 0) bg-red-50 border-red-200
                            @else bg-slate-50 border-slate-200 @endif">
                            <p class="text-xs font-semibold uppercase tracking-wide mb-1.5
                                @if ($nota_final >= $nota_minima_aprobacion) text-emerald-600
                                @elseif($nota_final >= $nota_minima_aprobacion - 3) text-amber-600
                                @elseif($nota_final > 0) text-red-600
                                @else text-slate-500 @endif">Nota Final</p>
                            <p class="text-3xl font-extrabold
                                @if ($nota_final >= $nota_minima_aprobacion) text-emerald-700
                                @elseif($nota_final >= $nota_minima_aprobacion - 3) text-amber-700
                                @elseif($nota_final > 0) text-red-700
                                @else text-slate-400 @endif">{{ number_format($nota_final, 2) }}</p>
                            @if ($suspenso && $nota_suspenso)
                                <p class="text-xs mt-1 text-amber-600">incluye suspenso</p>
                            @endif
                        </div>
                        {{-- Estado --}}
                        <div class="rounded-xl border p-4 text-center
                            @if ($estado_final === 'Aprobado') bg-emerald-50 border-emerald-200
                            @elseif($estado_final === 'Reprobado') bg-red-50 border-red-200
                            @else bg-slate-50 border-slate-200 @endif">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Estado</p>
                            <p class="text-xl font-bold
                                @if ($estado_final === 'Aprobado') text-emerald-700
                                @elseif($estado_final === 'Reprobado') text-red-700
                                @else text-slate-500 @endif">
                                {{ $estado_final ?: '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Fórmula detalle --}}
                    <div class="bg-indigo-50 rounded-xl border border-indigo-200 p-4 text-sm text-indigo-800">
                        <p class="font-semibold mb-1">Cálculo {{ $nuevo_calculo ? '(nuevo sistema)' : '(sistema antiguo)' }}:</p>
                        @if ($nuevo_calculo)
                            <p class="text-indigo-700">
                                ({{ number_format($promedio_insumos, 2) }} × 0.6) +
                                ({{ $examen_parcial ?: '0' }} × 0.2) +
                                ({{ $examen_final ?: '0' }} × 0.2) =
                                <strong>{{ number_format($nota_base, 2) }}</strong>
                                @if ($suspenso && $nota_suspenso)
                                    + ({{ $nota_suspenso }} ÷ 10 × 2.99) =
                                    <strong>{{ number_format($nota_final, 2) }}</strong>
                                @endif
                            </p>
                        @else
                            <p class="text-indigo-700">
                                ({{ number_format($promedio_insumos, 2) }} × 0.3) +
                                ({{ $examen_parcial ?: '0' }} × 0.3) +
                                ({{ $examen_final ?: '0' }} × 0.4) =
                                <strong>{{ number_format($nota_base, 2) }}</strong>
                                @if ($suspenso && $nota_suspenso)
                                    + ({{ $nota_suspenso }} ÷ 10 × 2.99) =
                                    <strong>{{ number_format($nota_final, 2) }}</strong>
                                @endif
                            </p>
                        @endif
                    </div>

                </div>

                {{-- ── Footer fijo ──────────────────────────────────────────── --}}
                <div class="flex-none border-t border-slate-200 bg-white px-6 py-4 rounded-b-2xl flex items-center justify-between gap-3">
                    {{-- Info izquierda --}}
                    <div class="flex items-center gap-3 text-sm text-slate-500 min-w-0">
                        <div class="flex items-center gap-1.5 bg-slate-100 rounded-lg px-3 py-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="text-xs font-medium text-slate-600">Intento {{ $numero_intento }}/{{ self::MAX_INTENTOS_PERMITIDOS }}</span>
                        </div>
                        @if ($es_borrador)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-100 text-amber-700 rounded-lg text-xs font-medium shrink-0">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.207 2.207L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                Borrador guardado
                            </span>
                        @endif
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button wire:click="cerrarFormulario"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 border-2 border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancelar
                        </button>

                        <button wire:click="guardarBorrador"
                            wire:loading.attr="disabled"
                            wire:target="guardarBorrador"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 border-2 border-indigo-500 text-indigo-600 text-sm font-semibold rounded-xl hover:bg-indigo-50 focus:outline-none focus:ring-4 focus:ring-indigo-100 transition-all disabled:opacity-60">
                            <svg class="w-4 h-4" wire:loading.remove wire:target="guardarBorrador" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <svg class="w-4 h-4 animate-spin" wire:loading wire:target="guardarBorrador" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Guardar Borrador
                        </button>

                        <button wire:click="guardarCalificacion"
                            wire:loading.attr="disabled"
                            wire:target="guardarCalificacion"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-sm font-semibold rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-200 transition-all shadow-sm disabled:opacity-60">
                            <svg class="w-4 h-4" wire:loading.remove wire:target="guardarCalificacion" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg class="w-4 h-4 animate-spin" wire:loading wire:target="guardarCalificacion" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Publicar Notas
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

    <style>
        @keyframes modal-in {
            from { opacity: 0; transform: scale(0.96) translateY(8px); }
            to   { opacity: 1; transform: scale(1)    translateY(0);   }
        }
        .animate-modal-in {
            animation: modal-in 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .modal-scroll::-webkit-scrollbar { width: 5px; }
        .modal-scroll::-webkit-scrollbar-track { background: #f8fafc; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 9999px; }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: #a5b4fc; }
    </style>
</div>
