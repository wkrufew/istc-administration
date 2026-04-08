<?php

namespace App\Http\Controllers\Estudiantil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActaCalificacionesController extends Controller
{
    public function index()
    {
        return view('estudiantil.acta-calificaciones.index');
    }
}
