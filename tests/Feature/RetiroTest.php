<?php

use App\Models\Carrera;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Periodo;
use App\Models\Retiro;
use App\Models\User;

// ─────────────────────────────────────────────────────────────────────────────
// Helper
// ─────────────────────────────────────────────────────────────────────────────

function crearContextoRetiro(): array
{
    $carrera = Carrera::create([
        'name'                => 'Administración de Empresas',
        'code'                => 'ADM-RET',
        'costo_credito'       => 150.00,
        'costo_carrera'       => 3600.00,
        'costo_convalidacion' => 50.00,
        'duracion_semestres'  => 4,
        'modalidad'           => 'Presencial',
        'tipo'                => 'Tecnologica',
        'is_active'           => true,
    ]);

    $periodo = Periodo::create([
        'code'                   => 'P-RET-01',
        'fecha_inicio'           => '2025-01-01',
        'fecha_fin'              => '2025-06-30',
        'fecha_limite_matricula' => '2025-01-15',
        'fecha_limite_pago'      => '2025-01-20',
    ]);

    $estudiante = User::factory()->create();

    $matricula = Matricula::create([
        'fecha_matricula' => '2025-01-10',
        'code'            => 'MAT-RET-001',
        'tipo'            => 'Nueva',
        'estado'          => 'Habilitada',
        'periodo_id'      => $periodo->id,
        'carrera_id'      => $carrera->id,
        'user_id'         => $estudiante->id,
    ]);

    return compact('carrera', 'periodo', 'estudiante', 'matricula');
}

function crearObligacion(int $userId, int $periodoId, int $matriculaId, string $estado): ObligacionesFinanciera
{
    return ObligacionesFinanciera::create([
        'user_id'          => $userId,
        'periodo_id'       => $periodoId,
        'matricula_id'     => $matriculaId,
        'tipo'             => 'COLEGIATURA',
        'monto_original'   => 720.00,
        'descuento'        => 0,
        'monto_final'      => 720.00,
        'estado'           => $estado,
        'fecha_vencimiento'=> '2025-02-01',
    ]);
}

// ─────────────────────────────────────────────────────────────────────────────
// Tests
// ─────────────────────────────────────────────────────────────────────────────

test('estado invalidado puede persistirse en una obligacion financiera', function () {
    ['estudiante' => $e, 'periodo' => $p, 'matricula' => $m] = crearContextoRetiro();

    $obligacion = crearObligacion($e->id, $p->id, $m->id, 'Pendiente');
    $obligacion->update(['estado' => 'Invalidado']);

    expect(ObligacionesFinanciera::find($obligacion->id)->estado)->toBe('Invalidado');
});

test('retiro invalida obligaciones pendientes parciales y vencidas de la matricula', function () {
    ['estudiante' => $e, 'periodo' => $p, 'matricula' => $m] = crearContextoRetiro();

    $pendiente = crearObligacion($e->id, $p->id, $m->id, 'Pendiente');
    $parcial   = crearObligacion($e->id, $p->id, $m->id, 'Parcial');
    $vencida   = crearObligacion($e->id, $p->id, $m->id, 'Vencido');

    ObligacionesFinanciera::where('matricula_id', $m->id)
        ->whereIn('estado', ['Pendiente', 'Parcial', 'Vencido'])
        ->update(['estado' => 'Invalidado']);

    expect(ObligacionesFinanciera::find($pendiente->id)->estado)->toBe('Invalidado');
    expect(ObligacionesFinanciera::find($parcial->id)->estado)->toBe('Invalidado');
    expect(ObligacionesFinanciera::find($vencida->id)->estado)->toBe('Invalidado');
});

test('retiro no invalida obligaciones que ya estaban pagadas', function () {
    ['estudiante' => $e, 'periodo' => $p, 'matricula' => $m] = crearContextoRetiro();

    $pagada    = crearObligacion($e->id, $p->id, $m->id, 'Pagado');
    $pendiente = crearObligacion($e->id, $p->id, $m->id, 'Pendiente');

    ObligacionesFinanciera::where('matricula_id', $m->id)
        ->whereIn('estado', ['Pendiente', 'Parcial', 'Vencido'])
        ->update(['estado' => 'Invalidado']);

    expect(ObligacionesFinanciera::find($pagada->id)->estado)->toBe('Pagado');
    expect(ObligacionesFinanciera::find($pendiente->id)->estado)->toBe('Invalidado');
});

test('retiro guarda el tipo de retiro correctamente', function () {
    ['estudiante' => $e, 'periodo' => $p, 'matricula' => $m] = crearContextoRetiro();

    $retiro = Retiro::create([
        'matricula_id'    => $m->id,
        'user_id'         => $e->id,
        'fecha_retiro'    => '2025-03-01',
        'motivo'          => 'Motivo de prueba',
        'tipo'            => 'Voluntario',
        'recargo_cobrado' => false,
    ]);

    expect(Retiro::find($retiro->id)->tipo)->toBe('Voluntario');

    $retiroAdmin = Retiro::create([
        'matricula_id'    => $m->id,
        'user_id'         => $e->id,
        'fecha_retiro'    => '2025-03-01',
        'motivo'          => null,
        'tipo'            => 'Administrativo',
        'recargo_cobrado' => false,
    ]);

    expect(Retiro::find($retiroAdmin->id)->tipo)->toBe('Administrativo');
});

test('retiro no invalida obligaciones de otras matriculas del mismo estudiante', function () {
    ['estudiante' => $e, 'periodo' => $p, 'carrera' => $c] = crearContextoRetiro();

    $matricula1 = Matricula::create([
        'fecha_matricula' => '2025-01-10',
        'code'            => 'MAT-RET-A',
        'tipo'            => 'Nueva',
        'estado'          => 'Habilitada',
        'periodo_id'      => $p->id,
        'carrera_id'      => $c->id,
        'user_id'         => $e->id,
    ]);

    $matricula2 = Matricula::create([
        'fecha_matricula' => '2025-01-10',
        'code'            => 'MAT-RET-B',
        'tipo'            => 'Renovacion',
        'estado'          => 'Habilitada',
        'periodo_id'      => $p->id,
        'carrera_id'      => $c->id,
        'user_id'         => $e->id,
    ]);

    $oblig1 = crearObligacion($e->id, $p->id, $matricula1->id, 'Pendiente');
    $oblig2 = crearObligacion($e->id, $p->id, $matricula2->id, 'Pendiente');

    // Solo se retira la matricula1
    ObligacionesFinanciera::where('matricula_id', $matricula1->id)
        ->whereIn('estado', ['Pendiente', 'Parcial', 'Vencido'])
        ->update(['estado' => 'Invalidado']);

    expect(ObligacionesFinanciera::find($oblig1->id)->estado)->toBe('Invalidado');
    expect(ObligacionesFinanciera::find($oblig2->id)->estado)->toBe('Pendiente');
});
