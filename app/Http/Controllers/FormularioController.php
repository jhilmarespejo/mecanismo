<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ModFormulario, ModFormularioArchivo, ModAdjunto, ModArchivo, ModPreguntasFormulario, ModVisita, ModBancoPregunta, };
use Illuminate\Support\Facades\{DB, Auth, Redirect, Validator, Session};
use Intervention\Image\Facades\Image;
use App\Http\Controllers\{VisitaController, CustomController};
use Barryvdh\DomPDF\Facade\Pdf;



// use Illuminate\Support\Facades\Storage;

class FormularioController extends Controller
{
    // Método para mostrar todos los formularios
    public function index() {
        $formularios = ModFormulario::all();
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Formularios', 'url' => ''],
        ];
        return view('formulario.index', compact('formularios', 'breadcrumbs'));
    }

    // Método para devolver los formularios filtrados (usado para AJAX)
    public function filtrar(Request $request) {
        $titulo = $request->input('titulo');

        // Filtrar formularios si hay un título
        $formularios = ModFormulario::when($titulo, function ($query, $titulo) {
            return $query->where('FRM_titulo', 'ILIKE', '%' . $titulo . '%');
        })->get();

        // Devolver solo el HTML de los formularios filtrados
        return view('formulario._formularios', compact('formularios'));
    }

    /** Funcion para crear un formulario nuevo
     */
    public function eleccion($VIS_id, $VIS_tipo){

        // Si las variables de sesion no existen redireccionar al usuario al panel
        if( !session('TES_tipo') ){
            return redirect('panel');
        }
        DB::enableQueryLog();
        if( Auth::user()->rol == 'Administrador' ){
            $formularios = ModFormulario::select('FRM_id', 'FRM_titulo')->get();
            return view('formulario.formularios-eleccion', compact('formularios','VIS_tipo','VIS_id'));
        } else {
            return redirect('panel');
        }
    }
    public function buscarPregunta(Request $request){
        $preguntas = ModBancoPregunta::select(
            // 'categorias.CAT_categoria as categoria',
            // 'c_parent.CAT_categoria AS subcategoria',
            'banco_preguntas.BCP_pregunta',
            'banco_preguntas.BCP_tipoRespuesta',
            'banco_preguntas.BCP_opciones',
            'banco_preguntas.BCP_complemento',
            'banco_preguntas.BCP_id'
            // 'categorias.CAT_id',
            // 'categorias.FK_CAT_id'
            )
            // ->leftJoin('categorias', 'banco_preguntas.FK_CAT_id', '=', 'categorias.CAT_id')
            // ->leftJoin('categorias as c_parent', 'categorias.FK_CAT_id', '=', 'c_parent.CAT_id')
            ->where('banco_preguntas.BCP_pregunta', 'ilike', '%'.$request->pregunta.'%')
            ->where('banco_preguntas.estado', 1)
            ->orderBy('banco_preguntas.BCP_id')
            ->get()->toArray();
            // $preguntas = CustomController::ordenaPreguntasCategorias($preguntas);
            // dump( $preguntas ); exit
            return response()->json($preguntas);
    
    }
    
    public function asignar(Request $request)
        {
            // Validar los datos entrantes
            $validatedData = $request->validate([
                'TES_tipo' => 'required|string',
                'EST_nombre' => 'required|string',
                'EST_id' => 'required|integer',
                'VIS_id' => 'required|integer',
                'opcion' => 'required|string',
                'FRM_id' => 'nullable|integer',
            ]);

            try {
                // Insertar en la tabla agrupador_formularios
                DB::table('agrupador_formularios')->insert([
                    'FK_FRM_id' => $validatedData['FRM_id'], // ID del formulario
                    'FK_VIS_id' => $validatedData['VIS_id'], // ID de la visita
                    'AGF_copia' => 1, // Opcional: Puedes usar otra lógica para manejar copias
                    'estado' => '1', // Por defecto, se inserta como activo
                ]);
                
                // Redirigir con mensaje de éxito a la ruta formulario/buscaFormularios/{VIS_id}
                return redirect()->route('formulario.buscaFormularios', ['VIS_id' => $validatedData['VIS_id']])
                                ->with('success', 'Formulario asignado correctamente a la visita.');
            } catch (\Exception $e) {
                // Manejar errores y redirigir con mensaje de error
                return redirect()->back()->with('error', 'Ocurrió un error al asignar el formulario. Intente nuevamente.');
            }
        }
            
    

    public function buscaFormularios( $VIS_id ){
        // dump($VIS_id );exit;
        //Obtener los formularios asociados con esta visita acompañado de las copias correspondientes
        $VIS_tipo = ModVisita::select('VIS_tipo')->where('VIS_id', $VIS_id)->first();


        DB::enableQueryLog();
        // $formularios = ModFormulario::from('formularios as f')
        // ->select('f.FRM_id','f.FRM_titulo','f.FRM_tipo','af.FK_VIS_id', 'af.AGF_id', 'af.estado', 'af.createdAt')
        // ->leftjoin('agrupador_formularios as af', 'af.FK_FRM_id', 'f.FRM_id')
        // ->where('af.FK_VIS_id', $VIS_id)
        // ->get()->toArray();


        $formularios =  ModFormulario::from('formularios as f')
        ->select('f.FRM_id','f.FRM_titulo','f.FRM_tipo','f.FRM_tipo','af.FK_VIS_id','af.AGF_id','af.estado','af.createdAt',
            DB::raw('(SELECT COUNT(*) FROM r_bpreguntas_formularios WHERE r_bpreguntas_formularios."FK_FRM_id" = f."FRM_id") AS preguntas'),
            DB::raw('(SELECT COUNT(*) FROM respuestas as r INNER JOIN r_bpreguntas_formularios as rb ON r."FK_RBF_id" = rb."RBF_id" WHERE af."AGF_id" = r."FK_AGF_id") AS respuestas')
        )
        ->leftJoin('agrupador_formularios as af', 'af.FK_FRM_id', 'f.FRM_id')
        ->where('af.FK_VIS_id', $VIS_id)
        ->get()->toArray();

        $quries = DB::getQueryLog();
        // dump( $quries );
        // exit;

        $grupo_formularios = CustomController::array_group($formularios, 'FRM_titulo');
        if(!session('EST_nombre')){
            dump('panel' );
            return redirect()->route('panel');
        }
        
        $VIS_tipo = $VIS_tipo->VIS_tipo;
        // dump( $grupo_formularios );//exit;
        $colorVisita = CustomController::colorTipoVisita( $VIS_tipo );

        return view('formulario.formularios-lista', compact('grupo_formularios', 'colorVisita', 'VIS_id', 'VIS_tipo'));
    }

    public function store(Request $request)
    {
        //dump($request->all());exit;
        // Iniciar la transacción
        DB::beginTransaction();
        try {
            $datos = $request->all();
        
            // **Paso 1: Crear el formulario**
            $nuevoFormulario = ModFormulario::create([
                'FRM_titulo' => $datos['FRM_titulo'],
                'FRM_tipo' => $datos['FRM_tipo'],
                'FK_USER_id' => Auth::id(),
            ]);
            $nuevo_FRM_id = $nuevoFormulario->FRM_id; // ID del formulario creado

            // **Paso 2: Procesar preguntas desde JSON si existe**
            if (isset($datos['listaPreguntasJSON'])) {
                $preguntas = json_decode($datos['listaPreguntasJSON'], true);

                foreach ($preguntas as $index => $pregunta) {
                    // **Paso 3: Insertar la pregunta en la tabla "banco_preguntas"**
                    $nuevaPregunta = ModBancoPregunta::create([
                        'BCP_pregunta'      => $pregunta['BCP_pregunta'],
                        'BCP_tipoRespuesta' => $pregunta['BCP_tipoRespuesta'],
                        'BCP_opciones'      => $pregunta['BCP_opciones'], // Asumimos que ya viene formateado correctamente
                        'BCP_complemento'   => $pregunta['BCP_complemento'],
                        
                        'FK_CAT_id'         => 1,
                    ]);
                    $nuevo_BCP_id = $nuevaPregunta->BCP_id; // Obtener ID de la pregunta

                    // **Paso 4: Relacionar la pregunta con el formulario**
                    ModPreguntasFormulario::create([
                        'FK_FRM_id' => $nuevo_FRM_id,  // ID del formulario
                        'FK_BCP_id' => $nuevo_BCP_id,  // ID de la pregunta
                        'RBF_orden' => $pregunta['RBF_orden'] // Orden directo del JSON
                    ]);
                }
            }
            
            // Confirmar la transacción
            DB::commit();
            
            // **Redirigir al método show() con el ID recién creado**
            return redirect()->route('formulario.verFormularioCreado', $nuevo_FRM_id)
                            ->with('success', 'Formulario guardado correctamente.');
        
        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollback();
            dd($e->getMessage());

            // Registrar el error
            return redirect()->route('formulario.index')
                            ->with('error', 'Error al crear el formulario. Intente nuevamente: ' . $e->getMessage());
        }
    }//store
    
    public function verFormularioCreado($id) {
        // Obtener el formulario
        $formulario = ModFormulario::select('FRM_id', 'FRM_titulo')->where('FRM_id', $id)->first();
        
        // Obtener las preguntas asociadas al formulario
        $preguntas = ModBancoPregunta::join('r_bpreguntas_formularios', 'banco_preguntas.BCP_id', '=', 'r_bpreguntas_formularios.FK_BCP_id')
            ->where('r_bpreguntas_formularios.FK_FRM_id', $id)
            ->orderBy('r_bpreguntas_formularios.RBF_orden') // Primera columna
            ->orderBy('r_bpreguntas_formularios.RBF_id') 
            ->get();
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Formularios', 'url' => route('formularios.index')],
            ['name' => 'Formulario actual', 'url' => ''],
        ];
        // Enviar los datos a la vista
        return view('formulario.verFormularioCreado', compact('formulario', 'preguntas', 'breadcrumbs'));
    }
    
    public function imprimirFormulario($id)
    {
        // Obtener los datos del formulario
        $formulario = ModFormulario::findOrFail($id);
    
        // Obtener las preguntas asociadas
        $preguntas = ModBancoPregunta::join('r_bpreguntas_formularios', 'banco_preguntas.BCP_id', '=', 'r_bpreguntas_formularios.FK_BCP_id')
            ->where('r_bpreguntas_formularios.FK_FRM_id', $id)
            ->orderBy('r_bpreguntas_formularios.RBF_orden')
            ->orderBy('r_bpreguntas_formularios.RBF_id')
            ->get();
        
        // Añadir información para el encabezado
        $encabezado = [
            'titulo' => 'Encabezado principal',
            'logo' => public_path('images/logo.png') // Ruta del logo (asegúrate de que exista)
        ];
    
        // Configurar opciones del PDF
        $options = [
            'margin-top' => '30mm', // Margen superior para el encabezado
            'margin-bottom' => '15mm',
            'margin-left' => '10mm',
            'margin-right' => '10mm',
            'footer-font-size' => 8,
            'footer-left' => 'Página [page] de [topage]',
            'footer-right' => 'Fecha: ' . date('d/m/Y')
        ];
    
        // Cargar la vista para el PDF con tamaño de papel configurable
        // Puedes usar 'letter' para carta o 'legal' para oficio
        $pdf = Pdf::loadView(
            'formulario.formulario-imprimirFormulario', 
            compact('formulario', 'preguntas', 'encabezado')
        )
        ->setPaper('letter', 'portrait')
        ->setOptions($options);
    
        // Descargar o visualizar el PDF
        return $pdf->stream('Formulario_' . $formulario->FRM_id . '.pdf');
    }
    
    public function nuevo(){
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Formularios', 'url' => route('formularios.index')],
            ['name' => 'Nuevo Formulario', 'url' => ''],
        ];
        return view('formulario.formulario-nuevo', compact('breadcrumbs'));
    }

    // PARA EDICION DE FORMULARIOS
    public function editar($id)
    {
        // Obtener el formulario
        $formulario = ModFormulario::findOrFail($id);
        
        // Obtener las preguntas asociadas al formulario con sus relaciones
        $preguntas = ModBancoPregunta::join('r_bpreguntas_formularios', 'banco_preguntas.BCP_id', '=', 'r_bpreguntas_formularios.FK_BCP_id')
            ->where('r_bpreguntas_formularios.FK_FRM_id', $id)
            ->orderBy('r_bpreguntas_formularios.RBF_orden')
            ->orderBy('r_bpreguntas_formularios.RBF_id')
            ->get();
        
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Formularios', 'url' => route('formularios.index')],
            ['name' => 'Editar Formulario', 'url' => ''],
        ];
        
        return view('formulario.formulario-editar', compact('formulario', 'preguntas', 'breadcrumbs'));
    }
        

    public function actualizar(Request $request, $id)
    {
        // Iniciar la transacción
        DB::beginTransaction();
        
        try {
            $datos = $request->all();
            
            // Actualizar el formulario
            $formulario = ModFormulario::findOrFail($id);
            $formulario->FRM_titulo = $datos['FRM_titulo'];
            $formulario->FRM_tipo = $datos['FRM_tipo'];
            $formulario->updatedBy = Auth::id();
            $formulario->updatedAt = now();
            $formulario->save();
            
            // Si hay preguntas para eliminar
            if (isset($datos['preguntasEliminar']) && !empty($datos['preguntasEliminar'])) {
                $preguntasEliminar = json_decode($datos['preguntasEliminar'], true);
                if (is_array($preguntasEliminar)) {
                    foreach ($preguntasEliminar as $rbfId) {
                        // Buscar la relación pregunta-formulario
                        $relacion = ModPreguntasFormulario::where('RBF_id', $rbfId)->first();
                        if ($relacion) {
                            // Eliminar la relación
                            $relacion->delete();
                        }
                    }
                }
            }
            
            // Procesar preguntas (existentes y nuevas)
            if (isset($datos['listaPreguntasJSON']) && !empty($datos['listaPreguntasJSON'])) {
                $preguntas = json_decode($datos['listaPreguntasJSON'], true);
                
                if (is_array($preguntas)) {
                    foreach ($preguntas as $pregunta) {
                        // 1. Si la pregunta tiene un BCP_id, significa que ya existe - actualizamos su orden
                        if (isset($pregunta['BCP_id']) && !empty($pregunta['BCP_id'])) {
                            ModPreguntasFormulario::where('FK_FRM_id', $id)
                                ->where('FK_BCP_id', $pregunta['BCP_id'])
                                ->update(['RBF_orden' => $pregunta['RBF_orden']]);
                        } 
                        // 2. Si no tiene BCP_id, es una pregunta nueva - la creamos
                        else {
                            // Verificar que tengamos todos los datos necesarios
                            if (isset($pregunta['BCP_pregunta']) && isset($pregunta['BCP_tipoRespuesta'])) {
                                $nuevaPregunta = ModBancoPregunta::create([
                                    'BCP_pregunta' => $pregunta['BCP_pregunta'],
                                    'BCP_tipoRespuesta' => $pregunta['BCP_tipoRespuesta'],
                                    'BCP_opciones' => $pregunta['BCP_opciones'] ?? null,
                                    'BCP_complemento' => $pregunta['BCP_complemento'] ?? null,
                                    'FK_CAT_id' => 1, // Categoría por defecto
                                ]);
                                
                                // Relacionar la pregunta con el formulario
                                ModPreguntasFormulario::create([
                                    'FK_FRM_id' => $id,
                                    'FK_BCP_id' => $nuevaPregunta->BCP_id,
                                    'RBF_orden' => $pregunta['RBF_orden'] ?? 1
                                ]);
                            }
                        }
                    }
                }
            }
            
            // Confirmar la transacción
            DB::commit();
            
            return redirect()->route('formulario.verFormularioCreado', $id)
                            ->with('success', 'Formulario actualizado correctamente.');
        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollback();
            
            // Registrar el error en logs
            // Log::error('Error al actualizar formulario: ' . $e->getMessage());
            // Log::error($e->getTraceAsString());
            
            return redirect()->route('formulario.editar', $id)
                            ->with('error', 'Error al actualizar el formulario: ' . $e->getMessage());
        }
    }
    
}
    
    // public function nuevo(Request $request){
    //     // dump($request->all());exit;
    //     $validated = $request->validate([
    //         'opcion' => 'required',
    //         'FRM_id' => 'sometimes|required_if:opcion,asignar,anterior', // Regla de validación condicional
    //         'nuevo_formulario' => 'sometimes|required_if:opcion,nuevo',
    //     ], [
    //         'required' => 'Debe seleccionar una opción',
    //         'FRM_id.required_if' => 'Debe seleccionar un formulario',
    //         'nuevo_formulario.required_if' => 'Debe ingresar un nombre para el nuevo formulario',
    //     ]);

    //     if( $request->opcion == 'nuevo' ){
    //         //crear formulario desde 0

    //             // dump($preguntas);exit;
    //         return view('formulario.formulario-nuevo', ['nuevo_formulario' => $request->nuevo_formulario]);
    //     }
    //     elseif($request->opcion== 'anterior' ){
    //         // Tomar el valor del input nuevo_formulario y buscar formularios anteriores
    //         // buscar formularios segun el el valor del input nuevo_formulario en la tabla formularios y mostra sus categorias, preguntas y opcciones en pantalla para editar
    //         // dump($request->all());
    //         $formulario = ModFormulario::from('formularios as f')
    //         ->select('f.FRM_titulo',
    //         'bp.BCP_pregunta as Pregunta','bp.BCP_tipoRespuesta', 'bp.BCP_opciones', 'bp.BCP_complemento',
    //         'categorias1.CAT_categoria as categoria',
    //         'categorias2.CAT_categoria as subcategoria')
    //         ->join('r_bpreguntas_formularios as rb', 'f.FRM_id', 'rb.FK_FRM_id')
    //         ->join('banco_preguntas as bp', 'rb.FK_BCP_id', 'bp.BCP_id')
    //         ->leftJoin('categorias as categorias1', 'bp.FK_CAT_id', 'categorias1.CAT_id')
    //         ->leftJoin('categorias as categorias2', 'categorias1.FK_CAT_id', 'categorias2.CAT_id')
    //         ->where('f.FRM_id', $request->FRM_id)
    //         ->get()->toArray();

    //         $preguntas_ordenadas = CustomController::ordenaPreguntasCategorias($formulario);
    //         $elementos_formulario = CustomController::array_group( $preguntas_ordenadas, 'subcategoria' );
    //         // dump($datos_agrupados);exit;
    //         return view('formulario.formulario-anterior', compact('elementos_formulario'));
    //     }

    //     elseif($request->opcion== 'asignar' ){
    //         //Tomar el valor del input nuevo_formulario y buscar formularios anteriores
    //         // visualizar todos sus datos y asignarle a esta visita
    //     }
    // }

    

    /* Funcion para obtener los formularios aplicados en la visita
        $id = Visita ID
    */
    

    // public function store(Request $request)
    // {
    //     // Recuperar todos los datos del formulario
    //     $datos = $request->all();
    //     dd($datos);
    //     // Crear un array para almacenar las preguntas procesadas
    //     $preguntas = [];

    //     // Procesar cada pregunta
    //     foreach ($datos['pregunta'] as $index => $preguntaTexto) {

    //         // Obtener las opciones asociadas a la pregunta
    //         $opcionesClave = 'opciones_' . ($index + 1); // Clave dinámica para opciones
    //         $opciones = isset($datos[$opcionesClave]) ? $datos[$opcionesClave] : [];

    //         // Convertir las opciones al formato JSON requerido {"0":"A","1":"B","2":"C"}
    //         $opcionesJSON = json_encode(array_values($opciones), JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
    //         if($opcionesJSON == '{}'){
    //             $opcionesJSON = null;
    //         }

    //         // Obtener tipo de respuesta
    //         $tipoRespuesta = isset($datos['tipoRespuesta'][$index]) ? $datos['tipoRespuesta'][$index] : null;

    //         // Obtener número de orden
    //         $orden = isset($datos['RBF_orden'][$index]) ? $datos['RBF_orden'][$index] : ($index + 1);
            
    //         // Construir el array de preguntas
    //         $preguntas[] = [
    //             'BCP_pregunta'       => null,
    //             'BCP_pregunta'       => $preguntaTexto,               // Texto de la pregunta
    //             'BCP_tipoRespuesta'  => $tipoRespuesta,               // Tipo de respuesta
    //             'BCP_opciones'       => $opcionesJSON,                // Opciones en formato JSON
    //             'BCP_complemento'    => null,                         // Complemento (nulo por defecto)
    //             'BCP_aclaracion'     => null,                         // Aclaraciones (nulo por defecto)
    //             'FK_CAT_id'          => 1,                            // ID de categoría
    //             'estado'             => 1,                            // Estado activo
    //             'RBF_orden'          => $orden                        // Número de orden
    //         ];
    //     }


    //     dump($preguntas);
    //     exit;
    //     // Guardar las preguntas en la base de datos
    //     // DB::table('banco_preguntas')->insert($preguntas);
        
    //     // Redireccionar con un mensaje de éxito
    //     return redirect()->back()->with('success', 'Formulario guardado correctamente.');
    // }
    
    // guarda el formulario creado dinamicamente
   
    
    
    // public function store(Request $request)
    // {
    //     // Iniciar la transacción
    //     DB::beginTransaction();
    //     dd($request->all());
    //     try {
    //         $datos = $request->all();
    
    //         // **Paso 1: Crear el formulario**
    //         $nuevoFormulario = ModFormulario::create([
    //             'FRM_titulo' => $datos['FRM_titulo'],
    //             'FK_USER_id' => Auth::id(),
    //         ]);
    //         $nuevo_FRM_id = $nuevoFormulario->FRM_id; // ID del formulario creado
    
    //         // **Paso 2: Procesar preguntas**
    //         foreach ($datos['pregunta'] as $index => $preguntaTexto) {
    //             // Obtener el tipo de respuesta
    //             $tipoRespuesta = $datos['tipoRespuesta'][$index];
    
    //             // **Inicializar valores predeterminados**
    //             $opcionesJSON = null;
    //             $complemento = null;
    
    //             // **Opciones: Asignar solo si el tipo de respuesta lo requiere**
    //             if (in_array($tipoRespuesta, ['Lista desplegable', 'Casilla verificación'])) {
    //                 // Clave dinámica ajustada para coincidir con las preguntas (index + 1)
    //                 $opcionesClave = 'opciones_' . ($index + 1);
    //                 $opciones = isset($datos[$opcionesClave]) ? $datos[$opcionesClave] : [];
    
    //                 // Convertir opciones al formato JSON {"0":"A","1":"B"}
    //                 $opcionesJSON = !empty($opciones)
    //                     ? json_encode(array_values($opciones), JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE)
    //                     : null;
    //                     dump($preguntaTexto);
    //                     dump($opcionesJSON);
    //             }
    
    //             // **Complemento: Asignar solo si el tipo de respuesta lo requiere**
    //             if ($tipoRespuesta === 'Lista desplegable') {
    //                 // Clave dinámica ajustada para coincidir con las preguntas (index + 1)
    //                 $complementoClave = 'BCP_complemento_' . ($index + 1);
    //                 $complemento = isset($datos[$complementoClave]) ? $datos[$complementoClave] : null;
    //             }
    
    //             // **Orden corregido para ser correlativo**
    //             $orden = $index + 1;
    
    //             // **Paso 3: Insertar la pregunta en la tabla "banco_preguntas"**
    //             $nuevaPregunta = ModBancoPregunta::create([
    //                 'BCP_pregunta'       => $preguntaTexto,
    //                 'BCP_tipoRespuesta'  => $tipoRespuesta,
    //                 'BCP_opciones'       => $opcionesJSON, // Opciones formateadas
    //                 'BCP_complemento'    => $complemento,  // Complemento asignado
    //                 'BCP_aclaracion'     => null,
    //                 'FK_CAT_id'          => 1, // Cambiar según categoría
    //                 'estado'             => 1,
    //             ]);
                
    //             $test =([
    //                 'BCP_pregunta'       => $preguntaTexto,
    //                 'BCP_tipoRespuesta'  => $tipoRespuesta,
    //                 'BCP_opciones'       => $opcionesJSON, // Opciones formateadas
    //                 'BCP_complemento'    => $complemento,  // Complemento asignado
    //                 'BCP_aclaracion'     => null,
    //                 'FK_CAT_id'          => 1, // Cambiar según categoría
    //                 'estado'             => 1,
    //             ]);
    //             // dump($nuevaPregunta);
    //             $nuevo_BCP_id = $nuevaPregunta->BCP_id; // Obtener ID de la pregunta
    
    //             // **Paso 4: Relacionar la pregunta con el formulario**
    //             ModPreguntasFormulario::create([
    //                 'FK_FRM_id' => $nuevo_FRM_id,   // ID del formulario
    //                 'FK_BCP_id' => $nuevo_BCP_id,  // ID de la pregunta
    //                 'RBF_orden' => $orden          // Orden correlativo
    //             ]);
    //         }
            
    //         // Confirmar la transacción
    //         // DB::commit();
    //         dd($test);
    //         exit;
    //         // **Redirigir al método show() con el ID recién creado**
    //         return redirect()->route('formulario.verFormularioCreado', $nuevo_FRM_id)
    //                          ->with('success', 'Formulario guardado correctamente.');
    
    //     } catch (\Exception $e) {
    //         // Revertir cambios en caso de error
    //         DB::rollback();
    
    //         // Registrar el error
    //         dd('Error al crear formulario: ' . $e->getMessage());
    
    //         // Redirigir con mensaje de error
    //         return redirect()->route('formulario.index')
    //                          ->with('error', 'Error al crear el formulario. Intente nuevamente.');
    //     }
    // }
    
        
    
            
    
    // public function verFormularioCreado($id) {
    //     // Obtener el formulario
    //     $formulario = ModFormulario::select('FRM_id', 'FRM_titulo')->where('FRM_id', $id)->first();
        
    //     // Obtener las preguntas asociadas al formulario
    //     $preguntas = ModBancoPregunta::join('r_bpreguntas_formularios', 'banco_preguntas.BCP_id', '=', 'r_bpreguntas_formularios.FK_BCP_id')
    //         ->where('r_bpreguntas_formularios.FK_FRM_id', $id)
    //         ->orderBy('r_bpreguntas_formularios.RBF_orden') // Primera columna
    //         ->orderBy('r_bpreguntas_formularios.RBF_id') 
    //         ->get();

    //     // Enviar los datos a la vista
    //     return view('formulario.verFormularioCreado', compact('formulario', 'preguntas'));
    // }
    
//}
