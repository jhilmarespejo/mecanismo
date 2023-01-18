<?php

namespace App\Http\Livewire;
use App\Models\ModOpcion;

use Livewire\Component;

class Opciones extends Component
{
    protected $listeners = ['emitx'];

    public function emitx( $tipo ){
        dump($tipo);
    }
    public function render()
    {
        return view('livewire.opciones');
    }
}
