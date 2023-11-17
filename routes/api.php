<?php

use App\Http\Controllers\api\{ApiAuthController, ApiEstablecimientosController, ApiVisitasController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('api_iniciar', [ApiAuthController::class, 'apiIniciar'] );

Route::get('apiVer', [ApiAuthController::class, 'apiVer']);

// https://test-mnp.defensoria.gob.bo/api/api_lista_tipos_establecimientos
Route::get('api_lista_tipos_establecimientos', [ApiEstablecimientosController::class, 'ApiListarTiposEstablecimientos']);


Route::group( [ 'midleware' => ['auth:sanctum']] , function () {
    Route::post('apiSalir', [ApiAuthController::class, 'apiSalir']);



    // https://test-mnp.defensoria.gob.bo/api/api_lista_establecimientos?FK_EST_id=1
    Route::post('api_lista_establecimientos', [ApiEstablecimientosController::class, 'ApiLlistarEstablecimientos']);

    Route::post('api_listar_visitas', [ApiVisitasController::class, 'apiListarVisitas']);


});
