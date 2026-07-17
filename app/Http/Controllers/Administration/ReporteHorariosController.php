<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;

class ReporteHorariosController extends Controller
{
    public function index()
    {
        return view('administracion.reporte-horarios.index');
    }
}
