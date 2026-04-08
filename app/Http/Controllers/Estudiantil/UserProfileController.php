<?php

namespace App\Http\Controllers\Estudiantil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function index()
    {
        return view('estudiantil.estudiante-perfil.index');
    }
}
