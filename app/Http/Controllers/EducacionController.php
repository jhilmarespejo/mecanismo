<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{ModEducacion};


class EducacionController extends Controller
{
    // public function index(){
    //     return view('educacion.index');
    // }

    public function index(Request $request) {
        $anioActual = $request->anio_actual ?? date('Y');

        // Obtener todos los registros para la tabla
        $educacions = ModEducacion::where('EDU_gestion', $anioActual)->get();

        // Consultas estadísticas

        // Consulta para obtener la cantidad de beneficiarios por ciudad, incluyendo el total
        $beneficiariosPorCiudad = DB::table('educacion')
            ->select('EDU_ciudad', DB::raw('SUM("EDU_cantidad_beneficiarios") as total_beneficiarios'))
            ->where('EDU_gestion', $anioActual)
            ->groupBy('EDU_ciudad')
            ->get();

        $totalBeneficiarios = DB::table('educacion')
            ->where('EDU_gestion', $anioActual)
            ->sum("EDU_cantidad_beneficiarios");

        $beneficiariosPorCiudad->push((object) ['EDU_ciudad' => 'Total', 'total_beneficiarios' => $totalBeneficiarios]);

        // Consulta para obtener la cantidad de beneficiarios por tipo, incluyendo el total
        $beneficiariosPorTipo = DB::table('educacion')
            ->select('EDU_beneficiarios', DB::raw('SUM("EDU_cantidad_beneficiarios") as total_beneficiarios'))
            ->where('EDU_gestion', $anioActual)
            ->groupBy('EDU_beneficiarios')
            ->get();

        $totalBeneficiariosPorTipo = DB::table('educacion')
            ->where('EDU_gestion', $anioActual)
            ->sum("EDU_cantidad_beneficiarios");

        $beneficiariosPorTipo->push((object) ['EDU_beneficiarios' => 'Total', 'total_beneficiarios' => $totalBeneficiariosPorTipo]);

        // Consulta para obtener la cantidad de temas por ciudad, incluyendo el total
        $temasPorCiudad = DB::table('educacion')
            ->select('EDU_ciudad', DB::raw('COUNT(DISTINCT "EDU_tema") as total_temas'))
            ->where('EDU_gestion', $anioActual)
            ->groupBy('EDU_ciudad')
            ->get();

        $totalTemasPorCiudad = DB::table('educacion')
            ->where('EDU_gestion', $anioActual)
            ->select(DB::raw('COUNT(DISTINCT "EDU_tema") as total_temas'))
            ->pluck('total_temas')
            ->first();

        $temasPorCiudad->push((object) ['EDU_ciudad' => 'Total', 'total_temas' => $totalTemasPorCiudad]);

        // Obtener temas abordados y cantidad de beneficiarios
        $temasBeneficiarios = DB::table('educacion')
            ->select('EDU_tema', DB::raw('SUM("EDU_cantidad_beneficiarios") as cantidad_beneficiarios'))
            ->where('EDU_gestion', $anioActual)
            ->groupBy('EDU_tema')
            ->get();

        // Agregar el total
        $totalBeneficiarios = DB::table('educacion')
            ->where('EDU_gestion', $anioActual)
            ->sum("EDU_cantidad_beneficiarios");

        $temasBeneficiarios->push((object)[
            'EDU_tema' => 'Total',
            'cantidad_beneficiarios' => $totalBeneficiarios
        ]);

        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Módulo educativo', 'url' => '']
        ];

        return view('educacion.index', compact('educacions', 'beneficiariosPorCiudad', 'beneficiariosPorTipo', 'temasPorCiudad', 'temasBeneficiarios', 'breadcrumbs', 'anioActual'));
    }


    public function create() {
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Módulo educativo', 'url' => route('educacion.index')],
            ['name' => 'Crear nuevo registro', 'url' => ''],
        ];
        return view('educacion.create', compact('breadcrumbs'));
    }


    // Guarda nuevo dato sobre actividades educativas
    public function store(Request $request) {
        // Validación de datos de entrada
        $request->validate([
            'edu_tema' => 'required|string|min:5|max:500',
            'edu_beneficiarios' => 'required|string|min:5|max:500',
            'edu_cantidad_beneficiarios' => 'required|integer', // Cambiado a integer para coincidir con la tabla
            'edu_medio_verificacion' => 'required|string|min:5|max:145',
            'edu_ciudad' => 'required|string|min:5|max:145',
            'edu_gestion' => 'required|integer', // Cambiado a integer para coincidir con la tabla
            'edu_imagen_medio_verificacion' => 'nullable|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:30720', // 30 MB = 30720 KB
        ], [
            'required' => 'El dato es requerido',
            'edu_imagen_medio_verificacion.max' => '¡El archivo debe ser menor o igual a 30MB!',
            'edu_imagen_medio_verificacion.mimes' => 'El archivo debe ser: imagen, documento, audio o video',
            'max' => 'Dato muy extenso',
            'min' => 'Dato muy reducido',
        ]);

        DB::beginTransaction();

        try {
            // Crear una nueva instancia de ModEducacion
            $educacion = new ModEducacion();
            $educacion->EDU_tema = $request->edu_tema; // Actualizado para reflejar la definición de la tabla
            $educacion->EDU_beneficiarios = $request->edu_beneficiarios; // Actualizado
            $educacion->EDU_cantidad_beneficiarios = $request->edu_cantidad_beneficiarios; // Actualizado
            $educacion->EDU_medio_verificacion = $request->edu_medio_verificacion; // Actualizado
            $educacion->EDU_ciudad = $request->edu_ciudad; // Actualizado
            $educacion->EDU_gestion = $request->edu_gestion; // Actualizado
        
            // Manejo del archivo de imagen, si existe
            if ($request->hasFile('edu_imagen_medio_verificacion')) {
                $educacion->EDU_imagen_medio_verificacion = $request->file('edu_imagen_medio_verificacion')->store('images/medio_verificacion', 'public');
            }

            // Guardar la nueva educación
            $educacion->save();

            // Confirmar la transacción
            DB::commit();

            return redirect()->route('educacion.index')->with('success', 'Los datos se han almacenado correctamente.');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al guardar la información.']);
        }
    }


    public function edit($edu_id)    {
        $educacion = ModEducacion::select('*')->where('EDU_id', $edu_id)->first();
        // dd($educacion->toArray());
        // exit;
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Módulo educativo', 'url' => route('educacion.index')],
            ['name' => 'Edición de registro', 'url' => ''],
        ];
        // dump($educacion->toArray());exit;
        return view('educacion.edit', compact('educacion', 'breadcrumbs'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'edu_tema' => 'required|string|min:5|max:500',
            'edu_beneficiarios' => 'required|string|min:5|max:500',
            'edu_cantidad_beneficiarios' => 'required|integer', // Cambiado a integer para coincidir con la tabla
            'edu_medio_verificacion' => 'required|string|min:5|max:145',
            'edu_ciudad' => 'required|string|min:5|max:145',
            'edu_gestion' => 'required|integer', // Cambiado a integer para coincidir con la tabla
            'edu_imagen_medio_verificacion' => 'nullable|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:30720', // 30 MB = 30720 KB
        ], [
            'required' => 'El dato es requerido',
            'edu_imagen_medio_verificacion.max' => '¡El archivo debe ser menor o igual a 30MB!',
            'edu_imagen_medio_verificacion.mimes' => 'El archivo debe ser: imagen, documento, audio o video',
            'max' => 'Dato muy extenso',
            'min' => 'Dato muy reducido',
        ]);

        DB::beginTransaction();

        try {

            $updateData = [
                'EDU_tema' => $request->input('edu_tema'),
                'EDU_beneficiarios' => $request->input('edu_beneficiarios'),
                'EDU_cantidad_beneficiarios' => $request->input('edu_cantidad_beneficiarios'),
                'EDU_medio_verificacion' => $request->input('edu_medio_verificacion'),
                'EDU_ciudad' => $request->input('edu_ciudad'),
                'EDU_gestion' => $request->input('edu_gestion'),
            ];

            // Verificar si hay un archivo nuevo y procesarlo
            if ($request->hasFile('edu_imagen_medio_verificacion')) {
                $updateData['EDU_imagen_medio_verificacion'] = $request->file('edu_imagen_medio_verificacion')->store('images/medio_verificacion', 'public');
            }

            // Actualizar los datos en la base de datos
            DB::table('educacion')->where('EDU_id', $id)->update($updateData);

            DB::commit();
            return redirect()->route('educacion.index')->with('success', 'Los datos se han actualizado correctamente.');
    
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            dump($e);exit;      
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al actualizar la información.']);
        }
    }



    public function destroy($id)
    {
        $educacion = ModEducacion::find($id);
        $educacion->delete();
        return redirect()->route('educacion.index');
    }

}

