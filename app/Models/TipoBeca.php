<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoBeca extends Model
{
    protected $table = 'tipos_beca';

    protected $fillable = [
        'nombre',
        'categoria',
        'porcentaje_descuento',
        'descripcion',
        'is_active',
    ];

    protected $casts = [
        'porcentaje_descuento' => 'decimal:2',
        'is_active'            => 'boolean',
    ];

    public function becasAplicadas(): HasMany
    {
        return $this->hasMany(BecaAplicada::class, 'tipo_beca_id');
    }
}
