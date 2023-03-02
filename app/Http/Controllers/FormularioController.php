<?php

namespace App\Http\Controllers;

use App\Models\{ModFormulario, ModFormularioArchivo, ModAdjunto, ModAdjuntoArchivo, ModArchivo, ModEstablecimiento};

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Image;
class FormularioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($rend = null){
        // DB::enableQueryLog();

        $formularios = ModFormulario::select('FRM_id', 'FRM_titulo', 'FRM_version', 'FRM_fecha')
            ->orderBy('FRM_id', 'Desc')->get();

        // $quries = DB::getQueryLog();
        //dump($quries);
        $response = 'index';
        if ($rend){
            $success = true;
            return view('formulario.formularios-responses', compact('formularios', 'response', 'success'));
        } else {
            return view('formulario.formularios-index', compact('formularios', 'response'));
        }

    }

    public function buscarEstablecimiento(Request $request){
        if($request->ajax()){

            $establecimientos = DB::table('establecimientos')
            ->select('establecimientos.EST_id','establecimientos.EST_nombre', 'c.CID_nombre as Ciudad')
            ->LeftJoin('ciudades as c', 'c.CID_id', 'establecimientos.FK_CID_id')
            ->where('establecimientos.EST_nombre', 'ilike', '%'.$request['establecimiento'].'%')->get();
            $response = 'establecimientos';
            return view('formulario.formularios-responses', compact('establecimientos', 'response'));
        }
    }

    public function buscaFormularios( $id ){

        DB::enableQueryLog();
        /* Consulta para obtener los formularios */
        $formularios = ModFormulario::from('formularios as f')
        ->select('f.FRM_id', 'f.FRM_titulo', 'f.FRM_version', 'f.FRM_fecha', 'f.FK_EST_id', 'f.FRM_tipoVisita', 'e.EST_id', 'e.EST_nombre', 'v.VIS_numero', 'v.VIS_tipo')
        ->leftjoin ('visitas as v', 'f.FK_VIS_id', 'v.VIS_id')
        ->rightjoin ('establecimientos as e', 'v.FK_EST_id', 'e.EST_id')
        ->where ('e.EST_id', $id)
        ->where ('e.estado', '1')
        ->orderby('f.createdAt', 'desc')->get();

        /*Consulta para obtener las recomendaciones */
        $recomendaciones = ModRecomendacion::from( 'recomendaciones as r' )
        ->select( 'e.EST_nombre','e.EST_id',
            DB::raw('SUM( ("r"."REC_cumplimiento" = 0)::int ) as "incumplido"'),
            DB::raw('SUM( ("r"."REC_cumplimiento" = 1)::int ) as "cumplido" '),
            DB::raw('SUM( ("r"."REC_cumplimiento" = 2)::int ) as "parcial" '),
            DB::raw('COUNT( ("r"."REC_id")::int ) as "total" ') )
        ->leftJoin( 'formularios as f', 'f.FRM_id', 'r.FK_FRM_id' )
        ->leftJoin( 'establecimientos as e', 'e.EST_id', 'f.FK_EST_id' )
        ->where( 'e.EST_id', $id )
        ->groupBy('e.EST_nombre','e.EST_id')->get();

        $quries = DB::getQueryLog();
        dump($quries);
        exit;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // dump($request->except('_token'));

        $validator = Validator::make($request->all(), [
            'FRM_titulo' => 'required|max:300',
            'FK_EST_id' => 'required',
            'FRM_version' => 'required',
            'FRM_tipoVisita' => 'required',
            'FRM_fecha' => 'required|min:10',
        ], [
            'required' => 'El dato es requerido!',
            'max' => 'Texto muy extenso!',
            'min' => 'Texto muy corto!',
        ]);
        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            DB::beginTransaction();
            try {
                ModFormulario::insert($request->except('_token'));
                DB::commit();
                return response()->json([ "message" => "¡Datos almacenados con exito!" ]);
            }catch (\Exception $e) {
                DB::rollback();
                exit ($e->getMessage());
            }
        }
    }

    /* Muestra en una nueva ventana los archivos adjuntos en cada formulario */
    // *************
    public function adjuntosFormulario($EST_id, $FRM_id = null){
        DB::enableQueryLog();

        // $formulario = ModFormulario::select('formularios.FRM_id', 'formularios.FRM_titulo', 'formularios.FRM_version', 'formularios.FRM_fecha', 'formularios.FK_EST_id', 'establecimientos.EST_nombre')
        // ->leftJoin('establecimientos', 'establecimientos.EST_id', 'formularios.FK_EST_id' )
        // ->where('FRM_id', $FRM_id)->first();
        // $establecimiento = ModEstablecimiento::select('EST_nombre')->where('EST_id', $EST_id)->get();

        // dump($establecimiento->toArray);exit;

        $adj = ModAdjunto::from( 'adjuntos as ad' )
        ->select('ad.*', 'a.ARC_ruta', 'a.ARC_id', 'a.ARC_tipoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 'raa.FK_ADJ_id', 'f.FRM_titulo', 'e.EST_nombre')
        ->leftjoin ('r_adjuntos_archivos as raa', 'ad.ADJ_id', 'raa.FK_ADJ_id')
        ->leftjoin ('archivos as a', 'raa.FK_ARC_id', 'a.ARC_id')
        ->leftjoin ('formularios as f', 'f.FRM_id', 'ad.FK_FRM_id')
        ->rightjoin ('establecimientos as e', 'e.EST_id', 'f.FK_EST_id')
        ->where ('e.EST_id', $EST_id);

        if( $FRM_id ){
            $adjuntos = $adj->where ('ad.FK_FRM_id', $FRM_id)->orderBy('ad.ADJ_id', 'desc')->get();
        }else{
            $adjuntos = $adj->orderBy('ad.ADJ_id', 'desc')->get();
        }

        $quries = DB::getQueryLog();
        // dump($quries);
        // exit;
        return view('formulario.formularios-adjuntos', compact('adjuntos', 'EST_id', 'FRM_id'));
    }

    /* Adiciona nuevos archivos adjuntos por formulario (es diferente de las recomendaciones) */
    public function adjuntosNuevo(Request $request){
        // dump( $request->except('_token'));exit;

        $ADJ_responsables = json_encode($request->ADJ_responsables, JSON_FORCE_OBJECT);
        $ADJ_entrevistados = json_encode($request->ADJ_entrevistados, JSON_FORCE_OBJECT);
        $request->validate([
            'ADJ_titulo' => 'required',
            // 'ADJ_fecha' => 'required|max:200|min:5',
            // 'ADJ_responsables.*' => 'required|max:200|min:5',
            // 'ADJ_entrevistados.*' => 'required|max:200|min:5',
            'ADJ_resumen' => 'required|min:5',
            'ARC_archivo' => 'required',
            'ARC_archivo.*' => 'required|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:300548',
            'ARC_descripcion.*' => 'nullable',
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
