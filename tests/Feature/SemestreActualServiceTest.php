<?php

use App\Models\Calificacion;
use App\Models\Carrera;
use App\Models\DetalleMatricula;
use App\Models\Materia;
use App\Models\Matricula;
use App\Models\MateriasArrastrada;
use App\Models\Paralelo;
use App\Models\Periodo;
use App\Models\Semestre;
use App\Models\User;
use App\Services\SemestreActualService;

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Crea una carrera con 3 semestres (3+3+2 materias) y los actores necesarios.
 * Devuelve: carrera, s1, s2, s3, ms1, ms2, ms3, periodo, paralelo, estudiante, docente.
 */
function crearContextoSemestre(): array
{
    $carrera = Carrera::create([
        'name'                => 'Tecnología en Sistemas',
        'code'                => 'TS-SEM',
        'costo_credito'       => 200.00,
        'costo_carrera'       => 2400.00,
        'costo_convalidacion' => 50.00,
        'duracion_semestres'  => 3,
        'modalidad'           => 'Presencial',
        'tipo'                => 'Tecnologica',
        'is_active'           => true,
    ]);

    $crearSem = fn(int $n) => Semestre::create([
        'name'             => "Semestre {$n}",
        'code'             => "SEM{$n}",
        'order'            => $n,
        'creditos_minimos' => 0,
        'creditos_maximos' => 40,
        'is_active'        => true,
        'carrera_id'       => $carrera->id,
    ]);

    $s1 = $crearSem(1);
    $s2 = $crearSem(2);
    $s3 = $crearSem(3);

    $crearMaterias = fn(Semestre $s, int $n) => collect(range(1, $n))->map(fn($i) =>
        Materia::create([
            'name'                   => "{$s->code}-M{$i}",
            'code'                   => "{$s->code}M{$i}",
            'horas_teoricas'         => 48,
            'horas_practicas'        => 0,
            'nota_minima_aprobacion' => 7.00,
            'tipo'                   => 'Obligatoria',
            'semestre_id'            => $s->id,
            'is_active'              => true,
        ])
    );

    $ms1 = $crearMaterias($s1, 3); // S1: 3 materias
    $ms2 = $crearMaterias($s2, 3); // S2: 3 materias
    $ms3 = $crearMaterias($s3, 2); // S3: 2 materias

    $periodo    = Periodo::create([
        'code'                   => 'P-SEM-01',
        'fecha_inicio'           => '2024-01-01',
        'fecha_fin'              => '2024-06-30',
        'fecha_limite_matricula' => '2024-01-15',
        'fecha_limite_pago'      => '2024-01-20',
    ]);
    $paralelo   = Paralelo::create(['name' => 'A', 'code' => 'PA', 'cupo_maximo' => 30, 'cupo_actual' => 0, 'is_active' => true]);
    $estudiante = User::factory()->create();
    $docente    = User::factory()->create();

    return compact('carrera', 's1', 's2', 's3', 'ms1', 'ms2', 'ms3', 'periodo', 'paralelo', 'estudiante', 'docente');
}

/**
 * Registra una calificación aprobatoria para una materia del historial del estudiante.
 * Reutiliza la matrícula del período si ya existe.
 */
function aprobarMateria(User $estudiante, User $docente, Materia $materia, Periodo $periodo, Paralelo $paralelo): void
{
    $carreraId = $materia->semestre->carrera_id;

    $matricula = Matricula::firstOrCreate(
        ['user_id' => $estudiante->id, 'periodo_id' => $periodo->id, 'carrera_id' => $carreraId],
        [
            'code'            => 'M-' . $estudiante->id . '-' . $periodo->id . '-' . $carreraId,
            'fecha_matricula' => $periodo->fecha_inicio,
            'tipo'            => 'Nueva',
            'estado'          => 'Habilitada',
        ]
    );

    $detalle = DetalleMatricula::create([
        'code'        => 'D-' . $matricula->id . '-' . $materia->id,
        'matricula_id' => $matricula->id,
        'materia_id'  => $materia->id,
        'paralelo_id' => $paralelo->id,
        'user_id'     => $estudiante->id,
        'tipo'        => 'Normal',
        'estado'      => 'Finalizado',
    ]);

    Calificacion::create([
        'detalle_matricula_id' => $detalle->id,
        'docente_id'           => $docente->id,
        'nota_final'           => 8.0,
        'estado_final'         => 'Aprobado',
    ]);
}

/**
 * Crea un registro de arrastre activo (estado='Arrastrada') para una materia.
 */
function ponerEnArrastre(User $estudiante, Materia $materia, Periodo $periodo, int $intento = 1): void
{
    MateriasArrastrada::create([
        'user_id'                 => $estudiante->id,
        'materia_id'              => $materia->id,
        'periodo_reprobado_id'    => $periodo->id,
        'numero_intento'          => $intento,
        'estado'                  => 'Arrastrada',
        'nota_obtenida'           => 4.5,
        'nota_minima_requerida'   => 7.0,
        'costo_adicional'         => 0,
        'porcentaje_penalizacion' => 0,
    ]);
}

// ─────────────────────────────────────────────────────────────────────────────
// Tests: determinar semestre actual para el wizard
// ─────────────────────────────────────────────────────────────────────────────

test('estudiante sin historial comienza en semestre 1', function () {
    [
        'carrera'    => $carrera,
        's1'         => $s1,
        'estudiante' => $est,
    ] = crearContextoSemestre();

    $result = (new SemestreActualService())->obtenerSemestreActual($est->id, $carrera->id);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($s1->id);
});

test('semestre 1 completamente aprobado avanza a semestre 2', function () {
    [
        'carrera'    => $carrera,
        's2'         => $s2,
        'ms1'        => $ms1,
        'periodo'    => $periodo,
        'paralelo'   => $paralelo,
        'estudiante' => $est,
        'docente'    => $doc,
    ] = crearContextoSemestre();

    foreach ($ms1 as $m) {
        aprobarMateria($est, $doc, $m, $periodo, $paralelo);
    }

    $result = (new SemestreActualService())->obtenerSemestreActual($est->id, $carrera->id);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($s2->id);
});

test('semestre 1 parcialmente aprobado se queda en semestre 1', function () {
    [
        'carrera'    => $carrera,
        's1'         => $s1,
        'ms1'        => $ms1,
        'periodo'    => $periodo,
        'paralelo'   => $paralelo,
        'estudiante' => $est,
        'docente'    => $doc,
    ] = crearContextoSemestre();

    // Solo aprueba 2 de 3 materias de S1; la tercera no tiene registro
    aprobarMateria($est, $doc, $ms1[0], $periodo, $paralelo);
    aprobarMateria($est, $doc, $ms1[1], $periodo, $paralelo);

    $result = (new SemestreActualService())->obtenerSemestreActual($est->id, $carrera->id);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($s1->id);
});

test('materia en arrastre activo cuenta como cubierta y libera el semestre', function () {
    /*
     * Regla de negocio clave:
     * S1 tiene 3 materias. El estudiante aprobó 2 y tiene la tercera en arrastre
     * (estado='Arrastrada'). S1 se considera "completo" para efectos de avance:
     * el estudiante ya cursó todas sus materias, aunque una esté pendiente de
     * aprobación. El wizard debe mostrar S2.
     */
    [
        'carrera'    => $carrera,
        's2'         => $s2,
        'ms1'        => $ms1,
        'periodo'    => $periodo,
        'paralelo'   => $paralelo,
        'estudiante' => $est,
        'docente'    => $doc,
    ] = crearContextoSemestre();

    aprobarMateria($est, $doc, $ms1[0], $periodo, $paralelo);
    aprobarMateria($est, $doc, $ms1[1], $periodo, $paralelo);
    ponerEnArrastre($est, $ms1[2], $periodo); // tercera materia en arrastre

    $result = (new SemestreActualService())->obtenerSemestreActual($est->id, $carrera->id);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($s2->id);
});

test('semestres 1 y 2 completos muestra semestre 3', function () {
    [
        'carrera'    => $carrera,
        's3'         => $s3,
        'ms1'        => $ms1,
        'ms2'        => $ms2,
        'periodo'    => $periodo,
        'paralelo'   => $paralelo,
        'estudiante' => $est,
        'docente'    => $doc,
    ] = crearContextoSemestre();

    foreach ([...$ms1, ...$ms2] as $m) {
        aprobarMateria($est, $doc, $m, $periodo, $paralelo);
    }

    $result = (new SemestreActualService())->obtenerSemestreActual($est->id, $carrera->id);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($s3->id);
});

test('todos los semestres completos retorna null', function () {
    [
        'carrera'    => $carrera,
        'ms1'        => $ms1,
        'ms2'        => $ms2,
        'ms3'        => $ms3,
        'periodo'    => $periodo,
        'paralelo'   => $paralelo,
        'estudiante' => $est,
        'docente'    => $doc,
    ] = crearContextoSemestre();

    foreach ([...$ms1, ...$ms2, ...$ms3] as $m) {
        aprobarMateria($est, $doc, $m, $periodo, $paralelo);
    }

    $result = (new SemestreActualService())->obtenerSemestreActual($est->id, $carrera->id);

    expect($result)->toBeNull();
});

test('arrastre aprobado en reintento cuenta como materia aprobada', function () {
    /*
     * Flujo: S1-M3 fue reprobada (arrastre), el estudiante la cursó de nuevo y
     * aprobó. El registro de arrastre queda en estado='Aprobada' y existe una
     * calificación con estado_final='Aprobado'. S1 debe quedar completo.
     */
    [
        'carrera'    => $carrera,
        's2'         => $s2,
        'ms1'        => $ms1,
        'periodo'    => $periodo,
        'paralelo'   => $paralelo,
        'estudiante' => $est,
        'docente'    => $doc,
    ] = crearContextoSemestre();

    $periodo2 = Periodo::create([
        'code'                   => 'P-SEM-02',
        'fecha_inicio'           => '2024-07-01',
        'fecha_fin'              => '2024-12-31',
        'fecha_limite_matricula' => '2024-07-15',
        'fecha_limite_pago'      => '2024-07-20',
    ]);

    // Aprueba M1 y M2 en P1
    aprobarMateria($est, $doc, $ms1[0], $periodo, $paralelo);
    aprobarMateria($est, $doc, $ms1[1], $periodo, $paralelo);

    // M3: falló en P1 → arrastre estado='Aprobada' (ya fue procesado)
    MateriasArrastrada::create([
        'user_id'                 => $est->id,
        'materia_id'              => $ms1[2]->id,
        'periodo_reprobado_id'    => $periodo->id,
        'numero_intento'          => 1,
        'estado'                  => 'Aprobada',      // pasó en el reintento
        'nota_obtenida'           => 8.0,
        'nota_minima_requerida'   => 7.0,
        'costo_adicional'         => 0,
        'porcentaje_penalizacion' => 0,
    ]);
    // Y la calificación aprobatoria del reintento en P2
    aprobarMateria($est, $doc, $ms1[2], $periodo2, $paralelo);

    $result = (new SemestreActualService())->obtenerSemestreActual($est->id, $carrera->id);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($s2->id);
});

test('materia con Perdida_Definitiva no es contabilizada y mantiene el semestre bloqueado', function () {
    /*
     * Si una materia queda en Perdida_Definitiva el semestre no puede completarse
     * por esa vía. El wizard seguirá mostrando S1 indefinidamente, lo que refleja
     * que el estudiante perdió la posibilidad de avanzar en esa materia.
     * Este test documenta el comportamiento actual esperado.
     */
    [
        'carrera'    => $carrera,
        's1'         => $s1,
        'ms1'        => $ms1,
        'periodo'    => $periodo,
        'paralelo'   => $paralelo,
        'estudiante' => $est,
        'docente'    => $doc,
    ] = crearContextoSemestre();

    aprobarMateria($est, $doc, $ms1[0], $periodo, $paralelo);
    aprobarMateria($est, $doc, $ms1[1], $periodo, $paralelo);

    // M3: 3 intentos fallidos → Perdida_Definitiva
    MateriasArrastrada::create([
        'user_id'                 => $est->id,
        'materia_id'              => $ms1[2]->id,
        'periodo_reprobado_id'    => $periodo->id,
        'numero_intento'          => 3,
        'estado'                  => 'Perdida_Definitiva',
        'nota_obtenida'           => 5.0,
        'nota_minima_requerida'   => 7.0,
        'costo_adicional'         => 0,
        'porcentaje_penalizacion' => 0,
    ]);

    $result = (new SemestreActualService())->obtenerSemestreActual($est->id, $carrera->id);

    // S1 sigue siendo el "actual" porque M3 no está ni aprobada ni en arrastre activo
    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($s1->id);
});
