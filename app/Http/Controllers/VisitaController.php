<?php

namespace App\Http\Controllers;

use App\Models\{ModVisita};
use Illuminate\Http\Request;
use DB;
use Validator;

class VisitaController extends Controller
{

    // Guardar datos de nueva visita
    public function guardarNuevaVisita(Request $request) {
        $numeroVisita = ModVisita::select('FK_EST_id')
        ->where('FK_EST_id', $request->FK_EST_id)
        ->get()->count();

        $validator = Validator::make($request->all(), [
            'VIS_tipo' => 'required',
            // 'VIS_fechas' => 'required',
        ], [
            'required' => 'El dato es requerido!',
        ]);

        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            $datos = [];
            array_push($datos, ['FK_EST_id' => $request->FK_EST_id, 'VIS_tipo' => $request->VIS_tipo, 'VIS_fechas' => $request->VIS_fechas, 'VIS_numero' => ($numeroVisita+1)]);
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


    /* Consulta para obtener los formularios aplicados en la visita*/
    public function buscaFormularios( $id ){
        // DB::enableQueryLog();
        $formularios = ModVisita::from('visitas as v')
        ->select('f.FRM_id', 'f.FRM_titulo', 'f.FRM_version', 'f.FRM_fecha', 'f.FRM_tipoVisita', 'f.FK_VIS_id', 'e.EST_id', 'e.EST_nombre'/*, 'v.VIS_numero', 'v.VIS_tipo', 'v.VIS_fechas'*/)
        ->rightjoin ('establecimientos as e', 'v.FK_EST_id', 'e.EST_id')
        ->leftjoin ('formularios as f', 'f.FK_VIS_id', 'v.VIS_id')
        ->where ('f.FK_VIS_id', $id)
        ->where ('e.estado', '1')
        ->orderby('f.createdAt', 'desc')->get();
        // $quries = DB::getQueryLog();
        // dump( $quries );

        return view('formulario.formularios-lista', compact('formularios'));
        dump( $formularios->toArray() );

    }



}
