<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PracticasPreProfesionalesController extends Controller
{
    public function index()
    {
        return view('administracion.practicas-pre-profesionales.index');
    }

    public function comunitaria()
    {
        return view('administracion.practicas-comunitarias.index');
    }
}
