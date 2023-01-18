<?php
namespace App\Http\Livewire;

use App\Models\ModBancoPregunta;
use App\Models\{Pregunta, Categoria};
use Livewire\{Component, WithPagination};
use DB;



class Cuestionario extends Component
{
    public $q;
    public $buscar;
    public $pregunta;
    public $FK_FRM_id;
    public $FK_BCP_id;

    protected $queryString = ['q'];

    protected $rules = [
        'FK_FRM_id' => 'required',
        'FK_BCP_id' => 'required|unique:App\Models\Pregunta',
    ];
    public function render()
    {
        DB::enableQueryLog();

        // $bancoPreguntas = BancoPreguntas::select('banco_preguntas.BCP_id', 'banco_preguntas.FK_CAT_id', 'banco_preguntas.BCP_pregunta', 'categorias.CAT_id', 'categorias.CAT_categoria', 'categorias.FK_CAT_id as subCatId')
        // ->join('categorias', 'banco_preguntas.FK_CAT_id', 'categorias.CAT_id')
        // ->where('BCP_pregunta',  'ilike', '%' . $this->buscar . '%')
        // ->orderby('banco_preguntas.BCP_id')
        // ->get();

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

        // $quries = DB::getQueryLog();
        // dump($quries);
        // exit;
        $preguntas = Pregunta::where('FK_FRM_id', $this->q )->get();

        /* $preguntas = Arreglo con las preguntas que se almacenaron en el nuevo formulario de preguntas*/
        $preguntas = Pregunta::select('r_bpreguntas_formularios.RBF_id', 'r_bpreguntas_formularios.FK_FRM_id',
        'r_bpreguntas_formularios.FK_BCP_id', 'banco_preguntas.BCP_pregunta', 'banco_preguntas.FK_CAT_id', 'categorias.CAT_categoria')
        ->leftJoin('banco_preguntas', 'banco_preguntas.BCP_id', 'r_bpreguntas_formularios.FK_BCP_id' )
        ->leftJoin('categorias', 'categorias.CAT_id', 'banco_preguntas.FK_CAT_id' )
        ->where('r_bpreguntas_formularios.FK_FRM_id', $this->q)
        ->orderBy('banco_preguntas.FK_CAT_id')
        ->get();

        return view('livewire.cuestionario-nuevo', ['bancoPreguntas' => $bancoPreguntas, 'formId' => $this->q, 'preguntas' => $preguntas ]);
    }

    public function save( $preguntaId ){
        //dump($preguntaId);

        $this->FK_FRM_id = $this->q;
        $this->FK_BCP_id = $preguntaId;

        $this->validate();

        Pregunta::create([
            'FK_FRM_id' => $this->q,
            'FK_BCP_id' => $preguntaId,
        ]);
    }
}


// SELECT c."CAT_id", c."CAT_categoria"
// , sc."CAT_categoria"
// from categorias "c"
// LEFT JOIN categorias sc on c."CAT_id" = sc."FK_CAT_id"
// where c."FK_CAT_id" is NULL
// order by c."CAT_id"



// SELECT bp."BCP_id", bp."BCP_pregunta", bp."FK_CAT_id"
// , c."CAT_id", c."CAT_categoria"
// ,sc."CAT_categoria" as "subCat"

// from categorias "c"

// full JOIN categorias sc on c."CAT_id" = sc."FK_CAT_id"
// JOIN banco_preguntas bp on sc."CAT_id" = bp."FK_CAT_id"
// --where c."FK_CAT_id" is NULL
// order by  c."CAT_id", sc."CAT_categoria"

// select "bp"."BCP_id", "bp"."BCP_pregunta" as "pregunta", "bp"."FK_CAT_id",
// "c"."CAT_id" , "c"."CAT_categoria" as "Categoria", "c"."FK_CAT_id",
// "sc"."CAT_categoria" as "subCategoria",  "sc"."FK_CAT_id" as "subCatId"
// from categorias as "c"
// JOIN categorias as "sc" on "sc"."FK_CAT_id" = "c"."CAT_id"
// JOIN banco_preguntas as "bp" on "bp"."FK_CAT_id" = "sc"."CAT_id"
// -- where "c"."FK_CAT_id" is null
// ORDER BY "bp"."BCP_id"
