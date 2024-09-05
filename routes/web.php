<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Formularios;
use App\Http\Livewire\CuestionarioIndex;
use App\Http\Livewire\Establecimientos;
use App\Http\Livewire\BancoPreguntasIndex;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Http\Controllers\{CuestionarioController, CategoriasController, EstablecimientosController, IndexController, RecomendacionesController, FormularioController, ReportesController, VisitaController, AjustesController,InformeVisitasController, AccesoController, InteroperabilidadController, UsersController, AsesoramientoController, IndicadorController,HistorialIndicadorController, EducacionController};

Route::post('/register', [RegisterController::class, 'store'])->name('register');


// Rutas para el inicio de sesión
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');



// Route::view('/acceso', 'acceso')->name('acceso');
// Route::view('/resgistro', 'resgistro')->name('registro');

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

Route::get('formulario/eleccion/{VIS_id}/{VIS_tipo}', [FormularioController::class, 'eleccion'])->name('formulario.index')->middleware('auth');
/* Ruta para mostrar los formularios relacionados a la vista */

Route::get('formulario/buscaFormularios/{VIS_id}', [FormularioController::class, 'buscaFormularios'])->name('formulario.buscaFormularios')->middleware('auth');

Route::post('formulario/nuevo', [FormularioController::class, 'nuevo'])->name('formulario.nuevo')->middleware('auth');
Route::post('formulario/sugerenciasFormularios', [FormularioController::class, 'sugerenciasFormularios'])->name('formulario.sugerenciasFormularios')->middleware('auth');

Route::post('formulario/buscarPregunta', [FormularioController::class, 'buscarPregunta'])->name('formulario.buscarPregunta')->middleware('auth');
Route::post('formulario/store', [FormularioController::class, 'store'])->name('formulario.store')->middleware('auth');

// Route::get('formulario/adjuntos/{est_id}/{frm_id?}', [FormularioController::class, 'adjuntosFormulario'])->name('formulario.adjuntos')->middleware('auth');




// INDEX
// Route::post('index/buscarIdForm', [IndexController::class, 'buscarIdFormulario'])->name('index.buscarIdForm')->middleware('auth');

Route::post('index/busquedaDinamica', [IndexController::class, 'busquedaDinamica'])->name('index.busquedaDinamica')->middleware('auth');

Route::post('index/buscarListasCasillas', [IndexController::class, 'buscarListasCasillas'])->name('index.buscarListasCasillas')->middleware('auth');


// LIVEWIRE
Route::get('formularios', Formularios::class)->middleware('auth');
Route::get('cuestionario', CuestionarioIndex::class);
// Route::get('establecimientos', Establecimientos::class);
Route::get('bancoDePreguntas', BancoPreguntasIndex::class)->middleware('auth');
Route::post('bancoDePreguntasEditar', BancoPreguntasIndex::class)->middleware('auth');

// CUESTIONARIO
Route::get('cuestionario', [CuestionarioController::class, 'index'])->name('cuestionario.index')->middleware('auth');
Route::post('cuestionario/buscarPreguntas', [CuestionarioController::class, 'buscarPreguntas'])->name('buscarPreguntas');

Route::post('cuestionario/guardaCuestionarioEditado', [CuestionarioController::class, 'store'])->name('cuestionario.guardaCuestionarioEditado')->middleware('auth');
Route::post('cuestionario/buscarRecomendaciones', [CuestionarioController::class, 'buscarRecomendaciones'])->name('cuestionario.buscarRecomendaciones')->middleware('auth');

Route::get('cuestionario/imprimir/{VIS_id}/{FRM_id}/{AGF_id}', [CuestionarioController::class, 'imprimirCuestionario'])->name('cuestionario.imprimir')->middleware('auth');

Route::get('cuestionario/duplicarCuestionario/{FRM_id}/{VIS_id}', [CuestionarioController::class, 'duplicarCuestionario'])->name('cuestionario.duplicarCuestionario')->middleware('auth');

Route::get('cuestionario/responder/{VIS_id}/{FRM_id}/{AGF_copia}', [CuestionarioController::class, 'responderCuestionario'])->name('cuestionario.responder')->middleware('auth');

Route::get('cuestionario/ver/{id}', [CuestionarioController::class, 'verCuestionario'])->name('cuestionario.ver')->middleware('auth');

Route::post('cuestionario/eliminar', [CuestionarioController::class, 'eliminarCuestionario'])->name('cuestionario.eliminar')->middleware('auth');

Route::post('cuestionario/guardarRespuestasCuestionario', [CuestionarioController::class, 'guardarRespuestasCuestionario'])->name('cuestionario.guardarRespuestasCuestionario')->middleware('auth');

Route::post('cuestionario/confirmaCuestionario/', [CuestionarioController::class, 'confirmaCuestionario'])->name('cuestionario.confirmaCuestionario')->middleware('auth');

Route::get('cuestionario/resultados/{id}', [CuestionarioController::class, 'resultadosCuestionario'])->name('cuestionario.resultados')->middleware('auth');



// RECOMENDACIONES
Route::get('recomendaciones/{VIS_id}', [RecomendacionesController::class, 'recomendaciones'])->name('recomendaciones')->middleware('auth');

Route::post('recomendaciones/cumplimiento', [RecomendacionesController::class, 'guardarCumplimientoRecomendaciones'])->name('recomendaciones.cumplimiento')->middleware('auth');
Route::post('recomendaciones/guardarNuevaRecomendacion', [RecomendacionesController::class, 'guardarNuevaRecomendacion'])->name('recomendaciones.nueva')->middleware('auth');
// Route::post('recomendaciones/guardarNuevaRecomendacionEstatal', [RecomendacionesController::class, 'guardarNuevaRecomendacionEstatal'])->name('recomendaciones.nuevaEstatal')->middleware('auth');

Route::get('/recomendacionesEstatales', [RecomendacionesController::class, 'recomendacionesEstatales'])->name('recomendaciones.recomendacionesEstatales')->middleware('auth');



// CATEGORIAS
Route::post('categorias/buscarSubcategoria', [CategoriasController::class, 'buscarSubcategoria'])->name('categorias.buscarSubcategoria')->middleware('auth');
Route::post('categorias/buscarPregunta', [CategoriasController::class, 'buscarPregunta'])->name('categorias.buscarPregunta')->middleware('auth');
Route::post('categorias/buscarElementos', [CategoriasController::class, 'buscarElementos'])->name('categorias.buscarElementos')->middleware('auth');
Route::get('categorias', [CategoriasController::class, 'index'])->name('categorias')->middleware('auth');
Route::post('categorias/guardaNuevaCategoria', [CategoriasController::class, 'guardaNuevaCategoria'])->name('categorias.guardaNuevaCategoria')->middleware('auth');


// ESTABLECIMIENTOS

Route::get('establecimientos/tipo', [EstablecimientosController::class, 'tipo'])->name('establecimientos')->middleware('auth');
Route::post('establecimientos/listarSegunTipo', [EstablecimientosController::class, 'listarSegunTipo'])->name('establecimientos.listarSegunTipo')->middleware('auth');
Route::post('establecimientos/guardarNuevoEstablecimiento', [EstablecimientosController::class, 'guardarNuevoEstablecimiento'])->name('establecimientos.listaguardarNuevoEstablecimientorPorTipo')->middleware('auth');

Route::get('establecimientos/index', [EstablecimientosController::class, 'index'])->name('establecimientos.index')->middleware('auth');
Route::get('establecimientos/mostrar/{id}', [EstablecimientosController::class, 'mostrar'])->name('establecimientos.mostrar');

Route::get('establecimientos/crear', [EstablecimientosController::class, 'crear'])->name('establecimientos.crear');
Route::post('establecimientos/almacenar', [EstablecimientosController::class, 'almacenar'])->name('establecimientos.almacenar');

    // estanblecimientos_info
    Route::get('establecimientos/infoMostrar/{EST_id}', [EstablecimientosController::class, 'infoMostrar'])->name('establecimientos.infoMostrar');
    Route::post('establecimientos/infoActualizar', [EstablecimientosController::class, 'infoActualizar'])->name('establecimientos.infoActualizar');


    // estanblecimientos_personal
    Route::get('establecimientos/personalMostrar/{EST_id}', [EstablecimientosController::class, 'personalMostrar'])->name('establecimientos.personalMostrar');
    Route::post('establecimientos/personalActualizar', [EstablecimientosController::class, 'personalActualizar'])->name('establecimientos.personalActualizar');

// REPORTES
Route::get('reportes', [ReportesController::class, 'index'])->name('reportes');

// VISITAS
Route::post('visita/guardarNuevaVisita', [VisitaController::class, 'guardarNuevaVisita'])->name('visita.guardarNuevaVisita')->middleware('auth');
Route::get('visita/historial/{id}', [VisitaController::class, 'historial'])->name('visita.historial')->middleware('auth');
Route::get('visita/actaVisita/{VIS_id}', [VisitaController::class, 'actaVisita'])->name('visita.actaVisita')->middleware('auth');
Route::post('visita/guardarActaVisita', [VisitaController::class, 'guardarActaVisita'])->name('visita.guardarActaVisita')->middleware('auth');
Route::get('visita/informeVisita/{VIS_id}/{flag?}', [VisitaController::class, 'informeVisita'])->name('visita.informeVisita')->middleware('auth');
Route::get('visita/resumen', [VisitaController::class, 'resumen'])->name('visita.resumen')->middleware('auth');



// INFORME DE VISITAS
Route::get('informeVisitas', [InformeVisitasController::class, 'index'])->name('informeVisitas.index')->middleware('auth');


// Route::get('visita/mostrarActa/{VIS_id}', [VisitaController::class, 'mostrarActa'])->name('visita.mostrarActa')->middleware('auth');

// Route::get('/offline', function () {
//     return view('modules/laravelpwa/offline');
// });

// USERS
// verificar y editar usuarios
 Route::get('users/list', [UsersController::class, 'list'])->middleware('auth')->name('users.list');

 Route::delete('users/{id}', [UsersController::class, 'destroy'])->middleware('auth')->name('users.destroy');
 Route::post('users/changeState', [UsersController::class, 'changeState'])->middleware('auth')->name('users.changeState');


 // ---- MÓDULO DE ASESORAMIENTO ----------


// Route::get('/asesoramientos', [AsesoramientoController::class, 'index'])->middleware('auth');
// Route::post('/asesoramientos/crear', [AsesoramientoController::class, 'crear'])->middleware('auth');
// Route::post('/asesoramientos/store', [AsesoramientoController::class, 'store'])->middleware('auth');
// Route::get('/asesoramientos/{id}', [AsesoramientoController::class, 'show'])->middleware('auth');
// Route::get('/asesoramientos/{id}/edit', [AsesoramientoController::class, 'edit'])->middleware('auth');
// Route::put('/asesoramientos/{id}', [AsesoramientoController::class, 'update'])->middleware('auth');
// Route::delete('/asesoramientos/{id}', [AsesoramientoController::class, 'destroy'])->middleware('auth');

Route::get('/asesoramientos', [AsesoramientoController::class, 'index'])->middleware('auth')->name('asesoramientos.index');
Route::get('/asesoramientos/create', [AsesoramientoController::class, 'create'])->middleware('auth')->name('asesoramientos.create');
Route::post('/asesoramientos', [AsesoramientoController::class, 'store'])->middleware('auth')->name('asesoramientos.store');
Route::get('/asesoramientos/{id}', [AsesoramientoController::class, 'show'])->middleware('auth')->name('asesoramientos.show');
Route::get('/asesoramientos/{id}/edit', [AsesoramientoController::class, 'edit'])->middleware('auth')->name('asesoramientos.edit');
Route::put('/asesoramientos/{id}', [AsesoramientoController::class, 'update'])->middleware('auth')->name('asesoramientos.update');
Route::delete('/asesoramientos/{id}', [AsesoramientoController::class, 'destroy'])->middleware('auth')->name('asesoramientos.destroy');


//----MODULO DE INDICADORES --------------------------

Route::get('/indicadores/panel', [IndicadorController::class, 'panel'])->middleware('auth');
Route::get('/indicadores/actualizar', [IndicadorController::class, 'actualizar'])->middleware('auth');
Route::post('/indicadores/guardar', [IndicadorController::class, 'guardar'])->middleware('auth')->name('guardar.indicadores');

//--------MODULO DE EDUCACION -------
// Route::resource('educacion', EducacionController::class)->middleware('auth');

// Ruta personalizada para el index que recibe un ID
Route::get('educacion/index/{id}', [EducacionController::class, 'index'])->name('educacion.index')->middleware('auth');
// Las demás rutas del resource (store, create, show, etc.)
Route::resource('educacion', EducacionController::class)->except('index')->middleware('auth');


// AJUSTES
Route::get('ajustes/{id}', [AjustesController::class, 'index'])->name('ajustes.index')->middleware('auth');



// INTEROPERABILIDAD
Route::get('interoperabilidad', [interoperabilidadController::class, 'index'])->name('interoperabilidad.index');//->middleware('auth');

// MODULO DE EDUCACION
Route::resource('educacion', EducacionController::class);