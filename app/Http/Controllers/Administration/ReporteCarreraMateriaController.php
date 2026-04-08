<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteCarreraMateriaController extends Controller
{
    public function index()
    {
        return view('administracion.reporte-carrera-materia.index');
    }
}
