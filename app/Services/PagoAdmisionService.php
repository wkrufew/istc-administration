<?php

namespace App\Services;

use App\Models\Aspirante;
use App\Models\AuditoriaGeneral;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Pago de matrícula hecho en admisión: quien gestiona aspirantes lo confirma (monto, método y
 * fecha) y eso es lo que la matrícula aplica después. Mientras no se haya aplicado se puede
 * registrar, corregir (con motivo) o rechazar (con motivo); todo queda en auditoría. Una vez
 * aplicado, el pago ya existe en las obligaciones y se corrige desde Pagos. Quién puede
 * hacerlo lo decide el permiso gestionar_aspirantes, no el rol.
 */
class PagoAdmisionService
{
    public const METODOS = ['Transferencia' => 'Transferencia', 'Deposito' => 'Depósito', 'Efectivo' => 'Efectivo', 'Tarjeta' => 'Tarjeta', 'Payphone' => 'Payphone'];

    private const CAMPOS = ['pago_estado', 'pago_monto', 'pago_metodo', 'pago_fecha', 'pago_verificado_at', 'pago_comprobante_path', 'pago_observacion'];

    /** Por qué no se puede registrar/corregir el pago de este aspirante ahora (null = sí se puede). */
    public static function motivoNoEditable(Aspirante $aspirante): ?string
    {
        if ($aspirante->pago_aplicado_id) {
            return 'Este pago ya se aplicó a una matrícula. Para corregirlo, anula ese pago en Pagos y regístralo de nuevo.';
        }
        if (! $aspirante->pagoEditable()) {
            return 'El pago se registra cuando el aspirante está en Verificación o Aprobado.';
        }
        return null;
    }

    /**
     * Primer día aceptado para la fecha del pago. Flexible a propósito (pagos atrasados, pagos
     * hechos antes de registrarlo en el sistema): un año antes de que se abriera su cohorte o de
     * que se registrara, lo que sea anterior. Solo frena errores de tipeo como "2002".
     */
    public static function fechaMinima(Aspirante $aspirante): string
    {
        $aspirante->loadMissing('cohorte');
        $referencias = array_filter([$aspirante->cohorte?->created_at, $aspirante->created_at]);
        $base = $referencias ? min($referencias) : now();

        return Carbon::parse($base)->subYear()->toDateString();
    }

    /**
     * Deja el pago verificado con lo que consta en el comprobante. Si se sube un archivo,
     * reemplaza al comprobante actual (el anterior queda en el disco y en la auditoría).
     * Corregir un pago ya confirmado exige motivo.
     *
     * @throws \DomainException si no se puede editar o falta el comprobante o el motivo
     */
    public static function registrar(Aspirante $aspirante, float $monto, string $metodo, string $fecha,
                                     ?UploadedFile $comprobante = null, string $motivo = ''): Aspirante
    {
        $archivoNuevo = null;

        try {
            return DB::transaction(function () use ($aspirante, $monto, $metodo, $fecha, $comprobante, $motivo, &$archivoNuevo) {
                // Bloqueo: que la matrícula no lo aplique mientras se corrige
                $aspirante = Aspirante::lockForUpdate()->findOrFail($aspirante->id);

                if ($motivoNo = self::motivoNoEditable($aspirante)) {
                    throw new \DomainException($motivoNo);
                }
                if (! array_key_exists($metodo, self::METODOS)) {
                    throw new \DomainException('Selecciona el método de pago.');
                }
                if ($monto <= 0) {
                    throw new \DomainException('El monto debe ser mayor a cero.');
                }
                $fechaPago = Carbon::parse($fecha)->startOfDay();
                if ($fechaPago->isFuture()) {
                    throw new \DomainException('La fecha del pago no puede ser futura.');
                }
                if ($fechaPago->lt(Carbon::parse(self::fechaMinima($aspirante)))) {
                    throw new \DomainException('Revisa la fecha del pago: no puede ser anterior al ' . Carbon::parse(self::fechaMinima($aspirante))->format('d/m/Y') . '.');
                }
                if (! $comprobante && ! $aspirante->pago_comprobante_path) {
                    throw new \DomainException('Sube el comprobante del pago.');
                }

                $esCorreccion = $aspirante->pagoConfirmado();
                if ($esCorreccion && mb_strlen(trim($motivo)) < 5) {
                    throw new \DomainException('Indica el motivo de la corrección.');
                }

                $antes = $aspirante->only(self::CAMPOS);

                $datos = [
                    'pago_estado'        => 'verificado',
                    'pago_monto'         => round($monto, 2),
                    'pago_metodo'        => $metodo,
                    'pago_fecha'         => $fechaPago->toDateString(),
                    'pago_verificado_at' => now(),
                    'pago_observacion'   => null,
                ];
                // El archivo se guarda al final de las validaciones; si algo falla después, se borra (catch de abajo)
                if ($comprobante) {
                    $archivoNuevo = $comprobante->store("aspirantes/{$aspirante->id}/pago", 'public');
                    $datos['pago_comprobante_path'] = $archivoNuevo;
                }
                $aspirante->update($datos);

                self::auditar($aspirante, $antes, $esCorreccion ? 'Pago de admisión corregido' : 'Pago de admisión registrado', $motivo);

                return $aspirante;
            });
        } catch (\Throwable $e) {
            // No dejar archivos sueltos: si no se guardó el registro, el comprobante nuevo sobra
            if ($archivoNuevo) {
                Storage::disk('public')->delete($archivoNuevo);
            }
            throw $e;
        }
    }

    /**
     * Rechaza el comprobante con motivo (se le envía al aspirante). Si el pago ya estaba
     * confirmado, deja de contar; el monto que tenía queda en la auditoría.
     *
     * @throws \DomainException si ya se aplicó a una matrícula o falta el motivo
     */
    public static function rechazar(Aspirante $aspirante, string $motivo): Aspirante
    {
        if (trim($motivo) === '') {
            throw new \DomainException('Indica el motivo del rechazo.');
        }

        return DB::transaction(function () use ($aspirante, $motivo) {
            $aspirante = Aspirante::lockForUpdate()->findOrFail($aspirante->id);

            if ($aspirante->pago_aplicado_id) {
                throw new \DomainException('Este pago ya se aplicó a una matrícula. Para corregirlo, anula ese pago en Pagos.');
            }

            $antes = $aspirante->only(self::CAMPOS);
            $aspirante->update(['pago_estado' => 'rechazado', 'pago_observacion' => trim($motivo)]);

            self::auditar($aspirante, $antes, 'Pago de admisión rechazado', $motivo);

            return $aspirante;
        });
    }

    /** Registra solo lo que cambió (más la acción, el aspirante y el motivo). */
    private static function auditar(Aspirante $aspirante, array $antes, string $accion, string $motivo = ''): void
    {
        $antes   = self::paraAuditoria($antes);
        $despues = self::paraAuditoria($aspirante->only(self::CAMPOS));
        $cambios = array_keys(array_filter($despues, fn($valor, $campo) => (string) $valor !== (string) ($antes[$campo] ?? ''), ARRAY_FILTER_USE_BOTH));

        AuditoriaGeneral::create([
            'auditable_type'  => Aspirante::class,
            'auditable_id'    => $aspirante->id,
            'evento'          => 'updated', // la columna admite 20 caracteres; la acción va en el detalle
            'valores_antes'   => array_intersect_key($antes, array_flip($cambios)),
            'valores_despues' => array_intersect_key($despues, array_flip($cambios))
                + ['accion' => $accion, 'user_id' => $aspirante->user_id]
                + (trim($motivo) !== '' ? ['motivo' => trim($motivo)] : []),
            'user_id'         => auth()->id(),
            'ip_address'      => request()?->ip(),
        ]);
    }

    /** Fechas a texto: solo el día si no tiene hora (fecha del pago), día y hora si la tiene. */
    private static function paraAuditoria(array $valores): array
    {
        return array_map(fn($v) => $v instanceof \DateTimeInterface
            ? ($v->format('H:i:s') === '00:00:00' ? $v->format('Y-m-d') : $v->format('Y-m-d H:i'))
            : $v, $valores);
    }
}
