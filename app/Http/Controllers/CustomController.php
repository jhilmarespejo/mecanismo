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

    public static function reorganizarArray($arrayOriginal) {
        $nuevoArray = [];
        foreach ($arrayOriginal as $elemento) {
            // Crear un nuevo elemento con las claves deseadas
            $nuevoElemento = [
                "CAT_categoria" => $elemento["CAT_categoria"],
                "BCP_pregunta" => $elemento["BCP_pregunta"],
                "RBF_id" => $elemento["RBF_id"],
                "RBF_orden" => $elemento["RBF_orden"],
                "respuestas" => []
            ];

            // Filtrar las respuestas del elemento
            $respuestas = array_filter($elemento, function ($key) {
                return $key != "CAT_categoria" && $key != "BCP_pregunta" && $key != "RBF_id" && $key != "RBF_orden";
            }, ARRAY_FILTER_USE_KEY);

            // Agregar las respuestas al nuevo elemento
            $nuevoElemento["respuestas"] = $respuestas;

            // Agregar el nuevo elemento al array final
            $nuevoArray[] = $nuevoElemento;
        }

        return $nuevoArray;
    }
}


