<?php

namespace App\Http\Controllers;

use App\Models\{ModEstablecimiento, ModFormulario, ModRecomendacion, ModVisita};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EstablecimientosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(  ){
        return view('establecimientos.establecimientos-index');
    }

    public function listar(Request $request){
        // dump( $request->except('_token'));
        // exit;
        $tiposEstablecimientos = DB::table('tipo_establecimiento')
            ->select('TES_id', 'TES_tipo')
            ->orderBy('TES_tipo')
            ->get();

            $FK_TES_id = $request->FK_TES_id;

        DB::enableQueryLog();
        $establecimientos = ModEstablecimiento::from('establecimientos as e')
        ->select('e.EST_id','e.EST_nombre','e.EST_departamento','e.EST_provincia','e.EST_municipio', 'e.EST_telefonoContacto', 'tes.TES_tipo', 'c.CID_nombre as Municipio', 'c2.CID_nombre as Provincia', 'c3.CID_nombre as Departamento')
        ->join('tipo_establecimiento as tes', 'e.FK_TES_id', 'tes.TES_id')
        ->leftJoin('ciudades as c', 'c.CID_id', 'e.FK_CID_id')
        ->leftJoin('ciudades as c2', 'c2.CID_id', 'c.FK_CID_id')
        ->leftJoin('ciudades as c3', 'c3.CID_id', 'c2.FK_CID_id')
        ->where('e.estado', 1);
        if( $FK_TES_id != 'todo' && $FK_TES_id != null ){
            $establecimientos = $establecimientos->Where('e.FK_TES_id', $FK_TES_id);
        }

        //->orderBy('tes.TES_id', 'asc' )
        // ->orderBy('e.EST_nombre')
        $establecimientos = $establecimientos->get();
            $quries = DB::getQueryLog();
            // dump($quries);//exit;
            return view('establecimientos.establecimientos-responses', compact('establecimientos', 'tiposEstablecimientos', 'FK_TES_id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ModEstablecimiento  $modEstablecimiento
     * @return \Illuminate\Http\Response
     * $id = id del establecimiento seleccionado
     */
    // public function show($id){
    public function historial($id){
        /* Consulta para obtener todas las visitas realizadas al establecimiento */
        $visitas = ModVisita::select('VIS_id', 'VIS_numero', 'VIS_fechas', 'VIS_tipo', 'VIS_titulo')
        ->where('FK_EST_id', $id)
        ->get();

        /* Consulta para obtener los formularios aplicados en la visita*/
        // DB::enableQueryLog();
            $formularios = ModVisita::from('visitas as v')
            ->select('f.FRM_id', 'f.FRM_titulo', 'f.FRM_version', 'f.FRM_fecha', 'f.FK_USER_id', 'f.FK_VIS_id', 'e.EST_id', 'e.EST_nombre'/*, 'v.VIS_numero', 'v.VIS_tipo', 'v.VIS_fechas'*/)
            ->rightjoin ('establecimientos as e', 'v.FK_EST_id', 'e.EST_id')
            ->leftjoin ('formularios as f', 'f.FK_VIS_id', 'v.VIS_id')
            ->where ('e.EST_id', $id)
            ->where ('e.estado', '1')
            ->orderby('f.createdAt', 'desc')->get();
        $quries = DB::getQueryLog();
        // dump( $quries );

        /*Consulta para obtener las recomendaciones */
            $recomendaciones = ModRecomendacion::from( 'recomendaciones as r' )
            ->select( 'e.EST_nombre','e.EST_id',
                DB::raw('SUM( ("r"."REC_cumplimiento" = 0)::int ) as "incumplido"'),
                DB::raw('SUM( ("r"."REC_cumplimiento" = 1)::int ) as "cumplido" '),
                DB::raw('SUM( ("r"."REC_cumplimiento" = 2)::int ) as "parcial" '),
                DB::raw('COUNT( ("r"."REC_id")::int ) as "total" ') )
            ->leftJoin( 'visitas as v', 'v.VIS_id', 'r.FK_VIS_id' )
            ->leftJoin( 'establecimientos as e', 'e.EST_id', 'v.FK_EST_id' )
            ->where( 'e.EST_id', $id )
            ->groupBy('e.EST_nombre','e.EST_id')->get();

        return view('establecimientos.establecimientos-historial', compact('visitas','formularios', 'id', 'recomendaciones'));
    }


    public function guardarNuevoEstablecimiento(Request $request)  {
        //dump($request->except('_token'));//exit;
        $validator = Validator::make( $request->all(), [
            'EST_nombre' => 'required',
            'FK_TES_id' => 'required',
        ], [
            'required' => '¡El dato es requerido!',
            'required_if' => '¡El dato es requerido!',
        ]);

        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            DB::beginTransaction();
            try {
                ModEstablecimiento::insert($request->except('_token'));
                //GUARDAR DATOS
                DB::commit();
                return response()->json([ "message" => "¡Datos almacenados con exito!" ]);
                // return redirect('/categorias')->with('status', '¡Datos almacenados con exito!');

            }catch (\Exception $e) {
                DB::rollback();
                exit ($e->getMessage());
            }
        }
    }

}

