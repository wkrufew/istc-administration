<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PagosController extends Controller
{
    public function index()
    {
        return view('administracion.pagos.index');
    }
}
