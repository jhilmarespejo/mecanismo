<?php

namespace App\Http\Livewire;
use Livewire\{Component, WithPagination};
use App\Models\ModBancoPregunta;
use Illuminate\Support\Facades\Auth;
use DB;

class BancoPreguntasIndex extends Component
{
    use WithPagination;
    // protected $paginationTheme = 'bootstrap';

    public $muestraBoton = false
            ,$buscarPregunta
            ,$inputs = []
            ,$BCP_id
            ,$BCP_pregunta
            ,$BCPopcionesAdicionales=[]
            ,$BCP_tipoRespuesta
            ,$BCP_opciones=[]
            ,$BCP_complemento
            ,$BCP_aclaracion
            ,$FK_CAT_id
            ,$resultadosPorPagina='10'
            ,$i=0
            ,$cantidadOpciones
            ,$ordenColumna = 'BCP_id'
            ,$ordenDireccion = 'Desc'
            ,$modalEditarPregunta = false
            //,$botonAdicionarPregunta = false
            ;

    protected $listeners = ['render', 'emit_BancoPreguntas_nuevaCategoria', 'desabilitarPregunta'];

    public function adicionarInput($i){
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs ,$i);
        // dump($i);
    }

    public function removerOpcion($i, $id=null){
        //dump( json_encode($this->BCP_opciones, true));exit;
        unset( $this->inputs[$i] );
        if($id){
            $opciones = $this->BCP_opciones;
            unset($opciones[$i]);

            $dataUpdated = ModBancoPregunta::find( $id );
            $dataUpdated->BCP_opciones = json_encode($opciones);
            $dataUpdated->save();

            // $opciones = ModBancoPregunta::select('BCP_opciones')->where('BCP_id', $id)->get();
            // foreach( $opciones as $opcion)
            $this->BCP_opciones = $opciones;
        }
    }

    public function updatedBCPTipoRespuesta( $tipo ){
        // dump($tipo);exit;
        if( $tipo == 'Afirmación' || $tipo == 'Casilla verificación' || $tipo == 'Lista desplegable' ){
            $this->muestraBoton = true;
        } else {
            $this->muestraBoton = false;
        }
    }



    /*
    public function emit_BancoPreguntas_nuevaCategoria. Recive $FK_CAT_id que se envia desde el componente Categorias.php para setear la nueva categoria (si es editada) de la pregunta en edicion

    $FK_CAT_id. Llave foranea para editar la pregunta en edicion
     */
    public function emit_BancoPreguntas_nuevaCategoria( $FK_CAT_id ){
        $this->FK_CAT_id = $FK_CAT_id;
    }

    function mount(){
        if( Auth::user()->rol != 'Administrador' ){
           return redirect()->to('panel');
        }
      }
    /*
    public function render. Renderiza a la vista banco-preguntas con los datos de la consulta
    $bancoPreguntas. variable que viaja hasta la vista banco-preguntas para mostrar la tabla con la informacion toda la informacion del banco de preguntas
    */
    public function render() {

            //$users = DB::table('users')->get();
            //DB::enableQueryLog();
            $bancoPreguntas = DB::table('banco_preguntas as bcp')->select('bcp.BCP_id', 'bcp.BCP_pregunta', 'bcp.FK_CAT_id', 'bcp.BCP_tipoRespuesta','bcp.BCP_opciones','bcp.BCP_complemento','bcp.BCP_aclaracion'
            , 'categorias.CAT_id', 'categorias.CAT_categoria'
            ,'sc.CAT_categoria as subCategoria')
            ->join ('categorias as sc', 'sc.CAT_id', 'bcp.FK_CAT_id')
            ->leftJoin ('categorias', 'categorias.CAT_id', 'sc.FK_CAT_id')
            ->where('bcp.BCP_pregunta',  'ilike', '%' . $this->buscarPregunta . '%')
            ->where('bcp.estado', '1')
            ->orderby($this->ordenColumna, $this->ordenDireccion)
            ->orderby('sc.CAT_id', 'desc')
            //->get();
            ->paginate($this->resultadosPorPagina);

            //$quries = DB::getQueryLog();
            //dump($quries);exit;

            return view('livewire.banco-preguntas-index', compact('bancoPreguntas'));
    }

    /*
    public function verPregunta. Envia y MUESTRA datos de la pregunta seleccionada para editar en el formulario del modal en la vista banco-preguntas
    $BCP_id. Es el ID unico de la pregunta para hacer edicion

    SI, $pregunta->CAT_id == null,la pregunta solo pertenece a una categoria, NO TIENE SUBCATEGORIA.
    Entonces $pregunta->FK_CAT_id = id de la categoria.

    SI, $pregunta->CAT_id != Null, la pregunta pertence a una subcategoria.
    Entonces: $pregunta->CAT_id = id de la categoria
    Entonces: $pregunta->FK_CAT_id = id de la subcategoria
    */
    public function verPregunta( $BCP_id ){
        //dump( $BCP_id );
        $this->modalEditarPregunta = true;
        DB::enableQueryLog();
        $preguntaBanco = DB::table('banco_preguntas as bcp')->select('bcp.BCP_id', 'bcp.BCP_pregunta', 'bcp.FK_CAT_id', 'bcp.BCP_opciones','bcp.BCP_complemento','bcp.BCP_aclaracion'
        , 'c.CAT_id', 'c.CAT_categoria'
        ,'sc.CAT_categoria as subCategoria'
        , 'bcp.BCP_tipoRespuesta')
        ->join ('categorias as sc', 'sc.CAT_id', 'bcp.FK_CAT_id')
        ->leftJoin ('categorias as c', 'c.CAT_id', 'sc.FK_CAT_id')
        ->where( 'bcp.BCP_id', $BCP_id )
        ->where( 'bcp.estado', '1' )
        ->get();
            // $quries = DB::getQueryLog();
            // dump($quries);//exit;
            //->orderby('sc.CAT_id')
        if( $preguntaBanco ){
            foreach( $preguntaBanco as $pregunta)
            $this->BCP_id = $pregunta->BCP_id;
            $this->BCP_pregunta = $pregunta->BCP_pregunta;
            $this->BCP_tipoRespuesta = $pregunta->BCP_tipoRespuesta;
            $this->FK_CAT_id = $pregunta->FK_CAT_id;
            $this->CAT_id = $pregunta->CAT_id;
            $this->BCP_opciones = json_decode($pregunta->BCP_opciones ,true);
            $this->BCP_complemento = $pregunta->BCP_complemento;
            $this->BCP_aclaracion = $pregunta->BCP_aclaracion;

            if( $this->BCP_opciones ){
                $this->i = count($this->BCP_opciones);
            }
            $this->emit('emit_Categorias_FKCATid', $pregunta->CAT_id, $pregunta->FK_CAT_id, $pregunta->subCategoria, true);
            // dump( $this->BCP_opciones );exit;
        }
    }

    public function updatedmodalEditarPregunta(){
        // if( !$this->modalPreguntaNueva ){
            //$this->emit('emit_Categorias_modal');
            $this->resetExcept('modalEditarPregunta','CAT_id');
        // }
    }
    /*
    public function editarPregunta. Recive y GUARDA los datos del modal donde esta formulario para editar datos.
    $data. Se validan los datos envidos desde el modal donde esta formulario para editar y guardar datos.
    */
    public function editarPregunta(){
        // $this->resetExcept('search');
        $data = $this->validate([
            'BCP_id' => 'required',
            'BCP_pregunta' => 'required|max:400',
            'BCP_tipoRespuesta' => 'required|max:150',
            'FK_CAT_id' => 'required',
            'BCP_opciones.*' => 'required',
            'BCP_complemento' => 'nullable',
            'BCP_aclaracion' => 'nullable',
            //'BCPopcionesAdicionales.*' => 'nullable',
        ],[
            'required' => 'El dato es requerido!',
            'max' => 'Texto muy extenso!',
        ]);
        //dump( $data ); exit;

        $dataUpdate = ModBancoPregunta::find($data["BCP_id"]);

        $dataUpdate->BCP_pregunta = $data["BCP_pregunta"];
        $dataUpdate->BCP_tipoRespuesta = $data["BCP_tipoRespuesta"];
        $dataUpdate->FK_CAT_id = $data["FK_CAT_id"];
        //$dataUpdate->BCP_opciones = json_encode(array_merge($data['BCP_opciones'], $this->BCPopcionesAdicionales), JSON_FORCE_OBJECT);
        $dataUpdate->BCP_complemento = $data["BCP_complemento"];
        $dataUpdate->BCP_aclaracion = $data["BCP_aclaracion"];

        if( isset($data['BCP_opciones']) ){
            $dataUpdate->BCP_opciones = json_encode(array_merge($data['BCP_opciones'], $this->BCPopcionesAdicionales), JSON_FORCE_OBJECT);
        } else {
            $dataUpdate->BCP_opciones = json_encode( $this->BCPopcionesAdicionales, JSON_FORCE_OBJECT);
        }
        // $dataUpdate->BCP_opciones = json_encode(array_merge($data['BCP_opciones'], $this->BCPopcionesAdicionales), JSON_FORCE_OBJECT);

        if($dataUpdate->save()){
            // $this->reset(['modalEditarPregunta', 'BCP_pregunta', 'BCP_tipoRespuesta' , 'FK_CAT_id', 'BCP_opciones', 'BCPopcionesAdicionales', 'i']);
            $this->resetExcept('CAT_id');
            //$this->resetPage();
            $this->emit('success', 'Se actualizó la pregunta');
        }
        // $this->resetPage();
        //$this->reset();
    }

    public function desabilitarPregunta( $id ){
        $dataUpdate = ModBancoPregunta::find( $id );
        $dataUpdate->estado = '0';
        if($dataUpdate->save()){
            $this->reset(['modalEditarPregunta', 'BCP_pregunta', 'BCP_tipoRespuesta' , 'FK_CAT_id']);
        }
        // $this->resetPage();
    }

    public function ordenar($columna){
        // dump($columna);exit;
        if ($this->ordenColumna == $columna) {
            if ($this->ordenDireccion == 'desc') {
                $this->ordenDireccion = 'asc';
            } else {
                $this->ordenDireccion = 'desc';
            }
        } else {
            $this->ordenColumna = $columna;
            $this->ordenDireccion = 'asc';
        }
    }

}
