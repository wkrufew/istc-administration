<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prerequisito extends Model
{
    protected $table = 'prerequisitos';

    protected $fillable = [
        'materia_id',
        'prerequisito_id',
        'es_obligatorio'
    ];

    protected $casts = [
        'es_obligatorio' => 'boolean',
    ];

    /**
     * Materia principal
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    /**
     * Materia prerequisito
     */
    public function prerequisito()
    {
        return $this->belongsTo(Materia::class, 'prerequisito_id');
    }
}
