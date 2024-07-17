{{-- categorias/index --}}
@extends('layouts.app')
@section('title', 'Asesoramiento')

@section('content')
<div class="container">
    <h1>Asesoramientos</h1>
    <a href="/asesoramientos/create">Crear nuevo asesoramiento</a>
    <ul>
        @foreach ($asesoramientos as $asesoramiento)
            <li>
                {{ $asesoramiento->ASE_actividad }} -
                <a href="/asesoramientos/{{ $asesoramiento->ASE_id }}">Ver</a> |
                <a href="/asesoramientos/{{ $asesoramiento->ASE_id }}/edit">Editar</a> |
                <form action="/asesoramientos/{{ $asesoramiento->ASE_id }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>

@endsection

