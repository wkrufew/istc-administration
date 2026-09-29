<?php

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\MateriasArrastrada;
use App\Models\Periodo;
use App\Models\Semestre;
use App\Models\User;
use App\Services\ArrastreService;
use Illuminate\Database\QueryException;

// ─────────────────────────────────────────────────────────────────────────────
// Helper
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Creates the minimum scaffolding needed to insert a MateriasArrastrada row.
 */
function crearContextoUnique(): array
{
    $carrera = Carrera::create([
        'name'                => 'Ingeniería en Software',
        'code'                => 'IS-UNQ',
        'costo_credito'       => 200.00,
        'costo_carrera'       => 4800.00,
        'costo_convalidacion' => 50.00,
        'duracion_semestres'  => 4,
        'modalidad'           => 'Presencial',
        'tipo'                => 'Tecnologica',
        'is_active'           => true,
    ]);

    $semestre = Semestre::create([
        'name'             => 'Semestre 1',
        'code'             => 'S1-UNQ',
        'order'            => 1,
        'creditos_minimos' => 0,
        'creditos_maximos' => 40,
        'is_active'        => true,
        'carrera_id'       => $carrera->id,
    ]);

    $materia1 = Materia::create([
        'name'                   => 'Matemáticas',
        'code'                   => 'MAT-UNQ',
        'horas_teoricas'         => 48,
        'horas_practicas'        => 0,
        'nota_minima_aprobacion' => 7.00,
        'tipo'                   => 'Obligatoria',
        'semestre_id'            => $semestre->id,
        'is_active'              => true,
    ]);

    $materia2 = Materia::create([
        'name'                   => 'Física',
        'code'                   => 'FIS-UNQ',
        'horas_teoricas'         => 48,
        'horas_practicas'        => 0,
        'nota_minima_aprobacion' => 7.00,
        'tipo'                   => 'Obligatoria',
        'semestre_id'            => $semestre->id,
        'is_active'              => true,
    ]);

    $periodo = Periodo::create([
        'code'                   => 'P-UNQ-01',
        'fecha_inicio'           => '2024-01-01',
        'fecha_fin'              => '2024-06-30',
        'fecha_limite_matricula' => '2024-01-15',
        'fecha_limite_pago'      => '2024-01-20',
    ]);

    $usuario1 = User::factory()->create();
    $usuario2 = User::factory()->create();

    return compact('materia1', 'materia2', 'periodo', 'usuario1', 'usuario2');
}

/**
 * Minimal MateriasArrastrada insert (all required fields).
 */
function insertarArrastreDirecto(int $userId, int $materiaId, int $periodoId, int $intento = 1): MateriasArrastrada
{
    return MateriasArrastrada::create([
        'user_id'                 => $userId,
        'materia_id'              => $materiaId,
        'periodo_reprobado_id'    => $periodoId,
        'numero_intento'          => $intento,
        'estado'                  => 'Arrastrada',
        'nota_obtenida'           => 4.5,
        'nota_minima_requerida'   => 7.0,
        'costo_adicional'         => 0,
        'porcentaje_penalizacion' => 0,
    ]);
}

// ─────────────────────────────────────────────────────────────────────────────
// Tests: restricción UNIQUE en materias_arrastradas (user_id, materia_id)
// ─────────────────────────────────────────────────────────────────────────────

test('no se puede insertar dos arrastres para el mismo estudiante y misma materia', function () {
    ['materia1' => $m, 'periodo' => $p, 'usuario1' => $u] = crearContextoUnique();

    insertarArrastreDirecto($u->id, $m->id, $p->id, 1);

    expect(fn() => insertarArrastreDirecto($u->id, $m->id, $p->id, 1))
        ->toThrow(QueryException::class);
});

test('estudiantes distintos pueden tener arrastre en la misma materia', function () {
    ['materia1' => $m, 'periodo' => $p, 'usuario1' => $u1, 'usuario2' => $u2] = crearContextoUnique();

    $r1 = insertarArrastreDirecto($u1->id, $m->id, $p->id);
    $r2 = insertarArrastreDirecto($u2->id, $m->id, $p->id);

    expect($r1)->toBeInstanceOf(MateriasArrastrada::class);
    expect($r2)->toBeInstanceOf(MateriasArrastrada::class);
    expect(MateriasArrastrada::count())->toBe(2);
});

test('el mismo estudiante puede tener arrastres en materias distintas', function () {
    ['materia1' => $m1, 'materia2' => $m2, 'periodo' => $p, 'usuario1' => $u] = crearContextoUnique();

    $r1 = insertarArrastreDirecto($u->id, $m1->id, $p->id);
    $r2 = insertarArrastreDirecto($u->id, $m2->id, $p->id);

    expect($r1)->toBeInstanceOf(MateriasArrastrada::class);
    expect($r2)->toBeInstanceOf(MateriasArrastrada::class);
    expect(MateriasArrastrada::count())->toBe(2);
});

test('ArrastreService actualiza el registro existente en lugar de crear uno duplicado', function () {
    /*
     * Esta prueba verifica que el servicio nunca intenta insertar un segundo
     * registro para (user_id, materia_id). Si lo hiciera, la restricción UNIQUE
     * (una vez aplicada) lanzaría una excepción.
     */
    ['materia1' => $materia, 'periodo' => $periodo, 'usuario1' => $estudiante] = crearContextoUnique();

    $service = new ArrastreService();

    // Primer fallo: crea el registro
    $service->gestionar($estudiante->id, $materia->id, $periodo->id, 4.0, 7.0, 1);

    // Segunda llamada con el mismo intento: actualiza (no inserta)
    $service->gestionar($estudiante->id, $materia->id, $periodo->id, 3.5, 7.0, 1);

    // Debe haber exactamente UN registro, no dos
    expect(MateriasArrastrada::where('user_id', $estudiante->id)
        ->where('materia_id', $materia->id)
        ->count()
    )->toBe(1);
});

test('ArrastreService no viola la restricción al registrar un segundo intento fallido', function () {
    /*
     * Un estudiante falla en período 1 (intento 1), luego vuelve a fallar
     * en período 2 (intento 2). El servicio debe actualizar el registro,
     * no insertar uno nuevo que violaría UNIQUE(user_id, materia_id).
     */
    ['materia1' => $materia, 'periodo' => $p1, 'usuario1' => $estudiante] = crearContextoUnique();

    $p2 = Periodo::create([
        'code'                   => 'P-UNQ-02',
        'fecha_inicio'           => '2024-07-01',
        'fecha_fin'              => '2024-12-31',
        'fecha_limite_matricula' => '2024-07-15',
        'fecha_limite_pago'      => '2024-07-20',
    ]);

    $service = new ArrastreService();

    $service->gestionar($estudiante->id, $materia->id, $p1->id, 4.0, 7.0, 1); // intento 1
    $service->gestionar($estudiante->id, $materia->id, $p2->id, 3.5, 7.0, 2); // intento 2 — actualiza

    $registros = MateriasArrastrada::where('user_id', $estudiante->id)
        ->where('materia_id', $materia->id)
        ->get();

    expect($registros)->toHaveCount(1);
    expect($registros->first()->numero_intento)->toBe(2);
});
