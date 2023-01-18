<?php

namespace App\Http\Livewire;

use App\Models\ModFormulario;
use App\Models\Categoria;
use Livewire\{Component, WithPagination};
use DB;

class Formularios extends Component
{
    use WithPagination;
    public $buscarForm = '';
    public $open = false;

    protected $listeners = ['render'];


    public function updatingBuscarForm()
    {//para la paginacion
        $this->resetPage();
    }

    protected $paginationTheme = 'bootstrap';
    public function render()
    {
        DB::enableQueryLog();

        $formularios = ModFormulario::select('FRM_id', 'FRM_titulo', 'FRM_version', 'FRM_fecha')
        ->where('FRM_titulo',  'ilike', '%' . $this->buscarForm . '%')
        ->orderBy('FRM_id', 'Desc')->paginate(10);

        // $formularios = Formulario::select('banco_preguntas.FRM_id', 'banco_preguntas.FRM_titulo', 'categorias'.'CAT_categoria')
        // ->join('categorias', 'categorias.CAT_id', 'FK_CAT_id')
        // ->where('FRM_titulo',  'ilike', '%' . $this->buscarForm . '%')
        // ->orderBy('FRM_id', 'Asc')->paginate(10);

        $quries = DB::getQueryLog();
        //dump($quries);
        return view('livewire.formularios', compact('formularios'));
    }
}
