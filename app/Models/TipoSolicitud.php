<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'tipos_solicitudes';

    const TIPOS_CERTIFICADO = [
        ''          => 'Sin certificado',
        'cna'       => 'Certif. No Adeudar',
        'matricula' => 'Certif. Matrícula',
    ];

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'requiere_documento',
        'tipo_certificado',
        'notifica_docente',
        'is_active',
    ];

    protected $casts = [
        'precio'             => 'decimal:2',
        'requiere_documento' => 'boolean',
        'notifica_docente'   => 'boolean',
        'is_active'          => 'boolean',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}
