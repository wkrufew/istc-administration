<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConvenioAplicado extends Model
{
    protected $table = 'convenios_aplicados';

    protected $fillable = [
        'user_id',
        'tipo_convenio_id',
        'periodo_id',
        'porcentaje_aplicado',
        'motivo',
        'documento_path',
        'observacion',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
        'registrado_por',
    ];

    protected $casts = [
        'porcentaje_aplicado' => 'decimal:2',
        'fecha_inicio'        => 'date',
        'fecha_fin'           => 'date',
        'is_active'           => 'boolean',
        'requiere_documento'  => 'boolean',
    ];

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tipoConvenio()
    {
        return $this->belongsTo(TipoConvenio::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
