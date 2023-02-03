<?php

namespace App\Http\Controllers;

// use App\Http\Livewire\Cuestionario;
use Illuminate\Http\Request;
use App\Models\{ModFormulario, ModCategoria, ModCuestionario, ModRespuesta, ModBancoPregunta, ModRecomendacion, ModEstablecimiento, ModArchivo, ModRecomendacionArchivo, ModRespuestaArchivo, ModAdjunto};
use DB;
use Image;
// use Psy\Command\WhereamiCommand;

class CuestionarioController extends Controller {
     /**
     * responderCuestionario the form for creating a new resource.
     * @return \Illuminate\Http\Response
     * Muestra un el cuestionario con preguntas listas para ser respondidas
     * $id Formulario dado
     */
    public function responderCuestionario( $FRM_id ){
        DB::enableQueryLog();

        /*Consulta para obtener las RECOMENDACIONES de formulario correspondiente */
            $recomendaciones = ModRecomendacion::select( 'recomendaciones.*', 'a.ARC_id','a.ARC_ruta', 'ra.FK_REC_id', 'a.ARC_descripcion', 'a.ARC_extension', 'a.ARC_tipo', 'a.ARC_tipoArchivo', 'e.EST_id', 'e.EST_nombre', 'FK_ARC_id')
            ->leftJoin( 'r_recomendaciones_archivos as ra', 'ra.FK_REC_id', 'recomendaciones.REC_id')
            ->leftJoin( 'archivos as a', 'ra.FK_ARC_id', 'a.ARC_id')
            ->rightJoin( 'formularios as f', 'f.FRM_id', 'recomendaciones.FK_FRM_id' )
            ->rightJoin( 'establecimientos as e', 'e.EST_id', 'f.FK_EST_id' )
            //->where( 'e.EST_id', $est_id )
            ->where( 'f.FRM_id', $FRM_id )
            ->orderBy('recomendaciones.REC_id', 'desc')
        ->get();

        /*Consulta para obtener las DOCUMENTOS ADJUNTOS de formulario correspondiente */
            $adjuntos = ModAdjunto::from( 'adjuntos as ad' )
            ->select('ad.*', 'a.ARC_ruta', 'a.ARC_id', 'a.ARC_tipoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 'raa.FK_ADJ_id', 'f.FRM_titulo', 'e.EST_nombre')
            ->leftjoin ('r_adjuntos_archivos as raa', 'ad.ADJ_id', 'raa.FK_ADJ_id')
            ->leftjoin ('archivos as a', 'raa.FK_ARC_id', 'a.ARC_id')
            ->leftjoin ('formularios as f', 'f.FRM_id', 'ad.FK_FRM_id')
            ->leftjoin ('establecimientos as e', 'e.EST_id', 'f.FK_EST_id')
            ->where ('ad.FK_FRM_id', $FRM_id)
            ->orderBy('ad.ADJ_id', 'desc')
        ->get();


        /* Se consultan las preguntas, categorias, formularios e instituciones del $FRM_id de Formulario dado  */
            $elementos = ModEstablecimiento:: select('rbf.RBF_id'
            ,'bp.BCP_id', 'bp.BCP_pregunta', 'bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 'bp.BCP_complemento', 'bp.BCP_adjunto'
            , 'bp.BCP_aclaracion', 'bp.FK_CAT_id'
            , 'c2.CAT_categoria as categoria', 'c.CAT_categoria as subcategoria'
            , 'c.FK_CAT_id as categoriaID'
            , 'formularios.FRM_id', 'formularios.FRM_titulo', 'formularios.FRM_version', 'formularios.FRM_fecha', 'formularios.FK_EST_id'
            , 'establecimientos.EST_nombre', 'establecimientos.EST_id', 'r.RES_respuesta', 'r.RES_complemento', 'r.RES_id','rra.FK_RES_id', 'a.ARC_ruta', 'a.ARC_id', 'a.ARC_tipoArchivo', 'a.ARC_extension','a.ARC_descripcion')
            ->leftJoin ('formularios', 'establecimientos.EST_id', 'formularios.FK_EST_id')
            ->leftJoin ('r_bpreguntas_formularios as rbf', 'formularios.FRM_id', 'rbf.FK_FRM_id')
            ->leftJoin ('banco_preguntas as bp', 'rbf.FK_BCP_id', 'bp.BCP_id')
            ->leftJoin ('categorias as c', 'bp.FK_CAT_id', 'c.CAT_id')
            ->leftJoin ('categorias as c2', 'c.FK_CAT_id', 'c2.CAT_id')
            ->leftJoin ('respuestas as r', 'r.FK_RBF_id', 'rbf.RBF_id' )
            ->leftjoin ('r_respuestas_archivos as rra', 'r.RES_id', 'rra.FK_RES_id')
            ->leftjoin ('archivos as a', 'rra.FK_ARC_id','a.ARC_id')
            ->where ('rbf.FK_FRM_id', $FRM_id)
            ->where ('bp.estado', '1')
            ->orderBy('c.CAT_id')
            ->orderBy('bp.BCP_id')
        ->get();

        $quries = DB::getQueryLog();
        // dump( $quries );
        // exit;
        return view('cuestionarios.cuestionario-responder', compact( 'elementos', 'FRM_id', 'recomendaciones', 'adjuntos'));
    }
    /* Guarda las recomendaciones uno a uno */
    /* SE DEBEN PREPARAR ARRAY DE CADA ELEMENTO Y GUARDARLOS CON OPCION DE ROLLBACK */
    public function guardarRecomendaciones( Request $request ){
        $ids = [];

        /* Guardar Y COMPRIME las imagenes en un foreach */
        if ( $request->file('REC_archivo') ){
            /* Array para guardar las imagenes */
            foreach($request->file('REC_archivo') as $key => $archivo ){

                $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );

                if( $tipoArchivo[0] == 'image'){
                    $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/imagenes'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                    array_push( $ids, $idArchivo->ARC_id );

                    $image = Image::make($archivo->path());

                    $image->resize(null, 600, function ($const) {
                        $const->aspectRatio();
                    })->save( public_path('/uploads/imagenes/').$archivo->store('') );
                } else {
                    $idArchivo = ModArchivo::create( ['ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/documentos'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                    array_push( $ids, $idArchivo->ARC_id );

                    $archivo->move(public_path('/uploads/documentos/'), $archivo->store(''));
                }
            }
            /* Si existe mas de una imagen o archivo por recomendacion itera los id de los archivos para guardarlos en la tabla relacionada */
            foreach ($ids as $key => $value) {
                ModRecomendacionArchivo::create(['FK_ARC_id' => $value, 'FK_REC_id' => $rec->REC_id]);
            }
        } else {

        }
        /* Guarda la recomendacion enviada */
        $rec = ModRecomendacion::create( ['REC_recomendacion' => $request->REC_recomendacion, 'FK_FRM_id' => $request->FK_FRM_id] );


        exit;
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
        if( $request->RES_tipoRespuesta == 'Casilla verificación' ){
            $rpta = json_encode($request->RES_respuesta, JSON_UNESCAPED_UNICODE);
            //echo $s;exit;
            unset($request['RES_respuesta']);
            $request->merge( ['RES_respuesta' => $rpta] );
        }

        //dump($request->except('_token'));//exit;
        if( $request->RES_tipoRespuesta == 'Casilla verificación' && is_null($request->RES_respuesta)){
            $request->merge( ['RES_respuesta' => null] );
        }
        /* Si la respuesta está vacia se envia mensaje y se marca un error y se rechaza transaccion.
        Si no se envia el mensaje la respuesta se guarda en VACIO */
        // elseif(is_null($request->RES_respuesta)){
        //     return response()->json([ "message" => "sin_respuesta" ]);
        // }

        DB::beginTransaction();
        try {
            $respuesta = ModRespuesta::select('RES_id')->where('FK_RBF_id', $request->FK_RBF_id)->first();
            if( is_null($respuesta) ) {
                $resp = ModRespuesta::create($request->except('_token', 'ARC_descripcion'));
                $resp_id = $resp->RES_id;
                //echo 'INSERTED';exit;
            }
            /* Si la respuesta ya se encuentra en la BD solo se actualiza */
            else {
                $resp = ModRespuesta::where('FK_RBF_id', $request->FK_RBF_id)->update($request->except('_token', 'FK_FRM_id', 'ARC_descripcion'));
                $resp_id = $respuesta['RES_id'];
                //echo 'UDATED';  exit;
            }
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
    public function imprimir($id){
        $elementos = array();
        $preguntas = ModCuestionario::select('FK_FRM_id', 'FK_BCP_id')->where('FK_FRM_id', $id)->get();

        $formulario = ModFormulario::select('formularios.FRM_id', 'formularios.FRM_titulo', 'formularios.FRM_version', 'formularios.FRM_fecha', 'formularios.FK_EST_id', 'establecimientos.EST_nombre')
        ->leftJoin('establecimientos', 'establecimientos.EST_id', 'formularios.FK_EST_id' )
        ->where('FRM_id', $id)->first();

        foreach ($preguntas as $pregunta){
            DB::enableQueryLog();
            $e = DB::table('banco_preguntas as bp')->select( 'bp.BCP_id' , 'bp.BCP_pregunta', 'bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 'bp.BCP_complemento', 'bp.BCP_aclaracion', 'bp.FK_CAT_id', 'c2.CAT_categoria as categoria', 'c.CAT_categoria as subcategoria', 'c.FK_CAT_id as categoriaID')
            ->leftJoin('categorias as c', 'bp.FK_CAT_id',  'c.CAT_id')
            ->leftJoin('categorias as c2', 'c.FK_CAT_id', 'c2.CAT_id')
            ->where ('BCP_id', $pregunta->FK_BCP_id)
            ->where ('bp.estado', '1')
            ->orderBy('c.CAT_id')
            ->orderBy('bp.BCP_id')
            ->get();
            $quries = DB::getQueryLog();
            // dump( $pregunta->FK_BCP_id );

            if( count($e) ){
                array_push( $elementos, $e );
            }
            // array_push($elementos, $e);
        }
        return view('cuestionarios.cuestionario-imprimir', compact('elementos', 'formulario', 'id'));
    }

    /* Muestra los archivos adjuntos al cuestionario*/
    public function adjuntosFormulario($est_id, $frm_id = null){

        $formulario = ModFormulario::select('formularios.FRM_id', 'formularios.FRM_titulo', 'formularios.FRM_version', 'formularios.FRM_fecha', 'formularios.FK_EST_id', 'establecimientos.EST_nombre')
        ->leftJoin('establecimientos', 'establecimientos.EST_id', 'formularios.FK_EST_id' )
        ->where('FRM_id', $frm_id)->first();

        DB::enableQueryLog();

        $adj = ModAdjunto::from( 'adjuntos as ad' )
        ->select('ad.*', 'a.ARC_ruta', 'a.ARC_id', 'a.ARC_tipoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 'raa.FK_ADJ_id')
        ->leftjoin ('r_adjuntos_archivos as raa', 'ad.ADJ_id', 'raa.FK_ADJ_id')
        ->leftjoin ('archivos as a', 'raa.FK_ARC_id', 'a.ARC_id')
        ->leftjoin ('formularios as f', 'f.FRM_id', 'ad.FK_FRM_id')
        ->where ('f.FK_EST_id', $est_id);

        if( $frm_id ){
            $adjuntos = $adj->where ('ad.FK_FRM_id', $frm_id)->orderBy('ad.ADJ_id', 'desc')->get();
        }else{
            $adjuntos = $adj->orderBy('ad.ADJ_id', 'desc')->get();
        }

        $quries = DB::getQueryLog();
        dump($quries);
        exit;
        return view('formulario.formularios-adjuntos', compact('formulario', 'adjuntos'));
    }

    /* Adiciona nuevos archivos adjuntos por formulario (es diferente de las recomendaciones) */
    public function adjuntosNuevo(Request $request){
        // dump( $request->except('_token'));exit;

        $ADJ_responsables = json_encode($request->ADJ_responsables, JSON_FORCE_OBJECT);
        $ADJ_entrevistados = json_encode($request->ADJ_entrevistados, JSON_FORCE_OBJECT);
        $request->validate([
            'ADJ_titulo' => 'required',
            'ADJ_fecha' => 'required|max:200|min:5',
            'ADJ_responsables.*' => 'required|max:200|min:5',
            'ADJ_entrevistados.*' => 'required|max:200|min:5',
            'ADJ_resumen' => 'required|min:5',
            'ARC_archivo' => 'required',
            'ARC_archivo.*' => 'required|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:300548',
            'ARC_descripcion.*' => 'required',
        ], [
            'required' => '¡El dato es requerido!',
            'ARC_archivo.*.max' => '¡El archivos debe ser menor o igual a 300MB!',
            'ARC_archivo.*.mimes' => 'El archivos debe ser: imagen, documento, audio o video',
            'max' => '¡Dato muy extenso!',
            'min' => '¡Dato muy corto!',
        ]);

        DB::beginTransaction();
        try {
            /* Guarda los archivos subidos */
            if ( $request->file('ARC_archivo') ){
                $ARC_ids = [];
                foreach($request->file('ARC_archivo') as $key => $archivo){
                    $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );
                    //     dump( $tipoArchivo, $tipoArchivo[0] );
                    // exit;
                    $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/adjuntos'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipo' => 'adjunto', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                    array_push( $ARC_ids, $idArchivo->ARC_id );
                    $archivo->move(public_path('/uploads/adjuntos/'), $archivo->store(''));
                }
            }

            /* Guarda datos en la tabla adjuntos */
            $adjunto = ModAdjunto::create(['FK_FRM_id' => $request->FK_FRM_id, 'ADJ_titulo' => $request->ADJ_titulo, 'ADJ_fecha' => $request->ADJ_fecha, 'ADJ_responsables' => json_encode($request->ADJ_responsables, JSON_FORCE_OBJECT), 'ADJ_entrevistados' => json_encode($request->ADJ_entrevistados, JSON_FORCE_OBJECT), 'ADJ_resumen' => $request->ADJ_resumen]);

            $adjuntos_archivos = [];
            foreach ($ARC_ids as $key => $value) {
                array_push($adjuntos_archivos, [ 'FK_ARC_id' => $value, 'FK_ADJ_id' => $adjunto->ADJ_id ]);
            }
            ModAdjuntoArchivo::insert($adjuntos_archivos);
            DB::commit();
            return redirect('formulario/adjuntos/'.$request->FK_FRM_id)->with('status', '¡Datos almacenados con exito!');
        }
        catch (\Exception $e) {
            DB::rollback();
            exit ($e->getMessage());
        }
    }


}


