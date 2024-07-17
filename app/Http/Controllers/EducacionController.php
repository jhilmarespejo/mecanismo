<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EducacionController extends Controller
{
    public function index()
    {


        return view('educacion.index');
    }

    public function create()
    {
        $mandatos = DB::table('mandatos')->get();
        return view('asesoramientos.create', ['mandatos' => $mandatos]);
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {
    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}

