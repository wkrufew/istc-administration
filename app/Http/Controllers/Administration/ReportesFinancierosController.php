<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportesFinancierosController extends Controller
{
    public function index()
    {
        return view('administracion.reportes-financieros.index');
    }
}
