<?php

namespace App\Http\Controllers\Api;
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
            $cookie = cookie('cookie_token', $token, 60*24);
            return response(['token' => $token])->withCookie($cookie);
        }else{
            return response()->json([
                'mensaje'=> 'ERROR '
            ]);
        }

    }

    public function apiSalir(Request $request){
        return response()->json([
            'mensaje'=> 'SALIDA'
        ]);
    }


}
