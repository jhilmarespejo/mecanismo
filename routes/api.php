<?php

use App\Http\Controllers\api\{ApiAuthController, ApiMultiplesController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//https://mnp-bolivia.defensoria.gob.bo/api/api_iniciar?username=jhilmar.espejo&password=123.abc
Route::post('api_iniciar', [ApiAuthController::class, 'apiIniciar'] );
Route::get('api_ver', [ApiAuthController::class, 'apiVer']);

Route::post('api_salir', [ApiAuthController::class, 'apiSalir'])->middleware('auth');

//----------------------------------------------------------------------------
// https://mnp-bolivia.defensoria.gob.bo/api/api_lista_tipos_establecimientos
Route::get('api_lista_tipos_establecimientos', [ApiMultiplesController::class, 'ApiListarTiposEstablecimientos']);//->middleware('auth');

// https://mnp-bolivia.defensoria.gob.bo/api/api_lista_establecimientos
Route::get('api_lista_establecimientos', [ApiMultiplesController::class, 'ApiListarEstablecimientos']);//->middleware('auth');

//  https://mnp-bolivia.defensoria.gob.bo/api/api_visitas_formularios
Route::get('api_visitas_formularios', [ApiMultiplesController::class, 'ApiVisitasFormularios']);//->middleware('auth');

// https://mnp-bolivia.defensoria.gob.bo/api/api_formularios_cuestionario
Route::get('api_formularios_cuestionario', [ApiMultiplesController::class, 'ApiFormulariosCuestionario']);//->middleware('auth');

// https://mnp-bolivia.defensoria.gob.bo/api/api_guardar_respuestas
Route::post('api_guardar_respuestas', [ApiMultiplesController::class, 'ApiGuardarRespuestas']);//->middleware('auth');

// Route::group( [ 'midleware' => ['auth:sanctum']] , function () {
// });
//test https://mnp-bolivia.defensoria.gob.bo/api/api_test
Route::get('api_test', [ApiMultiplesController::class, 'api_test']);
