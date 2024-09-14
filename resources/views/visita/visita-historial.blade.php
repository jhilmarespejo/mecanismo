@extends('layouts.app')
@section('title', 'Visitas')

@section('content')
<style>
    /* div.no-gutters div.card:hover, div.no-gutters span.rounded-circle:hover  {
        background-color: rgb(204, 224, 255);
    } */
    .d-xss-none {
            display: none;
        }
    .bg-red{
        /* color: #c9412f !important; */
        background-color: #fd4d36  !important;
    }
    @media screen and (max-width: 320px) {
        .d-xss-none {
            display:unset;
        }
    }
</style>

    <h2 class="text-center py-2">Historial de visitas</h2>
    
    @if (Session::has('TES_tipo') && Session::has('EST_nombre') && Session::has('EST_id') )
        @php
            $EST_id = session('EST_id');
            $TES_tipo = session('TES_tipo');
            $EST_nombre = session('EST_nombre');
            if (!$EST_nombre) {
                dump('Regrese al inicio!');
            }
        @endphp
        <h3 class="text-center">{{ $TES_tipo.': '. $EST_nombre }}</h3>
    @else

    @endif

    {{-- Se incluye una vista para agregar una nueva visita --}}
    @if(Auth::user()->rol == 'Administrador' )
        <div class="text-center ">
            @include('visita.visita-nuevo', ['EST_id' => $EST_id, 'EST_nombre' => $EST_nombre])
        </div>
    @endif

    <div class="container py-0 mt-4">
        @foreach ( $visitas as $key=>$visita )
            @if ($visita->VIS_id)
                @php $VIS_id = $visita->VIS_id;
                // Bloque para definir los colores por tipo de visita
                    if($visita->VIS_tipo == 'Visita en profundidad'){
                        $color = 'text-white bg-success';
                    }elseif($visita->VIS_tipo == 'Visita Temática') {
                        $color = 'text-white bg-danger';
                    }elseif($visita->VIS_tipo == 'Visita de seguimiento'){
                        $color = 'text-white bg-primary';
                    }elseif($visita->VIS_tipo == 'Visita reactiva'){
                        $color = 'text-white bg-info';
                    }elseif($visita->VIS_tipo == 'Visita Ad hoc'){
                        $color = 'text-white bg-warning';
                    }
                @endphp
                <!-- START timeline item 1 TARJETAS PARA MOSTRAR LOS FORMULARIOS APLICADOS-->
                <div class="row no-gutters mb-4">
                    @mobile
                    <div class="col align-self-center text-end">
                        <span class="text-shadow text-center alert {{$color}}">
                            Visita: <span class="fs-4">{{ count($visitas)-$key }}</span>
                        </span>
                    </div>
                    <div class="col-5 align-self-center text-end">
                        <span class="text-shadow text-center alert {{$color}}">
                            {{ \Carbon\Carbon::parse($visita->VIS_fechas)->format('d-m-Y') }}
                        </span>
                    </div>
                    @endmobile
                    @desktop
                    <div class="col align-self-center text-end">
                        <span class="text-end alert {{$color}}">
                            {{ \Carbon\Carbon::parse($visita->VIS_fechas)->format('d-m-Y') }}
                        </span>
                    </div>
                    <!-- timeline item 1 center dot -->
                    <div class="col-sm-2 text-center flex-column d-sm-flex bar" >
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                        <h2 class="m-2">
                            <span class="badge rounded-circle border border-4 border-primary text-primary text-shadow" >{{ count($visitas)-$key }}</span>
                        </h2>
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                    </div>
                    @enddesktop
                    <div class="col-sm-7 py-2 box-shadow rounded {{$color}}">
                        <div class="card ">
                            <div class="card-body  {{$color}}">
                                <h4 class="card-title text-center text-shadow">
                                    <b class="d-xss-none">{{ count($visitas)-$key }}. </b>
                                    {{$visita->VIS_tipo}}: {{ $visita->VIS_titulo}}
                                </h4>
                                <p class="card-text">
                                    <ul class="list-group">
                                        <li class="list-group-item border-0">
                                            <a class="text-decoration-none" href="/formulario/buscaFormularios/{{$VIS_id}}"><i class="bi bi-database"></i> Formularios</a>
                                        </li>
                                        <li class="list-group-item border-0">
                                            <a class="text-decoration-none" href="/visita/actaVisita/{{$VIS_id}}">
                                                <i class="bi bi-file-earmark-medical-fill"></i> Acta de visita
                                            </a>
                                        </li>
                                        <li class="list-group-item border-0">
                                            <a class="text-decoration-none" href="/recomendaciones/{{$VIS_id}}">
                                                <i class="bi bi-file-earmark-text-fill"></i> Recomendaciones
                                            </a>
                                        </li>
                                    </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END timeline item 1 -->
                @php $VIS_id = 'x'; @endphp
            @else
                <div class="alert alert-warning mx-5 mt-2 text-center" role="alert" data-bs-toggle="modal" data-bs-target="#nuevoFormulario">
                    No existen visitas programadas para éste establecimiento
                </div>
            @endif

        @endforeach
    </div>

    @if( count($visitas) < 1 )

    @endif


<div class="container"></div>


@endsection
