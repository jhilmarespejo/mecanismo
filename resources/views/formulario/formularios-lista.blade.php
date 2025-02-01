{{-- LISTA de los formularios aplicados a la visita seleccionada --}}

@extends('layouts.app')
@section('title', 'Formularios')

@section('content')
@php
    use Carbon\Carbon;
@endphp
@php
// recuperar las variables de sesion
    $TES_tipo = session('TES_tipo');
    $EST_nombre = session('EST_nombre');


@endphp
{{-- SUB MENU --}}
    <div class="btn-toolbar " role="toolbar" aria-label="Toolbar with button groups">
        <div class="btn-group me-2 mt-4" role="group" aria-label="First group">

          <a href="javascript:history.back()" role="button" class="btn btn-primary text-white text-decoration-none"> <i class="bi bi-arrow-return-left"></i> Página anterior</a>
        
        </div>
    </div><hr>
    
    {{-- @dump($formulario) --}}
  
    
    <div class="card">

        <div class="card-header {{$colorVisita}} text-white fs-5 text-center">
            {{$VIS_tipo}} <br>
            {{ $TES_tipo .' '. $EST_nombre }}
        </div>

        <div class="card-body">
            @if ( empty($grupo_formularios) )
                <div class="alert alert-warning" role="alert">
                    Aún no se asignaron formularios para esta visita <br>
                    @if( Auth::user()->rol == 'Administrador' )
                        <a href="/formulario/eleccion/{{$VIS_id}}/{{$VIS_tipo}}" class="text-light text-shadow box-shadow mt-3 p-2 btn btn-success btn-lg"> Asignar formulario </a>
                    @endif

                </div>

            @else

          <h5 class="card-title">Formularios:</h5>

          {{-- @dump( $grupo_formularios) --}}
          @php  $aux=0; foreach ($grupo_formularios as $key => $formulario): @endphp
                <div class="row">
                    <div class="col-lg-4 col-12">
                        
                        
                        <div class="card mb-3" style="max-width: 18rem;">
                            <div class="card-header">{{ $key }}</div>

                            <div class="card-body text-center">

                                <img src="/img/{{$aux}}.png" class="img-fluid w-75" alt="Nuevo formulario">


                                <a href="/cuestionario/duplicarCuestionario/{{$formulario[0]['FRM_id']}}/{{$VIS_id}}" class=" btn btn-success text-white text-shadow mt-2text-decoration-none box-shadow mt-1">
                                    <strong>Crear Nuevo formulario_ </strong>
                                </a>
                                
                                @if (Auth::user()->rol == 'Administrador')
                                    <a href="/cuestionario/resultados/{{ $formulario[0]["FRM_id"] }}" class="mt-2 btn btn-primary text-white box-shadow text-shadow">
                                        <i class="i bi-bar-chart-line"></i> Resultados
                                    </a>
                                @endif
                                <p class="alert alert-info p-0 mt-2 " role="alert">Formularios aplicados: <b>{{count($formulario)}}</b></p>

                            </div>
                           
                            
                        </div>
                        
                    </div>

                    <div class="col-lg-8 col-12">
                         {{-- MENSAJE DE ALERTA cuando se intenta duplicar un formulario cuyo $FRM->FRM_tipo == '1' --}}
                         @if ($formulario[0]["FRM_tipo"] == '1')
                            @if(session('warning'))
                                <div id="alert-warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ session('warning') }}
                                </div>
                            @endif
                        @endif

                        @foreach ($formulario as $item)
                            {{-- @dump($item) --}}
                            <?php/* if ( $resultado == $item["AGF_id"]){
                                    $sombra = 'gren-shadow';
                                    $lapiz = 'text-danger';
                                }else{
                                    $sombra = 'shadow';
                                    $lapiz = 'text-primary';
                                }*/
                            ?>
                            <div class="row border border-2 rounded m-1 p-0 align-items-center">
                                <div class="col-1">
                                    <a href="/cuestionario/responder/{{$VIS_id}}/{{$item["FRM_id"]}}/{{$item["AGF_id"]}}" class="m-0 p-1 d-block text-decoration-none text-dark">
                                    <i class="bi bi-file-earmark-ruled fs-5"></i></a>
                                </div>
                                <div class="col m-0 p-0 fs-6">
                                    @if ($item['AGF_id'] )
                                        <a href="/cuestionario/responder/{{$VIS_id}}/{{$item["FRM_id"]}}/{{$item["AGF_id"]}}" class="m-0 p-1 d-block text-decoration-none text-dark">
                                            <p class="m-0">{{ mb_strimwidth($item["FRM_titulo"], 0, 40, '...', 'UTF-8') }}</p>
                                            <p class="m-0 text-muted"><small>Creado: {{ Carbon::parse($item["createdAt"])->translatedFormat('d. M. Y H:i:s') }} | Código {{$item["AGF_id"]}}</small></p>
                                        </a>
                                    @endif
                                </div>
                                <div class="col m-0 p-0 fs-6">
                                    <p class="m-0">Preguntas: {{$item["preguntas"]}}</p>
                                    <p class="m-0">Respuestas: {{$item["respuestas"]}}</p>
                                </div>

                                <div class="col-1 fs-3 m-0 p-0">
                                    <a href="/cuestionario/responder/{{$VIS_id}}/{{$item["FRM_id"]}}/{{$item["AGF_id"]}}"><i class="bi bi-pencil-square "></i></a>
                                </div>
                                @if ( $item["estado"] != 1 && Auth::user()->rol == 'Administrador' )
                                    <div class="col-1 fs-3">
                                        <form action="{{ route('cuestionario.eliminar') }}" method="Post" class=" frm-eliminar-cuestionario m-0 p-0">
                                            @csrf
                                            <input type="hidden" name="AGF_id" value="{{ $item["AGF_id"] }}">
                                            <button type=submit id="eliminar_formulario" class="btn p-0"><i class="bi bi-trash px-2 text-warning fs-3"></i></button>
                                        </form>
                                    </div>
                                @endif


                            </div>

                            {{-- @if ($item['AGF_id'] )
                                <div class="col-auto d-flex card m-1 border-bottom m-0 p-0 " id="{{$item["AGF_id"]}}"  >
                                    <div class="card-header p-0 ">
                                        <ul class="list-group list-group-horizontal list-unstyled text-center">
                                            <li class="p-0 m-0">

                                                <span class="position-absolute top-20 start-50 translate-middle badge rounded-pill text-danger " style=" font-size: 1em; margin-top: 0.7rem">
                                                    {{$item["AGF_id"]}}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body text-center p-0 m-0  ">
                                    </div>
                                    <div class="card-footer p-0">
                                        <ul class="list-group list-group-horizontal list-unstyled ">

                                            <li class="p-0 m-0">
                                                <a href="/cuestionario/responder/{{$VIS_id}}/{{$item["FRM_id"]}}/{{$item["AGF_id"]}}"><i class="bi bi-pen-fill px-2 fs-5  $$$lapiz "></i></a>
                                            </li>
                                            @if ( $item["estado"] != 1 && Auth::user()->rol == 'Administrador' )
                                                <li class="p-0 m-0">
                                                    <form action="{{ route('cuestionario.eliminar') }}" method="Post" class=" frm-eliminar-cuestionario m-0 p-0">
                                                        @csrf
                                                        <input type="hidden" name="AGF_id" value="{{ $item["AGF_id"] }}">
                                                        <button type=submit id="eliminar_formulario" class="btn p-0"><i class="bi bi-trash px-2 text-warning fs-5"></i></button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            @endif --}}
                        @endforeach
                    </div>
                </div>
                <hr>

            @php
               $aux = ($aux + 1) % 5;
                endforeach
            @endphp
          {{-- </ul> --}}
          @endif


        </div>
      </div>


@endsection

@section('js')
    
<style>
    .gren-shadow {
  box-shadow: 4px 4px 4px #3d8bfd;}
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Verifica si existe el mensaje de alerta
        let alertElement = document.getElementById('alert-warning');
        if (alertElement) {
            // Mueve el scroll hacia el mensaje de alerta
            alertElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Desvanece el mensaje después de 5 segundos
            setTimeout(function () {
                let fadeEffect = setInterval(function () {
                    if (!alertElement.style.opacity) {
                        alertElement.style.opacity = 1;
                    }
                    if (alertElement.style.opacity > 0) {
                        alertElement.style.opacity -= 0.1;
                    } else {
                        clearInterval(fadeEffect);
                        alertElement.style.display = 'none';
                    }
                }, 50);
            }, 5000); // 5 segundos
        }
    });
    

    setTimeout(function () {
        let alertElement = document.getElementById('alert-warning');
        if (alertElement) {
            let fadeEffect = setInterval(function () {
                if (!alertElement.style.opacity) {
                    alertElement.style.opacity = 1;
                }
                if (alertElement.style.opacity > 0) {
                    alertElement.style.opacity -= 0.1;
                } else {
                    clearInterval(fadeEffect);
                    alertElement.style.display = 'none'; // Elimina el elemento del DOM
                }
            }, 50);
        }
    }, 5000); // 5 segundos
        // $(document).ready(function (){
        //     $('html, body').animate({
        //     }, 300);
        // });

    // $('.frm-duplicar-cuestionario').submit( function(e){
    //     e.preventDefault();


    //     // Swal.fire({
    //     //     title: '¿Está seguro de duplicar?',
    //     //     type: 'warning',
    //     //     showCancelButton: true,
    //     //     confirmButtonColor: '#3085d6',
    //     //     cancelButtonColor: '#d33',
    //     //     cancelButtonText: 'Cancelar',
    //     //     confirmButtonText: 'Duplicar'
    //     // }).then((result) => {
    //     //     if (result.value) {
    //     //         this.submit();
    //     //     }
    //     // });
    // });

    // ELIMINAR EL FORMULARIO SELECCIONADO CON AJAX Y NO RECARGAR LA PAGINA, SI LA RESPUESTA AJAX ES CORRECTA SOLO ELIMINAR EL ELMENTO DEL DOOM
    // $('.frm-eliminar-cuestionario').submit( function(e){
    //     e.preventDefault();
    //     // var form = $(this).parents('form');
    //     Swal.fire({
    //         title: '¿Está seguro de eliminar?',
    //         type: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         cancelButtonText: 'Cancelar',
    //         confirmButtonText: 'Eliminar'
    //     }).then((result) => {
    //         if (result.value) {
    //             this.submit();
    //             // Swal.fire('Eliminado', 'Elimando correctamente!', 'success');
    //         }
    //     });
    // });
</script>

@endsection





