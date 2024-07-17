<?php

namespace App\Http\Controllers;

use App\Models\{ModEstablecimiento, ModTipoEstablecimiento,ModFormulario, ModRecomendacion, ModVisita};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EstablecimientosController extends Controller
{

    // Retorna los tipos de establecimientos, cuando se presiona el boton "Nuevo establecimiento"
    public function tipo( ){
        $tipos_establecimiento = ModTipoEstablecimiento::select('TES_id', 'TES_tipo' )->get();
        return response()->json($tipos_establecimiento);
    }

    /**
     * guardarNuevoEstablecimiento
     *
     * @param  mixed $request
     * @return void
     * Guarda datos de nuevo establecimiento
     */
    public function guardarNuevoEstablecimiento(Request $request)  {
        // dump($request->except('_token'));//exit;
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

    // Listar los establecimiento por tipo, segun FK_TES_id de la tabla establecimientos, escogido en el mapa

    public function listarSegunTipo(Request $request){
        $TES_id = $request->TES_id;
        $TES_tipo = $request->TES_tipo;
        $EST_departamento = $request->EST_departamento;

        DB::enableQueryLog();
        $establecimientos = ModEstablecimiento::from('establecimientos as e')
            ->select('e.EST_nombre','e.EST_id','e.EST_direccion','e.EST_municipio','e.EST_departamento','e.FK_TES_id',
                DB::raw('COUNT(v."VIS_id") AS cantidad_visitas')
            )
            ->leftJoin('visitas as v', 'v.FK_EST_id', 'e.EST_id')
            ->when($request->EST_nombre, function ($query) use ($request) {
                return $query->where('e.EST_nombre', 'ilike', '%' . $request->EST_nombre . '%');
            })
            ->when(!$request->EST_nombre, function ($query) use ($TES_id, $EST_departamento) {
                return $query->where('FK_TES_id', $TES_id)
                            ->where('EST_departamento', 'ilike', '%' . $EST_departamento . '%');
            })
            ->groupBy('e.EST_nombre', 'e.EST_id', 'e.EST_direccion', 'e.EST_municipio', 'e.EST_departamento', 'e.FK_TES_id')
            ->orderBy('EST_nombre')
            ->get();

            // $quries = DB::getQueryLog();
            // dump ($quries);

        return view('establecimientos.establecimientos-por-tipo', compact('establecimientos', 'TES_id', 'TES_tipo', 'EST_departamento'));
        // dump($establecimientos);exit;
    }

}

