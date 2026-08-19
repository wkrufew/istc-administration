<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoConvenio extends Model
{
    protected $table = 'tipos_convenio';

    protected $fillable = [
        'nombre',
        'descripcion',
        'porcentaje_defecto',
        'tipo_alcance',
        'requiere_documento',
        'is_active',
    ];

    protected $casts = [
        'porcentaje_defecto' => 'decimal:2',
        'requiere_documento' => 'boolean',
        'is_active'          => 'boolean',
    ];

    public function conveniosAplicados()
    {
        return $this->hasMany(ConvenioAplicado::class);
    }
}
