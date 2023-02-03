@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')
<style>
    /* div.no-gutters div.card:hover, div.no-gutters span.rounded-circle:hover  {
        background-color: rgb(204, 224, 255);
    } */
    .d-xss-none {
            display: none;
        }
    @media screen and (max-width: 320px) {
        .d-xss-none {
            display:unset;
        }
    }
</style>
{{-- {{ $establecimientos[0] }} --}}
    @if(Session::has('success'))
        <div class="col-3 alert alert-success alert-dismissible notification" role="alert" id="alert">
            <strong>{{Session::get('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(Session::has('warning'))
        <div class="col-3 alert alert-warning alert-dismissible notification" role="alert" id="alert">
            <strong>{{Session::get('warning') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <h2 class="text-center py-2">Historial de visitas</h2>

    @php
        if ( count($recomendaciones) > 0) {
            $incumplido = $recomendaciones->toArray()[0]['incumplido'];
            $cumplido = $recomendaciones->toArray()[0]['cumplido'];
            $parcial = $recomendaciones->toArray()[0]['parcial'];
            $total = $recomendaciones->toArray()[0]['total'];
            $avance = (($cumplido)/$total)*100;
        } else {
            $avance = 0;
            $incumplido = 0;
            $cumplido = 0;
            $parcial = 0;
            $total = 0;
        }
        // dump($avance); //exit;
    @endphp
    <h3 class="text-center">{{ ( $establecimientos[0]->EST_poblacion == 'Privados privadas de libertad')? 'Centro penitenciario '. $establecimientos[0]->EST_nombre : $establecimientos[0]->EST_nombre }}</h3>

    <div class="text-center ">
        {{-- @livewire('formularios-nuevo') --}}
        @include('formulario.formulario-nuevo', ['EST_id' => $establecimientos[0]->EST_id, 'EST_nombre' => $establecimientos[0]->EST_nombre])
    </div>
    @if( isset( $establecimientos[0]->FRM_id ) )
    {{-- @if($avance > 0) --}}
        {{-- CARDS para mostrar los avances de las recomendaciones --}}
        {{-- @if ($avance != 0)
            <div class="row mt-4 px-3">
                <div class="btn col-sm card text-white bg-primary bg-gradient mb-3 mx-1 text-shadow" style="max-width: 18rem;">
                    <div class="card-body row">
                        <div class="col-sm text-center p-0 me-2">
                            Total de recomendaciones
                        </div>
                        <span class="col-sm text-center p-0 rounded-pill box-shadow bg-light text-primary fs-1">{{$total}}</span>
                    </div>
                </div>
                <div class="btn col-sm card text-white bg-success bg-gradient mb-3 mx-1 text-shadow" style="max-width: 18rem;">
                    <div class="card-body row">
                        <div class="col-sm text-center p-0 me-2">
                            Recomendaciones cumplidas
                        </div>
                        <span class="col-sm text-center p-0 rounded-pill box-shadow bg-light text-success fs-1">{{$cumplido}}</span>
                    </div>
                </div>
                <div class="btn col-sm card text-white bg-warning bg-gradient mb-3 mx-1 text-shadow" style="max-width: 18rem;">
                    <div class="card-body row">
                        <div class="col-sm text-center p-0 me-2">
                            Cumplimiento parcial
                        </div>
                        <span class="col-sm text-center p-0 rounded-pill box-shadow bg-light text-warning fs-1">{{$parcial}}</span>
                    </div>
                </div>
                <div class="btn col-sm card text-white bg-danger bg-gradient mb-3 mx-1 text-shadow" style="max-width: 18rem;">
                    <div class="card-body row">
                        <div class="col-sm text-center p-0 me-2">
                            Recomendaciones no cumplidas
                        </div>
                        <span class="col-sm text-center p-0 rounded-pill box-shadow bg-light text-danger fs-1">{{$incumplido}}</span>
                    </div>
                </div>
            </div>
        @endif --}}

        {{-- BARRA para mostrar el % de avance --}}
        {{-- <div class="row mt-1">

        </div> --}}
        <div class="row">
            <div class="col mt-1">
                <div class="card text-white bg-danger ">
                    <a href="/recomendaciones/{{$establecimientos[0]->EST_id}}" class="text-decoration-none text-light">
                        <div class="card-body">
                            <h5 class="card-title text-center">Recomendaciones </h5>
                            <div class="progress " style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated  " role="progressbar" style="width: {{$avance}}%;" aria-valuenow="{{$avance}}" aria-valuemin="0" aria-valuemax="100"><span class="fs-6 text-shadow"><strong>{{ round($avance, 1) }}%</strong></span></div>
                            </div>
                            <ul class="lh-1">
                                <li> <b class="fs-4">{{$total}}</b> Recomendaciones en total </li>
                                <li> <b class="fs-4">{{$cumplido}}</b> Recomendacion/es cumplidas</li>
                                <li> <b class="fs-4">{{$parcial}}</b> Cumplimiento parcial</li>
                                <li> <b class="fs-4">{{$incumplido}}</b> Recomendaciones no cumplidas</li>
                            </ul>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col mt-1">
                <div class="card text-white bg-warning mb-3">
                    <a href="/formulario/adjuntos/{{$establecimientos[0]->EST_id}}" class="text-decoration-none text-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Archivos adjuntos</h5>
                            <img src="/img/adjuntos.png" class="img-fluid rounded" alt="Archivos adjuntos">
                        </div>
                    </a>
                </div>
            </div>
        </div>



        <div class="container py-2 mt-4 mb-4">
            @foreach ($establecimientos as $key=>$establecimiento)
            <!-- START timeline item 1 TARJETAS PARA MOSTRAR LOS FORMULARIOS APLICADOS-->
                <div class="row no-gutters">
                    <div class="col align-self-center text-end">
                        <!--spacer-->
                        <span class="text-end alert alert-primary">
                            {{ $establecimiento->FRM_fecha }}
                        </span>
                    </div>
                    <!-- timeline item 1 center dot -->
                    <div class="col-sm-1 text-center flex-column d-none d-sm-flex bar" >
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>

                        <h2 class="m-2">
                            <span class="badge rounded-circle border border-4 border-primary text-primary text-shadow" >{{ count($establecimientos)-$key }}</span>
                        </h2>
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-sm-7 py-2">
                        <div class="card">
                            <div class="card-body">
                                {{-- <div class="float-end text-muted small">{{$establecimiento->FRM_fecha }}</div> --}}
                                <h4 class="card-title">
                                    <b class="d-xss-none">{{ count($establecimientos)-$key }}. </b>
                                    {{$establecimiento->FRM_titulo}}
                                </h4>
                                <p class="card-text">
                                    <ul class="list-group">
                                        <li class="list-group-item border-0"><a class="text-decoration-none" href="/cuestionario/imprimir/{{$establecimiento->FRM_id}}"><i class="bi bi-printer"></i> Imprimir cuestionario</a></li>

                                        <li class="list-group-item border-0"><a class="text-decoration-none" href="/cuestionario/responder/{{$establecimiento->FRM_id}}"><i class="bi bi-clipboard-check"></i> Responder/llenar cuestionario</a></li>

                                        {{-- <li class="list-group-item border-0">
                                            <a class="text-decoration-none" href="/formulario/adjuntos/{{$establecimientos[0]->EST_id}}/{{$establecimiento->FRM_id}}" id="{{$establecimiento->FRM_id}}"><i class="bi bi-clipboard-check"></i> Archivos adjuntos</a>
                                        </li> --}}

                                        <li class="list-group-item border-0">
                                            <a class="text-decoration-none" id="{{$establecimiento->FRM_id}}"><i class="bi bi-journal-bookmark-fill"></i> {{ $establecimiento->FRM_tipoVisita }}</a>
                                        </li>

                                    </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- END timeline item 1 -->
            @endforeach
        </div>
    @endif

    @if( !isset($establecimiento->FRM_id) )
        <div class="alert alert-warning mx-5 mt-2 text-center" role="alert" data-bs-toggle="modal" data-bs-target="#nuevoFormulario">
            Aún no se aplicaron formularios a este establecimiento
        </div>
    @endif

    @if( count($recomendaciones) == 0 )
        <div class="alert alert-danger mx-5 mt-2 text-center" role="alert">
            Aún no se asignaron recomendaciones a este establecimiento
        </div>
    @endif

    {{-- <div class="text-center py-2">
        @livewire('formularios-nuevo')
    </div> --}}
    {{-- @endif --}}

<div class="container"></div>

<script type="text/javascript">
    $("#alert").fadeOut(4500);
</script>
@endsection
