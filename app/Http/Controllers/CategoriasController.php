<?php

namespace App\Http\Controllers;

use App\Models\{ModCategoria, ModBancoPregunta};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if(Auth::user()->rol == 'Administrador' ){
            DB::enableQueryLog();

            $categorias = ModCategoria::from( 'categorias as c' )
            ->select('c.CAT_id', 'c.CAT_categoria','c.FK_CAT_id', 'c2.CAT_categoria as subcategoria', 'c2.FK_CAT_id as FK_CAT_id2')
            ->leftJoin('categorias as c2', 'c.CAT_id', 'c2.FK_CAT_id')
            ->whereNull('c.FK_CAT_id')
            ->orderBy ('c.CAT_id', 'desc')
            ->orderBy ('FK_CAT_id2', 'desc')
            ->get();

            $quries = DB::getQueryLog();
            // dump( $quries );
        return view( 'categorias.index', compact('categorias') );
        }else{
            return redirect('panel');
        }
    }

    public function guardaNuevaCategoria(Request $request){
        if(Auth::user()->rol == 'Administrador' ){
            // dump($request->except('_token', 'tipo_elemento'));//exit;
        $validator = Validator::make( $request->all(), [
            'tipo_elemento' => 'required',
            'CAT_categoria' => 'required',
            'FK_CAT_id' => 'required_if:tipo_elemento,Subcategoría',
        ], [
            'required' => '¡El dato es requerido!',
            'required_if' => '¡El dato es requerido!',
        ]);

        if ( $validator->fails() ){
            return response()->json( [ 'errors' => $validator->errors() ] );
        } else {
            DB::beginTransaction();
            try {
                ModCategoria::insert($request->except('_token', 'tipo_elemento'));
                DB::commit();
                return response()->json([ "message" => "¡Datos almacenados con exito!" ]);
                // return redirect('/categorias')->with('status', '¡Datos almacenados con exito!');

            }catch (\Exception $e) {
                DB::rollback();
                exit ($e->getMessage());
            }
        }
        }else{
            return redirect('panel');
        }


    }

    public function buscarElementos(Request $request) {
        if(Auth::user()->rol == 'Administrador' ){
            $id = $request->FK_CAT_id;
            $index = $request->index;
            // dump( $index, $id );
            //exit;

            $subCategorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
            ->where('FK_CAT_id', $id)->orderby('CAT_id')->get();

            if( $subCategorias->count() ){
                return view( 'categorias.catategorias-responses', compact('subCategorias', 'id', 'index') );
            }

            if( !$subCategorias->count() || $request->elementos == 'subCategoria' ) {
                DB::enableQueryLog();
                $index = $request->index;

                $listaPreguntas = ModBancoPregunta::select('BCP_id', 'BCP_pregunta', 'BCP_tipoRespuesta', 'BCP_opciones', 'BCP_complemento', 'BCP_aclaracion', 'FK_CAT_id')
                ->where( 'FK_CAT_id', $id )
                ->orderby('BCP_id')
                ->get();

                $quries = DB::getQueryLog();
                return view('categorias.catategorias-responses', compact('listaPreguntas', 'index'));
            }
        }else{
            return redirect('panel');
        }


    }

    public function buscarSubcategoria(Request $request){
        if(Auth::user()->rol == 'Administrador' ){
            $id = $request->CAT_id;
            $index = $request->index;
            $subCategorias = ModCategoria::select('CAT_id', 'CAT_categoria', 'FK_CAT_id')
            ->where('FK_CAT_id', $id)
            ->orderBy('CAT_id', 'asc')
            ->get();
            if(count($subCategorias)){
                return view('categorias.catategorias-responses', compact('subCategorias', 'id', 'index'));
            }
        }else{
            return redirect('panel');
        }
    }

    public function buscarPregunta(Request $request){
        if(Auth::user()->rol == 'Administrador' ){
            //dump($request->except('_token'));
            // exit;

            $index = $request->index;
            $preguntas = ModBancoPregunta::select('BCP_id', 'BCP_pregunta', 'BCP_tipoRespuesta', 'BCP_opciones', 'BCP_complemento', 'BCP_aclaracion', 'FK_CAT_id')
            ->where('FK_CAT_id', $request->FK_CAT_id)
            ->orderby('BCP_id')
            ->get();
            if(count($preguntas)){
                return view('categorias.catategorias-responses', compact('preguntas', 'index'));
            }
        }else{
            return redirect('panel');
        }
    }


}

// CONSULTA PARA VER LA PREGUNTA CON CATEGORIA Y SUB CATEGORIA
// select bp."BCP_id" , bp."BCP_pregunta", bp."BCP_tipoRespuesta", bp."BCP_opciones", bp."BCP_complemento", bp."BCP_aclaracion", bp."FK_CAT_id"
// , c2."CAT_categoria" as "CAT"
// , c."CAT_categoria" as "SUBCAT"
// from banco_preguntas bp
// left join categorias c on bp."FK_CAT_id" = c."CAT_id"
// left join categorias c2 on c."FK_CAT_id"  = c2."CAT_id"
// where "BCP_id" = 567
