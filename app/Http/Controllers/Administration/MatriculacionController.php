<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MatriculacionController extends Controller
{
    public function index()
    {
        return view('administracion.matriculaciones.index');
    }
}
