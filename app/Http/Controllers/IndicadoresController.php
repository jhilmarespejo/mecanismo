<?php

namespace App\Http\Controllers;

use App\Models\{ModFormulario, ModFormularioArchivo, ModAdjunto, ModArchivo, ModEstablecimiento};

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

// use Image;
// use Intervention\Image\Facades\Image;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Storage;

class IndicadoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

    return view('indicadores.index'/*, compact('formularios', 'response')*/);

    }

    public function actualizar( Request $request ){

        // si la peticion es ajax guardar datos
        // si la perticion no es ajax mostrar pantalla de recepcion de datos

    }



}
