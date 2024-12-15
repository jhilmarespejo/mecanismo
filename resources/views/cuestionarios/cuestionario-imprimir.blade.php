

@extends('layouts.app')
@section('title', 'Cuestionario')


@section('content')
<style>
    @page
    {
        size:  auto;   /* auto es el valor inicial */
        margin: 3mm;  /* afecta el margen en la configuración de impresión */
    }
    @media print {
        .encabezado{
            display:flex !important;
            margin-top:0px;
            padding-top: 0px;
        }
        #cuestionario {
            font-size: 12px;
            /* margin-left: -1px;*/
            /* margin-right: -10px; */

            width: 100%;
            margin-top: 0px;
        }
        #titulo{
            padding-top: 0px;
            font-size: 15px !important;
        }
        #establecimiento, #version {
            font-size: 10px !important;
        }
        .formulario{
            border: 1px !important;
            padding: 20px;
        }
        li{
            margin-left: -5px;
        }
        /* {
            width: 42%;
        }
        .col-respuestas{
            width: 55%;
        } */
        #nav1, .historial, #btn_imprimir, #establecimiento,#version, #nav2{
            display: none;
        }
        /* .complemento{
            margin-right: -40px;
        }
        small{
            font-size: 7px !important;
            font-weight: bold;
            margin-top: 0;
            padding-top: 0;
        } */
      }
        input.resp-lar{
            height: 50px;
        }
</style>

<div class="container-fluid" id="cuestionario" >
    @if ( count($elementos) > 0 )
        @mobile
        <div class="container-fluid row border-top border-bottom p-3">
            <div class="col ">
                {{-- <a class="text-decoration-none fs-4" href="/establecimientos/historial/{{$formulario->FK_EST_id}}" >
                    <i class="bi bi-arrow-90deg-left"></i>
                </a> --}}
                <a href="javascript:history.back()" role="button" class="text-decoration-none"> <i class="bi bi-arrow-90deg-left"></i></a>
            </div>
            <div class="col">
                <a class="text-decoration-none fs-4" id="imprimir_formulario"><i class="bi bi-printer-fill"></i></a>
            </div>
            <div class="col ">
                <a class="text-decoration-none fs-4" href="/cuestionario/responder/{{$FRM_id}}/{{$AGF_id}}" >
                    <i class="bi bi-pencil-square"></i>
                </a>
            </div>
        </div>
        @endmobile
        @desktop
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="nav2">
            <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-gear"></i></a>
            <button class="navbar-toggler bg-secondary " type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-three-dots"></i>
            </button>
            <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
                <ul class="navbar-nav" id="nav_2">
                <li class="nav-item p-1 px-3">

                    <a href="javascript:history.back()" role="button" class="text-decoration-none"> <i class="bi bi-arrow-90deg-left"></i> Página anterior</a>
                </li>
                <li class="nav-item p-1 px-3" id="btn_imprimir">
                    <a class="text-decoration-none" id="imprimir_formulario"><i class="bi bi-printer"></i> Imprimir</a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/cuestionario/responder/{{$VIS_id}}/{{$FRM_id}}/{{$AGF_id}}" >
                        <i class="bi bi-pencil-square"></i> Responder
                    </a>
                </li>
                {{-- <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/formulario/adjuntos/{{$formulario->FK_EST_id}}/" >
                        <i class="bi bi-folder-symlink"></i> Archivos adjuntos
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/recomendaciones/{{$formulario->FK_EST_id}}/" >
                        <i class="bi bi-chat-right-dots"></i></i> Recomendaciones
                    </a>
                </li> --}}
                </ul>
            </div>
            </div>
        </nav>

        @enddesktop
        <div class="d-flexx text-center bd-highlight mb-0 encabezado d-none">
            <div class="p-2 bd-highlight text-center"><img src="/img/logodp.png" style="height: 50px"></div>
            {{-- <div class="ms-auto p-2 bd-highlight"><img src="/img/logomnp.png" style="height: 50px"></div> --}}
        </div>
        <div class="text-center head">
            {{-- <p class="text-primary m-0 p-0" id="titulo" style="font-size: 30px" > Visita temática </p> --}}
            <p class="text-primary m-0 p-0" id="titulo" style="font-size: 30px" > {{ $FRM_titulo }} </p>
            <p class=" m-0 p-0" id="establecimiento" style="font-size: 20px">Establecimiento: {{ $EST_nombre }}</p>
            {{-- <p class=" m-0 p-0" id="version" style="font-size: 20px">Cuestionario: {{ $formulario->FRM_version }}</p> --}}
        </div>

        <div class="row border m-2 p-2  d-flex formulario">

            <ol>
                @php $c = 1; @endphp
                @foreach ($elementos_categorias as $key => $elemento)
                    <li> <strong id="categoria0">{{ $key }}</strong> </li>{{--subcategoria --}}
                    <ul class="list-unstyled">
                        @foreach($elemento as $k => $item)
                            <li class="mt-1" id="subcategorias">
                                {{-- @php dump($item) @endphp --}}
                                <div class="row border-bottom">
                                    @if ($item['BCP_tipoRespuesta'] == 'Afirmación')
                                        <div class="col-5" id="preguntas" >
                                            {{ $c. '. ' .$item['BCP_pregunta'] }}
                                                {{-- <small class="text-danger" style="font-size: 11px">* Marque solo una opción</small> --}}
                                                {{-- {{ var_dump($item['BCP_complemento']) }} --}}
                                        </div>
                                        <div class="col-7" id="preguntas" >
                                            <div class="row d-flex">
                                                @php
                                                    $opciones = json_decode( $item['BCP_opciones'], true);
                                                        if ( is_array($opciones) ) {
                                                            foreach ($opciones as $key => $opcion) {
                                                                echo "<div class='col-auto d-flex '><i class='bi bi-app'> </i>$opcion </div>";
                                                            }
                                                        }
                                                @endphp

                                                @if ( $item['BCP_complemento'] )
                                                    <div class="col-auto d-flex">{{ $item['BCP_complemento'] }} <input type="text" ></div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-5" id="preguntas" >
                                            {{ $c. '. ' .$item['BCP_pregunta'] }}
                                            @if ( $item['BCP_tipoRespuesta'] == 'Lista desplegable')
                                                <small class="text-danger" style="font-size: 11px">(* Marque solo una opción)</small>
                                            @elseif ($item['BCP_tipoRespuesta'] == 'Numeral')
                                                <small class="text-danger" style="font-size: 11px">(* Registre un número)</small>
                                            @elseif ($item['BCP_tipoRespuesta'] == 'Casilla verificación')
                                                <small class="text-danger" style="font-size: 11px">(* Puede marcar más de una opción)</small>
                                            @endif
                                        </div>

                                        <div class="col-7 col-respuestas">
                                            <div class="row d-flex">
                                                @php
                                                    $opciones = json_decode( $item['BCP_opciones'], true);
                                                    if ( is_array($opciones) ) {
                                                        foreach ($opciones as $key => $opcion) {
                                                            echo "<div class='col-auto d-flex'><i class='bi bi-app'> </i>$opcion </div>";
                                                        }
                                                    }
                                                    if ($item['BCP_tipoRespuesta'] == 'Numeral') {
                                                        echo "<div><input type='text' size='8'> </div>";
                                                    }
                                                    if ($item['BCP_tipoRespuesta'] == 'Respuesta corta') {
                                                        echo "<input type='text'> ";
                                                    }
                                                    if ($item['BCP_tipoRespuesta'] == 'Respuesta larga') {
                                                        echo "<input type='text' class='resp-lar'>";
                                                    }
                                                @endphp
                                                 @if ( $item['BCP_complemento'] )
                                                    <div class="col-auto d-flex">{{ $item['BCP_complemento'] }} <input type="text" ></div>
                                                @endif
                                            </div>
                                            {{-- xxxx - {{ var_dump($item['BCP_complemento']) }} --}}

                                        </div>
                                    @endif
                                </div>
                            </li>
                            @php $c++; @endphp
                        @endforeach
                    </ul>
                @endforeach
            </ol>
            @php
                // exit;
            @endphp


        </div>
    @else
        <div class="alert alert-warning p-3">
            <div class=" text-decoration-none" >Debe organizar preguntas para éste cuestionario </div>

        <a class="btn btn-danger bt-lg text-decoration-none" href="javascript:history.back()">Aceptar</a>
        </div>
    @endif

</div> {{-- container --}}

    {{-- <div class="text-center py-2" id="btn_imprimir">
        <span class="btn btn-success" id="imprimir_formulario">Imprimir formulario</span>
    </div> --}}

    {{-- <script src="/divjs/divjs.js"></script> --}}
    <script src="/printThis/printthis.js"></script>
    <script>
        $('#imprimir_formulario').on("click", function () {
            // $("#head").css("line-height: 1 !important;");
            // $('#cuestionario').printElement();
            // $("#cuestionario").print();

            $("#cuestionario").printThis({
                removeInlineSelector: '.historial',
                printDelay: 50,
            });
        });
    </script>

@endsection
