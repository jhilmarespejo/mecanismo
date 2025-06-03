<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{ModEstablecimiento, ModIndicador, ModHistorialIndicador};
use App\Http\Controllers\CustomController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


class IndicadorController extends Controller
{
    // public function actualizar( Request $request ) {
    //     DB::enableQueryLog();
    //     //$gestion = date('Y');
    //     /*$categorias = ModIndicador::select(
    //         'indicadores.*',
    //         'historial_indicadores.HIN_respuesta',
    //         'historial_indicadores.HIN_gestion',
    //         'historial_indicadores.HIN_informacion_complementaria'
            
    //         )
    //         ->leftJoin('historial_indicadores', 'indicadores.IND_id', 'historial_indicadores.FK_IND_id')
    //         ->where('indicadores.IND_estado', '1')
    //         ->where('historial_indicadores.HIN_gestion', $gestion)
    //         ->orderBy('IND_id')->get()->toArray();*/
        
    //     $gestion = $request->query('gestion', date('Y'));
    //     $categorias = ModIndicador::select(
    //         'indicadores.*',
    //         'historial_indicadores.HIN_respuesta',
    //         'historial_indicadores.HIN_gestion',
    //         'historial_indicadores.HIN_informacion_complementaria'
    //     )
    //     ->leftJoin('historial_indicadores', function ($join) use ($gestion) { // Pasar $gestion aquí
    //         $join->on('indicadores.IND_id', '=', 'historial_indicadores.FK_IND_id')
    //             ->where('historial_indicadores.HIN_gestion', $gestion);
    //     })
    //     ->where('indicadores.IND_estado', '=', '1')
    //     ->orderBy('indicadores.IND_orden', 'asc')
    //     ->orderBy('indicadores.IND_id', 'asc')
    //     ->get()
    //     ->toArray();
    //     $categorias = CustomController::organizarIndicadores( $categorias );
    //     // dump($indicadores);exit;
    //     $breadcrumbs = [
    //         ['name' => 'Inicio', 'url' => route('panel')],
    //         ['name' => 'Actualización de datos', 'url' => ''],
    //     ];
        
    //     // $centrosPenitenciarios = ModEstablecimiento::select('EST_nombre','EST_departamento')->where('FK_TES_id', 1)->get()->toArray();
    //     $centrosPenitenciarios = ModEstablecimiento::select('EST_id', 'EST_nombre', 'EST_departamento')
    //     ->where('FK_TES_id', 1)
    //     ->orderBy('EST_departamento') // Ordenar por departamento
    //     ->get()
    //     ->groupBy('EST_departamento'); // Agrupar por departamento

        
    //     $quries = DB::getQueryLog();
    //     //dump ($quries);
    //     return view('indicadores.actualizar', compact('categorias','breadcrumbs','gestion','centrosPenitenciarios'));
    // }

    public function actualizar(Request $request) {
        // Prevenir timeouts en consultas largas
        set_time_limit(120);
        
        $gestion = $request->query('gestion', date('Y'));
        
        // Usar cache similar para el panel
        $cacheKey = "indicadores_actualizar_{$gestion}";
        $categorias = Cache::remember($cacheKey, 300, function() use ($gestion) {
            return ModIndicador::select(
                'indicadores.*',
                'historial_indicadores.HIN_respuesta',
                'historial_indicadores.HIN_gestion',
                'historial_indicadores.HIN_informacion_complementaria'
            )
            ->leftJoin('historial_indicadores', function ($join) use ($gestion) {
                $join->on('indicadores.IND_id', '=', 'historial_indicadores.FK_IND_id')
                    ->where('historial_indicadores.HIN_gestion', $gestion);
            })
            ->where('indicadores.IND_estado', '=', '1')
            ->orderBy('indicadores.IND_orden', 'asc')
            ->orderBy('indicadores.IND_id', 'asc')
            ->get()
            ->toArray();
        });
        
        $categorias = CustomController::organizarIndicadores($categorias);
        
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Actualización de datos', 'url' => ''],
        ];
        
        // Centros penitenciarios (mantener como está)
        $centrosPenitenciarios = Cache::remember('centros_penitenciarios', 3600, function() {
            return ModEstablecimiento::select('EST_id', 'EST_nombre', 'EST_departamento')
                ->where('FK_TES_id', 1)
                ->orderBy('EST_departamento')
                ->get()
                ->groupBy('EST_departamento');
        });
        
        // Cargar listas desde archivos JSON**
        
        // 1. Cargar lista de delitos dinámicos
        $delitos = $this->cargarDelitos($gestion);
        
        // 2. Departamentos estáticos
        $departamentos = [
            'la_paz' => 'La Paz',
            'santa_cruz' => 'Santa Cruz',
            'cochabamba' => 'Cochabamba',
            'oruro' => 'Oruro',
            'potosi' => 'Potosí',
            'chuquisaca' => 'Chuquisaca',
            'tarija' => 'Tarija',
            'beni' => 'Beni',
            'pando' => 'Pando'
        ];
        
        // 3. Sexo estático
        $sexo = [
            'femenino' => 'Femenino',
            'masculino' => 'Masculino'
        ];
        
        return view('indicadores.actualizar', compact(
            'categorias', 
            'breadcrumbs', 
            'gestion', 
            'centrosPenitenciarios',
            'delitos',
            'departamentos', 
            'sexo'
        ));
    }

    /**
     * Cargar delitos desde archivo JSON
     */
    private function cargarDelitos($gestion)
    {
        try {
            $filePath = storage_path("app/config/listas/delitos_{$gestion}.json");
            
            // Si no existe el archivo para la gestión actual, usar el año anterior
            if (!file_exists($filePath)) {
                $fallbackYear = $gestion - 1;
                $fallbackPath = storage_path("app/config/listas/delitos/delitos_{$fallbackYear}.json");
                
                if (file_exists($fallbackPath)) {
                    $filePath = $fallbackPath;
                    //\Log::info("Usando delitos de {$fallbackYear} para {$gestion}");
                } else {
                    // Crear archivo por defecto
                    $this->crearArchivoDelitosDefault($gestion);
                    $filePath = storage_path("app/config/listas/delitos/delitos_{$gestion}.json");
                }
            }
            
            $content = file_get_contents($filePath);
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                //\Log::error("Error JSON delitos {$gestion}: " . json_last_error_msg());
                return $this->getDelitosDefault();
            }
            
            return $data;
            
        } catch (\Exception $e) {
            //\Log::error("Error cargando delitos para {$gestion}: " . $e->getMessage());
            return $this->getDelitosDefault();
        }
    }







    
/**
 * Crear archivo de delitos por defecto
 * @param int $gestion Año para el cual crear el archivo
 */
private function crearArchivoDelitosDefault($gestion)
{
    $defaultDelitos = $this->getDelitosDefault();
    $dirPath = storage_path('app/config/listas');
    
    // Crear directorio si no existe
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0755, true);
    }
    
    $filePath = "{$dirPath}/delitos_{$gestion}.json";
    $jsonContent = json_encode($defaultDelitos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    file_put_contents($filePath, $jsonContent);
    Log::info("Archivo de delitos creado para {$gestion}");
}

/**
 * Obtener delitos por defecto
 * @return array Array con los delitos estándar
 */
private function getDelitosDefault()
{
    return [
        'violencia_familiar' => 'Violencia familiar o doméstica',
        'robo_sin_violencia' => 'Robo sin violencia',
        'estafa_fraude' => 'Estafa o fraude',
        'ciberdelitos' => 'Ciberdelitos (fraude informático, amenazas, grooming)',
        'robo_con_violencia' => 'Robo con violencia',
        'hurto' => 'Hurto',
        'robo_autopartes' => 'Robo de autopartes'
    ];
}
    
    
    
    public function panel(Request $request) {
        $gestion = $request->query('gestion', date('Y'));
        
        // Usar cache similar para el panel
        $cacheKey = "indicadores_panel_{$gestion}";
        $categorias = Cache::remember($cacheKey, 300, function() use ($gestion) {
            return ModIndicador::select(
                'indicadores.*',
                'historial_indicadores.HIN_respuesta',
                'historial_indicadores.HIN_gestion',
                'historial_indicadores.HIN_informacion_complementaria'
            )
            ->leftJoin('historial_indicadores', function ($join) use ($gestion) {
                $join->on('indicadores.IND_id', 'historial_indicadores.FK_IND_id')
                    ->where('historial_indicadores.HIN_gestion', $gestion);
            })
            ->where('indicadores.IND_estado', '1')
            ->orderBy('indicadores.IND_orden', 'asc')
            ->orderBy('indicadores.IND_id', 'asc')
            ->get()
            ->toArray();
        });
        
        $categorias = CustomController::organizarIndicadores($categorias);
        
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Panel de datos', 'url' => ''],
        ];
        
        return view('indicadores.panel', compact('categorias', 'breadcrumbs', 'gestion'));
    }
        
    // Guarda los datos que se actualizan en los indicadores
    // public function guardar(Request $request) {
    //     // dump($request->all());exit;
    //     try {
    //         $validatedData = $request->validate([
    //             'respuesta' => 'required|string',
    //             'informacion_complementaria' => 'nullable|string',
    //             'FK_IND_id' => 'required|integer',
    //             'anio_consulta' => 'required',
    //         ]);
            
    //         $respuesta = $validatedData['respuesta'];
    //         // $informacionComplementaria = $request->informacion_complementaria;
    //         $informacionComplementaria = $validatedData['informacion_complementaria'];
    //         $indicadorId = $validatedData['FK_IND_id'];
    //         $gestion = $validatedData['anio_consulta'];
            
    //         //dump($respuesta, $indicadorId, $gestion); exit;
            
    //         // Buscar si ya existe un registro con la misma respuesta y el mismo indicador
    //         // $existingRecord = DB::table('historial_indicadores')
    //         //     ->where('FK_IND_id', $indicadorId)
    //         //     ->first();
            
    //         $existingRecord = DB::table('historial_indicadores')
    //             ->where('FK_IND_id', $indicadorId)
    //             ->where('HIN_gestion', $gestion)
    //             ->first();
            
    //         if ($existingRecord) {
    //             // Verificar si la gestión es la misma
    //             if ($existingRecord->HIN_gestion == $gestion) {
    //                 // Actualizar el registro existente
    //                 DB::table('historial_indicadores')
    //                     ->where('HIN_id', $existingRecord->HIN_id)
    //                     ->update([
    //                         'HIN_respuesta' => $respuesta,
    //                         'HIN_informacion_complementaria' => $informacionComplementaria,
    //                         'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
    //                         'HIN_gestion' => $gestion,
    //                     ]);
    //             } else {
    //                 // Insertar un nuevo registro con una gestión diferente
    //                 DB::table('historial_indicadores')->insert([
    //                     'HIN_respuesta' => $respuesta,
    //                     'HIN_informacion_complementaria' => $informacionComplementaria,
    //                     'FK_IND_id' => $indicadorId,
    //                     'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
    //                     'HIN_gestion' => $gestion,
    //                 ]);
    //             }
    //         } else {
    //             // Insertar un nuevo registro ya que no existe uno con la misma respuesta e indicador
    //             DB::table('historial_indicadores')->insert([
    //                 'HIN_respuesta' => $respuesta,
    //                 'HIN_informacion_complementaria' => $informacionComplementaria,
    //                 'FK_IND_id' => $indicadorId,
    //                 'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
    //                 'HIN_gestion' => $gestion,
    //             ]);
    //         }
            
    //         return response()->json(['success' => true, 'message' => 'Datos guardados correctamente.'], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error encontrado' => $e->getMessage()], 500);
    //     }
    // }
    

    
    

    public function guardar(Request $request) {
        try {
            // Validación básica
            $validatedData = $request->validate([
                'respuesta' => 'required',
                'informacion_complementaria' => 'nullable|string|max:500',
                'FK_IND_id' => 'required|integer|exists:indicadores,IND_id',
                'anio_consulta' => 'required|integer|min:2020|max:2030',
            ], [
                'respuesta.required' => 'La respuesta es obligatoria',
                'FK_IND_id.exists' => 'El indicador especificado no existe',
                'anio_consulta.min' => 'El año debe ser mayor a 2020',
                'anio_consulta.max' => 'El año no puede ser mayor a 2030'
            ]);
            
            $respuesta = $validatedData['respuesta'];
            $informacionComplementaria = $validatedData['informacion_complementaria'];
            $indicadorId = $validatedData['FK_IND_id'];
            $gestion = $validatedData['anio_consulta'];
            
            // Obtener indicador
            $indicador = ModIndicador::where('IND_id', $indicadorId)
                                    ->where('IND_estado', '1')
                                    ->first();
                                    
            if (!$indicador) {
                return response()->json([
                    'error' => 'El indicador no existe o está inactivo'
                ], 404);
            }
            
            // Validaciones específicas por tipo
            switch ($indicador->IND_tipo_repuesta) {
                case 'Lista delitos':
                    $validationResult = $this->validateListaDelitos($respuesta);
                    if (!$validationResult['valid']) {
                        return response()->json(['error' => $validationResult['message']], 400);
                    }
                    break;
                    
                case 'Lista departamentos':
                    $validationResult = $this->validateListaDepartamentos($respuesta);
                    if (!$validationResult['valid']) {
                        return response()->json(['error' => $validationResult['message']], 400);
                    }
                    break;
                    
                case 'Lista sexo':
                    $validationResult = $this->validateListaSexo($respuesta);
                    if (!$validationResult['valid']) {
                        return response()->json(['error' => $validationResult['message']], 400);
                    }
                    break;
                    
                case 'Lista centros penitenciarios':
                    $validationResult = $this->validateListaCentros($respuesta);
                    if (!$validationResult['valid']) {
                        return response()->json(['error' => $validationResult['message']], 400);
                    }
                    break;
            }
            
            DB::beginTransaction();
            
            try {
                $existingRecord = DB::table('historial_indicadores')
                    ->where('FK_IND_id', $indicadorId)
                    ->where('HIN_gestion', $gestion)
                    ->first();
                
                if ($existingRecord) {
                    DB::table('historial_indicadores')
                        ->where('HIN_id', $existingRecord->HIN_id)
                        ->update([
                            'HIN_respuesta' => $respuesta,
                            'HIN_informacion_complementaria' => $informacionComplementaria,
                            'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
                            'HIN_gestion' => $gestion,
                        ]);
                        
                    $mensaje = 'Datos actualizados correctamente para ' . $indicador->IND_tipo_repuesta;
                } else {
                    DB::table('historial_indicadores')->insert([
                        'HIN_respuesta' => $respuesta,
                        'HIN_informacion_complementaria' => $informacionComplementaria,
                        'FK_IND_id' => $indicadorId,
                        'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
                        'HIN_gestion' => $gestion,
                    ]);
                    
                    $mensaje = 'Datos guardados correctamente para ' . $indicador->IND_tipo_repuesta;
                }
                
                DB::commit();
                
                // Limpiar cache
                Cache::forget("indicadores_actualizar_{$gestion}");
                Cache::forget("indicadores_panel_{$gestion}");
                
                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'tipo' => $indicador->IND_tipo_repuesta
                ], 200);
                
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación de datos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // \Log::error('Error en IndicadorController::guardar', [
            //     'message' => $e->getMessage(),
            //     'line' => $e->getLine(),
            //     'file' => $e->getFile(),
            //     'request_data' => $request->all()
            // ]);
            
            return response()->json([
                'error' => 'Error interno del servidor. Detalles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar datos de tipo lista de delitos
     * @param string $respuesta JSON con los datos de delitos
     * @return array Array con resultado de validación
     */
    private function validateListaDelitos($respuesta) {
        $decodedResponse = json_decode($respuesta, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['valid' => false, 'message' => 'Formato de datos JSON inválido para delitos'];
        }
        
        $validDelitos = [
            'violencia_familiar', 
            'robo_sin_violencia', 
            'estafa_fraude', 
            'ciberdelitos', 
            'robo_con_violencia', 
            'hurto', 
            'robo_autopartes'
        ];
        
        // Verificar que todos los delitos requeridos estén presentes
        foreach ($validDelitos as $delito) {
            if (!array_key_exists($delito, $decodedResponse)) {
                return ['valid' => false, 'message' => 'Falta el delito: ' . $delito];
            }
            
            $value = $decodedResponse[$delito];
            if (!is_numeric($value) || $value < 0) {
                return ['valid' => false, 'message' => 'El valor para ' . $delito . ' debe ser un número positivo o cero'];
            }
        }
        
        return ['valid' => true];
    }
    


    /**
     * Validar datos de tipo lista de departamentos
     * @param string $respuesta JSON con los datos de departamentos
     * @return array Array con resultado de validación
     */
    private function validateListaDepartamentos($respuesta) {
        $decodedResponse = json_decode($respuesta, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['valid' => false, 'message' => 'Formato de datos JSON inválido para departamentos'];
        }
        
        $validDepartamentos = ['la_paz', 'santa_cruz', 'cochabamba', 'oruro', 'potosi', 'chuquisaca', 'tarija', 'beni', 'pando'];
        
        foreach ($decodedResponse as $key => $value) {
            if (!in_array($key, $validDepartamentos)) {
                return ['valid' => false, 'message' => 'Departamento inválido: ' . $key];
            }
            if (!is_numeric($value) || $value < 0) {
                return ['valid' => false, 'message' => 'Los valores por departamentos deben ser números positivos o cero'];
            }
        }
        
        return ['valid' => true];
    }


    /**
     * Validar datos de tipo lista de sexo
     * @param string $respuesta JSON con los datos de sexo
     * @return array Array con resultado de validación
     */
    private function validateListaSexo($respuesta) {
        $decodedResponse = json_decode($respuesta, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['valid' => false, 'message' => 'Formato de datos JSON inválido para sexo'];
        }
        
        $validSexos = ['femenino', 'masculino'];
        
        foreach ($decodedResponse as $key => $value) {
            if (!in_array($key, $validSexos)) {
                return ['valid' => false, 'message' => 'Tipo de sexo inválido: ' . $key];
            }
            if (!is_numeric($value) || $value < 0) {
                return ['valid' => false, 'message' => 'Los valores por sexo deben ser números positivos o cero'];
            }
        }
        
        return ['valid' => true];
    }


    /**
     * Validar datos de tipo lista de centros penitenciarios
     * @param string $respuesta JSON con los datos de centros
     * @return array Array con resultado de validación
     */
    private function validateListaCentros($respuesta) {
        $decodedResponse = json_decode($respuesta, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['valid' => false, 'message' => 'Formato de datos JSON inválido para centros penitenciarios'];
        }
        
        foreach ($decodedResponse as $key => $value) {
            if (!is_numeric($value) || $value < 0) {
                return ['valid' => false, 'message' => 'Los valores por centro deben ser números positivos o cero'];
            }
        }
        
        return ['valid' => true];
    }
            
    


    ///////////////////////////////////////////////////////////////////////////////
     /**
     * Función principal de reportes que maneja las peticiones AJAX
     * Gestiona la carga de categorías, indicadores, parámetros y datos para gráficos
     */
    public function reportes(Request $request) {
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Reportes', 'url' => ''],
        ];
        $gestiones = [2024, 2025, 2026, 2027, 2028];
        $promediosPorAnio = [];

        // (CATEGORIAS) Si la petición es para obtener los indicadores de una categoría
        if ($request->has('categoria_id')) {
            $indicadores = ModIndicador::select('*')
                ->from(DB::raw('(
                    SELECT DISTINCT ON ("IND_indicador") *
                    FROM indicadores
                    WHERE "IND_categoria" = ?
                    ORDER BY "IND_indicador"
                ) as t'))
                ->orderBy('IND_orden', 'asc')
                ->setBindings([$request->categoria_id])
                ->get();

            return response()->json($indicadores);
        }
    
        // (INDICADORES) Si la petición es para obtener los parámetros de un indicador
        if ($request->has('indicador_indicador')) {
            $parametros = ModIndicador::select('IND_parametro', 'IND_id', 'IND_tipo_repuesta')
                ->where('IND_indicador', $request->indicador_indicador)
                ->orderBy('IND_orden', 'asc')
                ->get();
            
            $indicadorPorAnio = $this->indicadorAnualSiNo($request->indicador_indicador, $gestiones);
            
            return response()->json([
                'parametros' => $parametros, 
                'indicadorPorAnio' => $indicadorPorAnio
            ]);
        }

        // (PARAMETROS) Si la petición es para obtener datos específicos de un parámetro
        if ($request->has('parametro_id')) {
            return $this->procesarParametro($request->input('parametro_id'), $gestiones);
        }

        // Si no hay peticiones específicas, cargar la vista principal
        $categorias = ModIndicador::select('IND_categoria')
            ->groupBy('IND_categoria')
            ->orderByRaw('MIN("IND_orden") ASC')
            ->pluck('IND_categoria');
            
        return view('indicadores.reportes', compact('categorias', 'promediosPorAnio', 'breadcrumbs'));
    }

    /**
     * Procesa un parámetro específico y determina qué tipo de gráfico generar
     * basado en el tipo de respuesta del indicador
     */
    private function procesarParametro($parametroId, $gestiones) {
        // Obtener información del parámetro
        $parametroInfo = ModIndicador::select('IND_tipo_repuesta', 'IND_parametro', 'IND_opciones')
            ->where('IND_id', $parametroId)
            ->first();

        if (!$parametroInfo) {
            return response()->json(['error' => 'Parámetro no encontrado'], 404);
        }

        $tipoRespuesta = $parametroInfo->IND_tipo_repuesta;
        
        switch ($tipoRespuesta) {
            case 'Lista desplegable':
                // Verificar si es tipo Si/No
                if ($parametroInfo->IND_opciones === '{"0":"No","1":"Si"}') {
                    $parametroPorAnio = $this->parametroAnualSiNo($parametroId, $gestiones);
                    if ($parametroPorAnio->count() > 0) {
                        return response()->json(['parametroPorAnioSiNo' => $parametroPorAnio]);
                    }
                }
                break;

            case 'Lista centros penitenciarios':
                $parametroPorAnioListaCentrosP = $this->parametroAnualListaCentrosP($parametroId, $gestiones);
                return response()->json(['listaCentrosPorAnio' => $parametroPorAnioListaCentrosP]);

            case 'Numeral':
                $parametroPorAnioNumeral = $this->parametroAnualNumeral($parametroId, $gestiones);
                return response()->json(['numeralPorAnio' => $parametroPorAnioNumeral]);

            case 'Lista delitos':
                $parametroPorAnioDelitos = $this->parametroAnualListaDelitos($parametroId, $gestiones);
                return response()->json(['delitosPorAnio' => $parametroPorAnioDelitos]);

            case 'Lista departamentos':
                $parametroPorAnioDepartamentos = $this->parametroAnualListaDepartamentos($parametroId, $gestiones);
                return response()->json(['departamentosPorAnio' => $parametroPorAnioDepartamentos]);

            case 'Lista sexo':
                $parametroPorAnioSexo = $this->parametroAnualListaSexo($parametroId, $gestiones);
                return response()->json(['sexoPorAnio' => $parametroPorAnioSexo]);

            default:
                return response()->json(['message' => 'Tipo de respuesta no soportado: ' . $tipoRespuesta]);
        }

        return response()->json(['message' => 'No se encontraron datos para este parámetro']);
    }

    /**
     * Obtiene datos numéricos de un parámetro a través de los años
     * Maneja valores 0 vs valores sin datos
     */
    private function parametroAnualNumeral($parametroId, $gestiones) {
        $results = DB::table('historial_indicadores as h')
            ->select('h.HIN_gestion', 'h.HIN_respuesta', 'h.FK_IND_id', 'i.IND_parametro')
            ->leftJoin('indicadores as i', 'h.FK_IND_id', '=', 'i.IND_id')
            ->where('i.IND_tipo_repuesta', 'Numeral')
            ->where('i.IND_id', $parametroId)
            ->whereIn('h.HIN_gestion', $gestiones)
            ->get()
            ->keyBy('HIN_gestion');

        // Generar datos para todos los años, diferenciando entre 0 y sin dato
        $numeralPorAnio = collect($gestiones)->map(function ($year) use ($results, $parametroId) {
            $response = $results->get($year);
            
            return [
                'year' => $year,
                'value' => $response ? (is_numeric($response->HIN_respuesta) ? floatval($response->HIN_respuesta) : null) : null,
                'hasData' => $response !== null,
                'parametro' => $response->IND_parametro ?? 'Sin información'
            ];
        });

        return $numeralPorAnio;
    }

    /**
     * Obtiene datos de delitos por año
     * Los datos se almacenan como JSON en HIN_respuesta
     */
    private function parametroAnualListaDelitos($parametroId, $gestiones) {
        $results = ModIndicador::select([
            'indicadores.IND_parametro',
            'y.hin_gestion as HIN_gestion',
            'historial_indicadores.HIN_respuesta',
            'historial_indicadores.HIN_informacion_complementaria'
        ])
        ->crossJoin(DB::raw('(SELECT unnest(ARRAY[' . implode(',', $gestiones) . ']) as hin_gestion) as y'))
        ->leftJoin('historial_indicadores', function($join) {
            $join->on('indicadores.IND_id', '=', 'historial_indicadores.FK_IND_id')
                 ->on('y.hin_gestion', '=', 'historial_indicadores.HIN_gestion');
        })
        ->where('indicadores.IND_id', $parametroId)
        ->where('indicadores.IND_tipo_repuesta', 'Lista delitos')
        ->orderBy('y.hin_gestion')
        ->get();

        return $results;
    }

    /**
     * Obtiene datos de departamentos por año
     * Los datos se almacenan como JSON en HIN_respuesta
     */
    private function parametroAnualListaDepartamentos($parametroId, $gestiones) {
        $results = ModIndicador::select([
            'indicadores.IND_parametro',
            'y.hin_gestion as HIN_gestion',
            'historial_indicadores.HIN_respuesta',
            'historial_indicadores.HIN_informacion_complementaria'
        ])
        ->crossJoin(DB::raw('(SELECT unnest(ARRAY[' . implode(',', $gestiones) . ']) as hin_gestion) as y'))
        ->leftJoin('historial_indicadores', function($join) {
            $join->on('indicadores.IND_id', '=', 'historial_indicadores.FK_IND_id')
                 ->on('y.hin_gestion', '=', 'historial_indicadores.HIN_gestion');
        })
        ->where('indicadores.IND_id', $parametroId)
        ->where('indicadores.IND_tipo_repuesta', 'Lista departamentos')
        ->orderBy('y.hin_gestion')
        ->get();

        return $results;
    }

    /**
     * Obtiene datos de sexo por año
     * Los datos se almacenan como JSON en HIN_respuesta
     */
    private function parametroAnualListaSexo($parametroId, $gestiones) {
        $results = ModIndicador::select([
            'indicadores.IND_parametro',
            'y.hin_gestion as HIN_gestion',
            'historial_indicadores.HIN_respuesta',
            'historial_indicadores.HIN_informacion_complementaria'
        ])
        ->crossJoin(DB::raw('(SELECT unnest(ARRAY[' . implode(',', $gestiones) . ']) as hin_gestion) as y'))
        ->leftJoin('historial_indicadores', function($join) {
            $join->on('indicadores.IND_id', '=', 'historial_indicadores.FK_IND_id')
                 ->on('y.hin_gestion', '=', 'historial_indicadores.HIN_gestion');
        })
        ->where('indicadores.IND_id', $parametroId)
        ->where('indicadores.IND_tipo_repuesta', 'Lista sexo')
        ->orderBy('y.hin_gestion')
        ->get();

        return $results;
    }
    
    ///////////////////////////////////////////////////////////////////////////////
    
 
    private function indicadorAnualSiNo($indicador, $gestiones) {
        $resultados = DB::table(DB::raw('(SELECT unnest(ARRAY[' . implode(',', $gestiones) . ']) AS year) AS years'))
            ->crossJoin('indicadores')
            ->leftJoin('historial_indicadores', function ($join) {
                $join->on('historial_indicadores.FK_IND_id', 'indicadores.IND_id')
                    ->on('historial_indicadores.HIN_gestion', 'years.year');
            })
            ->select(
                DB::raw('years.year AS "HIN_gestion" '), 
                'indicadores.IND_id',
                'indicadores.IND_indicador',
                'indicadores.IND_parametro',
                DB::raw("COALESCE(historial_indicadores.\"HIN_respuesta\", 'Sin respuesta') AS \"HIN_respuesta\"")
            )
            ->where('indicadores.IND_indicador', $indicador)
            ->where('indicadores.IND_opciones', '{"0":"No","1":"Si"}')
            ->orderBy('indicadores.IND_id')
            ->orderBy('years.year')
            ->get();
        
        return CustomController::calcularPromedioIndicadores($resultados);
    }
    
    private function parametroAnualSiNo($indicadorId,$gestiones){
        // $gestiones = [2024, 2025, 2026, 2027, 2028];
        
        // Obtener respuestas de historial_indicadores junto con el parámetro del indicador
        DB::enableQueryLog(); // Habilita el registro de consultas para depuración
        $results = DB::table('historial_indicadores as h')
            ->select('h.HIN_gestion', 'h.HIN_respuesta', 'h.FK_IND_id', 'i.IND_parametro')
            ->leftJoin('indicadores as i', 'h.FK_IND_id', '=', 'i.IND_id')
            ->where('i.IND_opciones', '{"0":"No","1":"Si"}')
            ->where('i.IND_id', $indicadorId)
            ->whereIn('h.HIN_gestion', $gestiones)
            ->get()
            ->keyBy('HIN_gestion'); // Asignamos los resultados usando el año como clave
        
        $quries = DB::getQueryLog();
        // dump ($quries);exit;
        if ($results->count() == 0) {
            // No hay resultados retornar null
            return $results;
        } else {  
            // Si  Hay resultados, Generar una colección con valores predeterminados para cada gestión
            $parametroPorAnio = collect($gestiones)->mapWithKeys(function ($year) use ($results, $indicadorId) {
                $response = $results->get($year); // Buscar el resultado del año actual
                
                return [
                    $year => (object) [
                        'HIN_gestion'  => $year,
                        'HIN_respuesta' => optional($response)->HIN_respuesta, // Puede ser "Si", "No" o null
                        'FK_IND_id'    => optional($response)->FK_IND_id ?? $indicadorId,
                        'IND_parametro' => optional($response)->IND_parametro ?? 'Sin información'
                    ]
                ];
            });
            return $parametroPorAnio;
        }
        
        
        // dump($parametroPorAnio); // Para depuración, puedes removerlo si ya no es necesario
        // exit;
    }
    
    private function parametroAnualListaCentrosP($indicadorId, $gestiones) {
        $results = ModIndicador::select([
            'indicadores.IND_numero',
            'indicadores.IND_indicador',
            'indicadores.IND_parametro',
            'indicadores.IND_categoria',
            'y.hin_gestion as HIN_gestion', // Cambiado a minúsculas en el cross join
            'historial_indicadores.HIN_respuesta',
            'historial_indicadores.HIN_informacion_complementaria'
        ])
        ->crossJoin(DB::raw('(SELECT unnest(ARRAY[' . implode(',', $gestiones) . ']) as hin_gestion) as y'))
        ->leftJoin('historial_indicadores', function($join) {
            $join->on('indicadores.IND_id', '=', 'historial_indicadores.FK_IND_id')
                 ->on('y.hin_gestion', '=', 'historial_indicadores.HIN_gestion');
        })
        ->where('indicadores.IND_id', $indicadorId)
        //->where('indicadores.IND_tipo_repuesta', 'Lista centros penitenciarios')
        ->orderBy('y.hin_gestion')
        ->get();
        return $results;
    }

    //////////////////////////////////////////////////////////


    






    
    
}
