<?php

namespace App\Http\Controllers;

use App\Models\{ModCategoria, ModBancoPregunta};
use Illuminate\Http\Request;
use DB;
use Validator;
use Illuminate\Support\Facades\Auth;

class InformeVisitasController extends Controller
{
    public function index( ){
        // exit($establecimiento_id);
        return view( 'informeVisitas.index' );
    }
}




?>
