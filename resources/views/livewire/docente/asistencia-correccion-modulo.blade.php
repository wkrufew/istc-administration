<div>
    <div class="p-4 rounded-2xl bg-slate-100 dark:bg-gray-800 shadow-md">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-200">
                Corrección de Asistencia (Módulo)
            </h1>
        </div>

        {{-- ALERTA --}}
        @if ($mensaje)
            <div
                class="mt-4 p-4 rounded-xl border
            {{ $tipo_mensaje == 'success'
                ? 'bg-green-100 border-green-300 text-green-800'
                : 'bg-red-100 border-red-300 text-red-800' }}">
                {{ $mensaje }}
            </div>
        @endif

        {{-- FILTROS --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">

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
                            {{ $h->dia_semana }} | {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                            - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- TOTAL CLASES --}}
        @if ($total_clases > 0)
            <div class="mt-6 text-sm font-semibold text-slate-700 dark:text-slate-200">
                Total clases del módulo:
                <span class="px-3 py-1 rounded-xl bg-indigo-600 text-white">{{ $total_clases }}</span>
            </div>
        @endif

        {{-- TABLA --}}
        <div class="mt-6">

            @if (empty($estudiantes))
                <div
                    class="p-6 bg-white dark:bg-gray-900 rounded-2xl shadow border border-slate-200 dark:border-gray-700">
                    <p class="text-slate-700 dark:text-slate-200">
                        Seleccione Período, Materia, Paralelo y Horario para cargar estudiantes.
                    </p>
                </div>
            @else
                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl shadow border border-slate-200 dark:border-gray-700 overflow-hidden">

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-200 dark:bg-gray-700 text-slate-900 dark:text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Código
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Estudiante
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Asistencias
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Nota
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                        Acción
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 dark:divide-gray-700">
                                @foreach ($estudiantes as $est)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            {{ $est['codigo_matricula'] }}
                                        </td>

                                        <td class="px-6 py-4">
                                            {{ $est['estudiante']->name }}
                                            {{ $est['estudiante']->first_name }}
                                            {{ $est['estudiante']->last_name }}
                                        </td>

                                        <td class="px-6 py-4 font-semibold">
                                            {{ $est['asistidas'] }}/{{ $est['total_clases'] }}
                                        </td>

                                        <td class="px-6 py-4 font-bold">
                                            {{ $est['nota_asistencia'] }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <button wire:click="abrirModalEditar({{ $est['detalle_id'] }})"
                                                class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                                                Editar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            @endif

        </div>

        {{-- MODAL --}}
        @if ($modalEditar)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-xl p-6">

                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-200">
                            Editar Asistencia
                        </h2>

                        <button wire:click="cerrarModal"
                            class="text-slate-600 dark:text-slate-200 hover:text-red-600 font-bold text-xl">
                            ✕
                        </button>
                    </div>

                    <div class="mt-6 space-y-4">

                        {{-- FECHA (SELECT PRO) --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                Fecha
                            </label>

                            <select wire:model.live="edit_fecha"
                                class="w-full rounded-xl border-2 border-slate-200 px-3 py-2 dark:bg-gray-900 dark:text-white">
                                @foreach ($fechas_validas as $f)
                                    <option value="{{ $f }}">
                                        {{ \Carbon\Carbon::parse($f)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ESTADO --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                Estado
                            </label>

                            <select wire:model="edit_estado"
                                class="w-full rounded-xl border-2 border-slate-200 px-3 py-2 dark:bg-gray-900 dark:text-white">
                                <option value="">Seleccione</option>
                                <option value="Presente">Presente</option>
                                <option value="Ausente">Ausente</option>
                                <option value="Tardanza">Tardanza</option>
                                <option value="Justificado">Justificado</option>
                            </select>
                        </div>

                        {{-- HORA --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                Hora Entrada
                            </label>

                            <input type="time" wire:model="edit_hora_entrada"
                                class="w-full rounded-xl border-2 border-slate-200 px-3 py-2 dark:bg-gray-900 dark:text-white">
                        </div>

                        {{-- OBS --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                Observaciones
                            </label>

                            <textarea wire:model="edit_observaciones" rows="3"
                                class="w-full rounded-xl border-2 border-slate-200 px-3 py-2 dark:bg-gray-900 dark:text-white"></textarea>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button wire:click="cerrarModal"
                            class="px-4 py-2 rounded-xl bg-slate-600 text-white font-semibold hover:bg-slate-700 transition">
                            Cancelar
                        </button>

                        <button wire:click="guardarCorreccion"
                            class="px-6 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                            Guardar
                        </button>
                    </div>

                </div>
            </div>
        @endif

    </div>

</div>
