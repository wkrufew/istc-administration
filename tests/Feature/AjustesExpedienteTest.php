<?php

/*
 * Ajustes acordados sobre el expediente y la matrícula:
 *  1. Eliminar definitivamente visible en cada documento y en los contratos (solo Administrador).
 *  2. Documentos de validación solo para estudiantes de validación.
 *  3. CONADIS solo con discapacidad marcada (estudiantes y docentes).
 *  4. Resumen del pago de admisión en la ficha.
 *  5. En la primera matrícula de un aspirante la carrera es la de admisión.
 */

use App\Livewire\Administration\Docentes\EditarDocente;
use App\Livewire\Administration\DocumentosPersonalForm;
use App\Livewire\Administration\Matriculacion;
use App\Livewire\Expediente\PanelDocumentos;
use App\Models\Aspirante;
use App\Models\AsignacionDocente;
use App\Models\ExpedienteDocumento;
use App\Models\User;
use App\Services\ExpedienteService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function personalCon(string $rol, array $permisos): User
{
    foreach ($permisos as $p) Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web'])->syncPermissions($permisos);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $u = User::factory()->create();
    $u->assignRole($rol);
    return $u->fresh();
}

function cohorteId(): int
{
    return DB::table('cohortes')->insertGetId(['nombre' => 'C ' . uniqid(), 'created_at' => now(), 'updated_at' => now()]);
}

function carreraId(): int
{
    return DB::table('carreras')->insertGetId(['name' => 'Carrera ' . uniqid(), 'code' => uniqid(), 'created_at' => now(), 'updated_at' => now()]);
}

function matriculaDe(User $u, int $carrera, string $tipo = 'Nueva'): int
{
    $periodo = DB::table('periodos')->insertGetId(['code' => 'P' . uniqid(), 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30',
        'fecha_limite_matricula' => '2026-01-10', 'fecha_limite_pago' => '2026-01-20', 'created_at' => now(), 'updated_at' => now()]);
    return DB::table('matriculas')->insertGetId(['code' => 'ISTC-MATRICULA-' . rand(1000, 9999), 'fecha_matricula' => now(), 'tipo' => $tipo,
        'estado' => 'Habilitada', 'periodo_id' => $periodo, 'carrera_id' => $carrera, 'user_id' => $u->id, 'created_at' => now(), 'updated_at' => now()]);
}

beforeEach(function () {
    $this->actingAs(personalCon('Secretaria', ['gestionar_estudiantes', 'gestionar_docentes', 'editar_usuarios']));
});

// ── 2 y 3: qué documentos corresponden ───────────────────────────────────────

test('estudiante regular que entró directo: sin documentos de validación, sin pago de admisión ni CONADIS', function () {
    $est = User::factory()->create();
    matriculaDe($est, carreraId());

    $tipos = array_keys(ExpedienteService::tiposVisibles($est, 'estudiante'));
    expect($tipos)->toContain('cedula', 'bachiller', 'habilitante')
        ->not->toContain('hoja_vida', 'cert_laborales', 'cert_cursos', 'mecanizado_iess', 'pago_admision', 'conadis');
});

test('validación: se reconoce por la matrícula de Validación (entró directo) o por admisión', function () {
    $directo = User::factory()->create();
    $carrera = carreraId();
    // En SQLite la lista de tipos de matrícula no incluye "Validacion" (se agregó con un ALTER de MySQL)
    if (DB::getDriverName() === 'sqlite') DB::statement('PRAGMA ignore_check_constraints = ON');
    matriculaDe($directo, $carrera, \App\Models\Matricula::TIPO_VALIDACION);

    $porAdmision = User::factory()->create();
    Aspirante::create(['user_id' => $porAdmision->id, 'cohorte_id' => cohorteId(), 'tipo_proceso' => 'validacion_conocimientos']);

    foreach ([$directo, $porAdmision] as $u) {
        expect(ExpedienteService::esValidacion($u))->toBeTrue()
            ->and(array_keys(ExpedienteService::tiposVisibles($u, 'estudiante')))->toContain('hoja_vida', 'cert_laborales', 'cert_cursos', 'mecanizado_iess');
    }
    // vino por admisión → sí ve el comprobante de pago
    expect(array_keys(ExpedienteService::tiposVisibles($porAdmision, 'estudiante')))->toContain('pago_admision');
});

test('un documento ya cargado se muestra aunque no le corresponda', function () {
    Storage::fake('local');
    $est = User::factory()->create();
    ExpedienteService::subir($est, 'cert_laborales', UploadedFile::fake()->create('c.pdf', 10, 'application/pdf'), 'secretaria');

    expect(array_keys(ExpedienteService::tiposVisibles($est, 'estudiante')))->toContain('cert_laborales')
        ->not->toContain('cert_cursos');
});

test('CONADIS solo con discapacidad marcada, también en el docente desde su formulario', function () {
    $est = User::factory()->create(['tiene_discapacidad' => true]);
    expect(array_keys(ExpedienteService::tiposVisibles($est, 'estudiante')))->toContain('conadis');

    $docente = User::factory()->create(['tiene_discapacidad' => false, 'first_name' => 'Doc', 'last_name' => 'Ente', 'cedula' => '0911111111']);
    expect(array_keys(ExpedienteService::tiposVisibles($docente, 'docente')))->not->toContain('conadis');

    Livewire::test(EditarDocente::class, ['docente' => $docente])
        ->set('tiene_discapacidad', true)->set('tipo_discapacidad', 'Visual')->set('porcentaje_discapacidad', '40')
        ->call('guardar')->assertHasNoErrors();
    $docente->refresh();
    expect($docente->tiene_discapacidad)->toBeTrue()->and($docente->porcentaje_discapacidad)->toBe(40)
        ->and(array_keys(ExpedienteService::tiposVisibles($docente, 'docente')))->toContain('conadis');

    // al desmarcarla, los datos dependientes se vacían
    Livewire::test(EditarDocente::class, ['docente' => $docente])->set('tiene_discapacidad', false)->call('guardar');
    expect($docente->fresh()->tipo_discapacidad)->toBeNull();
});

// ── 4: pago de admisión ──────────────────────────────────────────────────────

test('la ficha muestra el monto confirmado del pago de admisión y a qué matrícula se aplicó', function () {
    $est = User::factory()->create();
    $matricula = matriculaDe($est, carreraId());
    $obligacion = DB::table('obligaciones_financieras')->insertGetId([
        'user_id' => $est->id, 'periodo_id' => DB::table('matriculas')->where('id', $matricula)->value('periodo_id'), 'matricula_id' => $matricula,
        'tipo' => 'MATRICULA', 'monto_original' => 70, 'descuento' => 0, 'monto_final' => 70, 'estado' => 'Pagado',
        'fecha_vencimiento' => now(), 'created_at' => now(), 'updated_at' => now(),
    ]);
    $pago = DB::table('pagos')->insertGetId(['numero_comprobante' => 'C-' . uniqid(), 'obligacion_id' => $obligacion, 'monto' => 70, 'metodo_pago' => 'Transferencia',
        'estado' => 'Aprobado', 'fecha_pago' => now(), 'created_at' => now(), 'updated_at' => now()]);
    Aspirante::create(['user_id' => $est->id, 'cohorte_id' => cohorteId(), 'pago_estado' => 'verificado', 'pago_monto' => 70,
        'pago_metodo' => 'Transferencia', 'pago_verificado_at' => now(), 'pago_aplicado_id' => $pago]);

    $resumen = ExpedienteService::resumenPagoAdmision($est);
    expect($resumen['monto'])->toBe(70.0)
        ->and($resumen['metodo'])->toBe('Transferencia')
        ->and($resumen['matricula'])->toBe(DB::table('matriculas')->where('id', $matricula)->value('code'));

    Livewire::test(PanelDocumentos::class, ['user' => $est, 'perfil' => 'estudiante'])
        ->assertSee('Monto confirmado')->assertSee('$70.00')->assertSee('Aplicado a la matrícula');
})->skip(fn() => ! DB::getSchemaBuilder()->hasTable('pagos'), 'sin tabla pagos');

// ── 1: eliminar definitivamente visible ──────────────────────────────────────

test('eliminar definitivamente aparece en la fila del documento y en el contrato solo para Administrador', function () {
    Storage::fake('local');
    $docente = User::factory()->create();
    ExpedienteService::subir($docente, 'hoja_vida', UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf'), 'secretaria');
    $carrera = carreraId();
    $sem = DB::table('semestres')->insertGetId(['name' => 'S1', 'code' => uniqid(), 'carrera_id' => $carrera, 'created_at' => now(), 'updated_at' => now()]);
    $mat = DB::table('materias')->insertGetId(['name' => 'M', 'code' => uniqid(), 'semestre_id' => $sem, 'created_at' => now(), 'updated_at' => now()]);
    $par = DB::table('paralelos')->insertGetId(['name' => 'A', 'code' => uniqid(), 'created_at' => now(), 'updated_at' => now()]);
    $per = DB::table('periodos')->insertGetId(['code' => 'PC', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30',
        'fecha_limite_matricula' => '2026-01-10', 'fecha_limite_pago' => '2026-01-20', 'created_at' => now(), 'updated_at' => now()]);
    $asig = AsignacionDocente::create(['docente_id' => $docente->id, 'materia_id' => $mat, 'paralelo_id' => $par, 'periodo_id' => $per]);
    $contrato = ExpedienteService::subir($docente, 'contrato', UploadedFile::fake()->create('k.pdf', 10, 'application/pdf'), 'secretaria', $asig->id, 'firmado');

    // Secretaría: no aparece
    Livewire::test(PanelDocumentos::class, ['user' => $docente, 'perfil' => 'docente'])->assertDontSee('Eliminar definitivamente');
    Livewire::test(DocumentosPersonalForm::class, ['user' => $docente])->assertDontSee('Eliminar definitivamente')
        ->call('abrirEliminarContrato', $asig->id)->assertSet('contratoEliminarId', null);

    // Administrador: aparece en la fila y en el contrato, y borra con motivo
    $this->actingAs(personalCon('Administrador', ['gestionar_docentes']));
    Livewire::test(PanelDocumentos::class, ['user' => $docente, 'perfil' => 'docente'])->assertSee('Eliminar definitivamente');
    Livewire::test(DocumentosPersonalForm::class, ['user' => $docente])
        ->assertSee('Eliminar definitivamente')
        ->call('abrirEliminarContrato', $asig->id)->assertSet('contratoEliminarId', $contrato->id)
        ->call('confirmarEliminarContrato')->assertHasErrors('motivoEliminarContrato')
        ->set('motivoEliminarContrato', 'Se subió el contrato de otro docente')->call('confirmarEliminarContrato')->assertHasNoErrors();

    expect(ExpedienteDocumento::find($contrato->id))->toBeNull();
    Storage::disk('local')->assertMissing($contrato->archivo_path);
});

// ── 5: carrera de admisión en la primera matrícula ───────────────────────────

test('primera matrícula de un aspirante: la carrera es la de admisión y no se puede cambiar', function () {
    $carreraAdmision = carreraId();
    $otraCarrera     = carreraId();
    $u = User::factory()->create();
    Aspirante::create(['user_id' => $u->id, 'cohorte_id' => cohorteId(), 'carrera_id' => $carreraAdmision, 'estado' => 'aprobado']);

    $m = new Matriculacion();
    $m->estudiante = $u->fresh();
    expect($m->carreraDeAdmision())->toBe($carreraAdmision);

    $respeta = new ReflectionMethod($m, 'carreraRespetaAdmision');
    $m->carrera_id = $otraCarrera;
    expect($respeta->invoke($m))->toBeFalse()->and((int) $m->carrera_id)->toBe($carreraAdmision);   // vuelve a la de admisión
    $m->carrera_id = $carreraAdmision;
    expect($respeta->invoke($m))->toBeTrue();

    // Ya matriculado (o con matrícula regular): la carrera se elige libremente
    matriculaDe($u, $carreraAdmision);
    expect($m->carreraDeAdmision())->toBeNull();
});
