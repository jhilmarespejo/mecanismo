<?php

namespace App\Http\Controllers\api;
use App\Models\{ModEstablecimiento, ModTipoEstablecimiento, ModFormulario,ModPreguntasFormulario,ModRespuesta, ModAgrupadorFormulario};
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
    public function ApiFormulariosCuestionario() {
        $results = ModEstablecimiento::from('r_bpreguntas_formularios as rbf')
        ->select( 'rbf.RBF_id', 'rbf.FK_FRM_id', 'rbf.FK_BCP_id', 'bp.BCP_id', 'rbf.RBF_orden', 'rbf.estado', 'c.CAT_id', 'c2.CAT_id as CAT_subcat_id', 'bp.BCP_pregunta', 'bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 'bp.BCP_complemento', 'c.CAT_categoria as CAT_subcategoria', 'c2.CAT_categoria as CAT_categoria', 'f.FRM_titulo'
        )
        ->leftJoin ('formularios as f', 'f.FRM_id', 'rbf.FK_FRM_id')
        ->leftJoin ('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
        ->leftJoin ('categorias as c', 'bp.FK_CAT_id', 'c.CAT_id')
        ->leftJoin ('categorias as c2', 'c.FK_CAT_id', 'c2.CAT_id')
        ->where('rbf.estado', 1 )
        ->orderBy('rbf.RBF_id')
        ->orderBy('rbf.RBF_orden')
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

    public function ApiGuardarRespuestas(Request $request) {
        try {
            $datos = $request->all();
            // Obtener datos de la solicitud
            $agf = json_decode($datos['agrupador_formularios'], JSON_PRETTY_PRINT);
            $r = json_decode($datos['respuestas'], JSON_PRETTY_PRINT);

            DB::beginTransaction();

            // Guardar datos en la tabla 'agrupador_formularios' usando insert
            DB::table('agrupador_formularios')->insert($agf);

            // Guardar datos en la tabla 'respuestas' usando insert
            DB::table('respuestas')->insert($r);

            // Confirmar la transacción
            DB::commit();

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return response()->json(['error' => $e], 500);
        }

    }
}
