<?php

namespace App\Http\Controllers;

use App\Models\{ModEstablecimiento, ModRecomendacion, ModArchivo, ModRecomendacionArchivo};
use Illuminate\Http\Request;
// use DB;
use Illuminate\Support\Facades\DB;
use Validator;
use Image;

class RecomendacionesController extends Controller{
    /**
     * Muesta una ventana donde se puede hacer seguimiento a las recomendaciones.
     * Se puede observas los detalles de cada recomendacion
     */


    /* Guarda las recomendaciones uno a uno */
    /* SE DEBEN PREPARAR ARRAY DE CADA ELEMENTO Y GUARDARLOS CON OPCION DE ROLLBACK */
    public function nuevaRecomendacion( Request $request ){
        $ids = [];
        // dump($request->except('_token'));//exit;

        $validator = Validator::make( $request->all(), [
            'REC_fechaRecomendacion' => 'required',
            'REC_recomendacion' => 'required|min:5',
            // 'REC_tipo' => 'required',
            'ARC_descripcion.*' => 'required|min:5',
            // 'ARC_archivo' => 'sometimes|required_with:ARC_descripcion.*',
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
                            $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/imagenes'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipo' => 'recomemdacion', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                            array_push( $ids, $idArchivo->ARC_id );

                            /* GUARDA Y COMPRIME las imagenes en el bucle */
                            $image = Image::make($archivo->path());
                            $image->resize(null, 600, function ($const) {
                                $const->aspectRatio();
                            })->save( public_path('/uploads/imagenes/').$archivo->store('') );

                        } else {
                            $idArchivo = ModArchivo::create( ['ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/documentos'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipo' => 'recomemdacion', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                            array_push( $ids, $idArchivo->ARC_id );
                            $archivo->move(public_path('/uploads/documentos/'), $archivo->store(''));
                        }
                    }
                }
                /* Guarda la recomendacion enviada */
                $rec = ModRecomendacion::create( ['REC_recomendacion' => $request->REC_recomendacion, 'FK_FRM_id' => $request->FRM_id, 'REC_fechaRecomendacion' => $request->REC_fechaRecomendacion] );

                /* Si existe mas de una imagen o archivo por recomendacion itera los id de los archivos para guardarlos en la tabla relacionada */
                foreach ($ids as $key => $value) {
                    ModRecomendacionArchivo::create(['FK_ARC_id' => $value, 'FK_REC_id' => $rec->REC_id]);
                }
                DB::commit();
                return response()->json([ "message" => "correcto" ]);
            }
            catch (\Exception $e) {
                DB::rollback();
                echo  $e;
            }
            // exit;
        }

    }


    public function recomendaciones( $est_id, $frm_id ){
        // dump($est_id, $frm_id);exit;
        $establecimiento = ModEstablecimiento::select( 'EST_nombre' )
        ->where( 'EST_id', $est_id )
        ->first();

        DB::enableQueryLog();
        $recomendaciones = ModRecomendacion::select( 'recomendaciones.*', 'a.ARC_id','a.ARC_ruta', 'ra.FK_REC_id', 'a.ARC_descripcion', 'a.ARC_extension', 'a.ARC_tipo', 'a.ARC_tipoArchivo', 'e.EST_id', 'FK_ARC_id')
        ->leftJoin( 'r_recomendaciones_archivos as ra', 'ra.FK_REC_id', 'recomendaciones.REC_id')
        ->leftJoin( 'archivos as a', 'ra.FK_ARC_id', 'a.ARC_id')
        ->leftJoin( 'formularios as f', 'f.FRM_id', 'recomendaciones.FK_FRM_id' )
        ->leftJoin( 'establecimientos as e', 'e.EST_id', 'f.FK_EST_id' )
        ->where( 'e.EST_id', $est_id )
        ->orderBy('recomendaciones.REC_id', 'desc')
        ->get();
        $quries = DB::getQueryLog();
        // dump( $quries );
        // exit;
        return view('establecimientos.establecimientos-recomendaciones',compact('recomendaciones', 'establecimiento', 'est_id', 'frm_id') );
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

                        $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/imagenes'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipo' => 'acato-recomendacion', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                        array_push( $ids, $idArchivo->ARC_id );
                        $image = Image::make($archivo->path());

                        /* Para redimensionar imagenes a 600px */
                        $staus = $image->resize(null, 600, function ($const) {
                            $const->aspectRatio();
                        })->save( public_path('/uploads/imagenes/').$archivo->store('') );
                    } else {
                        $idArchivo = ModArchivo::create( ['ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/documentos'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanyo' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_tipo' => 'acato-recomendacion', 'ARC_tipoArchivo' => $tipoArchivo[0] ] );

                        array_push( $ids, $idArchivo->ARC_id );
                        $archivo->move(public_path('/uploads/documentos/'), $archivo->store(''));
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
            return response()->json([ "message" => "correcto" ]);
        }
        catch (\Exception $e) {
            DB::rollback();
            exit ($e->getMessage());
        }
    }


}
