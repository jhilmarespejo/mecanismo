<?php

namespace App\Http\Controllers;

// use App\Http\Livewire\Cuestionario;
use Illuminate\Http\Request;
use App\Models\{ModFormulario,ModRespuesta, ModCategoria, ModAdjunto, ModCuestionario, ModBancoPregunta, ModRecomendacion, ModEstablecimiento, ModArchivo, ModPreguntasFormulario, ModAgrupadorFormulario, ModRespuestaArchivo};
use Illuminate\Support\Facades\DB;
// use Image;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;
use App\Http\Controllers\{ CustomController};

// use Psy\Command\WhereamiCommand;

class CuestionarioController extends Controller {
    


/**
 * Muestra los resultados del cuestionario con estadísticas y gráficos corregidos
 * 
 * @param int $FRM_id ID del formulario
 * @return \Illuminate\View\View
 */
public function resultadosCuestionario($FRM_id)
{
    // Verificar permisos de usuario
    if (Auth::user()->rol != 'Administrador') {
        return redirect()->back()->with('warning', 'Usuario no autorizado para esta función');
    }
    
    // =========================== OBTENER COPIAS DEL FORMULARIO ===========================
    $copias = ModAgrupadorFormulario::from('agrupador_formularios as agf')
        ->select('f.FRM_titulo', 'agf.AGF_id', 'agf.AGF_copia')
        ->join('formularios as f', 'f.FRM_id', 'agf.FK_FRM_id')
        ->where('agf.FK_FRM_id', $FRM_id)
        ->get()->toArray();

    // Verificar si existen aplicaciones del formulario
    $totalAplicaciones = count($copias);
    
    if ($totalAplicaciones == 0) {
        return view('cuestionarios.cuestionario-resultado', [
            'resultados' => null,
            'FRM_titulo' => 'Formulario sin aplicaciones',
            'totalAplicaciones' => 0,
            'total' => 0, // Para compatibilidad con vista anterior
            'FRM_id' => $FRM_id,
            'estadisticas' => [
                'total_preguntas_reales' => 0,
                'total_aplicaciones' => 0,
                'aplicaciones_completas' => 0,
                'aplicaciones_incompletas' => 0,
                'porcentaje_completitud_general' => 0,
                'porcentaje_aplicaciones_completas' => 0,
                'total_respuestas_dadas' => 0,
                'tipos_preguntas' => [],
                'promedio_respuestas_por_aplicacion' => 0
            ],
            'VIS_id' => null
        ]);
    }

    $FRM_titulo = $copias[0]['FRM_titulo'];

    // =========================== OBTENER PREGUNTAS REALES DEL FORMULARIO ===========================
    // Solo contar preguntas que requieren respuesta (excluir secciones, subsecciones, etiquetas)
    $preguntasReales = ModBancoPregunta::from('banco_preguntas as bp')
        ->select(
            'bp.BCP_pregunta', 'bp.BCP_complemento', 'rbf.RBF_id', 'bp.BCP_id', 
            'bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 
            'c.CAT_id as categoriaID', 'c.CAT_categoria as subcategoria',
            'c.FK_CAT_id', 'c2.CAT_categoria as categoria'
        )
        ->join('r_bpreguntas_formularios as rbf', 'rbf.FK_BCP_id', 'bp.BCP_id')
        ->join('categorias as c', 'bp.FK_CAT_id', 'c.CAT_id')
        ->leftJoin('categorias as c2', 'c.FK_CAT_id', 'c2.CAT_id')
        ->where('rbf.FK_FRM_id', $FRM_id)
        ->whereNotIn('bp.BCP_tipoRespuesta', ['Sección', 'Subsección', 'Seccion', 'Subseccion', 'Etiqueta'])
        ->orderBy('rbf.RBF_orden')
        ->orderBy('rbf.RBF_id')
        ->get();

    $totalPreguntasReales = $preguntasReales->count();

    // =========================== CALCULAR COMPLETITUD POR APLICACIÓN ===========================
    $aplicacionesCompletas = 0;
    $aplicacionesIncompletas = 0;
    $totalRespuestasDadas = 0;

    foreach ($copias as $aplicacion) {
        $agfId = $aplicacion['AGF_id'];
        
        // Contar respuestas dadas para esta aplicación específica
        $respuestasDadaEnAplicacion = DB::table('respuestas as r')
            ->join('r_bpreguntas_formularios as rbf', 'rbf.RBF_id', 'r.FK_RBF_id')
            ->join('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
            ->where('r.FK_AGF_id', $agfId)
            ->where('rbf.FK_FRM_id', $FRM_id)
            ->whereNotIn('bp.BCP_tipoRespuesta', ['Sección', 'Subsección', 'Seccion', 'Subseccion', 'Etiqueta'])
            ->whereNotNull('r.RES_respuesta')
            ->where('r.RES_respuesta', '!=', '')
            ->where('r.RES_respuesta', '!=', 'null')
            ->count();

        $totalRespuestasDadas += $respuestasDadaEnAplicacion;

        // Determinar si la aplicación está completa
        if ($respuestasDadaEnAplicacion >= $totalPreguntasReales) {
            $aplicacionesCompletas++;
        } else {
            $aplicacionesIncompletas++;
        }
    }

    // =========================== CALCULAR PORCENTAJES ===========================
    $porcentajeCompletitudGeneral = $totalAplicaciones > 0 && $totalPreguntasReales > 0 
        ? round(($totalRespuestasDadas / ($totalAplicaciones * $totalPreguntasReales)) * 100, 1) 
        : 0;

    $porcentajeAplicacionesCompletas = $totalAplicaciones > 0 
        ? round(($aplicacionesCompletas / $totalAplicaciones) * 100, 1) 
        : 0;

    // =========================== PROCESAR RESPUESTAS PARA MOSTRAR EN LA VISTA ===========================
    // Obtener todas las preguntas (incluyendo secciones para la vista)
    $todasLasPreguntas = ModBancoPregunta::from('banco_preguntas as bp')
        ->select(
            'bp.BCP_pregunta', 'bp.BCP_complemento', 'rbf.RBF_id', 'bp.BCP_id', 
            'bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 
            'c.CAT_id as categoriaID', 'c.CAT_categoria as subcategoria',
            'c.FK_CAT_id', 'c2.CAT_categoria as categoria'
        )
        ->join('r_bpreguntas_formularios as rbf', 'rbf.FK_BCP_id', 'bp.BCP_id')
        ->join('categorias as c', 'bp.FK_CAT_id', 'c.CAT_id')
        ->leftJoin('categorias as c2', 'c.FK_CAT_id', 'c2.CAT_id')
        ->where('rbf.FK_FRM_id', $FRM_id)
        ->orderBy('rbf.RBF_orden')
        ->orderBy('rbf.RBF_id')
        ->get();

    // =========================== PROCESAR RESPUESTAS ABIERTAS ===========================
    $respuestasAbiertas = ModRespuesta::from('respuestas as r')
        ->select(
            'c.CAT_categoria', 'bp.BCP_pregunta', 'r.RES_respuesta', 
            'rbf.RBF_id', 'rbf.RBF_orden', 'r.FK_AGF_id', 'bp.BCP_tipoRespuesta'
        )
        ->rightJoin('r_bpreguntas_formularios as rbf', 'rbf.RBF_id', 'r.FK_RBF_id')
        ->leftJoin('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
        ->leftJoin('categorias as c', 'c.CAT_id', 'bp.FK_CAT_id')
        ->whereIn('bp.BCP_tipoRespuesta', ['Respuesta corta', 'Respuesta larga', 'Numeral'])
        ->where('rbf.FK_FRM_id', $FRM_id)
        ->groupBy(
            'c.CAT_categoria', 'bp.BCP_pregunta', 'rbf.RBF_orden', 
            'rbf.RBF_id', 'r.RES_respuesta', 'r.FK_AGF_id', 'bp.BCP_tipoRespuesta'
        )
        ->orderBy('rbf.RBF_orden')
        ->orderBy('rbf.RBF_id')
        ->get();

    // =========================== PROCESAR RESPUESTAS CERRADAS ===========================
    $respuestasAfirmacion = DB::select('
        SELECT "c"."CAT_categoria", "bp"."BCP_pregunta", 
               SUM(("r"."RES_respuesta" ilike \'%Si%\')::int) as "Si",
               SUM(("r"."RES_respuesta" ilike \'%No%\')::int) as "No", 
               "rbf"."RBF_id", "rbf"."RBF_orden", "bp"."BCP_tipoRespuesta"  
        FROM "respuestas" as "r"
        RIGHT JOIN "r_bpreguntas_formularios" as "rbf" ON "rbf"."RBF_id" = "r"."FK_RBF_id"
        LEFT JOIN "banco_preguntas" as "bp" ON "bp"."BCP_id" = "rbf"."FK_BCP_id"
        LEFT JOIN "categorias" as "c" ON "c"."CAT_id" = "bp"."FK_CAT_id"
        WHERE "bp"."BCP_tipoRespuesta" = \'Afirmación\' AND "rbf"."FK_FRM_id" = ?
        GROUP BY "c"."CAT_categoria", "bp"."BCP_pregunta", "rbf"."RBF_orden", 
                 "rbf"."RBF_id", "bp"."BCP_tipoRespuesta"
        ORDER BY "rbf"."RBF_orden", "rbf"."RBF_id"
    ', [$FRM_id]);

    // CONVERTIR OBJETOS stdClass A ARRAYS
    $arrayConteoRespAfir = array_map(function($obj) {
        return (array) $obj;
    }, $respuestasAfirmacion);

    // =========================== PROCESAR CASILLAS Y LISTAS DESPLEGABLES ===========================
    $arrayConteoRespCasVarif = [];

    foreach ($todasLasPreguntas as $pregunta) {
        if (in_array($pregunta->BCP_tipoRespuesta, ['Lista desplegable', 'Casilla verificación'])) {
            
            // Obtener opciones seleccionadas para esta pregunta
            $opcionesSeleccionadas = DB::table('respuestas as r')
                ->select('r.RES_respuesta')
                ->join('r_bpreguntas_formularios as rbf', 'rbf.RBF_id', 'r.FK_RBF_id')
                ->join('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
                ->where('rbf.FK_FRM_id', $FRM_id)
                ->where('rbf.FK_BCP_id', $pregunta->BCP_id)
                ->whereNotNull('r.RES_respuesta')
                ->groupBy('r.RES_respuesta')
                ->get()->toArray();

            $outputArray = array_map(function ($item) {
                return $item->RES_respuesta;
            }, $opcionesSeleccionadas);

            if (!empty($outputArray)) {
                $columnasOpciones = '';
                
                // Construir consulta SQL dinámica para contar opciones
                foreach ($outputArray as $opcionPregunta) {
                    if ($opcionPregunta == null) {
                        $opcionPregunta = 'Sin respuesta';
                    }
                    $etiqueta = str_replace(['[', ']', '"'], '', $opcionPregunta);
                    $columnasOpciones .= 'SUM(("r"."RES_respuesta" ilike \''.$opcionPregunta.'\')::int) as "'.str_replace(',', ' / ', $etiqueta).'",';
                }

                // Remover la última coma
                $columnasOpciones = rtrim($columnasOpciones, ',');

                // Ejecutar consulta para esta pregunta específica
                $respuestasCasillaVarif = DB::select('
                    SELECT "c"."CAT_categoria", "bp"."BCP_pregunta", '.$columnasOpciones.', 
                           "rbf"."RBF_id", "rbf"."RBF_orden", "bp"."BCP_tipoRespuesta" 
                    FROM "respuestas" as "r"  
                    RIGHT JOIN "r_bpreguntas_formularios" as "rbf" ON "rbf"."RBF_id" = "r"."FK_RBF_id" 
                    LEFT JOIN "banco_preguntas" as "bp" ON "bp"."BCP_id" = "rbf"."FK_BCP_id" 
                    LEFT JOIN "categorias" as "c" ON "c"."CAT_id" = "bp"."FK_CAT_id" 
                    WHERE "rbf"."FK_FRM_id" = ? AND "rbf"."FK_BCP_id" = ?
                    GROUP BY "c"."CAT_categoria", "bp"."BCP_pregunta", "rbf"."RBF_orden", 
                             "rbf"."RBF_id", "bp"."BCP_tipoRespuesta" 
                    ORDER BY "rbf"."RBF_orden", "rbf"."RBF_id"
                ', [$FRM_id, $pregunta->BCP_id]);

                // CONVERTIR A ARRAY Y AGREGAR SI NO ESTÁ VACÍO
                if (!empty($respuestasCasillaVarif)) {
                    $arrayConteoRespCasVarif[] = (array) $respuestasCasillaVarif[0];
                }
            }
        }
    }

    // =========================== UNIR Y PROCESAR RESULTADOS ===========================
    $resultados = array_merge($arrayConteoRespAfir, $arrayConteoRespCasVarif);

    // Filtrar elementos vacíos
    $resultados = array_filter($resultados, function($item) {
        return !empty($item) && isset($item['BCP_pregunta']);
    });

    // Agrupar respuestas cerradas
    $resultados = CustomController::agruparRespuestasCerradas($resultados);

    // Agrupar respuestas abiertas
    $arrayRespuestasAbiertas = CustomController::agruparRespuestasAbiertas(
        json_decode(json_encode($respuestasAbiertas), true)
    );
    
    // Combinar todos los resultados
    $resultados = array_merge($resultados, $arrayRespuestasAbiertas);

    // Ordenar por orden de pregunta
    usort($resultados, [CustomController::class, 'ordernarRespuestas']);

    // Agrupar por categorías
    $resultados = CustomController::array_group($resultados, 'CAT_categoria');

    // =========================== CALCULAR TIPOS DE PREGUNTAS ===========================
    $tiposPreguntas = [];
    foreach ($preguntasReales as $pregunta) {
        $tipo = $pregunta->BCP_tipoRespuesta;
        $tiposPreguntas[$tipo] = ($tiposPreguntas[$tipo] ?? 0) + 1;
    }

    // =========================== PREPARAR ESTADÍSTICAS CORREGIDAS PARA LA VISTA ===========================
    $estadisticas = [
        'total_preguntas_reales' => $totalPreguntasReales,
        'total_aplicaciones' => $totalAplicaciones,
        'aplicaciones_completas' => $aplicacionesCompletas,
        'aplicaciones_incompletas' => $aplicacionesIncompletas,
        'porcentaje_completitud_general' => $porcentajeCompletitudGeneral,
        'porcentaje_aplicaciones_completas' => $porcentajeAplicacionesCompletas,
        'total_respuestas_dadas' => $totalRespuestasDadas,
        'tipos_preguntas' => $tiposPreguntas,
        'promedio_respuestas_por_aplicacion' => $totalAplicaciones > 0 
            ? round($totalRespuestasDadas / $totalAplicaciones, 1) 
            : 0
    ];

    // =========================== OBTENER DATOS ADICIONALES PARA LA VISTA ===========================
    // Obtener información de la visita (si está disponible)
    $VIS_id = null;
    if (session('VIS_id')) {
        $VIS_id = session('VIS_id');
    } else {
        // Intentar obtener VIS_id desde la primera aplicación
        $primeraAplicacion = ModAgrupadorFormulario::where('FK_FRM_id', $FRM_id)->first();
        if ($primeraAplicacion) {
            $VIS_id = $primeraAplicacion->FK_VIS_id;
        }
    }

    // =========================== RETORNAR VISTA CON TODOS LOS DATOS CORREGIDOS ===========================
    return view('cuestionarios.cuestionario-resultado', compact(
        'resultados',
        'FRM_titulo', 
        'totalAplicaciones',
        'FRM_id',
        'estadisticas',
        'VIS_id'
    ))->with('total', $totalAplicaciones); // Agregar $total para compatibilidad total con la vista
}
    
    /* Muestra en forma de tabla vertical solo las respuetas del formulario seleccionado */
    // public function verCuestionario( $FRM_id ){
    //     exit('this->preguntasRespuestas($FRM_id)');
    //     // $elementos = $this->preguntasRespuestas($FRM_id);
    //     // return view('cuestionarios.cuestionario-ver', compact( 'elementos', 'FRM_id' ));
    // }


    public function duplicarCuestionario( $FRM_id, $VIS_id ){
        /* Obtiene la cantidad de copias realizadas (maximo) de un formulario. AGF_copia de latabla que agrupador_formularios */

        $max_FRM_version = ModAgrupadorFormulario::where('FK_FRM_id', $FRM_id)->where('FK_VIS_id', $VIS_id)->max( 'AGF_copia' );

        $FRM = ModFormulario::select('FRM_tipo', 'FK_VIS_id')->where( 'FRM_id', $FRM_id )->first();
        /* Se completa el array $nuevoFormulario */
        $nuevoFormulario['FK_FRM_id'] = $FRM_id;
        $nuevoFormulario['AGF_copia'] = $max_FRM_version+1;
        $nuevoFormulario['FK_VIS_id'] = $VIS_id;

        /* Si el formulario es de TIPO 1, puede duplicarse solo una vez */
        if( is_null($max_FRM_version) && ($FRM->FRM_tipo == '1' ) ){
            $resultado = $this->fn_duplicar_cuestionario( $nuevoFormulario );
        } elseif( $FRM->FRM_tipo == 'N' ){
            // dump("Multiple aplicacion");
            $resultado = $this->fn_duplicar_cuestionario( $nuevoFormulario );
        
        }else{
            $resultado = 0;
        }
        if ($resultado ==0) {
            return redirect('/formulario/buscaFormularios/' . $VIS_id)->with('warning','Este formulario solo puede duplicarse una vez');
        } else {
            return redirect('/formulario/buscaFormularios/' . $VIS_id);
        }
    }


    /*  return > 0: se guardó el dato correctamente
        return -1: Error al guardar el dato
    */
    public function fn_duplicar_cuestionario( $nuevoFormulario ){
        DB::beginTransaction();
        try {
            ModAgrupadorFormulario::insert( $nuevoFormulario );
            $ultimoFRMid = DB::getPdo()->lastInsertId();
            DB::commit();
            return $ultimoFRMid;//redirect('/cuestionario/responder/'.$FRM_id.'/'.$ultimoFRMid);
        }
        catch (\Exception $e) {
            DB::rollback();
            // return $e->getMessage();
            return -1;
        }
    }

    /* Elimina el cuestionario duplicado */
    public function eliminarCuestionario( Request $request ){
        if(Auth::user()->rol == 'Administrador' ){

            DB::beginTransaction();
            try {
                // DB::enableQueryLog();
                // dump($request);
                ModRespuesta::where('FK_AGF_id', $request->AGF_id)->delete();
                // $quries = DB::getQueryLog();
                // dump( $quries );
                // exit;
                ModAgrupadorFormulario::where('AGF_id', $request->AGF_id)->delete();

                DB::commit();
                return redirect()->back()->with('success', 'Eliminado correctamente');
            }
            catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('warning', 'No se pudo eliminar el formulario');
                exit ($e->getMessage());
            }
        }
    }

    public function preguntasRespuestas( $FRM_id, $AGF_copia){

        // $quries = DB::getQueryLog();
        // dump( $quries );
        // exit;
        // return $elementos;
    }

    //***VERIF */
    public function buscarRecomendaciones( Request $request ){
        // dump($request->except('_token'));exit;
        $id = $request->id;

        DB::enableQueryLog();
        $recomendaciones = ModRecomendacion::select( 'recomendaciones.REC_id', 'recomendaciones.REC_recomendacion', 'recomendaciones.FK_FRM_id', 'archivos.ARC_ruta' )
        ->leftJoin('r_recomendaciones_archivos as ra', 'ra.FK_REC_id', 'recomendaciones.REC_id')
        ->leftJoin('archivos', 'ra.FK_ARC_id', 'archivos.ARC_id')
        ->where('recomendaciones.FK_FRM_id',  $request->id)
        ->get();

        // $quries = DB::getQueryLog();
        // dump( $quries );
        // exit;

        return view('cuestionarios.cuestionario-responses', compact('recomendaciones', 'id'));
    }
    

    /**
     * Responder cuestionario con mejoras para manejo de secciones
     */
    public function responderCuestionario($VIS_id, $FRM_id, $AGF_id){
        DB::enableQueryLog();

        /* Consulta mejorada que incluye categorías y manejo de secciones */
        $elementos = ModFormulario::from('formularios as f')
        ->select(
            'rbf.RBF_id', 'bp.BCP_id', 'bp.BCP_pregunta', 'bp.BCP_tipoRespuesta', 
            'bp.BCP_opciones', 'bp.BCP_complemento', 'bp.BCP_adjunto', 'bp.BCP_aclaracion',
            // Agregamos categorías para manejar secciones
            'c.CAT_id as categoriaID', 'c.CAT_categoria as subcategoria', 
            'c.FK_CAT_id', 'c2.CAT_categoria as categoria',
            'f.FRM_id', 'f.FRM_titulo', 'f.FRM_fecha', 
            'r.RES_respuesta', 'r.RES_complemento', 'r.RES_id', 
            'a.ARC_ruta', 'a.ARC_id', 'a.ARC_formatoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 
            'af.AGF_copia', 'af.AGF_id', 'rbf.RBF_orden', 'rbf.RBF_salto_FK_BCP_id',
            'rbf.RBF_etiqueta' // Para manejar etiquetas personalizadas
        )
        ->join('agrupador_formularios as af', 'f.FRM_id', 'af.FK_FRM_id')
        ->join('r_bpreguntas_formularios as rbf', 'rbf.FK_FRM_id', 'f.FRM_id')
        ->join('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
        // Agregamos joins para categorías
        ->join('categorias as c', 'bp.FK_CAT_id', 'c.CAT_id')
        ->leftJoin('categorias as c2', 'c.FK_CAT_id', 'c2.CAT_id')
        ->leftJoin('respuestas as r', function($join){
            $join->on('r.FK_AGF_id', 'af.AGF_id')
            ->on('rbf.RBF_id','=', 'r.FK_RBF_id');
        })
        ->leftJoin('archivos as a', 'r.RES_id', 'a.FK_RES_id')
        ->where('rbf.FK_FRM_id', $FRM_id)
        ->where('af.AGF_id', $AGF_id)
        ->where('rbf.estado', 1)
        ->orderBy('rbf.RBF_orden', 'asc')
        ->orderBy('rbf.RBF_id', 'asc')
        ->get()->toArray();

        if (count($elementos) > 0) {
            $EST_nombre = session('EST_nombre');
            $FRM_titulo = $elementos[0]['FRM_titulo'];
            $AGF_copia = $elementos[0]['AGF_copia'];
            
            // Procesar elementos para identificar secciones
            $elementos_procesados = $this->procesarElementosConSecciones($elementos);
            
            return view('cuestionarios.cuestionario-responder', compact(
                'elementos', 'elementos_procesados', 'FRM_id', 'EST_nombre',
                'FRM_titulo', 'AGF_copia', 'AGF_id', 'VIS_id'
            ));
        } else {
            return view('cuestionarios.cuestionario-responder', compact('elementos', 'FRM_id'));
        }
    }
    
    /**
     * Procesar elementos para identificar y marcar secciones y subsecciones
     */
    private function procesarElementosConSecciones($elementos) {
        $procesados = [];
        $seccion_actual = null;
        $subseccion_actual = null;

        foreach ($elementos as $elemento) {
            $elemento['es_seccion'] = false;
            $elemento['es_subseccion'] = false;
            $elemento['seccion_padre'] = null;
            $elemento['subseccion_padre'] = null;

            // Identificar secciones y subsecciones
            if (in_array($elemento['BCP_tipoRespuesta'], ['Sección', 'Seccion'])) {
                $elemento['es_seccion'] = true;
                $seccion_actual = $elemento['BCP_id'];
                $subseccion_actual = null;
            } elseif (in_array($elemento['BCP_tipoRespuesta'], ['Subsección', 'Subseccion'])) {
                $elemento['es_subseccion'] = true;
                $elemento['seccion_padre'] = $seccion_actual;
                $subseccion_actual = $elemento['BCP_id'];
            } else {
                // Pregunta normal
                $elemento['seccion_padre'] = $seccion_actual;
                $elemento['subseccion_padre'] = $subseccion_actual;
            }

            $procesados[] = $elemento;
        }

        return $procesados;
    }

    /**
     * Guardado mejorado con mejor manejo de errores y notificaciones
     */
    public function guardarRespuestasCuestionario(Request $request)
    {
        $validated = $request->validate([
            'FK_RBF_id' => 'required|integer|exists:r_bpreguntas_formularios,RBF_id',
            'FK_AGF_id' => 'required|string|exists:agrupador_formularios,AGF_id',
            'RES_respuesta' => 'required',
            'RES_complemento' => 'nullable',
            'RES_tipoRespuesta' => 'required',
            'RES_complementoRespuesta' => 'nullable',
           
        ],[
            'RES_respuesta.required' => 'Debe ingresar una respuesta',
        ]);
        
        // cuando la respuesta es un array (pregunta tipo Casilla verificación), convierte a JSON para almacenarlo
        if (is_array($validated['RES_respuesta'])) {
            $resultado = [];

            // Recorrer elementos del array original
            foreach ($validated['RES_respuesta'] as $item) {
                // Si el elemento es un array, combinarlo
                if (is_array($item)) {
                    $resultado = array_merge($resultado, $item);
                } else {
                    $resultado[] = $item;
                }
            }
            
             $validated['RES_respuesta'] = json_encode(array_values($validated['RES_respuesta']));
        }
        try {
            // Bloqueo transaccional para evitar condiciones de carrera
            return DB::transaction(function () use ($validated) {
                // Usar lockForUpdate para bloquear el registro durante la transacción
                $respuesta = ModRespuesta::where([
                    'FK_RBF_id' => $validated['FK_RBF_id'],
                    'FK_AGF_id' => $validated['FK_AGF_id']
                ])->lockForUpdate()->first();
                
                if (!$respuesta) {
                    $respuesta = new ModRespuesta();
                }

                // Actualiza todos los campos necesarios
                $respuesta->fill($validated);
                $respuesta->save();

                return response()->json([
                    'status' => $respuesta->wasRecentlyCreated ? 'success' : 'updated',
                    'message' => $respuesta->wasRecentlyCreated 
                        ? 'Respuesta guardada correctamente' 
                        : 'Respuesta actualizada correctamente'
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar específicamente el error de violación de índice único
            if (str_contains($e->getMessage(), 'duplicate key value violates unique constraint')) {
                return response()->json([
                    'status' => 'skip',
                    'message' => 'La respuesta ya existe'
                ]);
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la respuesta: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la respuesta: ' . $e->getMessage()
            ], 500);
        }
    }


    

 


    /* Confirma la finalizacion del cuestionario y muestra al usuario un mensaje de confirmación */
    public function confirmaCuestionario( Request $request ){
        // dump( $request->except('_token') ); exit;

        DB::beginTransaction();
        try {
            ModAgrupadorFormulario::where('FK_FRM_id', $request->FRM_id)
            ->update(['estado' => $request->estado]);
            DB::commit();
            return response()->json( [ 'message'=>'Correcto!' ] );
        }
        catch (\Exception $e) {
            DB::rollback();
            exit ($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * @param  \App\Models\ModCuestionario  $cuestionario
     * @return \Illuminate\Http\Response
     * Muestra el formulario ya construido listo para imprimir
     */
    public function imprimirCuestionario($VIS_id, $FRM_id, $AGF_id){

        /* Se consultan las preguntas, respuestas, categorias, formularios e instituciones del $FRM_id de Formulario dado  */
        $elementos = ModFormulario::from('formularios as f')
        ->select ('rbf.RBF_id', 'bp.BCP_id', 'bp.BCP_pregunta', 'bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 'bp.BCP_complemento', 'bp.BCP_adjunto', 'bp.BCP_aclaracion', 'c.CAT_id as categoriaID', 'c.CAT_categoria as subcategoria', 'c.FK_CAT_id', 'c2.CAT_categoria as categoria', 'f.FRM_id', 'f.FRM_titulo', 'f.FRM_fecha', 'r.RES_respuesta', 'r.RES_complemento', 'r.RES_id', 'a.ARC_ruta', 'a.ARC_id',  'a.ARC_formatoArchivo',  'a.ARC_extension', 'a.ARC_descripcion', 'af.AGF_copia', 'af.AGF_id', 'rbf.RBF_orden', 'rbf.RBF_salto_FK_BCP_id' )
        ->join ('agrupador_formularios as af', 'f.FRM_id', 'af.FK_FRM_id')
        ->join ('r_bpreguntas_formularios as rbf', 'rbf.FK_FRM_id', 'f.FRM_id')
        ->join ('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
        ->join ('categorias as c', 'bp.FK_CAT_id', 'c.CAT_id')
        ->leftjoin ('categorias as c2', 'c.FK_CAT_id', 'c2.CAT_id')
        ->leftjoin('respuestas as r', function($join){
            $join->on('r.FK_AGF_id', 'af.AGF_id')
            ->on('rbf.RBF_id','=', 'r.FK_RBF_id');
        })
        ->leftjoin ('archivos as a', 'r.RES_id', 'a.FK_RES_id')
        ->where ('rbf.FK_FRM_id', $FRM_id)
        ->where('af.AGF_id', $AGF_id)
        ->where('rbf.estado', 1)
        // ->orderBy('c.CAT_id', 'asc')
        // ->orderBy('bp.BCP_id', 'asc')
        ->orderBy('rbf.RBF_orden', 'asc')
        ->orderBy('rbf.RBF_id', 'asc')
        ->get()->toArray();

        // dump($elementos[0]['FRM_titulo']);exit;

        // DB::enableQueryLog();
        if ( count($elementos) > 0 ){
            $EST_nombre =  session('EST_nombre');;
            $FRM_titulo = $elementos[0]['FRM_titulo'];
            $AGF_copia = $elementos[0]['AGF_copia'];
            $elementos_categorias = CustomController::array_group( $elementos, 'subcategoria' );
            return view('cuestionarios.cuestionario-imprimir', compact('elementos', 'elementos_categorias', 'FRM_id', 'FRM_titulo', 'EST_nombre','AGF_id', 'VIS_id'));
        } else {
            return view('cuestionarios.cuestionario-imprimir', compact('elementos'));
        }
    }

    /* Muestra los archivos adjuntos al cuestionario*/
    public function adjuntosFormulario($est_id, $frm_id = null){

        $formulario = ModFormulario::select('formularios.FRM_id', 'formularios.FRM_titulo', 'formularios.FRM_version', 'formularios.FRM_fecha', 'formularios.FK_EST_id', 'establecimientos.EST_nombre')
        ->leftJoin('establecimientos', 'establecimientos.EST_id', 'formularios.FK_EST_id' )
        ->where('FRM_id', $frm_id)->first();

        DB::enableQueryLog();

        // $adj = ModAdjunto::from( 'adjuntos as ad' )
        // ->select('ad.*', 'a.ARC_ruta', 'a.ARC_id', 'a.ARC_tipoArchivo', 'a.ARC_extension', 'a.ARC_descripcion', 'raa.FK_ADJ_id')
        // ->leftjoin ('r_adjuntos_archivos as raa', 'ad.ADJ_id', 'raa.FK_ADJ_id')
        // ->leftjoin ('archivos as a', 'raa.FK_ARC_id', 'a.ARC_id')
        // ->leftjoin ('formularios as f', 'f.FRM_id', 'ad.FK_FRM_id')
        // ->where ('f.FK_EST_id', $est_id);

        // if( $frm_id ){
        //     $adjuntos = $adj->where ('ad.FK_FRM_id', $frm_id)->orderBy('ad.ADJ_id', 'desc')->get();
        // }else{
        //     $adjuntos = $adj->orderBy('ad.ADJ_id', 'desc')->get();
        // }

        // $quries = DB::getQueryLog();
        // dump($quries);
        // exit;
        return view('formulario.formularios-adjuntos', compact('formulario', 'adjuntos'));
    }


     /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * Muestra el cuestionario VACÍO. DONDE se deben construir la estructura de categorias subcategorias y preguntas
     * VERIF
     */
    // public function index( ){
    //     // dump('index');exit;
    //     // $formulario = ModFormulario::select('formularios.FRM_id', 'formularios.FRM_titulo', 'formularios.FRM_version', 'formularios.FRM_fecha', 'formularios.FK_EST_id', 'establecimientos.EST_nombre')
    //     // ->leftJoin('establecimientos', 'establecimientos.EST_id', 'formularios.FK_EST_id' )
    //     // ->where('FRM_id', $id)->first();

    //     // $categorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
    //     // ->whereNull('FK_CAT_id')->get();
    //     $a=0;
    //     return view('cuestionarios.cuestionario-index', compact('a'));
    // }
    public function buscarPreguntas( Request $request ){
        $q = $request->q;
        DB::enableQueryLog();

        $preguntas = ModBancoPregunta::select(
            'banco_preguntas.BCP_id',
            'banco_preguntas.BCP_pregunta',
            'banco_preguntas.FK_CAT_id as ID_categoria',
            'categorias.CAT_categoria as categoria',
            'subcategoria.CAT_categoria as subcategoria'
        )
        ->leftJoin('categorias', 'banco_preguntas.FK_CAT_id', '=', 'categorias.CAT_id')
        ->leftJoin('categorias as subcategoria', 'categorias.FK_CAT_id', '=', 'subcategoria.CAT_id')
        ->where('BCP_pregunta', 'ilike', '%'.$q . '%')->get();

        $quries = DB::getQueryLog();
        // dump($quries);
        // exit;
        $preguntas = CustomController::ordenaPreguntasCategorias($preguntas->toArray());

        return response()->json($preguntas);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * Guarda la estructura del cuestionario construido en la funcion index
     *   */
    public function guardaCuestionarioEditado( Request $request ){
        // dump( $request->except('_token') );
        // exit;
        $a = array();
        foreach ($request->except('_token') as $key => $value){
            $columna = explode("_", $key);

            if($columna[1] == "preguntaId" ){
                array_push($a, [ 'FK_FRM_id' => $request['FRM_id'], 'FK_BCP_id'=> $value ]);
            }
            if($columna[1] == "etiqueta" ){
                // $bcpId = ModBancoPregunta::insert($p);
                $pregunta = ModBancoPregunta::create([
                    'BCP_pregunta' => $value,
                    'FK_CAT_id' => '0',
                ]);
                array_push($a, [ 'FK_FRM_id' => $request['FRM_id'], 'FK_BCP_id'=> $pregunta->BCP_id ]);
            }
        }
    // dump( $a );
    // exit;
        if( ModCuestionario::insert($a) ){
            return redirect()->route('cuestionario.imprimir', $request['FRM_id']);
        }
    }









}





