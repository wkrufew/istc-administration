<div>
    {{-- TOAST --}}
    <div x-data="{ toasts: [] }" x-on:toast.window="toasts.push($event.detail[0]); setTimeout(() => toasts.shift(), 4000)"
        class="fixed top-4 right-4 z-50 space-y-2" style="z-index:99999">
        <template x-for="(t, i) in toasts" :key="i">
            <div x-show="true" x-transition
                :class="{ 'bg-green-600': t.tipo==='success', 'bg-red-600': t.tipo==='error', 'bg-amber-500': t.tipo==='warning' }"
                class="text-white px-5 py-3 rounded-lg shadow-lg text-sm min-w-72">
                <span x-text="t.mensaje"></span>
            </div>
        </template>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-6">

        {{-- ================================================================
             HEADER: Perfil + selector de periodo
             ================================================================ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    {{-- Avatar --}}
                    <div
                        class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600
                                flex items-center justify-center text-white text-2xl font-bold shadow-md flex-shrink-0">
                        {{ strtoupper(substr($estudiante->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $estudiante->name }}</h2>
                        <div class="flex flex-wrap items-center gap-3 mt-1">
                            @if ($estudiante->matricula_numero)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ $estudiante->matricula_numero }}
                                </span>
                            @endif
                            @if ($matricula?->carrera)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    {{ $matricula->carrera->name }}
                                </span>
                            @endif
                            @if ($estudiante->is_beca)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    🎓 Becado
                                    {{ $estudiante->beca_porcentaje ? $estudiante->beca_porcentaje . '%' : '' }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ $estudiante->email }}</p>
                    </div>
                </div>

                {{-- Selector de periodo --}}
                <div class="sm:min-w-48">
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Periodo</label>
                    <select wire:model.live="periodoSeleccionado"
                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-medium">
                        <option value="">Seleccionar periodo</option>
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo->id }}">{{ $periodo->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if (!$periodoSeleccionado)
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-8 text-center text-blue-700">
                <svg class="w-12 h-12 mx-auto mb-3 text-blue-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="font-semibold">Selecciona un periodo para ver tu información</p>
            </div>
        @elseif (!$matricula)
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center text-yellow-700">
                <svg class="w-12 h-12 mx-auto mb-3 text-yellow-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="font-semibold">No tienes una matrícula habilitada en este periodo</p>
                <p class="text-sm mt-1">Contacta a secretaría si crees que esto es un error</p>
            </div>
        @else
            {{-- ================================================================
             STATS GENERALES
             ================================================================ --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

                @php
                    $statCards = [
                        [
                            'label' => 'Materias',
                            'valor' => $stats['total_materias'],
                            'color' => 'blue',
                            'icono' =>
                                'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                        ],
                        [
                            'label' => 'Arrastres',
                            'valor' => $stats['materias_arrastre'] + $stats['pendientes_arrastre'],
                            'color' => 'yellow',
                            'icono' =>
                                'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                        ],
                        [
                            'label' => 'Aprobadas',
                            'valor' => $stats['aprobadas'],
                            'color' => 'green',
                            'icono' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        [
                            'label' => 'Reprobadas',
                            'valor' => $stats['reprobadas'],
                            'color' => 'red',
                            'icono' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        [
                            'label' => 'Créditos',
                            'valor' => number_format($stats['total_creditos'], 2),
                            'color' => 'indigo',
                            'icono' =>
                                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                        ],
                        [
                            'label' => 'Promedio',
                            'valor' => $promedioGeneral ?? '—',
                            'color' => $promedioGeneral >= 7 ? 'green' : ($promedioGeneral ? 'red' : 'gray'),
                            'icono' =>
                                'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                        ],
                    ];
                    $colorMap = [
                        'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => 'text-blue-400'],
                        'yellow' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-600', 'icon' => 'text-yellow-400'],
                        'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'icon' => 'text-green-400'],
                        'red' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'icon' => 'text-red-400'],
                        'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'icon' => 'text-indigo-400'],
                        'gray' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'icon' => 'text-gray-400'],
                    ];
                @endphp

                @foreach ($statCards as $card)
                    @php $c = $colorMap[$card['color']]; @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 {{ $c['bg'] }}">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    {{ $card['label'] }}</p>
                                <p class="text-2xl font-bold {{ $c['text'] }} mt-1">{{ $card['valor'] }}</p>
                            </div>
                            <svg class="w-6 h-6 {{ $c['icon'] }} mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="{{ $card['icono'] }}" />
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ================================================================
             FILA: Materias + Financiero
             ================================================================ --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- MATERIAS DEL PERIODO (2/3) --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800">Materias del Periodo</h3>
                        <span class="text-xs text-gray-400">{{ $matricula->periodo->code }}</span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse ($materiasConDocente as $item)
                            @php
                                $cal = $item['calificacion'];
                                $nota = $cal?->nota_final;
                                $estado = $cal?->estado_final;
                                $aprobada = $estado === 'Aprobado';
                                $esArrastre = $item['tipo'] === 'Arrastre';
                            @endphp
                            <div class="px-6 py-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-semibold text-gray-900 text-sm truncate">
                                                {{ $item['materia']->name }}
                                            </p>
                                            @if ($esArrastre)
                                                <span
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                    Arrastre
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-400 flex-wrap">
                                            <span>{{ $item['materia']->code }}</span>
                                            <span>{{ $item['materia']->credits }} créd.</span>
                                            @if ($item['paralelo'])
                                                <span>Paralelo {{ $item['paralelo']->name }}</span>
                                            @endif
                                        </div>
                                        @if ($item['docente'])
                                            <div class="flex items-center gap-1.5 mt-1.5">
                                                <div
                                                    class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold flex-shrink-0">
                                                    {{ strtoupper(substr($item['docente']->name, 0, 1)) }}
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $item['docente']->name }}</span>
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-300 mt-1">Sin docente asignado</p>
                                        @endif
                                    </div>

                                    {{-- Nota --}}
                                    <div class="text-right flex-shrink-0">
                                        @if ($nota !== null)
                                            <div class="inline-flex flex-col items-center">
                                                <span
                                                    class="text-2xl font-bold {{ $aprobada ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ number_format($nota, 2) }}
                                                </span>
                                                <span
                                                    class="text-xs font-semibold px-2 py-0.5 rounded-full mt-0.5
                                                         {{ $aprobada ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ $estado }}
                                                </span>
                                            </div>
                                        @else
                                            {{-- Barra de progreso de insumos si hay parcial --}}
                                            @if ($cal && ($cal->insumo1 || $cal->examen_parcial))
                                                <div class="text-right">
                                                    @if ($cal->examen_parcial)
                                                        <p class="text-xs text-gray-500">Parcial: <span
                                                                class="font-semibold text-gray-700">{{ $cal->examen_parcial }}</span>
                                                        </p>
                                                    @endif
                                                    @if ($cal->promedio_insumos)
                                                        <p class="text-xs text-gray-500">Insumos: <span
                                                                class="font-semibold text-gray-700">{{ $cal->promedio_insumos }}</span>
                                                        </p>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-300 italic">Sin notas</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                                No hay materias registradas en este periodo
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- PANEL DERECHO: Financiero + Arrastres (1/3) --}}
                <div class="space-y-4">

                    {{-- RESUMEN FINANCIERO --}}
                    @if ($resumenFinanciero)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100">
                                <h3 class="font-bold text-gray-800">Estado Financiero</h3>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $matricula->periodo->code }}</p>
                            </div>
                            <div class="p-5 space-y-3">

                                @if ($resumenFinanciero['tiene_vencidas'])
                                    <div
                                        class="bg-red-50 border border-red-200 rounded-xl p-3 flex items-center gap-2 text-red-700 text-xs font-semibold">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Tienes obligaciones vencidas
                                    </div>
                                @endif

                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Deuda pendiente</span>
                                        <span
                                            class="font-bold {{ $resumenFinanciero['deuda_total'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            ${{ number_format($resumenFinanciero['deuda_total'], 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Total pagado</span>
                                        <span class="font-bold text-green-600">
                                            ${{ number_format($resumenFinanciero['total_pagado'], 2) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Detalle por tipo --}}
                                <div class="border-t border-gray-100 pt-3 space-y-1.5">
                                    @foreach ($resumenFinanciero['obligaciones'] as $ob)
                                        @php
                                            $obColors = [
                                                'MATRICULA' => 'bg-blue-100 text-blue-700',
                                                'COLEGIATURA' => 'bg-purple-100 text-purple-700',
                                                'ARRASTRE' => 'bg-yellow-100 text-yellow-700',
                                                'MULTA' => 'bg-red-100 text-red-700',
                                                'OTROS' => 'bg-gray-100 text-gray-600',
                                            ];
                                        @endphp
                                        <div class="flex items-center justify-between text-xs">
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="px-1.5 py-0.5 rounded text-xs font-semibold {{ $obColors[$ob->tipo] ?? 'bg-gray-100' }}">
                                                    {{ $ob->tipo }}
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                @if ($ob->saldo > 0)
                                                    <span
                                                        class="text-red-600 font-semibold">${{ number_format($ob->saldo, 2) }}
                                                        pendiente</span>
                                                @else
                                                    <span class="text-green-600 font-semibold">✓ Pagado</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <a href="{{ route('administracion.estudiantil.obligaciones-financieras') }}"
                                    class="block w-full text-center py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white
                                       text-sm font-semibold rounded-xl transition mt-2">
                                    Ver mis pagos
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- MATERIAS ARRASTRADAS PENDIENTES --}}
                    @if ($materiasArrastradas->count() > 0)
                        <div class="bg-white rounded-2xl border border-yellow-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-yellow-100 bg-yellow-50">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <h3 class="font-bold text-yellow-800 text-sm">
                                        Arrastres Pendientes
                                    </h3>
                                    <span
                                        class="ml-auto bg-yellow-200 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded-full">
                                        {{ $materiasArrastradas->count() }}
                                    </span>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-50">
                                @foreach ($materiasArrastradas as $arrastre)
                                    <div class="px-5 py-3">
                                        <p class="text-sm font-semibold text-gray-800">{{ $arrastre->materia->name }}
                                        </p>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs text-gray-400">
                                                Reprobó en {{ $arrastre->periodoReprobado?->code ?? '—' }}
                                            </span>
                                            <div class="text-right">
                                                <span class="text-xs text-red-500 font-semibold">
                                                    Nota: {{ $arrastre->nota_obtenida }}
                                                </span>
                                                @if ($arrastre->costo_adicional > 0)
                                                    <p class="text-xs text-yellow-600 font-semibold">
                                                        +${{ number_format($arrastre->costo_adicional, 2) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-1.5">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                Intento #{{ $arrastre->numero_intento }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        @endif {{-- fin del if matricula --}}

    </div>
</div>
