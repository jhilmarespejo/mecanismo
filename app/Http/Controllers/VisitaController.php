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
        'tipo_establecimientos.TES_tipo',
        'establecimientos_info.EINF_poblacion_atendida',
        'establecimientos_info.EINF_cantidad_actual_internos',
        'establecimientos_info.EINF_superficie_terreno',
        'establecimientos_info.EINF_superficie_construida',
        'establecimientos_info.EINF_derecho_propietario',
        'establecimientos_info.EINF_gestion'
    )
    ->join('tipo_establecimientos', 'tipo_establecimientos.TES_id', '=', 'establecimientos.FK_TES_id')
    ->leftJoin('establecimientos_info', function($join) use ($id) {
        $join->on('establecimientos_info.FK_EST_id', '=', 'establecimientos.EST_id');
    })
    ->where('establecimientos.EST_id', $id)
    ->first();
    
    // Crear un objeto stdClass si no hay establecimiento
    if (!$establecimiento) {
        $establecimiento = new \stdClass();
    }
    
    // Establecer explícitamente el año actual para la gestión
    $establecimiento->EINF_gestion = $anioActual;
    
    // Obtener el responsable del establecimiento
    $responsable = ModEstablecimientoPersonal::select(
        'EPER_nombre_responsable',
        'EPER_grado_profesion',
        'EPER_telefono',
        'EPER_email'
    )
    ->where('FK_EST_id', $id)
    ->orderByDesc('EPER_id')
    ->first();
    
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
        // Obtener los datos del establecimiento directamente si no hay visitas
        $estData = ModEstablecimiento::select('establecimientos.EST_id', 'establecimientos.EST_nombre', 'tipo_establecimientos.TES_tipo')
            ->join('tipo_establecimientos', 'tipo_establecimientos.TES_id', '=', 'establecimientos.FK_TES_id')
            ->where('establecimientos.EST_id', $id)
            ->first();
        
        if ($estData) {
            session()->put('EST_id', $estData->EST_id);
            session()->put('EST_nombre', $estData->EST_nombre);
            session()->put('TES_tipo', $estData->TES_tipo);
        }
    }

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
            'tipo_documento.in' => 'Tipo de documento no válido'
        ]);

        DB::beginTransaction();
        try {
            $tipoArchivo = explode("/", $request->documento->getClientMimeType());
            $descripcion = '';
            
            switch ($request->tipo_documento) {
                case 'reglamento':
                    $descripcion = 'Reglamento del centro';
                    $ruta = '/uploads/establecimientos/reglamentos';
                    break;
                case 'licencia':
                    $descripcion = 'Licencia de funcionamiento';
                    $ruta = '/uploads/establecimientos/licencias';
                    break;
                case 'fachada':
                    $descripcion = 'Fotografía de fachada';
                    $ruta = '/uploads/establecimientos/fachadas';
                    break;
            }
            
            // Guardar datos del archivo en la tabla archivos
            ModArchivo::create([
                'ARC_NombreOriginal' => $request->documento->getClientOriginalName(),
                'ARC_ruta' => $request->documento->store($ruta),
                'ARC_extension' => $request->documento->extension(),
                'ARC_tamanio' => $request->documento->getSize(),
                'ARC_descripcion' => $descripcion,
                'ARC_formatoArchivo' => $tipoArchivo[0],
                'FK_EST_id' => $request->EST_id,
                'ARC_origen' => $request->tipo_documento
            ]);

            if($tipoArchivo[0] == 'image'){
                Image::make($request->documento)
                ->resize(null, 600, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path($ruta).'/'.$request->documento->hashName());
            } else {
                $request->documento->move(public_path($ruta), $request->documento->hashName());
            }

            DB::commit();
            return redirect()->back()->with('success', 'Documento guardado correctamente');
        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al guardar el documento: ' . $e->getMessage());
        }
    }

    public function resumen(Request $request) {
        $anioActual = 0;
        if(is_null($request->anio_actual)){
            $anioActual = date('Y');
        } else {
            $anioActual = $request->anio_actual;
        }
        DB::enableQueryLog();

        $totalVisitas = DB::table('tipo_establecimientos as te')
            ->select('te.TES_tipo', 'e.EST_nombre', 'v.VIS_tipo', 'e.EST_id','v.VIS_fechas',
            DB::raw('COUNT(v."VIS_id") AS total_tipo_visitas'),
            DB::raw('SUM(COUNT(v."VIS_id")) OVER(PARTITION BY te."TES_tipo") AS total_tipo_establecimiento'),
            'total_establecimiento.total_establecimiento AS total_establecimiento')
            ->join('establecimientos as e', 'e.FK_TES_id', 'te.TES_id')
            ->join('visitas as v', 'v.FK_EST_id', 'e.EST_id')
            ->join(DB::raw('(SELECT e."EST_nombre",  COUNT(v."VIS_id") AS total_establecimiento
                            FROM establecimientos e
                            JOIN visitas v ON v."FK_EST_id" = e."EST_id"
                            GROUP BY e."EST_nombre") total_establecimiento'),
                    'total_establecimiento.EST_nombre', 'e.EST_nombre')
            ->leftJoin(DB::raw('(SELECT COUNT(v."VIS_id") AS total_general
                    FROM visitas v) total_general'), DB::raw('1'), '=', DB::raw('1'))
            ->whereYear('v.VIS_fechas', $anioActual)
            ->groupBy('te.TES_tipo', 'e.EST_nombre', 'e.EST_id', 'v.VIS_tipo', 'total_establecimiento.total_establecimiento', 'total_general.total_general', 'v.VIS_fechas')
            ->orderBy('te.TES_tipo')
            ->orderBy('e.EST_nombre')
            ->orderBy('e.EST_id')
            ->orderBy('v.VIS_tipo')
            ->addSelect(DB::raw('total_general.total_general AS total_general'))
            ->get();

        $totalVisitas = CustomController::agruparPorTipoYNombre($totalVisitas);
        return view('visita.visita-resumen', compact('totalVisitas', 'anioActual'));
    }
}