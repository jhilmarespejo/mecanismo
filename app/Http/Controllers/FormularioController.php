<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ModFormulario, ModFormularioArchivo, ModAdjunto, ModArchivo, ModEstablecimiento, ModVisita, ModBancoPregunta};
use Illuminate\Support\Facades\{DB, Auth, Redirect, Validator, Session};
use Intervention\Image\Facades\Image;
use App\Http\Controllers\{VisitaController, CustomController};


// use Illuminate\Support\Facades\Storage;

class FormularioController extends Controller
{
    /** Funcion para crear un formulario nuevo
     */
    public function eleccion($VIS_id, $VIS_tipo){

        // Si las variables de sesion no existen redireccionar al usuario al panel
        if( !session('TES_tipo') ){
            return redirect('panel');
        }
        DB::enableQueryLog();
        if( Auth::user()->rol == 'Administrador' ){
            $formularios = ModFormulario::select('FRM_id', 'FRM_titulo')->get();
            return view('formulario.formularios-eleccion', compact('formularios','VIS_tipo','VIS_id'));
        } else {
            return redirect('panel');
        }
    }

    public function nuevo(Request $request){

        // dump($request->all());exit;
        $validated = $request->validate([
            'opcion' => 'required',
            'FRM_id' => 'sometimes|required_if:opcion,asignar,anterior', // Regla de validación condicional
            'nuevo_formulario' => 'sometimes|required_if:opcion,nuevo',
        ], [
            'required' => 'Debe seleccionar una opción',
            'FRM_id.required_if' => 'Debe seleccionar un formulario',
            'nuevo_formulario.required_if' => 'Debe ingresar un nombre para el nuevo formulario',
        ]);

        if( $request->opcion == 'nuevo' ){
            //crear formulario desde 0

                // dump($preguntas);exit;
            return view('formulario.formulario-nuevo', ['nuevo_formulario' => $request->nuevo_formulario]);
        }
        elseif($request->opcion== 'anterior' ){
            // Tomar el valor del input nuevo_formulario y buscar formularios anteriores
            // buscar formularios segun el el valor del input nuevo_formulario en la tabla formularios y mostra sus categorias, preguntas y opcciones en pantalla para editar
            // dump($request->all());
            $formulario = ModFormulario::from('formularios as f')
            ->select('f.FRM_titulo',
            'bp.BCP_pregunta as Pregunta','bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 'bp.BCP_complemento',
            'categorias1.CAT_categoria as categoria',
            'categorias2.CAT_categoria as subcategoria')
            ->join('r_bpreguntas_formularios as rb', 'f.FRM_id', 'rb.FK_FRM_id')
            ->join('banco_preguntas as bp', 'rb.FK_BCP_id', 'bp.BCP_id')
            ->leftJoin('categorias as categorias1', 'bp.FK_CAT_id', 'categorias1.CAT_id')
            ->leftJoin('categorias as categorias2', 'categorias1.FK_CAT_id', 'categorias2.CAT_id')
            ->where('f.FRM_id', $request->FRM_id)
            ->get()->toArray();

            $preguntas_ordenadas = CustomController::ordenaPreguntasCategorias($formulario);
            $elementos_formulario = CustomController::array_group( $preguntas_ordenadas, 'subcategoria' );
            // dump($datos_agrupados);exit;
            return view('formulario.formulario-anterior', compact('elementos_formulario'));
        }

        elseif($request->opcion== 'asignar' ){
            //Tomar el valor del input nuevo_formulario y buscar formularios anteriores
            // visualizar todos sus datos y asignarle a esta visita
        }

    }
    public function buscarPregunta(Request $request){
        $preguntas = ModBancoPregunta::select(
            // 'categorias.CAT_categoria as categoria',
            // 'c_parent.CAT_categoria AS subcategoria',
            'banco_preguntas.BCP_pregunta',
            'banco_preguntas.BCP_tipoRespuesta',
            'banco_preguntas.BCP_opciones',
            'banco_preguntas.BCP_complemento',
            'banco_preguntas.BCP_id'
            // 'categorias.CAT_id',
            // 'categorias.FK_CAT_id'
            )
            // ->leftJoin('categorias', 'banco_preguntas.FK_CAT_id', '=', 'categorias.CAT_id')
            // ->leftJoin('categorias as c_parent', 'categorias.FK_CAT_id', '=', 'c_parent.CAT_id')
            ->where('banco_preguntas.BCP_pregunta', 'ilike', '%'.$request->pregunta.'%')
            ->where('banco_preguntas.estado', 1)
            ->orderBy('banco_preguntas.BCP_id')
            ->get()->toArray();
            // $preguntas = CustomController::ordenaPreguntasCategorias($preguntas);
            // dump( $preguntas ); exit;
            return response()->json($preguntas);

    }

    public function sugerenciasFormularios(Request $request){
        // $rastro = $request->nuevo_formulario
        // dump($request->nuevo_formulario);exit;

        $sugerencias_formularios = ModFormulario::select('FRM_id', 'FRM_titulo')->where('FRM_titulo', 'ILIKE', '%' . $request->nuevo_formulario . '%')->get();
        return response()->json($sugerencias_formularios);
        // dump( $sugerencias_formularios);exit;

    }


    /* Funcion para obtener los formularios aplicados en la visita
        $id = Visita ID
    */
    public function buscaFormularios( $VIS_id ){

        // dump($VIS_id );exit;
        //Obtener los formularios asociados con esta visita acompañado de las copias correspondientes
        $VIS_tipo = ModVisita::select('VIS_tipo')->where('VIS_id', $VIS_id)->first();


        DB::enableQueryLog();
        // $formularios = ModFormulario::from('formularios as f')
        // ->select('f.FRM_id','f.FRM_titulo','f.FRM_tipo','af.FK_VIS_id', 'af.AGF_id', 'af.estado', 'af.createdAt')
        // ->leftjoin('agrupador_formularios as af', 'af.FK_FRM_id', 'f.FRM_id')
        // ->where('af.FK_VIS_id', $VIS_id)
        // ->get()->toArray();


        $formularios =  ModFormulario::from('formularios as f')
        ->select('f.FRM_id','f.FRM_titulo','f.FRM_tipo','af.FK_VIS_id','af.AGF_id','af.estado','af.createdAt',
            DB::raw('(SELECT COUNT(*) FROM r_bpreguntas_formularios WHERE r_bpreguntas_formularios."FK_FRM_id" = f."FRM_id") AS preguntas'),
            DB::raw('(SELECT COUNT(*) FROM respuestas as r INNER JOIN r_bpreguntas_formularios as rb ON r."FK_RBF_id" = rb."RBF_id" WHERE af."AGF_id" = r."FK_AGF_id") AS respuestas')
        )
        ->leftJoin('agrupador_formularios as af', 'af.FK_FRM_id', 'f.FRM_id')
        ->where('af.FK_VIS_id', $VIS_id)
        ->get()->toArray();

        $quries = DB::getQueryLog();
        // dump( $quries );
        // exit;

        $grupo_formularios = CustomController::array_group($formularios, 'FRM_titulo');
        if(!session('EST_nombre')){
            dump('panel' );
            return redirect()->route('panel');
        }

        $VIS_tipo = $VIS_tipo->VIS_tipo;
        // dump( $VIS_tipo->VIS_tipo );exit;
        $colorVisita = CustomController::colorTipoVisita( $VIS_tipo );

        return view('formulario.formularios-lista', compact('grupo_formularios', 'colorVisita', 'VIS_id', 'VIS_tipo'));
    }

    public function store(Request $request){
        dump($request->all());

    }
     // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request) {
    //     // dump($request->except('_token'));

    //     $validator = Validator::make($request->all(), [
    //         'FRM_titulo' => 'required|max:300',
    //         'FK_EST_id' => 'required',
    //         'FRM_version' => 'required',
    //         'FRM_tipoVisita' => 'required',
    //         'FRM_fecha' => 'required|min:10',
    //     ], [
    //         'required' => 'El dato es requerido!',
    //         'max' => 'Texto muy extenso!',
    //         'min' => 'Texto muy corto!',
    //     ]);
    //     if ( $validator->fails() ){
    //         return response()->json( [ 'errors' => $validator->errors() ] );
    //     } else {
    //         DB::beginTransaction();
    //         try {
    //             ModFormulario::insert($request->except('_token'));
    //             DB::commit();
    //             return response()->json([ "message" => "¡Datos almacenados con exito!" ]);
    //         }catch (\Exception $e) {
    //             DB::rollback();
    //             exit ($e->getMessage());
    //         }
    //     }
    // }

    // // *** VERIFICAR EL LUGAR DONDE SE USA ESTA FUNCION
    // /* Muestra en una nueva ventana los archivos adjuntos en cada formulario */
    // public function adjuntosFormulario($EST_id, $FRM_id = null){
    //     DB::enableQueryLog();

    //     // $formulario = ModFormulario::select('formularios.FRM_id', 'formularios.FRM_titulo', 'formularios.FRM_version', 'formularios.FRM_fecha', 'formularios.FK_EST_id', 'establecimientos.EST_nombre')
    //     // ->leftJoin('establecimientos', 'establecimientos.EST_id', 'formularios.FK_EST_id' )
    //     // ->where('FRM_id', $FRM_id)->first();
    //     // $establecimiento = ModEstablecimiento::select('EST_nombre')->where('EST_id', $EST_id)->get();

    //     // dump($establecimiento->toArray);exit;

    //     $adj = ModAdjunto::from( 'adjuntos as ad' )
    //     ->select('ad.*', 'a.ARC_ruta', 'a.ARC_id', 'a.ARC_tipoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 'raa.FK_ADJ_id', 'f.FRM_titulo', 'e.EST_nombre')
    //     ->leftjoin ('r_adjuntos_archivos as raa', 'ad.ADJ_id', 'raa.FK_ADJ_id')
    //     ->leftjoin ('archivos as a', 'raa.FK_ARC_id', 'a.ARC_id')
    //     ->leftjoin ('formularios as f', 'f.FRM_id', 'ad.FK_FRM_id')
    //     ->rightjoin ('establecimientos as e', 'e.EST_id', 'f.FK_EST_id')
    //     ->where ('e.EST_id', $EST_id);

    //     if( $FRM_id ){
    //         $adjuntos = $adj->where ('ad.FK_FRM_id', $FRM_id)->orderBy('ad.ADJ_id', 'desc')->get();
    //     }else{
    //         $adjuntos = $adj->orderBy('ad.ADJ_id', 'desc')->get();
    //     }

    //     // $quries = DB::getQueryLog();
    //     // dump($quries);
    //     // exit;
    //     return view('formulario.formularios-adjuntos', compact('adjuntos', 'EST_id', 'FRM_id'));
    // }

    // // *** VERIFICAR EL LUGAR DONDE SE USA ESTA FUNCION
    // /* Adiciona nuevos archivos adjuntos por formulario (fotos, archivos, no videos) (es diferente de las recomendaciones)*/
    // public function adjuntosNuevo(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'ARC_archivo' => 'required|mimes:pdf,jpg,jpeg,png,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx|max:20048',
    //         'ARC_descripcion' => 'required',

    //     ], [
    //         'ARC_archivo.required' => 'El archivo requerido',
    //         'ARC_descripcion.required' => 'Se requere una breve descripción del archivo',
    //         'max' => 'El archivo debe ser menor a 20Mb',
    //         'mimes' => 'Puede subir archivos de imagen o PDF'
    //     ]);
    //     if ( $validator->fails() ){
    //         return response()->json( [ 'errors' => $validator->errors() ] );
    //     } else {
    //         DB::beginTransaction();
    //         try{

    //             $ruta = public_path('uploads/adjuntos/');
    //             $nombre = $request->ARC_archivo->store('');
    //             $tipoArchivo =  explode( "/", $request->ARC_archivo->getClientMimeType() );

    //             $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $request->ARC_archivo->getClientOriginalName(),'ARC_ruta' => $request->ARC_archivo->store('/uploads/adjuntos'), 'ARC_extension' => $request->ARC_archivo->extension(), 'ARC_tamanyo' => $request->ARC_archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion, 'ARC_tipo' => 'adjunto', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

    //             ModFormularioArchivo::create(['FK_FRM_id'=> $request->FK_FRM_id, 'FK_ARC_id' => $idArchivo->ARC_id ]);
    //             DB::commit();

    //             if( $tipoArchivo[0] == 'image'){
    //                 Image::make($request->ARC_archivo)
    //                 ->resize(450, null, function ($constraint) {
    //                     $constraint->aspectRatio();
    //                 })->save($ruta.$nombre);
    //             } else {
    //                 $request->ARC_archivo->move( $ruta, $nombre );
    //             }
    //             return response()->json( [ 'success' => 'Correcto!' ] );
    //         } catch (\Exception $e) {
    //             DB::rollback();
    //             exit ($e->getMessage());
    //         }
    //     }

    // }


}
