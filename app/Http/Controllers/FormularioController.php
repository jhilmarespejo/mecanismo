<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ModFormulario, ModAgrupadorFormulario, ModAdjunto, ModArchivo, ModPreguntasFormulario, ModVisita, ModBancoPregunta, };
use Illuminate\Support\Facades\{DB, Auth, Redirect, Validator, Session};
use Intervention\Image\Facades\Image;
use App\Http\Controllers\{VisitaController, CustomController};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;


class FormularioController extends Controller
{
    /**
     * MODULO CREACION Y ASIGNACION DE FORMULARIOS A VISITAS PROGRAMADAS
    */
    
    // Muestra la lista de formularios creados por el usuario
    //  ruta: .../formularios
    public function index() {
        $formularios = ModFormulario::all();
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Formularios', 'url' => ''],
        ];
        return view('formulario.index', compact('formularios', 'breadcrumbs'));
    }
    
    // Filtra los formularios por título
    public function filtrar(Request $request) {
        $titulo = $request->input('titulo');

        $formularios = ModFormulario::when($titulo, function ($query, $titulo) {
            return $query->where('FRM_titulo', 'ILIKE', '%' . $titulo . '%');
        })->get();

        return view('formulario._formularios', compact('formularios'));
    }
    
    
    // Viene del boton de "+ Asignar formulario" 
    ////  ruta: .../formulario/buscaFormularios/8
    public function eleccion($VIS_id, $VIS_tipo){
        if( !session('TES_tipo') ){
            return redirect('panel');
        }
        
        if( Auth::user()->rol == 'Administrador' ){
            $formularios = ModFormulario::select('FRM_id', 'FRM_titulo')->get();
            return view('formulario.formularios-eleccion', compact('formularios','VIS_tipo','VIS_id'));
        } else {
            return redirect('panel');
        }
    }

    
    // Función para asignar un formulario seleccionado de la lista de formulario a una visita programada (combobox: Seleccione un formulario:)
    public function asignar(Request $request)
    {
        $validatedData = $request->validate([
            'TES_tipo' => 'required|string',
            'EST_nombre' => 'required|string',
            'EST_id' => 'required|integer',
            'VIS_id' => 'required|integer',
            'opcion' => 'required|string',
            'FRM_id' => 'nullable|integer',
        ]);

        try {
            ModAgrupadorFormulario::create([
                'FK_FRM_id' => $validatedData['FRM_id'],
                'FK_VIS_id' => $validatedData['VIS_id'],
                'AGF_copia' => 1,
                'estado' => '1',
            ]);
            
            
            return redirect()->route('formulario.buscaFormularios', ['VIS_id' => $validatedData['VIS_id']])
                            ->with('success', 'Formulario asignado correctamente a la visita.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al asignar el formulario. Intente nuevamente.');
        }
    }
    
    // función para crear un nuevo formulario incuyendo N cantidad de preguntas
    // ruta: .../formulario/nuevo
    public function nuevo(){
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Formularios', 'url' => route('formularios.index')],
            ['name' => 'Nuevo Formulario', 'url' => ''],
        ];
        return view('formulario.formulario-nuevo', compact('breadcrumbs'));
    }


    // Función para guardar un nuevo formulario incluyendo N cantidad de preguntas
    // ruta: .../formulario/nuevo
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $datos = $request->all();
        
            $nuevoFormulario = ModFormulario::create([
                'FRM_titulo' => $datos['FRM_titulo'],
                'FRM_tipo' => $datos['FRM_tipo'],
                'FK_USER_id' => Auth::id(),
            ]);
            $nuevo_FRM_id = $nuevoFormulario->FRM_id;

            if (isset($datos['listaPreguntasJSON'])) {
                $preguntas = json_decode($datos['listaPreguntasJSON'], true);

                foreach ($preguntas as $index => $pregunta) {
                    $nuevaPregunta = ModBancoPregunta::create([
                        'BCP_pregunta'      => $pregunta['BCP_pregunta'],
                        'BCP_tipoRespuesta' => $pregunta['BCP_tipoRespuesta'],
                        'BCP_opciones'      => $pregunta['BCP_opciones'],
                        'BCP_complemento'   => $pregunta['BCP_complemento'],
                        'FK_CAT_id'         => 1,
                    ]);
                    $nuevo_BCP_id = $nuevaPregunta->BCP_id;

                    ModPreguntasFormulario::create([
                        'FK_FRM_id' => $nuevo_FRM_id,
                        'FK_BCP_id' => $nuevo_BCP_id,
                        'RBF_orden' => $pregunta['RBF_orden']
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('formulario.verFormularioCreado', $nuevo_FRM_id)
                            ->with('success', 'Formulario guardado correctamente.');
        
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('formulario.index')
                            ->with('error', 'Error al crear el formulario. Intente nuevamente: ' . $e->getMessage());
        }
    }


    // función para ver un formulario creado por el usuario
    // ruta: .../formulario/1283
    public function verFormularioCreado($id) {
        $formulario = ModFormulario::select('FRM_id', 'FRM_titulo')->where('FRM_id', $id)->first();
        
        $preguntas = ModBancoPregunta::join('r_bpreguntas_formularios', 'banco_preguntas.BCP_id', '=', 'r_bpreguntas_formularios.FK_BCP_id')
            ->where('r_bpreguntas_formularios.FK_FRM_id', $id)
            ->orderBy('r_bpreguntas_formularios.RBF_orden')
            ->orderBy('r_bpreguntas_formularios.RBF_id') 
            ->get();
            
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Formularios', 'url' => route('formularios.index')],
            ['name' => 'Formulario actual', 'url' => ''],
        ];
        
        return view('formulario.verFormularioCreado', compact('formulario', 'preguntas', 'breadcrumbs'));
    }

    // función para imprimir un formulario creado por el usuario
    // ruta: .../formulario/imprimir/1283
    public function imprimirFormulario($id, $tamano = 'carta')
    {
        $formulario = ModFormulario::findOrFail($id);
        
        $preguntas = ModBancoPregunta::join('r_bpreguntas_formularios', 'banco_preguntas.BCP_id', '=', 'r_bpreguntas_formularios.FK_BCP_id')
            ->where('r_bpreguntas_formularios.FK_FRM_id', $id)
            ->orderBy('r_bpreguntas_formularios.RBF_orden')
            ->orderBy('r_bpreguntas_formularios.RBF_id')
            ->get();
        
        $configPapel = $tamano === 'oficio' ? 'legal' : 'letter';
        
        $pdf = Pdf::loadView('formulario.formulario-imprimirFormulario', compact('formulario', 'preguntas', 'tamano'))
                  ->setPaper($configPapel, 'portrait');
        
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setOption('chroot', public_path());
        
        $nombreArchivo = 'Formulario_' . $id . '_' . str_replace(' ', '_', $formulario->FRM_titulo) . '.pdf';
        
        return $pdf->stream($nombreArchivo);
    }
    
    
    // función que muestra la vista pra editar un formulario seleccionado por el usuario
    // ruta: .../formulario/1283/editar
    public function editar($id)
    {
        $formulario = ModFormulario::findOrFail($id);
        
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
    

    // función que guarda los datos editados de un formulario seleccionado por el usuario 
    // ruta: .../formulario/1283/editar
    public function actualizar(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $datos = $request->all();
            
            $formulario = ModFormulario::findOrFail($id);
            $formulario->FRM_titulo = $datos['FRM_titulo'];
            $formulario->FRM_tipo = $datos['FRM_tipo'];
            $formulario->updatedBy = Auth::id();
            $formulario->updatedAt = now();
            $formulario->save();
            
            if (isset($datos['preguntasEliminar']) && !empty($datos['preguntasEliminar'])) {
                $preguntasEliminar = json_decode($datos['preguntasEliminar'], true);
                if (is_array($preguntasEliminar)) {
                    foreach ($preguntasEliminar as $rbfId) {
                        $relacion = ModPreguntasFormulario::where('RBF_id', $rbfId)->first();
                        if ($relacion) {
                            $relacion->delete();
                        }
                    }
                }
            }
            
            if (isset($datos['listaPreguntasJSON']) && !empty($datos['listaPreguntasJSON'])) {
                $preguntas = json_decode($datos['listaPreguntasJSON'], true);
                
                if (is_array($preguntas)) {
                    foreach ($preguntas as $pregunta) {
                        if (isset($pregunta['BCP_id']) && !empty($pregunta['BCP_id'])) {
                            ModPreguntasFormulario::where('FK_FRM_id', $id)
                                ->where('FK_BCP_id', $pregunta['BCP_id'])
                                ->update(['RBF_orden' => $pregunta['RBF_orden']]);
                            
                            if (array_key_exists('BCP_pregunta', $pregunta)) {
                                ModBancoPregunta::where('BCP_id', $pregunta['BCP_id'])
                                    ->update(['BCP_pregunta' => $pregunta['BCP_pregunta']]);
                            }
                        } else {
                            if (isset($pregunta['BCP_pregunta']) && isset($pregunta['BCP_tipoRespuesta'])) {
                                $nuevaPregunta = ModBancoPregunta::create([
                                    'BCP_pregunta' => $pregunta['BCP_pregunta'],
                                    'BCP_tipoRespuesta' => $pregunta['BCP_tipoRespuesta'],
                                    'BCP_opciones' => $pregunta['BCP_opciones'] ?? null,
                                    'BCP_complemento' => $pregunta['BCP_complemento'] ?? null,
                                    'FK_CAT_id' => 1,
                                ]);
                                
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
            
            DB::commit();
            
            return redirect()->route('formulario.verFormularioCreado', $id)
                            ->with('success', 'Formulario actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error al actualizar formulario: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('formulario.editar', $id)
                            ->with('error', 'Error al actualizar el formulario: ' . $e->getMessage());
        }
    }
    
    
    /**
     * FIN DEL MODULO CREACION, EDICION Y ASIGNACION DE FORMULARIOS A VISITAS PROGRAMADAS
    */



    /**
     * MODULO DE LISTADO DE FORMULARIOS, PREGUNTAS Y RESPUESTAS.
     */
    
    // Función para buscar formularios por visita incluyendo cantidad de preguntas y respuestas por formulario
    // ruta: .../formulario/buscaFormularios/8
    public function buscaFormularios( $VIS_id ){
        $VIS_tipo = ModVisita::select('VIS_tipo')->where('VIS_id', $VIS_id)->first();
        
        // CONSULTA OPTIMIZADA CON CONTEO CORRECTO DE PREGUNTAS
        $formularios = DB::select("
            SELECT 
            f.\"FRM_id\",
            f.\"FRM_titulo\",
            f.\"FRM_tipo\",
            af.\"FK_VIS_id\",
            af.\"AGF_id\",
            af.\"estado\",
            af.\"createdAt\",
            -- Agregado el createdBy del agrupador_formularios
            af.\"createdBy\" as \"AGF_createdBy\",
            -- Agregado el username del usuario creador
            u.\"username\" as \"USER_username\",
            -- CONTEO CORRECTO: Solo preguntas reales (no secciones/subsecciones)
            COALESCE(preguntas_reales.total_preguntas, 0) AS preguntas,
            -- CONTEO DE RESPUESTAS DADAS
            COALESCE(respuestas_dadas.total_respuestas, 0) AS respuestas
        FROM formularios f
        LEFT JOIN agrupador_formularios af ON af.\"FK_FRM_id\" = f.\"FRM_id\"
        -- Agregado JOIN con la tabla users para obtener el username
        LEFT JOIN users u ON u.\"id_usuario_dp\" = af.\"createdBy\"

        -- Subquery para contar solo preguntas reales
        LEFT JOIN (
            SELECT 
                rbf.\"FK_FRM_id\",
                COUNT(*) as total_preguntas
            FROM r_bpreguntas_formularios rbf
            INNER JOIN banco_preguntas bp ON bp.\"BCP_id\" = rbf.\"FK_BCP_id\"
            WHERE rbf.\"estado\" = 1 
            AND bp.\"BCP_tipoRespuesta\" NOT IN ('Sección', 'Subsección', 'Seccion', 'Subseccion', 'Etiqueta')
            GROUP BY rbf.\"FK_FRM_id\"
        ) preguntas_reales ON preguntas_reales.\"FK_FRM_id\" = f.\"FRM_id\"

        -- Subquery para contar respuestas dadas
        LEFT JOIN (
            SELECT 
                af_inner.\"FK_FRM_id\",
                af_inner.\"AGF_id\",
                COUNT(CASE 
                    WHEN r.\"RES_respuesta\" IS NOT NULL 
                    AND r.\"RES_respuesta\" != '' 
                    AND r.\"RES_respuesta\" != 'null' 
                    THEN 1 
                END) as total_respuestas
            FROM agrupador_formularios af_inner
            LEFT JOIN respuestas r ON r.\"FK_AGF_id\" = af_inner.\"AGF_id\"
            GROUP BY af_inner.\"FK_FRM_id\", af_inner.\"AGF_id\"
        ) respuestas_dadas ON respuestas_dadas.\"FK_FRM_id\" = f.\"FRM_id\" 
                            AND respuestas_dadas.\"AGF_id\" = af.\"AGF_id\"

        WHERE af.\"FK_VIS_id\" = :vis_id
        ORDER BY f.\"FRM_titulo\", af.\"AGF_copia\" DESC
        ", ['vis_id' => $VIS_id]);
        
      
        // Convertir a array para compatibilidad
        $formularios = array_map(function($item) {
            return (array) $item;
        }, $formularios);

        // Filtrar formularios según el rol del usuario
        if (Auth::user()->rol != 'Administrador') {
            // $user_id = Auth::id();
            $user_id = Auth::user()->id_usuario_dp;
            $grupo_formularios = [];
            
            // Agrupar por título de formulario
            $temp_grupo = CustomController::array_group($formularios, 'FRM_titulo');
            
            foreach ($temp_grupo as $titulo => $aplicaciones) {
                // Filtrar solo las aplicaciones creadas por el usuario actual
                $aplicaciones_filtradas = array_filter($aplicaciones, function($aplicacion) use ($user_id) {
                    return $aplicacion['AGF_createdBy'] == $user_id;
                });

                // dump($aplicaciones_filtradas);
                
                // Si hay aplicaciones del usuario, agregarlas al resultado final
                if (!empty($aplicaciones_filtradas)) {
                    $grupo_formularios[$titulo] = array_values($aplicaciones_filtradas);
                } else {
                    // Si no hay aplicaciones del usuario, mostrar solo el formulario principal sin aplicaciones
                    $grupo_formularios[$titulo] = [[
                        'FRM_id' => $aplicaciones[0]['FRM_id'],
                        'FRM_titulo' => $aplicaciones[0]['FRM_titulo'],
                        'FRM_tipo' => $aplicaciones[0]['FRM_tipo'],
                        'FK_VIS_id' => $aplicaciones[0]['FK_VIS_id'],
                        'AGF_id' => null,
                        'estado' => 'disponible',
                        'createdAt' => null,
                        'AGF_createdBy' => null,  // Añadido
                        'USER_username' => null,  // Añadido
                        'preguntas' => $aplicaciones[0]['preguntas'],
                        'respuestas' => null
                    ]];
                }
            }
        } else {
            // Para administradores, mostrar todo sin filtrar
            $grupo_formularios = CustomController::array_group($formularios, 'FRM_titulo');
        }

        if(!session('EST_nombre')){
            return redirect()->route('panel');
        }

        $VIS_tipo = $VIS_tipo->VIS_tipo;
        $colorVisita = CustomController::colorTipoVisita($VIS_tipo);

        return view('formulario.formularios-lista', compact('grupo_formularios', 'colorVisita', 'VIS_id', 'VIS_tipo'));
    }
    
    // public function buscarPregunta(Request $request){
    //     exit("buscar pregunta");
    //     $preguntas = ModBancoPregunta::select(
    //         'banco_preguntas.BCP_pregunta',
    //         'banco_preguntas.BCP_tipoRespuesta',
    //         'banco_preguntas.BCP_opciones',
    //         'banco_preguntas.BCP_complemento',
    //         'banco_preguntas.BCP_id'
    //         )
    //         ->where('banco_preguntas.BCP_pregunta', 'ilike', '%'.$request->pregunta.'%')
    //         ->where('banco_preguntas.estado', 1)
    //         ->orderBy('banco_preguntas.BCP_id')
    //         ->get()->toArray();
            
    //     return response()->json($preguntas);
    // }
    
}