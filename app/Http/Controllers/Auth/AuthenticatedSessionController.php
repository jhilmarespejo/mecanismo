<?php

// app/Http/Controllers/Auth/AuthenticatedSessionController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        // dump($request->all());exit;
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->status == 0) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'username' => ['Tu cuenta está inactiva.'],
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended('/panel'); // O la ruta que quieras usar
        }

        throw ValidationException::withMessages([
            'username' => ['Las credenciales no coinciden con nuestros registros.'],
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
