<?php

use App\Http\Controllers\api\{ApiAuthController, ApiMultiplesController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//https://test-mnp.defensoria.gob.bo/api/api_iniciar?username=jhilmar.espejo&password=123.abc
Route::post('api_iniciar', [ApiAuthController::class, 'apiIniciar'] );
Route::get('api_ver', [ApiAuthController::class, 'apiVer']);

Route::post('api_salir', [ApiAuthController::class, 'apiSalir'])->middleware('auth');

//----------------------------------------------------------------------------
// https://test-mnp.defensoria.gob.bo/api/api_lista_tipos_establecimientos
Route::get('api_lista_tipos_establecimientos', [ApiMultiplesController::class, 'ApiListarTiposEstablecimientos']);//->middleware('auth');

// https://test-mnp.defensoria.gob.bo/api/api_lista_establecimientos
Route::get('api_lista_establecimientos', [ApiMultiplesController::class, 'ApiListarEstablecimientos']);//->middleware('auth');

// https://test-mnp.defensoria.gob.bo/api/api_historial_visitas_formularios
Route::get('api_historial_visitas_formularios', [ApiMultiplesController::class, 'ApiHistorialVisitasFormularios']);//->middleware('auth');

// https://test-mnp.defensoria.gob.bo/api/api_formularios_cuestionarios
Route::get('api_formularios_cuestionarios', [ApiMultiplesController::class, 'ApiFormulariosCuestionarios']);//->middleware('auth');



// Route::group( [ 'midleware' => ['auth:sanctum']] , function () {
// });
