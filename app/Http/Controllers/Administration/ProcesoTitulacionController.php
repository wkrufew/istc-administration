<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProcesoTitulacionController extends Controller
{
    public function index()
    {
        return view('administracion.notas-titulacion.index');
    }
}
