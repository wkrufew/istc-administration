<?php

/*
 * Eliminar la asignación de una materia a un docente: no se borra el historial ni lo ya
 * registrado (asistencias, notas), porque la base de datos borra en cadena sus horarios,
 * asistencias, clases no dictadas y avisos.
 */

use App\Livewire\Administration\AsignacionDocente as PantallaAsignacion;
use App\Models\AsignacionDocente;
use App\Models\AuditoriaGeneral;
use App\Models\User;
use App\Services\AsignacionDocenteService;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Período general compartido por dos carreras: vigente para la carrera A y ya terminado para B
 * (el caso en que antes se marcaba "Activo" a todo por mirar el período general).
 */
function escenarioAsignaciones(): array
{
    $carreraA = DB::table('carreras')->insertGetId(['name' => 'Carrera A', 'code' => 'CA', 'created_at' => now(), 'updated_at' => now()]);
    $carreraB = DB::table('carreras')->insertGetId(['name' => 'Carrera B', 'code' => 'CB', 'created_at' => now(), 'updated_at' => now()]);
    $periodo  = DB::table('periodos')->insertGetId([
        'code' => 'P-TEST', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-12-31',
        'fecha_limite_matricula' => '2026-01-15', 'fecha_limite_pago' => '2026-01-20', 'created_at' => now(), 'updated_at' => now(),
    ]);
    DB::table('carrera_periodo')->insert([
        ['carrera_id' => $carreraA, 'periodo_id' => $periodo, 'is_current' => true,  'is_active' => true],
        ['carrera_id' => $carreraB, 'periodo_id' => $periodo, 'is_current' => false, 'is_active' => true],
    ]);
    $semA = DB::table('semestres')->insertGetId(['name' => 'S1 A', 'code' => 'S1A', 'carrera_id' => $carreraA, 'created_at' => now(), 'updated_at' => now()]);
    $semB = DB::table('semestres')->insertGetId(['name' => 'S1 B', 'code' => 'S1B', 'carrera_id' => $carreraB, 'created_at' => now(), 'updated_at' => now()]);
    $materiaA = DB::table('materias')->insertGetId(['name' => 'Materia A', 'code' => 'MA', 'semestre_id' => $semA, 'created_at' => now(), 'updated_at' => now()]);
    $materiaB = DB::table('materias')->insertGetId(['name' => 'Materia B', 'code' => 'MB', 'semestre_id' => $semB, 'created_at' => now(), 'updated_at' => now()]);
    $paralelo = DB::table('paralelos')->insertGetId(['name' => 'A', 'code' => 'PA', 'created_at' => now(), 'updated_at' => now()]);

    $docente = User::factory()->create();

    $asignar = fn(int $materia) => AsignacionDocente::create([
        'docente_id' => $docente->id, 'materia_id' => $materia, 'paralelo_id' => $paralelo, 'periodo_id' => $periodo,
    ]);

    return compact('carreraA', 'periodo', 'materiaA', 'materiaB', 'paralelo', 'docente', 'asignar');
}

function horarioDe(AsignacionDocente $a): int
{
    return DB::table('horarios')->insertGetId([
        'dia_semana' => 'Lunes', 'hora_inicio' => '08:00', 'hora_fin' => '10:00', 'materia_id' => $a->materia_id,
        'paralelo_id' => $a->paralelo_id, 'periodo_id' => $a->periodo_id, 'asignacion_docente_id' => $a->id,
        'created_at' => now(), 'updated_at' => now(),
    ]);
}

function inscritoEn(AsignacionDocente $a, int $carreraId): int
{
    $est = User::factory()->create();
    $matricula = DB::table('matriculas')->insertGetId([
        'code' => 'M-' . uniqid(), 'fecha_matricula' => now(), 'tipo' => 'Nueva', 'estado' => 'Habilitada',
        'periodo_id' => $a->periodo_id, 'carrera_id' => $carreraId, 'user_id' => $est->id, 'created_at' => now(), 'updated_at' => now(),
    ]);
    return DB::table('detalle_matriculas')->insertGetId([
        'code' => 'D-' . uniqid(), 'matricula_id' => $matricula, 'materia_id' => $a->materia_id, 'paralelo_id' => $a->paralelo_id,
        'user_id' => $est->id, 'estado' => 'Inscrito', 'created_at' => now(), 'updated_at' => now(),
    ]);
}

beforeEach(function () {
    $this->e = escenarioAsignaciones();
    $this->actingAs(User::factory()->create());
});

test('vigente para su carrera y sin registros: se elimina con sus horarios y queda auditada', function () {
    $a = ($this->e['asignar'])($this->e['materiaA']);
    horarioDe($a);

    $impacto = AsignacionDocenteService::impacto($a);
    expect(AsignacionDocenteService::esVigente($a))->toBeTrue()
        ->and(AsignacionDocenteService::motivoNoEliminar($a))->toBeNull()
        ->and(AsignacionDocenteService::avisoEliminar($impacto))->toContain('1 horario');

    AsignacionDocenteService::eliminar($a);

    expect(AsignacionDocente::find($a->id))->toBeNull()
        ->and(DB::table('horarios')->where('asignacion_docente_id', $a->id)->count())->toBe(0);
    $registro = AuditoriaGeneral::where('auditable_type', AsignacionDocente::class)->where('auditable_id', $a->id)->first();
    expect($registro->evento)->toBe('deleted')->and($registro->valores_antes['materia'])->toBe('Materia A');
});

test('período terminado para su carrera: no se elimina aunque otra carrera lo tenga vigente', function () {
    $a = ($this->e['asignar'])($this->e['materiaB']);

    expect(AsignacionDocenteService::esVigente($a))->toBeFalse()
        ->and(AsignacionDocenteService::motivoNoEliminar($a))->toContain('período que ya terminó');
    expect(fn() => AsignacionDocenteService::eliminar($a))->toThrow(\DomainException::class);
    expect(AsignacionDocente::find($a->id))->not->toBeNull();
});

test('con asistencias registradas: no se elimina y no se pierde ninguna', function () {
    $a = ($this->e['asignar'])($this->e['materiaA']);
    $horario = horarioDe($a);
    DB::table('asistencias')->insert([
        'fecha' => now()->toDateString(), 'estado' => 'Presente', 'detalle_matricula_id' => inscritoEn($a, $this->e['carreraA']),
        'horario_id' => $horario, 'docente_id' => $this->e['docente']->id, 'created_at' => now(), 'updated_at' => now(),
    ]);

    expect(AsignacionDocenteService::motivoNoEliminar($a))->toContain('1 asistencia');
    expect(fn() => AsignacionDocenteService::eliminar($a))->toThrow(\DomainException::class);
    expect(DB::table('asistencias')->count())->toBe(1)->and(AsignacionDocente::find($a->id))->not->toBeNull();
});

test('con notas registradas: no se elimina', function () {
    $a = ($this->e['asignar'])($this->e['materiaA']);
    DB::table('calificacions')->insert([
        'detalle_matricula_id' => inscritoEn($a, $this->e['carreraA']), 'docente_id' => $this->e['docente']->id,
        'nota_final' => 8, 'created_at' => now(), 'updated_at' => now(),
    ]);

    expect(AsignacionDocenteService::motivoNoEliminar($a))->toContain('1 nota');
    expect(fn() => AsignacionDocenteService::eliminar($a))->toThrow(\DomainException::class);
});

test('la pantalla marca Histórico con candado y el servidor no la deja eliminar', function () {
    Permission::firstOrCreate(['name' => 'gestionar_docentes', 'guard_name' => 'web']);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $vigente   = ($this->e['asignar'])($this->e['materiaA']);
    $terminada = ($this->e['asignar'])($this->e['materiaB']);

    $w = Livewire::test(PantallaAsignacion::class, ['docente' => $this->e['docente']])->set('filtro', '');
    $filas = $w->viewData('asignaciones')->keyBy('id');
    expect($filas[$vigente->id]->es_vigente)->toBeTrue()
        ->and($filas[$vigente->id]->motivo_bloqueo)->toBeNull()
        ->and($filas[$terminada->id]->es_vigente)->toBeFalse()
        ->and($filas[$terminada->id]->motivo_bloqueo)->not->toBeNull();

    $w->call('eliminar', $terminada->id);
    expect(AsignacionDocente::find($terminada->id))->not->toBeNull();

    $w->call('eliminar', $vigente->id);
    expect(AsignacionDocente::find($vigente->id))->toBeNull();
});

function docenteActivo(): User
{
    Role::firstOrCreate(['name' => 'Docente', 'guard_name' => 'web'])
        ->syncPermissions([Permission::firstOrCreate(['name' => 'acceso_docencia', 'guard_name' => 'web'])]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $u = User::factory()->create(['is_active' => true]);
    $u->assignRole('Docente');
    return $u;
}

test('cambiar docente: conserva horarios y asistencias, pasa las notas y queda auditado', function () {
    $a = ($this->e['asignar'])($this->e['materiaA']);
    $a->update(['file_contrato' => 'contratos/anterior.pdf']);
    $horario = horarioDe($a);
    $detalle = inscritoEn($a, $this->e['carreraA']);
    DB::table('asistencias')->insert([
        'fecha' => now()->toDateString(), 'estado' => 'Presente', 'detalle_matricula_id' => $detalle,
        'horario_id' => $horario, 'docente_id' => $this->e['docente']->id, 'created_at' => now(), 'updated_at' => now(),
    ]);
    DB::table('calificacions')->insert([
        'detalle_matricula_id' => $detalle, 'docente_id' => $this->e['docente']->id, 'nota_final' => 8,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $nuevo = docenteActivo();

    $notas = AsignacionDocenteService::cambiarDocente($a, $nuevo->id, 'Renuncia del docente');

    $a->refresh();
    expect($notas)->toBe(1)
        ->and($a->docente_id)->toBe($nuevo->id)
        ->and($a->file_contrato)->toBeNull()
        ->and(DB::table('calificacions')->where('detalle_matricula_id', $detalle)->value('docente_id'))->toBe($nuevo->id)
        ->and(DB::table('horarios')->where('asignacion_docente_id', $a->id)->count())->toBe(1)
        // la asistencia sigue registrada con quien la tomó
        ->and(DB::table('asistencias')->value('docente_id'))->toBe($this->e['docente']->id);

    $registro = AuditoriaGeneral::where('auditable_type', AsignacionDocente::class)->where('auditable_id', $a->id)->latest('id')->first();
    expect($registro->valores_antes['docente'])->toBe($this->e['docente']->name)
        ->and($registro->valores_antes['contrato'])->toBe('contratos/anterior.pdf')
        ->and($registro->valores_despues['notas_transferidas'])->toBe(1)
        ->and($registro->valores_despues['motivo'])->toBe('Renuncia del docente');
});

test('cambiar docente: no en períodos terminados, ni al mismo, ni a quien no es docente activo', function () {
    $vigente   = ($this->e['asignar'])($this->e['materiaA']);
    $terminada = ($this->e['asignar'])($this->e['materiaB']);
    $nuevo     = docenteActivo();
    $inactivo  = docenteActivo();
    $inactivo->update(['is_active' => false]);
    $noDocente = User::factory()->create();

    expect(fn() => AsignacionDocenteService::cambiarDocente($terminada, $nuevo->id, 'Motivo'))->toThrow(\DomainException::class, 'terminó');
    expect(fn() => AsignacionDocenteService::cambiarDocente($vigente, $this->e['docente']->id, 'Motivo'))->toThrow(\DomainException::class, 'distinto');
    expect(fn() => AsignacionDocenteService::cambiarDocente($vigente, $inactivo->id, 'Motivo'))->toThrow(\DomainException::class, 'no está activo');
    expect(fn() => AsignacionDocenteService::cambiarDocente($vigente, $noDocente->id, 'Motivo'))->toThrow(\DomainException::class);
    expect($vigente->fresh()->docente_id)->toBe($this->e['docente']->id);
});

test('cambiar docente desde la pantalla: exige motivo y la materia sale de la lista', function () {
    $a = ($this->e['asignar'])($this->e['materiaA']);
    $nuevo = docenteActivo();

    $w = Livewire::test(PantallaAsignacion::class, ['docente' => $this->e['docente']])
        ->call('abrirCambio', $a->id)
        ->assertSee('Cambiar docente')
        ->set('cambioDocenteId', $nuevo->id)
        ->call('confirmarCambio')
        ->assertHasErrors('cambioMotivo');
    expect($a->fresh()->docente_id)->toBe($this->e['docente']->id);

    $w->set('cambioMotivo', 'Licencia por maternidad')->call('confirmarCambio')->assertHasNoErrors();
    expect($a->fresh()->docente_id)->toBe($nuevo->id)
        ->and($w->get('cambioAsignacionId'))->toBeNull()
        ->and($w->viewData('asignaciones')->pluck('id'))->not->toContain($a->id);
});

test('el borrado antiguo por dirección ya no existe', function () {
    Role::firstOrCreate(['name' => 'Admin test', 'guard_name' => 'web'])
        ->syncPermissions([Permission::firstOrCreate(['name' => 'acceso_administrativo', 'guard_name' => 'web']),
                           Permission::firstOrCreate(['name' => 'gestionar_docentes', 'guard_name' => 'web'])]);
    $admin = User::factory()->create();
    $admin->assignRole('Admin test');
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $a = ($this->e['asignar'])($this->e['materiaB']);
    $respuesta = $this->actingAs($admin)->delete('/admin/docentes/' . $a->id);

    expect(in_array($respuesta->status(), [404, 405], true))->toBeTrue()
        ->and(AsignacionDocente::find($a->id))->not->toBeNull();
});
