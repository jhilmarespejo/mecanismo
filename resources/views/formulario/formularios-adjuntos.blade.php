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

@php
$id = null; $elemento = []; $img = []; $i=0;
foreach($adjuntos as $k=>$adjunto){

    if( $id != $adjunto->ADJ_id ){
        array_push($elemento, ['ADJ_id' => $adjunto->ADJ_id, 'FK_FRM_id' => $adjunto->FK_FRM_id, 'ADJ_titulo' => $adjunto->ADJ_titulo, 'ADJ_fecha' => $adjunto->ADJ_fecha, 'ADJ_responsables' => $adjunto->ADJ_responsables, 'ADJ_entrevistados' => $adjunto->ADJ_entrevistados, 'ADJ_resumen' => $adjunto->ADJ_resumen]);

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
    dump( $elemento, $adjuntos->toArray());
    @endphp


    {{-- @dump($formulario) --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
          <a class="navbar-brand" href="#"><i class="bi bi-gear"></i></a>
          <button class="navbar-toggler bg-secondary " type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-three-dots"></i>
          </button>
          <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
            <ul class="navbar-nav" id="nav_2">
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/establecimientos/historial/{{$formulario['FK_EST_id']}}" >
                        <i class="bi bi-arrow-90deg-left"></i> Historial
                    </a>
                </li>
                <li class="nav-item p-1 px-3" id="btn_imprimir">
                    <a class="text-decoration-none" href="/cuestionario/imprimir/{{$formulario['FRM_id']}}" >
                        <i class="bi bi-printer"></i> Vista para imprimir formulario</span>
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/cuestionario/responder/{{$formulario['FRM_id']}}" >
                        <i class="bi bi-ui-checks-grid"></i> Responder cuestionario
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/recomendaciones/{{$formulario['FK_EST_id']}}/{{$formulario['FRM_id']}}" >
                        <i class="bi bi-chat-right-dots"></i> Recomendaciones
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="text-center head">
        <p class="text-primary m-0 p-0" style="font-size: 30px" > {{ $formulario['FRM_titulo'] }} </p>
        <p class=" m-0 p-0" id="establecimiento" style="font-size: 20px">Establecimiento: {{ $formulario['EST_nombre'] }}</p>
    </div>
    <div class="container p-sm-5 p-0">
        {{-- Elementos para nuevo adjunto --}}
        <div class="card shadow-lg" style="" id="adjunto_1">
            <div class="card-header bg-success" style="background-image: linear-gradient(to top right, #ffffff00, 60%,#fff);"> </div>
            <h3 class="row titulo" style="padding: 1% 4%; cursor: pointer">
                <span class="btn btn-success rounded-pill col-2 col-sm-1 fs-4 text-light"><i class="bi bi-file-earmark-plus t-shadow"></i></span>
                <span id="titulo" class="col hover">Nuevo adjunto</span>
                <br/>
            </h3>
            <form action="{{ route('formulario.adjuntosNuevo') }}" method="POST" id="nuevo_adjunto_form" enctype="multipart/form-data"> @csrf
                <div class="card-body" id="card_body_1" style="padding: 2% 5%; {{ (count($errors->all()) != 0)? '' : 'display:none' }}">
                    <div class="row">
                        <h3>
                            <div class="form-floating col-9" id="input_titulo">
                                <input type="text" id="titulo_nuevo" class="form-control" placeholder="Título" style="font-size: 30px;" name="ADJ_titulo" value="{{ old('ADJ_titulo')}}">
                                <label for="floatingInput fs-6">Título:</label>
                            </div>
                        </h3>

                        @error('ADJ_titulo')
                        <small class="text-danger"><i class="bi bi-info-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-sm-6 border-end">
                            <dl>
                                <dt>Fecha del documento:</dt>
                                <dd class="pe-4">
                                    <input type="hidden" name="FK_FRM_id" value="{{$formulario['FRM_id']}}">

                                    <input type="date" name="ADJ_fecha" class="form-control" value="{{ old('ADJ_fecha')}}">

                                    @error('ADJ_fecha')
                                    <small class="text-danger"><i class="bi bi-info-circle"></i> {{ $message }}</small>
                                    @enderror
                                </dd>
                                <dt>Responsable/s de la visita:</dt>
                                <dd class="responsables">
                                    <div class="input-group">
                                        <input type="text" name="ADJ_responsables[]" class="form-control" value="{{ old('ADJ_responsables.0')}}">
                                        <div id="nuevo_responsable" class="input-group-text bg-success text-light btn"><i class="bi bi-plus-circle"></i></div>
                                    </div>
                                    @error('ADJ_responsables.*')
                                    <small class="text-danger"><i class="bi bi-info-circle"></i> {{ $message }}</small>
                                    @enderror
                                </dd>
                                <dt>Entrevistado/s:</dt>
                                <dd class="entrevistados">
                                    <div class="input-group">
                                        <input type="text" name="ADJ_entrevistados[]" class="form-control" value="{{ old('ADJ_entrevistados.0')}}">
                                        <div class="nuevo-entrevistado input-group-text bg-success text-light btn"><i class="bi bi-plus-circle"></i></div>
                                    </div>
                                    @error('ADJ_entrevistados.*')
                                    <small class="text-danger"><i class="bi bi-info-circle"></i> {{ $message }}</small>
                                    @enderror
                                </dd>
                                <dt>Resumen:</dt>
                                <dd>
                                    <textarea class="form-control" name="ADJ_resumen" cols="30" rows="2">{{ old('ADJ_resumen')}}</textarea>
                                    @error('ADJ_resumen')
                                    <small class="text-danger"><i class="bi bi-info-circle"></i> {{ $message }}</small>
                                    @enderror
                                </dd>
                            </dl>
                        </div>
                        <div class="col-sm-6">
                            <div class=" px-3">
                                <label ><b>Archivo</b></label>
                                <div class="input-group">
                                    <input type="file" name="ARC_archivo[]" class="form-control" placeholder="Archivo" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture>
                                    <div class="input-group-text bg-success text-light btn" id="btn_nuevos_controles"><i class="bi bi-plus-circle"></i></div>
                                </div>
                                @error('ARC_archivo.*')
                                <small class="text-danger"><i class="bi bi-info-circle"></i> {{ $message }}</small>
                                @enderror
                                @error('ARC_archivo')
                                <small class="text-danger"><i class="bi bi-info-circle"></i> {{ $message }}</small>
                                @enderror
                                <div class="my-3">
                                    <label class="form-label"><b>Descripción del archivo:</b></label>
                                    <textarea name="ARC_descripcion[]" class="form-control" cols="30" rows="1">{{ old('ARC_descripcion.0')}}</textarea>
                                    @error('ARC_descripcion.*')
                                    <small class="text-danger"><i class="bi bi-info-circle"></i> {{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="border-bottom px-3" id="nuevo_elemento"></div>
                        </div>
                    </div>
                    <div class="row text-center px-5">
                        <button type="submit" class="shadow btn btn-success text-light" id="btn_guarda_adjuntos" style="text-shadow: 0px 0px 3px #000;">Guardar datos</button>
                        <button class="btn btn-primary d-none text-light" id="btn_guarda_spiner" type="button" disabled>
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            Cargando...
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ---Archivos adjuntos --------------- --}}
        @php
        $colores=array('primary', 'info', 'warning', 'danger' );
        @endphp
        @foreach ($elemento as $k=>$adjunto )
        @php $i = rand(0,3);  @endphp
        <div class="card shadow-lg mt-3" style="" id="adjunto_X">
            <div class="card-header bg-{{ $colores[$i] }}" style="background-image: linear-gradient(to top right, #ffffff00, 60%,#fff);"> </div>
            <h3 class="row border-bottom d-flex align-items-center" style="padding: 2% 0% 1% 5%">
                <span class="btn btn-{{ $colores[$i] }} rounded-pill col-2 col-sm-1  fs-4 text-light">{{ count($elemento)-$k }}</i></span>
                <span class="col">{{ $adjunto['ADJ_titulo'] }}</span>
                <br/>
            </h3>
            <div class="card-body" id="card_body_" style="padding: 2% 5%;">
                <div class="row">
                    <div class="col-sm-6 border-end">
                        <dl>
                            <dt>Fecha del documento:</dt>
                            <dd class="pe-4">
                                {{ $adjunto['ADJ_fecha'] }}
                            </dd>
                            <dt>Responsable/s de la visita:</dt>
                            <dd>
                                <ul>
                                    @foreach ((json_decode($adjunto['ADJ_responsables'], true)) as $responsables)
                                    <li> {{ $responsables }} </li>
                                    @endforeach
                                </ul>
                            </dd>
                            <dt>Entrevistado/s:</dt>
                            <dd >
                                <ul>
                                    @foreach ((json_decode($adjunto['ADJ_entrevistados'], true)) as $entrevistados)
                                    <li> {{ $entrevistados }} </li>
                                    @endforeach
                                </ul>
                            </dd>
                            <dt>Resumen:</dt>
                            <dd>
                                {{ $adjunto['ADJ_resumen'] }}
                            </dd>
                        </dl>
                    </div>
                    <div class="col-sm-6">
                        <dt>Archivos:</dt>
                        <dd>
                            @include('includes.archivos', ['archivos' => $adjuntos, 'id' => $adjunto['ADJ_id']])
                        </dd>

                    </div>
                </div>
            </div>
        </div>
        @endforeach


    </div>

    <script>
        /*Elimina input de entrevistado o responsable*/
        $(document).on('click', '.elimina-entrevistado, .elimina-reponsable', function(e){
            $(this).parent().fadeOut("slow");
        });
        /*Agrega input para entrevistado*/
        $('.nuevo-entrevistado').click(function(){
            $('.entrevistados').append('<div class="input-group pt-1"><input type="text" name="ADJ_entrevistados[]" class="form-control" ><div class="hover input-group-text bg-warning text-light elimina-entrevistado btn"><i class="bi bi-trash"></i></div></div>').hide().fadeIn();
        })

        /*Agrega input de responsable o entrevistador*/
        $('#nuevo_responsable').click(function(){
            $('.responsables').append('<div class="input-group pt-1"><input type="text" name="ADJ_responsables[]" class="form-control" ><div class="hover input-group-text bg-warning text-light elimina-reponsable btn"><i class="bi bi-trash"></i></div></div>').hide().fadeIn();
        })

        $(document).ready(function(){
            $('.titulo').click(function(){
                $('#card_body_1').slideToggle(function(){ });
            });
            $('#btn_guarda_adjuntos').click( function(){
                $(this).addClass('d-none');
                $('#btn_guarda_spiner').removeClass('d-none');
            } );
        });
        /* Elimina control seleccionado para nuevo archivo*/
        $(document).on('click', '.elimina-archivo', function(e){
            $(this).parent().parent().fadeOut("slow");
        });

        // let j=0;
        /* Agrega nuevos controles para nuevo archivo*/
        $(document).on('click', '#btn_nuevos_controles', function(){
            $('#nuevo_elemento').append('<div> <label ><b>Archivo</b></label><div class="input-group border-top pt-2"><input type="file" name="ARC_archivo[]" class="form-control" placeholder="Archivo"  accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture><div class="input-group-text bg-warning btn elimina-archivo"><i class="bi bi-trash text-light"></i></div></div><div class="my-3"><label class="form-label">Descripción del archivo:</label><textarea name="ARC_descripcion[]" class="form-control" cols="30" rows="1"></textarea></div> </div>');
        });


</script>

@endsection
