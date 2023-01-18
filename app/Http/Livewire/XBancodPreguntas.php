<?php

namespace App\Http\Livewire;
use App\Models\BancoPreguntas;
use App\Models\Pregunta;
use Livewire\Component;

class BancodPreguntas extends Component
{
    
    // public $buscar = '';
    // public $FK_FRM_id;
    // public $FK_BCP_id;
    // public $message = '';
    // public $formId;

    // public function mount($formId){
    //     $this->formId = $formId;
    // }


    // protected $rules = [
    //     'FK_FRM_id' => 'required',
    //     'FK_BCP_id' => 'required',
    // ];

    // public function render()
    // {
    //     $bancoPreguntas = BancoPreguntas::select('banco_preguntas.BCP_id', 'banco_preguntas.FK_CAT_id', 'banco_preguntas.BCP_pregunta', 'categorias.CAT_id', 'categorias.CAT_categoria')
    //     ->join('categorias', 'banco_preguntas.FK_CAT_id', 'categorias.CAT_id')
    //     ->where('BCP_pregunta',  'ilike', '%' . $this->buscar . '%')
    //     ->get();
    //     //dump($this->formId);
    //     //$bancoPreguntas = BancoPreguntas::get();
    //     $preguntas = Pregunta::get();

    //     return view('livewire.bancod-preguntas', ['bancoPreguntas' => $bancoPreguntas, 'formId' => $this->formId]);

    // }

    // public function save($preguntaId, $formId){
    //     //dump( $categoriaId.' ddddd '.  $preguntaId);
    //     $this->FK_FRM_id = $formId;
    //     $this->FK_BCP_id = $preguntaId;

    //     $this->validate();

    //     Pregunta::create([
    //         'FK_FRM_id' => $this->FK_FRM_id,
    //         'FK_BCP_id' => $this->FK_BCP_id,
    //     ]);
    // }
}
