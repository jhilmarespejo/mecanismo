<?php

namespace App\Http\Controllers;

use App\Models\{User};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;

class AccesoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function acceso(){
        return view('acceso.acceso');
    }

    public function iniciar( Request $request ){
        $credenciales =[
            "username" => $request->username,
            "password" => $request->password,
        ];
        $recordar = ( ($request->remember) ? true: false );
        // dump( $recordar  );exit;

        if( Auth::attempt( $credenciales, $recordar) ){
            $request->session()->regenerate();
            return redirect()->intended('/panel');
        }else{
            return redirect('/acceso');
        }
    }

    public function finalizar(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/acceso');
    }



}
