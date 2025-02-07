<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{ModEstablecimiento, ModIndicador, ModHistorialIndicador};
use App\Http\Controllers\CustomController;
use Carbon\Carbon;

class IndicadorController extends Controller
{
    public function actualizar( Request $request ) {
        DB::enableQueryLog();
        //$gestion = date('Y');
        /*$categorias = ModIndicador::select(
            'indicadores.*',
            'historial_indicadores.HIN_respuesta',
            'historial_indicadores.HIN_gestion',
            'historial_indicadores.HIN_informacion_complementaria'
            
            )
            ->leftJoin('historial_indicadores', 'indicadores.IND_id', 'historial_indicadores.FK_IND_id')
            ->where('indicadores.IND_estado', '1')
            ->where('historial_indicadores.HIN_gestion', $gestion)
            ->orderBy('IND_id')->get()->toArray();*/
        
        $gestion = $request->query('gestion', date('Y'));
        $categorias = ModIndicador::select(
            'indicadores.*',
            'historial_indicadores.HIN_respuesta',
            'historial_indicadores.HIN_gestion',
            'historial_indicadores.HIN_informacion_complementaria'
        )
        ->leftJoin('historial_indicadores', function ($join) use ($gestion) { // Pasar $gestion aquí
            $join->on('indicadores.IND_id', '=', 'historial_indicadores.FK_IND_id')
                ->where('historial_indicadores.HIN_gestion', $gestion);
        })
        ->where('indicadores.IND_estado', '=', '1')
        ->orderBy('indicadores.IND_id', 'asc')
        ->get()
        ->toArray();
        $categorias = CustomController::organizarIndicadores( $categorias );
        // dump($indicadores);exit;
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Actualización de datos', 'url' => ''],
        ];
        
        // $centrosPenitenciarios = ModEstablecimiento::select('EST_nombre','EST_departamento')->where('FK_TES_id', 1)->get()->toArray();
        $centrosPenitenciarios = ModEstablecimiento::select('EST_id', 'EST_nombre', 'EST_departamento')
        ->where('FK_TES_id', 1)
        ->orderBy('EST_departamento') // Ordenar por departamento
        ->get()
        ->groupBy('EST_departamento'); // Agrupar por departamento

        
        $quries = DB::getQueryLog();
        //dump ($quries);
        return view('indicadores.actualizar', compact('categorias','breadcrumbs','gestion','centrosPenitenciarios'));
    }

    public function panel(Request $request){
        $gestion = $request->query('gestion', date('Y'));
        $categorias = ModIndicador::select(
            'indicadores.*',
            'historial_indicadores.HIN_respuesta',
            'historial_indicadores.HIN_gestion',
            'historial_indicadores.HIN_informacion_complementaria'
        )
        ->leftJoin('historial_indicadores', function ($join) use ($gestion) { // Pasar $gestion aquí
            $join->on('indicadores.IND_id', '=', 'historial_indicadores.FK_IND_id')
                ->where('historial_indicadores.HIN_gestion', $gestion);
        })
        ->where('indicadores.IND_estado', '=', '1')
        ->orderBy('indicadores.IND_id', 'asc')
        ->get()
        ->toArray();
        $queries = DB::getQueryLog();
        // dump ($quries);exit;
        
        
        $categorias = CustomController::organizarIndicadores( $categorias );
        // dump($categorias);//    exit;
       
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Panel de datos', 'url' => ''],
        ];
        
        return view('indicadores.panel', compact('categorias', 'breadcrumbs', 'gestion'));
    }
    
    
    // Guarda los datos que se actualizan en los indicadores
    public function guardar(Request $request) {
        // dump($request->all());exit;
        try {
            $validatedData = $request->validate([
                'respuesta' => 'required|string',
                'informacion_complementaria' => 'nullable|string',
                'FK_IND_id' => 'required|integer',
                'anio_consulta' => 'required',
            ]);
            
            $respuesta = $validatedData['respuesta'];
            // $informacionComplementaria = $request->informacion_complementaria;
            $informacionComplementaria = $validatedData['informacion_complementaria'];
            $indicadorId = $validatedData['FK_IND_id'];
            $gestion = $validatedData['anio_consulta'];
            
            //dump($respuesta, $indicadorId, $gestion); exit;
            
            // Buscar si ya existe un registro con la misma respuesta y el mismo indicador
            // $existingRecord = DB::table('historial_indicadores')
            //     ->where('FK_IND_id', $indicadorId)
            //     ->first();
            
            $existingRecord = DB::table('historial_indicadores')
                ->where('FK_IND_id', $indicadorId)
                ->where('HIN_gestion', $gestion)
                ->first();
            
            if ($existingRecord) {
                // Verificar si la gestión es la misma
                if ($existingRecord->HIN_gestion == $gestion) {
                    // Actualizar el registro existente
                    DB::table('historial_indicadores')
                        ->where('HIN_id', $existingRecord->HIN_id)
                        ->update([
                            'HIN_respuesta' => $respuesta,
                            'HIN_informacion_complementaria' => $informacionComplementaria,
                            'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
                            'HIN_gestion' => $gestion,
                        ]);
                } else {
                    // Insertar un nuevo registro con una gestión diferente
                    DB::table('historial_indicadores')->insert([
                        'HIN_respuesta' => $respuesta,
                        'HIN_informacion_complementaria' => $informacionComplementaria,
                        'FK_IND_id' => $indicadorId,
                        'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
                        'HIN_gestion' => $gestion,
                    ]);
                }
            } else {
                // Insertar un nuevo registro ya que no existe uno con la misma respuesta e indicador
                DB::table('historial_indicadores')->insert([
                    'HIN_respuesta' => $respuesta,
                    'HIN_informacion_complementaria' => $informacionComplementaria,
                    'FK_IND_id' => $indicadorId,
                    'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
                    'HIN_gestion' => $gestion,
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'Datos guardados correctamente.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error encontrado' => $e->getMessage()], 500);
        }
    }
    
    // Función tablero que maneja las peticiones
    public function reportes(Request $request) {
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Reportes', 'url' => ''],
        ];
        $gestiones = [2024, 2025, 2026, 2027, 2028];
        $promediosPorAnyo = [];

        // (CATEGORIAS) Si la petición es para obtener los indicadores de una categoría
            if ($request->has('categoria_id')) {
                
                DB::enableQueryLog();
                
                $indicadores=ModIndicador::select('*')
                ->from(DB::raw('(
                    SELECT DISTINCT ON ("IND_indicador") *
                    FROM indicadores
                    WHERE "IND_categoria" = ?
                    ORDER BY "IND_indicador"
                ) as t'))
                ->orderBy('IND_orden', 'asc')
                ->setBindings([$request->categoria_id])
                ->get();

                // $quries = DB::getQueryLog();
                // dump ($quries);
                return response()->json($indicadores);
            }
        
        // (INDICADORES) Si la petición es para obtener los parámetros de un indicador
            if ($request->has('indicador_indicador')) {
                DB::enableQueryLog();
                
                $parametros = ModIndicador::select('IND_parametro', 'IND_id')
                    ->where('IND_indicador', $request->indicador_indicador)
                    ->orderBy('IND_orden', 'asc')
                    ->get();
                
                $indicadorPorAnyo = $this->indicadorAnualSiNo($request->indicador_indicador, $gestiones);
                // if (empty($indicadorPorAnyo)) {
                //     return response()->json(['error' => 'No se encontraron datos para este indicador'], 404);
                // }
                // dump($indicadorPorAnyo);
                
            
                return response()->json(['parametros' => $parametros, 'indicadorPorAnyo' => $indicadorPorAnyo]);
            }

        // (PARAMETROS) Si la petición es para obtener los parámetros de un indicador
            if ($request->has('parametro_id')) {
                $parametroPorAnyo = $this->parametroAnualSiNo($request->input('parametro_id'), $gestiones);

                if ($parametroPorAnyo->count() > 0) {
                    // Hay resultados SI/No
                    return response()->json(['parametroPorAnyo' => $parametroPorAnyo]);
                } else {
                    // Consultar resultados tipo Lista centros penitenciarios
                    $parametroPorAnyoListaCentrosP = $this->parametroAnualListaCentrosP($request->input('parametro_id'), $gestiones);
                    return response()->json(['listaCentrosPorAnyo' => $parametroPorAnyoListaCentrosP]);
                    dump($parametroPorAnyoListaCentrosP->toArray());exit;
                    
                    //return response()->json(['message' => 'No se encontraron resultados.']);
                }
            }
        // Si no hay peticiones específicas, cargar la vista principal
            $categorias = ModIndicador::select('IND_categoria')
                ->groupBy('IND_categoria')
                ->orderByRaw('MIN("IND_orden") ASC')
                ->pluck('IND_categoria');
            return view('indicadores.reportes', compact('categorias', 'promediosPorAnyo', 'breadcrumbs'));
    }
    
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
            $parametroPorAnyo = collect($gestiones)->mapWithKeys(function ($year) use ($results, $indicadorId) {
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
            return $parametroPorAnyo;
        }
        
        
        // dump($parametroPorAnyo); // Para depuración, puedes removerlo si ya no es necesario
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
        ->where('indicadores.IND_tipo_repuesta', 'Lista centros penitenciarios')
        ->orderBy('y.hin_gestion')
        ->get();
        return $results;
    }
    
    
}
