<?php

namespace App\Http\Controllers;

use App\Models\{ModRecomendacion, ModVisita, ModBancoPregunta, ModFormulario, ModCategoria, ModTipoEstablecimiento, ModEstablecimiento};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Console\DumpCommand;
use Redirect,Response;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(){
        DB::enableQueryLog();
        // enviar los tipos de establecimientos al mapa

        $establecimientosPorTipo = ModTipoEstablecimiento::from('tipo_establecimientos as tes')->select('tes.TES_tipo','TES_id', DB::raw('COUNT("establecimientos"."EST_id") as total'))
        ->leftJoin('establecimientos', 'tes.TES_id', '=', 'establecimientos.FK_TES_id')
        ->groupBy('tes.TES_id', 'TES_id', 'tes.TES_tipo')
        ->get()->toArray();

        $porDepartamento  = ModEstablecimiento::from('establecimientos as e')->select(
            'e.EST_departamento',
            'tipo_establecimientos.TES_tipo',
            'tipo_establecimientos.TES_id',
            DB::raw('COUNT("e"."EST_id") as cantidad')
        )
        ->join('tipo_establecimientos', 'e.FK_TES_id', 'tipo_establecimientos.TES_id')
        ->groupBy('e.EST_departamento', 'tipo_establecimientos.TES_tipo', 'tipo_establecimientos.TES_id')
        ->orderBy('e.EST_departamento')
        ->get();


        $establecimientosPorDepartamento = [];

        foreach ($porDepartamento as $item) {
            $departamento = $item->EST_departamento;
            $tipoNombre = $item->TES_tipo;
            $tipoId = $item->TES_id;
            $cantidad = $item->cantidad;

            // Inicializa el departamento si no existe aún en el array
            if (!isset($establecimientosPorDepartamento[$departamento])) {
                $establecimientosPorDepartamento[$departamento] = [
                    'total' => 0,
                    'tipos' => []
                ];
            }

            // Sumar al total de establecimientos en el departamento
            $establecimientosPorDepartamento[$departamento]['total'] += $cantidad;

            // Agregar el tipo de establecimiento y su información al departamento correspondiente
            $establecimientosPorDepartamento[$departamento]['tipos'][] = [
                'TES_id' => $tipoId,
                'TES_tipo' => $tipoNombre,
                'cantidad' => $cantidad
            ];
        }
        $totalEstablecimientos = array_sum(array_column($establecimientosPorDepartamento, 'total'));
    // dump($establecimientosPorDepartamento, $establecimientosPorTipo);//exit;

    return view('index.panel', compact( 'establecimientosPorDepartamento', 'establecimientosPorTipo', 'totalEstablecimientos' ));
    }

    /* Busca los ids que coincidan con el nombre del formulario seleccionado */
    public function buscarIdFormulario( Request $request ){
        // dump($request->except('_token'));
        $ids = ModFormulario::select( 'FRM_id' )
        ->where( 'FRM_titulo', $request->formulario )
        ->get();

        foreach( $ids as $key=>$idFormulario ){
            $idFromularios[$key] = $idFormulario->FRM_id;
        }
        //$ids = json_encode( $ids );
        // dump( $idFromularios );

        // Busca las categorias que pertenecen al/los formularios seleccionados
        $categoriasFormulario = ModCategoria::from( 'categorias as c' )
        ->select( 'c.CAT_categoria', 'c.CAT_id' )
        ->leftJoin( 'banco_preguntas as bp', 'c.CAT_id', 'bp.FK_CAT_id' )
        ->leftJoin( 'r_bpreguntas_formularios as rbf', 'rbf.FK_BCP_id', 'bp.BCP_id' )
        ->leftJoin( 'formularios as f', 'rbf.FK_FRM_id', 'f.FRM_id' )
        ->whereIn( 'f.FRM_id', $idFromularios)
        ->groupBy( 'c.CAT_categoria', 'c.CAT_id' )
        ->orderBy( 'c.CAT_id' )
        ->get();

        // dump( $categoriasFormulario->toArray() );
        // exit;
        return view( 'index.index-responses', compact( 'categoriasFormulario', 'idFromularios' ) );
    }

    /* Busca todas las preguntas (con tipo de respuesta AFIRMACION) relacionadas a la categoria del formulario seleccionado  */
    public function busquedaDinamica( Request $request ){
        // dump($request->except('_token'));
        // exit;

        $idsForm = json_decode($request->formularios);
        $titulo = $request->nombreCategoria;
        DB::enableQueryLog();

        /* 1RO. Consulta para obtener una lista de las preguntas de tipo afirmacion (si/no), que pertenezcan al FORMULARIO y CATEGORIA seleccionada  */
        $afirmaciones =  ModBancoPregunta::from( 'banco_preguntas as bp' )
        ->select( DB::raw('DISTINCT("bp"."BCP_id") ') , 'bp.BCP_pregunta' )
        ->leftjoin( 'categorias as c', 'bp.FK_CAT_id', 'c.CAT_id' )
        ->leftjoin( 'r_bpreguntas_formularios as rbf', 'bp.BCP_id', 'rbf.FK_BCP_id' )
        ->where( 'c.CAT_id', $request->categoria )
        ->whereIn( 'rbf.FK_FRM_id', $idsForm )
        ->where( 'bp.BCP_tipoRespuesta', 'Afirmación' )
        ->get();
        $quries = DB::getQueryLog();
        dump( $quries, $idsForm );
        exit;

        /* 2DO. con la lista anterior se ensambla la 2da parte de la consulta */
        $opciones=''; $bcpIds='';
        foreach( $afirmaciones as $k=>$afirmacion ){
            // $opciones .= 'SUM( ("r"."RES_respuesta" = "Si")::int) as "si", SUM( ("r"."RES_respuesta" = "No")::int) as "no", SUM( ("r"."RES_respuesta" IS NULL)::int) as "nulo" ';
            $bcpIds .= $afirmacion->BCP_id.',';
        }
        //$opciones = rtrim($opciones, ",");
        $formularios = preg_replace("/[\[\]]/", "", $request->formularios);

        /* CONSULTA PARA RESPUESTAS DE TIPO AFIRMACION */
        /* 3RA parte (FINAL) se insertan las $opciones en la consulta final */
        if( $bcpIds != '' ){
            $consultaAfirmaciones = 'select "bp"."BCP_pregunta", SUM( ("r"."RES_respuesta" = \'Si\')::int) as "si", SUM( ("r"."RES_respuesta" = \'No\')::int) as "no", SUM( ("r"."RES_respuesta" IS NULL)::int) as "nulo" from "banco_preguntas" as "bp" left join "r_bpreguntas_formularios" as "rbf" on "rbf"."FK_BCP_id" = "bp"."BCP_id"
            left join "respuestas" as "r" on "r"."FK_RBF_id" = "rbf"."RBF_id"
            left join "formularios" as "f" on "f"."FRM_id" = "rbf"."FK_FRM_id" where "bp"."BCP_id" in ('.rtrim($bcpIds, ",").') and "f"."FRM_id" in ('.$formularios.') and "f"."estado" = 1 group by "bp"."BCP_pregunta"';


            $afirmaciones = DB::select( $consultaAfirmaciones );
        } else {
            $afirmaciones = null;
        }
        //echo($consultaAfirmaciones); //exit;

        $listaCasillas = 'select "bp"."BCP_id", "bp"."BCP_pregunta", "bp"."BCP_tipoRespuesta" from "banco_preguntas" as "bp" left join "r_bpreguntas_formularios" as "rbf" on "bp"."BCP_id" = "rbf"."FK_BCP_id" left join "formularios" as "f" on "rbf"."FK_FRM_id" = "f"."FRM_id" left join "establecimientos" as "e" on "f"."FK_EST_id" = "e"."EST_id" where "bp"."BCP_tipoRespuesta" in ('."'Casilla verificación', 'Lista desplegable'".') and "bp"."FK_CAT_id" = '.$request->categoria.' and "rbf"."FK_FRM_id" in ('.$formularios.') group by "bp"."BCP_id", "bp"."BCP_pregunta", "bp"."BCP_tipoRespuesta" order by "bp"."BCP_id"';

        $preguntas = DB::select($listaCasillas);

        // dump($preguntas); //exit;
        return view( 'index.index-responses', compact( 'afirmaciones', 'preguntas', 'formularios', 'titulo' ) );
    }

    public function buscarListasCasillas( Request $request ){
        // dump( $request->except('_tocken') );
        // exit;

        //se extraen las opciones por cada pregunta individual
        $opcionesPregunta = ModBancoPregunta::select('BCP_tipoRespuesta', 'BCP_opciones', 'BCP_pregunta')
        ->where('BCP_id', $request->BCP_id)
        ->get()->first()->toArray();
        $opcionesPregunta = json_decode( $opcionesPregunta['BCP_opciones'], true );

        $titulo = $opcionesPregunta['BCP_pregunta'];
        $elemento = '';

        foreach( $opcionesPregunta as $opcionPregunta ){
            $elemento .= 'SUM( ("r"."RES_respuesta" ilike '."'%".$opcionPregunta."%'".')::int) as "'.$opcionPregunta.'",';
        }

        $consultaListaCasillas = 'select "rbf"."FK_FRM_id", "e"."EST_nombre", '.rtrim($elemento, ",").'  from "respuestas" as "r" left join "r_bpreguntas_formularios" as "rbf" on "rbf"."RBF_id" = "r"."FK_RBF_id" left join "banco_preguntas" as "bp" on "bp"."BCP_id" = "rbf"."FK_BCP_id" left join "formularios" as "f" on "rbf"."FK_FRM_id" = "f"."FRM_id" left join "establecimientos" as "e" on "e"."EST_id" = "f"."FK_EST_id" where "rbf"."FK_BCP_id" = '.$request->BCP_id.' and "f"."FRM_id" in ('.$request->formularios.') group by "e"."EST_nombre", "rbf"."FK_FRM_id" ';
        dump( $consultaListaCasillas );//exit;

        $listaCasillas = DB::select( $consultaListaCasillas );

        return view( 'index.index-responses', compact( 'listaCasillas', 'titulo' ) );
        // exit;

        /* Hacer una consulta para obtener todas las opciones de respuesta del la pregunta seleccionada ya es que es de tipo  "Casilla de verificación" y "Lista desplegable" */
    }
}
