<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    public function apiIniciar(Request $request){
        $credenciales = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
        if( Auth::attempt( $credenciales) ){
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('mnp_token', $token, 60*8);
            // response()->setStatusCode()->json;
            return response(['token' => $token, 'success' => true, 'USER_id' => $user->id], 200)->withCookie($cookie);
        }else{
            return response()->json([
                'mensaje'=> 'ERROR'
            ]);
        }
    }

    public function apiSalir(Request $request){
        $cookie = \Cookie::forget('mnp_token');
        return response()->json(["message"=>"Cierre sesión correcto", 'success'=>true ])->withCookie($cookie);
    }

    /* see uso para la verificacion del token */
    public function apiVer( Request $request ){
         var_dump (\Cookie::get( 'mnp_token' ));
    }


}
