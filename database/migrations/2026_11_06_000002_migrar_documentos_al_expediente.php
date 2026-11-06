<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pasa al expediente los documentos que ya existen. Solo AGREGA filas: no modifica ni borra
 * las columnas de origen ni mueve archivos (cada fila apunta al mismo archivo, disco público).
 *
 *  - Aspirantes que ya tienen matrícula: sus documentos de admisión, con su estado y observación.
 *  - users.certificado_discapacidad_path: certificado CONADIS.
 *  - documents (docentes): hoja de vida, SENESCYT, cédula.
 *  - asignacion_docentes.file_contrato: contrato firmado ligado a su asignación.
 *
 * Autocontenida a propósito (no usa modelos ni servicios de la app), para que siga corriendo
 * igual aunque el código cambie después.
 */
return new class extends Migration
{
    private const FUENTES = ['aspirantes', 'users', 'documents', 'asignacion_docentes'];

    public function up(): void
    {
        $filas = [];
        $ahora = now();

        $fila = fn(array $d) => $d + [
            'descripcion' => null, 'asignacion_docente_id' => null, 'variante' => null, 'disco' => 'public',
            'observacion' => null, 'vigente' => true, 'subido_por' => null, 'revisado_por' => null,
            'revisado_at' => null, 'created_at' => $ahora, 'updated_at' => $ahora,
        ];
        $estado = fn(?string $e) => match ($e) {
            'aprobado', 'verificado' => 'aprobado',
            'rechazado'              => 'rechazado',
            default                  => 'pendiente',
        };

        // ── 1. Documentos de admisión de quienes ya tienen matrícula (su postulación más reciente)
        $docsAdmision = [
            'cedula'          => ['cedula_path', 'cedula_estado', 'cedula_observacion'],
            'bachiller'       => ['bachiller_path', 'bachiller_estado', 'bachiller_observacion'],
            'habilitante'     => ['habilitante_path', 'habilitante_estado', 'habilitante_observacion'],
            'pago_admision'   => ['pago_comprobante_path', 'pago_estado', 'pago_observacion'],
            'hoja_vida'       => ['hoja_vida_path', 'hoja_vida_estado', 'hoja_vida_observacion'],
            'cert_laborales'  => ['cert_laborales_path', 'cert_laborales_estado', 'cert_laborales_observacion'],
            'cert_cursos'     => ['cert_cursos_path', 'cert_cursos_estado', 'cert_cursos_observacion'],
            'mecanizado_iess' => ['mecanizado_iess_path', null, null],
        ];
        $ultimas = DB::table('aspirantes')->whereNull('deleted_at')
            ->whereExists(fn($q) => $q->selectRaw(1)->from('matriculas')->whereColumn('matriculas.user_id', 'aspirantes.user_id'))
            ->whereExists(fn($q) => $q->selectRaw(1)->from('users')->whereColumn('users.id', 'aspirantes.user_id'))
            ->selectRaw('MAX(id) as id')->groupBy('user_id')->pluck('id');

        foreach ($ultimas->chunk(200) as $ids) {
            foreach (DB::table('aspirantes')->whereIn('id', $ids)->get() as $asp) {
                foreach ($docsAdmision as $tipo => [$colPath, $colEstado, $colObs]) {
                    if (empty($asp->$colPath)) continue;
                    $est = $estado($colEstado ? $asp->$colEstado : null);
                    $filas[] = $fila([
                        'user_id' => $asp->user_id, 'tipo' => $tipo, 'archivo_path' => $asp->$colPath,
                        'nombre_original' => basename($asp->$colPath), 'estado' => $est,
                        'observacion' => $colObs ? $asp->$colObs : null, 'origen' => 'admision', 'fuente' => 'aspirantes',
                        'subido_at' => $asp->updated_at,
                        'revisado_at' => $est !== 'pendiente' ? $asp->updated_at : null,
                    ]);
                }
            }
        }

        // ── 2. Certificado CONADIS guardado en el usuario
        if (Schema::hasColumn('users', 'certificado_discapacidad_path')) {
            foreach (DB::table('users')->whereNotNull('certificado_discapacidad_path')->where('certificado_discapacidad_path', '!=', '')
                         ->get(['id', 'certificado_discapacidad_path', 'updated_at']) as $u) {
                $filas[] = $fila([
                    'user_id' => $u->id, 'tipo' => 'conadis', 'archivo_path' => $u->certificado_discapacidad_path,
                    'nombre_original' => basename($u->certificado_discapacidad_path), 'estado' => 'pendiente',
                    'origen' => 'admision', 'fuente' => 'users', 'subido_at' => $u->updated_at,
                ]);
            }
        }

        // ── 3. Documentos personales de docentes (tabla documents): subidos por administración
        if (Schema::hasTable('documents')) {
            $mapa = array_filter([
                'file_curriculum' => 'hoja_vida',
                'file_senescyt'   => 'senescyt',
                'file_cedula'     => 'cedula',
                'file_otro'       => 'otro',
                'file_contrato'   => 'contrato',
            ], fn($col) => Schema::hasColumn('documents', $col), ARRAY_FILTER_USE_KEY);

            $docs = DB::table('documents')->whereNotNull('user_id')
                ->whereExists(fn($q) => $q->selectRaw(1)->from('users')->whereColumn('users.id', 'documents.user_id'))
                ->get();
            foreach ($docs as $d) {
                foreach ($mapa as $col => $tipo) {
                    if (empty($d->$col)) continue;
                    $filas[] = $fila([
                        'user_id' => $d->user_id, 'tipo' => $tipo, 'archivo_path' => $d->$col,
                        'nombre_original' => basename($d->$col), 'estado' => 'aprobado',
                        'variante' => $tipo === 'contrato' ? 'firmado' : null,
                        'origen' => 'secretaria', 'fuente' => 'documents',
                        'subido_at' => $d->updated_at, 'revisado_at' => $d->updated_at,
                    ]);
                }
            }
        }

        // ── 4. Contratos por asignación
        if (Schema::hasColumn('asignacion_docentes', 'file_contrato')) {
            $asignaciones = DB::table('asignacion_docentes')->whereNotNull('file_contrato')->where('file_contrato', '!=', '')
                ->whereExists(fn($q) => $q->selectRaw(1)->from('users')->whereColumn('users.id', 'asignacion_docentes.docente_id'))
                ->get(['id', 'docente_id', 'file_contrato', 'updated_at']);
            foreach ($asignaciones as $a) {
                $filas[] = $fila([
                    'user_id' => $a->docente_id, 'tipo' => 'contrato', 'asignacion_docente_id' => $a->id, 'variante' => 'firmado',
                    'archivo_path' => $a->file_contrato, 'nombre_original' => basename($a->file_contrato), 'estado' => 'aprobado',
                    'origen' => 'secretaria', 'fuente' => 'asignacion_docentes',
                    'subido_at' => $a->updated_at, 'revisado_at' => $a->updated_at,
                ]);
            }
        }

        // Si una persona tenía el mismo documento en dos lugares (p. ej. cédula de admisión y de
        // documents), queda vigente el más reciente; el otro, en el historial
        $grupos = collect($filas)->groupBy(fn($f) => $f['user_id'] . '|' . $f['tipo'] . '|' . ($f['asignacion_docente_id'] ?? ''));
        $filas = $grupos->flatMap(function ($g) {
            $g = $g->sortBy(fn($f) => (string) $f['subido_at'])->values();
            return $g->map(fn($f, $i) => ['vigente' => $i === $g->count() - 1] + $f);
        })->all();

        foreach (array_chunk($filas, 500) as $lote) {
            DB::table('expediente_documentos')->insert($lote);
        }
    }

    public function down(): void
    {
        DB::table('expediente_documentos')->whereIn('fuente', self::FUENTES)->delete();
    }
};
