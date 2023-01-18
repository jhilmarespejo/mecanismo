<?php

namespace App\Http\Livewire;

use App\Models\{ModEstablecimiento, ModNivelSeguridad, ModTipoEstablecimiento};
use Livewire\{Component, WithFileUploads};
use Illuminate\Support\Facades\Log;

class EstablecimientosNuevo extends Component
{

    public $updatedAt, $createdAt;
    public $abreModal = false;
    public $EST_nombre,
    $EST_direccion,
    $EST_normativaInterna,
    $EST_telefonoContacto,
    $EST_genero,
    $EST_grupoGeneracional,
    $EST_poblacion,
    $EST_superficie,
    $EST_superficieConstruida,
    $FK_CID_id,
    $FK_TES_id,
    $FK_NSG_id,
    $EST_coberturaMunicipios,
    $establecimiento;
    protected $paginationTheme = 'bootstrap';

    // protected $listeners = ['submit'];

    protected $listeners = ['emit_Establecimientos_FK_CID'];

    public function emit_Establecimientos_FK_CID($id){
        $this->FK_CID_id = $id;
    }

    // public function mount(){
    //     $this->establecimiento = new ModEstablecimiento() ;
    // }
    public function render()
    {
        $tiposEstablecimiento = ModTipoEstablecimiento::get();
        $nivelesSeguridad = ModNivelSeguridad::get();
        //dump();
        return view('livewire.establecimientos-nuevo', compact('tiposEstablecimiento', 'nivelesSeguridad'));
    }

    public function save()  {

        $data = $this->validate( [
            'EST_nombre' => 'required',
            'EST_direccion' => 'required',
            'EST_normativaInterna' => 'required',
            'EST_telefonoContacto' => 'required',
            'EST_genero' => 'required',
            'EST_grupoGeneracional' => 'required',
            'EST_poblacion' => 'required',
            'EST_superficie' => 'required',
            'EST_superficieConstruida' => 'required',
            'FK_TES_id' => 'required',
            'FK_NSG_id' => 'required',
            'EST_coberturaMunicipios' => 'nullable',
            'FK_CID_id' => 'required',
        ],[
            'required' => 'El dato es requerido!',
            'max' => 'Texto muy extenso!',
        ]);

        if(ModEstablecimiento::create($data)){
            $this->emitTo('establecimientos', 'render');
            $this->emit('success', 'El establecimiento se creó con éxito');
            $this->reset();
        } else {
            $this->emit('danger', 'Error, intenete nuevamente');
        }

    }


}
