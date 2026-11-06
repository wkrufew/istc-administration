<?php

/*
 * Expediente único de documentos: migración de lo existente, traspaso desde admisión,
 * historial de versiones, acceso a los archivos y pantallas de documentos del docente.
 */

use App\Livewire\Administration\DocumentosPersonalForm;
use App\Livewire\Administration\DocumentosPersonalListado;
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

function migracionExpediente(): object
{
    return require database_path('migrations/2026_11_06_000002_migrar_documentos_al_expediente.php');
}

function aspiranteConDocumentos(User $user, array $datos = []): Aspirante
{
    $cohorte = DB::table('cohortes')->insertGetId(['nombre' => 'Cohorte ' . uniqid(), 'created_at' => now(), 'updated_at' => now()]);
    return Aspirante::create(array_merge([
        'user_id' => $user->id, 'cohorte_id' => $cohorte, 'estado' => 'aprobado', 'tipo_proceso' => 'regular',
        'cedula_path' => 'aspirantes/1/cedula/c.pdf', 'cedula_estado' => 'aprobado',
        'bachiller_path' => 'aspirantes/1/bachiller/b.pdf', 'bachiller_estado' => 'rechazado', 'bachiller_observacion' => 'Ilegible',
        'pago_comprobante_path' => 'aspirantes/1/pago/p.pdf', 'pago_estado' => 'verificado',
    ], $datos));
}

function conMatricula(User $user): void
{
    $carrera = DB::table('carreras')->insertGetId(['name' => 'C ' . uniqid(), 'code' => uniqid(), 'created_at' => now(), 'updated_at' => now()]);
    $periodo = DB::table('periodos')->insertGetId(['code' => 'P' . uniqid(), 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30',
        'fecha_limite_matricula' => '2026-01-10', 'fecha_limite_pago' => '2026-01-20', 'created_at' => now(), 'updated_at' => now()]);
    DB::table('matriculas')->insert(['code' => 'M' . uniqid(), 'fecha_matricula' => now(), 'tipo' => 'Nueva', 'estado' => 'Habilitada',
        'periodo_id' => $periodo, 'carrera_id' => $carrera, 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()]);
}

function asignacionDe(User $docente, ?string $contrato = null): AsignacionDocente
{
    $carrera = DB::table('carreras')->insertGetId(['name' => 'C ' . uniqid(), 'code' => uniqid(), 'created_at' => now(), 'updated_at' => now()]);
    $semestre = DB::table('semestres')->insertGetId(['name' => 'S1', 'code' => uniqid(), 'carrera_id' => $carrera, 'created_at' => now(), 'updated_at' => now()]);
    $materia = DB::table('materias')->insertGetId(['name' => 'Materia', 'code' => uniqid(), 'semestre_id' => $semestre, 'created_at' => now(), 'updated_at' => now()]);
    $paralelo = DB::table('paralelos')->insertGetId(['name' => 'A', 'code' => uniqid(), 'created_at' => now(), 'updated_at' => now()]);
    $periodo = DB::table('periodos')->insertGetId(['code' => 'P' . uniqid(), 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30',
        'fecha_limite_matricula' => '2026-01-10', 'fecha_limite_pago' => '2026-01-20', 'created_at' => now(), 'updated_at' => now()]);
    return AsignacionDocente::create(['docente_id' => $docente->id, 'materia_id' => $materia, 'paralelo_id' => $paralelo,
        'periodo_id' => $periodo, 'file_contrato' => $contrato]);
}

function gestorDocentes(): User
{
    foreach (['acceso_administrativo', 'gestionar_docentes'] as $p) Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Secretaria', 'guard_name' => 'web'])->syncPermissions(['acceso_administrativo', 'gestionar_docentes']);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $u = User::factory()->create();
    $u->assignRole('Secretaria');
    return $u->fresh();
}

// ─────────────────────────────────────────────────────────────────────────────
// Migración de lo existente
// ─────────────────────────────────────────────────────────────────────────────

test('migración: pasa los documentos de admisión de quien ya tiene matrícula, con su estado', function () {
    $matriculado = User::factory()->create();
    aspiranteConDocumentos($matriculado);
    conMatricula($matriculado);
    $enAdmision = User::factory()->create();
    aspiranteConDocumentos($enAdmision);

    migracionExpediente()->up();

    $docs = ExpedienteDocumento::where('user_id', $matriculado->id)->get()->keyBy('tipo');
    expect($docs->keys()->sort()->values()->all())->toBe(['bachiller', 'cedula', 'pago_admision'])
        ->and($docs['cedula']->estado)->toBe('aprobado')
        ->and($docs['pago_admision']->estado)->toBe('aprobado')
        ->and($docs['bachiller']->estado)->toBe('rechazado')
        ->and($docs['bachiller']->observacion)->toBe('Ilegible')
        ->and($docs['cedula']->disco)->toBe('public')
        ->and($docs['cedula']->archivo_path)->toBe('aspirantes/1/cedula/c.pdf');
    // quien sigue en admisión no pasa: se maneja en Admisión hasta su primera matrícula
    expect(ExpedienteDocumento::where('user_id', $enAdmision->id)->count())->toBe(0);
});

test('migración: documentos y contratos de docentes, sin tocar las tablas de origen', function () {
    $docente = User::factory()->create();
    DB::table('documents')->insert(['user_id' => $docente->id, 'file_curriculum' => 'dp/cv.pdf', 'file_senescyt' => 'dp/sen.pdf',
        'file_cedula' => 'dp/ced.pdf', 'created_at' => now(), 'updated_at' => now()]);
    $asignacion = asignacionDe($docente, 'dp/contratos/c1.pdf');

    migracionExpediente()->up();

    $docs = ExpedienteDocumento::where('user_id', $docente->id)->get();
    expect($docs->pluck('tipo')->sort()->values()->all())->toBe(['cedula', 'contrato', 'hoja_vida', 'senescyt'])
        ->and($docs->every(fn($d) => $d->estado === 'aprobado' && $d->vigente))->toBeTrue();
    $contrato = $docs->firstWhere('tipo', 'contrato');
    expect($contrato->asignacion_docente_id)->toBe($asignacion->id)->and($contrato->variante)->toBe('firmado');
    // las columnas de origen siguen igual
    expect(DB::table('documents')->value('file_cedula'))->toBe('dp/ced.pdf')
        ->and($asignacion->fresh()->file_contrato)->toBe('dp/contratos/c1.pdf');
})->skip(fn() => ! DB::getSchemaBuilder()->hasColumn('asignacion_docentes', 'file_contrato'), 'sin columna file_contrato');

test('migración: el mismo documento en dos lugares deja vigente el más reciente y down() borra solo lo importado', function () {
    $user = User::factory()->create();
    aspiranteConDocumentos($user, ['bachiller_path' => null, 'pago_comprobante_path' => null, 'updated_at' => now()->subYear()]);
    conMatricula($user);
    DB::table('documents')->insert(['user_id' => $user->id, 'file_cedula' => 'dp/ced-nueva.pdf', 'created_at' => now(), 'updated_at' => now()]);
    ExpedienteDocumento::create(['user_id' => $user->id, 'tipo' => 'otro', 'archivo_path' => 'x.pdf', 'origen' => 'secretaria']);

    migracionExpediente()->up();

    $cedulas = ExpedienteDocumento::where('user_id', $user->id)->where('tipo', 'cedula')->get();
    expect($cedulas)->toHaveCount(2)
        ->and($cedulas->where('vigente', true)->pluck('archivo_path')->all())->toBe(['dp/ced-nueva.pdf']);

    migracionExpediente()->down();
    expect(ExpedienteDocumento::where('user_id', $user->id)->pluck('tipo')->all())->toBe(['otro']);
});

// ─────────────────────────────────────────────────────────────────────────────
// Traspaso en la primera matrícula
// ─────────────────────────────────────────────────────────────────────────────

test('traspaso desde admisión: pasa una sola vez aunque se repita', function () {
    $user = User::factory()->create(['certificado_discapacidad_path' => 'users/1/conadis/c.pdf']);
    $aspirante = aspiranteConDocumentos($user);

    expect(ExpedienteService::traspasarDesdeAdmision($aspirante))->toBe(4)
        ->and(ExpedienteService::traspasarDesdeAdmision($aspirante->fresh()))->toBe(0)
        ->and(ExpedienteDocumento::where('user_id', $user->id)->count())->toBe(4)
        ->and(ExpedienteDocumento::where('user_id', $user->id)->where('tipo', 'conadis')->value('estado'))->toBe('pendiente');
});

// ─────────────────────────────────────────────────────────────────────────────
// Versiones y acceso a los archivos
// ─────────────────────────────────────────────────────────────────────────────

test('subir: guarda en disco privado, la versión anterior queda en el historial', function () {
    Storage::fake('local');
    $docente = User::factory()->create();
    $this->actingAs($secretaria = gestorDocentes());

    $v1 = ExpedienteService::subir($docente, 'hoja_vida', UploadedFile::fake()->create('cv1.pdf', 50, 'application/pdf'), 'secretaria');
    $v2 = ExpedienteService::subir($docente, 'hoja_vida', UploadedFile::fake()->create('cv2.pdf', 50, 'application/pdf'), 'docente');

    expect($v1->fresh()->vigente)->toBeFalse()
        ->and($v2->vigente)->toBeTrue()
        ->and($v1->estado)->toBe('aprobado')->and($v1->revisado_por)->toBe($secretaria->id)
        ->and($v2->estado)->toBe('pendiente')
        ->and($v2->disco)->toBe('local')
        ->and($v2->nombre_original)->toBe('cv2.pdf');
    Storage::disk('local')->assertExists($v1->archivo_path);
    Storage::disk('local')->assertExists($v2->archivo_path);
});

test('archivo: lo abre el dueño o quien gestiona docentes; nadie más', function () {
    Storage::fake('local');
    $docente = User::factory()->create();
    $doc = ExpedienteService::subir($docente, 'cedula', UploadedFile::fake()->create('ced.pdf', 20, 'application/pdf'), 'docente');

    $this->actingAs($docente)->get($doc->url())->assertOk();
    $this->actingAs(gestorDocentes())->get($doc->url())->assertOk();
    $this->actingAs(User::factory()->create())->get($doc->url())->assertRedirect();

    Storage::disk('local')->delete($doc->archivo_path);
    $this->actingAs($docente)->get($doc->url())->assertNotFound();
});

// ─────────────────────────────────────────────────────────────────────────────
// Pantallas de documentos del docente
// ─────────────────────────────────────────────────────────────────────────────

test('documentos del docente: se guardan en el expediente y quitar no borra el archivo', function () {
    Storage::fake('local');
    $this->actingAs(gestorDocentes());
    $docente = User::factory()->create();
    $asignacion = asignacionDe($docente);

    // Los documentos personales se suben en el panel del expediente (perfil docente)
    Livewire::test(\App\Livewire\Expediente\PanelDocumentos::class, ['user' => $docente, 'perfil' => 'docente'])
        ->set('archivos.hoja_vida', UploadedFile::fake()->create('cv.pdf', 30, 'application/pdf'))->call('subir', 'hoja_vida')
        ->set('archivos.cedula', UploadedFile::fake()->create('ced.pdf', 30, 'application/pdf'))->call('subir', 'cedula')
        ->assertHasNoErrors();

    $w = Livewire::test(DocumentosPersonalForm::class, ['user' => $docente]);
    expect(ExpedienteDocumento::vigentes()->where('user_id', $docente->id)->pluck('tipo')->sort()->values()->all())->toBe(['cedula', 'hoja_vida']);
    expect($w->viewData('urls')['file_curriculum'])->not->toBeNull()->and($w->viewData('urls')['file_senescyt'])->toBeNull();

    $w->call('seleccionarAsignacion', $asignacion->id)
      ->set('contratoTemporal', UploadedFile::fake()->create('contrato.pdf', 30, 'application/pdf'))
      ->call('subirContrato')->assertHasNoErrors();
    $contrato = ExpedienteService::vigente($docente->id, 'contrato', $asignacion->id);
    expect($contrato->variante)->toBe('firmado')->and($w->viewData('contratos'))->toHaveKey($asignacion->id);

    $w->call('eliminarContrato', $asignacion->id);
    expect($contrato->fresh()->vigente)->toBeFalse()->and($w->viewData('contratos'))->not->toHaveKey($asignacion->id);
    Storage::disk('local')->assertExists($contrato->archivo_path);
});

test('listado de documentación: muestra al personal con documentos y quitarlos los deja en el historial', function () {
    Storage::fake('local');
    $this->actingAs(gestorDocentes());
    $docente = User::factory()->create(['name' => 'Docente Con Docs']);
    ExpedienteService::subir($docente, 'senescyt', UploadedFile::fake()->create('s.pdf', 10, 'application/pdf'), 'secretaria');

    $w = Livewire::test(DocumentosPersonalListado::class);
    $fila = $w->viewData('documentos')->first();
    expect($fila->user->id)->toBe($docente->id)
        ->and($fila->file_senescyt)->not->toBeNull()
        ->and($w->instance()->countFiles($fila))->toBe(1);

    $w->call('deleteDocument', $docente->id);
    expect(ExpedienteDocumento::where('user_id', $docente->id)->count())->toBe(1)
        ->and(ExpedienteDocumento::vigentes()->where('user_id', $docente->id)->count())->toBe(0)
        ->and($w->viewData('documentos')->total())->toBe(0);
});
