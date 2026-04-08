<div>
    <div class="min-h-screen p-4 rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <div>
            <div class="">

                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-200">
                        Asistencia de Estudiantes
                    </h1>
                    <div>
                        <a href="{{ route('administracion.docencia.asistencias.correccion') }}"
                            class="bg-green-600 hover:bg-green-700 border-green-700 border-2 text-white font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline text-sm">Justificacion
                            Asistencias</a>
                    </div>
                </div>

                {{-- ALERTA --}}
                @if ($mensaje)
                <div
                    class="mt-4 p-4 rounded-xl border
            {{ $tipo_mensaje == 'success' ? 'bg-green-100 border-green-300 text-green-800' : 'bg-red-100 border-red-300 text-red-800' }}">
                    {{ $mensaje }}
                </div>
                @endif

                {{-- FILTROS --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4">

                    {{-- PERIODO --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Período
                        </label>
                        <select wire:model.live="periodo_id"
                            class="w-full rounded-xl border-2 border-slate-200 px-3 py-2 dark:bg-gray-900 dark:text-white">
                            <option value="">Seleccione</option>
                            @foreach ($periodos as $p)
                            <option value="{{ $p->id }}">{{ $p->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- MATERIA --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Materia
                        </label>
                        <select wire:model.live="materia_id"
                            class="w-full rounded-xl border-2 border-slate-200 px-3 py-2 dark:bg-gray-900 dark:text-white"
                            @disabled(!$periodo_id)>
                            <option value="">Seleccione</option>
                            @foreach ($materias_asignadas as $m)
                            <option value="{{ $m['materia']->id }}">{{ $m['materia']->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PARALELO --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Paralelo
                        </label>
                        <select wire:model.live="paralelo_id"
                            class="w-full rounded-xl border-2 border-slate-200 px-3 py-2 dark:bg-gray-900 dark:text-white"
                            @disabled(!$materia_id)>
                            <option value="">Seleccione</option>
                            @foreach ($paralelos as $par)
                            <option value="{{ $par->id }}">{{ $par->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- HORARIO --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Horario
                        </label>
                        <select wire:model.live="horario_id"
                            class="w-full rounded-xl border-2 border-slate-200 px-3 py-2 dark:bg-gray-900 dark:text-white"
                            @disabled(!$paralelo_id)>
                            <option value="">Seleccione</option>
                            @foreach ($horarios as $h)
                            <option value="{{ $h->id }}">
                                {{ $h->dia_semana }}
                                | {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FECHA --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Fecha
                        </label>
                        <input type="date" wire:model.live="fecha"
                            class="w-full rounded-xl border-2 border-slate-200 px-3 py-2 dark:bg-gray-900 dark:text-white"
                            @disabled(!$horario_id)>
                    </div>

                </div>

                {{-- TOTAL CLASES --}}
                @if ($total_clases > 0)
                <div class="mt-5 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Total clases del módulo:
                    <span class="px-3 py-1 rounded-xl bg-indigo-600 text-white">
                        {{ $total_clases }}
                    </span>
                </div>
                @endif

                {{-- BOTONES --}}
                @if (!empty($estudiantes))
                <div class="mt-6 flex flex-wrap gap-3">
                    <button wire:click="marcarTodosPresentes"
                        class="px-4 py-2 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 transition">
                        Marcar todos presentes
                    </button>

                    <button wire:click="marcarTodosAusentes"
                        class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">
                        Marcar todos ausentes
                    </button>

                    <button wire:click="copiarHoraActualATodos"
                        class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                        Copiar hora actual
                    </button>

                    <button wire:click="limpiarTodo"
                        class="px-4 py-2 rounded-xl bg-slate-600 text-white font-semibold hover:bg-slate-700 transition">
                        Limpiar todo
                    </button>
                </div>
                @endif

                {{-- TABLA --}}
                <div class="mt-6">

                    @if (empty($estudiantes))
                    <div
                        class="p-6 bg-white dark:bg-gray-900 rounded-2xl shadow border border-slate-200 dark:border-gray-700">
                        <p class="text-slate-700 dark:text-slate-200">
                            Seleccione Período, Materia, Paralelo, Horario y Fecha para cargar estudiantes.
                        </p>
                    </div>
                    @else
                    <div
                        class="bg-white dark:bg-gray-900 rounded-2xl shadow border border-slate-200 dark:border-gray-700 overflow-hidden">

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-200 dark:bg-gray-700 text-slate-900 dark:text-white">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                            Código
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                            Estudiante
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                            Asistencia
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                            Nota (10)
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                            Entrada
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                            Observaciones
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200 dark:divide-gray-700">
                                    @foreach ($estudiantes as $index => $est)
                                    <tr
                                        class="hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                        <td class="px-3 py-4">
                                            {{ $est['estudiante']->matricula_numero }}
                                        </td>

                                        <td class="px-3 py-4">
                                            {{ $est['estudiante']->nombre_completo ?? $est['estudiante']->name }}
                                        </td>

                                        <td class="px-3 py-4{{--  font-bold --}}">
                                            {{-- {{ $est['asistidas'] }}/{{ $est['total_clases'] }} --}}
                                            @php
                                            $porcentajeAsistencia =
                                            $est['total_clases'] > 0
                                            ? round(
                                            ($est['asistidas'] / $est['total_clases']) * 100,
                                            )
                                            : 0;
                                            @endphp

                                            <div class="space-y-1">
                                                <div
                                                    class="flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    <span>{{ $est['asistidas'] }}/{{ $est['total_clases'] }}</span>
                                                    <span>{{ $porcentajeAsistencia }}%</span>
                                                </div>

                                                <div
                                                    class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                                    <div class="h-3 rounded-full transition-all duration-500
                {{ $porcentajeAsistencia >= 80 ? 'bg-green-500' : ($porcentajeAsistencia >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                        style="width: {{ $porcentajeAsistencia }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-3 py-4 font-bold">
                                            {{-- {{ $est['nota_asistencia'] }} --}}
                                            @php
                                            $nota = $est['nota_asistencia'];
                                            $porcentajeNota = round(($nota / 10) * 100);
                                            @endphp

                                            <div class="space-y-1">
                                                <div
                                                    class="flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 space-x-4">
                                                    <span>{{ number_format($nota, 2) }}/10</span>
                                                    <span>{{ $porcentajeNota }}%</span>
                                                </div>

                                                <div
                                                    class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                                    <div class="h-3 rounded-full transition-all duration-500
                {{ $nota >= 8 ? 'bg-green-500' : ($nota >= 6 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                        style="width: {{ $porcentajeNota }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-3 py-4">
                                            <select wire:model="estudiantes.{{ $index }}.estado"
                                                class="border-2 border-slate-200 rounded-xl px-3 py-2 text-sm dark:bg-gray-900 dark:text-white">
                                                <option value="">Seleccione</option>
                                                <option value="Presente">Presente</option>
                                                <option value="Ausente">Ausente</option>
                                                <option value="Tardanza">Atraso</option>
                                                <option value="Justificado">Justificado</option>
                                            </select>
                                        </td>

                                        <td class="px-3 py-4">
                                            <input type="time"
                                                wire:model="estudiantes.{{ $index }}.hora_entrada"
                                                class="border-2 border-slate-200 rounded-xl px-3 py-2 text-sm dark:bg-gray-900 dark:text-white">
                                        </td>

                                        <td class="px-3 py-4">
                                            <input type="text"
                                                wire:model="estudiantes.{{ $index }}.observaciones"
                                                class="border-2 border-slate-200 rounded-xl px-3 py-2 text-sm w-full dark:bg-gray-900 dark:text-white">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-6 flex justify-end">
                            <button wire:click="guardarAsistencias"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 focus:ring-4 focus:ring-indigo-100 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Guardar Asistencias
                            </button>
                        </div>

                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>