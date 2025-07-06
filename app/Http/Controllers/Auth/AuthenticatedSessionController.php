<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    protected $authService;

    public function __construct(ApiAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function create()
    {
        return view('auth.login');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        
        if ($this->authService->authenticate($request->username, $request->password)) {
            $request->session()->regenerate();
            return redirect()->intended('/panel');
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