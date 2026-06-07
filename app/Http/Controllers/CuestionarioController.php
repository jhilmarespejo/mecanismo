<?php

namespace App\Http\Controllers;

// use App\Http\Livewire\Cuestionario;
use Illuminate\Http\Request;
use App\Models\{ModFormulario,ModRespuesta, ModVisita, ModAdjunto, ModCuestionario, ModBancoPregunta, ModRecomendacion, ModEstablecimiento, ModArchivo, ModPreguntasFormulario, ModAgrupadorFormulario, ModRespuestaArchivo};
use Illuminate\Support\Facades\DB;
// use Image;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use App\Http\Controllers\{ CustomController};

// use Psy\Command\WhereamiCommand;

class CuestionarioController extends Controller {
    /**
     * Muestra los resultados del cuestionario con estadísticas y gráficos corregidos
     * 
     * @param int $FRM_id ID del formulario
     * @return \Illuminate\View\View
     */
    // Función para mostrar los resultados del cuestionario con graficos estadísticos 
    // ruta: .../cuestionario/resultados/1281/8
    public function resultadosGlobalesCuestionario ($FRM_id/*, $VIS_id*/)
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
            //->whereYear('agf.createdAt', Carbon::now()->year)
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
                'VIS_id' => null,
                'resultadoGeneral' => 1

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
        $resultados = array_merge($arrayConteoRespCasVarif);

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
        $resultadoGeneral = 1;
        return view('cuestionarios.cuestionario-resultado', compact(
            'resultados',
            'FRM_titulo', 
            'totalAplicaciones',
            'FRM_id',
            'estadisticas',
            'VIS_id',
            'resultadoGeneral'
        ))->with('total', $totalAplicaciones); // Agregar $total para compatibilidad total con la vista
    }
    /**
     * Muestra los resultados del cuestionario para una visita específica con estadísticas y gráficos
     * 
     * @param int $FRM_id ID del formulario
     * @param int $VIS_id ID de la visita
     * @return \Illuminate\View\View
     */
    public function resultadosCuestionarioPorVisita($FRM_id, $VIS_id)
    {
        // Verificar permisos de usuario
        if (Auth::user()->rol != 'Administrador') {
            return redirect()->back()->with('warning', 'Usuario no autorizado para esta función');
        }
        
        // =========================== OBTENER COPIAS DEL FORMULARIO PARA ESTA VISITA ===========================
        $copias = ModAgrupadorFormulario::from('agrupador_formularios as agf')
            ->select('f.FRM_titulo', 'agf.AGF_id', 'agf.AGF_copia')
            ->join('formularios as f', 'f.FRM_id', 'agf.FK_FRM_id')
            ->where('agf.FK_FRM_id', $FRM_id)
            ->where('agf.FK_VIS_id', $VIS_id)  // Filtro añadido por visita
            //->whereYear('agf.createdAt', Carbon::now()->year)
            ->get()->toArray();
        
        // Verificar si existen aplicaciones del formulario para esta visita
        $totalAplicaciones = count($copias);
        
        if ($totalAplicaciones == 0) {
            return view('cuestionarios.cuestionario-resultado', [
                'resultados' => null,
                'FRM_titulo' => 'Formulario sin aplicaciones en esta visita',
                'totalAplicaciones' => 0,
                'total' => 0,
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
                'VIS_id' => $VIS_id,
                'resultadoGeneral' => 0
            ]);
        }

        $FRM_titulo = $copias[0]['FRM_titulo'];

        // =========================== OBTENER PREGUNTAS REALES DEL FORMULARIO ===========================
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
            ->whereIn('r.FK_AGF_id', array_column($copias, 'AGF_id'))  // Filtro por aplicaciones de esta visita
            ->groupBy(
                'c.CAT_categoria', 'bp.BCP_pregunta', 'rbf.RBF_orden', 
                'rbf.RBF_id', 'r.RES_respuesta', 'r.FK_AGF_id', 'bp.BCP_tipoRespuesta'
            )
            ->orderBy('rbf.RBF_orden')
            ->orderBy('rbf.RBF_id')
            ->get();

        // =========================== PROCESAR CASILLAS Y LISTAS DESPLEGABLES ===========================
        $arrayConteoRespCasVarif = [];
        
        foreach ($todasLasPreguntas as $pregunta) {
            if (in_array($pregunta->BCP_tipoRespuesta, ['Lista desplegable', 'Casilla verificación'])) {
                
                $opcionesSeleccionadas = DB::table('respuestas as r')
                    ->select('r.RES_respuesta')
                    ->join('r_bpreguntas_formularios as rbf', 'rbf.RBF_id', 'r.FK_RBF_id')
                    ->join('banco_preguntas as bp', 'bp.BCP_id', 'rbf.FK_BCP_id')
                    ->where('rbf.FK_FRM_id', $FRM_id)
                    ->where('rbf.FK_BCP_id', $pregunta->BCP_id)
                    ->whereIn('r.FK_AGF_id', array_column($copias, 'AGF_id'))  // Filtro por aplicaciones de esta visita
                    ->whereNotNull('r.RES_respuesta')
                    ->groupBy('r.RES_respuesta')
                    ->get()->toArray();

                

                $outputArray = array_map(function ($item) {
                    return $item->RES_respuesta;
                }, $opcionesSeleccionadas);

                if (!empty($outputArray)) {
                    $columnasOpciones = '';
                    
                    foreach ($outputArray as $opcionPregunta) {
                        if ($opcionPregunta == null) {
                            $opcionPregunta = 'Sin respuesta';
                        }
                        $etiqueta = str_replace(['[', ']', '"'], '', $opcionPregunta);
                        $columnasOpciones .= 'SUM(("r"."RES_respuesta" ilike \''.$opcionPregunta.'\')::int) as "'.str_replace(',', ' / ', $etiqueta).'",';
                    }

                    $columnasOpciones = rtrim($columnasOpciones, ',');

                    $respuestasCasillaVarif = DB::select('
                        SELECT "c"."CAT_categoria", "bp"."BCP_pregunta", '.$columnasOpciones.', 
                            "rbf"."RBF_id", "rbf"."RBF_orden", "bp"."BCP_tipoRespuesta" 
                        FROM "respuestas" as "r"  
                        RIGHT JOIN "r_bpreguntas_formularios" as "rbf" ON "rbf"."RBF_id" = "r"."FK_RBF_id" 
                        LEFT JOIN "banco_preguntas" as "bp" ON "bp"."BCP_id" = "rbf"."FK_BCP_id" 
                        LEFT JOIN "categorias" as "c" ON "c"."CAT_id" = "bp"."FK_CAT_id" 
                        WHERE "rbf"."FK_FRM_id" = ? AND "rbf"."FK_BCP_id" = ?
                        AND "r"."FK_AGF_id" IN ('.implode(',', array_fill(0, count($copias), '?')).')
                        GROUP BY "c"."CAT_categoria", "bp"."BCP_pregunta", "rbf"."RBF_orden", 
                                "rbf"."RBF_id", "bp"."BCP_tipoRespuesta" 
                        ORDER BY "rbf"."RBF_orden", "rbf"."RBF_id"
                    ', array_merge([$FRM_id, $pregunta->BCP_id], array_column($copias, 'AGF_id')));

                    if (!empty($respuestasCasillaVarif)) {
                        $arrayConteoRespCasVarif[] = (array) $respuestasCasillaVarif[0];
                    }
                }
            }
        }
        // =========================== UNIR Y PROCESAR RESULTADOS ===========================
        $resultados = array_merge($arrayConteoRespCasVarif);
        $resultados = array_filter($resultados, function($item) {
            return !empty($item) && isset($item['BCP_pregunta']);
        });

        $resultados = CustomController::agruparRespuestasCerradas($resultados);
        $arrayRespuestasAbiertas = CustomController::agruparRespuestasAbiertas(
            json_decode(json_encode($respuestasAbiertas), true)
        );
        
        $resultados = array_merge($resultados, $arrayRespuestasAbiertas);
        usort($resultados, [CustomController::class, 'ordernarRespuestas']);
        $resultados = CustomController::array_group($resultados, 'CAT_categoria');

        // =========================== CALCULAR TIPOS DE PREGUNTAS ===========================
        $tiposPreguntas = [];
        foreach ($preguntasReales as $pregunta) {
            $tipo = $pregunta->BCP_tipoRespuesta;
            $tiposPreguntas[$tipo] = ($tiposPreguntas[$tipo] ?? 0) + 1;
        }

        // =========================== PREPARAR ESTADÍSTICAS ===========================
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
        
        // =========================== RETORNAR VISTA ===========================
        $resultadoGeneral = 0;
        return view('cuestionarios.cuestionario-resultado', compact(
            'resultados',
            'FRM_titulo', 
            'totalAplicaciones',
            'FRM_id',
            'estadisticas',
            'VIS_id',
            'resultadoGeneral'
        ))->with('total', $totalAplicaciones);
    }  

    
    // Función para duplicar un formulario para luego ser aplicado.
    // ruta: .../cuestionario/duplicarCuestionario/1281/8
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
            return redirect('/formulario/muestraFormulariosVista/' . $VIS_id)->with('warning','Este formulario solo puede duplicarse una vez');
        } else {
            return redirect('/formulario/muestraFormulariosVista/' . $VIS_id);
        }
    }

    /*  return > 0: se guardó el dato correctamente
        return -1: Error al guardar el dato
    */
    public function fn_duplicar_cuestionario( $nuevoFormulario ){
        DB::beginTransaction();
        try {
            // ModAgrupadorFormulario::insert( $nuevoFormulario );
            // $ultimoFRMid = DB::getPdo()->lastInsertId();
            // DB::commit();
            // return $ultimoFRMid;//redirect('/cuestionario/responder/'.$FRM_id.'/'.$ultimoFRMid);

            $registro = ModAgrupadorFormulario::create($nuevoFormulario);
            DB::commit();
            return $registro->id;
        }
        catch (\Exception $e) {
            DB::rollback();
            // return $e->getMessage();
            return -1;
        }
    }

    /* Elimina culquier cuestionario solo para usuarios administradores */
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
    
    // public function preguntasRespuestas( $FRM_id, $AGF_copia){

    //     // $quries = DB::getQueryLog();
    //     // dump( $quries );
    //     // exit;
    //     // return $elementos;
    // }

    //***VERIF */
    // public function buscarRecomendaciones( Request $request ){
    //     // dump($request->except('_token'));exit;
    //     $id = $request->id;

    //     DB::enableQueryLog();
    //     $recomendaciones = ModRecomendacion::select( 'recomendaciones.REC_id', 'recomendaciones.REC_recomendacion', 'recomendaciones.FK_FRM_id', 'archivos.ARC_ruta' )
    //     ->leftJoin('r_recomendaciones_archivos as ra', 'ra.FK_REC_id', 'recomendaciones.REC_id')
    //     ->leftJoin('archivos', 'ra.FK_ARC_id', 'archivos.ARC_id')
    //     ->where('recomendaciones.FK_FRM_id',  $request->id)
    //     ->get();

    //     // $quries = DB::getQueryLog();
    //     // dump( $quries );
    //     // exit;

    //     return view('cuestionarios.cuestionario-responses', compact('recomendaciones', 'id'));
    // }
    

    /**
     * Funcion para responder determinado cuestionario
     * ruta: .../cuestionario/responder/8/1281/798
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
     * Funcion para guardar respuestas individuales de un cuestionario
     * Funciona cuando se termina de responder una pre
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



    /* Confirma la finalizacion del cuestionario y muestra al usuario un mensaje de confirmación de haber terminado de responder el formualario */
    public function confirmaCuestionario( Request $request ){
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

}





