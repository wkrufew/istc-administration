{{-- Mostrar errores de validación --}}
@if ($errors->any())
    <div class="mb-5 rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 p-4">
        <div class="font-semibold text-red-700 dark:text-red-300 mb-2">¡Ups! Algo salió mal.</div>
        <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Formulario para crear o editar un horario --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Día</label>
        <select name="dia_semana"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                <option value="{{ $dia }}" @selected(old('dia_semana', $horario->dia_semana ?? '') == $dia)>{{ $dia }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Aula</label>
        <input type="text" name="aula" value="{{ old('aula', $horario->aula ?? '') }}"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Hora Inicio</label>
        <input type="time" name="hora_inicio"
            value="{{ old('hora_inicio', $horario->hora_inicio?->format('H:i') ?? '') }}"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Hora Fin</label>
        <input type="time" name="hora_fin"
            value="{{ old('hora_fin', $horario->hora_fin?->format('H:i') ?? '') }}"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Modalidad</label>
        <select name="modalidad_clase"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            @foreach (['Presencial', 'Virtual', 'Híbrida', 'Semipresencial'] as $modalidad)
                <option value="{{ $modalidad }}" @selected(old('modalidad_clase', $horario->modalidad_clase ?? '') == $modalidad)>{{ $modalidad }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Materia</label>
        <select name="materia_id"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            @foreach ($materias as $m)
                <option value="{{ $m->id }}" @selected(old('materia_id', $horario->materia_id ?? '') == $m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Paralelo</label>
        <select name="paralelo_id"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            @foreach ($paralelos as $p)
                <option value="{{ $p->id }}" @selected(old('paralelo_id', $horario->paralelo_id ?? '') == $p->id)>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Periodo</label>
        <select name="periodo_id"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            @foreach ($periodos as $pe)
                <option value="{{ $pe->id }}" @selected(old('periodo_id', $horario->periodo_id ?? '') == $pe->id)>
                    {{ $pe->code }} — {{ $pe->description }}</option>
            @endforeach
        </select>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Asignación Docente</label>
        <select name="asignacion_docente_id"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-verdeclaro focus:ring-2 focus:ring-verdeclaro/40 transition">
            @foreach ($asignaciones as $a)
                <option value="{{ $a->id }}" @selected(old('asignacion_docente_id', $horario->asignacion_docente_id ?? '') == $a->id)>
                    {{ $a->periodo->code ?? '' }} - {{ $a->materia->name ?? 'Materia' }} -
                    {{ $a->paralelo->code ?? '' }} -
                    {{ $a->docente->name ?? '' }}
                </option>
            @endforeach
        </select>
    </div>
</div>
