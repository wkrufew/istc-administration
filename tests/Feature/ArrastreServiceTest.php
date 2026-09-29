<?php

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\MateriasArrastrada;
use App\Models\Periodo;
use App\Models\Semestre;
use App\Models\Setting;
use App\Models\User;
use App\Services\ArrastreService;
use App\Services\SettingService;

// ─────────────────────────────────────────────────────────────────────────────
// Helpers de setup
// ─────────────────────────────────────────────────────────────────────────────

function crearContextoArrastre(): array
{
    SettingService::flush();

    Setting::create([
        'group' => 'matricula',
        'key'   => 'matricula.porcentaje_arrastre',
        'value' => '10',
        'label' => 'Porcentaje de penalización por arrastre (%)',
    ]);

    $carrera = Carrera::create([
        'name'               => 'Tecnología en Sistemas',
        'code'               => 'TS001',
        'costo_credito'      => 200.00,
        'costo_carrera'      => 2400.00,
        'costo_convalidacion'=> 50.00,
        'duracion_semestres' => 4,
        'modalidad'          => 'Presencial',
        'tipo'               => 'Tecnologica',
        'is_active'          => true,
    ]);

    $semestre = Semestre::create([
        'name'             => 'Primer Semestre',
        'code'             => 'S1',
        'order'            => 1,
        'creditos_minimos' => 0,
        'creditos_maximos' => 40,
        'is_active'        => true,
        'carrera_id'       => $carrera->id,
    ]);

    // 48 horas teóricas + 0 prácticas = 1 crédito
    // costo_normal = 1 * 200 = 200
    // costo_adicional intento 1 = 200 * 0.10 = 20.00
    // costo_adicional intento 2+ = 200 * 0.20 = 40.00
    $materia = Materia::create([
        'name'                   => 'Programación I',
        'code'                   => 'PROG101',
        'horas_teoricas'         => 48,
        'horas_practicas'        => 0,
        'nota_minima_aprobacion' => 7.00,
        'tipo'                   => 'Obligatoria',
        'semestre_id'            => $semestre->id,
        'is_active'              => true,
    ]);

    $periodo1 = Periodo::create([
        'code'                   => 'P-2024-01',
        'fecha_inicio'           => '2024-01-01',
        'fecha_fin'              => '2024-06-30',
        'fecha_limite_matricula' => '2024-01-15',
        'fecha_limite_pago'      => '2024-01-20',
    ]);
    $periodo2 = Periodo::create([
        'code'                   => 'P-2024-02',
        'fecha_inicio'           => '2024-07-01',
        'fecha_fin'              => '2024-12-31',
        'fecha_limite_matricula' => '2024-07-15',
        'fecha_limite_pago'      => '2024-07-20',
    ]);
    $user     = User::factory()->create();

    return compact('materia', 'periodo1', 'periodo2', 'user');
}

// ─────────────────────────────────────────────────────────────────────────────
// gestionar() — lógica de estados
// ─────────────────────────────────────────────────────────────────────────────

test('nota aprobatoria sin arrastre previo no crea ningún registro', function () {
    ['materia' => $materia, 'periodo1' => $periodo, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $periodo->id, 8.0, 7.0, 1);

    expect(MateriasArrastrada::count())->toBe(0);
});

test('nota menor a 4 en primer intento crea registro Arrastrada', function () {
    ['materia' => $materia, 'periodo1' => $periodo, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $periodo->id, 3.5, 7.0, 1);

    $arrastre = MateriasArrastrada::first();
    expect($arrastre)->not->toBeNull()
        ->and($arrastre->estado)->toBe('Arrastrada')
        ->and($arrastre->numero_intento)->toBe(1)
        ->and((float) $arrastre->nota_obtenida)->toBe(3.5)
        ->and($arrastre->periodo_reprobado_id)->toBe($periodo->id);
});

test('nota en zona de suspenso fallada crea registro Arrastrada', function () {
    ['materia' => $materia, 'periodo1' => $periodo, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $periodo->id, 5.0, 7.0, 1);

    $arrastre = MateriasArrastrada::first();
    expect($arrastre->estado)->toBe('Arrastrada')
        ->and($arrastre->numero_intento)->toBe(1);
});

test('nota aprobatoria sobre arrastre activo actualiza estado a Aprobada', function () {
    ['materia' => $materia, 'periodo1' => $p1, 'periodo2' => $p2, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $p1->id, 5.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $p2->id, 8.5, 7.0, 2);

    $arrastre = MateriasArrastrada::first();
    expect(MateriasArrastrada::count())->toBe(1)
        ->and($arrastre->estado)->toBe('Aprobada')
        ->and((float) $arrastre->nota_obtenida)->toBe(8.5);
});

test('segundo fallo actualiza el registro existente a intento 2 sin crear duplicado', function () {
    ['materia' => $materia, 'periodo1' => $p1, 'periodo2' => $p2, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $p1->id, 5.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $p2->id, 4.5, 7.0, 2);

    expect(MateriasArrastrada::count())->toBe(1);
    $arrastre = MateriasArrastrada::first();
    expect($arrastre->estado)->toBe('Arrastrada')
        ->and($arrastre->numero_intento)->toBe(2)
        ->and($arrastre->periodo_reprobado_id)->toBe($p2->id);
});

test('tercer fallo actualiza estado a Perdida_Definitiva', function () {
    ['materia' => $materia, 'periodo1' => $p1, 'periodo2' => $p2, 'user' => $user] = crearContextoArrastre();
    $periodo3 = Periodo::create(['code' => 'P-2024-03', 'fecha_inicio' => '2025-01-01', 'fecha_fin' => '2025-06-30', 'fecha_limite_matricula' => '2025-01-15', 'fecha_limite_pago' => '2025-01-20']);
    $service  = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $p1->id, 3.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $p2->id, 4.0, 7.0, 2);
    $service->gestionar($user->id, $materia->id, $periodo3->id, 5.0, 7.0, 3);

    expect(MateriasArrastrada::count())->toBe(1);
    $arrastre = MateriasArrastrada::first();
    expect($arrastre->estado)->toBe('Perdida_Definitiva')
        ->and($arrastre->numero_intento)->toBe(3);
});

test('nota aprobatoria en tercer intento sigue siendo Aprobada', function () {
    ['materia' => $materia, 'periodo1' => $p1, 'periodo2' => $p2, 'user' => $user] = crearContextoArrastre();
    $periodo3 = Periodo::create(['code' => 'P-2024-03', 'fecha_inicio' => '2025-01-01', 'fecha_fin' => '2025-06-30', 'fecha_limite_matricula' => '2025-01-15', 'fecha_limite_pago' => '2025-01-20']);
    $service  = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $p1->id, 3.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $p2->id, 4.0, 7.0, 2);
    $service->gestionar($user->id, $materia->id, $periodo3->id, 9.0, 7.0, 3);

    $arrastre = MateriasArrastrada::first();
    expect($arrastre->estado)->toBe('Aprobada');
});

test('corrección de nota: de reprobado a aprobado en el mismo período actualiza a Aprobada', function () {
    ['materia' => $materia, 'periodo1' => $periodo, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $periodo->id, 5.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $periodo->id, 8.0, 7.0, 1);

    expect(MateriasArrastrada::count())->toBe(1);
    $arrastre = MateriasArrastrada::first();
    expect($arrastre->estado)->toBe('Aprobada');
});

test('corrección inversa: de aprobado a reprobado vuelve a Arrastrada', function () {
    ['materia' => $materia, 'periodo1' => $periodo, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $periodo->id, 5.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $periodo->id, 8.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $periodo->id, 4.5, 7.0, 1);

    expect(MateriasArrastrada::count())->toBe(1);
    $arrastre = MateriasArrastrada::first();
    expect($arrastre->estado)->toBe('Arrastrada')
        ->and((float) $arrastre->nota_obtenida)->toBe(4.5);
});

test('arrastre de período anterior queda Aprobado cuando se aprueba en período nuevo', function () {
    ['materia' => $materia, 'periodo1' => $p1, 'periodo2' => $p2, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    // Falla en p1 y el registro queda con periodo_reprobado_id=p1
    $service->gestionar($user->id, $materia->id, $p1->id, 4.0, 7.0, 1);
    $arrastre = MateriasArrastrada::first();
    expect($arrastre->periodo_reprobado_id)->toBe($p1->id);

    // Aprueba en p2 — debe actualizar aunque el periodo sea distinto
    $service->gestionar($user->id, $materia->id, $p2->id, 7.5, 7.0, 2);

    expect(MateriasArrastrada::count())->toBe(1);
    expect(MateriasArrastrada::first()->estado)->toBe('Aprobada');
});

// ─────────────────────────────────────────────────────────────────────────────
// calcularNumeroIntento()
// ─────────────────────────────────────────────────────────────────────────────

test('calcularNumeroIntento retorna 1 cuando no hay registro previo', function () {
    ['materia' => $materia, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    expect($service->calcularNumeroIntento($user->id, $materia->id))->toBe(1);
});

test('calcularNumeroIntento retorna 2 cuando el registro activo es intento 1', function () {
    ['materia' => $materia, 'periodo1' => $periodo, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $periodo->id, 5.0, 7.0, 1);

    expect($service->calcularNumeroIntento($user->id, $materia->id))->toBe(2);
});

test('calcularNumeroIntento retorna 3 como máximo independientemente del historial', function () {
    ['materia' => $materia, 'periodo1' => $p1, 'periodo2' => $p2, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $p1->id, 5.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $p2->id, 4.0, 7.0, 2);

    expect($service->calcularNumeroIntento($user->id, $materia->id))->toBe(3);
});

test('calcularNumeroIntento retorna 1 si el único registro es Aprobada', function () {
    ['materia' => $materia, 'periodo1' => $p1, 'periodo2' => $p2, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $p1->id, 5.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $p2->id, 8.0, 7.0, 2);

    expect($service->calcularNumeroIntento($user->id, $materia->id))->toBe(1);
});

// ─────────────────────────────────────────────────────────────────────────────
// calcularCostoAdicional()
// ─────────────────────────────────────────────────────────────────────────────

test('costo adicional intento 1 es 10% del costo del crédito', function () {
    // 48h teóricas / 48 = 1 crédito; costo_credito=200; 10% = 20.00
    ['materia' => $materia] = crearContextoArrastre();
    $service = new ArrastreService();

    expect($service->calcularCostoAdicional($materia->id, 1))->toBe(20.00);
});

test('costo adicional intento 2 dobla el porcentaje (20%)', function () {
    // 48h / 48 = 1 crédito; costo_credito=200; 20% = 40.00
    ['materia' => $materia] = crearContextoArrastre();
    $service = new ArrastreService();

    expect($service->calcularCostoAdicional($materia->id, 2))->toBe(40.00);
});

test('costo adicional es 0 cuando la materia no existe', function () {
    crearContextoArrastre();
    $service = new ArrastreService();

    expect($service->calcularCostoAdicional(99999, 1))->toBe(0.0);
});

// ─────────────────────────────────────────────────────────────────────────────
// porcentaje_penalizacion en el registro
// ─────────────────────────────────────────────────────────────────────────────

test('el registro guarda el porcentaje simple en intento 1', function () {
    ['materia' => $materia, 'periodo1' => $periodo, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $periodo->id, 5.0, 7.0, 1);

    expect((float) MateriasArrastrada::first()->porcentaje_penalizacion)->toBe(10.0);
});

test('el registro guarda el porcentaje doble en intento 2', function () {
    ['materia' => $materia, 'periodo1' => $p1, 'periodo2' => $p2, 'user' => $user] = crearContextoArrastre();
    $service = new ArrastreService();

    $service->gestionar($user->id, $materia->id, $p1->id, 5.0, 7.0, 1);
    $service->gestionar($user->id, $materia->id, $p2->id, 4.0, 7.0, 2);

    expect((float) MateriasArrastrada::first()->porcentaje_penalizacion)->toBe(20.0);
});
