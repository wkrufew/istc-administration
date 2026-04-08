<?php

namespace App\Http\Controllers\Docencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalificacionesController extends Controller
{
    public function index()
    {
        return view('docencia.calificaciones.index');
    }
}
