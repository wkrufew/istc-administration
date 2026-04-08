<?php

namespace App\Http\Controllers\Docencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AsistenciasController extends Controller
{
    public function index()
    {
        return view('docencia.asistencias.index');
    }

    public function asistenciacorreccion()
    {
        return view('docencia.asistencias.asistenciacorreccion');
    }
}
