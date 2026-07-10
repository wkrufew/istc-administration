<div>
    <div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

            {{-- HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Mi Horario</h2>
                    <p class="text-gray-500 text-sm mt-1">Clases asignadas para el periodo seleccionado</p>
                </div>

                {{-- SELECT PERIODO --}}
                <div class="sm:min-w-56">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Periodo</label>
                    <select wire:model.live="periodo_id"
                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-medium">
                        @foreach ($periodos as $p)
                            <option value="{{ $p->id }}">{{ $p->code ?? 'Período #' . $p->id }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- RESUMEN RÁPIDO --}}
            @php
                $totalClases   = collect($horariosPorDia)->flatten(1)->count();
                $diasConClases = collect($horariosPorDia)->filter(fn($c) => count($c) > 0)->count();
                $materiasUnicas = collect($horariosPorDia)->flatten(1)->pluck('materia')->unique()->count();
            @endphp

            @if ($totalClases > 0)
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $totalClases }}</p>
                        <p class="text-xs text-gray-500 mt-1">Clases semanales</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ $diasConClases }}</p>
                        <p class="text-xs text-gray-500 mt-1">Días con clases</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                        <p class="text-2xl font-bold text-purple-600">{{ $materiasUnicas }}</p>
                        <p class="text-xs text-gray-500 mt-1">Materias</p>
                    </div>
                </div>
            @endif

            {{-- GRILLA DE HORARIO --}}
            @if ($totalClases === 0)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-14 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="font-semibold text-gray-500">No tienes clases registradas en este periodo</p>
                    <p class="text-sm text-gray-400 mt-1">Contacta a secretaría si crees que es un error</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    @foreach ($horariosPorDia as $dia => $clases)
                        @php $tieneClases = count($clases) > 0; @endphp

                        <div class="bg-white rounded-2xl border {{ $tieneClases ? 'border-gray-200' : 'border-gray-100' }} shadow-sm overflow-hidden flex flex-col">

                            {{-- Cabecera del día: solo el nombre completo, centrado verticalmente --}}
                            <div class="px-4 py-3.5 {{ $tieneClases ? 'bg-gray-800' : 'bg-gray-50' }} flex items-center justify-between min-h-[52px]">
                                <p class="text-sm font-bold {{ $tieneClases ? 'text-white' : 'text-gray-400' }}">
                                    {{ $dia }}
                                </p>
                                @if ($tieneClases)
                                    <span class="w-6 h-6 rounded-lg bg-white/15 flex items-center justify-center text-xs font-bold text-white">
                                        {{ count($clases) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Clases --}}
                            <div class="p-3 space-y-2.5 flex-1">
                                @if (!$tieneClases)
                                    <div class="flex flex-col items-center justify-center py-8 text-gray-300">
                                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4" />
                                        </svg>
                                        <p class="text-xs">Libre</p>
                                    </div>
                                @else
                                    @foreach ($clases as $c)
                                        {{-- Tarjeta de clase --}}
                                        <div class="relative rounded-xl overflow-hidden border border-gray-100 shadow-sm
                                                    hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">

                                            {{-- Badge modalidad — esquina superior derecha --}}
                                            @if ($c['modalidad'])
                                                @php
                                                    $badgeModalidad = [
                                                        'Presencial'     => ['bg' => '#16a34a', 'label' => 'Presencial'],
                                                        'Virtual'        => ['bg' => '#2563eb', 'label' => 'Virtual'],
                                                        'Híbrida'        => ['bg' => '#7c3aed', 'label' => 'Híbrida'],
                                                        'Semipresencial' => ['bg' => '#ea580c', 'label' => 'Semi'],
                                                    ][$c['modalidad']] ?? ['bg' => '#6b7280', 'label' => $c['modalidad']];
                                                @endphp
                                                <div class="absolute top-0 right-0 rounded-bl-xl z-10
                                                            px-2 py-1 flex items-center justify-center
                                                            text-white text-xs font-bold leading-none"
                                                     style="background-color: {{ $badgeModalidad['bg'] }}">
                                                    {{ $badgeModalidad['label'] }}
                                                </div>
                                            @endif

                                            {{-- Barra de color izquierda + contenido --}}
                                            <div class="flex">
                                                <div style="background-color: {{ $c['color'] }}; width: 4px;" class="flex-shrink-0"></div>

                                                <div class="flex-1 p-3 space-y-2 min-w-0">

                                                    {{-- Hora (pr-16 para no solaparse con el badge de modalidad) --}}
                                                    <div class="flex items-center gap-1.5 pr-16">
                                                        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="text-xs font-bold text-gray-700 tabular-nums">
                                                            {{ \Carbon\Carbon::parse($c['hora_inicio'])->format('H:i') }}
                                                            <span class="text-gray-400 font-normal">–</span>
                                                            {{ \Carbon\Carbon::parse($c['hora_fin'])->format('H:i') }}
                                                        </span>
                                                    </div>

                                                    {{-- Materia: wrap completo, sin corte --}}
                                                    <div>
                                                        <p class="text-xs font-bold text-gray-900 leading-snug break-words">
                                                            {{ $c['materia'] }}
                                                        </p>
                                                        @if ($c['materia_code'])
                                                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $c['materia_code'] }}</p>
                                                        @endif
                                                    </div>

                                                    {{-- Docente: wrap completo, sin truncate --}}
                                                    <div class="flex items-start gap-1.5">
                                                        <div class="w-5 h-5 rounded-full flex items-center justify-center
                                                                    text-white text-xs font-bold flex-shrink-0 mt-0.5"
                                                             style="background-color: {{ $c['color'] }}">
                                                            {{ strtoupper(substr($c['docente'], 0, 1)) }}
                                                        </div>
                                                        <span class="text-xs text-gray-600 leading-snug break-words min-w-0">
                                                            {{ $c['docente'] }}
                                                        </span>
                                                    </div>

                                                    {{-- Footer: aula izquierda, paralelo derecha --}}
                                                    @if ($c['aula'] || $c['paralelo'])
                                                        <div class="flex items-center justify-between pt-1 border-t border-gray-100 gap-1">
                                                            {{-- Aula (izquierda) --}}
                                                            @if ($c['aula'])
                                                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                    </svg>
                                                                    Aula {{ $c['aula'] }}
                                                                </span>
                                                            @else
                                                                <span></span>
                                                            @endif

                                                            {{-- Paralelo (derecha) --}}
                                                            @if ($c['paralelo'])
                                                                <span class="text-xs font-semibold text-gray-500 flex-shrink-0">
                                                                    Paralelo: {{ $c['paralelo'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
