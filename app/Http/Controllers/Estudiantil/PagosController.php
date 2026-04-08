<?php

namespace App\Http\Controllers\Estudiantil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PagosController extends Controller
{
    public function index()
    {
        return view('estudiantil.obligaciones-financieras.index');
    }
}
