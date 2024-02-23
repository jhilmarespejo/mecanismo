<?php

namespace App\Http\Controllers;

use App\Models\{ModCategoria, ModBancoPregunta};
use Illuminate\Http\Request;
use DB;
use Validator;
use Illuminate\Support\Facades\Auth;

class InteroperabilidadController extends Controller
{
    public function index( ){
        return view( 'interoperabilidad.index' );
    }
}




?>
