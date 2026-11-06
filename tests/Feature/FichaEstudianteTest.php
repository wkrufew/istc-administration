<?php

/*
 * Ficha del estudiante (datos por pestañas, revisión por secretaría, historial) y panel de
 * documentos del expediente (revisar, solicitar, historial, eliminar definitivamente).
 */

use App\Livewire\Administration\Estudiantes\FichaEstudiante;
use App\Livewire\Expediente\PanelDocumentos;
use App\Models\Aspirante;
use App\Models\AuditoriaGeneral;
use App\Models\ExpedienteDocumento;
use App\Models\User;
use App\Services\ExpedienteService;
use App\Support\FichaPersona;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function rolFicha(string $rol, array $permisos): User
{
    foreach ($permisos as $p) Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web'])->syncPermissions($permisos);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $u = User::factory()->create();
    $u->assignRole($rol);
    return $u->fresh();
}

/** Estudiante con todos los datos obligatorios llenos. */
function estudianteCompleto(array $datos = []): User
{
    $campos = collect(FichaPersona::camposRequeridos(new User(['is_facturador' => true])))
        ->mapWithKeys(fn($c) => [$c => $c === 'fecha_nacimiento' ? '2000-01-01' : 'X'])->all();

    return User::factory()->create(array_merge($campos, [
        'first_name' => 'Ana', 'last_name' => 'Pérez', 'cedula' => (string) fake()->unique()->numerify('09########'),
        'is_facturador' => true, 'bono_dh' => 'NO', 'etnia' => 'Mestizo',
    ], $datos));
}

beforeEach(function () {
    $this->secretaria = rolFicha('Secretaria', ['gestionar_estudiantes', 'editar_usuarios']);
    $this->actingAs($this->secretaria);
});

// ─────────────────────────────────────────────────────────────────────────────
// Estado y revisión de la ficha
// ─────────────────────────────────────────────────────────────────────────────

test('la ficha con datos obligatorios vacíos es "Incompleta" y no se puede aprobar', function () {
    $est = User::factory()->create();

    expect(FichaPersona::estado($est))->toBe('incompleta')->and(FichaPersona::faltantes($est))->not->toBeEmpty();

    Livewire::test(FichaEstudiante::class, ['estudiante' => $est])->call('aprobarFicha');
    expect($est->fresh()->ficha_estado)->toBeNull();
});

test('aprobar, observar y reabrir la ficha quedan registrados', function () {
    $est = estudianteCompleto();
    expect(FichaPersona::estado($est))->toBe('sin_revisar');

    $w = Livewire::test(FichaEstudiante::class, ['estudiante' => $est])
        ->set('observacionFicha', 'El ingreso del hogar no coincide')->call('observarFicha');
    expect($est->fresh()->ficha_estado)->toBe('con_observaciones')
        ->and($est->fresh()->ficha_observacion)->toBe('El ingreso del hogar no coincide');

    $w->call('aprobarFicha');
    $est->refresh();
    expect($est->ficha_estado)->toBe('aprobada')
        ->and($est->ficha_observacion)->toBeNull()
        ->and($est->ficha_revisada_por)->toBe($this->secretaria->id);

    $w->call('reabrirFicha');
    expect(FichaPersona::estado($est->fresh()))->toBe('sin_revisar');

    $registros = AuditoriaGeneral::where('auditable_type', User::class)->where('auditable_id', $est->id)->get();
    expect($registros->pluck('valores_despues.ficha')->all())->toBe(['Con observaciones', 'Aprobada', 'Sin revisar']);
});

test('guardar: todos los campos se editan, quedan en auditoría y se aplican las reglas del portal', function () {
    $est = estudianteCompleto(['address' => 'CALLE VIEJA', 'tiene_discapacidad' => true, 'tipo_discapacidad' => 'Visual']);

    Livewire::test(FichaEstudiante::class, ['estudiante' => $est])
        ->set('f.address', 'av. siempre viva 123')
        ->set('f.ingresos_hogar', '$500')
        ->set('f.tiene_discapacidad', false)
        ->set('f.etnia', 'Indígena')->set('f.pueblo_nacionalidad', 'kichwa')
        ->call('guardar')
        ->assertHasNoErrors();

    $est->refresh();
    expect($est->address)->toBe('AV. SIEMPRE VIVA 123')               // mayúsculas como en admisión
        ->and($est->pueblo_nacionalidad)->toBe('KICHWA')
        ->and($est->tiene_discapacidad)->toBeFalse()
        ->and($est->tipo_discapacidad)->toBeNull();                      // ya no aplica

    $cambio = AuditoriaGeneral::where('auditable_type', User::class)->where('auditable_id', $est->id)->latest('id')->first();
    expect($cambio->valores_antes['address'])->toBe('CALLE VIEJA')
        ->and($cambio->valores_despues['address'])->toBe('AV. SIEMPRE VIVA 123')
        ->and($cambio->valores_despues['tipo_discapacidad'])->toBeNull();
});

test('guardar sin cambios no toca nada aunque haya espacios de más o minúsculas guardadas', function () {
    $est = estudianteCompleto(['last_name' => 'PÉREZ ', 'address' => 'Cumandá centro']);

    Livewire::test(FichaEstudiante::class, ['estudiante' => $est])->call('guardar')->assertHasNoErrors();

    expect(AuditoriaGeneral::where('auditable_type', User::class)->where('auditable_id', $est->id)->count())->toBe(0)
        ->and($est->fresh()->last_name)->toBe('PÉREZ ')
        ->and($est->fresh()->address)->toBe('Cumandá centro');
});

test('guardar con un error lleva a la pestaña donde está', function () {
    $est = estudianteCompleto();
    User::factory()->create(['cedula' => '0912345678']);

    Livewire::test(FichaEstudiante::class, ['estudiante' => $est])
        ->call('cambiarTab', 'familia')
        ->set('f.cedula', '0912345678')
        ->call('guardar')
        ->assertHasErrors('f.cedula')
        ->assertSet('tab', 'personales');
});

test('admisión usa la misma lista de campos obligatorios', function () {
    $u = User::factory()->create(['etnia' => 'Indígena', 'tiene_discapacidad' => true]);
    $cohorte = DB::table('cohortes')->insertGetId(['nombre' => 'C', 'created_at' => now(), 'updated_at' => now()]);
    $asp = Aspirante::create(['user_id' => $u->id, 'cohorte_id' => $cohorte]);

    $u->update(['is_facturador' => false]);
    expect($asp->fresh()->camposFichaRequeridos())->toBe(FichaPersona::camposRequeridos($u->fresh()))
        ->and($asp->fresh()->camposFichaRequeridos())->toContain('pueblo_nacionalidad', 'nro_conadis', 'fact_nombre');
});

// ─────────────────────────────────────────────────────────────────────────────
// Panel de documentos
// ─────────────────────────────────────────────────────────────────────────────

test('panel: secretaría sube (aprobado), rechaza con observación, aprueba y solicita', function () {
    Storage::fake('local');
    $est = estudianteCompleto();
    $w = Livewire::test(PanelDocumentos::class, ['user' => $est, 'perfil' => 'estudiante']);

    $w->set('archivos.cedula', UploadedFile::fake()->create('ced.pdf', 20, 'application/pdf'))->call('subir', 'cedula')->assertHasNoErrors();
    $doc = ExpedienteService::vigente($est->id, 'cedula');
    expect($doc->estado)->toBe('aprobado');

    $w->call('abrirAccion', 'rechazar', $doc->id)->call('confirmarAccion')->assertHasErrors('accionTexto');
    $w->set('accionTexto', 'La foto está borrosa')->call('confirmarAccion')->assertHasNoErrors();
    expect($doc->fresh()->estado)->toBe('rechazado')->and($doc->fresh()->observacion)->toBe('La foto está borrosa');

    $w->call('aprobar', $doc->id);
    expect($doc->fresh()->estado)->toBe('aprobado')->and($doc->fresh()->observacion)->toBeNull();

    // Solicitar uno que no tiene: queda registrado sin archivo
    $w->call('abrirAccion', 'solicitar', null, 'bachiller')->set('accionTexto', 'Falta el título de bachiller')->call('confirmarAccion');
    $sol = ExpedienteService::vigente($est->id, 'bachiller');
    expect($sol->estado)->toBe('solicitado')->and($sol->archivo_path)->toBeNull();

    // Al subirlo, el solicitado pasa al historial
    $w->set('archivos.bachiller', UploadedFile::fake()->create('b.pdf', 20, 'application/pdf'))->call('subir', 'bachiller');
    expect($sol->fresh()->vigente)->toBeFalse()->and(ExpedienteService::vigente($est->id, 'bachiller')->tieneArchivo())->toBeTrue();
});

test('panel: no deja tocar documentos de otra persona', function () {
    Storage::fake('local');
    $est   = estudianteCompleto();
    $ajeno = ExpedienteService::subir(estudianteCompleto(), 'cedula', UploadedFile::fake()->create('x.pdf', 10, 'application/pdf'), 'secretaria');
    ExpedienteService::rechazar($ajeno, 'Prueba de rechazo');

    $w = Livewire::test(PanelDocumentos::class, ['user' => $est, 'perfil' => 'estudiante']);
    expect(fn() => $w->call('aprobar', $ajeno->id))->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    expect($ajeno->fresh()->estado)->toBe('rechazado');
});

test('eliminar definitivamente: solo Administrador, con motivo; borra el archivo y restaura la versión anterior', function () {
    Storage::fake('local');
    $est = estudianteCompleto();
    $v1 = ExpedienteService::subir($est, 'cedula', UploadedFile::fake()->create('buena.pdf', 10, 'application/pdf'), 'secretaria');
    $v2 = ExpedienteService::subir($est, 'cedula', UploadedFile::fake()->create('de-otra-persona.pdf', 10, 'application/pdf'), 'secretaria');

    // Secretaría no puede
    expect(fn() => ExpedienteService::eliminarDefinitivamente($v2, 'Se subió el de otra persona'))->toThrow(\DomainException::class);
    Livewire::test(PanelDocumentos::class, ['user' => $est, 'perfil' => 'estudiante'])
        ->call('abrirAccion', 'eliminar', $v2->id)->assertSet('accion', null);

    $this->actingAs(rolFicha('Administrador', ['gestionar_estudiantes', 'editar_usuarios']));
    expect(fn() => ExpedienteService::eliminarDefinitivamente($v2, ''))->toThrow(\DomainException::class, 'motivo');

    Livewire::test(PanelDocumentos::class, ['user' => $est, 'perfil' => 'estudiante'])
        ->call('abrirAccion', 'eliminar', $v2->id)
        ->set('accionTexto', 'Se subió la cédula de otra persona')
        ->call('confirmarAccion')->assertHasNoErrors();

    expect(ExpedienteDocumento::find($v2->id))->toBeNull()
        ->and($v1->fresh()->vigente)->toBeTrue();
    Storage::disk('local')->assertMissing($v2->archivo_path);
    Storage::disk('local')->assertExists($v1->archivo_path);

    $registro = AuditoriaGeneral::where('auditable_type', ExpedienteDocumento::class)->where('auditable_id', $v2->id)->where('evento', 'deleted')->first();
    expect($registro->valores_despues['motivo'])->toBe('Se subió la cédula de otra persona');
});

test('eliminar definitivamente un documento de admisión limpia también su referencia en admisión', function () {
    Storage::fake('public');
    Storage::disk('public')->put('aspirantes/9/cedula/c.pdf', 'pdf');
    $est = estudianteCompleto();
    $cohorte = DB::table('cohortes')->insertGetId(['nombre' => 'C', 'created_at' => now(), 'updated_at' => now()]);
    $asp = Aspirante::create(['user_id' => $est->id, 'cohorte_id' => $cohorte, 'cedula_path' => 'aspirantes/9/cedula/c.pdf', 'cedula_estado' => 'aprobado']);
    ExpedienteService::traspasarDesdeAdmision($asp);

    $this->actingAs(rolFicha('Administrador', ['gestionar_estudiantes']));
    ExpedienteService::eliminarDefinitivamente(ExpedienteService::vigente($est->id, 'cedula'), 'Era de otra persona');

    expect($asp->fresh()->cedula_path)->toBeNull();
    Storage::disk('public')->assertMissing('aspirantes/9/cedula/c.pdf');
});

test('la ficha abre cada pestaña con sus datos', function () {
    $est = estudianteCompleto();
    $w = Livewire::test(FichaEstudiante::class, ['estudiante' => $est]);

    foreach (array_keys(FichaEstudiante::TABS) as $tab) {
        $w->call('cambiarTab', $tab)->assertSet('tab', $tab)->assertOk();
    }
    $w->call('cambiarTab', 'socioeconomicos')->assertSee('Tipo de colegio');
    $w->call('cambiarTab', 'documentos')->assertSee('Cédula de identidad');
});
