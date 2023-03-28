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
{{-- {{ $formularios[0] }} --}}
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
    <h3 class="text-center">{{ ( $formularios[0]->EST_poblacion == 'Privados privadas de libertad')? 'Centro penitenciario '. $formularios[0]->EST_nombre : $formularios[0]->EST_nombre }}</h3>

    {{-- para nuevo formulario --}}
    {{-- @if(Auth::user()->rol == 'Administrador' )
        <div class="text-center ">
            @include('formulario.formulario-nuevo', ['EST_id' => $formularios[0]->EST_id, 'EST_nombre' => $formularios[0]->EST_nombre])
        </div>
    @endif --}}

    {{-- para nueva visita --}}
    @if(Auth::user()->rol == 'Administrador' )
        <div class="text-center ">
            @include('visita.visita-nuevo', ['EST_id' => $formularios[0]->EST_id, 'EST_nombre' => $formularios[0]->EST_nombre])
        </div>
    @endif

    @if( count($visitas) )
        {{-- Bloque para mostrar conteo de recomendaciones --}}
        {{-- <div class="row">
            <div class="col-sm mt-1">
                <div class="card text-white bg-danger ">
                    <a href="/recomendaciones/{{$formularios[0]->EST_id}}" class="text-decoration-none text-light">
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
            </div> --}}
            {{-- <div class="col-sm mt-1">
                <div class="card text-white bg-warning mb-3">
                    <a href="/formulario/adjuntos/{{$formularios[0]->EST_id}}" class="text-decoration-none text-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Archivos adjuntos</h5>
                            <img src="/img/adjuntos.png" class="img-fluid rounded" alt="Archivos adjuntos">
                        </div>
                    </a>
                </div>
            </div> --}}
        </div>

        {{-- @dump($formularios->toArray()) --}}

        {{-- <div class="d-none container py-2 mt-4 mb-4">
            @foreach ( $formularios as $key=>$establecimiento ) --}}
            <!-- START timeline item 1 TARJETAS PARA MOSTRAR LOS FORMULARIOS APLICADOS-->
                {{-- <div class="row no-gutters ">
                    <div class="col align-self-center text-end">
                        <!--spacer-->
                        <span class="text-end alert alert-primary">
                            {{ $establecimiento->FRM_fecha }}
                        </span>
                    </div> --}}
                    <!-- timeline item 1 center dot -->
                    {{-- <div class="col-sm-1 text-center flex-column d-none d-sm-flex bar" >
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>

                        <h2 class="m-2">
                            <span class="badge rounded-circle border border-4 border-primary text-primary text-shadow" >{{ count($formularios)-$key }}</span>
                        </h2>
                        <div class="row h-50">
                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-sm-7 py-2">
                        <div class="card">
                            <div class="card-body"> --}}
                                {{-- <div class="float-end text-muted small">{{$establecimiento->FRM_fecha }}</div> --}}
                                {{-- <h4 class="card-title">sssssssss
                                    <b class="d-xss-none">{{ count($formularios)-$key }}. </b>
                                    {{ $establecimiento->FRM_titulo }}
                                </h4>
                                <p class="card-text">
                                    <ul class="list-group">
                                        <li class="list-group-item border-0"><a class="text-decoration-none" href="/cuestionario/imprimir/{{$establecimiento->FRM_id}}"><i class="bi bi-printer"></i> Imprimir cuestionario</a></li>
                                        <li class="list-group-item border-0">


                                        </li>

                                        <li class="list-group-item border-0"><a class="text-decoration-none" href="/cuestionario/responder/{{$establecimiento->FRM_id}}"><i class="bi bi-file-ruled"></i> Responder/llenar cuestionario</a></li> --}}

                                        {{-- <li class="list-group-item border-0">
                                            <a class="text-decoration-none" href="/formulario/adjuntos/{{$formularios[0]->EST_id}}/{{$establecimiento->FRM_id}}" id="{{$establecimiento->FRM_id}}"><i class="bi bi-clipboard-check"></i> Archivos adjuntos</a>
                                        </li> --}}

                                        {{-- <li class="list-group-item border-0">
                                            <a class="text-decoration-none" id="{{$establecimiento->FRM_id}}"><i class="bi bi-journal-bookmark-fill"></i> {{ $establecimiento->FRM_tipoVisita }}</a>
                                        </li>

                                    </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                </div> --}}
            <!-- END timeline item 1 -->
            {{-- @endforeach
        </div> --}}

        <div class="container py-0 mt-4">
            @foreach ( $visitas as $key=>$visita )
                @php $VIS_id = $visita->VIS_id; @endphp

                {{-- Bloque para definir los colores por tipo de visita --}}
                @php
                    if($visita->VIS_tipo == 'Visita en profundidad'){
                        $color = 'text-white bg-success';
                    }elseif($visita->VIS_tipo == 'Visita Temática') {
                        $color = 'text-white bg-danger';
                    }elseif($visita->VIS_tipo == 'Visita de seguimiento'){
                        $color = 'text-white bg-primary';
                    }elseif($visita->VIS_tipo == 'Visita reactiva'){
                        $color = 'text-white bg-red';
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
                                            <a class="text-decoration-none" href="/visita/buscaFormularios/{{$VIS_id}}"><i class="bi bi-database"></i> Formularios</a>
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
            @endforeach
        </div>
    @endif

    @if( count($visitas) < 1 )
        <div class="alert alert-warning mx-5 mt-2 text-center" role="alert" data-bs-toggle="modal" data-bs-target="#nuevoFormulario">
            Aún no se aplicaron formularios a este establecimiento
        </div>
    @endif

    @if( count($recomendaciones) == 0 )
        {{-- <div class="alert alert-danger mx-5 mt-2 text-center" role="alert">
            Aún no se asignaron recomendaciones a este establecimiento
        </div> --}}
    @endif

    {{-- <div class="text-center py-2">
        @livewire('formularios-nuevo')
    </div> --}}
    {{-- @endif --}}

<div class="container"></div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">


    // $(".visita").click(function (e) {
    //     var id = parseInt((this.id).replace(/[^0-9.]/g, ""));
    //     console.log(id);
    //     $.ajax({
    //             async: true,
    //             headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    //             url: "/visita/buscaFormularios",
    //             type: 'post',
    //             data: {id: id},
    //             beforeSend: function () { },
    //             success: function (data, response) {
    //                 $('small.error').empty();
    //                 jQuery.each(data.errors, function( key, value ){
    //                     $('#'+key+'_err').append( value );
    //                 });
    //                 if(!data.errors){
    //                     Swal.fire({
    //                         icon: 'success',
    //                         title: data.message,
    //                         showConfirmButton: false,
    //                     });
    //                     setTimeout(function(){ location.reload() }, 2000);
    //                 }
    //             },
    //             error: function(response){ console.log(response) }
    //         });

    // });
</script>
@endsection
