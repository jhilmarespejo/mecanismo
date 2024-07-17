<?php

namespace App\Http\Controllers;

use App\Models\HistorialIndicador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{ModIndicador, ModHistorialIndicador};

class HistorialIndicadorController extends Controller
{
    public function index()
    {
        $historialIndicadores = DB::table('historial_indicadores')->get();
        return view('historial_indicadores.index', compact('historialIndicadores'));
    }

    public function create()
    {   // Obtener todas las preguntas del indicador respectivo
        $preguntas = ModIndicador::pluck('IND_pregunta', 'IND_id');
        return view('historial_indicadores.create', compact('preguntas'));
    }

    public function show(HistorialIndicador $historialIndicador)
    {
        return view('historial_indicadores.show', compact('historialIndicador'));
    }

    public function edit(HistorialIndicador $historialIndicador)
    {
        return view('historial_indicadores.edit', compact('historialIndicador'));
    }

    public function store(Request $request)
    {
        // dump( $request->all());exit;
        $request->validate([
            'HIN_respuesta' => 'required|max:500',
            'HIN_fecha_respuesta' => 'required',
            'HIN_fuente_verificacion' => 'required',
            'FK_IND_id' => 'required|exists:App\Models\ModIndicador,IND_id',
        ]);

        $historialIndicador = new ModHistorialIndicador;
        $historialIndicador->HIN_respuesta = $request->HIN_respuesta;
        $historialIndicador->HIN_fecha_respuesta = $request->HIN_fecha_respuesta;
        $historialIndicador->HIN_fuente_verificacion = $request->HIN_fuente_verificacion;
        $historialIndicador->FK_IND_id = $request->FK_IND_id;
        $historialIndicador->save();

        return redirect()->route('historial-indicadores.index')->with('success', 'Historial de Indicador creado correctamente.');
    }

    public function update(Request $request, HistorialIndicador $historialIndicador)
    {
        $request->validate([
            'HIN_respuesta' => 'required|max:500',
            'HIN_fecha_respuesta' => 'required',
            'HIN_fuente_verificacion' => 'required',
            'FK_IND_id' => 'required|exists:App\Models\Indicador,IND_id',
        ]);

        $historialIndicador->HIN_respuesta = $request->HIN_respuesta;
        $historialIndicador->HIN_fecha_respuesta = $request->HIN_fecha_respuesta;
        $historialIndicador->HIN_fuente_verificacion = $request->HIN_fuente_verificacion;
        $historialIndicador->FK_IND_id = $request->FK_IND_id;
        $historialIndicador->save();

        return redirect()->route('historial-indicadores.index')->with('success', 'Historial de Indicador actualizado correctamente.');
    }

    public function destroy(HistorialIndicador $historialIndicador)
    {
        // Implementar la lógica para eliminar un historial de indicador
    }
}
