<?php

namespace App\Http\Controllers;

use App\Models\{ModEstablecimiento, ModFormulario, ModRecomendacion};
use Illuminate\Http\Request;
use DB;
use Validator;

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
            ->get();
        DB::enableQueryLog();
        $establecimientos = ModEstablecimiento::select('establecimientos.EST_id','establecimientos.EST_nombre','establecimientos.EST_direccion', 'establecimientos.EST_telefonoContacto', 'tes.TES_tipo', 'c.CID_nombre as Municipio', 'c2.CID_nombre as Provincia', 'c3.CID_nombre as Departamento')
            ->join('tipo_establecimiento as tes', 'establecimientos.FK_TES_id', 'tes.TES_id')
            ->leftJoin('ciudades as c', 'c.CID_id', 'establecimientos.FK_CID_id')
            ->leftJoin('ciudades as c2', 'c2.CID_id', 'c.FK_CID_id')
            ->leftJoin('ciudades as c3', 'c3.CID_id', 'c2.FK_CID_id')
            ->orWhere('establecimientos.FK_TES_id', 'ilike', '%' . $request->FK_TES_id . '%')
            //->where('establecimientos.EST_nombre',  'ilike', '%' . $this->buscarEstablecimiento . '%')
            ->where('establecimientos.estado', 1)
            ->orderBy('establecimientos.EST_id')
            // ->orderBy('establecimientos.EST_nombre')
            ->get();
            //->where('tes.TES_tipo','Centro Penitenciario' )
            //->orderby($this->ordenColumna, $this->ordenDireccion)
            //->paginate(5);
            $quries = DB::getQueryLog();
            // dump($quries);
            return view('establecimientos.establecimientos-responses', compact('establecimientos', 'tiposEstablecimientos'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ModEstablecimiento  $modEstablecimiento
     * @return \Illuminate\Http\Response
     */
    // public function show($id){
    public function historial($id){
        DB::enableQueryLog();
        $establecimientos = ModFormulario::select('formularios.FRM_id','formularios.FRM_titulo','formularios.FRM_version','formularios.FRM_fecha','formularios.FK_EST_id', 'establecimientos.EST_id', 'establecimientos.EST_nombre')
        ->rightJoin('establecimientos', 'formularios.FK_EST_id', 'establecimientos.EST_id')
        ->where('establecimientos.EST_id', $id)
        ->orderby('formularios.createdAt', 'desc')
        ->where('establecimientos.estado', '1')
        ->get();

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
        // dump($quries);
        // exit;

        return view('establecimientos.establecimientos-show', compact('establecimientos', 'id', 'recomendaciones'));

    }

    public function guardarNuevoEstablecimiento(Request $request)  {
        //dump($request->except('_token'));//exit;
        $validator = Validator::make( $request->all(), [
            'EST_nombre' => 'required',
            'EST_direccion' => 'required',
            'EST_telefonoContacto' => 'required',
            'FK_TES_id' => 'required',
            // 'FK_NSG_id' => 'required',
            // 'FK_CID_id' => 'required',
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
