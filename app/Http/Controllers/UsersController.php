<?php

namespace App\Http\Controllers;

use App\Models\{ModFormulario, ModFormularioArchivo, ModAdjunto, ModArchivo, ModEstablecimiento};

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

// use Image;
// use Intervention\Image\Facades\Image;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function list(){
        $users = DB::table('users')->select('id', 'username', 'name', 'rol', 'status')->OrderBy('id', 'desc')->get()->toArray();
        // dump($users);
        return view('users.users-list', compact('users'));
    }
    public function destroy($id)
    {
        DB::table('users')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }

    public function changeState(Request $request)  {
        // dump($request->all());
        $id = preg_replace('/[^0-9]/', '', $request->id);
        $status = ($request->status == 'true') ? 1 : 0;
        DB::table('users')->where('id', $id)->update(['status' => $status]);
    }


}
