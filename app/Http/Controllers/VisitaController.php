<?php

namespace App\Http\Controllers;

use App\Models\{ModVisita, ModFormulario, ModBancoPregunta, ModRespuesta};
use Illuminate\Http\Request;
use DB;
use Validator;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class VisitaController extends Controller
{  // Guardar datos de nueva visita
    public function guardarNuevaVisita( Request $request ) {
        $numeroVisita = ModVisita::select('FK_EST_id')
        ->where('FK_EST_id', $request->FK_EST_id)
        ->get()->count();

        $validator = Validator::make($request->all(), [
            'VIS_tipo' => 'required',
            'VIS_titulo' => 'required',
        ], [
            'required' => 'El dato es requerido!',
        ]);

        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            $datos = [];
            array_push($datos, ['FK_EST_id' => $request->FK_EST_id, 'VIS_tipo' => $request->VIS_tipo, 'VIS_fechas' => $request->VIS_fechas, 'VIS_numero' => ($numeroVisita+1), 'VIS_titulo' => $request->VIS_titulo]);
            DB::beginTransaction();
            try {
                ModVisita::insert( $datos );
                DB::commit();
                return response()->json([ "message" => "¡Datos almacenados con exito!" ]);
            }catch (\Exception $e) {
                DB::rollback();
                exit ($e->getMessage());
            }
        }
    }


    /* Consulta para obtener los formularios aplicados en la visita
        $id = Visita ID
    */
    public function buscaFormularios( $id ){
        DB::enableQueryLog();
        $z = 0;
        $r= 'select distinct on("f"."FRM_titulo") "f"."FRM_titulo", "f"."FRM_id", "f"."FK_VIS_id"
        from formularios f where "f"."FK_VIS_id" ='.$id.' and "f"."FK_USER_id" = \''.$z.'\' order by "f"."FRM_titulo", "f"."FRM_id"';

        $fs = DB::select( $r );
        $fs = json_decode(json_encode($fs), true);
        // dump($r);exit;

        $formularios = ModVisita::from('visitas as v')
        ->select('f.FRM_id', 'f.FRM_titulo', 'f.FRM_version', 'f.FRM_fecha', 'f.FK_USER_id', 'f.FK_VIS_id', 'f.estado', 'e.EST_id', 'e.EST_nombre'/*, 'v.VIS_numero', 'v.VIS_tipo', 'v.VIS_fechas'*/)
        ->rightjoin ('establecimientos as e', 'v.FK_EST_id', 'e.EST_id')
        ->leftjoin ('formularios as f', 'f.FK_VIS_id', 'v.VIS_id')
        ->where ('f.FK_VIS_id', $id)
        ->where ('e.estado', '1');
        if( Auth::user()->rol == 'Operador' ){
            $formularios = $formularios->where('f.FK_USER_id', Auth::user()->id);
        }
        $formularios = $formularios->orderby('f.createdAt', 'desc')
        ->orderby('f.FRM_titulo', 'asc')
        ->get();
        // $quries = DB::getQueryLog();
        // dump( $quries );

        // dump( $formularios->toArray() );
        // exit;
        return view('formulario.formularios-lista', compact('formularios', 'fs'));

    }

    /*Vista para guardar nueva acta de Visita */
    public function actaVisita( $VIS_id ){
        // dump( $id); exit;
        $visita = ModVisita::select('VIS_urlActa', 'FK_EST_id')
        ->where('VIS_id', $VIS_id)
        ->get()->toArray();

        return view('visita.acta-visita', compact('VIS_id','visita'));
    }

    public function guardarActaVisita( Request $request ){
        $request->validate([
            'VIS_acta' => 'required|mimes:pdf,jpg,jpeg,png,xls,xlsx,ppt,pptx,doc,docx|max:20048',
        ], [
            'VIS_acta.required' => 'El archivo es necesario!!!!',
            'VIS_acta.max' => 'El archivo debe ser menor a 20Mb',
            'VIS_acta.mimes' => 'Puede subir archivos de imagen o PDF'
        ]);

        DB::beginTransaction();
        try {
            $ruta = public_path('uploads/actas/');
            $nombre = $request->VIS_acta->store('');

            $tipoArchivo =  explode( "/", $request->VIS_acta->getClientMimeType() );
            ModVisita::where('VIS_id', $request->VIS_id)
            ->update(['VIS_urlActa' => $request->VIS_acta->store('/uploads/actas')]);

            if( $tipoArchivo[0] == 'image'){
                // dump( $tipoArchivo[0] );exit;
                Image::make($request->VIS_acta)
                ->resize(null, 550, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($ruta.$nombre);
            } else {
                $request->VIS_acta->move( $ruta, $nombre );
            }
            DB::commit();
            return redirect()->back()->with('success', 'Correcto');
        }
        catch (\Exception $e) {
            DB::rollback();
            //d( $e );
            exit ($e->getMessage());
        }
    }

    public function informeVisita( $VIS_id ){
        // dump($VIS_id);exit;

        /* DAtos para la el informe */
        $datos = ModVisita::from('visitas as v')
        ->distinct('f.FRM_titulo')
        ->select('f.FRM_titulo','v.VIS_tipo', 'v.VIS_titulo', 'e.EST_nombre', 'te.TES_tipo')
        ->leftJoin('establecimientos as e', 'e.EST_id', 'v.FK_EST_id')
        ->leftJoin('tipo_establecimiento as te', 'te.TES_id', 'e.FK_TES_id')
        ->leftjoin('formularios as f', 'f.FK_VIS_id', 'v.VIS_id')
        ->where('v.VIS_id', $VIS_id)
        ->orderby('f.FRM_titulo')
        ->get();

        DB::enableQueryLog();

        /* Datos para archivos adjuntos relacionados con la VISITA */
        $imagenes = ModVisita::from('archivos as a')
        ->select('a.ARC_ruta', 'a.ARC_tipoArchivo', 'a.ARC_descripcion', 'a.ARC_extension' )
        ->leftJoin ('r_formularios_archivos as rfa', 'rfa.FK_ARC_id', 'a.ARC_id')
        ->leftJoin ('formularios as f', 'f.FRM_id', 'rfa.FK_FRM_id')
        ->where('f.FK_VIS_id', $VIS_id)
        ->where('a.ARC_tipoArchivo', 'image')
        ->get();

        $referencia = 'Informe de '. $datos->toArray()[0]['VIS_tipo'] .'-'. $datos->toArray()[0]['VIS_titulo'];

        // $quries = DB::getQueryLog();

        /* preguntas y respuestas para ANALISIS */
        $preguntasAnalisis = $this->preguntasAnalisis( $VIS_id );
        // dump($preg@untasAnalisis);exit;

        return view('visita.informe-visita', compact('datos', 'referencia', 'imagenes', 'preguntasAnalisis'));

    }

    public function preguntasAnalisis($VIS_id ){
        /* *** Automatizar este proceso: */
        /* Segun las preguntas seleccionadas se realizan las siguientes consultas */
        /* Para la visita 1, que es la visita de pruebas se seleccionaron 2 formularios para evaluar respuestas y hacer el ananlisis en el informe de visita */

        $frmIds = [];
        $formularios = ['F-5. Muerte natural. Salud: Entrevista a personal de salud', 'F-2. Muerte violenta. Violencia: Entrevista a Jefe de Seguridad'];
        /*Busca los FRM_id de los formularios del array, guarda los FRM_id en frmIds */
        foreach($formularios as $k=>$formulario){
            DB::enableQueryLog();
            $form = ModFormulario::from ('formularios as f')
            ->select('f.FRM_id')
            ->where ( 'f.estado', 'completado' )
            ->where ( 'f.FK_VIS_id', $VIS_id )
            ->where ( 'f.FK_USER_id','>', 0 )
            ->where ( 'f.FRM_titulo', $formulario )
            ->first();
            if($form){
                array_push($frmIds, implode($form->toArray()));
            }
            // $quries = DB::getQueryLog();
        }

        $a = ModBancoPregunta::from ('banco_preguntas as bp')
        ->select('bp.BCP_id', 'bp.BCP_pregunta', 'r.RES_respuesta', 'r.RES_complemento')
        ->leftJoin ('r_bpreguntas_formularios as rbf', 'rbf.FK_BCP_id','bp.BCP_id')
        ->leftJoin ('respuestas as r', 'r.FK_RBF_id', 'rbf.RBF_id')
        ->leftJoin ('formularios as f', 'rbf.FK_FRM_id', 'f.FRM_id')
        ->whereIn ( 'f.FRM_id', $frmIds)
        ->whereIn ( 'bp.BCP_id', [1878, 1880, 1967, 1966])
        ->get()->toArray();

        $b = ModBancoPregunta::from ('banco_preguntas as bp')
        ->select( DB::raw('SUM( ("r"."RES_respuesta")::int ) as "muertes_naturales"'),)
        ->leftJoin ('r_bpreguntas_formularios as rbf', 'rbf.FK_BCP_id', 'bp.BCP_id')->leftJoin ('respuestas as r', 'r.FK_RBF_id', 'rbf.RBF_id')
        ->leftJoin ('formularios as f', 'f.FRM_id', 'rbf.FK_FRM_id')
        ->whereIn ( 'bp.BCP_id', [2006,2007,2008,2009,2010,2011,2012] )
        ->where ( 'f.estado', 'completado')
        ->where ('f.FK_VIS_id', $VIS_id)
        ->get()->toArray();

        array_push($a, ["BCP_id" => null,
        "BCP_pregunta" => "Muertes naturales",
        "RES_respuesta" => implode($b[0]),
        "RES_complemento" => null]);
        return( $a );
    }



}
