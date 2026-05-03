<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;

class DocumentacionPersonalController extends Controller
{
    public function index()
    {
        return view('administracion.documentacion-personal.index');
    }
}
