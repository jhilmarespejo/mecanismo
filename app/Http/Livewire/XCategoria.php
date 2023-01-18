<?php

namespace App\Http\Livewire;

use App\Models\Ciudad;
use Livewire\Component;

class Categoria extends Component
{

    public $dep, $prov, $FK_CID_id ;


    public function render()
    {
        $provincias = [] ;
        $municipios = [] ;
        $departamentos = Ciudad::whereNull('FK_CID_id')->get();
        if($this->dep){
            $provincias = Ciudad::where('FK_CID_id', $this->dep)->get();
            //dump($this->dep);
        }
        if($this->prov){
            $municipios = Ciudad::where('FK_CID_id', $this->prov)->get();
            //dump($this->dep);
        }
        $cidId = $this->FK_CID_id;
        //$this->emit('NuevoEstablecimiento','UpdatingFK_CID_id', $this->FK_CID_id);
        return view('livewire.ciudades',compact('departamentos', 'provincias', 'municipios','cidId'));
    }
}
