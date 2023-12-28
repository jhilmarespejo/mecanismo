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
}


