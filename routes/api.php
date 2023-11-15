<?php

use App\Http\Controllers\api\ApiAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('apiIniciar', [ApiAuthController::class, 'apiIniciar'] );

Route::get('apiVer', [ApiAuthController::class, 'apiVer']);

Route::group( [ 'midleware' => ['auth:sanctum']] , function () {
    Route::post('apiSalir', [ApiAuthController::class, 'apiSalir']);
});
