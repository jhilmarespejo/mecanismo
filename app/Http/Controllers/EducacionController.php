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
        $anioActual=0;
        if( is_null($request->anio_actual ) ){
            $anioActual = date('Y');
        } else {
            $anioActual = $request->anio_actual;
        }
        
        // Obtener todos los registros para la tabla
        $educacions = ModEducacion::all()->where('edu_gestion', $anioActual);
        
        // Consultas estadísticas

        // Consulta para obtener la cantidad de beneficiarios por ciudad, incluyendo el total
        $beneficiariosPorCiudad = DB::table('educacion')
            ->select('edu_ciudad', DB::raw('SUM(edu_cantidad_beneficiarios) as total_beneficiarios'))
            ->where('edu_gestion', $anioActual)
            ->groupBy('edu_ciudad')
            ->get();

        $totalBeneficiarios = DB::table('educacion')->where('edu_gestion', $anioActual)
            ->sum('edu_cantidad_beneficiarios');
        $beneficiariosPorCiudad->push((object) ['edu_ciudad' => 'Total', 'total_beneficiarios' => $totalBeneficiarios]);
        
        // Consulta para obtener la cantidad de beneficiarios por tipo, incluyendo el total
        $beneficiariosPorTipo = DB::table('educacion')
        ->select('edu_beneficiarios', DB::raw('SUM(edu_cantidad_beneficiarios) as total_beneficiarios'))
        ->where('edu_gestion', $anioActual)
        ->groupBy('edu_beneficiarios')
        ->get();
        
        $totalBeneficiariosPorTipo = DB::table('educacion')->where('edu_gestion', $anioActual)
        ->sum('edu_cantidad_beneficiarios');
        
        $beneficiariosPorTipo->push((object) ['edu_beneficiarios' => 'Total', 'total_beneficiarios' => $totalBeneficiariosPorTipo]);
        
        // // Consulta para obtener la cantidad de temas por ciudad, incluyendo el total
        $temasPorCiudad = DB::table('educacion')
        ->select('edu_ciudad', DB::raw('COUNT(DISTINCT edu_tema) as total_temas'))
        ->where('edu_gestion', $anioActual)
        ->groupBy('edu_ciudad')
        ->get();
        
        $totalTemasPorCiudad = DB::table('educacion')->where('edu_gestion', $anioActual)
        ->select(DB::raw('COUNT(DISTINCT edu_tema) as total_temas'))
        ->where('edu_gestion', $anioActual)
        ->pluck('total_temas')
        ->first();
        
        $temasPorCiudad->push((object) ['edu_ciudad' => 'Total', 'total_temas' => $totalTemasPorCiudad]);
        
        // Obtener temas abordados y cantidad de beneficiarios
        $temasBeneficiarios = DB::table('educacion')
            ->select('edu_tema', DB::raw('COUNT(edu_beneficiarios) as cantidad_beneficiarios'))
            ->where('edu_gestion', $anioActual)
            ->groupBy('edu_tema')
            ->get();

            // Agregar el total
            $totalBeneficiarios = DB::table('educacion')
                ->select(DB::raw('COUNT(edu_beneficiarios) as cantidad_beneficiarios'))
                ->where('edu_gestion', $anioActual)
                ->first();

        $temasBeneficiarios->push((object)[
            'edu_tema' => 'Total',
            'cantidad_beneficiarios' => $totalBeneficiarios->cantidad_beneficiarios
        ]);

        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Módulo educativo', 'url' => '']
        ];



        return view('educacion.index', compact('educacions', 'beneficiariosPorCiudad', 'beneficiariosPorTipo', 'temasPorCiudad', 'temasBeneficiarios', 'breadcrumbs', 'anioActual'));
    }

    public function create()
    {
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Módulo educativo', 'url' => route('educacion.index')],
            ['name' => 'Crear nuevo registro', 'url' => ''],
        ];
        return view('educacion.create', compact('breadcrumbs'));
    }


    //Guarda nuevo dato sobre actividades educativas
    public function store(Request $request){
        // dump($request->all());exit;
        $request->validate([
            'edu_tema' => 'required|string|min:5|max:500',
            'edu_beneficiarios' => 'required|string|min:5|max:500',
            'edu_cantidad_beneficiarios' => 'required|numeric',
            'edu_medio_verificacion' => 'required|string|min:5|max:145',
            'edu_ciudad' => 'required|string|min:5|max:145',
            'edu_gestion' => 'required|numeric',
            'edu_imagen_medio_verificacion' => 'nullable|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:30505', // 30 mb
        ], [
            'required' => 'El dato es requerido',
            'ARC_archivo.max' => '¡El archivos debe ser menor o igual a 30MB!',
            'ARC_archivo.mimes' => 'El archivos debe ser: imagen, documento, audio o video',
            'max' => 'Dato muy extenso',
            'min' => 'Dato muy reducido',
        ]);
        DB::beginTransaction();

        try {
            $educacion = new ModEducacion();
            $educacion->edu_tema = $request->edu_tema;
            $educacion->edu_beneficiarios = $request->edu_beneficiarios;
            $educacion->edu_cantidad_beneficiarios = $request->edu_cantidad_beneficiarios;
            $educacion->edu_medio_verificacion = $request->edu_medio_verificacion;
            $educacion->edu_ciudad = $request->edu_ciudad;
            $educacion->edu_gestion = $request->edu_gestion;
            if ($request->hasFile('edu_imagen_medio_verificacion')) {
                $educacion->edu_imagen_medio_verificacion = $request->file('edu_imagen_medio_verificacion')->store('images/medio_verificacion', 'public');
            }
            
            $educacion->save();

            DB::commit();

            return redirect()->route('educacion.index')->with('success', 'Los datos se han almacenado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al guardar la información.']);
        }
        exit;
    }

    public function edit($edu_id)
    {
        $educacion = ModEducacion::find($edu_id);
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Módulo educativo', 'url' => route('educacion.index')],
            ['name' => 'Edición de registro', 'url' => ''],
        ];
        // dump($educacion->toArray());exit;
        return view('educacion.edit', compact('educacion', 'breadcrumbs'));
    }

    public function update(Request $request, $id)
    {
        
        $request->validate([
            'edu_tema' => 'required|string|min:5|max:500',
            'edu_beneficiarios' => 'required|string|min:5|max:500',
            'edu_cantidad_beneficiarios' => 'required|numeric',
            'edu_medio_verificacion' => 'required|string|min:5|max:145',
            'edu_ciudad' => 'required|string|min:5|max:145',
            'edu_gestion' => 'required|numeric',
            'edu_imagen_medio_verificacion' => 'nullable|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:30505', // 30 mb
        ], [
            'required' => 'El dato es requerido',
            'ARC_archivo.max' => '¡El archivos debe ser menor o igual a 30MB!',
            'ARC_archivo.mimes' => 'El archivos debe ser: imagen, documento, audio o video',
            'max' => 'Dato muy extenso',
            'min' => 'Dato muy reducido',
        ]);
        DB::beginTransaction();

        try {
            $educacion = ModEducacion::find($id);
            $educacion->edu_tema = $request->edu_tema;
            $educacion->edu_beneficiarios = $request->edu_beneficiarios;
            $educacion->edu_cantidad_beneficiarios = $request->edu_cantidad_beneficiarios;
            $educacion->edu_medio_verificacion = $request->edu_medio_verificacion;
            $educacion->edu_gestion = $request->edu_gestion;
            $educacion->edu_ciudad = $request->edu_ciudad;
            if ($request->hasFile('edu_imagen_medio_verificacion')) {
                // dump($request->all());exit;
                $educacion->edu_imagen_medio_verificacion = $request->file('edu_imagen_medio_verificacion')->store('images/medio_verificacion', 'public');
                // dd($request->file('edu_imagen_medio_verificacion')->store('images/medio_verificacion', 'public'));
            }
            $educacion->save();
            DB::commit();

            return redirect()->route('educacion.index')->with('success', 'Los datos se han actualizado correctamente.');

            
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al guardar la información.']);
        }
        exit;
    }

    public function destroy($id)
    {
        $educacion = ModEducacion::find($id);
        $educacion->delete();
        return redirect()->route('educacion.index');
    }

}

