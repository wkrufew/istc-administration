<div class="max-w-3xl mx-auto py-10 sm:px-6 lg:px-8 border border-gray-200 rounded-lg mb-4">
    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>¡Ups! Algo salió mal.</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario para crear o editar un paralelo --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Día</label>
            <select name="dia_semana" class="w-full border rounded-full px-2 py-1">
                @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                    <option value="{{ $dia }}" @selected(old('dia_semana', $horario->dia_semana ?? '') == $dia)>{{ $dia }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Aula</label>
            <input type="text" name="aula" value="{{ old('aula', $horario->aula ?? '') }}"
                class="w-full border rounded-full px-2 py-1">
        </div>

        <div>
            <label class="block text-sm font-medium">Hora Inicio</label>
            <input type="time" name="hora_inicio"
                value="{{ old('hora_inicio', $horario->hora_inicio?->format('H:i') ?? '') }}"
                class="w-full border rounded-full px-2 py-1">
        </div>

        <div>
            <label class="block text-sm font-medium">Hora Fin</label>
            <input type="time" name="hora_fin"
                value="{{ old('hora_fin', $horario->hora_fin?->format('H:i') ?? '') }}"
                class="w-full border rounded-full px-2 py-1">
        </div>

        <div>
            <label class="block text-sm font-medium">Modalidad</label>
            <select name="modalidad_clase" class="w-full border rounded-full px-2 py-1">
                @foreach (['Presencial', 'Virtual', 'Híbrida', 'Semipresencial'] as $modalidad)
                    <option value="{{ $modalidad }}" @selected(old('modalidad_clase', $horario->modalidad_clase ?? '') == $modalidad)>{{ $modalidad }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Materia</label>
            <select name="materia_id" class="w-full border rounded-full px-2 py-1">
                @foreach ($materias as $m)
                    <option value="{{ $m->id }}" @selected(old('materia_id', $horario->materia_id ?? '') == $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Paralelo</label>
            <select name="paralelo_id" class="w-full border rounded-full px-2 py-1">
                @foreach ($paralelos as $p)
                    <option value="{{ $p->id }}" @selected(old('paralelo_id', $horario->paralelo_id ?? '') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Periodo</label>
            <select name="periodo_id" class="w-full border rounded-full px-2 py-1">
                @foreach ($periodos as $pe)
                    <option value="{{ $pe->id }}" @selected(old('periodo_id', $horario->periodo_id ?? '') == $pe->id)>
                        {{ $pe->code }} — {{ $pe->description }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium">Asignación Docente</label>
            <select name="asignacion_docente_id" class="w-full border rounded-full px-2 py-1">
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
</div>
