<?php

namespace App\Http\Controllers;

use App\Models\{ModVisita, ModFormulario, ModBancoPregunta, ModEstablecimiento, ModRespuesta, ModArchivo, ModTipoEstablecimiento, ModEstablecimientoInfo, ModEstablecimientoPersonal};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator, Auth, Session, URL, Storage};

use Intervention\Image\Facades\Image;
use App\Http\Controllers\{CustomController};
use Illuminate\Support\Arr;


use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\Style\ListItem;
// use PhpOffice\PhpWord\Style\addTableStyle;

class VisitaController extends Controller{
    
    // Guardar datos de nueva visita
    public function guardarNuevaVisita( Request $request ) {
        
        $validator = Validator::make($request->all(), [
            'VIS_tipo' => 'required',
            'VIS_fechas' => 'required|date|after_or_equal:today',
        ], [
            'required' => 'El dato es requerido!',
            'after_or_equal' => 'La fecha está en el pasado'
        ]);

        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            DB::beginTransaction();
            try {
                ModVisita::insert($request->except('_token'));
                DB::commit();
                // dump($request->except('_token'));exit;
                //return response()->json([ "message" => "¡Datos almacenados con exito!" ]);
            }catch (\Exception $e) {
                DB::rollback();
                exit ($e->getMessage());
            }
        }
    }


    /**
     * historial
     *
     * @param  mixed $id
     * @return void
     * Consulta para obtener el historial de las visitas realizadas al establecimiento
     */
    public function historial($id){
        $anioActual = date('Y'); // Obtiene el año actual
        
        // Obtener información básica del establecimiento (siempre existe)
        $establecimientoBase = ModEstablecimiento::select(
            'establecimientos.EST_id',
            'establecimientos.EST_nombre',
            'establecimientos.EST_departamento',
            'establecimientos.EST_municipio',
            'establecimientos.EST_direccion',
            'establecimientos.EST_telefono_contacto',
            'establecimientos.EST_capacidad_creacion',
            'establecimientos.EST_anyo_funcionamiento',
            'tipo_establecimientos.TES_tipo'
        )
        ->join('tipo_establecimientos', 'tipo_establecimientos.TES_id', '=', 'establecimientos.FK_TES_id')
        ->where('establecimientos.EST_id', $id)
        ->first();
        
        // Si no existe el establecimiento, retornar error o redirigir
        if (!$establecimientoBase) {
            abort(404, 'Establecimiento no encontrado');
        }
        
        // Obtener información adicional del establecimiento para el año actual
        $establecimientoInfo = ModEstablecimientoInfo::select(
            'EINF_poblacion_atendida',
            'EINF_cantidad_actual_internos',
            'EINF_superficie_terreno',
            'EINF_superficie_construida',
            'EINF_derecho_propietario',
            'EINF_gestion'
        )
        ->where('FK_EST_id', $id)
        ->where('EINF_gestion', $anioActual)
        ->first();
        
        // Si no hay info del año actual, buscar la más reciente
        if (!$establecimientoInfo) {
            $establecimientoInfo = ModEstablecimientoInfo::select(
                'EINF_poblacion_atendida',
                'EINF_cantidad_actual_internos',
                'EINF_superficie_terreno',
                'EINF_superficie_construida',
                'EINF_derecho_propietario',
                'EINF_gestion'
            )
            ->where('FK_EST_id', $id)
            ->orderByDesc('EINF_gestion')
            ->first();
        }
        
        // CREAR EL OBJETO ESTABLECIMIENTO DE FORMA CORRECTA
        $establecimiento = new \stdClass();
        
        // Asignar datos base (siempre existen)
        $establecimiento->EST_id = $establecimientoBase->EST_id;
        $establecimiento->EST_nombre = $establecimientoBase->EST_nombre;
        $establecimiento->EST_departamento = $establecimientoBase->EST_departamento;
        $establecimiento->EST_municipio = $establecimientoBase->EST_municipio;
        $establecimiento->EST_direccion = $establecimientoBase->EST_direccion;
        $establecimiento->EST_telefono_contacto = $establecimientoBase->EST_telefono_contacto;
        $establecimiento->EST_capacidad_creacion = $establecimientoBase->EST_capacidad_creacion;
        $establecimiento->EST_anyo_funcionamiento = $establecimientoBase->EST_anyo_funcionamiento;
        $establecimiento->TES_tipo = $establecimientoBase->TES_tipo;
        
        // Asignar datos de información adicional (pueden ser null)
        if ($establecimientoInfo) {
            $establecimiento->EINF_poblacion_atendida = $establecimientoInfo->EINF_poblacion_atendida;
            $establecimiento->EINF_cantidad_actual_internos = $establecimientoInfo->EINF_cantidad_actual_internos;
            $establecimiento->EINF_superficie_terreno = $establecimientoInfo->EINF_superficie_terreno;
            $establecimiento->EINF_superficie_construida = $establecimientoInfo->EINF_superficie_construida;
            $establecimiento->EINF_derecho_propietario = $establecimientoInfo->EINF_derecho_propietario;
            $establecimiento->EINF_gestion = $establecimientoInfo->EINF_gestion;
        } else {
            // Si no hay información adicional, asignar null
            $establecimiento->EINF_poblacion_atendida = null;
            $establecimiento->EINF_cantidad_actual_internos = null;
            $establecimiento->EINF_superficie_terreno = null;
            $establecimiento->EINF_superficie_construida = null;
            $establecimiento->EINF_derecho_propietario = null;
            $establecimiento->EINF_gestion = $anioActual; // Gestión actual por defecto
        }
        
        // Obtener el responsable del establecimiento para el año actual
        $responsable = ModEstablecimientoPersonal::select(
            'EPER_nombre_responsable',
            'EPER_grado_profesion',
            'EPER_telefono',
            'EPER_email',
            'EPER_gestion'
        )
        ->where('FK_EST_id', $id)
        ->where('EPER_gestion', $anioActual)
        ->first();
        
        // Si no hay responsable del año actual, buscar el más reciente
        if (!$responsable) {
            $responsable = ModEstablecimientoPersonal::select(
                'EPER_nombre_responsable',
                'EPER_grado_profesion',
                'EPER_telefono',
                'EPER_email',
                'EPER_gestion'
            )
            ->where('FK_EST_id', $id)
            ->orderByDesc('EPER_gestion')
            ->first();
        }
        
        // Obtener documentos del establecimiento (reglamento, licencia, foto fachada)
        $documentos = ModArchivo::select(
            'ARC_id',
            'ARC_descripcion',
            'ARC_ruta',
            'ARC_extension',
            'ARC_formatoArchivo',
            'ARC_origen'
        )
        ->where('FK_EST_id', $id)
        ->whereIn('ARC_origen', ['reglamento', 'licencia', 'fachada'])
        ->get()
        ->keyBy('ARC_origen');
        
        // Obtener visitas 
        $visitas = ModVisita::from('visitas as v')
        ->select('v.VIS_id', 'v.VIS_fechas', 'v.VIS_tipo', 'v.VIS_titulo', 'e.EST_nombre', 'e.EST_id', 'tes.TES_tipo')
        ->rightJoin('establecimientos as e', 'e.EST_id', 'v.FK_EST_id')
        ->rightJoin('tipo_establecimientos as tes', 'tes.TES_id', 'e.FK_TES_id')
        ->where('e.EST_id', $id)
        ->orderBy('v.VIS_fechas', 'desc')
        ->get();

        // Verificar si hay visitas antes de acceder al array
        if ($visitas->count() > 0) {
            session()->put('EST_id', $visitas->first()->EST_id);
            session()->put('EST_nombre', $visitas->first()->EST_nombre);
            session()->put('TES_tipo', $visitas->first()->TES_tipo);
        } else {
            // Usar los datos del establecimiento base que ya tenemos
            session()->put('EST_id', $establecimientoBase->EST_id);
            session()->put('EST_nombre', $establecimientoBase->EST_nombre);
            session()->put('TES_tipo', $establecimientoBase->TES_tipo);
        }
        
        // DEBUG: Temporalmente para verificar datos
        // dd($establecimiento, $responsable);
        
        return view('visita.visita-historial', compact('visitas', 'establecimiento', 'responsable', 'documentos', 'anioActual'));
    }
        
    
    /*Vista para guardar nueva acta de Visita */
    public function actaVisita($VIS_id){
        $visita = ModArchivo::select('ARC_formatoArchivo', 'ARC_ruta', 'ARC_extension', 'FK_VIS_id')
        ->where('FK_VIS_id', $VIS_id)
        ->get()->toArray();
        
        return view('visita.acta-visita', compact('VIS_id','visita'));
    }

    public function guardarActaVisita(Request $request){
        $request->validate([
            'VIS_acta' => 'required|mimes:pdf,jpg,jpeg,png,xls,xlsx,ppt,pptx,doc,docx|max:20048',
        ], [
            'VIS_acta.required' => 'El archivo es necesario!!!!',
            'VIS_acta.max' => 'El archivo debe ser menor a 20Mb',
            'VIS_acta.mimes' => 'Puede subir archivos de imagen o PDF'
        ]);

        DB::beginTransaction();
        try {
            // Guardar el archivo en la tabla de archivos y en la carpeta actas
            $tipoArchivo = explode("/", $request->VIS_acta->getClientMimeType());
            //Guarda datos del archivo en la tabla archivos
            ModArchivo::create([
                'ARC_NombreOriginal' => $request->VIS_acta->getClientOriginalName(),
                'ARC_ruta' => $request->VIS_acta->store('/uploads/actas'),
                'ARC_extension' => $request->VIS_acta->extension(),
                'ARC_tamanio' => $request->VIS_acta->getSize(),
                'ARC_descripcion' => 'Acta de visita',
                'ARC_formatoArchivo' => $tipoArchivo[0],
                'FK_VIS_id' => $request->VIS_id,
                'ARC_origen' => 'acta'
            ]);

            if($tipoArchivo[0] == 'image'){
                Image::make($request->VIS_acta)
                ->resize(null, 600, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('uploads/actas/').$request->VIS_acta->store(''));
            } else {
                $request->VIS_acta->move(public_path('uploads/actas/'), $request->VIS_acta->store(''));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Correcto');
        }
        catch (\Exception $e) {
            DB::rollback();
            exit ($e->getMessage());
        }
    }
    
    public function guardarDocumentoEstablecimiento(Request $request){
        $request->validate([
            'documento' => 'required|mimes:pdf,jpg,jpeg,png|max:20048',
            'tipo_documento' => 'required|in:reglamento,licencia,fachada',
            'EST_id' => 'required|exists:establecimientos,EST_id'
        ], [
            'documento.required' => 'El archivo es necesario',
            'documento.max' => 'El archivo debe ser menor a 20Mb',
            'documento.mimes' => 'Puede subir archivos de imagen o PDF',
            'tipo_documento.required' => 'El tipo de documento es requerido',
            'tipo_documento.in' => 'Tipo de documento no válido',
            'EST_id.required' => 'El establecimiento es requerido',
            'EST_id.exists' => 'El establecimiento no existe'
        ]);

        DB::beginTransaction();
        try {
            $tipoArchivo = explode("/", $request->documento->getClientMimeType());
            $descripcion = '';
            $carpeta = '';
            
            switch ($request->tipo_documento) {
                case 'reglamento':
                    $descripcion = 'Reglamento del centro';
                    $carpeta = 'reglamentos';
                    break;
                case 'licencia':
                    $descripcion = 'Licencia de funcionamiento';
                    $carpeta = 'licencias';
                    break;
                case 'fachada':
                    $descripcion = 'Fotografía de fachada';
                    $carpeta = 'fachadas';
                    break;
            }
            
            // Verificar si ya existe un documento de este tipo para el establecimiento
            $documentoExistente = ModArchivo::where('FK_EST_id', $request->EST_id)
                ->where('ARC_origen', $request->tipo_documento)
                ->first();
                
            if ($documentoExistente) {
                // Eliminar el archivo anterior del sistema de archivos
                if (Storage::exists($documentoExistente->ARC_ruta)) {
                    Storage::delete($documentoExistente->ARC_ruta);
                }
                // También eliminar del public path si existe
                $publicPath = public_path($documentoExistente->ARC_ruta);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                }
                // Eliminar registro de la base de datos
                $documentoExistente->delete();
            }
            
            // Generar nombre único para el archivo
            $nombreArchivo = time() . '_' . $request->tipo_documento . '.' . $request->documento->extension();
            
            // Definir la ruta completa
            $rutaCompleta = 'uploads/establecimientos/' . $carpeta . '/' . $nombreArchivo;
            
            // Guardar datos del archivo en la tabla archivos
            $archivo = ModArchivo::create([
                'ARC_NombreOriginal' => $request->documento->getClientOriginalName(),
                'ARC_ruta' => $rutaCompleta,
                'ARC_extension' => $request->documento->extension(),
                'ARC_tamanio' => $request->documento->getSize(),
                'ARC_descripcion' => $descripcion,
                'ARC_formatoArchivo' => $tipoArchivo[0],
                'FK_EST_id' => $request->EST_id,
                'ARC_origen' => $request->tipo_documento,
                'estado' => 1
            ]);

            // Crear el directorio si no existe
            $directorio = public_path('uploads/establecimientos/' . $carpeta);
            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Guardar el archivo
            if($tipoArchivo[0] == 'image'){
                // Redimensionar y guardar imagen
                Image::make($request->documento)
                    ->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save(public_path($rutaCompleta), 80);
            } else {
                // Mover archivo PDF
                $request->documento->move($directorio, $nombreArchivo);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Documento guardado correctamente');
        }
        catch (\Exception $e) {
            DB::rollback();
            // \Log::error('Error al guardar documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar el documento: ' . $e->getMessage());
        }
    }
    
    // Muestra un breve resumen de las visitas en la vista principal
    public function resumen(Request $request) {
        $anioActual = $request->input('anio_actual', date('Y'));
        
        // Usar Eloquent para evitar problemas de case-sensitivity
        $visitas = ModVisita::select([
                'visitas.VIS_id',
                'visitas.VIS_tipo', 
                'visitas.VIS_fechas',
                'establecimientos.EST_id',
                'establecimientos.EST_nombre',
                'tipo_establecimientos.TES_tipo'
            ])
            ->join('establecimientos', 'visitas.FK_EST_id', '=', 'establecimientos.EST_id')
            ->join('tipo_establecimientos', 'establecimientos.FK_TES_id', '=', 'tipo_establecimientos.TES_id')
            ->whereYear('visitas.VIS_fechas', $anioActual)
            ->orderBy('tipo_establecimientos.TES_tipo')
            ->orderBy('establecimientos.EST_nombre')
            ->orderBy('visitas.VIS_tipo')
            ->get();

        if ($visitas->isEmpty()) {
            $totalVisitasProcessed = [
                'resultado' => [],
                'total_general' => 0
            ];
        } else {
            // Procesar datos usando Collections de Laravel
            $visitasAgrupadas = $visitas->groupBy(['TES_tipo', 'EST_nombre', 'VIS_tipo']);
            
            $datosParaVista = collect();
            
            foreach ($visitasAgrupadas as $tipoEst => $establecimientos) {
                foreach ($establecimientos as $nombreEst => $tiposVisita) {
                    foreach ($tiposVisita as $tipoVisita => $visitasDelTipo) {
                        $primerVisita = $visitasDelTipo->first();
                        $ultimaVisita = $visitasDelTipo->last();
                        
                        $datosParaVista->push((object)[
                            'TES_tipo' => $tipoEst,
                            'EST_nombre' => $nombreEst,
                            'EST_id' => $primerVisita->EST_id,
                            'VIS_tipo' => $tipoVisita,
                            'total_tipo_visitas' => $visitasDelTipo->count(),
                            'primera_fecha' => $visitasDelTipo->min('VIS_fechas'),
                            'ultima_fecha' => $visitasDelTipo->max('VIS_fechas'),
                            'VIS_fechas' => $visitasDelTipo->min('VIS_fechas'),
                            'total_general' => $visitas->count()
                        ]);
                    }
                }
            }
            
            // Calcular totales por tipo de establecimiento y establecimiento
            $totalesPorTipo = $datosParaVista->groupBy('TES_tipo')
                ->map(function($grupo) {
                    return $grupo->sum('total_tipo_visitas');
                });
                
            $totalesPorEstablecimiento = $datosParaVista->groupBy(['TES_tipo', 'EST_nombre'])
                ->map(function($tipoGrupo) {
                    return $tipoGrupo->map(function($estGrupo) {
                        return $estGrupo->sum('total_tipo_visitas');
                    });
                });
            
            // Agregar totales a cada item
            $datosParaVista = $datosParaVista->map(function($item) use ($totalesPorTipo, $totalesPorEstablecimiento) {
                $item->total_tipo_establecimiento = $totalesPorTipo[$item->TES_tipo] ?? 0;
                $item->total_establecimiento = $totalesPorEstablecimiento[$item->TES_tipo][$item->EST_nombre] ?? 0;
                return $item;
            });
            
            $totalVisitasProcessed = CustomController::agruparPorTipoYNombre($datosParaVista);
        }
        
        return view('visita.visita-resumen', compact('totalVisitasProcessed', 'anioActual'));
    }
    
    /**
     * Mostrar formulario de edición del establecimiento
     */
    public function editarFichaEstablecimiento($id)
    {
        $anioActual = date('Y');
        
        // Obtener información del establecimiento
        $establecimiento = ModEstablecimiento::select(
            'establecimientos.EST_id',
            'establecimientos.EST_nombre',
            'establecimientos.EST_departamento',
            'establecimientos.EST_municipio',
            'establecimientos.EST_direccion',
            'establecimientos.EST_telefono_contacto',
            'establecimientos.EST_capacidad_creacion',
            'establecimientos.EST_anyo_funcionamiento',
            'tipo_establecimientos.TES_tipo'
        )
        ->join('tipo_establecimientos', 'tipo_establecimientos.TES_id', '=', 'establecimientos.FK_TES_id')
        ->where('establecimientos.EST_id', $id)
        ->first();
        
        if (!$establecimiento) {
            return redirect()->back()->with('error', 'Establecimiento no encontrado');
        }
        
        // Obtener información adicional del establecimiento (del año actual o más reciente)
        $establecimientoInfo = ModEstablecimientoInfo::where('FK_EST_id', $id)
            ->where('EINF_gestion', $anioActual)
            ->first();

        // dump($establecimientoInfo);exit;
        // Si no hay info del año actual, buscar la más reciente o crear nueva
        if (!$establecimientoInfo) {
            $establecimientoInfo = ModEstablecimientoInfo::where('FK_EST_id', $id)
                ->orderByDesc('EINF_gestion')
                ->first();
                
            if (!$establecimientoInfo) {
                // Crear objeto vacío para el formulario
                $establecimientoInfo = new \stdClass();
                $establecimientoInfo->EINF_id = null;
                $establecimientoInfo->EINF_poblacion_atendida = null;
                $establecimientoInfo->EINF_cantidad_actual_internos = null;
                $establecimientoInfo->EINF_superficie_terreno = null;
                $establecimientoInfo->EINF_superficie_construida = null;
                $establecimientoInfo->EINF_derecho_propietario = null;
                $establecimientoInfo->EINF_gestion = $anioActual;
            }
        }
        
        // Obtener responsable del establecimiento (del año actual o más reciente)
        $responsable = ModEstablecimientoPersonal::where('FK_EST_id', $id)
            ->where('EPER_gestion', $anioActual)
            ->first();
        
        // Si no hay responsable del año actual, buscar el más reciente o crear nuevo
        if (!$responsable) {
            $responsable = ModEstablecimientoPersonal::where('FK_EST_id', $id)
                ->orderByDesc('EPER_gestion')
                ->first();
                
            if (!$responsable) {
                // Crear objeto vacío para el formulario
                $responsable = new \stdClass();
                $responsable->EPER_id = null;
                $responsable->EPER_nombre_responsable = null;
                $responsable->EPER_grado_profesion = null;
                $responsable->EPER_telefono = null;
                $responsable->EPER_email = null;
                $responsable->EPER_gestion = $anioActual;
            }
        }
        
        return view('visita.editar-ficha-establecimiento', compact('establecimiento', 'establecimientoInfo', 'responsable', 'anioActual'));
    }
    
    /**
     * Actualizar información del establecimiento
     */
    public function actualizarFichaEstablecimiento(Request $request, $id)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'EST_departamento' => 'required|string|max:20',
            'EST_municipio' => 'required|string|max:300',
            'EST_direccion' => 'nullable|string|max:400',
            'EST_telefono_contacto' => 'nullable|string|max:100',
            'EST_capacidad_creacion' => 'nullable|string|max:300',
            'EST_anyo_funcionamiento' => 'nullable|integer|min:1901|max:' . now()->year,
            
            'EINF_poblacion_atendida' => 'nullable|string|max:300',
            'EINF_cantidad_actual_internos' => 'nullable|string|max:300',
            // 'EINF_superficie_terreno' => 'nullable|numeric',
            // 'EINF_superficie_construida' => 'nullable|numeric',

            'EINF_superficie_terreno' => [
                'nullable',
                'required_with:EINF_superficie_construida',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],

            'EINF_superficie_construida' => [
                'nullable',
                'required_with:EINF_superficie_terreno',
                'regex:/^\d+(\.\d{1,2})?$/',
                'lte:EINF_superficie_terreno',
            ],

            
            'EINF_derecho_propietario' => 'nullable|string|max:255',
            'EPER_nombre_responsable' => 'nullable|string|max:200',
            'EPER_grado_profesion' => 'nullable|string|max:150',
            'EPER_telefono' => 'nullable|string|max:50',
            'EPER_email' => 'nullable|email|max:100',
        ], [
            'required' => 'El campo :attribute es requerido.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'numeric' => 'El campo :attribute debe ser un número.',
            'email' => 'El campo :attribute debe ser un email válido.',

            'EST_anyo_funcionamiento.integer' => 'El año de funcionamiento debe ser un número entero.',
            'EST_anyo_funcionamiento.min' => 'El año de funcionamiento debe ser mayor a 1900.',
            'EST_anyo_funcionamiento.max' => 'El año de funcionamiento no puede ser mayor al año actual.',

            'EINF_superficie_terreno.numeric' => 'La superficie del terreno debe ser un valor numérico.',
            'EINF_superficie_terreno.required_with' => 'La superficie del terreno es obligatoria si se proporciona la superficie construida.',
            
            'EINF_superficie_construida.numeric' => 'La superficie construida debe ser un valor numérico.',
            'EINF_superficie_construida.required_with' => 'La superficie construida es obligatoria si se proporciona la superficie del terreno.',
            'EINF_superficie_construida.lte' => 'La superficie construida no puede ser mayor que la superficie del terreno.',
        ]);

        // Si hay errores de validación, devolver respuesta JSON
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $anioActual = date('Y');
            
            // Verificar que el establecimiento existe
            $establecimiento = ModEstablecimiento::find($id);
            if (!$establecimiento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Establecimiento no encontrado'
                ], 404);
            }
            
            // Actualizar tabla establecimientos
            ModEstablecimiento::where('EST_id', $id)->update([
                'EST_departamento' => $request->EST_departamento,
                'EST_municipio' => $request->EST_municipio,
                'EST_direccion' => $request->EST_direccion,
                'EST_telefono_contacto' => $request->EST_telefono_contacto,
                'EST_capacidad_creacion' => $request->EST_capacidad_creacion,
                'EST_anyo_funcionamiento' => $request->EST_anyo_funcionamiento,
            ]);
            
            // Actualizar o crear información del establecimiento para el año actual
            $infoExistente = ModEstablecimientoInfo::where('FK_EST_id', $id)
                ->where('EINF_gestion', $anioActual)
                ->first();
                
            if ($infoExistente) {
                // Actualizar registro existente
                ModEstablecimientoInfo::where('EINF_id', $infoExistente->EINF_id)->update([
                    'EINF_poblacion_atendida' => $request->EINF_poblacion_atendida,
                    'EINF_cantidad_actual_internos' => $request->EINF_cantidad_actual_internos,
                    'EINF_superficie_terreno' => $request->EINF_superficie_terreno,
                    'EINF_superficie_construida' => $request->EINF_superficie_construida,
                    'EINF_derecho_propietario' => $request->EINF_derecho_propietario,
                ]);
            } else {
                // Crear nuevo registro
                ModEstablecimientoInfo::create([
                    'FK_EST_id' => $id,
                    'EINF_poblacion_atendida' => $request->EINF_poblacion_atendida,
                    'EINF_cantidad_actual_internos' => $request->EINF_cantidad_actual_internos,
                    'EINF_superficie_terreno' => $request->EINF_superficie_terreno,
                    'EINF_superficie_construida' => $request->EINF_superficie_construida,
                    'EINF_derecho_propietario' => $request->EINF_derecho_propietario,
                    'EINF_gestion' => $anioActual,
                ]);
            }
            
            // Actualizar o crear responsable del establecimiento para el año actual
            $responsableExistente = ModEstablecimientoPersonal::where('FK_EST_id', $id)
                ->where('EPER_gestion', $anioActual)
                ->first();
                
            if ($responsableExistente) {
                // Actualizar registro existente
                ModEstablecimientoPersonal::where('EPER_id', $responsableExistente->EPER_id)->update([
                    'EPER_nombre_responsable' => $request->EPER_nombre_responsable,
                    'EPER_grado_profesion' => $request->EPER_grado_profesion,
                    'EPER_telefono' => $request->EPER_telefono,
                    'EPER_email' => $request->EPER_email,
                ]);
            } else {
                // Crear nuevo registro solo si hay datos del responsable
                if ($request->EPER_nombre_responsable) {
                    ModEstablecimientoPersonal::create([
                        'FK_EST_id' => $id,
                        'EPER_nombre_responsable' => $request->EPER_nombre_responsable,
                        'EPER_grado_profesion' => $request->EPER_grado_profesion,
                        'EPER_telefono' => $request->EPER_telefono,
                        'EPER_email' => $request->EPER_email,
                        'EPER_gestion' => $anioActual,
                    ]);
                }
            }
            
            DB::commit();
            
            // Si es una petición AJAX, devolver respuesta JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Información actualizada correctamente'
                ]);
            }
            
            // Si es una petición normal, redirigir
            return redirect()->route('visita.historial', $id)->with('success', 'Información actualizada correctamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            // Log del error para debugging
            // \Log::error('Error al actualizar establecimiento: ' . $e->getMessage(), [
            //     'establecimiento_id' => $id,
            //     'request_data' => $request->all(),
            //     'trace' => $e->getTraceAsString()
            // ]);
            
            // Si es una petición AJAX, devolver respuesta JSON de error
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la información: ' . $e->getMessage()
                ], 500);
            }
            
            // Si es una petición normal, redirigir con error
            return redirect()->back()->with('error', 'Error al actualizar la información: ' . $e->getMessage())->withInput();
        }
    }
}