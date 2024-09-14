<?php

namespace App\Http\Controllers;

use App\Models\{ModVisita, ModFormulario, ModBancoPregunta, ModEstablecimiento, ModRespuesta,ModArchivo, ModTipoEstablecimiento};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator, Auth, Session, URL, Storage};

use Intervention\Image\Facades\Image;
use App\Http\Controllers\{CustomController};
use Illuminate\Support\Arr;


use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\Style\ListItem;
// use PhpOffice\PhpWord\Style\addTableStyle;

class VisitaController extends Controller{

    // Guardar datos de nueva visita
    public function guardarNuevaVisita( Request $request ) {

        $validator = Validator::make($request->all(), [
            'VIS_tipo' => 'required',
            'VIS_fechas' => 'required|date|after_or_equal:today',
        ], [
            'required' => 'El dato es requerido!',
            'after_or_equal' => 'La fecha está en el pasado'
        ]);

        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            DB::beginTransaction();
            try {
                ModVisita::insert($request->except('_token'));
                DB::commit();
                // dump($request->except('_token'));exit;
                //return response()->json([ "message" => "¡Datos almacenados con exito!" ]);
            }catch (\Exception $e) {
                DB::rollback();
                exit ($e->getMessage());
            }
        }
    }



    /**
     * historial
     *
     * @param  mixed $id
     * @return void
     * Consulta para obtener el historial de las visitas realizadas al establecimiento
     */
    public function historial($id){

        DB::enableQueryLog();
        $visitas = ModVisita::from('visitas as v')
        ->select('v.VIS_id', 'v.VIS_fechas', 'v.VIS_tipo', 'v.VIS_titulo','e.EST_nombre', 'e.EST_id', 'tes.TES_tipo')
        ->rightJoin('establecimientos as e', 'e.EST_id', 'v.FK_EST_id')
        ->rightJoin('tipo_establecimientos as tes', 'tes.TES_id', 'e.FK_TES_id'  )
        ->where('e.EST_id', $id)
        ->get();
        // $quries = DB::getQueryLog();
        // dump($visitas); exit;

        session()->put('EST_id', $visitas->toArray()[0]['EST_id']);
        session()->put('EST_nombre', $visitas->toArray()[0]['EST_nombre']);
        session()->put('TES_tipo', $visitas->toArray()[0]['TES_tipo']);

        return view('visita.visita-historial', compact('visitas'));
    }

    /*Vista para guardar nueva acta de Visita */
    public function actaVisita( $VIS_id ){
        $visita = ModArchivo::select('ARC_formatoArchivo', 'ARC_ruta', 'ARC_extension', 'FK_VIS_id')
        ->where('FK_VIS_id', $VIS_id)
        ->get()->toArray();
        // dump( $visita); exit;


        return view('visita.acta-visita', compact('VIS_id','visita'));
    }

    public function guardarActaVisita( Request $request ){
        // dump($request->all());exit;
        $request->validate([
            'VIS_acta' => 'required|mimes:pdf,jpg,jpeg,png,xls,xlsx,ppt,pptx,doc,docx|max:20048',
        ], [
            'VIS_acta.required' => 'El archivo es necesario!!!!',
            'VIS_acta.max' => 'El archivo debe ser menor a 20Mb',
            'VIS_acta.mimes' => 'Puede subir archivos de imagen o PDF'
        ]);

        DB::beginTransaction();
        try {
            // Guardar el archivo en la tabla de archivos y en la carpeta actas
            $tipoArchivo =  explode( "/", $request->VIS_acta->getClientMimeType() );
            //Guarda datos del archivo en la tabla archivos
            ModArchivo::create( [ 'ARC_NombreOriginal' => $request->VIS_acta->getClientOriginalName(),'ARC_ruta' => $request->VIS_acta->store('/uploads/actas'), 'ARC_extension' => $request->VIS_acta->extension(), 'ARC_tamanio' => $request->VIS_acta->getSize(), 'ARC_descripcion' =>  'Acta de visita', 'ARC_formatoArchivo' =>  $tipoArchivo[0], 'FK_VIS_id' => $request->VIS_id, 'ARC_origen' => 'acta'] );


            if( $tipoArchivo[0] == 'image'){
                Image::make($request->VIS_acta)
                ->resize(null, 600, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('uploads/actas/').$request->VIS_acta->store(''));
            } else {
                $request->VIS_acta->move( public_path('uploads/actas/'),$request->VIS_acta->store('') );
            }


            DB::commit();
            return redirect()->back()->with('success', 'Correcto');
        }
        catch (\Exception $e) {
            DB::rollback();
            exit ($e->getMessage());
        }
    }

    public function resumen(Request $request) {
        $anioActual=0;
            if( is_null($request->anio_actual ) ){
                $anioActual = date('Y');
            } else {
                $anioActual = $request->anio_actual;
            }
            DB::enableQueryLog();

        $totalVisitas = DB::table('tipo_establecimientos as te')
            ->select('te.TES_tipo', 'e.EST_nombre', 'v.VIS_tipo', 'e.EST_id','v.VIS_fechas',
            DB::raw('COUNT(v."VIS_id") AS total_tipo_visitas'),
            DB::raw('SUM(COUNT(v."VIS_id")) OVER(PARTITION BY te."TES_tipo") AS total_tipo_establecimiento'),
            'total_establecimiento.total_establecimiento AS total_establecimiento')
            ->join('establecimientos as e', 'e.FK_TES_id', 'te.TES_id')
            ->join('visitas as v', 'v.FK_EST_id', 'e.EST_id')
            ->join(DB::raw('(SELECT e."EST_nombre",  COUNT(v."VIS_id") AS total_establecimiento
                            FROM establecimientos e
                            JOIN visitas v ON v."FK_EST_id" = e."EST_id"
                            GROUP BY e."EST_nombre") total_establecimiento'),
                    'total_establecimiento.EST_nombre', 'e.EST_nombre')
            ->leftJoin(DB::raw('(SELECT COUNT(v."VIS_id") AS total_general
                    FROM visitas v) total_general'), DB::raw('1'), '=', DB::raw('1'))
            ->whereYear('v.VIS_fechas', $anioActual)
            ->groupBy('te.TES_tipo', 'e.EST_nombre', 'e.EST_id', 'v.VIS_tipo', 'total_establecimiento.total_establecimiento', 'total_general.total_general', 'v.VIS_fechas')
            ->orderBy('te.TES_tipo')
            ->orderBy('e.EST_nombre')
            ->orderBy('e.EST_id')
            ->orderBy('v.VIS_tipo')
            ->addSelect(DB::raw('total_general.total_general AS total_general'))
            ->get();

     
            $totalVisitas = CustomController::agruparPorTipoYNombre($totalVisitas);
        $quries = DB::getQueryLog();
        // dump($totalVisitas); exit;
        return view('visita.visita-resumen', compact('totalVisitas', 'anioActual'));

    }


    // public function informeVisita( $VIS_id, $flag = null ){
        //     // dump($VIS_id);exit;

        //     /* DAtos para la el informe */
        //     $datos = ModVisita::from('visitas as v')
        //     ->distinct('f.FRM_titulo')
        //     ->select('f.FRM_titulo','v.VIS_tipo', 'v.VIS_titulo', 'e.EST_nombre', 'te.TES_tipo', 'v.VIS_urlActa', 'v.VIS_fechas')
        //     ->leftJoin('establecimientos as e', 'e.EST_id', 'v.FK_EST_id')
        //     ->leftJoin('tipo_establecimiento as te', 'te.TES_id', 'e.FK_TES_id')
        //     ->leftjoin('formularios as f', 'f.FK_VIS_id', 'v.VIS_id')
        //     ->where('v.VIS_id', $VIS_id)
        //     ->orderby('f.FRM_titulo')
        //     ->get();

        //     DB::enableQueryLog();

        //     /* Datos para archivos adjuntos (IMAGENES) relacionados con la VISITA */
        //     $imagenes = ModVisita::from('archivos as a')
        //     ->select('a.ARC_ruta', 'a.ARC_tipoArchivo', 'a.ARC_descripcion', 'a.ARC_extension' )
        //     ->leftJoin ('r_formularios_archivos as rfa', 'rfa.FK_ARC_id', 'a.ARC_id')
        //     ->leftJoin ('formularios as f', 'f.FRM_id', 'rfa.FK_FRM_id')
        //     ->where('f.FK_VIS_id', $VIS_id)
        //     ->where('a.ARC_tipoArchivo', 'image')
        //     ->get();

        //     $referencia = $datos->toArray()[0]['VIS_tipo'] .' '. $datos->toArray()[0]['VIS_titulo'].':';

        //     // $quries = DB::getQueryLog();

        //     /* preguntas y respuestas para ANALISIS */
        //     $preguntasAnalisis = $this->preguntasAnalisis( $VIS_id );
        //     // dump($preg@untasAnalisis);exit;


        //     /* Flag para descargar el informe */
        //     if( $flag == 0){
        //         return view('visita.informe-visita', compact('datos', 'referencia', 'imagenes', 'preguntasAnalisis', 'VIS_id'));
        //     }
        //     if( $flag == 1){
        //         // **************************************************************************
        //         $documento = new \PhpOffice\PhpWord\PhpWord();
        //         $propiedades = $documento->getDocInfo();
        //         $propiedades->setCreator("MNP-Bolivia");
        //         $propiedades->setTitle("Informe de visita");
        //         $documento->getSettings()->setThemeFontLang(new Language("ES-MX"));

        //         /* --- ESTILOS  ------------ */
        //         $documento->addTitleStyle(1, array('size' => 11, 'bold' => true, 'name' => 'Arial'));
        //         $documento->addTitleStyle(2, array('size' => 10.5, 'bold' => true, 'name' => 'Arial'));
        //         $bold = ['bold' => true, 'name' => 'Arial', 'size' => 11];
        //         $arial11 = ['name' => 'Arial', 'size' => 11];
        //         $estiloTabla = [
        //             'borderColor' => 'ffffff',
        //             'borderSize'  => 6,
        //             'cellMargin'  => 50,
        //         ];

        //         /* --- imagen ------------ */

        //         $seccion = $documento->addSection();
        //         // $seccion->addImage();

        //         $encabezado = $seccion->addHeader();
        //         $encabezado->addImage(URL::to('')."/img/logoinforme.png", [
        //             "width" => 100,
        //             "alignment" => Jc::CENTER,
        //         ]);

        //         $seccion->addText('INFORME DE VISITA', 1, ['alignment' => 'center', 'lineHeight' => 1,'size'=>11]);
        //         $seccion->addText('INF/DP/MNP/2023/...', 1, ['alignment' => 'center', 'lineHeight' => 1,'size'=>11]);
        //         /* --- TABLA  ------------ */
        //         // $seccion = $documento->addSection();


        //         // Guardarlo para usarlo más tarde
        //         $documento->addTableStyle("estilo1", $estiloTabla);

        //         $tabla = $seccion->addTable("estilo1"); # Agregar tabla con el estilo que guardamos antes
        //         $tabla->addRow(); # Agregar fila
        //         $tabla->addCell()->addText("A:",$bold);
        //         $tabla->addCell()->addText("... <w:br/> Delegado(a) Defensorial Departamental",  $arial11);
        //         $tabla->addRow(); # Agregar fila
        //         $tabla->addCell()->addText("De:",$bold);
        //         $tabla->addCell()->addText(Auth::user()->name."<w:br/>...",  $arial11);
        //         $tabla->addRow(); # Agregar fila
        //         $tabla->addCell()->addText("REFERENCIA:",$bold);
        //         $tabla->addCell()->addText( $referencia.' '.$datos->toArray()[0]['EST_nombre'].' - '.$datos->toArray()[0]['VIS_fechas'], $arial11);
        //         $tabla->addRow(); # Agregar fila
        //         $tabla->addCell()->addText("FECHA:",$bold);
        //         $tabla->addCell()->addText(date('d-m-Y'),  $arial11);

        //         $seccion->addLine(array('weight' => 1, 'width' => 450, 'height' => 0, 'color' => 000000));


        //         $seccion->addTitle('1. ANTECEDENTES:', 1);

        //         $seccion->addText("La Defensoría del Pueblo tiene un nuevo mandato como Mecanismo Nacional de Prevención de la Tortura del Estado Plurinacional de Bolivia (MNP), en cumplimiento de la Ley N° 1397 de 29 de septiembre de 2021 y el Protocolo Facultativo de la Convención Contra la Tortura y otros Tratos o Penas Crueles, Inhumanos o Degradantes, ratificado por Ley N° 3298 de 12 de diciembre de 2005.", $arial11, ["alignment" => Jc::BOTH]);
        //         $seccion->addTextBreak(1);

        //         $seccion->addTitle("2. DESARROLLO DE LA VISITA:", 1);
        //         $seccion->addText("El 3 de marzo de 2023 la Delegación Defensorial realizó un ingreso simultáneo a centros penitenciarios a nivel nacional, visitando:", $arial11, ["alignment" => Jc::BOTH]);

        //         $seccion->addText('* '. $datos->toArray()[0]['TES_tipo'].' de '. $datos->toArray()[0]['EST_nombre'], ['name' => 'Arial', 'size' => 13]);
        //         $seccion->addTextBreak(1);

        //         $seccion->addTitle("3. PROBLEMAS IDENTIFICADOS Y RECOMENDACIONES:",1);
        //         $seccion->addText("El 3 de marzo de 2023 la Delegación Defensorial realizó un ingreso simultáneo a centros penitenciarios a nivel nacional, visitando:", $arial11, ["alignment" => Jc::BOTH]);

        //         for ( $x = 0; $x < count($preguntasAnalisis); $x++ ){
        //             $opciones = json_decode($preguntasAnalisis[$x]["RES_respuesta"], JSON_PRETTY_PRINT) ;

        //             $seccion->addText($preguntasAnalisis[$x]["BCP_pregunta"], $arial11, ['indent' => 1]);

        //             if ($preguntasAnalisis[$x]["RES_respuesta"]){
        //                 if( json_last_error() ){
        //                     $seccion->addText($preguntasAnalisis[$x]["RES_respuesta"], $arial11, ['indent' => 2]);
        //                 }else {
        //                     if ( is_numeric($opciones) ){
        //                         $seccion->addText($opciones, $arial11, ['indent' => 2]);
        //                     }else{
        //                             for ($i = 0; $i < count($opciones); $i++){
        //                             $seccion->addText($opciones[$i], $arial11, ['indent' => 2]);
        //                             }
        //                     }
        //                 }
        //             }
        //             if ( $preguntasAnalisis[$x]["RES_complemento"] ){
        //                 $seccion->addText($preguntasAnalisis[$x]["RES_complemento"],1, ['indent' => 2]);
        //             }
        //         }


        //         if(  count($imagenes) > 0 ){
        //             $seccion->addTitle("IMÁGENES DE RESPALDO:", 2, ['indent' => 1] );

        //             foreach ( $imagenes as $k=>$imagen  ){
        //                 $seccion->addImage(URL::to('')."/".$imagen->ARC_ruta, [
        //                     "height" => 150,
        //                     "alignment" => Jc::CENTER,
        //                     ]
        //                 );
        //                 $seccion->addText('Imagen '.($k+1).'. '.$imagen->ARC_descripcion,1, ["alignment" => Jc::CENTER,] );
        //             }

        //         }
        //         $seccion->addTextBreak(1);
        //         $seccion->addTitle("4. RECOMENDACIONES:",1);
        //         // $seccion->addOLEObject(URL::to('')."/uploads/actas/0DATOS.xlsx");

        //         /* --- TABLA ------------ */


        //         # Para que no diga que se abre en modo de compatibilidad
        //         $documento->getCompatibility()->setOoxmlVersion(15);
        //         $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($documento, 'Word2007');
        //         $objWriter->save('Informe de visita.docx');

        //         return response()->download(public_path('Informe de visita.docx'));
        //     }

    // }

    // public function preguntasAnalisis($VIS_id ){
        //     /* *** Automatizar este proceso: */
        //     /* Segun las preguntas seleccionadas se realizan las siguientes consultas */
        //     /* Para la visita 1, que es la visita de pruebas se seleccionaron 2 formularios para evaluar respuestas y hacer el ananlisis en el informe de visita */

        //     $frmIds = [];
        //     $formularios = ['F-5. Muerte natural. Salud: Entrevista a personal de salud', 'F-2. Muerte violenta. Violencia: Entrevista a Jefe de Seguridad'];
        //     /*Busca los FRM_id de los formularios del array, guarda los FRM_id en frmIds */
        //     foreach($formularios as $k=>$formulario){
        //         DB::enableQueryLog();
        //         $form = ModFormulario::from ('formularios as f')
        //         ->select('f.FRM_id')
        //         ->where ( 'f.estado', 'completado' )
        //         ->where ( 'f.FK_VIS_id', $VIS_id )
        //         ->where ( 'f.FK_USER_id','>', 0 )
        //         ->where ( 'f.FRM_titulo', $formulario )
        //         ->first();
        //         if($form){
        //             array_push($frmIds, implode($form->toArray()));
        //         }
        //         // $quries = DB::getQueryLog();
        //     }

        //     $a = ModBancoPregunta::from ('banco_preguntas as bp')
        //     ->select('bp.BCP_id', 'bp.BCP_pregunta', 'r.RES_respuesta', 'r.RES_complemento')
        //     ->leftJoin ('r_bpreguntas_formularios as rbf', 'rbf.FK_BCP_id','bp.BCP_id')
        //     ->leftJoin ('respuestas as r', 'r.FK_RBF_id', 'rbf.RBF_id')
        //     ->leftJoin ('formularios as f', 'rbf.FK_FRM_id', 'f.FRM_id')
        //     ->whereIn ( 'f.FRM_id', $frmIds)
        //     ->whereIn ( 'bp.BCP_id', [1878, 1880, 1967, 1966])
        //     ->get()->toArray();

        //     $b = ModBancoPregunta::from ('banco_preguntas as bp')
        //     ->select( DB::raw('SUM( ("r"."RES_respuesta")::int ) as "muertes_naturales"'),)
        //     ->leftJoin ('r_bpreguntas_formularios as rbf', 'rbf.FK_BCP_id', 'bp.BCP_id')
        //     ->leftJoin ('respuestas as r', 'r.FK_RBF_id', 'rbf.RBF_id')
        //     ->leftJoin ('formularios as f', 'f.FRM_id', 'rbf.FK_FRM_id')
        //     ->whereIn ( 'bp.BCP_id', [2006,2007,2008,2009,2010,2011,2012] )
        //     ->where ( 'f.estado', 'completado')
        //     ->where ('f.FK_VIS_id', $VIS_id)
        //     ->get()->toArray();

        //     array_push($a, ["BCP_id" => null,
        //     "BCP_pregunta" => "Muertes naturales",
        //     "RES_respuesta" => implode($b[0]),
        //     "RES_complemento" => null]);
        //     return( $a );
    // }

    /* Esta función devuelve un color, el cual es asignado de acuerdo al tipo de visita  */



}


