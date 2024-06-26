<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Formularios;
use App\Http\Livewire\CuestionarioIndex;
use App\Http\Livewire\Establecimientos;
use App\Http\Livewire\BancoPreguntasIndex;

use App\Http\Controllers\{CuestionarioController, CategoriasController, EstablecimientosController, IndexController, RecomendacionesController, FormularioController, ReportesController, VisitaController, IndicadoresController, AjustesController,InformeVisitasController, AccesoController};
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
/* -----------login personalizado ----------- */

Route::view('/acceso', 'acceso')->name('acceso');
Route::view('/resgistro', 'resgistro')->name('resgistro');

Route::get('acceso', [AccesoController::class, 'acceso'])->name('acceso');
Route::post('iniciar', [AccesoController::class, 'iniciar'])->name('acceso.iniciar');
Route::get('finalizar', [AccesoController::class, 'finalizar'])->name('acceso.finalizar');

/*------------------------------------------ */
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

// FORMULARIO
Route::get('formulario/adjuntos/{est_id}/{frm_id?}', [FormularioController::class, 'adjuntosFormulario'])->name('formulario.adjuntos')->middleware('auth');
Route::get('formulario/{VIS_id}', [FormularioController::class, 'index'])->name('formulario.index')->middleware('auth');

// Route::post('formulario/adjuntosNuevo', [FormularioController::class, 'adjuntosNuevo'])->name('formulario.adjuntosNuevo')->middleware('auth');

Route::post('formulario/store', [FormularioController::class, 'store'])->name('formulario.store')->middleware('auth');

/* Ruta para mostrar los formularios relacionados a la vista */
Route::get('formulario/buscaFormularios/{VIS_id}/{resultado?}', [FormularioController::class, 'buscaFormularios'])->name('formulario.buscaFormularios')->middleware('auth');

// INDEX
Route::post('index/buscarIdForm', [IndexController::class, 'buscarIdFormulario'])->name('index.buscarIdForm')->middleware('auth');

Route::post('index/busquedaDinamica', [IndexController::class, 'busquedaDinamica'])->name('index.busquedaDinamica')->middleware('auth');

Route::post('index/buscarListasCasillas', [IndexController::class, 'buscarListasCasillas'])->name('index.buscarListasCasillas')->middleware('auth');


// LIVEWIRE
Route::get('formularios', Formularios::class)->middleware('auth');
Route::get('cuestionario', CuestionarioIndex::class);
// Route::get('establecimientos', Establecimientos::class);
Route::get('bancoDePreguntas', BancoPreguntasIndex::class)->middleware('auth');
Route::post('bancoDePreguntasEditar', BancoPreguntasIndex::class)->middleware('auth');

// CUESTIONARIO
Route::get('cuestionario/{id}', [CuestionarioController::class, 'index'])->name('cuestionario.index')->middleware('auth');

Route::get('cuestionario/imprimir/{FRM_id}/{AGF_id}', [CuestionarioController::class, 'imprimirCuestionario'])->name('cuestionario.imprimir')->middleware('auth');

Route::get('cuestionario/duplicar/{FRM_id}', [CuestionarioController::class, 'duplicarCuestionario'])->name('cuestionario.duplicar')->middleware('auth');

Route::get('cuestionario/responder/{FRM_id}/{AGF_copia}', [CuestionarioController::class, 'responderCuestionario'])->name('cuestionario.responder')->middleware('auth');

Route::get('cuestionario/ver/{id}', [CuestionarioController::class, 'verCuestionario'])->name('cuestionario.ver')->middleware('auth');

Route::post('cuestionario/eliminar', [CuestionarioController::class, 'eliminarCuestionario'])->name('cuestionario.eliminar')->middleware('auth');

Route::post('cuestionario/guardarRespuestasCuestionario', [CuestionarioController::class, 'guardarRespuestasCuestionario'])->name('cuestionario.guardarRespuestasCuestionario')->middleware('auth');

Route::post('cuestionario/store', [CuestionarioController::class, 'store'])->name('cuestionario.store')->middleware('auth');

Route::post('cuestionario/buscarRecomendaciones', [CuestionarioController::class, 'buscarRecomendaciones'])->name('cuestionario.buscarRecomendaciones')->middleware('auth');

Route::post('cuestionario/confirmaCuestionario/', [CuestionarioController::class, 'confirmaCuestionario'])->name('cuestionario.confirmaCuestionario')->middleware('auth');

Route::get('cuestionario/resultados/{id}', [CuestionarioController::class, 'resultadosCuestionario'])->name('cuestionario.resultados')->middleware('auth');



// RECOMENDACIONES
Route::get('recomendaciones/{VIS_id}', [RecomendacionesController::class, 'recomendaciones'])->name('recomendaciones')->middleware('auth');

Route::post('recomendaciones/cumplimiento', [RecomendacionesController::class, 'guardarCumplimientoRecomendaciones'])->name('recomendaciones.cumplimiento')->middleware('auth');


Route::post('recomendaciones/nuevaRecomendacion', [RecomendacionesController::class, 'nuevaRecomendacion'])->name('recomendaciones.nueva')->middleware('auth');



// CATEGORIAS
Route::post('categorias/buscarSubcategoria', [CategoriasController::class, 'buscarSubcategoria'])->name('categorias.buscarSubcategoria')->middleware('auth');
Route::post('categorias/buscarPregunta', [CategoriasController::class, 'buscarPregunta'])->name('categorias.buscarPregunta')->middleware('auth');
Route::post('categorias/buscarElementos', [CategoriasController::class, 'buscarElementos'])->name('categorias.buscarElementos')->middleware('auth');
Route::get('categorias', [CategoriasController::class, 'index'])->name('categorias')->middleware('auth');
Route::post('categorias/guardaNuevaCategoria', [CategoriasController::class, 'guardaNuevaCategoria'])->name('categorias.guardaNuevaCategoria')->middleware('auth');


// ESTABLECIMIENTOS
Route::get('establecimientos', [EstablecimientosController::class, 'index'])->name('establecimientos')->middleware('auth');
Route::get('establecimientos/historial/{id}', [EstablecimientosController::class, 'historial'])->name('establecimientos.historial')->middleware('auth');
Route::post('establecimientos/listar', [EstablecimientosController::class, 'listar'])->name('establecimientos.listar')->middleware('auth');
Route::post('establecimientos/guardarNuevoEstablecimiento', [EstablecimientosController::class, 'guardarNuevoEstablecimiento'])->name('establecimientos.guardarNuevoEstablecimiento')->middleware('auth');

// REPORTES
Route::get('reportes', [ReportesController::class, 'index'])->name('reportes');

// VISITAS
Route::post('visita/guardarNuevaVisita', [VisitaController::class, 'guardarNuevaVisita'])->name('visita.guardarNuevaVisita')->middleware('auth');


Route::get('visita/actaVisita/{VIS_id}', [VisitaController::class, 'actaVisita'])->name('visita.actaVisita')->middleware('auth');

Route::post('visita/guardarActaVisita', [VisitaController::class, 'guardarActaVisita'])->name('visita.guardarActaVisita')->middleware('auth');

Route::get('visita/informeVisita/{VIS_id}/{flag?}', [VisitaController::class, 'informeVisita'])->name('visita.informeVisita')->middleware('auth');

// INDICADORES

Route::get('indicadores/', [IndicadoresController::class, 'index'])->name('indicadores.index');

Route::post('indicadores/actualizar/', [IndicadoresController::class, 'actualizar'])->name('indicadores.actualizar')->middleware('auth');

// AJUSTES
Route::get('ajustes/{id}', [AjustesController::class, 'index'])->name('ajustes.index')->middleware('auth');

// INFORME DE VISITAS
Route::get('informeVisitas', [InformeVisitasController::class, 'index'])->name('informeVisitas.index')->middleware('auth');

// Route::get('visita/mostrarActa/{VIS_id}', [VisitaController::class, 'mostrarActa'])->name('visita.mostrarActa')->middleware('auth');

// Route::get('/offline', function () {
//     return view('modules/laravelpwa/offline');
// });


