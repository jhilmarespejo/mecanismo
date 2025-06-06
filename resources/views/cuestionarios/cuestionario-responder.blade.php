@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')

<style>
    .hover:hover{
        background-color:  #eaeaea;
    }
    @media screen and (max-width: 380px) {
        ol, ul{padding-left: 10px;}
    }
</style>


<div class="container-fluid p-sm-3 p-0 mx-0" id="cuestionario" >
    @if ( count($elementos) > 0 )
        {{-- minimenu --}}
        @mobile
        <div class="container-fluid row border-top border-bottom p-3">
            <div class="col ">

                <a href="javascript:history.back()" role="button" class="text-decoration-none"> <i class="bi bi-arrow-90deg-left"></i></a>
            </div>
            <div class="col ">
                <a class="text-decoration-none fs-4" href="/cuestionario/imprimir/{{$VIS_id}}/{{$FRM_id}}/{{$AGF_id}}" >
                    <i class="bi bi-printer-fill"></i></span>
                </a>
            </div>
        </div>
        @endmobile

        @desktop
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="nav2">
            <div class="container-fluid">
              <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
                <ul class="navbar-nav" id="nav_2">
                    <li class="nav-item p-1 px-3">
                        <a href="javascript:history.back()" role="button" class="text-decoration-none"> <i class="bi bi-arrow-90deg-left"></i> Página anterior</a>
                    </li>
                    <li class="nav-item p-1 px-3" id="btn_imprimir">
                        <a class="text-decoration-none" href="/cuestionario/imprimir/{{$VIS_id}}/{{$FRM_id}}/{{$AGF_id}}" >
                            <i class="bi bi-printer"></i> Imprimir</span>
                        </a>
                    </li>
                </ul>
              </div>
            </div>
        </nav>
        @endmobile
        {{-- Encabezado --}}
        <div class="text-center head">
            <p class="m-0 p-0 fs-3" id="establecimiento">{{ $EST_nombre }}</p>
            <p class="text-primary m-0 p-0 fs-3" id="titulo"> {{ $FRM_titulo }}</p>
            {{-- <p class="text-primary m-0 p-0 fs-5" id="titulo">Responder/llenar cuestionario: código-{{ $AGF_id }}</p> --}}
            <p class="text-primary m-0 p-0 fs-5" id="titulo">Responder/llenar cuestionario</p>
        </div>

        {{-- Cuestionario --}}
        <div class="row border m-sm-2 p-2 d-flex">
            {{-- boton para el plegar/desplegar el cuestionario --}}
            <legend class="text-primary fs-3 text-center" > Cuestionario</legend>

            {{-- @desktop --}}
                <div class="form-switch fs-4">
                    <input class="form-check-input" type="checkbox" checked onclick="plegar_desplegar('frm_cuestionario')">
                </div>
                @include('includes.cuestionario_desktop')
            {{-- @enddesktop --}}
            {{-- @mobile
                @include('includes.cuestionario_mobile')
            @endmobile --}}
        </div>

        {{-- INCLUDE para Recomendaciones --}}
        {{-- <div class="row border m-sm-2 p-2 d-flex"> --}}
            {{-- boton para el plegar/desplegar las observaciones --}}
            {{-- <div class="form-switch fs-4">
                <input class="form-check-input chek-observaciones" type="checkbox" onclick="plegar_desplegar('accordion_observaciones')">
            </div>
            <legend class="text-primary fs-4 text-center" > Oservaciones identificadas</legend>
            @include('includes.recomendaciones')
        </div> --}}

        {{-- INCLUDE para Adjuntos --}}
        {{-- boton para el plegar/desplegar los adjuntos --}}

        {{-- <div class="row border m-sm-2 p-2 d-flex">
            <div class="form-switch fs-4">
                <input class="form-check-input chek-adjuntos" type="checkbox" onclick="plegar_desplegar('div_adjuntos')">
            </div>
            <legend class="text-primary fs-4 text-center" > Archivos adjuntos</legend>
            @include('includes.adjuntos', ['FRM_id' => $FRM_id])
        </div> --}}

    @else

        @if(Auth::user()->rol == 'Administrador' )
            <div class="alert alert-warning p-3">

                <div class=" text-decoration-none" >Debe organizar preguntas para éste cuestionario </div>

                <a class="btn btn-danger bt-lg text-decoration-none" href="javascript:history.back()">Aceptar</a>
            </div>
        @else
            <div class="alert alert-warning p-3 btn btn-danger bt-lg text-decoration-none">
                El cuestionario aún no está disponible
            </div>
        @endif
    @endif

</div> {{-- /container --}}


<script>

    function plegar_desplegar(totoggle) {
        $('#'+totoggle).toggle("slow");
    }

    $(document).ready(function() {
        $('#accordion_observaciones').toggle("slow");
        $('#div_adjuntos').toggle("slow");
        // Evita enviar formulario al presionar Enter
        $("form").keypress(function(e) {
            if (e.which == 13) {
                return false;
            }
        });
    });

    function confirmaCuestionario( FRM_id ){
        $.ajax({
                async: true,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: '/cuestionario/confirmaCuestionario',
                type: 'POST',
                data: {estado: 'completado', FRM_id: FRM_id },
                // contentType: false,
                // processData: false,
                beforeSend: function () { },
                success: function (data, response) {
                    // console.log(data.message);
                    // Swal.fire(data.message);


                    Swal.fire({
                        text: data.message,
                        // icon: 'success',
                        confirmButtonText: 'Aceptar'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            window.history.back();
                        }
                    })
                },
                //complete : function(data, response) {},
                error: function(response){  }
            });
    }


    /*Boton para confirmar los datos del formulario*/
   /* Guarda cada respuesta del formulario cuando se el mouse se mueve a la siguiente pregunta*/
</script>

@endsection

