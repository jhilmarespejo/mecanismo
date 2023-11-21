<?php

namespace App\Http\Controllers\api;
use App\Models\{ModEstablecimiento, ModTipoEstablecimiento};
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiMultiplesController extends Controller
{

    /**
     */
    public function ApiListarTiposEstablecimientos()
    {
        return ModTipoEstablecimiento::select('TES_id','TES_tipo' )->get();
    }

    /*
    type: GET
    retorna todos los establecimientos incluyendo su tipo
    */
    public function ApiListarEstablecimientos() {

        $establecimientos = ModEstablecimiento::select('EST_nombre', 'tes.TES_tipo','EST_id',  'EST_direccion', 'EST_departamento', 'EST_provincia', 'EST_municipio' )
        //->from('establecimientos as e')
        ->leftjoin('tipo_establecimiento as tes', 'establecimientos.FK_TES_id','tes.TES_id' )
        //->where( 'FK_TES_id', $request->FK_EST_id )
        ->get()->toArray();
        // $establecimientos = CustomController::array_group( $establecimientos, 'TES_tipo' );

        return $establecimientos;
    }


    /*
    type: GET
    retorna todos los establecimientos incluyendo las visitas relacionadas
    */
    public function ApiHistorialVisitas() {
        // DB::enableQueryLog();
         /* Consulta para obtener todas las visitas realizadas al establecimiento */
         $visitas = ModEstablecimiento::select('v.VIS_tipo', 'v.VIS_titulo','EST_nombre', 'EST_id',
         'v.VIS_id', 'v.VIS_numero', 'v.VIS_fechas')
         ->join('visitas as v', 'v.FK_EST_id','EST_id')
         ->get()->toArray();
        //  $quries = DB::getQueryLog();
        // print_r( $quries );
        // $visitas = CustomController::array_group( $visitas, 'EST_nombre' );

        return $visitas;

        /* Consulta para obtener los formularios aplicados en la visita*/
            // DB::enableQueryLog();
            //      $formularios = ModVisita::from('visitas as v')
            //      ->select('f.FRM_id', 'f.FRM_titulo', 'f.FRM_version', 'f.FRM_fecha', 'f.FK_USER_id', 'f.FK_VIS_id', 'e.EST_id', 'e.EST_nombre'/*, 'v.VIS_numero', 'v.VIS_tipo', 'v.VIS_fechas'*/)
            //      ->rightjoin ('establecimientos as e', 'v.FK_EST_id', 'e.EST_id')
            //      ->leftjoin ('formularios as f', 'f.FK_VIS_id', 'v.VIS_id')
            //      ->where ('e.EST_id', $id)
            //      ->where ('e.estado', '1')
            //      ->orderby('f.createdAt', 'desc')->get();
        //  $quries = DB::getQueryLog();
    }

    /*
    type: GET
    retorna la lista de formularios relacionadas con el establecimiento y la visita
    */
    public function ApiHistorialformularios() {
        // DB::enableQueryLog();
         $historial = ModEstablecimiento::select( 'f.FRM_titulo','v.VIS_titulo', 'establecimientos.EST_nombre', 'f.FRM_tipo', 'f.FRM_fecha', 'f.FK_VIS_id','establecimientos.EST_id', 'v.VIS_tipo',   'f.FK_USER_id', 'f.FRM_orden' )
        ->join ('visitas as v', 'v.FK_EST_id', 'establecimientos.EST_id')
        ->join ('formularios as f', 'f.FK_VIS_id', 'v.VIS_id')
         ->get()->toArray();
        // $historial = CustomController::array_group( $historial, 'EST_nombre' );
        //  $quries = DB::getQueryLog();
        // print_r( $quries );

        return $historial;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }



    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     //
    // }
}
