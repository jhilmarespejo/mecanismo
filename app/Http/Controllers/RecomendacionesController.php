<?php

namespace App\Http\Controllers;

use App\Models\{ModEstablecimiento, ModRecomendacion, ModSeguimientoRecomendacion, ModArchivo, ModRecomendacionArchivo, ModVisita};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Session};
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\CustomController;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecomendacionesController extends Controller{
   
    // Función que muestra la vista donde el usuario puede ver las recomendaciones o crear nuevas recomendaciones PARA UNA VISITA
    // metodo: GET
    // ruta: .../recomendaciones/{VIS_id} 
    public function recomendaciones( $VIS_id ){
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
        return view('recomendaciones.recomendaciones', compact('recomendaciones', 'progresos', 'VIS_id'));
    }
    

     /**
     * Esta función guarda una nueva recomendación para una visita específica
     */
    // metodo: POST
    // ruta: recomendaciones/guardarNuevaRecomendacion
    public function guardarNuevaRecomendacion( Request $request ){
        $ids = [];
        //dump($request->except('_token'));exit;

        $validator = Validator::make( $request->all(), [
            'REC_recomendacion' => 'required|min:5',
            'REC_autoridad_competente' => 'required|min:5',
            'ARC_descripcion.*' => 'required|min:5',
            'ARC_archivo.*' => 'required|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:24576 ', // el limite maximo es 24MB en bytes, pero le indicamos que es 20MB en el mensaje
        ], [
            'required' => '¡El dato es requerido!',
            'ARC_archivo.*.max' => '¡El archivos debe ser menor o igual a 20MB!',
            'ARC_archivo.*.mimes' => 'El archivos debe ser: imagen, documento, audio o video',
            'max' => 'Dato muy extenso',
            'min' => 'Dato muy reducido',
            'ARC_descripcion.required' => 'Agregue una descripción',
        ]);
        
        // Si el campo REC_fecha_recomendacion_estatal existe, agregar regla de fecha
        if ($request->filled('REC_fecha_recomendacion_estatal')) {
            $validator->addRules([
                'REC_fecha_recomendacion_estatal' => 'required|date'
            ]);
        }
        
        if ( $validator->fails() ){
            //dump($validator->errors());exit;
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            DB::beginTransaction();
            try {
                 /* Guarda la recomendacion enviada */
                //Verificar si la recomendacion es para el Estado o para un establecimiento
                if($request->VIS_estado){ // Recomendación para el Estado
                    $rec = ModRecomendacion::create( [
                        'REC_recomendacion' => $request->REC_recomendacion, 
                        'REC_estatal' => $request->VIS_estado, 
                        'REC_fechaRecomendacion' => $request->REC_fecha_recomendacion_estatal, 
                        'REC_autoridad_competente' => $request->REC_autoridad_competente,
                        'estado' => '1'] );
                }elseif($request->VIS_id){ // Recomendación para un establecimiento durante una visita
                    $rec = ModRecomendacion::create( [
                        'REC_recomendacion' => $request->REC_recomendacion, 
                        'FK_VIS_id' => $request->VIS_id, 
                        'REC_fechaRecomendacion' => $request->REC_fecha_recomendacion_estatal, 
                        'REC_autoridad_competente' => $request->REC_autoridad_competente,
                        'estado' => '1'] );
                }
                
                
                //  dump($REC->REC_id);exit;
                // verifica si el request trae un archivo
                if ( $request->file('ARC_archivo') ){
                    /* Crear Array para guardar las imagenes */
                    foreach($request->file('ARC_archivo') as $key => $archivo ){
                        $tipoArchivo =  explode( "/", $archivo->getClientMimeType() );
                        // dump($tipoArchivo);
                        if( $tipoArchivo[0] == 'image'){
                            // Usar store() para obtener la ruta
                             $rutaAlmacenada = $archivo->store('uploads/recomendaciones', 'public');
                            
                            
                            $idArchivo = ModArchivo::create( [ 
                                'ARC_NombreOriginal' => $archivo->getClientOriginalName(),
                                'ARC_ruta' => 'storage/' . $rutaAlmacenada, 
                                'ARC_extension' => $archivo->extension(), 
                                'ARC_tamanio' => $archivo->getSize(), 
                                'ARC_descripcion' =>  $request->ARC_descripcion[$key], 
                                'ARC_origen' => 'recomendaciones', 
                                'ARC_formatoArchivo' => $tipoArchivo[0], 
                                'FK_REC_id' => $rec->REC_id, 
                                'estado' => '1' 
                            ]);

                            // Procesar la imagen usando la ruta del archivo ya guardado
                            $rutaCompleta = storage_path('app/public/' . $rutaAlmacenada);
                            if (file_exists($rutaCompleta)) {
                                $image = Image::make($rutaCompleta);
                                $image->resize(null, 600, function ($const) {
                                    $const->aspectRatio();
                                })->save($rutaCompleta);
                            }
                        /* Guarda los docmentos que no son imagenes */
                        } else {
                            // Para archivos no-imagen
                            $rutaAlmacenada = $archivo->store('uploads/recomendaciones', 'public');
                            
                            $idArchivo = ModArchivo::create( [
                                'ARC_NombreOriginal' => $archivo->getClientOriginalName(),
                                'ARC_ruta' => 'storage/' . $rutaAlmacenada,  
                                'ARC_extension' => $archivo->extension(), 
                                'ARC_tamanio' => $archivo->getSize(), 
                                'ARC_descripcion' =>  $request->ARC_descripcion[$key], 
                                'ARC_origen' => 'recomendaciones', 
                                'ARC_formatoArchivo' => $tipoArchivo[0], 
                                'FK_REC_id' => $rec->REC_id, 
                                'estado' => '1' 
                            ]);

                            array_push( $ids, $idArchivo->ARC_id );
                        }
                    }
                }
                DB::commit();
                return response()->json([ "success" => "Almacenado correctamente" ]);
            }
            catch (\Exception $e) {
                dump($e);
                DB::rollback();
                return response()->json([ "error" => "Error al guardar: " . $e->getMessage() ]);
            }
            // exit;
        }
    }
    

    // Para una recomendación de una visita específica es posible hacer un seguimiento de los avances realizados para cumplir con ésta, cada recomendación de visita podria tener una serie de avances, estos avances se guardan en la tabla seguimiento_recomendaciones
    // metodo: POST
    // ruta: recomendaciones/guardarCumplimientoRecomendaciones
    
    public function guardarCumplimientoRecomendaciones( Request $request ){
        // dump($request->except('_token'));exit;
        $validator = Validator::make( $request->all(), [
            'SREC_fecha_seguimiento' => 'required',
            'SREC_descripcion' => 'required|min:10',
            'REC_cumplimiento' => 'required',
            'ARC_archivo.*' => 'required|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:24576 ', // el limite maximo es 24MB en bytes, pero le indicamos que es 20MB en el mensaje
            // 'ARC_descripcion.*' => 'required',
        ], [
            'ARC_archivo.*.max' => '¡El archivos debe ser menor o igual a 20MB!',
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
                        
                        $rutaAlmacenada = $archivo->store('uploads/seguimiento_recomendaciones', 'public');

                        $idArchivo = ModArchivo::create( [ 'ARC_NombreOriginal' => $archivo->getClientOriginalName(), 
                        'ARC_ruta' => 'storage/' . $rutaAlmacenada, 
                        'ARC_extension' => $archivo->extension(), 'ARC_tamanio' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'FK_SREC_id' => $SREC_id, 'ARC_formatoArchivo' => $tipoArchivo[0],'estado' => '1' ] );

                        $image = Image::make($archivo->path());

                        /* Para redimensionar imagenes a 600px */
                        $rutaCompleta = storage_path('app/public/' . $rutaAlmacenada);
                        $staus = $image->resize(null, 600, function ($const) {
                            $const->aspectRatio();
                        })->save($rutaCompleta);
                    } else {
                        $rutaAlmacenada = $archivo->store('uploads/seguimiento_recomendaciones', 'public');
                        $idArchivo = ModArchivo::create( ['ARC_NombreOriginal' => $archivo->getClientOriginalName(), 'ARC_ruta' => 'storage/' . $rutaAlmacenada, 'ARC_extension' => $archivo->extension(), 'ARC_tamanio' => $archivo->getSize(), 'ARC_descripcion' =>  $request->ARC_descripcion[$key], 'FK_SREC_id' => $SREC_id, 'ARC_formatoArchivo' => $tipoArchivo[0],'estado' => '1' ]);
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


    // Función que muestra la vista donde el usuario puede ver las recomendaciones o crear nuevas RECOMENDACIONES ESTATALES, estas recomendaciones NO tienen relacion con una VISITA en particular, son recomendaciones al gobierno boliviano y provienen del INFORME ANUAL del MNP
    // metodo: GET
    // ruta: .../recomendacionesEstatales
    public function recomendacionesEstatales(Request $request)
    {
        $anioActual = $request->anio_actual ?? date('Y');
        
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Recomendaciones informe anual', 'url' => ''],
        ];
        
        DB::enableQueryLog();
        
        // CORRECCIÓN: Usar leftJoin con condiciones en el join, no en el where
        $recomendaciones = ModRecomendacion::select(
                'r.REC_id', 
                'r.REC_recomendacion', 
                'r.REC_fechaRecomendacion', 
                'r.REC_cumplimiento', 
                'r.REC_fechaCumplimiento', 
                'r.REC_autoridad_competente',
                'a.ARC_id', 
                'a.FK_REC_id', 
                'a.ARC_descripcion', 
                'a.ARC_ruta', 
                'a.ARC_extension', 
                'a.ARC_formatoArchivo'
            )
            ->from('recomendaciones as r')
            // CORRECCIÓN: Mover condiciones del where al join
            ->leftJoin('archivos as a', function($join) {
                $join->on('a.FK_REC_id', '=', 'r.REC_id')
                    ->where('a.estado', '1'); // Solo archivos activos
            })
            ->where('r.REC_estatal', 'Si')
            ->where('r.estado', '1')
            ->whereYear('r.REC_fechaRecomendacion', $anioActual)
            ->orderBy('r.REC_id', 'desc')
            ->get()
            ->toArray();
        
        // CORRECCIÓN: Lo mismo para los progresos
        $progresos = ModSeguimientoRecomendacion::select(
                'sr.SREC_id', 
                'sr.SREC_descripcion',
                'sr.SREC_fecha_seguimiento', 
                'sr.FK_REC_id', 
                'sr.SREC_autoridad_competente',  
                'a.ARC_id', 
                'a.ARC_formatoArchivo', 
                'a.ARC_descripcion', 
                'a.ARC_ruta', 
                'a.ARC_extension', 
                'a.FK_SREC_id'
            )
            ->from('seguimiento_recomendaciones as sr')
            // CORRECCIÓN: Mover condiciones del where al join para archivos
            ->leftJoin('archivos as a', function($join) {
                $join->on('a.FK_SREC_id', '=', 'sr.SREC_id')
                    ->where('a.estado', '1'); // Solo archivos activos
            })
            ->leftJoin('recomendaciones as r', 'r.REC_id', 'sr.FK_REC_id')
            ->whereYear('r.REC_fechaRecomendacion', $anioActual)
            ->where('r.REC_estatal', 'Si')
            //->where('sr.estado', '1') // Asegurar que el seguimiento esté activo
            ->get()
            ->toArray();
        
        // Opcional: Ver la consulta SQL generada
        // $queries = DB::getQueryLog();
        // \Log::info('Consulta recomendaciones:', $queries);
        
        $progresos = CustomController::agruparSeguimientosImagenes($progresos);
        $recomendaciones = CustomController::agruparRecomendacionesImagenes($recomendaciones);
        
        return view('recomendaciones.recomendaciones-estatales', 
            compact('progresos', 'recomendaciones', 'breadcrumbs', 'anioActual')
        );
    }
    
    /**
     * Actualizar una recomendación estatal
     */

    public function actualizarRecomendacionEstatal(Request $request)
    {
        // Validación (mantener igual)
        $validator = Validator::make($request->all(), [
            'REC_id' => 'required|exists:recomendaciones,REC_id',
            'REC_recomendacion' => 'required|min:5',
            'REC_fechaRecomendacion' => 'required|date',
            'REC_cumplimiento' => 'nullable|in:0,1,2',
            'REC_autoridad_competente' => 'required|min:5',
            'ARC_archivo_nuevo.*' => 'nullable|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:24576 ', // el limite maximo es 24MB en bytes, pero le indicamos que es 20MB en el mensaje
            'ARC_descripcion_nuevo.*' => 'required_with:ARC_archivo_nuevo.*|min:5',
        ], [
            'required' => '¡El dato es requerido!',
            'ARC_archivo_nuevo.*.max' => '¡El archivo debe ser menor o igual a 20MB!',
            'ARC_archivo_nuevo.*.mimes' => 'El archivo debe ser: imagen, documento, audio o video',
            'min' => 'Dato muy reducido',
            'ARC_descripcion_nuevo.*.required_with' => 'La descripción es requerida para el archivo',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Actualizar la recomendación (igual)
            $recomendacion = ModRecomendacion::findOrFail($request->REC_id);
            $recomendacion->update([
                'REC_recomendacion' => $request->REC_recomendacion,
                'REC_fechaRecomendacion' => $request->REC_fechaRecomendacion,
                'REC_cumplimiento' => $request->REC_cumplimiento,
                'REC_autoridad_competente' => $request->REC_autoridad_competente,
                'updatedBy' => auth()->id(),
                'updatedAt' => now(),
            ]);

            // Eliminar archivos marcados para eliminar (igual)
            if ($request->has('archivos_eliminados')) {
                $archivosEliminar = array_filter($request->input('archivos_eliminados', []));
                if (!empty($archivosEliminar)) {
                    ModArchivo::whereIn('ARC_id', $archivosEliminar)
                        ->where('FK_REC_id', $recomendacion->REC_id)
                        ->where('estado', 1)
                        ->update([
                            'estado' => 0,
                            'updatedBy' => auth()->id(),
                            'updatedAt' => now(),
                        ]);
                }
            }
            
            // Agregar nuevos archivos
            if ($request->hasFile('ARC_archivo_nuevo')) {
                foreach ($request->file('ARC_archivo_nuevo') as $key => $archivo) {
                    $tipoArchivo = explode("/", $archivo->getClientMimeType());
                    
                    // Usar store() para obtener la ruta
                    $rutaAlmacenada = $archivo->store('uploads/recomendaciones', 'public');
                    
                    if ($tipoArchivo[0] == 'image') {
                        
                        // Para imágenes: Guardar en BD
                        $idArchivo = ModArchivo::create([
                            'ARC_NombreOriginal' => $archivo->getClientOriginalName(),
                            'ARC_ruta' => 'storage/' . $rutaAlmacenada,
                            'ARC_extension' => $archivo->extension(),
                            'ARC_tamanio' => $archivo->getSize(),
                            'ARC_descripcion' => $request->ARC_descripcion_nuevo[$key] ?? '',
                            'ARC_origen' => 'recomendaciones',
                            'ARC_formatoArchivo' => $tipoArchivo[0],
                            'FK_REC_id' => $recomendacion->REC_id,
                            'estado' => '1',
                            'createdBy' => auth()->id(),
                            'createdAt' => now(),
                        ]);
                        
                        // Procesar imagen DESPUÉS de guardarla
                        $rutaCompleta = storage_path('app/public/' . $rutaAlmacenada);
                        if (file_exists($rutaCompleta)) {
                            try {
                                $image = Image::make($rutaCompleta);
                                $image->resize(null, 600, function ($const) {
                                    $const->aspectRatio();
                                })->save($rutaCompleta);
                            } catch (\Exception $e) {
                                Log::warning('No se pudo procesar imagen: ' . $e->getMessage());
                            }
                        }
                    } else {
                        // Para archivos no-imagen
                        $idArchivo = ModArchivo::create([
                            'ARC_NombreOriginal' => $archivo->getClientOriginalName(),
                            'ARC_ruta' => 'storage/' . $rutaAlmacenada,
                            'ARC_extension' => $archivo->extension(),
                            'ARC_tamanio' => $archivo->getSize(),
                            'ARC_descripcion' => $request->ARC_descripcion_nuevo[$key] ?? '',
                            'ARC_origen' => 'recomendaciones',
                            'ARC_formatoArchivo' => $tipoArchivo[0],
                            'FK_REC_id' => $recomendacion->REC_id,
                            'estado' => '1',
                            'createdBy' => auth()->id(),
                            'createdAt' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            
            return response()->json([
                'success' => 'Recomendación actualizada correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar recomendación: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al actualizar la recomendación: ' . $e->getMessage()
            ], 500);
        }
    }
        


    /**
     * Eliminar una recomendación estatal (versión mínima)
     */
    public function eliminarEstatal(Request $request)
    {
        try {
            $request->validate([
                'REC_id' => 'required|exists:recomendaciones,REC_id'
            ]);
            
            $afectadas = ModRecomendacion::where('REC_id', $request->REC_id)
                ->where('estado', 1) // Solo si está activa
                ->update([
                    'estado' => 0,
                    'updatedBy' => auth()->id(),
                    'updatedAt' => now()
                ]);
            
            if ($afectadas === 0) {
                return response()->json([
                    'error' => 'La recomendación no existe o ya fue eliminada'
                ], 404);
            }
            
            return response()->json([
                'success' => 'Recomendación eliminada correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al procesar la solicitud'
            ], 500);
        }
    }
}
