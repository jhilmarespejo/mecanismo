<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(
        private ApiAuthService $authService
    ) {}

    public function showLoginForm()
    {
        return view('auth.login'); // Mantén tu vista actual con Bootstrap 5
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($this->authService->authenticate(
            $request->username,
            $request->password
        )) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Credenciales incorrectas',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}



// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Services\ApiAuthService;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class LoginController extends Controller
// {
//     public function __construct(
//         private ApiAuthService $authService
//     ) {}
    
//     public function showLoginForm()
//     {
//         return view('auth.login'); // Mantén tu vista actual con Bootstrap 5
//     }
    
//     public function login(Request $request)
//     {
//         $request->validate([
//             'username' => 'required|string',
//             'password' => 'required|string'
//         ]);
        
//         if ($this->authService->authenticate(
//             $request->username,
//             $request->password
//         )) {
//             return redirect()->intended('/panel');
//         }
        
//         return back()->withErrors([
//             'username' => 'Credenciales incorrectas',
//         ]);
//     }
    
//     public function logout(Request $request)
//     {
//         Auth::logout();
//         $request->session()->invalidate();
//         return redirect('/');
//     }
// }
