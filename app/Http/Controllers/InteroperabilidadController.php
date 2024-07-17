<?php

namespace App\Http\Controllers;

use App\Models\{ModCategoria, ModBancoPregunta};
use Illuminate\Http\Request;
use DB;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class InteroperabilidadController extends Controller
{
    public function index( ){
        // Realizar la consulta a la API
        $datos_api = Http::get('http://mnp.local/api/api_test');


        // Verificar si la consulta fue exitosa (código de estado 200)
        if ($datos_api->successful()) {
            // Obtener los datos de la respuesta en formato JSON
            $datos_api = $datos_api->json();

            return view( 'interoperabilidad.index', compact('datos_api'));
        } else {
            // Si la consulta no fue exitosa, manejar el error apropiadamente
            return response()->json(['error' => 'Error al consultar la API'], $datos_api->status());
        }
        // return view( 'interoperabilidad.index' );


    }

    public function simulador()
    {

    }
}




?>
