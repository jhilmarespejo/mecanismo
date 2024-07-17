<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{ModIndicador, ModHistorialIndicador};
use App\Http\Controllers\CustomController;
use Carbon\Carbon;

class IndicadorController extends Controller
{
    public function actualizar( Request $request )
    {


        // $indicadores = ModIndicador::orderBy('IND_id', 'asc')->get()->toArray();

        $categorias = ModIndicador::select(
            'indicadores.*',
            'historial_indicadores.HIN_respuesta',
            'historial_indicadores.HIN_gestion'
            )
        ->leftJoin('historial_indicadores', 'indicadores.IND_id', 'historial_indicadores.FK_IND_id')
        ->orderBy('IND_id')->get()->toArray();
        $categorias = CustomController::organizarIndicadores( $categorias );
        // dump($indicadores);exit;
        return view('indicadores.actualizar', compact('categorias'));
    }

    public function guardar(Request $request)
    {
        try {

            $respuesta = $request->respuesta;
            $indicadorId = $request->FK_IND_id;
            $gestion = Carbon::now()->format('Y');

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
                            'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
                            'HIN_gestion' => $gestion,
                        ]);
                } else {
                    // Insertar un nuevo registro con una gestión diferente
                    DB::table('historial_indicadores')->insert([
                        'HIN_respuesta' => $respuesta,
                        'FK_IND_id' => $indicadorId,
                        'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
                        'HIN_gestion' => $gestion,
                    ]);
                }
            } else {
                // Insertar un nuevo registro ya que no existe uno con la misma respuesta e indicador
                DB::table('historial_indicadores')->insert([
                    'HIN_respuesta' => $respuesta,
                    'FK_IND_id' => $indicadorId,
                    'HIN_fecha_respuesta' => Carbon::now()->format('Y-m-d'),
                    'HIN_gestion' => $gestion,
                ]);
            }

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function panel(){
        $categorias = ModIndicador::select(
            'indicadores.*',
            'historial_indicadores.HIN_respuesta',
            'historial_indicadores.HIN_gestion'
            )
        ->leftJoin('historial_indicadores', 'indicadores.IND_id', 'historial_indicadores.FK_IND_id')
        ->orderBy('IND_id')->get()->toArray();

        $categorias = CustomController::organizarIndicadores( $categorias, );
        // dump($categorias);exit;
        //tomar los datos de cada indicador y hacer el calculo correspondiente
        // hacer una funcion para iterar y ver cuando cambian los IND_numero
        return view('indicadores.panel', compact('categorias'));
    }

}
