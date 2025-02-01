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
    public function tablero(Request $request)
    {
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Tablero de Indicadores', 'url' => ''],
        ];

        $promediosPorAnyo = [];
        // Si la petición es para obtener los indicadores de una categoría
            if ($request->has('categoria_id')) {
                
                DB::enableQueryLog();
                
                $indicadores=DB::table('indicadores')
                ->select('*')
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
        
        // Si la petición es para obtener los parámetros de un indicador
            if ($request->has('indicador_indicador')) {
                DB::enableQueryLog();
                $parametros = DB::table('indicadores')
                ->select('IND_parametro', 'IND_id')
                ->where('IND_indicador', $request->indicador_indicador)
                ->orderBy('IND_orden', 'asc')
                ->get();
                
                $quries = DB::getQueryLog();
                //dump ($quries);exit;
                
                // CALCULA LOS RESULTADOS DEL INDICADOR SELECCIONADO
                $gestiones = [2024, 2025, 2026, 2027, 2028];

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
                    ->where('indicadores.IND_indicador', $request->indicador_indicador)
                    ->where('indicadores.IND_opciones', '{"0":"No","1":"Si"}')
                    ->orderBy('indicadores.IND_id')
                    ->orderBy('years.year')
                    ->get();
                    // dump($resultados);exit; 
                    
                    $promediosPorAnyo = CustomController::calcularPromedioIndicadores( $resultados );
                
                return response()->json(['parametros' => $parametros, 'promediosPorAnyo' => $promediosPorAnyo]);
            }
        
        // Si no hay peticiones específicas, cargar la vista principal
            $categorias = ModIndicador::select('IND_categoria')
                ->groupBy('IND_categoria')
                ->orderByRaw('MIN("IND_orden") ASC')
                ->pluck('IND_categoria');
            return view('indicadores.tablero', compact('categorias', 'promediosPorAnyo', 'breadcrumbs'));
    }
    public function obtenerResultados(Request $request)
        {
            $parametroId = $request->input('parametro_id');
            
            // Años a consultar
            $gestiones = [2024, 2025, 2026, 2027, 2028];
            
            $resultados = DB::table('historial_indicadores')
                ->rightJoin(DB::raw('(SELECT unnest(ARRAY[' . implode(',', $gestiones) . ']) AS gestion) g'), function($join) use ($parametroId) {
                    $join->on('historial_indicadores.HIN_gestion', '=', 'g.gestion')
                        ->where('historial_indicadores.FK_IND_id', '=', $parametroId);
                })
                ->select(
                    'g.gestion as HIN_gestion', 
                    'historial_indicadores.HIN_respuesta',
                    'historial_indicadores.HIN_fuente_verificacion',
                    'historial_indicadores.HIN_informacion_complementaria'
                )
                ->orderBy('g.gestion')
                ->get();
                // dump($resultados);exit;
            
            return response()->json($resultados);
        }
    // Consulta para obtener los resultados de los indicadores SI/NO
    /*public function resultadoSiNo()
    {
        $years = [2024, 2025, 2026, 2027, 2028];
        
        $indicadores = ModIndicador::where('IND_opciones', '{"0":"No","1":"Si"}')
            ->with(['historial' => function ($query) use ($years) {
                $query->whereIn('HIN_gestion', $years);
            }])
            ->get()
            ->map(function ($indicador) use ($years) {
                // Para cada año, verificar si existe una respuesta en el historial
                $resultadosPorAnio = [];
                foreach ($years as $year) {
                    $respuesta = $indicador->historial->firstWhere('HIN_gestion', $year);
                    $resultadosPorAnio[$year] = $respuesta ? $respuesta->HIN_respuesta : null;
                }
                
                // Agregar los resultados al indicador
                $indicador->resultados_por_anio = $resultadosPorAnio;
                return $indicador; // Devolver el indicador modificado
            });

        // Verificar si hay indicadores
        if ($indicadores->isEmpty()) {
            return view('indicadores.resultadosino', ['mensaje' => 'No se encontraron indicadores.']);
        }
        
        return view('indicadores.resultadosino', compact('indicadores'));
    }*/
    
    
    
}
