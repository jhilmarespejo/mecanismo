@extends('layouts.app')
@section('title', 'Archivos adjuntos')

@section('content')
<style>
    @media screen and (max-width: 1000px) {
        #adjunto_1 {
            margin: 0px 0px 0px 0px !important;
        }
    }
    .t-shadow{
        text-shadow: 0px 0px 3px #000;
    }
</style>
@if (session('status'))
<div class="alert alert-success alert-dismissible w-50" role="alert">
    <strong><i class="bi bi-exclamation-triangle"></i> </strong>{{ session('status') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- @php
    $id = null; $elemento = []; $img = []; $i=0;
    foreach($adjuntos as $k=>$adjunto){
        if( $id != $adjunto->ADJ_id ){
            array_push($elemento, ['ADJ_id' => $adjunto->ADJ_id, 'FK_FRM_id' => $adjunto->FK_FRM_id, 'ADJ_titulo' => $adjunto->ADJ_titulo, 'ADJ_fecha' => $adjunto->ADJ_fecha, 'ADJ_responsables' => $adjunto->ADJ_responsables, 'ADJ_entrevistados' => $adjunto->ADJ_entrevistados, 'ADJ_resumen' => $adjunto->ADJ_resumen, 'FRM_titulo' => $adjunto->FRM_titulo, 'EST_nombre' => $adjunto->EST_nombre]);

            // $img[$adjunto->ADJ_id][$i] = ['ADJ_id' => $adjunto->ADJ_id,'ARC_id' =>$adjunto->ARC_id, 'ruta' => $adjunto->ARC_ruta];

                // $elemento[$i] = ['ADJ_id' => $adjunto->ADJ_id, 'FK_FRM_id' => $adjunto->FK_FRM_id, 'ADJ_titulo' => $adjunto->ADJ_titulo, 'ADJ_fecha' => $adjunto->ADJ_fecha, 'ADJ_responsables' => $adjunto->ADJ_responsables, 'ADJ_entrevistados' => $adjunto->ADJ_entrevistados, 'ADJ_resumen' => $adjunto->ADJ_resumen ];
                // $i++;
        }
        // else {
            //     // $img[$adjunto->ADJ_id][$i] = ['ADJ_id' => $adjunto->ADJ_id, 'ARC_id' => $adjunto->ARC_id, 'ruta' => $adjunto->ARC_ruta];
            // }
            // if(){}
            // dump($adjunto->ADJ_titulo);
            $id=$adjunto->ADJ_id;
    }
    // dump( $elemento);//exit;
@endphp --}}



    {{-- sub menu --}}
    {{-- {{ Request::fullUrl()  }} --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
          <a class="navbar-brand" href="#"><i class="bi bi-gear"></i></a>
          <button class="navbar-toggler bg-secondary " type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-three-dots"></i>
          </button>
          <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
            <ul class="navbar-nav" id="nav_2">
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/establecimientos/historial/{{ $EST_id }}" >
                        <i class="bi bi-arrow-90deg-left"></i> Historial
                    </a>
                </li>
                {{-- <li class="nav-item p-1 px-3" id="btn_imprimir">
                    <a class="text-decoration-none" href="/cuestionario/imprimir/{{ $FRM_id }}" >
                        <i class="bi bi-printer"></i> Vista para imprimir formulario</span>
                    </a>
                </li> --}}
                {{-- <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/cuestionario/responder/{{ $FRM_id }}" >
                        <i class="bi bi-ui-checks-grid"></i> Responder cuestionario
                    </a>
                </li> --}}
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/recomendaciones/{{ $EST_id }}" >
                        <i class="bi bi-chat-right-dots"></i> Recomendaciones
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="text-center head">
        {{-- <p class="text-primary m-0 p-0" style="font-size: 30px" > {{ $elemento['FRM_titulo'] }} </p> --}}
        <h2 class="text-center py-2 text-primary">Documentos adjuntos</h2>
        <h3 class="text-center py-2 text-primary">{{ $adjuntos->toArray()[0]['EST_nombre'] }}</h3>
        
    </div>
    @include('includes.adjuntos')
@endsection
