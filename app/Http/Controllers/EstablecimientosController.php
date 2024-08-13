<?php

namespace App\Http\Controllers;

use App\Models\{ModEstablecimiento, ModTipoEstablecimiento,ModFormulario, ModRecomendacion, ModVisita, ModEstablecimientoInfo,ModEstablecimientoPersonal};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// use Carbon\Carbon;

class EstablecimientosController extends Controller
{

    // Retorna los tipos de establecimientos, cuando se presiona el boton "Nuevo establecimiento"
    public function tipo( ){
        $tipos_establecimiento = ModTipoEstablecimiento::select('TES_id', 'TES_tipo' )->get();
        return response()->json($tipos_establecimiento);
    }

    /**
     * guardarNuevoEstablecimiento
     *
     * @param  mixed $request
     * @return void
     * Guarda datos de nuevo establecimiento
     */
    public function guardarNuevoEstablecimiento(Request $request)  {
        // dump($request->except('_token'));//exit;
        $validator = Validator::make( $request->all(), [
            'EST_nombre' => 'required',
            'FK_TES_id' => 'required',
            'EST_departamento' => 'required',
            'EST_municipio' => 'required',
        ], [
            'required' => '¡El dato es requerido!',
            'required_if' => '¡El dato es requerido!',
        ]);

        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            DB::beginTransaction();
            try {
                ModEstablecimiento::insert($request->except('_token'));
                //GUARDAR DATOS
                DB::commit();
                return response()->json([ "message" => "¡Datos almacenados con exito!" ]);
                // return redirect('/categorias')->with('status', '¡Datos almacenados con exito!');

            }catch (\Exception $e) {
                DB::rollback();
                exit ($e->getMessage());
            }
        }
    }

    // Listar los establecimiento por tipo, segun FK_TES_id de la tabla establecimientos, escogido en el mapa

    public function listarSegunTipo(Request $request){
        $TES_id = $request->TES_id;
        $TES_tipo = $request->TES_tipo;
        $EST_departamento = $request->EST_departamento;

        DB::enableQueryLog();
        $establecimientos = ModEstablecimiento::from('establecimientos as e')
            ->select('e.EST_nombre','e.EST_id','e.EST_direccion','e.EST_municipio','e.EST_departamento','e.FK_TES_id',
                DB::raw('COUNT(v."VIS_id") AS cantidad_visitas')
            )
            ->leftJoin('visitas as v', 'v.FK_EST_id', 'e.EST_id')
            ->when($request->EST_nombre, function ($query) use ($request) {
                return $query->where('e.EST_nombre', 'ilike', '%' . $request->EST_nombre . '%');
            })
            ->when(!$request->EST_nombre, function ($query) use ($TES_id, $EST_departamento) {
                return $query->where('FK_TES_id', $TES_id)
                            ->where('EST_departamento', 'ilike', '%' . $EST_departamento . '%');
            })
            ->groupBy('e.EST_nombre', 'e.EST_id', 'e.EST_direccion', 'e.EST_municipio', 'e.EST_departamento', 'e.FK_TES_id')
            ->orderBy('EST_nombre')
            ->get();

            // $quries = DB::getQueryLog();
            // dump ($quries);

        return view('establecimientos.establecimientos-por-tipo', compact('establecimientos', 'TES_id', 'TES_tipo', 'EST_departamento'));
        // dump($establecimientos);exit;
    }

    public function index(Request $request){

        // 1. Distribución de Establecimientos por Tipo
        $distribucionPorTipo = ModEstablecimiento::select('tipo_establecimientos.TES_tipo', DB::raw('count("establecimientos"."EST_id") as total'))
        ->join('tipo_establecimientos', 'establecimientos.FK_TES_id', 'tipo_establecimientos.TES_id')
        ->groupBy('tipo_establecimientos.TES_tipo')
        ->orderBy('total', 'desc')
        ->get();
        $estabsPorTipo = [];
        foreach ($distribucionPorTipo as $item) {
            $estabsPorTipo[] = [
                'name' => $item->TES_tipo,
                'y' => $item->total
            ];
        }
        
        
        // 2. Establecimientos por Departamento
        $establecimientosPorDepartamento = ModEstablecimiento::select('EST_departamento', DB::raw('count("EST_id") as total'))
        ->groupBy('EST_departamento')
        ->orderBy('total', 'desc')
        ->get();

        $estabsPorDepartamento = [];
        foreach ($establecimientosPorDepartamento as $item) {
            $estabsPorDepartamento[] = [
                'name' => $item->EST_departamento,
                'y' => $item->total
            ];
        }
        // Agregar el total general como una serie adicional
        $totalGeneral = $establecimientosPorDepartamento->sum('total');
        // $estabsPorDepartamento[] = [
        //     'name' => 'Total General',
        //     'y' => $totalGeneral,
        //     'color' => '#FF6F61', // Un color destacado para el total general
        //     'dataLabels' => ['enabled' => false] // Opcional: Ocultar las etiquetas de datos para el total general
        // ];
        
        $tipo_establecimientos = ModTipoEstablecimiento::select('TES_id', 'TES_tipo')->get();

        DB::enableQueryLog();

        $TES_id = $request->TES_id;
        $establecimientos = ModEstablecimiento::join('tipo_establecimientos as te', 'e.FK_TES_id', 'te.TES_id')
        ->when($request->TES_id, function ($query, $TES_id) {
            return $query->where('e.FK_TES_id', $TES_id);
        })
        ->from('establecimientos as e')
        ->select('e.EST_id','e.FK_TES_id','e.EST_nombre','e.EST_departamento','e.EST_municipio','e.EST_direccion','e.EST_telefono_contacto', 'te.TES_tipo')
        ->orderBy('e.EST_id', 'DESC')->get()->toArray();

        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Lugares de detención', 'url' => ''],
        ];

        return view('establecimientos.establecimientos-index', compact('tipo_establecimientos', 'establecimientos','TES_id','estabsPorDepartamento', 'estabsPorTipo', 'totalGeneral', 'breadcrumbs'));
    }
    public function mostrar($id) {
        

        $establecimiento = ModEstablecimiento::select('EST_nombre', 'EST_departamento', 'EST_municipio', 'EST_direccion', 'EST_telefono_contacto', 'EST_anyo_funcionamiento', 'EST_capacidad_creacion')->findOrFail($id);

        $info = ModEstablecimientoInfo::select('EINF_cantidad_policias_varones','EINF_cantidad_policias_mujeres','EINF_cantidad_celdas_varones','EINF_cantidad_celdas_mujeres','EINF_normativa_interna','EINF_formato_registro_aprehendidos','EINF_cantidad_actual_internos','EINF_poblacion_atendida','EINF_rangos_edad_poblacion','EINF_tipo_entidad','EINF_tipo_administracion','EINF_banyo_ppl','EINF_telefono_ppl','EINF_camaras_vigilancia','EINF_ambientes_visita','EINF_informacion_ddhh','EINF_observaciones','EINF_gestion')
        ->where('FK_EST_id', $id)
        ->where('EINF_gestion', date('Y'))->first();

        $personal = ModEstablecimientoPersonal::select('EPER_nombre_responsable','EPER_grado_profesion','EPER_fecha_incorporacion','EPER_experiencia','EPER_telefono','EPER_email','EPER_gestion')
        ->where('FK_EST_id', $id)
        ->where('EPER_gestion', date('Y'))->get();

        return response()->json(compact('establecimiento', 'info', 'personal'));
    }
    public function crear()
    {
        $tipos = ModTipoEstablecimiento::all(); // Obtén los tipos de establecimiento
        return view('establecimientos.establecimientos-crear', compact('tipos'));
    }

    public function almacenar(Request $request){
        // dump( date('Y') );exit;
        $validated = $request->validate([

            'establecimiento.FK_TES_id' => 'required',
            'establecimiento.EST_nombre' => 'required|string|max:300|min:4',
            'establecimiento.EST_departamento' => 'required|string|max:20|min:4',
            'establecimiento.EST_municipio' => 'required|string|max:300|min:4',
            'establecimiento.EST_direccion' => 'required|string|max:400|min:4',
            'establecimiento.EST_telefono_contacto' => 'nullable|string|max:100|min:4',
            'establecimiento.EST_anyo_funcionamiento' => 'nullable|string|max:100|min:2',
            'establecimiento.EST_capacidad_creacion' => 'nullable|string|max:300|min:1',

            // Validación para información adicional
            'info.EINF_cantidad_policias_varones' => 'nullable|string|max:100',
            'info.EINF_cantidad_policias_mujeres' => 'nullable|string|max:100',
            'info.EINF_cantidad_celdas_varones' => 'nullable|string|max:100',
            'info.EINF_cantidad_celdas_mujeres' => 'nullable|string|max:100',
            'info.EINF_normativa_interna' => 'nullable|string|max:500',
            'info.EINF_formato_registro_aprehendidos' => 'nullable|string|max:100',
            'info.EINF_cantidad_actual_internos' => 'nullable|string|max:300',
            'info.EINF_poblacion_atendida' => 'nullable|string|max:300',
            'info.EINF_rangos_edad_poblacion' => 'nullable|string|max:150',
            'info.EINF_tipo_entidad' => 'nullable|string|max:50',
            'info.EINF_tipo_administracion' => 'nullable|string|max:200',
            'info.EINF_banyo_ppl' => 'nullable|string|max:50',
            'info.EINF_telefono_ppl' => 'nullable|string|max:50',
            'info.EINF_camaras_vigilancia' => 'nullable|string|max:50',
            'info.EINF_ambientes_visita' => 'nullable|string|max:50',
            'info.EINF_informacion_ddhh' => 'nullable|string|max:50',
            'info.EINF_observaciones' => 'nullable|string|max:450',
            'info.EINF_gestion' => 'nullable|string|max:5|min:2',
            // Agregar validación para otros campos según sea necesario

            // Validación para personal
            'personal.EPER_nombre_responsable' => 'nullable|string|max:200|min:2',
            'personal.EPER_grado_profesion' => 'nullable|string|max:150|min:2',
            'personal.EPER_fecha_incorporacion' => 'nullable|string|max:70|min:2',
            'personal.EPER_experiencia' => 'nullable|string|max:70|min:2',
            'personal.EPER_telefono' => 'nullable|string|max:50|min:2',
            'personal.EPER_email' => 'nullable|email|max:100|min:2',
            'personal.EPER_gestion' => 'nullable|string|max:5|min:2',

        ], [
            'required' => 'El dato es necesario!',
            'max' => 'Dato muy extendido!',
            'min' => 'Dato reducido!',
            'email' => 'Ingrese un email válido',
        ]);

        DB::beginTransaction();
        try {
            // Guardar el establecimiento
            $establecimientoData = $validated['establecimiento'];

            $establecimiento = ModEstablecimiento::create($establecimientoData);

            // Guardar la información adicional
            if (isset($validated['info'])) {
                $infoData = $validated['info'];
                $infoData['FK_EST_id'] = $establecimiento->EST_id; // Asociar con el establecimiento
                $infoData['EINF_gestion'] = date('Y'); // Asociar con el establecimiento
                ModEstablecimientoInfo::create($infoData);
            }

            // Guardar el personal
            if (isset($validated['personal'])) {
                $personalData = $validated['personal'];
                $personalData['FK_EST_id'] = $establecimiento->EST_id; // Asociar con el establecimiento
                $personalData['EPER_gestion'] = date('Y'); // Asociar con el establecimiento
                ModEstablecimientoPersonal::create($personalData);
            }

            DB::commit();

            return redirect()->route('establecimientos.index')->with('success', 'Establecimiento guardado exitosamente!');
        } catch (\Exception $e) {
            DB::rollBack();
            // dump( $e );//exit;

            // return redirect()->back()->withErrors(['error' => 'Ocurrió un error al guardar el establecimiento.'])->withInput();
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al guardar el establecimiento.'])->withInput();
        }
    }


    public function infoMostrar( Request $request, $EST_id){
        DB::enableQueryLog();
        $gestion = $request->query('gestion', date('Y'));

        $infoAdicional = ModTipoEstablecimiento::from('tipo_establecimientos as te')->select('e.EST_nombre','e.EST_id', 'te.TES_tipo','ei.FK_EST_id','ei.EINF_cantidad_policias_varones','ei.EINF_cantidad_policias_mujeres','ei.EINF_cantidad_celdas_varones','ei.EINF_cantidad_celdas_mujeres','ei.EINF_normativa_interna','ei.EINF_formato_registro_aprehendidos','ei.EINF_cantidad_actual_internos','ei.EINF_poblacion_atendida','ei.EINF_rangos_edad_poblacion','ei.EINF_tipo_entidad','ei.EINF_tipo_administracion','ei.EINF_banyo_ppl','ei.EINF_telefono_ppl','ei.EINF_camaras_vigilancia','ei.EINF_ambientes_visita','ei.EINF_informacion_ddhh','ei.EINF_observaciones','ei.EINF_gestion')
        ->leftJoin('establecimientos as e', 'e.FK_TES_id', '=', 'te.TES_id')
        ->leftJoin('establecimientos_info as ei', function ($join) use ($gestion) {
            $join->on('ei.FK_EST_id', '=', 'e.EST_id')
                 ->where('ei.EINF_gestion', '=', $gestion);
        })
        ->where('e.EST_id', $EST_id)
        ->first();

        $quries = DB::getQueryLog();
            // dump ($gestion);//exit;

        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Lugares de detención', 'url' => route('establecimientos.index')],
            ['name' => 'Información adicional', 'url' => '']
        ];
        return view('establecimientos.establecimientos-info-mostrar', compact('infoAdicional','gestion', 'breadcrumbs'));

    }

    public function infoActualizar(Request $request) {
        $data = $request->validate([
            // Validación para información adicional
            'info.FK_EST_id' => 'required|exists:establecimientos,EST_id',
            'info.EINF_cantidad_policias_varones' => 'nullable|string|max:100',
            'info.EINF_cantidad_policias_mujeres' => 'nullable|string|max:100',
            'info.EINF_cantidad_celdas_varones' => 'nullable|string|max:100',
            'info.EINF_cantidad_celdas_mujeres' => 'nullable|string|max:100',
            'info.EINF_normativa_interna' => 'nullable|string|max:500',
            'info.EINF_formato_registro_aprehendidos' => 'nullable|string|max:100',
            'info.EINF_cantidad_actual_internos' => 'nullable|string|max:300',
            'info.EINF_poblacion_atendida' => 'nullable|string|max:300',
            'info.EINF_rangos_edad_poblacion' => 'nullable|string|max:150',
            'info.EINF_tipo_entidad' => 'nullable|string|max:50',
            'info.EINF_tipo_administracion' => 'nullable|string|max:200',
            'info.EINF_banyo_ppl' => 'nullable|string|max:50',
            'info.EINF_telefono_ppl' => 'nullable|string|max:50',
            'info.EINF_camaras_vigilancia' => 'nullable|string|max:50',
            'info.EINF_ambientes_visita' => 'nullable|string|max:50',
            'info.EINF_informacion_ddhh' => 'nullable|string|max:50',
            'info.EINF_observaciones' => 'nullable|string|max:450',
            'info.EINF_gestion' => 'required|string|max:5|min:2',
            // Agregar validación para otros campos según sea necesario
        ], [
            'required' => 'El dato es necesario!',
            'max' => 'Dato muy extendido!',
            'min' => 'Dato reducido!',
            'email' => 'Ingrese un email válido',
        ]);
        // dump( $data['info']);exit;
        DB::beginTransaction();
        try {
            $info = ModEstablecimientoInfo::updateOrCreate(
                [
                    'FK_EST_id' => $data['info']['FK_EST_id'],
                    'EINF_gestion' => $data['info']['EINF_gestion']
                ],
                $data['info']
            );
            DB::commit();

            return response()->json(['message' => 'Información actualizada correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            // return response()->json(['message' => 'Hubo un problema al actualizar la información.'], 500);
            dd($e);
            return response()->json(['message' => 'Hubo un problema al actualizar la información.'], 500);
        }


        // $data = $request->info;
        // $gestion = $data['info']['EINF_gestion'];

        // $info = ModEstablecimientoInfo::where('FK_EST_id', $data['FK_EST_id'])
        // ->where('EINF_gestion', $gestion)
        // ->first();

        // if ($info) {
        //     $info->update($data);
        //     return response()->json(['success' => true]);
        // } else {
        //     return response()->json(['success' => false], 404);
        // }



    }


    public function personalMostrar( Request $request, $EST_id){
        DB::enableQueryLog();
        $gestion = $request->query('gestion', date('Y'));

        $infoPersonal = ModTipoEstablecimiento::from('tipo_establecimientos as te')->select('e.EST_nombre','e.EST_id', 'te.TES_tipo','ep.EPER_nombre_responsable','ep.EPER_grado_profesion','ep.EPER_fecha_incorporacion','ep.EPER_experiencia','ep.EPER_telefono','ep.EPER_email','ep.EPER_gestion')
        ->leftJoin('establecimientos as e', 'e.FK_TES_id', '=', 'te.TES_id')
        ->leftJoin('establecimientos_personal as ep', function ($join) use ($gestion) {
            $join->on('ep.FK_EST_id', '=', 'e.EST_id')
                 ->where('ep.EPER_gestion', '=', $gestion);
        })
        ->where('e.EST_id', $EST_id)
        ->first();

        $quries = DB::getQueryLog();
            // dump ($infoPersonal->toArray());exit;

        $breadcrumbs = [
            ['name' => 'Inicio', 'url' => route('panel')],
            ['name' => 'Lugares de detención', 'url' => route('establecimientos.index')],
            ['name' => 'Información sobre el personal', 'url' => '']
        ];
        return view('establecimientos.establecimientos-personal-mostrar', compact('infoPersonal','gestion', 'breadcrumbs'));

    }

    public function personalActualizar(Request $request){
        $data = $request->validate([
            'personal.FK_EST_id' => 'required|exists:establecimientos,EST_id',
            'personal.EPER_nombre_responsable' => 'required|string|max:255',
            'personal.EPER_grado_profesion' => 'nullable|string|max:255',
            'personal.EPER_fecha_incorporacion' => 'nullable|string',
            'personal.EPER_experiencia' => 'nullable|string|max:255',
            'personal.EPER_telefono' => 'nullable|string|max:255',
            'personal.EPER_email' => 'nullable|email|max:255',
            'personal.EPER_gestion' => 'required|string|min:2|max:4',
        ], [
            'required' => 'El dato es necesario!',
            'max' => 'Dato muy extendido!',
            'min' => 'Dato reducido!',
            'email' => 'Ingrese un email válido',
        ]);

        DB::beginTransaction();
        try {
            $personal = ModEstablecimientoPersonal::updateOrCreate(
                [
                    'FK_EST_id' => $data['personal']['FK_EST_id'],
                    'EPER_gestion' => $data['personal']['EPER_gestion']
                ],
                $data['personal']
            );
            DB::commit();

            return response()->json(['message' => 'Información actualizada correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Hubo un problema al actualizar la información.'], 500);
        }

    }




    // public function personalActualizar(Request $request) {
    //     $data = $request->personal;
    //     $gestion = $data['EPER_gestion'];


    //     DB::enableQueryLog();

    //     $info = ModEstablecimientoPersonal::where('FK_EST_id', $data['FK_EST_id'])
    //     ->where('EPER_gestion', $gestion)
    //     ->first();
    //     $quries = DB::getQueryLog();
    //     // dump($info );exit;

    //     if ($info) {
    //         $info->update($data);
    //         return response()->json(['success' => true]);
    //     } else {
    //         return response()->json(['success' => false], 404);
    //     }
    // }
}