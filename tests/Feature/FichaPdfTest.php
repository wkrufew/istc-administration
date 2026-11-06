<?php

/*
 * PDF de la ficha (estudiante, aspirante, docente):
 *  - cada ruta exige el permiso de su sección;
 *  - el resumen financiero solo con ?financiero=1 y ver_obligaciones_financieras;
 *  - toda descarga queda en auditoría (evento "exportado");
 *  - la ficha cubre todos los campos de FichaPersona y omite lo que no aplica.
 */

use App\Models\Aspirante;
use App\Models\AuditoriaGeneral;
use App\Models\User;
use App\Services\FichaPdfService;
use App\Support\FichaPersona;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function rolPdf(string $nombre, array $permisos): void
{
    foreach ($permisos as $p) Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => $nombre, 'guard_name' => 'web'])->syncPermissions($permisos);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
}

function personaPdf(string $rol, array $datos = []): User
{
    $u = User::factory()->create($datos + ['first_name' => 'Ana', 'last_name' => 'Pérez', 'is_active' => true,
        'cedula' => (string) fake()->unique()->numerify('09########')]);
    $u->assignRole($rol);
    return $u->fresh();
}

/** Bloque de la ficha por título (o null). */
function bloquePdf(array $ficha, string $titulo): ?array
{
    return collect($ficha['bloques'])->firstWhere('titulo', $titulo);
}

beforeEach(function () {
    rolPdf('Estudiante', ['acceso_estudiantil']);
    rolPdf('Docente', ['acceso_docencia']);
    rolPdf('Secretaria', ['acceso_administrativo', 'gestionar_estudiantes', 'gestionar_docentes', 'gestionar_aspirantes']);
    rolPdf('Contadora', ['acceso_administrativo', 'gestionar_estudiantes', 'gestionar_aspirantes', 'ver_obligaciones_financieras']);
    rolPdf('Procuraduria', ['acceso_administrativo']);
});

test('la ficha incluye todos los campos de la ficha del estudiante (ninguno se queda fuera)', function () {
    $enPdf = collect(FichaPdfService::GRUPOS_PERSONA)->flatten()->all();
    $enFicha = collect(FichaPersona::SECCIONES)->flatten()->all();

    expect(array_diff($enFicha, $enPdf))->toBe([]);
});

test('estudiante: el PDF se genera, se abre en el navegador y queda en auditoría', function () {
    $estudiante = personaPdf('Estudiante');
    $this->actingAs(personaPdf('Secretaria'));

    $respuesta = $this->get(route('administracion.administrativa.estudiantes.ficha-pdf', $estudiante));

    $respuesta->assertOk()->assertHeader('Content-Type', 'application/pdf');
    expect($respuesta->headers->get('Content-Disposition'))->toStartWith('inline')
        ->and(substr($respuesta->getContent(), 0, 4))->toBe('%PDF');

    $auditoria = AuditoriaGeneral::where('evento', 'exportado')->where('auditable_id', $estudiante->id)->first();
    expect($auditoria)->not->toBeNull()
        ->and($auditoria->valores_despues['accion'])->toBe('Descargó la ficha del estudiante en PDF')
        ->and($auditoria->valores_despues['resumen_financiero'])->toBe('No');
});

test('resumen financiero: solo con el permiso de ver obligaciones, aunque se pida a mano', function () {
    $estudiante = personaPdf('Estudiante');

    // Sin el permiso: se ignora ?financiero=1
    $this->actingAs(personaPdf('Secretaria'));
    $this->get(route('administracion.administrativa.estudiantes.ficha-pdf', $estudiante) . '?financiero=1')->assertOk();
    expect(AuditoriaGeneral::where('evento', 'exportado')->latest('id')->first()->valores_despues['resumen_financiero'])->toBe('No');

    // Con el permiso
    $this->actingAs(personaPdf('Contadora'));
    $this->get(route('administracion.administrativa.estudiantes.ficha-pdf', $estudiante) . '?financiero=1')->assertOk();
    expect(AuditoriaGeneral::where('evento', 'exportado')->latest('id')->first()->valores_despues['resumen_financiero'])->toBe('Sí');

    // Desmarcado por defecto: sin el parámetro no va
    expect(bloquePdf(FichaPdfService::estudiante($estudiante), 'Totales y beneficios'))->toBeNull()
        ->and(bloquePdf(FichaPdfService::estudiante($estudiante, true), 'Totales y beneficios'))->not->toBeNull();
});

test('sin el permiso de la sección no se descarga; y cada ruta solo acepta a su tipo de persona', function () {
    $estudiante = personaPdf('Estudiante');
    $docente    = personaPdf('Docente');

    $this->actingAs(personaPdf('Procuraduria'));
    $this->get(route('administracion.administrativa.estudiantes.ficha-pdf', $estudiante))->assertRedirect()->assertSessionHas('swal');
    $this->get(route('administracion.administrativa.docentes.ficha-pdf', $docente))->assertRedirect()->assertSessionHas('swal');
    expect(AuditoriaGeneral::where('evento', 'exportado')->count())->toBe(0);

    $this->actingAs(personaPdf('Secretaria'));
    $this->get(route('administracion.administrativa.estudiantes.ficha-pdf', $docente))->assertNotFound();
    $this->get(route('administracion.administrativa.docentes.ficha-pdf', $estudiante))->assertNotFound();
    $this->get(route('administracion.administrativa.docentes.ficha-pdf', $docente))->assertOk()->assertHeader('Content-Type', 'application/pdf');
});

test('aspirante: exige gestionar aspirantes y trae postulación, documentos y pago de admisión', function () {
    $cohorte = DB::table('cohortes')->insertGetId(['nombre' => 'Cohorte Prueba', 'created_at' => now(), 'updated_at' => now()]);
    $aspirante = Aspirante::create(['user_id' => personaPdf('Estudiante')->id, 'cohorte_id' => $cohorte, 'estado' => 'verificacion',
        'pago_estado' => 'verificado', 'pago_comprobante_path' => 'x.pdf', 'pago_monto' => 70, 'pago_metodo' => 'Efectivo',
        'pago_fecha' => '2026-09-01', 'pago_verificado_at' => now()]);

    $this->actingAs(personaPdf('Procuraduria'));
    $this->get(route('administracion.administrativa.aspirantes.ficha-pdf', $aspirante))->assertRedirect();

    $this->actingAs(personaPdf('Contadora'));
    $this->get(route('administracion.administrativa.aspirantes.ficha-pdf', $aspirante) . '?financiero=1')->assertOk();

    $ficha = FichaPdfService::aspirante($aspirante, true);
    $pago  = collect(bloquePdf($ficha, 'Pago de admisión')['campos'])->pluck('valor', 'etiqueta');
    expect(bloquePdf($ficha, 'Postulación'))->not->toBeNull()
        ->and(bloquePdf($ficha, 'Documentos de admisión'))->not->toBeNull()
        ->and($pago['Monto confirmado'])->toBe('$70.00')
        ->and($pago['Fecha del pago'])->toBe('01/09/2026')
        ->and(AuditoriaGeneral::where('evento', 'exportado')->where('auditable_type', Aspirante::class)->exists())->toBeTrue();
});

test('omite lo que no aplica: detalle de discapacidad solo si tiene; facturación a terceros solo si no factura a su nombre', function () {
    $sin = personaPdf('Estudiante', ['tiene_discapacidad' => false, 'is_facturador' => true]);
    $con = personaPdf('Estudiante', ['tiene_discapacidad' => true, 'tipo_discapacidad' => 'Visual', 'porcentaje_discapacidad' => 40,
        'is_facturador' => false, 'fact_nombre' => 'Papá Pérez']);
    $this->actingAs(personaPdf('Secretaria'));

    $etiquetas = fn(User $u, string $bloque) => collect(bloquePdf(FichaPdfService::estudiante($u), $bloque)['campos'])->pluck('valor', 'etiqueta');

    expect($etiquetas($sin, 'Salud y discapacidad')->keys()->all())->not->toContain('Tipo de discapacidad')
        ->and($etiquetas($sin, 'Facturación')->keys()->all())->toBe(['Factura a su nombre'])
        ->and($etiquetas($con, 'Salud y discapacidad')['Tipo de discapacidad'])->toBe('Visual')
        ->and($etiquetas($con, 'Salud y discapacidad')['Porcentaje de discapacidad'])->toBe('40%')
        ->and($etiquetas($con, 'Facturación')['Facturar a (nombre)'])->toBe('Papá Pérez');
});

test('docente: datos, carga docente y contratos', function () {
    $docente = personaPdf('Docente', ['phone' => '0999999999']);
    $this->actingAs(personaPdf('Secretaria'));

    $ficha = FichaPdfService::docente($docente);
    expect($ficha['titulo'])->toBe('Ficha del docente')
        ->and(bloquePdf($ficha, 'Asignaciones y contratos'))->not->toBeNull()
        ->and(bloquePdf($ficha, 'Documentos del expediente'))->not->toBeNull()
        ->and(bloquePdf($ficha, 'Totales y beneficios'))->toBeNull(); // el docente no tiene resumen financiero
});

test('los botones aparecen en las tres pantallas, y la casilla financiera solo con el permiso', function () {
    $estudiante = personaPdf('Estudiante');

    $this->actingAs(personaPdf('Secretaria'));
    $sinPermiso = view('components.boton-ficha-pdf', ['url' => '/x', 'financiero' => true])->render();
    expect($sinPermiso)->toContain('PDF de la ficha')->not->toContain('Incluir resumen financiero');

    $this->actingAs(personaPdf('Contadora'));
    $conPermiso = view('components.boton-ficha-pdf', ['url' => '/x', 'financiero' => true])->render();
    expect($conPermiso)->toContain('Incluir resumen financiero')->toContain("financiero: false");
});
