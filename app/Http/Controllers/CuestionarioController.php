<?php

namespace App\Http\Controllers;

// use App\Http\Livewire\Cuestionario;
use Illuminate\Http\Request;
use App\Models\{ModFormulario, ModCategoria, ModCuestionario, ModRespuesta, ModBancoPregunta, ModRecomendacion, ModEstablecimiento, ModArchivo, ModRecomendacionArchivo, ModRespuestaArchivo, ModAdjunto, ModPreguntasFormulario, ModAgrupadorFormulario};
use Illuminate\Support\Facades\DB;
// use Image;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;

// use Psy\Command\WhereamiCommand;

class CuestionarioController extends Controller {
     /**
     * responderCuestionario the form for creating a new resource.
     * @return \Illuminate\Http\Response
     * Muestra un el cuestionario con preguntas listas para ser respondidas
     * $id Formulario dado
     */

    public function responderCuestionario( $FRM_id, $AGF_id){
        /*Consulta para obtener las RECOMENDACIONES de formulario correspondiente */
            // $recomendaciones = ModRecomendacion::select( 'recomendaciones.*', 'a.ARC_id','a.ARC_ruta', 'ra.FK_REC_id', 'a.ARC_descripcion', 'a.ARC_extension', 'a.ARC_tipo', 'a.ARC_tipoArchivo', 'e.EST_id', 'e.EST_nombre', 'FK_ARC_id')
            //     ->leftJoin( 'r_recomendaciones_archivos as ra', 'ra.FK_REC_id', 'recomendaciones.REC_id')
            //     ->leftJoin( 'archivos as a', 'ra.FK_ARC_id', 'a.ARC_id' )
            //     ->rightJoin( 'formularios as f', 'f.FRM_id', 'recomendaciones.FK_FRM_id' )
            //     //->rightJoin( 'establecimientos as e', 'e.EST_id', 'f.FK_EST_id' )
            //     //->where( 'e.EST_id', $est_id )
            //     ->where( 'f.FRM_id', $FRM_id )
            //     ->orderBy('recomendaciones.REC_id', 'desc')
        // ->get();

        /*Consulta para obtener las DOCUMENTOS ADJUNTOS de formulario correspondiente */
            $adjuntos = ModAdjunto::from( 'archivos as a' )
            ->select('a.ARC_ruta', 'a.ARC_id', 'a.ARC_tipoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 'fa.FK_ARC_id', 'fa.FK_FRM_id')
            ->leftjoin ('r_formularios_archivos as fa', 'fa.FK_ARC_id', 'a.ARC_id')
            ->leftjoin ('formularios as f', 'f.FRM_id','fa.FK_FRM_id')
            // ->leftjoin ('formularios as f', 'f.FRM_id', 'ad.FK_FRM_id')
            // ->leftjoin ('establecimientos as e', 'e.EST_id', 'f.FK_EST_id')
            ->where ('fa.FK_FRM_id', $FRM_id)
            ->orderBy('a.ARC_id', 'desc')
            ->get();

        // dump( $adjuntos );exit;


        DB::enableQueryLog();

        /* Se consultan las preguntas, respuestas, categorias, formularios e instituciones del $FRM_id de Formulario dado  */
        $elementos = ModFormulario::from('formularios as f')
        ->select ('rbf.RBF_id', 'bp.BCP_id', 'bp.BCP_pregunta', 'bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 'bp.BCP_complemento',
        'bp.BCP_adjunto', 'bp.BCP_aclaracion',
        'c.CAT_id as categoriaID', 'c.CAT_categoria as subcategoria', 'c.FK_CAT_id', 'c2.CAT_categoria as categoria',
        'f.FRM_id', 'f.FRM_titulo', 'f.FRM_fecha', 'e.EST_nombre', 'e.EST_id',
        'r.RES_respuesta', 'r.RES_complemento', 'r.RES_id'
        , 'rra.FK_RES_id', 'a.ARC_ruta', 'a.ARC_id',
        'a.ARC_tipoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 'af.AGF_copia', 'af.AGF_id','rbf.RBF_orden','rbf.RBF_salto_FK_BCP_id' )
        ->join ('agrupador_formularios as af', 'f.FRM_id', 'af.FK_FRM_id')
        ->join ('r_bpreguntas_formularios as rbf', 'rbf.FK_FRM_id', 'f.FRM_id')
        ->join ('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
        ->join ('categorias as c', 'bp.FK_CAT_id', 'c.CAT_id')
        ->leftjoin ('categorias as c2', 'c.FK_CAT_id', 'c2.CAT_id')
        ->leftjoin('respuestas as r', function($join){
            $join->on('r.FK_AGF_id', 'af.AGF_id')
            ->on('rbf.RBF_id','=', 'r.FK_RBF_id');
        })
        ->join ('visitas as v', 'v.VIS_id', 'f.FK_VIS_id')
        ->join ('establecimientos as e', 'e.EST_id', 'v.FK_EST_id')
        ->leftjoin ('r_respuestas_archivos as rra', 'r.RES_id', 'rra.FK_RES_id')
        ->leftjoin ('archivos as a', 'rra.FK_ARC_id', 'a.ARC_id')
        ->where ('rbf.FK_FRM_id', $FRM_id)
        ->where('af.AGF_id', $AGF_id)
        ->where('rbf.estado', 1)
        // ->orderBy('c.CAT_id', 'asc')
        // ->orderBy('bp.BCP_id', 'asc')
        ->orderBy('rbf.RBF_orden', 'asc')
        ->orderBy('rbf.RBF_id', 'asc')
        ->get()->toArray();


        // ->where ('rbf.FK_FRM_id', $FRM_id)
        // ->where('af.AGF_id', $AGF_id)
        // ->where('bp.estado', 1)
        // ->orderBy('rbf.RBF_orden', 'asc')
        // ->get()->toArray();

        // $quries = DB::getQueryLog();
        // dump($quries);
        // exit;
        // DB::enableQueryLog();
        if ( count($elementos) > 0 ){
            $EST_nombre = $elementos[0]['EST_nombre'];
            $FRM_titulo = $elementos[0]['FRM_titulo'];
            $AGF_copia = $elementos[0]['AGF_copia'];
            $elementos_categorias = CustomController::array_group( $elementos, 'subcategoria' );
            return view( 'cuestionarios.cuestionario-responder', compact( 'elementos', 'FRM_id', 'adjuntos', 'EST_nombre','FRM_titulo','AGF_copia', 'elementos_categorias', 'AGF_id') );
        } else {
            return view( 'cuestionarios.cuestionario-responder', compact( 'elementos','FRM_id' ) );
        }


        $quries = DB::getQueryLog();
        // dump( $elementos_categorias );//exit;

        //exit;
        return view( 'cuestionarios.cuestionario-responder', compact( 'elementos', 'FRM_id', 'adjuntos', 'EST_nombre','FRM_titulo','AGF_copia', 'elementos_categorias' ) );
    }

    /* Muestra en forma de tabla vertical solo las respuetas del formulario seleccionado */
    public function verCuestionario( $FRM_id ){
        exit('this->preguntasRespuestas($FRM_id)');
        // $elementos = $this->preguntasRespuestas($FRM_id);
        // return view('cuestionarios.cuestionario-ver', compact( 'elementos', 'FRM_id' ));
    }

    public function duplicarCuestionario( $FRM_id,  ){
        /* Obtiene la cantidad de copias realizadas (maximo) de un formulario. AGF_copia de latabla que agrupador_formularios */
        $max_FRM_version = ModAgrupadorFormulario::where('FK_FRM_id', $FRM_id)->max( 'AGF_copia' );
        $FRM = ModFormulario::select('FRM_tipo', 'FK_VIS_id')->where( 'FRM_id', $FRM_id )->get()->toArray();

        /* Se completa el array $nuevoFormulario */
        $nuevoFormulario['FK_FRM_id'] = $FRM_id;
        $nuevoFormulario['AGF_copia'] = $max_FRM_version+1;
        $nuevoFormulario['FK_USER_id'] = Auth::user()->id;
        /* Sólo si el formulario es de TIPO 1, puede duplicarse muchas veces */
        if( is_null($max_FRM_version) && ($FRM[0]["FRM_tipo"] == '1' ) ){
            // dump("Posible crear");
            $resultado = $this->fn_duplicar_cuestionario( $nuevoFormulario );
        }elseif( $FRM[0]["FRM_tipo"] == 'N' ){
            // dump("Posible crear");
            $resultado = $this->fn_duplicar_cuestionario( $nuevoFormulario );
        }else{
            $resultado = 0;
        }
        // dump(  ); exit;
        return redirect('/formulario/buscaFormularios/'.$FRM[0]["FK_VIS_id"].'/'. $resultado);
    }
    /*  return > 0: se guardó el dato correctamente
        return -1: Error al guardar el dato
    */
    public function fn_duplicar_cuestionario( $nuevoFormulario ){
        DB::beginTransaction();
        try {
            ModAgrupadorFormulario::insert( $nuevoFormulario );
            $ultimoFRMid = DB::getPdo()->lastInsertId();
            DB::commit();
            return $ultimoFRMid;//redirect('/cuestionario/responder/'.$FRM_id.'/'.$ultimoFRMid);
        }
        catch (\Exception $e) {
            DB::rollback();
            // return $e->getMessage();
            return -1;
        }
    }

    /* Elimina el cuestionario duplicado */
    public function eliminarCuestionario( Request $request ){
        if(Auth::user()->rol == 'Administrador' ){

            DB::beginTransaction();
            try {
                ModFormulario::where('FRM_id', $request->FRM_id)->delete();
                ModPreguntasFormulario::where('FK_FRM_id', $request->FRM_id)->delete();

                DB::commit();
                return redirect()->back()->with('success', 'Eliminado correctamente');
            }
            catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('warning', 'No se pudo eliminar el formulario');
                exit ($e->getMessage());
            }
        }
    }

    public function preguntasRespuestas( $FRM_id, $AGF_copia){

        // $quries = DB::getQueryLog();
        // dump( $quries );
        // exit;
        // return $elementos;
    }
    /* Guarda las recomendaciones uno a uno */
    /* SE DEBEN PREPARAR ARRAY DE CADA ELEMENTO Y GUARDARLOS CON OPCION DE ROLLBACK */
    public function guardarRecomendaciones( Request $request ){
        // $ids = [];

        // /* Guardar Y COMPRIME las imagenes en un foreach */
        // if ( $request->file('REC_archivo') ){
        //     /* Array para guardar las imagenes */
        //     foreach($request->file('REC_archivo') as $key => $archivo ){

        //         $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );

        //         if( $tipoArchivo[0] == 'image'){
        //             $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/imagenes'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

        //             array_push( $ids, $idArchivo->ARC_id );

        //             $image = Image::make($archivo->path());

        //             $image->resize(null, 600, function ($const) {
        //                 $const->aspectRatio();
        //             })->save( public_path('/uploads/imagenes/').$archivo->store('') );
        //         } else {
        //             $idArchivo = ModArchivo::create( ['ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/documentos'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

        //             array_push( $ids, $idArchivo->ARC_id );

        //             $archivo->move(public_path('/uploads/documentos/'), $archivo->store(''));
        //         }
        //     }
        //     /* Si existe mas de una imagen o archivo por recomendacion itera los id de los archivos para guardarlos en la tabla relacionada */
        //     foreach ($ids as $key => $value) {
        //         ModRecomendacionArchivo::create(['FK_ARC_id' => $value, 'FK_REC_id' => $rec->REC_id]);
        //     }
        // } else {

        // }
        // /* Guarda la recomendacion enviada */
        // $rec = ModRecomendacion::create( ['REC_recomendacion' => $request->REC_recomendacion, 'FK_FRM_id' => $request->FK_FRM_id] );
        // exit;
    }

    public function buscarRecomendaciones( Request $request ){
        // dump($request->except('_token'));exit;
        $id = $request->id;

        DB::enableQueryLog();
        $recomendaciones = ModRecomendacion::select( 'recomendaciones.REC_id', 'recomendaciones.REC_recomendacion', 'recomendaciones.FK_FRM_id', 'archivos.ARC_ruta' )
        ->leftJoin('r_recomendaciones_archivos as ra', 'ra.FK_REC_id', 'recomendaciones.REC_id')
        ->leftJoin('archivos', 'ra.FK_ARC_id', 'archivos.ARC_id')
        ->where('recomendaciones.FK_FRM_id',  $request->id)
        ->get();

        // $quries = DB::getQueryLog();
        // dump( $quries );
        // exit;

        return view('cuestionarios.cuestionario-responses', compact('recomendaciones', 'id'));
    }

    /**
     * guardarRespuestasCuestionario a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * Guarda las respuestas del cuestionario
     */
    public function guardarRespuestasCuestionario(Request $request){
        // dump( $request->FK_RBF_id, $request->FK_AGF_id);//exit;
        if( $request->RES_tipoRespuesta == 'Casilla verificación' ){
            $rpta = json_encode($request->RES_respuesta, JSON_UNESCAPED_UNICODE);
            unset($request['RES_respuesta']);
            $request->merge( ['RES_respuesta' => $rpta] );
        }

        if( $request->RES_tipoRespuesta == 'Casilla verificación' && $request->RES_respuesta == 'null'){
            $request->merge( ['RES_respuesta' => null] );
        }

        /* Si la respuesta está vacia se envia mensaje y se marca un error y se rechaza transaccion.
        Si no se envia el mensaje la respuesta se guarda en VACIO */
        DB::beginTransaction();
        try {
            $respuesta = ModRespuesta::select('RES_id','FK_AGF_id')
            ->where('FK_RBF_id', $request->FK_RBF_id)
            ->where('FK_AGF_id', $request->FK_AGF_id)
            ->first();
            // dump($respuesta );

            // si la respuesta es nueva se inserta un nuevo dato en la tabla respuestas
            if( is_null($respuesta) && !is_null($request->RES_respuesta) ) {
                $resp = ModRespuesta::create($request->except('_token', 'ARC_descripcion'));
                $resp_id = $resp->RES_id;
                // echo 'INSERTED';
            }

            /* Si la respuesta ya se encuentra en la BD solo se actualiza */
            elseif( $respuesta ) {
                $resp = ModRespuesta::where('FK_RBF_id', $request->FK_RBF_id)->where('FK_AGF_id', $request->FK_AGF_id)->update($request->except('_token', 'FK_FRM_id', 'ARC_descripcion'));
                $resp_id = $respuesta['RES_id'];
                // echo 'UDATED';
            }
            //exit;
            $msg= 'correcto';

            if ( $request->file('RES_adjunto') ){
                // dump($request->file('RES_adjunto'));
                $ARC_ids = [];
                foreach( $request->file('RES_adjunto') as $archivo ){
                    $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );

                    $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/formularios'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion, 'ARC_tipo' => 'formulario', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                    $archivo->move(public_path('/uploads/formularios/'), $archivo->store(''));
                    array_push( $ARC_ids, $idArchivo->ARC_id );
                }
                if(count($ARC_ids) > 0){
                    $resp_archivos = array();
                    foreach($ARC_ids as $ARC_id){
                        array_push($resp_archivos, ['FK_ARC_id' => $ARC_id, 'FK_RES_id' => $resp_id ]);
                    }
                    ModRespuestaArchivo::insert($resp_archivos);
                    //  INSERTAR EN LA TABLA r_respuetas_archivos $resp_archivos
                    //  verificar que no se suban 2 veces los mismos archivos
                }
                $msg= 'archivos_correcto';
            }
            DB::commit();
            return response()->json([ "message" => $msg ]);
        }
        catch (\Exception $e) {
            DB::rollback();
            exit ($e->getMessage());
        }
        exit;
    }
    /* Confirma la finalizacion del cuestionario y muestra al usuario un mensaje de confirmación */
    public function confirmaCuestionario( Request $request ){
        // dump( $request->except('_token') ); exit;

        DB::beginTransaction();
        try {
            ModAgrupadorFormulario::where('FK_FRM_id', $request->FRM_id)
            ->update(['estado' => $request->estado]);
            DB::commit();
            return response()->json( [ 'message'=>'Correcto!' ] );
        }
        catch (\Exception $e) {
            DB::rollback();
            exit ($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * Muestra el cuestionario VACÍO. DONDE se deben construir la estructura de categorias subcategorias y preguntas
     */
    public function index($id){
        $formulario = ModFormulario::select('formularios.FRM_id', 'formularios.FRM_titulo', 'formularios.FRM_version', 'formularios.FRM_fecha', 'formularios.FK_EST_id', 'establecimientos.EST_nombre')
        ->leftJoin('establecimientos', 'establecimientos.EST_id', 'formularios.FK_EST_id' )
        ->where('FRM_id', $id)->first();

        $categorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
        ->whereNull('FK_CAT_id')->get();
        return view('cuestionarios.cuestionario-index', compact('formulario', 'categorias'));
    }


    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * Guarda la estructura del cuestionario construido en la funcion index
     *     */
    public function store( Request $request ){
        // dump( $request->except('_token') );
        // exit;
        $a = array();
        foreach ($request->except('_token') as $key => $value){
            $columna = explode("_", $key);

            if($columna[1] == "preguntaId" ){
                array_push($a, [ 'FK_FRM_id' => $request['FRM_id'], 'FK_BCP_id'=> $value ]);
            }
            if($columna[1] == "etiqueta" ){
                // $bcpId = ModBancoPregunta::insert($p);
                $pregunta = ModBancoPregunta::create([
                    'BCP_pregunta' => $value,
                    'FK_CAT_id' => '0',
                ]);
                array_push($a, [ 'FK_FRM_id' => $request['FRM_id'], 'FK_BCP_id'=> $pregunta->BCP_id ]);
            }
        }
    // dump( $a );
    // exit;
        if( ModCuestionario::insert($a) ){
            return redirect()->route('cuestionario.imprimir', $request['FRM_id']);
        }
    }

    /**
     * Display the specified resource.
     * @param  \App\Models\ModCuestionario  $cuestionario
     * @return \Illuminate\Http\Response
     * Muestra el formulario ya construido listo para imprimir
     */
    public function imprimirCuestionario($FRM_id, $AGF_id){

        $elementos = ModFormulario::from('formularios as f')
        ->select ('rbf.RBF_id', 'bp.BCP_id', 'bp.BCP_pregunta', 'bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 'bp.BCP_complemento',
        'bp.BCP_aclaracion',
        'c.CAT_id as categoriaID', 'c.CAT_categoria as subcategoria', 'c.FK_CAT_id', 'c2.CAT_categoria as categoria',
        'f.FRM_id', 'f.FRM_titulo', 'f.FRM_fecha', 'e.EST_nombre', 'e.EST_id','af.FK_FRM_id', 'af.AGF_id')
        //'r.RES_respuesta', 'r.RES_complemento', 'r.RES_id'
        //, 'rra.FK_RES_id', 'a.ARC_ruta', 'a.ARC_id',
        //'a.ARC_tipoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 'af.AGF_copia', 'af.AGF_id')
        ->join ('agrupador_formularios as af', 'f.FRM_id', 'af.FK_FRM_id')
        ->join ('r_bpreguntas_formularios as rbf', 'rbf.FK_FRM_id', 'f.FRM_id')
        ->join ('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
        ->join ('categorias as c', 'bp.FK_CAT_id', 'c.CAT_id')
        ->leftjoin ('categorias as c2', 'c.FK_CAT_id', 'c2.CAT_id')
        //>leftjoin('respuestas as r', function($join){
        //     $join->on('r.FK_AGF_id', 'af.AGF_id')
        //     ->on('rbf.RBF_id','=', 'r.FK_RBF_id');
        // })
        ->join ('visitas as v', 'v.VIS_id', 'f.FK_VIS_id')
        ->join ('establecimientos as e', 'e.EST_id', 'v.FK_EST_id')
        // ->leftjoin ('r_respuestas_archivos as rra', 'r.RES_id', 'rra.FK_RES_id')
        // ->leftjoin ('archivos as a', 'rra.FK_ARC_id', 'a.ARC_id')
        ->where ('rbf.FK_FRM_id', $FRM_id)
        ->where('af.AGF_id', $AGF_id)
        ->where('rbf.estado', 1)
        ->orderBy('rbf.RBF_orden', 'asc')
        ->orderBy('rbf.RBF_id', 'asc')
        ->get()->toArray();

        if ( count($elementos) > 0 ){
            $EST_nombre = $elementos[0]['EST_nombre'];
            $FRM_titulo = $elementos[0]['FRM_titulo'];
            $AGF_id = $elementos[0]['AGF_id'];

            $elementos_categorias = CustomController::array_group( $elementos, 'subcategoria' );
            return view('cuestionarios.cuestionario-imprimir', compact('elementos', 'elementos_categorias', 'FRM_id', 'FRM_titulo', 'EST_nombre','AGF_id'));
        } else {
            return view('cuestionarios.cuestionario-imprimir', compact('elementos'));
        }
    }

    /* Muestra los archivos adjuntos al cuestionario*/
    public function adjuntosFormulario($est_id, $frm_id = null){

        $formulario = ModFormulario::select('formularios.FRM_id', 'formularios.FRM_titulo', 'formularios.FRM_version', 'formularios.FRM_fecha', 'formularios.FK_EST_id', 'establecimientos.EST_nombre')
        ->leftJoin('establecimientos', 'establecimientos.EST_id', 'formularios.FK_EST_id' )
        ->where('FRM_id', $frm_id)->first();

        DB::enableQueryLog();

        // $adj = ModAdjunto::from( 'adjuntos as ad' )
        // ->select('ad.*', 'a.ARC_ruta', 'a.ARC_id', 'a.ARC_tipoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 'raa.FK_ADJ_id')
        // ->leftjoin ('r_adjuntos_archivos as raa', 'ad.ADJ_id', 'raa.FK_ADJ_id')
        // ->leftjoin ('archivos as a', 'raa.FK_ARC_id', 'a.ARC_id')
        // ->leftjoin ('formularios as f', 'f.FRM_id', 'ad.FK_FRM_id')
        // ->where ('f.FK_EST_id', $est_id);

        // if( $frm_id ){
        //     $adjuntos = $adj->where ('ad.FK_FRM_id', $frm_id)->orderBy('ad.ADJ_id', 'desc')->get();
        // }else{
        //     $adjuntos = $adj->orderBy('ad.ADJ_id', 'desc')->get();
        // }

        // $quries = DB::getQueryLog();
        // dump($quries);
        // exit;
        return view('formulario.formularios-adjuntos', compact('formulario', 'adjuntos'));
    }

}



