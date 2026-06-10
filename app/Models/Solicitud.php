<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Solicitud extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitudes';

    const ESTADOS = [
        'pendiente'      => 'Pendiente',
        'aprobada'       => 'Aprobada',
        'rechazada'      => 'Rechazada',
        'pendiente_pago' => 'Pend. Pago',
        'pagada'         => 'Pagada',
        'en_proceso'     => 'En Proceso',
        'entregada'      => 'Entregada',
        'cancelada'      => 'Cancelada',
    ];

    const ESTADOS_TERMINALES = ['rechazada', 'entregada', 'cancelada'];

    protected $fillable = [
        'estudiante_id',
        'tipo_solicitud_id',
        'descripcion',
        'documento_path',
        'estado',
        'precio_aplicado',
        'notas_admin',
        'certificado_codigo',
        'docente_notificado_id',
        'procesado_por',
    ];

    protected $casts = [
        'precio_aplicado' => 'decimal:2',
    ];

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class);
    }

    public function procesadoPor()
    {
        return $this->belongsTo(User::class, 'procesado_por');
    }

    public function docenteNotificado()
    {
        return $this->belongsTo(User::class, 'docente_notificado_id');
    }

    public function obligacion()
    {
        return $this->hasOne(ObligacionesFinanciera::class, 'solicitud_id');
    }

    public function esTerminal(): bool
    {
        return in_array($this->estado, self::ESTADOS_TERMINALES);
    }
}
