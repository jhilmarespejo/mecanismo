
@extends('layouts.app')
@section('title', 'Cuestionario')


@section('content')

<style>
    div.no-gutters div.card:hover, div.no-gutters span.rounded-circle:hover  {
        background-color: rgb(204, 224, 255);
    }
</style>
{{-- {{ $establecimientos[0] }} --}}
@if(Session::has('success'))
        <div class="col-3 alert alert-success alert-dismissible notification" role="alert">
            <strong>{{Session::get('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(Session::has('warning'))
        <div class="col-3 alert alert-warning alert-dismissible notification" role="alert">
            <strong>{{Session::get('warning') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
<h2 class="text-center">Historial</h2>

@if($establecimientos)
@endif



    <h3 class="text-center">{{ ( $establecimientos[0]->EST_poblacion == 'Privados privadas de libertad')? 'Centro penitenciario '. $establecimientos[0]->EST_nombre : $establecimientos[0]->EST_nombre }}</h3>

    <div class="text-center ">
        @livewire('formularios-nuevo')
    </div>

    <div class="container py-2 mt-4 mb-4">
        @foreach ($establecimientos as $key=>$establecimiento)
        {{-- <p><a href="">{{$establecimiento->FRM_titulo}} ({{$establecimiento->FRM_fecha }})</a></p> --}}
            @if ( $key%2 == 0 )
                <!-- START timeline item 1 -->
                <div class="row no-gutters">
                    <div class="col-sm"> <!--spacer--> </div>
                    <!-- timeline item 1 center dot -->
                    <div class="col-sm-1 text-center flex-column d-none d-sm-flex bar" >
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                        <h5 class="m-2">
                            <span class="badge rounded-circle bg-light border border-4 border-primary">&nbsp;</span>
                        </h5>
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-sm py-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-end text-muted small">{{$establecimiento->FRM_fecha }}</div>
                                <h4 class="card-title">{{$establecimiento->FRM_titulo}}</h4>
                                <p class="card-text">
                                    <ul class="list-group">
                                        <li class="list-group-item border-0"><a class="text-decoration-none" href="/cuestionario/imprimir/{{$establecimiento->FRM_id}}"><i class="bi bi-printer"></i> Imprimir cuestionario</a></li>
                                        {{-- <li class="list-group-item border-0"><a class="text-decoration-none" href="/cuestionario/{{$establecimiento->FRM_id}}"><i class="bi bi-tools"></i> Construir cuestionario</a></li> --}}
                                        <li class="list-group-item border-0"><a class="text-decoration-none" href="/responderCuestionario/{{$establecimiento->FRM_id}}"><i class="bi bi-clipboard-check"></i> Responder/llenar cuestionario</a></li>
                                    </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                    <!-- END timeline item 1 -->
            @else
                <!-- START timeline item 2 -->
                <div class="row no-gutters">
                    <div class="col-sm py-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-end small">{{$establecimiento->FRM_fecha }}</div>
                                <h4 class="card-title">{{$establecimiento->FRM_titulo}}</h4>
                                <p class="card-text">
                                    <ul class="list-group">
                                        <li class="list-group-item border-0"><a class="text-decoration-none" href="/cuestionario/imprimir/{{$establecimiento->FRM_id}}"><i class="bi bi-printer"></i> Imprimir cuestionario</a></li>
                                        {{-- <li class="list-group-item border-0"><a class="text-decoration-none" href="/responderCuestionario/{{$establecimiento->FRM_id}}"><i class="bi bi-tools"></i> Construir cuestionario</a></li> --}}
                                        <li class="list-group-item border-0"><a class="text-decoration-none" href="/responderCuestionario/{{$establecimiento->FRM_id}}"><i class="bi bi-clipboard-check"></i> Responder/llenar cuestionario</a></li>
                                    </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1 text-center flex-column d-none d-sm-flex">
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                        <h5 class="m-2">
                            <span class="badge rounded-circle bg-light border border-4 border-primary">&nbsp;</span>
                        </h5>
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-sm"> <!--spacer--> </div>
                </div>
                <!-- END timeline item 1 -->
            @endif
        @endforeach
    </div>


<script type="text/javascript">
    $(".alert").fadeOut(4500);
</script>
@endsection
