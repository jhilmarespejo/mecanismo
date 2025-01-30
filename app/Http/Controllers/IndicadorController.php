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

        $quries = DB::getQueryLog();
        // dump ($quries);exit;
        
        
        $categorias = CustomController::organizarIndicadores( $categorias, );
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
            
            // dump($respuesta, $indicadorId, $gestion); exit;
            
            // Buscar si ya existe un registro con la misma respuesta y el mismo indicador
            $existingRecord = DB::table('historial_indicadores')
                ->where('FK_IND_id', $indicadorId)
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
    
    // Consulta para obtener los resultados de los indicadores SI/NO
    public function obtenerResultadoSiNo() 
    {
        // Primero obtenemos los años que queremos analizar
        $years = [2024, 2025, 2026, 2027, 2028];
        
        // Obtenemos los indicadores que solo tienen opciones Si/No
        $indicadores = ModIndicador::where('IND_opciones', '{"0":"No","1":"Si"}')
            ->where('IND_estado', '1')
            ->where('IND_id', '<=', '185')    
            ->select(
                'IND_id',
                'IND_numero',
                'IND_indicador',
                'IND_categoria'
            )
            ->orderBy('IND_orden')
            ->get();

        $resultados = [];

        foreach ($years as $year) {
            foreach ($indicadores as $indicador) {
                // Calculamos el porcentaje de respuestas "Si" para cada indicador por año
                $respuestas = ModHistorialIndicador::where('FK_IND_id', $indicador->IND_id)
                    ->where('HIN_gestion', $year)
                    ->select(
                        DB::raw('COUNT(CASE WHEN "HIN_respuesta" = \'Si\' THEN 1 END) as respuestas_si'),
                        DB::raw('COUNT("HIN_respuesta") as total_respuestas')
                    )
                    ->first();

                $porcentaje = 0;
                if ($respuestas->total_respuestas > 0) {
                    $porcentaje = ($respuestas->respuestas_si / $respuestas->total_respuestas) * 100;
                }

                $resultados[] = [
                    'year' => $year,
                    'indicador_id' => $indicador->IND_id,
                    'indicador_numero' => $indicador->IND_numero,
                    'indicador_nombre' => $indicador->IND_indicador,
                    'categoria' => $indicador->IND_categoria,
                    'porcentaje' => round($porcentaje, 2)
                ];
            }
        }

        //dd($resultados);    // Para depurar y ver los resultados
        return view('indicadores.resultadosino', compact('resultados'));
    }
    
    
    
}
