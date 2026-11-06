<?php

namespace App\Services;

use App\Models\Aspirante;
use App\Models\ExpedienteDocumento;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Expediente único de documentos (estudiantes y docentes).
 *
 *  - Cada archivo es una fila; al reemplazarlo, la versión anterior queda en el historial
 *    (vigente = false). Nunca se borran archivos desde aquí.
 *  - Lo nuevo se guarda en el disco privado ('local') y se ve solo por la ruta expediente.archivo,
 *    que controla quién puede abrirlo. Los archivos anteriores siguen en el disco público.
 *  - Lo que sube secretaría queda aprobado; lo que sube la persona queda pendiente de revisión.
 */
class ExpedienteService
{
    public const DISCO = 'local';

    /** Documentos de admisión que pasan al expediente en la primera matrícula. */
    private const DOCS_ADMISION = [
        'cedula'          => ['cedula_path', 'cedula_estado', 'cedula_observacion'],
        'bachiller'       => ['bachiller_path', 'bachiller_estado', 'bachiller_observacion'],
        'habilitante'     => ['habilitante_path', 'habilitante_estado', 'habilitante_observacion'],
        'pago_admision'   => ['pago_comprobante_path', 'pago_estado', 'pago_observacion'],
        'hoja_vida'       => ['hoja_vida_path', 'hoja_vida_estado', 'hoja_vida_observacion'],
        'cert_laborales'  => ['cert_laborales_path', 'cert_laborales_estado', 'cert_laborales_observacion'],
        'cert_cursos'     => ['cert_cursos_path', 'cert_cursos_estado', 'cert_cursos_observacion'],
        'mecanizado_iess' => ['mecanizado_iess_path', null, null],
    ];

    /**
     * ¿Es estudiante de validación de conocimientos? Por su postulación en admisión o, si entró
     * directo (estudiantes anteriores al portal), por tener una matrícula de tipo Validación.
     */
    public static function esValidacion(User $user): bool
    {
        return $user->aspirante()->where('tipo_proceso', 'validacion_conocimientos')->exists()
            || $user->matriculas()->where('tipo', \App\Models\Matricula::TIPO_VALIDACION)->exists();
    }

    /**
     * Documentos que corresponden a la persona (filas del panel; contratos y "otro" van aparte):
     *  - los de validación (hoja de vida, certificados, mecanizado) solo si es de validación;
     *  - el comprobante de pago de admisión solo si vino por admisión;
     *  - el CONADIS solo si tiene marcada la discapacidad.
     * Un documento que ya tiene cargado se muestra siempre, aunque no le corresponda.
     *
     * @return array<string, string> tipo => nombre
     */
    public static function tiposVisibles(User $user, string $perfil): array
    {
        $existentes = ExpedienteDocumento::where('user_id', $user->id)->whereNull('asignacion_docente_id')
            ->distinct()->pluck('tipo')->all();
        $esValidacion    = $perfil === 'estudiante' && self::esValidacion($user);
        $vinoPorAdmision = $user->aspirante()->exists();

        $tipos = [];
        foreach (ExpedienteDocumento::TIPOS as $tipo => $def) {
            if (! in_array($perfil, $def['perfiles'], true) || in_array($tipo, ['contrato', 'otro'], true)) continue;

            $corresponde = match (true) {
                in_array($tipo, $existentes, true) => true,
                $perfil === 'estudiante' && in_array($tipo, ExpedienteDocumento::SOLO_VALIDACION, true) => $esValidacion,
                $tipo === 'pago_admision' => $vinoPorAdmision,
                $tipo === 'conadis'       => (bool) $user->tiene_discapacidad,
                default                   => true,
            };
            if ($corresponde) $tipos[$tipo] = $def['nombre'];
        }

        return $tipos;
    }

    /**
     * Lo que se registró del pago de admisión (secretaría confirma monto y método al verificarlo)
     * y a qué matrícula se aplicó. null si no vino por admisión o el pago no se verificó con monto.
     */
    public static function resumenPagoAdmision(User $user): ?array
    {
        // Misma regla que la matrícula: solo cuenta lo que secretaría confirmó con monto y método
        return $user->aspirante()->first()?->resumenPago();
    }

    /** Versión vigente de un documento (de una asignación y variante, en el caso de contratos). */
    public static function vigente(int $userId, string $tipo, ?int $asignacionId = null, ?string $variante = null): ?ExpedienteDocumento
    {
        return ExpedienteDocumento::vigentes()
            ->where('user_id', $userId)
            ->where('tipo', $tipo)
            ->where('asignacion_docente_id', $asignacionId)
            ->when($variante !== null, fn($q) => $q->where('variante', $variante))
            ->latest('id')
            ->first();
    }

    /**
     * Sube una versión nueva; la anterior del mismo documento pasa al historial.
     *
     * @param string $origen secretaria | estudiante | docente
     */
    public static function subir(User $user, string $tipo, UploadedFile $archivo, string $origen,
                                 ?int $asignacionId = null, ?string $variante = null, ?string $descripcion = null): ExpedienteDocumento
    {
        if (! array_key_exists($tipo, ExpedienteDocumento::TIPOS)) {
            throw new \InvalidArgumentException("Tipo de documento desconocido: {$tipo}");
        }

        $nombre = $tipo . '-' . Str::random(16) . '.' . strtolower($archivo->getClientOriginalExtension() ?: 'pdf');
        $path   = $archivo->storeAs("expediente/{$user->id}/{$tipo}", $nombre, self::DISCO);

        try {
            return DB::transaction(function () use ($user, $tipo, $archivo, $origen, $asignacionId, $variante, $descripcion, $path) {
                // "Otro" admite varios documentos a la vez; los demás tipos tienen una sola versión vigente
                if ($tipo !== 'otro') {
                    ExpedienteDocumento::vigentes()
                        ->where('user_id', $user->id)->where('tipo', $tipo)
                        ->where('asignacion_docente_id', $asignacionId)
                        ->where('variante', $variante)
                        ->lockForUpdate()
                        ->update(['vigente' => false]);
                }

                $porSecretaria = $origen === 'secretaria';

                $doc = ExpedienteDocumento::create([
                    'user_id'               => $user->id,
                    'tipo'                  => $tipo,
                    'descripcion'           => $descripcion,
                    'asignacion_docente_id' => $asignacionId,
                    'variante'              => $variante,
                    'archivo_path'          => $path,
                    'disco'                 => self::DISCO,
                    'nombre_original'       => $archivo->getClientOriginalName(),
                    'estado'                => $porSecretaria ? 'aprobado' : 'pendiente',
                    'origen'                => $origen,
                    'vigente'               => true,
                    'subido_por'            => auth()->id(),
                    'subido_at'             => now(),
                    'revisado_por'          => $porSecretaria ? auth()->id() : null,
                    'revisado_at'           => $porSecretaria ? now() : null,
                ]);
                self::auditar($doc, 'created', null, ['archivo' => $archivo->getClientOriginalName(), 'estado' => $doc->estado]);

                return $doc;
            });
        } catch (\Throwable $e) {
            Storage::disk(self::DISCO)->delete($path);
            throw $e;
        }
    }

    /** Quita un documento del expediente vigente; queda en el historial y el archivo no se borra. */
    public static function retirar(ExpedienteDocumento $documento): void
    {
        $documento->update(['vigente' => false]);
        self::auditar($documento, 'updated', ['vigente' => 'Sí'], ['vigente' => 'Quitado (queda en el historial)']);
    }

    // ── Revisión por secretaría ───────────────────────────────────────────────

    public static function aprobar(ExpedienteDocumento $documento): void
    {
        if (! $documento->tieneArchivo()) {
            throw new \DomainException('No hay archivo para aprobar: aún no se ha subido.');
        }
        $antes = $documento->estado;
        $documento->update(['estado' => 'aprobado', 'observacion' => null, 'revisado_por' => auth()->id(), 'revisado_at' => now()]);
        self::auditar($documento, 'updated', ['estado' => $antes], ['estado' => 'aprobado']);
    }

    public static function rechazar(ExpedienteDocumento $documento, string $observacion): void
    {
        if (trim($observacion) === '') throw new \DomainException('Indica qué está mal para que lo corrijan.');
        if (! $documento->tieneArchivo()) throw new \DomainException('No hay archivo para rechazar: aún no se ha subido.');

        $antes = $documento->estado;
        $documento->update(['estado' => 'rechazado', 'observacion' => trim($observacion), 'revisado_por' => auth()->id(), 'revisado_at' => now()]);
        self::auditar($documento, 'updated', ['estado' => $antes], ['estado' => 'rechazado', 'observacion' => trim($observacion)]);
    }

    /**
     * Pide un documento: si ya hay uno vigente (aunque esté aprobado) se vuelve a pedir con la
     * observación; si no hay ninguno, queda registrado como solicitado y sin archivo.
     */
    public static function solicitar(User $user, string $tipo, string $observacion, ?int $asignacionId = null, ?string $variante = null): ExpedienteDocumento
    {
        if (trim($observacion) === '') throw new \DomainException('Indica qué documento necesitas y por qué.');

        $actual = $tipo === 'otro' ? null : self::vigente($user->id, $tipo, $asignacionId, $variante);
        if ($actual) {
            $antes = $actual->estado;
            $actual->update(['estado' => 'solicitado', 'observacion' => trim($observacion), 'revisado_por' => auth()->id(), 'revisado_at' => now()]);
            self::auditar($actual, 'updated', ['estado' => $antes], ['estado' => 'solicitado', 'observacion' => trim($observacion)]);
            return $actual;
        }

        $doc = ExpedienteDocumento::create([
            'user_id' => $user->id, 'tipo' => $tipo, 'asignacion_docente_id' => $asignacionId, 'variante' => $variante,
            'archivo_path' => null, 'estado' => 'solicitado', 'observacion' => trim($observacion),
            'origen' => 'secretaria', 'vigente' => true, 'revisado_por' => auth()->id(), 'revisado_at' => now(),
        ]);
        self::auditar($doc, 'created', null, ['estado' => 'solicitado', 'observacion' => trim($observacion)]);
        return $doc;
    }

    /** Solo Administrador o Super Admin pueden eliminar definitivamente (p. ej. se subió el documento de otra persona). */
    public static function puedeEliminarDefinitivamente(?User $quien = null): bool
    {
        return (bool) ($quien ?? auth()->user())?->hasAnyRole(['Administrador', 'Super Admin']);
    }

    /**
     * Borra el archivo y su registro para siempre (archivo subido por error). Si era la versión
     * vigente, vuelve a quedar vigente la anterior. Si el mismo archivo estaba también en el
     * registro de admisión o en las columnas anteriores al expediente, esa referencia se limpia
     * para que no quede un enlace roto. Queda en auditoría con el motivo.
     */
    public static function eliminarDefinitivamente(ExpedienteDocumento $documento, string $motivo): void
    {
        if (! self::puedeEliminarDefinitivamente()) {
            throw new \DomainException('Solo un Administrador puede eliminar documentos definitivamente.');
        }
        if (mb_strlen(trim($motivo)) < 5) {
            throw new \DomainException('Indica el motivo de la eliminación (por ejemplo: "Se subió el documento de otra persona").');
        }

        DB::transaction(function () use ($documento, $motivo) {
            $documento = ExpedienteDocumento::lockForUpdate()->findOrFail($documento->id);
            $path  = $documento->archivo_path;
            $disco = $documento->disco;

            self::auditar($documento, 'deleted', [
                'tipo' => $documento->nombreTipo(), 'archivo' => $path, 'estado' => $documento->estado,
                'subido_at' => $documento->subido_at?->format('d/m/Y H:i'), 'vigente' => $documento->vigente ? 'Sí' : 'No',
            ], ['motivo' => trim($motivo)]);

            $eraVigente = $documento->vigente;
            $documento->delete();

            // Si era la vigente, la versión anterior vuelve a serlo
            if ($eraVigente && $documento->tipo !== 'otro') {
                ExpedienteDocumento::where('user_id', $documento->user_id)->where('tipo', $documento->tipo)
                    ->where('asignacion_docente_id', $documento->asignacion_docente_id)
                    ->where('variante', $documento->variante)
                    ->whereNotNull('archivo_path')
                    ->latest('id')->first()?->update(['vigente' => true]);
            }

            if ($path && ! ExpedienteDocumento::where('archivo_path', $path)->exists()) {
                self::limpiarReferenciasAnteriores($documento->user_id, $path);
                Storage::disk($disco)->delete($path);
            }
        });
    }

    /** Columnas anteriores al expediente que podían apuntar al mismo archivo. */
    private static function limpiarReferenciasAnteriores(int $userId, string $path): void
    {
        foreach (array_column(self::DOCS_ADMISION, 0) as $col) {
            DB::table('aspirantes')->where('user_id', $userId)->where($col, $path)->update([$col => null]);
        }
        DB::table('users')->whereKey($userId)->where('certificado_discapacidad_path', $path)->update(['certificado_discapacidad_path' => null]);
        if (\Illuminate\Support\Facades\Schema::hasTable('documents')) {
            foreach (['file_curriculum', 'file_senescyt', 'file_cedula', 'file_otro', 'file_contrato'] as $col) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('documents', $col)) {
                    DB::table('documents')->where('user_id', $userId)->where($col, $path)->update([$col => null]);
                }
            }
        }
        DB::table('asignacion_docentes')->where('docente_id', $userId)->where('file_contrato', $path)->update(['file_contrato' => null]);
    }

    /** Versiones de un documento, de la más reciente a la más antigua (historial). */
    public static function historial(int $userId, string $tipo, ?int $asignacionId = null)
    {
        return ExpedienteDocumento::with(['subidoPor:id,name', 'revisadoPor:id,name'])
            ->where('user_id', $userId)->where('tipo', $tipo)
            ->where('asignacion_docente_id', $asignacionId)
            ->latest('id')->get();
    }

    private static function auditar(ExpedienteDocumento $doc, string $evento, ?array $antes, ?array $despues): void
    {
        \App\Models\AuditoriaGeneral::create([
            'auditable_type'  => ExpedienteDocumento::class,
            'auditable_id'    => $doc->id,
            'evento'          => $evento,
            'valores_antes'   => $antes,
            'valores_despues' => ($despues ?? []) + ['user_id' => $doc->user_id, 'documento' => $doc->nombreTipo()],
            'user_id'         => auth()->id(),
            'ip_address'      => request()?->ip(),
        ]);
    }

    /**
     * Primera matrícula: los documentos de admisión pasan al expediente, con su estado y
     * observación, apuntando a los mismos archivos. Si se repite no duplica nada.
     *
     * @return int documentos traspasados
     */
    public static function traspasarDesdeAdmision(Aspirante $aspirante): int
    {
        $aspirante->loadMissing('user');
        $user = $aspirante->user;
        if (! $user) return 0;

        $documentos = [];
        foreach (self::DOCS_ADMISION as $tipo => [$colPath, $colEstado, $colObs]) {
            if (! empty($aspirante->$colPath)) {
                $documentos[] = [$tipo, $aspirante->$colPath, $colEstado ? $aspirante->$colEstado : null, $colObs ? $aspirante->$colObs : null];
            }
        }
        if (! empty($user->certificado_discapacidad_path)) {
            $documentos[] = ['conadis', $user->certificado_discapacidad_path, null, null];
        }

        $traspasados = 0;
        DB::transaction(function () use ($documentos, $user, $aspirante, &$traspasados) {
            foreach ($documentos as [$tipo, $path, $estado, $observacion]) {
                $yaEsta = ExpedienteDocumento::where('user_id', $user->id)->where('tipo', $tipo)
                    ->where('archivo_path', $path)->exists();
                if ($yaEsta) continue;

                ExpedienteDocumento::vigentes()->where('user_id', $user->id)->where('tipo', $tipo)
                    ->whereNull('asignacion_docente_id')->update(['vigente' => false]);

                $estado = match ($estado) {
                    'aprobado', 'verificado' => 'aprobado',
                    'rechazado'              => 'rechazado',
                    default                  => 'pendiente',
                };
                ExpedienteDocumento::create([
                    'user_id'         => $user->id,
                    'tipo'            => $tipo,
                    'archivo_path'    => $path,
                    'disco'           => 'public',
                    'nombre_original' => basename($path),
                    'estado'          => $estado,
                    'observacion'     => $observacion,
                    'origen'          => 'admision',
                    'fuente'          => 'traspaso_admision',
                    'vigente'         => true,
                    'subido_at'       => $aspirante->updated_at,
                    'revisado_at'     => $estado !== 'pendiente' ? $aspirante->updated_at : null,
                ]);
                $traspasados++;
            }
        });

        return $traspasados;
    }
}
