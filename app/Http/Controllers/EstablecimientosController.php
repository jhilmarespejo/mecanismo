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

        
    /**
     * listar
     *
     * @param  mixed $request
     * @return void
     * Hace una lista de establecimientos filtrando por tipo o sin tipo
     */
    public function listar(Request $request){
        $tiposEstablecimientos = DB::table('tipo_establecimientos')
            ->select('TES_id', 'TES_tipo')
            ->orderBy('TES_tipo')
            ->get();

        $FK_TES_id = $request->FK_TES_id;

        // DB::enableQueryLog();
        $establecimientos = ModEstablecimiento::from('establecimientos as e')
        ->select('e.EST_id','e.EST_nombre','e.EST_departamento','e.EST_municipio', 'e.EST_telefonoContacto', 'tes.TES_tipo')
        ->join('tipo_establecimientos as tes', 'e.FK_TES_id', 'tes.TES_id')
        ->where('e.estado', 1);
        if( $FK_TES_id != 'todo' && $FK_TES_id != null ){
            $establecimientos = $establecimientos->Where('e.FK_TES_id', $FK_TES_id);
        }
        $establecimientos = $establecimientos->get();
        // $quries = DB::getQueryLog();
        // dump ($quries);
        return view('establecimientos.establecimientos-responses', compact('establecimientos', 'tiposEstablecimientos', 'FK_TES_id'));
    }

        



        
    /**
     * guardarNuevoEstablecimiento
     *
     * @param  mixed $request
     * @return void
     * Guarda datos de nuevo establecimiento
     */
    public function guardarNuevoEstablecimiento(Request $request)  {
        //dump($request->except('_token'));//exit;
        $validator = Validator::make( $request->all(), [
            'EST_nombre' => 'required',
            'FK_TES_id' => 'required',
            'EST_departamento' => 'required',
            'EST_municipio' => 'required',
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

