<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('apiIniciar', [ApiAuthController::class, 'apiIniciar'] );
Route::post('apiSalir', [ApiAuthController::class, 'apiSalir'] );

