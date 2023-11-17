<?php

namespace App\Http\Controllers\api;
use App\Models\{ModEstablecimiento, ModTipoEstablecimiento};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiEstablecimientosController extends Controller
{

    /**
     */
    public function ApiListarTiposEstablecimientos()
    {
        return ModTipoEstablecimiento::select('TES_id','TES_tipo' )->get();
    }

    public function ApiLlistarEstablecimientos( Request $request ) {
        return ModEstablecimiento::select('EST_id', 'EST_nombre', 'EST_direccion', 'EST_departamento', 'EST_provincia', 'EST_municipio' )
        ->where( 'FK_TES_id', $request->FK_EST_id )
        ->get();
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
