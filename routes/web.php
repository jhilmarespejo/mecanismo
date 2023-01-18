<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Formularios;
use App\Http\Livewire\CuestionarioIndex;
use App\Http\Livewire\Establecimientos;
use App\Http\Livewire\BancoPreguntasIndex;

use App\Http\Controllers\{CuestionarioController, CategoriasController, EstablecimientosController, IndexController, RecomendacionesController, FormularioController};
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    //Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/panel', [IndexController::class, 'dashboard'])->name('panel');
});


Route::get('formulario/adjuntos/{id}', [FormularioController::class, 'adjuntosFormulario'])->name('formulario.adjuntos');

Route::post('formulario/adjuntosNuevo', [FormularioController::class, 'adjuntosNuevo'])->name('formulario.adjuntosNuevo');


Route::post('index/buscarIdForm', [IndexController::class, 'buscarIdFormulario'])->name('index.buscarIdForm');

Route::post('index/buscarAfirmaciones', [IndexController::class, 'buscarAfirmaciones'])->name('index.buscarAfirmaciones');

Route::post('index/buscarListasCasillas', [IndexController::class, 'buscarListasCasillas'])->name('index.buscarListasCasillas');

Route::get('formularios', Formularios::class);
Route::get('cuestionario', CuestionarioIndex::class);
// Route::get('establecimientos', Establecimientos::class);
Route::get('bancoDePreguntas', BancoPreguntasIndex::class);
Route::post('bancoDePreguntasEditar', BancoPreguntasIndex::class);



Route::post('formulario/store', [FormularioController::class, 'store'])->name('formulario.store');

Route::get('cuestionario/{id}', [CuestionarioController::class, 'index'])->name('cuestionario.index');
Route::get('cuestionario/imprimir/{id}', [CuestionarioController::class, 'imprimir'])->name('cuestionario.imprimir');
Route::get('cuestionario/responder/{id}', [CuestionarioController::class, 'responderCuestionario'])->name('cuestionario.responder');
Route::post('cuestionario/guardarRespuestasCuestionario', [CuestionarioController::class, 'guardarRespuestasCuestionario'])->name('cuestionario.guardarRespuestasCuestionario');
Route::post('cuestionario/store', [CuestionarioController::class, 'store'])->name('cuestionario.store');


Route::post('cuestionario/buscarRecomendaciones', [CuestionarioController::class, 'buscarRecomendaciones'])->name('cuestionario.buscarRecomendaciones');


Route::post('recomendaciones/cumplimiento', [RecomendacionesController::class, 'guardarCumplimientoRecomendaciones'])->name('recomendaciones.cumplimiento');

Route::get('recomendaciones/{est_id}/{frm_id}', [RecomendacionesController::class, 'recomendaciones'])->name('recomendaciones');
Route::post('recomendaciones/nuevaRecomendacion', [RecomendacionesController::class, 'nuevaRecomendacion'])->name('recomendaciones.nueva');




Route::post('categorias/buscarSubcategoria', [CategoriasController::class, 'buscarSubcategoria'])->name('categorias.buscarSubcategoria');
Route::post('categorias/buscarPregunta', [CategoriasController::class, 'buscarPregunta'])->name('categorias.buscarPregunta');
Route::post('categorias/buscarElementos', [CategoriasController::class, 'buscarElementos'])->name('categorias.buscarElementos');
Route::get('categorias', [CategoriasController::class, 'index'])->name('categorias');
Route::post('categorias/guardaNuevaCategoria', [CategoriasController::class, 'guardaNuevaCategoria'])->name('categorias.guardaNuevaCategoria');



Route::get('establecimientos', [EstablecimientosController::class, 'index'])->name('establecimientos');
Route::get('establecimientos/historial/{id}', [EstablecimientosController::class, 'historial'])->name('establecimientos.historial');
Route::post('establecimientos/listar', [EstablecimientosController::class, 'listar'])->name('establecimientos.listar');
Route::post('establecimientos/guardarNuevoEstablecimiento', [EstablecimientosController::class, 'guardarNuevoEstablecimiento'])->name('establecimientos.guardarNuevoEstablecimiento');





