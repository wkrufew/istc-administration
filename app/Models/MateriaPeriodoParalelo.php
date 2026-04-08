<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriaPeriodoParalelo extends Model
{
    protected $table = 'materia_periodo_paralelo';

    protected $fillable = [
        'materia_id',
        'periodo_id',
        'paralelo_id',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'is_active' => 'boolean',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class);
    }
}
