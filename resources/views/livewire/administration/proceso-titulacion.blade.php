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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 space-y-4">

        {{-- HEADER --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Proceso de Titulación</h2>
            <p class="text-gray-500 text-sm mt-1">Gestión de prácticas preprofesionales y titulación de estudiantes</p>
        </div>

        {{-- BUSCADOR --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                        Buscar estudiante
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="busqueda"
                            placeholder="Nombre, cédula o número de matrícula..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 shadow-sm
                                   focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    @if (strlen($busqueda) > 0 && strlen($busqueda) < 3)
                        <p class="text-xs text-amber-600 mt-1">Escribe al menos 3 caracteres para buscar</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 pt-5">
                    <input type="checkbox" wire:model.live="soloAptos" id="soloAptos"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="soloAptos" class="text-sm font-medium text-gray-700 cursor-pointer">
                        Solo malla completa
                    </label>
                </div>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Estudiante</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Carrera</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Malla</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Prácticas</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Titulación</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Acta</th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($estudiantes as $est)
                            @php
                                $matricula = $est->matriculas->first();
                                $carreraId = $matricula?->carrera_id;
                                $malla = $carreraId
                                    ? $this->estadoMallaEstudiante($est->id, $carreraId)
                                    : ['completa' => false, 'semestres_ok' => 0, 'semestres_total' => 0];
                                $practica = $carreraId ? $this->estadoPracticaEstudiante($est->id, $carreraId) : null;
                                $titulacion = $carreraId
                                    ? $this->estadoTitulacionEstudiante($est->id, $carreraId)
                                    : null;
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                {{-- Estudiante --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                                                    flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                            {{ strtoupper(substr($est->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $est->name }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ $est->matricula_numero ?? $est->cedula }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Carrera --}}
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        {{ $matricula?->carrera?->code ?? '—' }}
                                    </span>
                                </td>

                                {{-- Malla --}}
                                <td class="px-5 py-4 text-center">
                                    @if ($malla['completa'])
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Completa
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            {{ $malla['semestres_ok'] }}/{{ $malla['semestres_total'] }} sem.
                                        </span>
                                    @endif
                                </td>

                                {{-- Prácticas --}}
                                <td class="px-5 py-4 text-center">
                                    @if (!$practica)
                                        <span class="text-xs text-gray-400">—</span>
                                    @elseif ($practica->estado === 'Completada' && $practica->nota >= 7)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            ✓ {{ number_format($practica->nota, 2) }}
                                        </span>
                                    @elseif ($practica->estado === 'En_Curso')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            En curso
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Reprobada
                                        </span>
                                    @endif
                                </td>

                                {{-- Titulación --}}
                                <td class="px-5 py-4 text-center">
                                    @if (!$titulacion)
                                        <span class="text-xs text-gray-400">—</span>
                                    @elseif ($titulacion->estado === 'Aprobado')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            ✓ {{ number_format($titulacion->nota_final_egreso, 2) }}
                                        </span>
                                    @elseif ($titulacion->estado === 'Reprobado')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Reprobado
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>

                                {{-- Acta --}}
                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('administracion.administrativa.titulacion.acta', $est->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl
                                               bg-gray-100 hover:bg-indigo-100 text-gray-500 hover:text-indigo-700
                                               transition-all duration-200 group"
                                        title="Ver acta de calificaciones">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>

                                {{-- Acción --}}
                                <td class="px-5 py-4 text-center">
                                    @if ($malla['completa'])
                                        <button wire:click="abrirModal({{ $est->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold
                                                   bg-blue-600 hover:bg-blue-700 text-white transition shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Proceso
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-300 italic">No apto</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="font-medium">No se encontraron estudiantes</p>
                                    <p class="text-sm mt-1">Intenta con otro criterio de búsqueda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($estudiantes->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $estudiantes->links() }}
                </div>
            @endif
        </div>


        {{-- ================================================================
             MODAL WIZARD
             ================================================================ --}}
        @if ($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" style="z-index:99999">
                <div class="flex items-center justify-center min-h-screen px-4 py-8">
                    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="cerrarModal"></div>

                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl">

                        {{-- HEADER DEL MODAL --}}
                        <div class="bg-gray-800 px-6 py-4 rounded-t-2xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-white font-bold">Proceso de Titulación</h3>
                                    <p class="text-gray-400 text-xs mt-0.5">{{ $estudianteNombre }}</p>
                                </div>
                                <button wire:click="cerrarModal" class="text-gray-400 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- STEPS --}}
                            <div class="flex items-center gap-2 mt-4">
                                @foreach ([1 => 'Prácticas', 2 => 'Titulación', 3 => 'Resumen'] as $num => $label)
                                    <div class="flex items-center gap-2 flex-1">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-all
                                                        {{ $paso > $num ? 'bg-green-500 text-white' : ($paso === $num ? 'bg-blue-500 text-white' : 'bg-gray-600 text-gray-400') }}">
                                                @if ($paso > $num)
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @else
                                                    {{ $num }}
                                                @endif
                                            </div>
                                            <span
                                                class="text-xs font-medium {{ $paso === $num ? 'text-white' : 'text-gray-500' }}">
                                                {{ $label }}
                                            </span>
                                        </div>
                                        @if ($num < 3)
                                            <div
                                                class="flex-1 h-px {{ $paso > $num ? 'bg-green-500' : 'bg-gray-600' }} mx-2">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- RESUMEN DE MALLA (siempre visible) --}}
                        <div class="px-6 py-3 bg-gray-50 border-b border-gray-100">
                            <div class="flex items-center justify-between flex-wrap gap-3">
                                <div class="flex items-center gap-3 flex-wrap">
                                    @foreach ($semestresDetalle as $sem)
                                        <div class="text-center">
                                            <p class="text-xs text-gray-400">{{ $sem['semestre'] }}</p>
                                            <p
                                                class="text-sm font-bold {{ $sem['promedio'] >= 7 ? 'text-green-600' : ($sem['promedio'] ? 'text-red-500' : 'text-gray-400') }}">
                                                {{ $sem['promedio'] ? number_format($sem['promedio'], 2) : '—' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400">Promedio Malla</p>
                                    <p
                                        class="text-xl font-bold {{ $promedioMalla >= 7 ? 'text-green-600' : ($promedioMalla ? 'text-red-500' : 'text-gray-400') }}">
                                        {{ $promedioMalla ? number_format($promedioMalla, 2) : '—' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- ================================================
                             PASO 1: PRÁCTICAS PREPROFESIONALES
                             ================================================ --}}
                        @if ($paso === 1)
                            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">

                                @if ($practicaId)
                                    <div
                                        class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Práctica registrada. Puedes actualizar los datos y la nota progresivamente.
                                    </div>
                                @endif

                                @error('practica_general')
                                    <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-xl p-3">
                                        {{ $message }}</p>
                                @enderror

                                {{-- Empresa --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2 sm:col-span-1">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Empresa /
                                            Institución <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="practicaEmpresa"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            placeholder="Nombre de la empresa">
                                        @error('practicaEmpresa')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sector</label>
                                        <select wire:model="practicaSector"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">Seleccionar...</option>
                                            @foreach (['Público', 'Privado', 'ONG', 'Educativo', 'Salud', 'Tecnología', 'Otro'] as $s)
                                                <option value="{{ $s }}">{{ $s }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Dirección de la
                                            empresa</label>
                                        <input type="text" wire:model="practicaDireccion"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tutor en empresa
                                            <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="practicaTutorEmpresa"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        @error('practicaTutorEmpresa')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Cargo del
                                            tutor</label>
                                        <input type="text" wire:model="practicaCargoTutor"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Teléfono
                                            empresa</label>
                                        <input type="text" wire:model="practicaTelefono"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email
                                            empresa</label>
                                        <input type="email" wire:model="practicaEmail"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Cargo del
                                            estudiante <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="practicaCargoEstudiante"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        @error('practicaCargoEstudiante')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Actividades
                                            realizadas</label>
                                        <textarea wire:model="practicaActividades" rows="2"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha inicio
                                            <span class="text-red-500">*</span></label>
                                        <input type="date" wire:model="practicaFechaInicio"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        @error('practicaFechaInicio')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha fin</label>
                                        <input type="date" wire:model="practicaFechaFin"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Total
                                            horas</label>
                                        <input type="number" wire:model="practicaTotalHoras" min="1"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Estado</label>
                                        <select wire:model="practicaEstado"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="En_Curso">En Curso</option>
                                            <option value="Completada">Completada</option>
                                            <option value="Reprobada">Reprobada</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                                            Nota
                                            <span class="font-normal text-gray-400">(sobre 10, mín. 7)</span>
                                        </label>
                                        <input type="number" wire:model="practicaNota" step="0.01"
                                            min="0" max="10"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            placeholder="0.00">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-gray-600 mb-1">Observaciones</label>
                                        <input type="text" wire:model="practicaObservaciones"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 border-t border-gray-100 flex justify-between">
                                <button wire:click="cerrarModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                                    Cancelar
                                </button>
                                <button wire:click="guardarPractica" wire:loading.attr="disabled"
                                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="guardarPractica">Guardar y Continuar
                                        →</span>
                                    <span wire:loading wire:target="guardarPractica">Guardando...</span>
                                </button>
                            </div>
                        @endif

                        {{-- ================================================
                             PASO 2: TITULACIÓN
                             ================================================ --}}
                        @if ($paso === 2)
                            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">

                                @error('titulacion_general')
                                    <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-xl p-3">
                                        {{ $message }}</p>
                                @enderror

                                {{-- Tipo de titulación --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">
                                        Modalidad de Titulación <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="titulacionTipo"
                                                value="Examen_Complexivo" class="sr-only peer">
                                            <div
                                                class="border-2 rounded-xl p-3 text-center transition-all
                                                        peer-checked:border-blue-600 peer-checked:bg-blue-50
                                                        hover:border-blue-300 border-gray-200">
                                                <p
                                                    class="text-sm font-semibold text-gray-700 peer-checked:text-blue-700">
                                                    Examen Complexivo
                                                </p>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="titulacionTipo"
                                                value="Proyecto_Investigacion" class="sr-only peer">
                                            <div
                                                class="border-2 rounded-xl p-3 text-center transition-all
                                                        peer-checked:border-blue-600 peer-checked:bg-blue-50
                                                        hover:border-blue-300 border-gray-200">
                                                <p class="text-sm font-semibold text-gray-700">
                                                    Proyecto de Investigación
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                                            Nota de titulación
                                            <span class="font-normal text-gray-400">(mín. 7)</span>
                                        </label>
                                        <input type="number" wire:model="titulacionNota" step="0.01"
                                            min="0" max="10"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha de
                                            evaluación</label>
                                        <input type="date" wire:model="titulacionFechaEval"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Presidente del
                                            tribunal</label>
                                        <input type="text" wire:model="titulacionPresidente"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Miembro 1</label>
                                        <input type="text" wire:model="titulacionMiembro1"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Miembro 2</label>
                                        <input type="text" wire:model="titulacionMiembro2"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha de
                                            registro</label>
                                        <input type="date" wire:model="titulacionFechaRegistro"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-gray-600 mb-1">Observaciones</label>
                                        <input type="text" wire:model="titulacionObservaciones"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 border-t border-gray-100 flex justify-between">
                                <button wire:click="$set('paso', 1)"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                                    ← Atrás
                                </button>
                                <button wire:click="guardarTitulacion" wire:loading.attr="disabled"
                                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="guardarTitulacion">Guardar y Ver Resumen
                                        →</span>
                                    <span wire:loading wire:target="guardarTitulacion">Guardando...</span>
                                </button>
                            </div>
                        @endif

                        {{-- ================================================
                             PASO 3: RESUMEN
                             ================================================ --}}
                        @if ($paso === 3)
                            @php
                                $titulacionFinal = $titulacionId
                                    ? \App\Models\NotaTitulacion::find($titulacionId)
                                    : null;
                                $aprobado = $titulacionFinal?->estado === 'Aprobado';
                            @endphp
                            <div class="p-6 space-y-5">

                                {{-- Resultado visual --}}
                                <div class="text-center py-4">
                                    @if ($aprobado)
                                        <div
                                            class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-9 h-9 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <p class="text-2xl font-bold text-green-600">
                                            {{ number_format($titulacionFinal->nota_final_egreso, 2) }}
                                        </p>
                                        <p class="text-sm font-semibold text-green-700 mt-1">Aprobado</p>
                                    @elseif ($titulacionFinal?->estado === 'Reprobado')
                                        <div
                                            class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-9 h-9 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <p class="text-2xl font-bold text-red-600">
                                            {{ number_format($titulacionFinal->nota_final_egreso, 2) }}
                                        </p>
                                        <p class="text-sm font-semibold text-red-700 mt-1">Reprobado</p>
                                    @else
                                        <div
                                            class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-9 h-9 text-amber-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-amber-700">Proceso incompleto — faltan
                                            notas</p>
                                    @endif
                                </div>

                                {{-- Desglose de notas --}}
                                <div class="bg-gray-50 rounded-2xl p-4 space-y-3">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Desglose de
                                        notas</h4>

                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Promedio de malla</span>
                                            <span
                                                class="text-sm font-bold {{ $titulacionFinal?->promedio_malla >= 7 ? 'text-green-600' : 'text-red-500' }}">
                                                {{ $titulacionFinal?->promedio_malla ? number_format($titulacionFinal->promedio_malla, 2) : '—' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">
                                                {{ $titulacionFinal?->tipo_titulacion_label ?? 'Titulación' }}
                                            </span>
                                            <span
                                                class="text-sm font-bold {{ $titulacionFinal?->nota_titulacion >= 7 ? 'text-green-600' : ($titulacionFinal?->nota_titulacion ? 'text-red-500' : 'text-gray-400') }}">
                                                {{ $titulacionFinal?->nota_titulacion ? number_format($titulacionFinal->nota_titulacion, 2) : '—' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Prácticas preprofesionales</span>
                                            <span
                                                class="text-sm font-bold {{ $titulacionFinal?->nota_practicas >= 7 ? 'text-green-600' : ($titulacionFinal?->nota_practicas ? 'text-red-500' : 'text-gray-400') }}">
                                                {{ $titulacionFinal?->nota_practicas ? number_format($titulacionFinal->nota_practicas, 2) : '—' }}
                                            </span>
                                        </div>
                                        <div class="border-t border-gray-200 pt-2 flex justify-between items-center">
                                            <span class="text-sm font-bold text-gray-700">Nota final de egreso</span>
                                            <span
                                                class="text-lg font-bold {{ $aprobado ? 'text-green-600' : ($titulacionFinal?->nota_final_egreso ? 'text-red-600' : 'text-gray-400') }}">
                                                {{ $titulacionFinal?->nota_final_egreso ? number_format($titulacionFinal->nota_final_egreso, 2) : '—' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Intento --}}
                                @if ($titulacionFinal?->numero_intento > 1)
                                    <p class="text-xs text-center text-amber-600 font-medium">
                                        Intento #{{ $titulacionFinal->numero_intento }}
                                    </p>
                                @endif
                            </div>

                            <div class="px-6 py-4 border-t border-gray-100 flex justify-between">
                                <button wire:click="$set('paso', 2)"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                                    ← Atrás
                                </button>
                                <div class="flex gap-2">
                                    @if ($aprobado)
                                        {{-- TODO: enlace al acta cuando esté lista --}}
                                        <button disabled
                                            class="px-5 py-2 text-sm font-semibold text-white bg-green-600 rounded-xl opacity-60 cursor-not-allowed">
                                            Generar Acta (próximamente)
                                        </button>
                                    @endif
                                    <button wire:click="cerrarModal"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
