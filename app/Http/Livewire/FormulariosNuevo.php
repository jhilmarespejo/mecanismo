<?php

namespace App\Http\Livewire;

use App\Models\{ModFormulario, ModEstablecimiento};
use Livewire\{Component, WithFileUploads};
use DB;

class FormulariosNuevo extends Component
{
    use WithFileUploads;
    protected $listeners = [''];
    public $FRM_titulo
        ,$FK_EST_id
        ,$FRM_version
        ,$FRM_fecha
        ,$EST_nombre
        ,$EST_id
        ,$establecimientos=[]
        ,$abreModal = false
        ;

    /*
    Busca y Asigna EST_id ya que el evento onclik no funciona en los option de <input list="establecimientos"
    $nombre: nombre del establecimiento seleccionado

     */
    public function asignarEstablecimientoId($nombre){
        $nombre = explode(" [", $nombre );
        $id = ModEstablecimiento::select('EST_id')->where('EST_nombre', $nombre[0])->first();
        $this->FK_EST_id = $id->EST_id;
    }
    public function updatedESTNombre(){

        DB::enableQueryLog();
        $this->establecimientos = ModEstablecimiento::select('establecimientos.EST_id','establecimientos.EST_nombre', 'c.CID_nombre as Ciudad')
        ->LeftJoin('ciudades as c', 'c.CID_id', 'establecimientos.FK_CID_id')
        ->where('establecimientos.EST_nombre', 'ilike', '%'.$this->EST_nombre.'%')->get();
        $quries = DB::getQueryLog();
        //dump($quries);exit;
    }
    public function render()
    {
        $this->FRM_fecha = date("Y-m-d");
        return view('livewire.formularios-nuevo', ['establecimientos' => $this->establecimientos]);
    }

    public function save()
    {
        // $this->validate();

        // Formulario::create([
        //     'FRM_titulo' => $this->FRM_titulo,
        //     // 'updatedAt' => null,
        //     // 'createdAt' => null,
        // ]);

        $data = $this->validate([
            'FRM_titulo' => 'required|max:300',
            'FK_EST_id' => 'required',
            'FRM_version' => 'required',
            'FRM_fecha' => 'required|min:10',
        ],[
            'required' => 'El dato es requerido!',
            'max' => 'Texto muy extenso!',
            'min' => 'Texto muy corto!',
        ]);

        //dump($data ); exit;
        $data = ModFormulario::create($data);
        if($data){
            $estId = $data->FRM_id;
            //dump($estId);
            // $this->reset();
            // $this->emitTo('formularios', 'render');
            //$this->resetPage();
            //$this->emit('success', 'Se agregó el formulario');
            //return redirect()->to('/cuestionario?q='.$estId);
            return redirect('http://127.0.0.1:8001/cuestionario/'.$estId);
        } else {
            $this->emit('danger', 'Error! intente nuevamente');
        }


    }


}
