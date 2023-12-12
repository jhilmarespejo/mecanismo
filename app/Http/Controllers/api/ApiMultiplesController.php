<?php

namespace App\Http\Controllers\api;
use App\Models\{ModEstablecimiento, ModTipoEstablecimiento, ModFormulario,ModPreguntasFormulario};
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiMultiplesController extends Controller
{

    /**
     */
    public function ApiListarTiposEstablecimientos() {
        return ModTipoEstablecimiento::select('TES_id','TES_tipo' )->orderBy('TES_id')->get();
    }

    /*
    type: GET
    retorna todos los establecimientos incluyendo su tipo
    */
    public function ApiListarEstablecimientos() {

        $establecimientos = ModEstablecimiento::select('EST_nombre', 'tes.TES_tipo','tes.TES_id','EST_id',  'EST_direccion', 'EST_departamento', 'EST_provincia', 'EST_municipio' )
        //->from('establecimientos as e')
        ->leftjoin('tipo_establecimiento as tes', 'establecimientos.FK_TES_id','tes.TES_id' )
        // ->orderBy('TES_id' )
        ->orderBy('EST_nombre' )
        //->where( 'FK_TES_id', $request->FK_EST_id )
        ->get()->toArray();
        // $establecimientos = CustomController::array_group( $establecimientos, 'TES_tipo' );

        return $establecimientos;
    }

    /*
    type: GET
    retorna todos los formularios incluyendo sus preguntas y opciones
    */
    public function ApiFormulariosCuestionarios() {
        $results = ModEstablecimiento::from('r_bpreguntas_formularios as rbf')
        ->select('rbf.FK_FRM_id',  'rbf.FK_BCP_id', 'rbf.estado', 'rbf.RBF_orden','rbf.RBF_etiqueta',
        'bp.BCP_pregunta', 'bp.BCP_opciones', 'bp.BCP_tipoRespuesta', 'bp.BCP_complemento')
        ->join('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
        ->get()->toArray();

        return $results;
    }


    /*
    type: GET
    retorna todos los establecimientos incluyendo las visitas y formularios relacionados
    */
    public function ApiVisitasFormularios() {
        DB::enableQueryLog();
        /* Consulta para obtener los formularios aplicados en la visita*/
        $visitas = ModFormulario::from('formularios as f')
        ->select('f.FRM_id', 'v.VIS_id', 'e.EST_id', 'f.FRM_titulo' , 'f.FRM_fecha','v.VIS_titulo','v.VIS_fechas', 'v.VIS_numero','v.VIS_titulo', 'v.VIS_tipo', 'e.EST_nombre')
        ->leftjoin ('visitas as v', 'v.VIS_id', 'f.FK_VIS_id')
        ->leftjoin ('establecimientos as e', 'e.EST_id', 'v.FK_EST_id')
        ->get()->toArray();

        //$visitas_formularios = CustomController::array_group( $visitas, 'VIS_tipo' );
        // $quries = DB::getQueryLog();
        // print_r( $quries );

        return $visitas;
        //  $quries = DB::getQueryLog();
    }

    /*
    type: GET
    retorna la lista de formularios relacionadas con el establecimiento y la visita   */
    public function ApiHistorialformularios() {
        // DB::enableQueryLog();
         $historial = ModEstablecimiento::select( 'f.FRM_titulo','v.VIS_titulo', 'establecimientos.EST_nombre', 'f.FRM_tipo', 'f.FK_VIS_id','establecimientos.EST_id', 'v.VIS_tipo',   'f.FK_USER_id', 'f.FRM_orden' )
        ->join ('visitas as v', 'v.FK_EST_id', 'establecimientos.EST_id')
        ->join ('formularios as f', 'f.FK_VIS_id', 'v.VIS_id')
         ->get();
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
