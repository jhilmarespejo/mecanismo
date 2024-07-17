<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsesoramientoController extends Controller
{
    public function index()
    {
        $asesoramientos = DB::table('asesoramientos as ase')
        ->select('ase.*', 'man.MAN_mandato', 'man.MAN_descripcion_mandato')
        ->leftJoin('mandatos as man', 'man.MAN_id', 'ase.FK_MAN_id')
        ->get();

        $asesoramientos = $asesoramientos->groupBy(['MAN_mandato', 'MAN_descripcion_mandato'])->toArray();
        // @dump($asesoramientos);exit;

        return view('asesoramientos.index', compact('asesoramientos'));
    }

    public function create()
    {
        $mandatos = DB::table('mandatos')->get();
        return view('asesoramientos.create', ['mandatos' => $mandatos]);
    }

    public function store(Request $request)
    {
        // Definir las reglas de validación
        $request->validate([
            'ASE_actividad' => 'required|string|',
            'ASE_fecha_actividad' => 'required|date',
            // 'ASE_recomendacion' => 'nullable|string|max:255',
            'FK_MAN_id' => 'required|integer|exists:mandatos,MAN_id',
        ], [
            'ASE_actividad.required' => 'La actividad es obligatoria.',
            'ASE_actividad.string' => 'La actividad debe ser una cadena de texto.',
            'ASE_actividad.max' => 'La actividad no debe exceder los 255 caracteres.',
            'ASE_fecha_actividad.required' => 'La fecha de actividad es obligatoria.',
            'ASE_fecha_actividad.date' => 'La fecha de actividad debe ser una fecha válida.',
            // 'ASE_recomendacion.string' => 'La recomendación debe ser una cadena de texto.',
            // 'ASE_recomendacion.max' => 'La recomendación no debe exceder los 255 caracteres.',
            'FK_MAN_id.required' => 'El mandato es obligatorio.',
            'FK_MAN_id.integer' => 'El mandato debe ser un número entero.',
            'FK_MAN_id.exists' => 'El mandato seleccionado no existe.',
        ]);

        // Insertar los datos en la tabla 'asesoramientos'
        DB::table('asesoramientos')->insert([
            'ASE_actividad' => $request->input('ASE_actividad'),
            'ASE_fecha_actividad' => $request->input('ASE_fecha_actividad'),
            'ASE_recomendacion' => $request->input('ASE_recomendacion'),
            'FK_MAN_id' => $request->input('FK_MAN_id'),
        ]);

        // Redirigir al listado de asesoramientos
        return redirect('/asesoramientos')->with('success', 'Asesoramiento creado exitosamente.');
    }

    public function show($id)
    {
        $asesoramiento = DB::table('asesoramientos')->where('ASE_id', $id)->first();
        return view('asesoramientos.show', ['asesoramiento' => $asesoramiento]);
    }

    public function edit($id)
    {
        $asesoramiento = DB::table('asesoramientos')->where('ASE_id', $id)->first();
        $mandatos = DB::table('mandatos')->get();
        return view('asesoramientos.edit', ['asesoramiento' => $asesoramiento, 'mandatos' => $mandatos]);
    }

    public function update(Request $request, $id)
    {
        DB::table('asesoramientos')->where('ASE_id', $id)->update([
            'ASE_actividad' => $request->input('ASE_actividad'),
            'ASE_fecha_actividad' => $request->input('ASE_fecha_actividad'),
            'ASE_recomendacion' => $request->input('ASE_recomendacion'),
            'FK_MAN_id' => $request->input('FK_MAN_id'),
        ]);
        return redirect('/asesoramientos');
    }

    public function destroy($id)
    {
        DB::table('asesoramientos')->where('ASE_id', $id)->delete();
        return redirect('/asesoramientos');
    }
}

