<?php

namespace App\Http\Controllers;

use App\Models\{ModFormulario};

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

// use Image;
// use Intervention\Image\Facades\Image;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Storage;

/* agrupa la estructura del resultado de una consulta  por el $key */
class CustomController extends Controller {
    public static function array_group(array $array, string $key) {
        $grouped = [];
        foreach ($array as $item) {
            if(!$item[$key]){
                $item[$key] = 'nokey';
            }
            $grouped[$item[$key]][] = $item;
        }
        return $grouped;
    }

    /* Para ordenar un array  segun el valor de las caves RBF_orden RBF_id y luego por */
    public static function ordernarRespuestas($a, $b) {
        // Primero ordena por RBF_orden
        if ($a['RBF_orden'] != $b['RBF_orden']) {
            return $a['RBF_orden'] - $b['RBF_orden'];
        }

        // Si RBF_orden es igual, ordena por RBF_id
        return $a['RBF_id'] - $b['RBF_id'];
    }

    /* Elimina elementos vacios de un array */
    public static function arrayNoVacio($array) {
        return !empty($array);
    }

    public static function agruparRespuestasCerradas($arrayOriginal) {
        $nuevoArray = [];
        foreach ($arrayOriginal as $elemento) {
            // Crear un nuevo elemento con las claves deseadas
            $nuevoElemento = [
                "CAT_categoria" => $elemento["CAT_categoria"],
                "BCP_pregunta" => $elemento["BCP_pregunta"],
                "RBF_id" => $elemento["RBF_id"],
                "RBF_orden" => $elemento["RBF_orden"],
                "BCP_tipoRespuesta" => $elemento["BCP_tipoRespuesta"],
                "respuestas" => []
            ];

            // Filtrar las respuestas del elemento
            $respuestas = array_filter($elemento, function ($key) {
                return $key != "CAT_categoria" && $key != "BCP_pregunta" && $key != "RBF_id" && $key != "RBF_orden" && $key != "BCP_tipoRespuesta";
            }, ARRAY_FILTER_USE_KEY);

            // Agregar las respuestas al nuevo elemento
            $nuevoElemento["respuestas"] = $respuestas;

            // Agregar el nuevo elemento al array final
            $nuevoArray[] = $nuevoElemento;
        }

        return $nuevoArray;
    }


    public static function agruparRespuestasAbiertas($array) {
        $result = [];

        foreach ($array as $item) {
            $pregunta = $item["BCP_pregunta"];
            $respuesta = $item["RES_respuesta"];
            $fk_agf_id = $item["FK_AGF_id"];

            $result[$pregunta]["CAT_categoria"] = $item["CAT_categoria"];
            $result[$pregunta]["BCP_pregunta"] = $pregunta;
            $result[$pregunta]["RBF_id"] = $item["RBF_id"];
            $result[$pregunta]["RBF_orden"] = $item["RBF_orden"];
            $result[$pregunta]["BCP_tipoRespuesta"] = $item["BCP_tipoRespuesta"];

            if (!isset($result[$pregunta]["RES_respuesta"])) {
                $result[$pregunta]["RES_respuesta"] = [];
            }

            $result[$pregunta]["respuestas"][] = [
                "respuesta" => $respuesta,
                "FK_AGF_id" => $fk_agf_id,
            ];
            sort($result[$pregunta]["respuestas"]);
        }

        return $result;
    }

    public static function pr($array) {
        echo '<div style="background-color: #f8f8f8; padding: 10px; border: 1px solid #ccc; margin: 10px 0;">';
        echo '<pre style="font-size: 12px; line-height: 1.2; color: #333; overflow: auto;">';
        var_dump($array);
        echo '</pre>';
        echo '</div>';
    }

    //Agrupa las recomendaciones con sus respectivos archivos adjuntos
    public static function agruparRecomendacionesImagenes($array) {
        $result = [];
        foreach ($array as $item) {
            $recId = $item['REC_id'];

            if (!isset($result[$recId])) {
                $result[$recId] = [
                    'REC_id' => $item['REC_id'],
                    'REC_recomendacion' => $item['REC_recomendacion'],
                    'REC_fechaRecomendacion' => $item['REC_fechaRecomendacion'],
                    'REC_cumplimiento' => $item['REC_cumplimiento'],
                    'REC_fechaCumplimiento' => $item['REC_fechaCumplimiento'],
                    'REC_autoridad_competente' => $item['REC_autoridad_competente'],
                    'archivos' => []
                ];
            }

            if ($item['ARC_id'] !== null) {
                $result[$recId]['archivos'][] = [
                    'ARC_id' => $item['ARC_id'],
                    'FK_REC_id' => $item['FK_REC_id'],
                    'ARC_descripcion' => $item['ARC_descripcion'],
                    'ARC_ruta' => $item['ARC_ruta'],
                    'ARC_extension' => $item['ARC_extension'],
                    'ARC_formatoArchivo' => $item['ARC_formatoArchivo'],
                ];
            }
        }
        return array_values($result);
    }
    //Agrupa los seguimientos de las recomendaciones con sus respectivos archivos adjuntos
    public static function agruparSeguimientosImagenes($array) {
        $nuevoArray = [];

        foreach ($array as $item) {
            $fk_rec_id = $item['FK_REC_id'];

            // Agrupar por FK_REC_id
            if (!isset($nuevoArray[$fk_rec_id])) {
                $nuevoArray[$fk_rec_id] = [];
            }

            $srec_id = $item['SREC_id'];

            // Agrupar por SREC_id dentro de FK_REC_id
            if (!isset($nuevoArray[$fk_rec_id][$srec_id])) {
                $nuevoArray[$fk_rec_id][$srec_id] = [
                    'SREC_id' => $item['SREC_id'],
                    'SREC_descripcion' => $item['SREC_descripcion'],
                    'SREC_fecha_seguimiento' => $item['SREC_fecha_seguimiento'],
                    'FK_REC_id' => $item['FK_REC_id'],
                    'archivos' => []
                ];
            }

            // Agregar imágenes si SREC_id es igual a FK_SREC_id
            if ($item['SREC_id'] === $item['FK_SREC_id']) {
                $imagen = [
                    'ARC_id' => $item['ARC_id'],
                    'ARC_formatoArchivo' => $item['ARC_formatoArchivo'],
                    'ARC_descripcion' => $item['ARC_descripcion'],
                    'ARC_ruta' => $item['ARC_ruta'],
                    'ARC_extension' => $item['ARC_extension'],
                    'FK_SREC_id' => $item['FK_SREC_id']
                ];

                $nuevoArray[$fk_rec_id][$srec_id]['archivos'][] = $imagen;
            }
        }

        return $nuevoArray;
    }

    public static function colorTipoVisita( $tipoVisita ){
        // dump($tipoVisita);exit;
        if($tipoVisita == 'Visita en profundidad'){
            $colorVisita = 'text-white bg-success';
        }elseif($tipoVisita == 'Visita Temática') {
            $colorVisita = 'text-white bg-danger';
        }elseif($tipoVisita == 'Visita de seguimiento'){
            $colorVisita = 'text-white bg-primary';
        }elseif($tipoVisita == 'Visita reactiva'){
            $colorVisita = 'text-white bg-info';
        }elseif($tipoVisita == 'Visita Ad hoc'){
            $colorVisita = 'text-white bg-warning';
        }elseif( is_null($tipoVisita) ){
            return redirect()->route('panel');
        }
        return $colorVisita;
    }

    //Ordena un array de preguntas seguidas de sus respectivas Categorias y Subcategorías
    public static function ordenaPreguntasCategorias( $preguntas ){
        $preguntasOrdenadas = [];

        foreach ($preguntas as $item) {
            if ($item['categoria'] && $item['subcategoria']) {
                // Si ambos tienen valor, intercambiarlos
                $categoria = $item['categoria'];
                $item['categoria'] = $item['subcategoria'];
                $item['subcategoria'] = $categoria;
            }
            $preguntasOrdenadas[] = $item;
        }

        return $preguntasOrdenadas;

    }

    //Agrupar las visitas por tipo y nombre
    public static function agruparPorTipoYNombre($datos)
    {
        // // Agrupar por TES_tipo y EST_nombre
        // $agrupadosPorTipoYNombre = $coleccion->groupBy(['TES_tipo', 'EST_nombre'])->toArray();

        // // Devolver los resultados
        // return $agrupadosPorTipoYNombre;
        $resultado = [];

        foreach ($datos as $item) {
            $tesTipo = $item->TES_tipo;
            $estNombre = $item->EST_nombre;
            $totalGeneal= $item->total_general;
            $EST_id = $item->EST_id;
            $VIS_fechas = $item->VIS_fechas;
            // dump($totalGeneal);

            // Asegurarse de que la clave TES_tipo exista
            if (!isset($resultado[$tesTipo])) {
                $resultado[$tesTipo] = [
                    'total_tipo_establecimiento' => $item->total_tipo_establecimiento,
                    'establecimientos' => [],
                ];
                // $resultado['total_general'] = $totalGeneal;
            }

            // Asegurarse de que la clave EST_nombre exista dentro de TES_tipo
            if (!isset($resultado[$tesTipo]['establecimientos'][$estNombre])) {
                $resultado[$tesTipo]['establecimientos'][$estNombre] = [
                    'total_establecimiento' => $item->total_establecimiento,
                    'EST_id' => $EST_id,

                    'visitas' => []
                ];
            }

            // Añadir la visita al arreglo correspondiente
            $resultado[$tesTipo]['establecimientos'][$estNombre]['visitas'][] = [
                'VIS_tipo' => $item->VIS_tipo,
                'total_tipo_visitas' => $item->total_tipo_visitas,
                'VIS_fechas' => $VIS_fechas,

            ];
        }
            return ['resultado'=>$resultado, 'total_geneal' => $totalGeneal];


    }

    // Agrupa los indicadores por categorias
    public static function organizarIndicadores($indicadores) {
        $result = [];

        foreach ($indicadores as $indicador) {
            $categoria = $indicador['IND_categoria'];
            $indicadorClave = $indicador['IND_indicador'];

            // Inicializar el array de la categoría si no existe
            if (!isset($result[$categoria])) {
                $result[$categoria] = [];
            }

            // Inicializar el array del indicador si no existe
            if (!isset($result[$categoria][$indicadorClave])) {
                $result[$categoria][$indicadorClave] = [];
            }

            // Añadir el indicador al array correspondiente
            $result[$categoria][$indicadorClave][] = $indicador;
        }

        return $result;
    }
}

