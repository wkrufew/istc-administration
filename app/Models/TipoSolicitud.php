<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'tipos_solicitudes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'requiere_documento',
        'is_active',
    ];

    protected $casts = [
        'precio'             => 'decimal:2',
        'requiere_documento' => 'boolean',
        'is_active'          => 'boolean',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}
