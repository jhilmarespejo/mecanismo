<?php

namespace App\Http\Controllers;

use App\Models\{ModVisita, ModFormulario};
use Illuminate\Http\Request;
use DB;
use Validator;
use Illuminate\Support\Facades\Auth;

class VisitaController extends Controller
{

    // Guardar datos de nueva visita
    public function guardarNuevaVisita(Request $request) {
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



}