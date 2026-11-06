<?php

/*
 * Secciones de personas (Usuarios, Docentes, Estudiantes): permisos, asignación de roles,
 * protección del Super Admin, navegación y reenvío de credenciales.
 */

use App\Livewire\Administration\CreateUser;
use App\Livewire\Administration\Docentes\EditarDocente;
use App\Livewire\Administration\Estudiantes\FichaEstudiante;
use App\Livewire\Administration\UsersAdministration;
use App\Livewire\Administration\Usuarios\EditarCuenta;
use App\Models\AuditoriaGeneral;
use App\Models\Setting;
use App\Models\User;
use App\Services\CredencialesService;
use App\Services\SettingService;
use App\Support\PerfilUsuario;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

// ─────────────────────────────────────────────────────────────────────────────
// Escenario: roles como en producción (resumido)
// ─────────────────────────────────────────────────────────────────────────────

const PERMISOS_PERSONAS = [
    'acceso_administrativo', 'acceso_docencia', 'acceso_estudiantil', 'acceso_admision',
    'asignar_roles', 'gestionar_usuarios', 'gestionar_docentes', 'gestionar_estudiantes', 'gestionar_aspirantes',
    'crear_usuarios', 'editar_usuarios', 'eliminar_usuarios', 'reenviar_credenciales', 'moodle_gestion',
];

function rolesPersonas(): array
{
    foreach (PERMISOS_PERSONAS as $p) Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);

    $roles = [
        'Super Admin'   => PERMISOS_PERSONAS,
        'Administrador' => PERMISOS_PERSONAS,
        // Secretaría: maneja personas pero no asigna roles
        'Secretaria'    => ['acceso_administrativo', 'gestionar_usuarios', 'gestionar_docentes', 'gestionar_estudiantes',
                            'crear_usuarios', 'editar_usuarios', 'reenviar_credenciales'],
        'Rector'        => ['acceso_administrativo', 'acceso_docencia'],
        'Docente'       => ['acceso_docencia'],
        'Estudiante'    => ['acceso_estudiantil'],
        // Sin permiso de ninguna sección de personas
        'Procuraduria'  => ['acceso_administrativo'],
    ];
    foreach ($roles as $nombre => $permisos) {
        Role::firstOrCreate(['name' => $nombre, 'guard_name' => 'web'])->syncPermissions($permisos);
    }
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return collect($roles)->keys()->mapWithKeys(fn($r) => [$r => Role::findByName($r)])->all();
}

function personaCon(string $rol, array $datos = []): User
{
    $user = User::factory()->create(array_merge([
        'first_name' => 'Nombre', 'last_name' => 'Apellido', 'is_active' => true,
        'cedula' => (string) fake()->unique()->numerify('09########'),
    ], $datos));
    $user->assignRole($rol);
    return $user->fresh();
}

function activarCorreo(bool $activo = true): void
{
    Setting::updateOrCreate(['key' => 'smtp.activo'],
        ['group' => 'smtp', 'value' => $activo ? '1' : '0', 'type' => 'boolean', 'label' => 'SMTP activo']);
    SettingService::flush();
}

beforeEach(function () {
    Bus::fake();
    $this->roles = rolesPersonas();
});

// ─────────────────────────────────────────────────────────────────────────────
// Rutas: cada sección exige su permiso
// ─────────────────────────────────────────────────────────────────────────────

test('sin el permiso de la sección no se entra escribiendo la dirección', function () {
    $sinPermiso = personaCon('Procuraduria');
    $estudiante = personaCon('Estudiante');

    // La app no muestra un error: redirige con el aviso "Acceso denegado" (bootstrap/app.php)
    $this->actingAs($sinPermiso);
    foreach ([
        route('administracion.administrativa.estudiantes.index'),
        route('administracion.administrativa.estudiantes.edit', $estudiante),
        route('administracion.administrativa.docentes.index'),
        route('administracion.administrativa.docentes.edit', $estudiante),
        route('administracion.administrativa.users.index'),
        route('administracion.administrativa.users.edit', $estudiante),
    ] as $url) {
        $this->get($url)->assertRedirect()->assertSessionHas('swal');
    }
});

test('la ruta vieja que cambiaba roles por formulario ya no existe', function () {
    $estudiante = personaCon('Estudiante');
    $this->actingAs(personaCon('Procuraduria'));

    $this->put('/admin/users/' . $estudiante->id, ['roles' => [$this->roles['Super Admin']->id]])
        ->assertNotFound();
    expect($estudiante->fresh()->hasRole('Estudiante'))->toBeTrue();
});

test('cada ficha abre en su sección y redirige si la persona es de otra', function () {
    $docente    = personaCon('Docente');
    $estudiante = personaCon('Estudiante');
    $this->actingAs(personaCon('Administrador'));

    $this->get(route('administracion.administrativa.estudiantes.edit', $docente))
        ->assertRedirect(route('administracion.administrativa.docentes.edit', $docente));
    $this->get(route('administracion.administrativa.docentes.edit', $estudiante))
        ->assertRedirect(route('administracion.administrativa.estudiantes.edit', $estudiante));
});

test('todas las pantallas de personas cargan y Moodle vuelve a su sección', function () {
    $docente    = personaCon('Docente');
    $estudiante = personaCon('Estudiante');
    $cuenta     = personaCon('Rector');
    $this->actingAs(personaCon('Administrador'));

    foreach ([
        'users.index' => [], 'users.create' => [], 'users.edit' => [$cuenta],
        'docentes.index' => [], 'docentes.create' => [], 'docentes.edit' => [$docente],
        'estudiantes.index' => [], 'estudiantes.create' => [], 'estudiantes.edit' => [$estudiante],
    ] as $ruta => $params) {
        $this->get(route('administracion.administrativa.' . $ruta, $params))->assertOk();
    }

    $this->get(route('administracion.administrativa.estudiantes.moodle', $estudiante))
        ->assertOk()->assertSee(route('administracion.administrativa.estudiantes.index'), false);
    $this->get(route('administracion.administrativa.docentes.moodle', $docente))
        ->assertOk()->assertSee(route('administracion.administrativa.docentes.edit', $docente), false);
});

// ─────────────────────────────────────────────────────────────────────────────
// Roles: quién los asigna y cuáles
// ─────────────────────────────────────────────────────────────────────────────

test('sin asignar_roles no se cambia el rol aunque se envíe a mano', function () {
    $docente = personaCon('Docente');
    $this->actingAs(personaCon('Secretaria'));

    Livewire::test(EditarCuenta::class, ['usuario' => $docente])
        ->set('role_id', $this->roles['Administrador']->id)
        ->call('guardar');

    expect($docente->fresh()->getRoleNames()->all())->toBe(['Docente']);
});

test('solo un Super Admin asigna el rol Super Admin', function () {
    $docente = personaCon('Docente');
    $this->actingAs(personaCon('Administrador'));

    Livewire::test(EditarCuenta::class, ['usuario' => $docente])
        ->set('role_id', $this->roles['Super Admin']->id)
        ->call('guardar')
        ->assertHasErrors('role_id');
    expect($docente->fresh()->hasRole('Super Admin'))->toBeFalse();

    $this->actingAs(personaCon('Super Admin'));
    Livewire::test(EditarCuenta::class, ['usuario' => $docente])
        ->set('role_id', $this->roles['Super Admin']->id)
        ->call('guardar')
        ->assertHasNoErrors();
    expect($docente->fresh()->hasRole('Super Admin'))->toBeTrue();
});

test('un cambio de rol queda en auditoría con el rol anterior y el nuevo', function () {
    $docente = personaCon('Docente');
    $this->actingAs(personaCon('Administrador'));

    Livewire::test(EditarCuenta::class, ['usuario' => $docente])
        ->set('role_id', $this->roles['Rector']->id)
        ->call('guardar')
        ->assertHasNoErrors();

    $registro = AuditoriaGeneral::where('auditable_type', User::class)->where('auditable_id', $docente->id)->latest('id')->first();
    expect($docente->fresh()->hasRole('Rector'))->toBeTrue()
        ->and($registro->valores_antes['rol'])->toBe('Docente')
        ->and($registro->valores_despues['rol'])->toBe('Rector');
});

test('la cuenta de un Super Admin no la toca quien no es Super Admin', function () {
    $superAdmin = personaCon('Super Admin', ['first_name' => 'Original']);
    $this->actingAs(personaCon('Administrador'));

    Livewire::test(EditarCuenta::class, ['usuario' => $superAdmin])
        ->assertSee('Solo un Super Admin puede')
        ->set('first_name', 'Cambiado')
        ->set('is_active', false)
        ->call('guardar');

    $superAdmin->refresh();
    expect($superAdmin->first_name)->toBe('Original')->and($superAdmin->is_active)->toBeTrue();

    Livewire::test(UsersAdministration::class)->call('toggleStatus', $superAdmin->id);
    Livewire::test(UsersAdministration::class)->call('eliminar', $superAdmin->id);
    expect(User::find($superAdmin->id))->not->toBeNull()->and(User::find($superAdmin->id)->is_active)->toBeTrue();
});

test('nadie se desactiva ni se quita a sí mismo el permiso de asignar roles', function () {
    $admin = personaCon('Administrador');
    $this->actingAs($admin);

    Livewire::test(EditarCuenta::class, ['usuario' => $admin])->set('is_active', false)->call('guardar')->assertHasErrors('is_active');
    Livewire::test(EditarCuenta::class, ['usuario' => $admin])->set('role_id', $this->roles['Docente']->id)->call('guardar')->assertHasErrors('role_id');
    Livewire::test(UsersAdministration::class)->call('eliminar', $admin->id);

    $admin->refresh();
    expect($admin->is_active)->toBeTrue()->and($admin->hasRole('Administrador'))->toBeTrue()->and($admin->trashed())->toBeFalse();
});

test('al crear desde una sección solo se aceptan los roles de esa sección', function () {
    $this->actingAs(personaCon('Administrador'));

    $form = fn(string $perfil) => Livewire::test(CreateUser::class, ['perfil' => $perfil])
        ->set('first_name', 'Ana')->set('last_name', 'Pérez')
        ->set('email', fake()->unique()->safeEmail())->set('cedula', (string) fake()->unique()->numerify('09########'))
        ->set('password', 'secreto123')->set('password_confirmation', 'secreto123');

    $form(PerfilUsuario::DOCENTE)->assertSet('role_id', $this->roles['Docente']->id)
        ->set('role_id', $this->roles['Administrador']->id)->call('save')->assertHasErrors('role_id');

    $form(PerfilUsuario::CUENTA)->set('role_id', $this->roles['Super Admin']->id)->call('save')->assertHasErrors('role_id');

    $form(PerfilUsuario::DOCENTE)->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('administracion.administrativa.docentes.index'));
    expect(User::where('first_name', 'Ana')->first()->hasRole('Docente'))->toBeTrue();
});

test('la ficha del estudiante ya no cambia el rol', function () {
    $estudiante = personaCon('Estudiante');
    $this->actingAs(personaCon('Secretaria'));

    Livewire::test(FichaEstudiante::class, ['estudiante' => $estudiante])
        ->set('f.first_name', 'Nuevo')
        ->set('f.is_facturador', true)
        ->call('guardar')
        ->assertHasNoErrors();

    $estudiante->refresh();
    expect($estudiante->first_name)->toBe('Nuevo')->and($estudiante->getRoleNames()->all())->toBe(['Estudiante']);
});

test('la ficha del docente guarda sus datos y no permite desactivarse a uno mismo', function () {
    $rector = personaCon('Rector');
    $rector->givePermissionTo(['gestionar_docentes', 'editar_usuarios']);
    $this->actingAs($rector->fresh());

    Livewire::test(EditarDocente::class, ['docente' => $rector])
        ->set('phone', '0991112233')->set('is_active', false)
        ->call('guardar')->assertHasErrors('is_active');
    expect($rector->fresh()->is_active)->toBeTrue();

    Livewire::test(EditarDocente::class, ['docente' => $rector])
        ->set('phone', '0991112233')->call('guardar')->assertHasNoErrors();
    expect($rector->fresh()->phone)->toBe('0991112233');
});

// ─────────────────────────────────────────────────────────────────────────────
// Listados
// ─────────────────────────────────────────────────────────────────────────────

test('Administrador y Super Admin no aparecen como estudiantes ni docentes', function () {
    personaCon('Administrador');
    personaCon('Super Admin');
    $est = personaCon('Estudiante');
    $doc = personaCon('Docente');
    $rector = personaCon('Rector');

    expect(PerfilUsuario::estudiantes()->pluck('id')->all())->toBe([$est->id])
        ->and(PerfilUsuario::docentes()->pluck('id')->sort()->values()->all())->toBe(collect([$doc->id, $rector->id])->sort()->values()->all());
});

// ─────────────────────────────────────────────────────────────────────────────
// Reenvío de credenciales (un solo servicio)
// ─────────────────────────────────────────────────────────────────────────────

test('reenvío: restablece a la cédula, audita, envía y respeta la espera', function () {
    Mail::fake();
    activarCorreo();
    $docente = personaCon('Docente', ['password' => Hash::make('otraClave')]);
    $this->actingAs(personaCon('Secretaria'));

    CredencialesService::reenviarAcceso($docente);

    expect(Hash::check($docente->cedula, $docente->fresh()->password))->toBeTrue()
        ->and(CredencialesService::segundosParaReenvio($docente))->toBeGreaterThan(0);
    Mail::assertSent(\App\Mail\ReenvioCredencialesAcceso::class, fn($m) => $m->hasTo($docente->email));

    expect(fn() => CredencialesService::reenviarAcceso($docente))->toThrow(\DomainException::class, 'Espera');
});

test('reenvío: si el correo falla no cambia la contraseña ni queda auditado', function () {
    activarCorreo();
    $docente = personaCon('Docente', ['password' => Hash::make('otraClave')]);
    $this->actingAs(personaCon('Secretaria'));
    Mail::shouldReceive('mailer')->andThrow(new \Exception('servidor de correo caído'));

    expect(fn() => CredencialesService::reenviarAcceso($docente))->toThrow(\RuntimeException::class, 'La contraseña no se cambió');
    expect(Hash::check('otraClave', $docente->fresh()->password))->toBeTrue()
        ->and(CredencialesService::segundosParaReenvio($docente))->toBe(0);
});

test('reenvío: con el correo apagado o a un estudiante sin matrícula no se envía nada', function () {
    Mail::fake();
    $docente    = personaCon('Docente');
    $estudiante = personaCon('Estudiante');
    $this->actingAs(personaCon('Secretaria'));

    activarCorreo(false);
    expect(fn() => CredencialesService::reenviarAcceso($docente))->toThrow(\DomainException::class, 'desactivado');

    activarCorreo();
    expect(fn() => CredencialesService::reenviarAcceso($estudiante))->toThrow(\DomainException::class, 'matrícula habilitada');
    Mail::assertNothingSent();
});
