<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = (new CreateNewUser())->create($request->all());
            // Auth::login($user);

            return redirect()->route('users.list')->with('status', 'Usuario registrado con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
