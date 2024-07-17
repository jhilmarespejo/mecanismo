@extends('layouts.app')
@section('title', 'Formulario anterior')

@section('content')
<style> </style>

@php
    // dump(Session::all());
@endphp

FORMULARIO ANTERIOR
@if (session('status'))
<div class="alert alert-success alert-dismissible w-50" role="alert">
    formulario anterior
    <strong><i class="bi bi-exclamation-triangle"></i> </strong>{{ session('status') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@dump($elementos_formulario)

    {{-- sub menu --}}
    {{-- @mobile
    <div class="container-fluid row border-top border-bottom p-3">
        <div class="col">
            <a class="text-decoration-none" href="/establecimientos/historial/{{ $EST_id }}" >
                <i class="bi bi-arrow-90deg-left"></i> Historial
            </a>
        </div>
        <div class="col">
            <a class="text-decoration-none" href="/recomendaciones/{{ $EST_id }}" >
                <i class="bi bi-chat-right-dots"></i> Recomendaciones
            </a>
        </div>
    </div>
    @endmobile
    @desktop
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
          <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
            <ul class="navbar-nav" id="nav_2">
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/establecimientos/historial/{{ $EST_id }}" >
                        <i class="bi bi-arrow-90deg-left"></i> Historial
                    </a>
                </li> --}}
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
                {{-- <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/recomendaciones/{{ $EST_id }}" >
                        <i class="bi bi-chat-right-dots"></i> Recomendaciones
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    @enddesktop --}}



@endsection
