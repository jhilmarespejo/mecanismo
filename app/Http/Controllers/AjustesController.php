<?php

namespace App\Http\Controllers;

use App\Models\{ModCategoria, ModBancoPregunta};
use Illuminate\Http\Request;
use DB;
use Validator;
use Illuminate\Support\Facades\Auth;

class AjustesController extends Controller
{
    public function index( $establecimiento_id ){
        // exit($establecimiento_id);
        return view( 'ajustes.index' );
    }
}

// tengo una tabla banco_preguntas y otra tabla llamada formularios,
// la relacion entre las tablas banco_preguntas y formularios es de muchos a muchos,
// a esta tabla la llame cuestionario.
// Que tablas me faltan para guardar las respuestas, tomando en cuenta que 1 mismo cuestionario
// puede ser respondido mas de una vez


// escribe la estructura de una t
