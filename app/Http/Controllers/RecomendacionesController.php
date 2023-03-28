<?php

namespace App\Http\Controllers;

use App\Models\{ModEstablecimiento, ModRecomendacion, ModArchivo, ModRecomendacionArchivo, ModVisita};
use Illuminate\Http\Request;
// use DB;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Validator;

class RecomendacionesController extends Controller{
    /**
     * Muesta una ventana donde se puede hacer seguimiento a las recomendaciones.
     * Se puede observas los detalles de cada recomendacion
     */
    /* Guarda las recomendaciones uno a uno */
    public function nuevaRecomendacion( Request $request ){
        $ids = [];
        // dump($request->except('_token'));//exit;

        $validator = Validator::make( $request->all(), [
            'REC_recomendacion' => 'required|min:5',
            'ARC_descripcion.*' => 'required|min:5',
            'ARC_archivo.*' => 'required|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:300548',
        ], [
            'required' => '¡El dato es requerido!',
            'ARC_archivo.*.max' => '¡El archivos debe ser menor o igual a 300MB!',
            'ARC_archivo.*.mimes' => 'El archivos debe ser: imagen, documento, audio o video',
            'max' => 'Dato muy extenso',
            'min' => 'Dato muy reducido',
            'ARC_descripcion.*.required' => 'Agregue una descripción',
        ]);

        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            DB::beginTransaction();
            try {
                if ( $request->file('ARC_archivo') ){
                    /* Array para guardar las imagenes */
                    foreach($request->file('ARC_archivo') as $key => $archivo ){
                        $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );
                        if( $tipoArchivo[0] == 'image'){
                            $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/recomendaciones'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipo' => 'recomemdacion', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                            array_push( $ids, $idArchivo->ARC_id );

                            /* GUARDA Y COMPRIME las imagenes en el bucle */
                            $image = Image::make($archivo->path());
                            $image->resize(null, 600, function ($const) {
                                $const->aspectRatio();
                            })->save( public_path('/uploads/recomendaciones/').$archivo->store('') );
                            // echo 'imagen';
                        /* Guarda los docmentos que no son imagenes */
                        } else {
                            $idArchivo = ModArchivo::create( ['ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/recomendaciones'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipo' => 'recomemdacion', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                            // $ruta = public_path('uploads/recomendaciones/');
                            // $nombre = $archivo->store('');
                            array_push( $ids, $idArchivo->ARC_id );
                            // dump($ruta.$nombre);
                            $archivo->move( public_path('uploads/recomendaciones/'),$archivo->store('') );
                            // echo 'archivo';
                        }
                        // dump($archivo);
                    }
                }
                /* Guarda la recomendacion enviada */
                $rec = ModRecomendacion::create( ['REC_recomendacion' => $request->REC_recomendacion, 'FK_VIS_id' => $request->VIS_id, 'REC_fechaRecomendacion' => date("d-m-Y h:i:s")] );

                /* Si existe mas de una imagen o archivo por recomendacion itera los id de los archivos para guardarlos en la tabla relacionada */
                foreach ($ids as $key => $value) {
                    ModRecomendacionArchivo::create(['FK_ARC_id' => $value, 'FK_REC_id' => $rec->REC_id]);
                }
                // dump($ids);
                // echo 'commit';
                // exit;
                DB::commit();
                return response()->json([ "success" => "Guardado correctamente" ]);
            }
            catch (\Exception $e) {
                DB::rollback();
                echo  $e;
            }
            // exit;
        }
    }


    public function recomendaciones( $VIS_id = null ){
        // dump($est_id, $frm_id);exit;
        $establecimiento = ModVisita::from('visitas as v')
        ->select( 'e.EST_nombre', 'e.EST_id' )
        ->leftJoin('establecimientos as e', 'e.EST_id', 'v.FK_EST_id')
        ->where( 'v.VIS_id', $VIS_id )
        ->first()->toArray();
        //$frm_id=null;

        DB::enableQueryLog();
        $recomendaciones = ModRecomendacion::select( 'recomendaciones.*', 'a.ARC_id','a.ARC_ruta', 'ra.FK_REC_id', 'a.ARC_descripcion', 'a.ARC_extension', 'a.ARC_tipo', 'a.ARC_tipoArchivo')
        ->leftJoin( 'r_recomendaciones_archivos as ra', 'ra.FK_REC_id', 'recomendaciones.REC_id')
        ->leftJoin( 'archivos as a', 'ra.FK_ARC_id', 'a.ARC_id')
        ->leftJoin( 'visitas as v', 'v.VIS_id', 'recomendaciones.FK_VIS_id')
        // ->leftJoin( 'establecimientos as e', 'e.EST_id', 'v.FK_EST_id' )
        ->where( 'v.VIS_id', $VIS_id )
        ->orderby('recomendaciones.REC_id', 'desc')
        ->get() ;

        /* Se ordenan los arrays de datos */
        $aux = null;
        $a = []; //array de recomendaciones
        $archivosRec = []; //array de archivos de cada recomendacion
        $archivosRecAcato = []; //archivos del acato a cada recomendacion

        foreach ($recomendaciones as $k=>$rec){
            if ( $aux != $rec->REC_id ) {
                array_push($a, ['REC_id' => $rec->REC_id, 'REC_recomendacion' => $rec->REC_recomendacion, 'REC_cumplimiento' => $rec->REC_cumplimiento, 'REC_fechaCumplimiento' => $rec->REC_fechaCumplimiento, 'REC_detallesCumplimiento' => $rec->REC_detallesCumplimiento, 'REC_fechaRecomendacion' => $rec->REC_fechaRecomendacion, 'REC_tipo' => $rec->REC_tipo, 'ARC_id' => $rec->ARC_id ] );
            } if( $rec->ARC_ruta != null ){
                if ($rec->ARC_tipo == 'recomemdacion') {
                    array_push( $archivosRec, ['REC_id' => $rec->REC_id, 'ARC_ruta' => $rec->ARC_ruta, 'ARC_id' => $rec->ARC_id, 'ARC_descripcion' => $rec->ARC_descripcion, 'ARC_extension' => $rec->ARC_extension, 'ARC_tipo' => $rec->ARC_tipo, 'ARC_tipoArchivo' =>  $rec->ARC_tipoArchivo, 'FK_REC_id' =>  $rec->FK_REC_id] );
                }
                if ($rec->ARC_tipo == 'acato-recomendacion') {
                    array_push( $archivosRecAcato, ['REC_id' => $rec->REC_id, 'ARC_ruta' => $rec->ARC_ruta, 'ARC_id' => $rec->ARC_id, 'ARC_descripcion' => $rec->ARC_descripcion, 'ARC_extension' => $rec->ARC_extension, 'ARC_tipo' => $rec->ARC_tipo, 'ARC_tipoArchivo' =>  $rec->ARC_tipoArchivo, 'FK_REC_id' =>  $rec->FK_REC_id] );
                }
            }
            $aux = $rec->REC_id;
        }

        return view('recomendaciones.recomendaciones', compact('a', 'archivosRec', 'archivosRecAcato', 'establecimiento', 'VIS_id'));


    }

    public function guardarCumplimientoRecomendaciones( Request $request ){
        // dump($request->except('_token'));exit;
        $validator = Validator::make( $request->all(), [
            'REC_fechaCumplimiento' => 'required|min:10',
            'REC_detallesCumplimiento' => 'required|min:10',
            'REC_cumplimiento' => 'required',
            // 'ARC_descripcion.*' => 'required',
        ], [
            'required' => 'El dato es necesario!!!!',
            'min' => 'Dato reducido',
        ]);
        if ( $validator->fails() ){
            // dump($validator->errors());
            return response()->json( [ 'errors'=>$validator->errors() ] );
        }

        // exit;
        DB::beginTransaction();
        try {
            if( $request->file('REC_archivo') ){
                $ids = [];
                foreach($request->file('REC_archivo') as $key => $archivo ){
                    $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );
                    if( $tipoArchivo[0] == 'image'){
                        $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );

                        $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(), 'ARC_ruta' => $archivo->store('/uploads/recomendaciones'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipo' => 'acato-recomendacion', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                        array_push( $ids, $idArchivo->ARC_id );
                        $image = Image::make($archivo->path());

                        /* Para redimensionar imagenes a 600px */
                        $staus = $image->resize(null, 600, function ($const) {
                            $const->aspectRatio();
                        })->save( public_path('/uploads/recomendaciones/').$archivo->store('') );
                    } else {
                        $idArchivo = ModArchivo::create( ['ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/recomendaciones'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipo' => 'acato-recomendacion', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                        array_push( $ids, $idArchivo->ARC_id );
                        $archivo->move(public_path('/uploads/recomendaciones/'), $archivo->store(''));
                    }
                }
                foreach ($ids as $key => $value) {
                    ModRecomendacionArchivo::create(['FK_ARC_id' => $value, 'FK_REC_id' => $request->REC_id]);
                }
            }
            /* ACTUALIZAR la recomendacion enviada */
            ModRecomendacion::where( 'REC_id', $request->REC_id )
            ->update( ['REC_cumplimiento' => $request->REC_cumplimiento, 'REC_fechaCumplimiento' => $request->REC_fechaCumplimiento , 'REC_detallesCumplimiento' =>$request->REC_detallesCumplimiento] );
            DB::commit();
            // ModRecomendacion::where( 'REC_id', $request->REC_id )
            // ->update( ['REC_cumplimiento' => $request->REC_cumplimiento] );

            // ModRecomendacion::create( ['REC_cumplimiento' => $request->REC_cumplimiento, 'REC_fechaCumplimiento' => $request->REC_fechaCumplimiento , 'REC_detallesCumplimiento' =>$request->REC_detallesCumplimiento, 'FK_REC_id' => $request->REC_id] );
            // DB::commit();
            // return response()->json( [ 'errors'=>'correcto' ] );
            return response()->json([ "success" => "Guardado correctamente" ]);
        }
        catch (\Exception $e) {
            DB::rollback();
            exit ($e->getMessage());
        }
    }


}
