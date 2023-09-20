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




?>
