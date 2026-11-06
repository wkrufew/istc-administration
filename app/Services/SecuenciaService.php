<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Contadores para códigos correlativos (tabla secuencias). La fila se bloquea hasta que
 * termina la transacción que la pidió: dos usuarios a la vez nunca reciben el mismo
 * número, y un número ya entregado no se repite aunque el registro se anule después.
 */
class SecuenciaService
{
    /**
     * @param callable|null $inicial Valor de arranque si la clave aún no existe (el último número ya usado)
     */
    public static function siguiente(string $clave, ?callable $inicial = null): int
    {
        return DB::transaction(function () use ($clave, $inicial) {
            $fila = DB::table('secuencias')->where('clave', $clave)->lockForUpdate()->first();

            if (! $fila) {
                DB::table('secuencias')->insertOrIgnore([
                    'clave'      => $clave,
                    'valor'      => $inicial ? (int) $inicial() : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $fila = DB::table('secuencias')->where('clave', $clave)->lockForUpdate()->first();
            }

            $valor = (int) $fila->valor + 1;
            DB::table('secuencias')->where('id', $fila->id)->update(['valor' => $valor, 'updated_at' => now()]);

            return $valor;
        });
    }
}
