<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-5">

    {{-- ══ SIN MOODLE_ID ════════════════════════════════════════════════════ --}}
    @if(! $this->moodleId)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center">
        <div class="w-14 h-14 rounded-2xl bg-violet-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-slate-800">Sin cuenta Moodle vinculada</h2>
        <p class="text-sm text-slate-400 mt-2 max-w-sm mx-auto">
            Tu cuenta aún no está conectada a Moodle. Contacta a secretaría para sincronizar tu acceso a la plataforma virtual.
        </p>
    </div>

    {{-- ══ MOODLE NO ACTIVO ══════════════════════════════════════════════════ --}}
    @elseif(! $this->moodleActivo)
    <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-8 text-center">
        <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-slate-800">Integración Moodle inactiva</h2>
        <p class="text-sm text-slate-400 mt-2">La integración con la plataforma virtual no está disponible en este momento.</p>
    </div>

    @else

    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-700
                            flex items-center justify-center shadow-sm shadow-violet-500/20 flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 leading-tight">Actividades Moodle</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Tareas, cuestionarios y actividades de tus cursos virtuales.</p>
                </div>
            </div>
            <button wire:click="refrescar"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold
                       text-slate-500 bg-slate-50 hover:bg-slate-100 border border-slate-200
                       transition-colors shadow-sm">
                <svg class="w-3.5 h-3.5" wire:loading.class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar
            </button>
        </div>
    </div>

    {{-- ══ STATS ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {{-- Total --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-4 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800 leading-none">{{ $this->stats['total'] }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Total</p>
            </div>
        </div>

        {{-- Vencidos --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-4 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-bold text-red-600 leading-none">{{ $this->stats['vencidos'] }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Vencidos</p>
            </div>
        </div>

        {{-- Esta semana --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-4 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-bold text-amber-600 leading-none">{{ $this->stats['esta_semana'] }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Esta semana</p>
            </div>
        </div>

        {{-- Cursos activos --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-4 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-bold text-green-600 leading-none">{{ $this->stats['cursos_activos'] }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Cursos</p>
            </div>
        </div>
    </div>

    {{-- ══ FILTROS ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-4 py-3">
        <div class="flex flex-wrap gap-2">
            @foreach([
                ['key' => 'todos',    'label' => 'Todos',    'active' => 'bg-slate-600 text-white shadow-sm'],
                ['key' => 'tareas',   'label' => 'Tareas',   'active' => 'bg-blue-600 text-white shadow-sm'],
                ['key' => 'quizzes',  'label' => 'Evaluaciones',  'active' => 'bg-violet-600 text-white shadow-sm'],
                ['key' => 'vencidos', 'label' => 'Vencidos', 'active' => 'bg-red-600 text-white shadow-sm'],
            ] as $f)
            @php $active = $this->filtro === $f['key']; @endphp
            <button wire:click="setFiltro('{{ $f['key'] }}')"
                class="inline-flex items-center px-4 py-1.5 rounded-xl text-xs font-semibold transition-all
                       {{ $active ? $f['active'] : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100' }}">
                {{ $f['label'] }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ══ SIN EVENTOS ══════════════════════════════════════════════════════ --}}
    @if(empty($this->eventosFiltrados))
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-14 text-center">
        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <p class="text-slate-500 font-semibold text-sm">Sin actividades por el momento</p>
        <p class="text-slate-400 text-xs mt-1">
            @if($filtro === 'todos')
                Cuando tus docentes publiquen tareas o cuestionarios en Moodle aparecerán aquí.
            @else
                No hay actividades con el filtro seleccionado.
            @endif
        </p>
    </div>

    @else

    {{-- ══ PRÓXIMOS ══════════════════════════════════════════════════════ --}}
    @if(! empty($this->proximos))
    <section class="space-y-3">
        <div class="flex items-center gap-2.5 px-1">
            <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Próximos</h2>
            <div class="flex-1 h-px bg-slate-200"></div>
            <span class="text-xs text-slate-400">{{ count($this->proximos) }} actividades</span>
        </div>

        @foreach($this->proximos as $evento)
        @php
            $modulo = $evento['modulename'] ?? '';
            $colorStripe = match(true) {
                $modulo === 'assign' => 'bg-blue-500',
                $modulo === 'quiz'   => 'bg-violet-500',
                $modulo === 'forum'  => 'bg-green-500',
                default              => 'bg-slate-400',
            };
            $colorBg = match(true) {
                $modulo === 'assign' => 'bg-blue-50 text-blue-700',
                $modulo === 'quiz'   => 'bg-violet-50 text-violet-700',
                $modulo === 'forum'  => 'bg-green-50 text-green-700',
                default              => 'bg-slate-100 text-slate-600',
            };
            $tipoLabel = match($modulo) {
                'assign' => 'Tarea',
                'quiz'   => 'Cuestionario',
                'forum'  => 'Foro',
                'scorm'  => 'SCORM',
                'lesson' => 'Lección',
                default  => 'Actividad',
            };
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden
                    hover:border-slate-300 hover:shadow-md transition-all duration-200">
            <div class="flex">
                <div class="w-1 flex-shrink-0 {{ $colorStripe }}"></div>
                <div class="flex-1 px-5 py-4">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold {{ $colorBg }}">
                                    {{ $tipoLabel }}
                                </span>
                                <span class="text-xs text-slate-400 truncate">{{ $evento['curso'] }}</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-800 leading-snug">{{ $evento['nombre'] }}</p>
                            @if(! empty($evento['descripcion']))
                            <p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ Str::limit($evento['descripcion'], 120) }}</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-bold text-slate-700">{{ $evento['fecha'] }}</p>
                            <p class="text-[11px] text-blue-500 font-medium mt-0.5">{{ $evento['fecha_relativa'] }}</p>
                        </div>
                    </div>
                    @if($evento['url'])
                    <div class="mt-3 pt-3 border-t border-slate-100">
                        <a href="{{ $evento['url'] }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600
                                   hover:text-blue-800 transition-colors">
                            Ver en Moodle
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </section>
    @endif

    {{-- ══ VENCIDOS ══════════════════════════════════════════════════════ --}}
    @if(! empty($this->vencidos))
    <section class="space-y-3">
        <div class="flex items-center gap-2.5 px-1">
            <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Vencidos</h2>
            <div class="flex-1 h-px bg-slate-200"></div>
            <span class="text-xs text-slate-400">{{ count($this->vencidos) }} actividades</span>
        </div>

        @foreach($this->vencidos as $evento)
        @php
            $modulo = $evento['modulename'] ?? '';
            $tipoLabel = match($modulo) {
                'assign' => 'Tarea',
                'quiz'   => 'Cuestionario',
                'forum'  => 'Foro',
                'scorm'  => 'SCORM',
                'lesson' => 'Lección',
                default  => 'Actividad',
            };
        @endphp
        <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden opacity-75
                    hover:opacity-90 hover:border-red-200 transition-all duration-200">
            <div class="flex">
                <div class="w-1 flex-shrink-0 bg-red-400"></div>
                <div class="flex-1 px-5 py-4">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-red-50 text-red-600">
                                    {{ $tipoLabel }}
                                </span>
                                <span class="text-xs text-slate-400 truncate">{{ $evento['curso'] }}</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-600 leading-snug line-through decoration-red-300">
                                {{ $evento['nombre'] }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-bold text-red-500">{{ $evento['fecha'] }}</p>
                            <p class="text-[11px] text-red-400 font-medium mt-0.5">{{ $evento['fecha_relativa'] }}</p>
                        </div>
                    </div>
                    @if($evento['url'])
                    <div class="mt-3 pt-3 border-t border-slate-100">
                        <a href="{{ $evento['url'] }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400
                                   hover:text-slate-600 transition-colors">
                            Ver en Moodle
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </section>
    @endif

    @endif {{-- fin eventos filtrados --}}

    {{-- Nota al pie --}}
    <p class="text-center text-xs text-slate-300 pb-2">
        Los datos se actualizan automáticamente cada 10 minutos · Los vencidos desaparecen después de 15 días
    </p>

    @endif {{-- fin moodle activo --}}
</div>
