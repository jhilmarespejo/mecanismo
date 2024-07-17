{{-- categorias/index --}}
@extends('layouts.app')
@section('title', 'Asesoramiento')

@section('content')
<div class="container">
       <h1>Ver Asesoramiento</h1>
    <p>Actividad: {{ $asesoramiento->ASE_actividad }}</p>
    <p>Fecha de Actividad: {{ $asesoramiento->ASE_fecha_actividad }}</p>
    <p>Recomendación: {{ $asesoramiento->ASE_recomendacion }}</p>
    <p>Mandato ID: {{ $asesoramiento->FK_MAN_id }}</p>
    <a href="/asesoramientos">Volver</a>
</div>

@endsection

