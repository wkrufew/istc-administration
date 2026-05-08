<?php

namespace App\Livewire\Docente;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MenuDocente extends Component
{
    public function render()
    {
        return view('livewire.docente.menu-docente');
    }
}
