<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoInstitucional extends Model
{
    protected $table = 'documentos_institucionales';

    protected $fillable = ['tipo', 'nombre', 'path'];

    public const TIPOS = [
        'silabo'  => 'Sílabo',
        'rubrica' => 'Rúbrica de Evaluación',
        'acta'    => 'Acta de Calificaciones',
        'guia'    => 'Guía Didáctica',
        'otro'    => 'Otro',
    ];
}
