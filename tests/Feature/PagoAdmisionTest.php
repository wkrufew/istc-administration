<?php

/*
 * Pago de matrícula hecho en admisión:
 *  - Solo cuenta lo que se confirmó (monto, método, fecha): la ficha y la matrícula usan la misma regla.
 *  - Los verificados antes de registrar monto se completan con "Registrar pago"; no se aprueban sin él.
 *  - Corregir un pago confirmado: quien tenga gestionar_aspirantes, con motivo (decide el permiso, no el rol).
 *  - Ya aplicado a una matrícula, no se toca desde Aspirantes.
 *  - Los documentos se revisan solo en Verificación (lo exige el servidor).
 *  - La matrícula se cobra completa: el asistente no tiene descuento manual.
 */

use App\Livewire\Administration\GestionAspirantes;
use App\Livewire\Administration\Matriculacion;
use App\Models\Aspirante;
use App\Models\AuditoriaGeneral;
use App\Models\User;
use App\Services\ExpedienteService;
use App\Services\PagoAdmisionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function usuarioConRol(string $rol, array $permisos = ['gestionar_aspirantes']): User
{
    foreach ($permisos as $p) Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web'])->syncPermissions($permisos);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $u = User::factory()->create();
    $u->assignRole($rol);
    return $u->fresh();
}

/** Aspirante inscrito el 01/01/2026, aprobado y con el pago verificado "a la antigua" (sin monto confirmado). */
function aspiranteConPago(array $datos = []): Aspirante
{
    $cohorte = DB::table('cohortes')->insertGetId(['nombre' => 'C ' . uniqid(), 'created_at' => now(), 'updated_at' => now()]);
    $aspirante = Aspirante::create($datos + [
        'user_id'               => User::factory()->create()->id,
        'cohorte_id'            => $cohorte,
        'estado'                => 'aprobado',
        'pago_estado'           => 'verificado',
        'pago_comprobante_path' => 'aspirantes/1/pago/comprobante.pdf',
    ]);
    $aspirante->forceFill(['created_at' => '2026-01-01 08:00:00'])->save();
    return $aspirante->fresh();
}

beforeEach(fn() => Storage::fake('public'));

// ── La regla de "confirmado" ─────────────────────────────────────────────────

test('verificado antes del cambio (sin fecha de verificación): no cuenta como confirmado aunque tenga monto', function () {
    $aspirante = aspiranteConPago(['pago_monto' => 70]); // lo escribió el aspirante

    expect($aspirante->pagoConfirmado())->toBeFalse()
        ->and($aspirante->pagoVerificadoSinMonto())->toBeTrue()
        ->and($aspirante->tienePagoAdmisionPorAplicar())->toBeFalse()
        // la ficha ya no dice "Monto confirmado" con un valor que nadie confirmó
        ->and(ExpedienteService::resumenPagoAdmision($aspirante->user))->toBeNull();
});

test('confirmado: cuenta en la ficha y en la matrícula', function () {
    $aspirante = aspiranteConPago(['pago_monto' => 70, 'pago_metodo' => 'Efectivo', 'pago_fecha' => '2026-10-01', 'pago_verificado_at' => now()]);

    $resumen = ExpedienteService::resumenPagoAdmision($aspirante->user);
    expect($aspirante->tienePagoAdmisionPorAplicar())->toBeTrue()
        ->and($resumen['monto'])->toBe(70.0)
        ->and($resumen['fecha_pago'])->toBe('01/10/2026')
        ->and($resumen['aplicado'])->toBeFalse();
});

// ── Registrar el pago de los verificados sin monto ───────────────────────────

test('registrar: monto sugerido, método y fecha vacíos y obligatorios; queda confirmado y auditado', function () {
    $this->actingAs(usuarioConRol('Secretaria'));
    $aspirante = aspiranteConPago(['pago_monto' => 70]);

    $componente = Livewire::test(GestionAspirantes::class)
        ->call('verDetalle', $aspirante->id)
        ->assertSee('Verificado sin monto registrado')
        ->call('abrirPago')
        ->assertSet('showPago', true)
        ->assertSet('pagoMonto', '70.00')
        ->assertSet('pagoMetodo', '')
        ->assertSet('pagoFecha', '')
        ->assertSee('sugerido, compáralo con el comprobante')
        ->call('guardarPago')
        ->assertHasErrors(['pagoMetodo', 'pagoFecha']);

    $componente->set('pagoMetodo', 'Transferencia')
        ->set('pagoFecha', '2026-09-15')
        ->call('guardarPago')
        ->assertHasNoErrors()
        ->assertSet('showPago', false);

    $aspirante->refresh();
    expect($aspirante->pagoConfirmado())->toBeTrue()
        ->and((float) $aspirante->pago_monto)->toBe(70.0)
        ->and($aspirante->pago_fecha->format('Y-m-d'))->toBe('2026-09-15')
        ->and($aspirante->tienePagoAdmisionPorAplicar())->toBeTrue()
        ->and(AuditoriaGeneral::where('auditable_id', $aspirante->id)->where('valores_despues->accion', 'Pago de admisión registrado')->exists())->toBeTrue();
});

test('sin comprobante: se sube y verifica de una vez', function () {
    $this->actingAs(usuarioConRol('Secretaria'));
    $aspirante = aspiranteConPago(['estado' => 'verificacion', 'pago_estado' => 'pendiente', 'pago_comprobante_path' => null]);

    $componente = Livewire::test(GestionAspirantes::class)
        ->call('verDetalle', $aspirante->id)
        ->assertSee('Subir comprobante y verificar')
        ->call('abrirPago')
        ->set('pagoMonto', '70')->set('pagoMetodo', 'Efectivo')->set('pagoFecha', now()->format('Y-m-d'))
        ->call('guardarPago')
        ->assertHasErrors(['pagoComprobante' => 'required']);

    $componente->set('pagoComprobante', UploadedFile::fake()->create('recibo.pdf', 100, 'application/pdf'))
        ->call('guardarPago')
        ->assertHasNoErrors();

    $aspirante->refresh();
    expect($aspirante->pagoConfirmado())->toBeTrue();
    Storage::disk('public')->assertExists($aspirante->pago_comprobante_path);
});

test('fecha del pago: flexible hacia atrás, nunca futura ni un error evidente; monto mayor a cero', function () {
    $this->actingAs(usuarioConRol('Secretaria'));
    $aspirante = aspiranteConPago();

    $componente = Livewire::test(GestionAspirantes::class)
        ->call('verDetalle', $aspirante->id)
        ->call('abrirPago')
        ->set('pagoMetodo', 'Efectivo')
        ->set('pagoMonto', '0')
        ->set('pagoFecha', now()->addDay()->format('Y-m-d'))
        ->call('guardarPago')
        ->assertHasErrors(['pagoMonto', 'pagoFecha']);

    // Error de tipeo evidente (más de un año antes de registrarse / abrirse la cohorte)
    $componente->set('pagoMonto', '70')->set('pagoFecha', '2002-05-10')
        ->call('guardarPago')
        ->assertHasErrors(['pagoFecha' => 'after_or_equal']);

    // El servicio también lo exige
    expect(fn() => PagoAdmisionService::registrar($aspirante, 70, 'Efectivo', '2024-12-31'))->toThrow(\DomainException::class);
    expect($aspirante->fresh()->pagoConfirmado())->toBeFalse();

    // Flexible: un pago anterior a su registro en el sistema (registrado el 01/01/2026) sí se acepta
    $componente->set('pagoFecha', '2025-11-15')->call('guardarPago')->assertHasNoErrors();
    expect($aspirante->fresh()->pago_fecha->format('Y-m-d'))->toBe('2025-11-15');
});

// ── Rechazar un pago: motivo obligatorio y auditoría ─────────────────────────

test('rechazar un pago confirmado exige motivo y queda en auditoría con el monto que tenía', function () {
    $this->actingAs(usuarioConRol('Secretaria'));
    $aspirante = aspiranteConPago(['estado' => 'verificacion', 'pago_monto' => 70, 'pago_metodo' => 'Efectivo', 'pago_fecha' => '2026-09-01', 'pago_verificado_at' => now()]);

    $componente = Livewire::test(GestionAspirantes::class)->call('verDetalle', $aspirante->id);

    // Sin motivo (llamada a mano): no hace nada
    $componente->call('actualizarDocumento', 'pago', 'rechazado', '   ');
    expect($aspirante->fresh()->pagoConfirmado())->toBeTrue();

    // Volver a "pendiente" a mano tampoco (solo se rechaza o se verifica con el formulario)
    $componente->call('actualizarDocumento', 'pago', 'pendiente');
    expect($aspirante->fresh()->pago_estado)->toBe('verificado');

    $componente->call('actualizarDocumento', 'pago', 'rechazado', 'El comprobante no es legible');
    $aspirante->refresh();
    $auditoria = AuditoriaGeneral::where('auditable_id', $aspirante->id)->where('valores_despues->accion', 'Pago de admisión rechazado')->first();

    expect($aspirante->pago_estado)->toBe('rechazado')
        ->and($aspirante->pago_observacion)->toBe('El comprobante no es legible')
        ->and($aspirante->pagoConfirmado())->toBeFalse()
        ->and($auditoria)->not->toBeNull()
        ->and($auditoria->valores_antes)->toBe(['pago_estado' => 'verificado', 'pago_observacion' => null])
        ->and($auditoria->valores_despues['motivo'])->toBe('El comprobante no es legible');
    // El monto que tenía se conserva en el registro (y la auditoría de cuando se confirmó lo muestra)
    expect((float) $aspirante->pago_monto)->toBe(70.0);
});

test('rechazar cualquier documento exige motivo también en el servidor', function () {
    $this->actingAs(usuarioConRol('Secretaria'));
    $aspirante = aspiranteConPago(['estado' => 'verificacion', 'cedula_estado' => 'pendiente', 'cedula_path' => 'x.pdf']);

    Livewire::test(GestionAspirantes::class)
        ->call('verDetalle', $aspirante->id)
        ->call('actualizarDocumento', 'cedula', 'rechazado', '');
    expect($aspirante->fresh()->cedula_estado)->toBe('pendiente');
});

// ── Auditoría solo con lo que cambió / sin archivos sueltos ──────────────────

test('la auditoría guarda solo los campos que cambiaron', function () {
    $this->actingAs(usuarioConRol('Secretaria'));
    $aspirante = aspiranteConPago(['pago_monto' => 50, 'pago_metodo' => 'Efectivo', 'pago_fecha' => '2026-09-01', 'pago_verificado_at' => now()->subDay()]);

    PagoAdmisionService::registrar($aspirante, 70, 'Efectivo', '2026-09-01', null, 'Monto mal digitado');

    $auditoria = AuditoriaGeneral::where('auditable_id', $aspirante->id)->latest('id')->first();
    expect(array_keys($auditoria->valores_antes))->toEqualCanonicalizing(['pago_monto', 'pago_verificado_at'])
        ->and($auditoria->valores_antes['pago_monto'])->toBe('50.00')
        ->and($auditoria->valores_despues['pago_monto'])->toBe('70.00')
        ->and($auditoria->valores_despues)->not->toHaveKeys(['pago_estado', 'pago_metodo', 'pago_fecha', 'pago_comprobante_path']);
});

test('si el guardado falla, el comprobante subido no queda suelto en el disco', function () {
    $this->actingAs(usuarioConRol('Secretaria'));
    $aspirante = aspiranteConPago(['pago_comprobante_path' => null, 'estado' => 'verificacion', 'pago_estado' => 'pendiente']);

    // Simula una falla justo después de guardar el archivo (al escribir la auditoría)
    AuditoriaGeneral::creating(fn() => throw new \RuntimeException('falla simulada'));

    expect(fn() => PagoAdmisionService::registrar($aspirante, 70, 'Efectivo', '2026-09-01',
        UploadedFile::fake()->create('recibo.pdf', 100, 'application/pdf')))->toThrow(\RuntimeException::class);

    expect(Storage::disk('public')->allFiles("aspirantes/{$aspirante->id}"))->toBe([])
        ->and($aspirante->fresh()->pago_comprobante_path)->toBeNull()
        ->and($aspirante->fresh()->pagoConfirmado())->toBeFalse();
});

// ── Corregir un pago confirmado (lo decide el permiso) ───────────────────────

test('corregir un pago confirmado: cualquiera con permiso de aspirantes, con motivo', function () {
    $aspirante = aspiranteConPago(['pago_monto' => 50, 'pago_metodo' => 'Efectivo', 'pago_fecha' => '2026-09-01', 'pago_verificado_at' => now()]);

    // Con el permiso (rol Secretaria): ve "Corregir pago", se cargan los datos guardados y exige motivo
    $this->actingAs(usuarioConRol('Secretaria'));
    $componente = Livewire::test(GestionAspirantes::class)
        ->call('verDetalle', $aspirante->id)
        ->assertSee('Corregir pago')
        ->call('abrirPago')
        ->assertSet('pagoMetodo', 'Efectivo')
        ->assertSet('pagoFecha', '2026-09-01')
        ->set('pagoMonto', '70')
        ->call('guardarPago')
        ->assertHasErrors(['pagoMotivo' => 'required']);

    $componente->set('pagoMotivo', 'El monto no coincide con el comprobante')
        ->call('guardarPago')
        ->assertHasNoErrors();

    $auditoria = AuditoriaGeneral::where('auditable_id', $aspirante->id)->where('valores_despues->accion', 'Pago de admisión corregido')->first();
    expect((float) $aspirante->fresh()->pago_monto)->toBe(70.0)
        ->and($auditoria)->not->toBeNull()
        ->and($auditoria->evento)->toBe('updated')
        ->and((float) $auditoria->valores_antes['pago_monto'])->toBe(50.0)
        ->and($auditoria->valores_despues['motivo'])->toBe('El monto no coincide con el comprobante');
    // Sin el permiso la pantalla de Aspirantes no abre (lo cubren las pruebas de seguridad de rutas)
});

// ── Lo que no se puede tocar ─────────────────────────────────────────────────

test('ya aplicado a una matrícula o aspirante fuera de Verificación/Aprobado: no se edita', function () {
    $this->actingAs(usuarioConRol('Secretaria'));

    $est      = User::factory()->create();
    $carrera  = DB::table('carreras')->insertGetId(['name' => 'Carrera ' . uniqid(), 'code' => uniqid(), 'created_at' => now(), 'updated_at' => now()]);
    $periodo  = DB::table('periodos')->insertGetId(['code' => 'P' . uniqid(), 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30',
        'fecha_limite_matricula' => '2026-01-10', 'fecha_limite_pago' => '2026-01-20', 'created_at' => now(), 'updated_at' => now()]);
    $matricula = DB::table('matriculas')->insertGetId(['code' => 'ISTC-MATRICULA-' . rand(1000, 9999), 'fecha_matricula' => now(), 'tipo' => 'Nueva',
        'estado' => 'Habilitada', 'periodo_id' => $periodo, 'carrera_id' => $carrera, 'user_id' => $est->id, 'created_at' => now(), 'updated_at' => now()]);
    $obligacion = DB::table('obligaciones_financieras')->insertGetId(['user_id' => $est->id, 'periodo_id' => $periodo, 'matricula_id' => $matricula,
        'tipo' => 'MATRICULA', 'monto_original' => 70, 'descuento' => 0, 'monto_final' => 70, 'estado' => 'Pagado',
        'fecha_vencimiento' => now(), 'created_at' => now(), 'updated_at' => now()]);
    $pago = DB::table('pagos')->insertGetId(['numero_comprobante' => 'C-' . uniqid(), 'obligacion_id' => $obligacion, 'monto' => 70, 'metodo_pago' => 'Efectivo',
        'estado' => 'Aprobado', 'fecha_pago' => now(), 'created_at' => now(), 'updated_at' => now()]);

    $aplicado = aspiranteConPago(['user_id' => $est->id, 'pago_monto' => 70, 'pago_metodo' => 'Efectivo', 'pago_verificado_at' => now(), 'pago_aplicado_id' => $pago]);
    Livewire::test(GestionAspirantes::class)
        ->call('verDetalle', $aplicado->id)
        ->assertSee('Aplicado a ISTC-MATRICULA-')
        ->assertSee('anula ese pago en Pagos')
        ->assertDontSee('Corregir pago');
    expect(PagoAdmisionService::motivoNoEditable($aplicado))->toContain('ya se aplicó');
    expect(fn() => PagoAdmisionService::registrar($aplicado, 80, 'Efectivo', '2026-10-01', null, 'cambio de monto'))
        ->toThrow(\DomainException::class);
    expect((float) $aplicado->fresh()->pago_monto)->toBe(70.0);

    $enProceso = aspiranteConPago(['estado' => 'proceso', 'pago_estado' => 'pendiente']);
    expect(PagoAdmisionService::motivoNoEditable($enProceso))->not->toBeNull();
});

test('verificar por primera vez un comprobante pendiente', function () {
    $this->actingAs(usuarioConRol('Secretaria'));
    $aspirante = aspiranteConPago(['estado' => 'verificacion', 'pago_estado' => 'pendiente', 'pago_monto' => 65]);

    Livewire::test(GestionAspirantes::class)
        ->call('verDetalle', $aspirante->id)
        ->assertSee('declarado por el aspirante')
        ->call('abrirPago')
        ->assertSet('pagoMonto', '65.00')
        ->set('pagoMetodo', 'Deposito')
        ->set('pagoFecha', '2026-03-02')
        ->call('guardarPago')
        ->assertHasNoErrors();

    $aspirante->refresh();
    expect($aspirante->pago_estado)->toBe('verificado')
        ->and($aspirante->pago_metodo)->toBe('Deposito')
        ->and($aspirante->pagoConfirmado())->toBeTrue();
});

// ── Servidor: documentos solo en Verificación ────────────────────────────────

test('el servidor no deja cambiar documentos de un aspirante que no está en Verificación', function () {
    $this->actingAs(usuarioConRol('Secretaria'));
    $aspirante = aspiranteConPago(['pago_monto' => 70, 'pago_metodo' => 'Efectivo', 'pago_verificado_at' => now(), 'cedula_estado' => 'aprobado']);

    // Llamadas hechas "a mano" (la pantalla no muestra esos botones en un aprobado)
    Livewire::test(GestionAspirantes::class)
        ->call('verDetalle', $aspirante->id)
        ->call('actualizarDocumento', 'pago', 'pendiente')
        ->call('actualizarDocumento', 'cedula', 'rechazado', 'no se ve');

    $aspirante->refresh();
    expect($aspirante->pago_estado)->toBe('verificado')
        ->and($aspirante->cedula_estado)->toBe('aprobado')
        ->and($aspirante->pagoConfirmado())->toBeTrue();
});

// ── Aprobar exige el monto ───────────────────────────────────────────────────

test('no se aprueba con el pago verificado sin monto', function () {
    $aspirante = aspiranteConPago(['estado' => 'verificacion']);
    expect($aspirante->razonesNoPuedeAprobar())->toContain('Registra el monto del pago de matrícula');

    $aspirante->update(['pago_monto' => 70, 'pago_metodo' => 'Efectivo', 'pago_verificado_at' => now()]);
    expect($aspirante->fresh()->razonesNoPuedeAprobar())->not->toContain('Registra el monto del pago de matrícula');
});

// ── Matrícula sin descuento manual ───────────────────────────────────────────

test('el asistente de matrícula ya no tiene descuento manual que se pueda enviar a mano', function () {
    expect(property_exists(Matriculacion::class, 'descuento'))->toBeFalse();
});
