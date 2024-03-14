{{-- LISTA de los formularios aplicados a la visita seleccionada --}}

@extends('layouts.app')
@section('title', 'Formularios')

@section('content')

@php
    $TES_tipo = session('TES_tipo');
    $EST_nombre = session('EST_nombre');
    $VIS_tipo = session('VIS_tipo');
@endphp
{{-- SUB MENU --}}
    <div class="btn-toolbar " role="toolbar" aria-label="Toolbar with button groups">
        <div class="btn-group me-2 mt-4" role="group" aria-label="First group">

          <a href="javascript:history.back()" role="button" class="btn btn-primary text-white text-decoration-none"> <i class="bi bi-arrow-return-left"></i> Atrás</a>

        </div>
    </div><hr>

    {{-- @dump($formulario) --}}

    <div class="card">

        <div class="card-header {{$colorVisita}} text-white fs-5 text-center">
            {{$VIS_tipo}} <br>
            {{ $TES_tipo .' '. $EST_nombre }}
        </div>
        <div class="card-body">


            @if ( array_key_exists('nokey', $formulario) )
                <div class="alert alert-warning" role="alert">
                    Aún no se asignaron formularios para esta visita <br>
                    @if( Auth::user()->rol == 'Administrador' )
                        <a href="/formulario/{{$VIS_id}}" class="text-light text-shadow box-shadow mt-3 p-2 btn btn-success btn-lg"> Crear formulario </a>
                    @endif

                </div>

            @else

          <h5 class="card-title">Formularios:</h5>

            @php  $aux=0; foreach ($formulario as $key => $form): @endphp
                <div class="row">
                    <div class="col-lg-4 col-12">
                        <div class="card mb-3" style="max-width: 18rem;">
                            <div class="card-header">{{ $form[0]["FRM_titulo"] }}</div>

                            <div class="card-body text-center">

                                <img src="/img/{{$aux}}.png" class="img-fluid w-75" alt="Nuevo formulario">


                                <a href="/cuestionario/duplicar/{{$form[0]["FRM_id"]}}" class=" btn btn-success text-white text-shadow mt-2text-decoration-none box-shadow mt-1">
                                    <strong>Nuevo formulario</strong>
                                </a>

                                @if (Auth::user()->rol == 'Administrador')
                                    <a href="/cuestionario/resultados/{{ $form[0]["FRM_id"] }}" class="mt-2 btn btn-primary text-white box-shadow text-shadow">
                                        <i class="i bi-bar-chart-line"></i> Resultados
                                    </a>
                                @endif
<<<<<<< HEAD
                                @foreach ($cantidadCopiasFormulario as $indice=>$copiasFormulario)
                                    @if ( $indice == $form[0]["FRM_id"])
                                    <p class="alert alert-info p-0 mt-2 " role="alert">Formularios aplicados: <b>{{$copiasFormulario}}</b></p>
                                    @endif
                                @endforeach

=======
                                    @foreach ($cantidadCopiasFormulario as $indice=>$copiasFormulario)
                                        @if ( $indice == $form[0]["FRM_id"])
                                            <p class="alert alert-info p-0 mt-2 " role="alert">Formularios aplicados: <b>{{$copiasFormulario}}</b></p>
                                        @endif
                                    @endforeach
>>>>>>> main
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-12">
                        <div class="row">
                            @foreach ($form as $item)
                            <?php if ( $resultado == $item["AGF_id"]){
                                    $sombra = 'gren-shadow';
                                    $lapiz = 'text-danger';
                                }else{
                                    $sombra = 'shadow';
                                    $lapiz = 'text-primary';
                                }
                            ?>
                            @if ($item['AGF_id'] )
                                <div class="col-auto d-flex card m-1 border-bottom m-0 p-0 {{ $sombra }}" id="{{$item["AGF_id"]}}"  >
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
                                        <img src="/img/c-{{$aux}}.png" class="img-fluid w-75" alt="Nuevo formulario">
                                    </div>
                                    <div class="card-footer p-0">
                                        <ul class="list-group list-group-horizontal list-unstyled ">
                                            {{-- <li class="p-0 m-0">
                                                <a href="/cuestionario/imprimir/{{$item["FRM_id"]}}/{{$item["AGF_id"]}}"><i class="bi bi-printer-fill px-2 text-primary fs-5"></i></a>
                                            </li> --}}
                                            <li class="p-0 m-0">
                                                <a href="/cuestionario/responder/{{$item["FRM_id"]}}/{{$item["AGF_id"]}}"><i class="bi bi-pen-fill px-2 fs-5 {{ $lapiz }}"></i></a>
                                            </li>
                                            {{-- @dump($item) --}}
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
                                    <div class="p-0 m-0 alert alert-info" role="alert" style="font-size: 13px">P: <b>{{$item["cantidad_preguntas"]}}</b> - R: <b class="{{ ($item["cantidad_respuestas"] == 0)? 'bg-danger text-white px-2 box-shadow rounded-circle':'' }}">{{$item["cantidad_respuestas"]}}</b> </div>
                                </div>
                            @endif
                       @endforeach
                        </div>
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
    @if (Session::has('success'))
        <script>
            Swal.fire(
                '{{Session::get('success') }}',
            )
        </script>
    @endif
    @if(Session::has('warning'))
        <script>
            Swal.fire(
                '{{Session::get('warning') }}',
            )
        </script>

    @endif
<style>
    .gren-shadow {
  box-shadow: 4px 4px 4px #3d8bfd;}
</style>
<script>
        $(document).ready(function (){
            $('html, body').animate({
                scrollTop: $("#{{$resultado}}").offset().top
            }, 300);
        });

    $('.frm-duplicar-cuestionario').submit( function(e){
        e.preventDefault();


        // Swal.fire({
        //     title: '¿Está seguro de duplicar?',
        //     type: 'warning',
        //     showCancelButton: true,
        //     confirmButtonColor: '#3085d6',
        //     cancelButtonColor: '#d33',
        //     cancelButtonText: 'Cancelar',
        //     confirmButtonText: 'Duplicar'
        // }).then((result) => {
        //     if (result.value) {
        //         this.submit();
        //     }
        // });
    });
    $('.frm-eliminar-cuestionario').submit( function(e){
        e.preventDefault();
        // var form = $(this).parents('form');
        Swal.fire({
            title: '¿Está seguro de eliminar?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Eliminar'
        }).then((result) => {
            if (result.value) {
                this.submit();
                // Swal.fire('Eliminado', 'Elimando correctamente!', 'success');
            }
        });
    });
</script>

@endsection





