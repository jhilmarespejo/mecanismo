{{-- LISTA de los formularios aplicados a la visita seleccionada --}}


@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')
{{-- minimenu --}}
    @mobile
    <div class="container-fluid row border-top border-bottom p-3">
        <div class="col ">
            {{-- MEJORARE EL ENLACE --}}
            <a href="javascript:history.back()" role="button" class="text-decoration-none"> <i class="bi bi-arrow-90deg-left"></i></a>
        </div>
    </div>
    @endmobile

    @desktop
    <nav class="navbar navbar-expand-lg navbar-light bg-light" id="nav2">
        <div class="container-fluid">
        <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
            <ul class="navbar-nav" id="nav_2">
                <li class="nav-item p-1 px-3">
                    {{-- MEJORARE EL ENLACE --}}
                    <a href="javascript:history.back()" role="button" class="text-decoration-none"> <i class="bi bi-arrow-90deg-left"></i> Volver atrás</a>
                </li>

            </ul>
        </div>
        </div>
    </nav>
    @endmobile


<div class="container p-2">
    <div class="card text-dark bg-light " >
        <div class="card-header fs-5">Formularios aplicados en esta visita</div>
        <div class="card-body">
            <dl class="">
                @if ( count($formularios) > 0 )
                    @foreach ($formularios as $key=>$formulario)
                            <dt class="mt-3 ">
                                <span class="badge bg-primary rounded-pill text-shadow p-1">
                                    {{ count($formularios)-$key }}
                                </span>
                                <span class="badge bg-primary rounded-pill text-shadow p-1">
                                    {{ \Carbon\Carbon::parse($formulario->FRM_fecha)->format('d-m-Y') }}
                                </span>
                                <a href="/cuestionario/ver/{{$formulario->FRM_id}}"><i class="bi bi-eye-fill px-2 text-success fs-5"></i></a>
                                <a href="/cuestionario/imprimir/{{$formulario->FRM_id}}"><i class="bi bi-printer-fill px-2 text-success fs-5"></i></a>
                                <a href="/cuestionario/responder/{{$formulario->FRM_id}}"><i class="bi bi-pen-fill px-2 text-success fs-5"></i></a>
                                <a href="/cuestionario/duplicar/{{$formulario->FRM_id}}/{{$formulario->FK_VIS_id}}"><i class="bi bi-clipboard-plus-fill px-2 text-success fs-5"></i></i></a>

                            </dt>
                            <dd class="ps-3 border-bottom mb-1 table-hover">{{$formulario->FRM_titulo}} ({{$formulario->FRM_version}})</dd>
                    @endforeach
                @else

                @endif
            </dl>
        </div>
    </div>
</div>

@endsection





{{-- <h3 class="text-center">{{ ( $formularios[0]->EST_poblacion == 'Privados privadas de libertad')? 'Centro penitenciario '. $formularios[0]->EST_nombre : $formularios[0]->EST_nombre }}</h3> --}}

{{-- Entrevista al jefe de seguridad
Entrevista a los PPL
Verificacion --}}
