<?php

namespace App\Http\Livewire;

use Livewire\{Component, WithFileUploads, WithPagination};
use App\Models\ModBancoPregunta;

class BancoPreguntasNueva extends Component
{

    public $modalPreguntaNueva = false
        ,$muestraBoton = false
        ,$BCP_pregunta
        ,$BCP_tipoRespuesta
        ,$BCP_opciones
        ,$FK_CAT_id
        ,$BCP_complemento
        ,$BCP_aclaracion
        ,$i=0
        ,$inputs = [];

    protected $listeners = ['emit_BancoPreguntas_nuevaCategoria'];
    use WithPagination;

    public function updatedModalPreguntaNueva(){
        // if( !$this->modalPreguntaNueva ){
            $this->emit('emit_Categorias_modal');
            $this->resetExcept('modalPreguntaNueva');
        // }
    }
    public function updatedBCPTipoRespuesta( $tipo ){
        //dump($tipo);exit;
        if( $tipo == 'Afirmación' || $tipo == 'Casilla verificación' || $tipo == 'Lista desplegable' ){
            $this->muestraBoton = true;
        } else {
            $this->muestraBoton = false;
        }
    }
    /**
     * Adiciona un input de tipo texto a la lista.
     * $i   el numero de indice, cantidad total de inputs
     */
    public function adicionaInput($i){
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs ,$i);
    }
    /**
     * Elimina un input de tipo texto a la lista.
     * $i   el numero de indice a eliminar
     */
    public function removerInput($i){
        unset($this->inputs[$i]);
    }
    public function emit_BancoPreguntas_nuevaCategoria( $CAT_id ){
        $this->FK_CAT_id = $CAT_id;
    }

    public function render()
    {
        return view('livewire.banco-preguntas-nueva');
    }


    /**
     * Guarda una nueva pregunta y sus opciones.
    */
    public function guardarPregunta(){

        $data = $this->validate([
            'BCP_pregunta' => 'required|max:400',
            'BCP_tipoRespuesta' => 'required|max:150',
            'FK_CAT_id' => 'required',
            'BCP_opciones.*' => 'required',
            'BCP_complemento' => 'nullable',
            'BCP_aclaracion' => 'nullable',
        ],[
            'required' => 'El dato es requerido!',
            'max' => 'Texto muy extenso!',
        ]);
        if($this->BCP_opciones){
            $data['BCP_opciones'] = json_encode($data['BCP_opciones']);
        }
        // dump($data ); exit;

        if(ModBancoPregunta::create($data)){
            $this->reset(['modalPreguntaNueva', 'BCP_pregunta', 'BCP_tipoRespuesta' , 'FK_CAT_id']);
            $this->emitTo('banco-preguntas-index', 'render');
            //$this->resetPage();
            $this->emit('success', 'Se agregó la pregunta');
        }

    }


}
