<?php

namespace App\Http\Controllers\api;
use App\Models\{ModEstablecimiento, ModVisita};

use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiVisitasController extends Controller {
    // Muestra una lista de las visitas realizadas a establecimiento con FK_EST_id.
    public function apiListarVisitas(Request $request){
        return ModVisita::select('VIS_id', 'VIS_numero', 'VIS_fechas', 'VIS_tipo', 'VIS_titulo')
        ->where('FK_EST_id', $request->FK_EST_id)
        ->get();
    }






}
