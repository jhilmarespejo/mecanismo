<?php

namespace App\Http\Controllers;

use App\Models\{ModCategoria, ModBancoPregunta};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $categorias= 'xxx';

        return view( 'reportes.index', compact('categorias') );
    }



}
