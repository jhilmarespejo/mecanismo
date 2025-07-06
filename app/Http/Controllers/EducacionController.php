<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{ModEducacion};
use Illuminate\Support\Facades\Storage;

class EducacionController extends Controller
{
    public function index(Request $request) {
        $anioActual = $request->anio_actual ?? date('Y');

        // Obtener todos los registros para la tabla
        $educacions = ModEducacion::where('EDU_gestion', $anioActual)->get();

        // Consultas estadísticas
        $beneficiariosPorCiudad = DB::table('educacion')
            ->select('EDU_ciudad', DB::raw('SUM("EDU_cantidad_beneficiarios") as total_beneficiarios'))
            ->where('EDU_gestion', $anioActual)
            ->groupBy('EDU_ciudad')
            ->get();

        $totalBeneficiarios = DB::table('educacion')
            ->where('EDU_gestion', $anioActual)
            ->sum("EDU_cantidad_beneficiarios");

        $beneficiariosPorCiudad->push((object) ['EDU_ciudad' => 'Total', 'total_beneficiarios' => $totalBeneficiarios]);

        $beneficiariosPorTipo = DB::table('educacion')
            ->select('EDU_beneficiarios', DB::raw('SUM("EDU_cantidad_beneficiarios") as total_beneficiarios'))
            ->where('EDU_gestion', $anioActual)
            ->groupBy('EDU_beneficiarios')
            ->get();

        $totalBeneficiariosPorTipo = DB::table('educacion')
            ->where('EDU_gestion', $anioActual)
            ->sum("EDU_cantidad_beneficiarios");

        $beneficiariosPorTipo->push((object) ['EDU_beneficiarios' => 'Total', 'total_beneficiarios' => $totalBeneficiariosPorTipo]);

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

        $temasBeneficiarios = DB::table('educacion')
            ->select('EDU_tema', DB::raw('SUM("EDU_cantidad_beneficiarios") as cantidad_beneficiarios'))
            ->where('EDU_gestion', $anioActual)
            ->groupBy('EDU_tema')
            ->get();

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

    public function store(Request $request) {
        // Validación de datos de entrada
        $request->validate([
            'edu_tema' => 'required|string|min:5|max:500',
            'edu_beneficiarios' => 'required|string|min:5|max:500',
            'edu_cantidad_beneficiarios' => 'required|integer|min:1',
            'edu_medio_verificacion' => 'required|string|min:5|max:500',
            'edu_ciudad' => 'required|string|min:3|max:145',
            'edu_gestion' => 'required|integer|min:2020|max:2030',
            'edu_fecha_inicio' => 'required|date|before_or_equal:edu_fecha_fin',
            'edu_fecha_fin' => 'required|date|after_or_equal:edu_fecha_inicio',
            'edu_imagen_medio_verificacion.*' => 'nullable|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:30720',
        ], [
            'required' => 'El campo :attribute es requerido',
            'edu_imagen_medio_verificacion.*.max' => '¡El archivo debe ser menor o igual a 30MB!',
            'edu_imagen_medio_verificacion.*.mimes' => 'El archivo debe ser: imagen, documento, audio o video',
            'max' => 'El campo :attribute no debe exceder :max caracteres',
            'min' => 'El campo :attribute debe tener al menos :min caracteres',
            'edu_fecha_inicio.before_or_equal' => 'La fecha de inicio debe ser anterior o igual a la fecha de fin',
            'edu_fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio',
        ]);

        DB::beginTransaction();

        try {
            $educacion = new ModEducacion();
            $educacion->EDU_tema = $request->edu_tema;
            $educacion->EDU_beneficiarios = $request->edu_beneficiarios;
            $educacion->EDU_cantidad_beneficiarios = $request->edu_cantidad_beneficiarios;
            $educacion->EDU_medio_verificacion = $request->edu_medio_verificacion;
            $educacion->EDU_ciudad = $request->edu_ciudad;
            $educacion->EDU_gestion = $request->edu_gestion;
            $educacion->EDU_fecha_inicio = $request->edu_fecha_inicio;
            $educacion->EDU_fecha_fin = $request->edu_fecha_fin;

            // Manejo de múltiples archivos
            if ($request->hasFile('edu_imagen_medio_verificacion')) {
                $archivos = [];
                foreach ($request->file('edu_imagen_medio_verificacion') as $archivo) {
                    $path = $archivo->store('uploads/medio_verificacion', 'public');
                    $archivos[] = $path;
                }
                $educacion->EDU_imagen_medio_verificacion = json_encode($archivos);
            }

            $educacion->save();
            DB::commit();

            return redirect()->route('educacion.index')->with('success', 'Actividad educativa creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al guardar la información: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id) {
        $educacion = ModEducacion::findOrFail($id);
        
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Módulo educativo', 'url' => route('educacion.index')],
            ['name' => 'Detalles de actividad', 'url' => ''],
        ];

        return view('educacion.show', compact('educacion', 'breadcrumbs'));
    }

    public function edit($edu_id) {
        $educacion = ModEducacion::where('EDU_id', $edu_id)->firstOrFail();
        
        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Módulo educativo', 'url' => route('educacion.index')],
            ['name' => 'Edición de registro', 'url' => ''],
        ];
        
        return view('educacion.edit', compact('educacion', 'breadcrumbs'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'edu_tema' => 'required|string|min:5|max:500',
            'edu_beneficiarios' => 'required|string|min:5|max:500',
            'edu_cantidad_beneficiarios' => 'required|integer|min:1',
            'edu_medio_verificacion' => 'required|string|min:5|max:500',
            'edu_ciudad' => 'required|string|min:3|max:145',
            'edu_gestion' => 'required|integer|min:2020|max:2030',
            'edu_fecha_inicio' => 'required|date|before_or_equal:edu_fecha_fin',
            'edu_fecha_fin' => 'required|date|after_or_equal:edu_fecha_inicio',
            'edu_imagen_medio_verificacion.*' => 'nullable|mimes:jpg,jpeg,png,pdf,webm,mp4,mov,flv,mkv,wmv,avi,mp3,ogg,acc,flac,wav,xls,xlsx,ppt,pptx,doc,docx|max:30720',
        ], [
            'required' => 'El campo :attribute es requerido',
            'edu_imagen_medio_verificacion.*.max' => '¡El archivo debe ser menor o igual a 30MB!',
            'edu_imagen_medio_verificacion.*.mimes' => 'El archivo debe ser: imagen, documento, audio o video',
            'max' => 'El campo :attribute no debe exceder :max caracteres',
            'min' => 'El campo :attribute debe tener al menos :min caracteres',
            'edu_fecha_inicio.before_or_equal' => 'La fecha de inicio debe ser anterior o igual a la fecha de fin',
            'edu_fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio',
        ]);

        DB::beginTransaction();

        try {
            $educacion = ModEducacion::findOrFail($id);
            
            $updateData = [
                'EDU_tema' => $request->input('edu_tema'),
                'EDU_beneficiarios' => $request->input('edu_beneficiarios'),
                'EDU_cantidad_beneficiarios' => $request->input('edu_cantidad_beneficiarios'),
                'EDU_medio_verificacion' => $request->input('edu_medio_verificacion'),
                'EDU_ciudad' => $request->input('edu_ciudad'),
                'EDU_gestion' => $request->input('edu_gestion'),
                'EDU_fecha_inicio' => $request->input('edu_fecha_inicio'),
                'EDU_fecha_fin' => $request->input('edu_fecha_fin'),
            ];

            // Manejo de nuevos archivos
            if ($request->hasFile('edu_imagen_medio_verificacion')) {
                // Eliminar archivos anteriores si existen
                if ($educacion->EDU_imagen_medio_verificacion) {
                    $archivosAnteriores = json_decode($educacion->EDU_imagen_medio_verificacion, true);
                    if (is_array($archivosAnteriores)) {
                        foreach ($archivosAnteriores as $archivo) {
                            Storage::disk('public')->delete($archivo);
                        }
                    } else {
                        // Si es un string (formato anterior)
                        Storage::disk('public')->delete($educacion->EDU_imagen_medio_verificacion);
                    }
                }

                $archivos = [];
                foreach ($request->file('edu_imagen_medio_verificacion') as $archivo) {
                    $path = $archivo->store('uploads/medio_verificacion', 'public');
                    $archivos[] = $path;
                }
                $updateData['EDU_imagen_medio_verificacion'] = json_encode($archivos);
            }

            DB::table('educacion')->where('EDU_id', $id)->update($updateData);

            DB::commit();
            return redirect()->route('educacion.index')->with('success', 'Actividad educativa actualizada exitosamente.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al actualizar la información: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id) {
        try {
            $educacion = ModEducacion::findOrFail($id);
            
            // Eliminar archivos asociados
            if ($educacion->EDU_imagen_medio_verificacion) {
                $archivos = json_decode($educacion->EDU_imagen_medio_verificacion, true);
                if (is_array($archivos)) {
                    foreach ($archivos as $archivo) {
                        Storage::disk('public')->delete($archivo);
                    }
                } else {
                    // Si es un string (formato anterior)
                    Storage::disk('public')->delete($educacion->EDU_imagen_medio_verificacion);
                }
            }
            
            $educacion->delete();
            
            return redirect()->route('educacion.index')->with('success', 'Actividad educativa eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('educacion.index')->with('error', 'Error al eliminar la actividad: ' . $e->getMessage());
        }
    }
}