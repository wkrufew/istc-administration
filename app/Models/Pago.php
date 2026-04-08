<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'numero_comprobante',
        'codigo_referencia',
        'obligacion_id',
        'monto',
        'metodo_pago',
        'numero_cuota',
        'estado',
        'fecha_pago',
        'descripcion',
        'datos_gateway',
        'comprobante_path',
    ];

    protected $casts = [
        'monto'        => 'decimal:2',
        'fecha_pago'   => 'datetime',
        'datos_gateway' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | CONSTANTES
    |--------------------------------------------------------------------------
    */

    const ESTADO_PENDIENTE   = 'Pendiente';
    const ESTADO_PROCESANDO  = 'Procesando';
    const ESTADO_APROBADO    = 'Aprobado';
    const ESTADO_RECHAZADO   = 'Rechazado';
    const ESTADO_REEMBOLSADO = 'Reembolsado';

    const METODO_TRANSFERENCIA = 'Transferencia';
    const METODO_EFECTIVO      = 'Efectivo';
    const METODO_TARJETA       = 'Tarjeta';
    const METODO_DEPOSITO      = 'Deposito';
    const METODO_PAYPHONE      = 'Payphone';

    /*
    |--------------------------------------------------------------------------
    | BOOT
    |--------------------------------------------------------------------------
    */

    /* protected static function booted()
    {
        static::creating(function ($pago) {

            if (empty($pago->numero_comprobante)) {
                $ultimo = self::max('id') + 1;
                $pago->numero_comprobante =
                    'TRX-' . now()->year . '-' .
                    str_pad($ultimo, 5, '0', STR_PAD_LEFT);
            }

            if (empty($pago->estado)) {
                $pago->estado = self::ESTADO_PENDIENTE;
            }

            if (empty($pago->metodo_pago)) {
                $pago->metodo_pago = self::METODO_TRANSFERENCIA;
            }
        });

        static::updated(function ($pago) {

            if ($pago->estado === self::ESTADO_APROBADO) {
                $pago->obligacion->actualizarEstado();
            }
        });
    } */

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function obligacion()
    {
        return $this->belongsTo(ObligacionesFinanciera::class, 'obligacion_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getMontoFormateadoAttribute()
    {
        return '$' . number_format($this->monto, 2);
    }

    public function getEsAprobadoAttribute()
    {
        return $this->estado === self::ESTADO_APROBADO;
    }

    /* public function getComprobanteUrlAttribute()
    {
        return $this->comprobante_path
            ? asset('storage/' . $this->comprobante_path)
            : null;
    } */
}
