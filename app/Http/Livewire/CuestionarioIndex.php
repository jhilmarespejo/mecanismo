<?php
namespace App\Http\Livewire;

use App\Models\{ModBancoPregunta, ModFormulario, ModCategoria};
//use App\Models\{Pregunta, Categoria};
use Livewire\{Component, WithPagination};
use DB;

class CuestionarioIndex extends Component
{
    public $q
        ,$buscar
        ,$pregunta
        ,$FK_FRM_id
        ,$FK_BCP_id
        ,$formularios
        ,$categorias
        ,$categoria
        ,$subcategorias
        ,$mostrarBotonSubcategoria = true
        ;

    protected $queryString = ['q'];

    protected $rules = [
        'FK_FRM_id' => 'required',
        'FK_BCP_id' => 'required|unique:App\Models\Pregunta',
    ];

    public function render()
    {
        DB::enableQueryLog();

        $this->formulario = ModFormulario::select('FRM_titulo', 'FRM_version', 'FRM_fecha')->where('FRM_id', $this->q)->first();
        /* $bancoPreguntas: Arreglo  con las preguntas del banco de preguntas y sus respectivas categorias y subcategorias */

        $bancoPreguntas = ModBancoPregunta::select('banco_preguntas.BCP_id', 'banco_preguntas.BCP_pregunta', 'banco_preguntas.FK_CAT_id'
        , 'categorias.CAT_id', 'categorias.CAT_categoria'
        ,'sc.CAT_categoria as subCategoria')
        ->join ('categorias as sc', 'sc.CAT_id', 'banco_preguntas.FK_CAT_id')
        ->leftJoin ('categorias', 'categorias.CAT_id', 'sc.FK_CAT_id')
        ->where('banco_preguntas.BCP_pregunta',  'ilike', '%' . $this->buscar . '%')
        ->orderby('categorias.CAT_id')
        ->orderby('sc.CAT_id')
        ->get();

        $preguntas = '';//Pregunta::where('FK_FRM_id', $this->q )->get();

        /* $preguntas = Arreglo con las preguntas que se almacenaron en el nuevo formulario de preguntas*/
        // $preguntas = Pregunta::select('r_bpreguntas_formularios.RBF_id', 'r_bpreguntas_formularios.FK_FRM_id',
        // 'r_bpreguntas_formularios.FK_BCP_id', 'banco_preguntas.BCP_pregunta', 'banco_preguntas.FK_CAT_id', 'categorias.CAT_categoria')
        // ->leftJoin('banco_preguntas', 'banco_preguntas.BCP_id', 'r_bpreguntas_formularios.FK_BCP_id' )
        // ->leftJoin('categorias', 'categorias.CAT_id', 'banco_preguntas.FK_CAT_id' )
        // ->where('r_bpreguntas_formularios.FK_FRM_id', $this->q)
        // ->orderBy('banco_preguntas.FK_CAT_id')
        // ->get();

        return view('livewire.cuestionario-index', ['bancoPreguntas' => $bancoPreguntas, 'formId' => $this->q, 'preguntas' => $preguntas, 'formularios' => $this->formulario, 'categorias' => $this->categorias ]);
    }

    public function save( $preguntaId ){
        $this->FK_FRM_id = $this->q;
        $this->FK_BCP_id = $preguntaId;

        $this->validate();

        Pregunta::create([
            'FK_FRM_id' => $this->q,
            'FK_BCP_id' => $preguntaId,
        ]);
    }


    public $inputs = []
        ,$i = 1
        ;

    public function change($cat){
        dump($cat);
    }
    public function agreagarCategoria($i){
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs ,$i);

        $this->categorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
        ->whereNull('FK_CAT_id')->get();
    }

    public function remove($i){
        unset($this->inputs[$i]);
    }
}
