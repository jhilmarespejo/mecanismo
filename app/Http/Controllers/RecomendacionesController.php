<?php

namespace App\Http\Controllers;

use App\Models\{ModEstablecimiento, ModRecomendacion, ModSeguimientoRecomendacion, ModArchivo, ModRecomendacionArchivo, ModVisita};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Session};
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\CustomController;

class RecomendacionesController extends Controller{
    /**
     * Muesta una ventana donde se puede hacer seguimiento a las recomendaciones.
     * Se puede observas los detalles de cada recomendacion
     */
    /* Guarda las recomendaciones uno a uno */
    public function guardarNuevaRecomendacion( Request $request ){
        $ids = [];
        // dump($request->except('_token'));exit;

        $validator = Validator::make( $request->all(), [
            'REC_recomendacion' => 'required|min:5',
            'REC_autoridad_competente' => 'required|min:5',
            'ARC_descripcion.*' => 'required|min:5',
            'ARC_archivo.*' => 'required|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:30505 ', // 30 mb
        ], [
            'required' => '¡El dato es requerido!',
            'ARC_archivo.*.max' => '¡El archivos debe ser menor o igual a 30MB!',
            'ARC_archivo.*.mimes' => 'El archivos debe ser: imagen, documento, audio o video',
            'max' => 'Dato muy extenso',
            'min' => 'Dato muy reducido',
            'ARC_descripcion.required' => 'Agregue una descripción',
        ]);

        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            DB::beginTransaction();
            try {
                 /* Guarda la recomendacion enviada */
                //Verificar si la recomendacion es para el Estado o para un establecimiento
                if($request->VIS_estado){
                    $rec = ModRecomendacion::create( ['REC_recomendacion' => $request->REC_recomendacion, 'REC_estado' => $request->VIS_estado, 'REC_fechaRecomendacion' => date("d-m-Y h:i:s"), 'REC_autoridad_competente' => $request->REC_autoridad_competente] );
                }elseif($request->VIS_id){
                    $rec = ModRecomendacion::create( ['REC_recomendacion' => $request->REC_recomendacion, 'FK_VIS_id' => $request->VIS_id, 'REC_fechaRecomendacion' => date("d-m-Y h:i:s"), 'REC_autoridad_competente' => $request->REC_autoridad_competente] );
                }


                //  dump($REC->REC_id);exit;
                // verifica si el request trae un archivo
                if ( $request->file('ARC_archivo') ){
                    /* Crear Array para guardar las imagenes */
                    foreach($request->file('ARC_archivo') as $key => $archivo ){
                        $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );
                        // dump($tipoArchivo);
                        if( $tipoArchivo[0] == 'image'){
                            $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/recomendaciones'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanio' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_origen' => 'recomendaciones', 'ARC_formatoArchivo' => $tipoArchivo[0], 'FK_REC_id' => $rec->REC_id ] );

                            // array_push( $ids, $idArchivo->ARC_id );

                            /* GUARDA Y COMPRIME las imagenes en el bucle */
                            $image = Image::make($archivo->path());
                            $image->resize(null, 600, function ($const) {
                                $const->aspectRatio();
                            })->save( public_path('/uploads/recomendaciones/').$archivo->store('') );
                        /* Guarda los docmentos que no son imagenes */
                        } else {
                            $idArchivo = ModArchivo::create( ['ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/recomendaciones'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanio' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'ARC_origen' => 'recomendaciones', 'ARC_formatoArchivo' => $tipoArchivo[0], 'FK_REC_id' => $rec->REC_id ] );

                            array_push( $ids, $idArchivo->ARC_id );
                            $archivo->move( public_path('uploads/recomendaciones/'),$archivo->store('') );
                        }
                    }
                }
                DB::commit();
                return response()->json([ "success" => "Guardado correctamente" ]);
            }
            catch (\Exception $e) {
                dump($e);
                DB::rollback();
            }
            // exit;
        }
    }


    public function recomendaciones( $VIS_id ){
        // $EST_id = Session::get('EST_id');
        // $TES_tipo = Session::get('TES_tipo');
        // $EST_nombre = Session::get('EST_nombre');

        DB::enableQueryLog();

        $recomendaciones = ModRecomendacion::select('r.REC_id', 'r.REC_recomendacion', 'r.REC_fechaRecomendacion', 'r.REC_cumplimiento', 'r.REC_fechaCumplimiento', 'r.REC_autoridad_competente', 'a.ARC_id', 'a.FK_REC_id', 'a.ARC_descripcion', 'a.ARC_ruta', 'a.ARC_extension', 'a.ARC_formatoArchivo')
        ->from('recomendaciones as r')
        ->leftJoin('archivos as a', 'a.FK_REC_id', 'r.REC_id')
        ->where('r.FK_VIS_id', $VIS_id)
        ->orderBy('r.REC_id', 'desc')
        ->get()->toArray();

        $progresos = ModSeguimientoRecomendacion::select('sr.SREC_id', 'sr.SREC_descripcion','sr.SREC_fecha_seguimiento', 'sr.FK_REC_id', 'sr.SREC_autoridad_competente',  'a.ARC_id', 'a.ARC_formatoArchivo', 'a.ARC_descripcion', 'a.ARC_ruta', 'a.ARC_extension', 'a.FK_SREC_id')
        ->from('seguimiento_recomendaciones as sr')
        ->leftJoin('archivos as a', 'a.FK_SREC_id', 'sr.SREC_id')
        ->leftJoin('recomendaciones as r', 'r.REC_id', 'sr.FK_REC_id')
        ->where('r.FK_VIS_id', $VIS_id)
        ->get()->toArray();


        // $quries = DB::getQueryLog();

        // $progresos = CustomController::array_group( $progresos, 'FK_REC_id' );
        $progresos = CustomController::agruparSeguimientosImagenes( $progresos );
        $recomendaciones = CustomController::agruparRecomendacionesImagenes( $recomendaciones);

        // dump($progresos);
        // dump($recomendaciones);exit;
        // dump($a);
        // dump($archivosRec);//exit;
        return view('recomendaciones.recomendaciones', compact('recomendaciones', 'progresos', 'VIS_id'));
    }

    public function guardarCumplimientoRecomendaciones( Request $request ){
        // dump($request->except('_token'));exit;
        $validator = Validator::make( $request->all(), [
            'SREC_fecha_seguimiento' => 'required',
            'SREC_descripcion' => 'required|min:10',
            'REC_cumplimiento' => 'required',
            'ARC_archivo.*' => 'required|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:30905 ', // 30 mb
            // 'ARC_descripcion.*' => 'required',
        ], [
            'ARC_archivo.*.max' => '¡El archivos debe ser menor o igual a 30MB!',
            'required' => 'El dato es necesario!!!!',
            'min' => 'Dato reducido',
        ]);
        if ( $validator->fails() ){
            // dump($validator->errors());
            return response()->json( [ 'errors'=>$validator->errors() ] );
        }

        DB::beginTransaction();
        try {
             // Actualiza el estado de la recomendacion
             ModRecomendacion::where( 'REC_id', $request->REC_id )
             ->update( ['REC_cumplimiento' => $request->REC_cumplimiento] );

             /* CREAR UN NUEVO REGISTRO DE seguimiento a la recomendación */
            //  ModSeguimientoRecomendacion::create( ['SREC_descripcion' => $request->SREC_descripcion, 'SREC_fecha_seguimiento' => $request->SREC_fecha_seguimiento , 'FK_REC_id' => $request->REC_id] );
            $seguimiento = ModSeguimientoRecomendacion::create([
                'SREC_descripcion' => $request->SREC_descripcion,
                'SREC_fecha_seguimiento' => $request->SREC_fecha_seguimiento,
                'FK_REC_id' => $request->REC_id
            ]);
            $SREC_id = $seguimiento->SREC_id;
            // dump($seguimiento->SREC_id); exit;

            if( $request->file('REC_archivo') ){
                // $ids = [];
                foreach($request->file('REC_archivo') as $key => $archivo ){
                    $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );
                    if( $tipoArchivo[0] == 'image'){
                        $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );

                        $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(), 'ARC_ruta' => $archivo->store('/uploads/seguimiento_recomendaciones'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanio' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'FK_SREC_id' => $SREC_id, 'ARC_formatoArchivo' => $tipoArchivo[0] ] );

                        $image = Image::make($archivo->path());

                        /* Para redimensionar imagenes a 600px */
                        $staus = $image->resize(null, 600, function ($const) {
                            $const->aspectRatio();
                        })->save( public_path('/uploads/seguimiento_recomendaciones/').$archivo->store('') );
                    } else {
                        $idArchivo = ModArchivo::create( ['ARC_NombreOriginal' => $archivo->getClientOriginalName(),'ARC_ruta' => $archivo->store('/uploads/seguimiento_recomendaciones'), 'ARC_extension' => $archivo->extension(), 'ARC_tamanio' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'FK_SREC_id' => $SREC_id, 'ARC_formatoArchivo' => $tipoArchivo[0] ]);
                        // array_push( $ids, $idArchivo->ARC_id );
                        $archivo->move(public_path('/uploads/seguimiento_recomendaciones/'), $archivo->store(''));
                    }
                }
            }//try


            // // DB::commit();
            // // return response()->json( [ 'errors'=>'correcto' ] );
            DB::commit();
            return response()->json([ "success" => "Guardado correctamente" ]);
        }
        catch (\Exception $e) {
            DB::rollback();
            exit ($e->getMessage());
        }
    }

    public function recomendacionesEstatales(){
        DB::enableQueryLog();

        $recomendaciones = ModRecomendacion::select('r.REC_id', 'r.REC_recomendacion', 'r.REC_fechaRecomendacion', 'r.REC_cumplimiento', 'r.REC_fechaCumplimiento', 'r.REC_autoridad_competente', 'a.ARC_id', 'a.FK_REC_id', 'a.ARC_descripcion', 'a.ARC_ruta', 'a.ARC_extension', 'a.ARC_formatoArchivo')
        ->from('recomendaciones as r')
        ->leftJoin('archivos as a', 'a.FK_REC_id', 'r.REC_id')
        ->where('r.REC_estado', 'Si')
        ->orderBy('r.REC_id', 'desc')
        ->get()->toArray();

        $progresos = ModSeguimientoRecomendacion::select('sr.SREC_id', 'sr.SREC_descripcion','sr.SREC_fecha_seguimiento', 'sr.FK_REC_id', 'sr.SREC_autoridad_competente',  'a.ARC_id', 'a.ARC_formatoArchivo', 'a.ARC_descripcion', 'a.ARC_ruta', 'a.ARC_extension', 'a.FK_SREC_id')
        ->from('seguimiento_recomendaciones as sr')
        ->leftJoin('archivos as a', 'a.FK_SREC_id', 'sr.SREC_id')
        ->leftJoin('recomendaciones as r', 'r.REC_id', 'sr.FK_REC_id')
        ->where('r.REC_estado', "Si")
        ->get()->toArray();


        // $quries = DB::getQueryLog();

        $progresos = CustomController::agruparSeguimientosImagenes( $progresos );
        $recomendaciones = CustomController::agruparRecomendacionesImagenes( $recomendaciones);

        // dump($recomendaciones, $progresos);exit;

        return view('recomendaciones.recomendaciones-estatales', compact('progresos', 'recomendaciones'));
        //mostrar una ventana donde se realicen recomendaciones al estado
    }

    public function recomendacionesPorEstablecimiento(){
        //mostrar un rating, por cantidad de recomendaciones dadas por establecimiento
    }

}
