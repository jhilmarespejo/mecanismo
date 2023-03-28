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
        <div class="card-body">
            <dl class="">
                @if ( count($formularios) > 0 )
                    @foreach ($formularios as $key=>$formulario)
                            <dt class="mt-3">
                                <ul class="list-group list-group-horizontal list-unstyled ">

                                    <li class=" p-0 m-0">
                                        <span class="badge bg-primary rounded-pill text-shadow p-1">
                                        {{ \Carbon\Carbon::parse($formulario->FRM_fecha)->format('d-m-Y') }}
                                        </span>
                                    </li>

                                    <li class="p-0 m-0">
                                        <a href="/cuestionario/ver/{{$formulario->FRM_id}}"><i class="bi bi-eye-fill px-2 text-primary fs-5"></i></a>
                                    </li>
                                    <li class="p-0 m-0">
                                        <a href="/cuestionario/imprimir/{{$formulario->FRM_id}}"><i class="bi bi-printer-fill px-2 text-primary fs-5"></i></a>
                                    </li>
                                    <li class="p-0 m-0">
                                        <a href="/cuestionario/responder/{{$formulario->FRM_id}}"><i class="bi bi-pen-fill px-2 text-primary fs-5"></i></a>
                                    </li>
                                    @if ( $formulario->estado != 'completado' )
                                    <li class="p-0 m-0">
                                        <form action="{{ route('cuestionario.eliminar') }}" method="Post" class=" frm-eliminar-cuestionario">
                                            @csrf
                                            <input type="hidden" name="FRM_id" value="{{$formulario->FRM_id}}">
                                            <button type=submit id="eliminar_formulario" class="btn p-0"><i class="bi bi-trash px-2 text-danger fs-5"></i></button>
                                        </form>
                                    </li>
                                    @endif

                                </ul>
                            </dt>
                            <dd class="border-bottom mb-1 ms-3 p-3 table-hover position-relative">
                                {{$formulario->FRM_titulo}} ({{ count($formularios)-($key) }})
                                <span class="position-absolute top-50 start-0 translate-middle badge rounded-pill">
                                    @if ( $formulario->estado == 'completado' )
                                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                    @else
                                        <i class="bi bi-exclamation-diamond-fill text-warning fs-4"></i>
                                    @endif
                                </span>
                            </dd>
                    @endforeach
                    <hr>

                @else
                sin formularios

                @endif

                @foreach ( $fs as $k=>$f)
                    @if (strstr($f['FRM_titulo'], 'Violencia'))
                        <div class="alert alert-danger row p-0" role="alert">
                            <a href="/cuestionario/duplicar/{{$f['FRM_id']}}/{{$f['FK_VIS_id']}}" class="text-decoration-none"><i class="bi bi-clipboard-plus-fill px-2 text-success fs-5"></i>
                                <strong>NUEVO</strong> {{ $f['FRM_titulo'] }}
                            </a>
                        </div>
                    @endif
                @endforeach
                <hr>
                @foreach ( $fs as $k=>$f)
                @if (strstr($f['FRM_titulo'], 'Salud'))
                    <div class="alert alert-success row p-0" role="alert">
                        <a href="/cuestionario/duplicar/{{$f['FRM_id']}}/{{$f['FK_VIS_id']}}" class="text-decoration-none"><i class="bi bi-clipboard-plus-fill px-2 text-success fs-5"></i>
                            <strong>NUEVO</strong> {{ $f['FRM_titulo'] }}
                        </a>
                    </div>
                @endif

            @endforeach
            </dl>
        </div>
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

    <script>
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





{{-- <h3 class="text-center">{{ ( $formularios[0]->EST_poblacion == 'Privados privadas de libertad')? 'Centro penitenciario '. $formularios[0]->EST_nombre : $formularios[0]->EST_nombre }}</h3> --}}

{{-- Entrevista al jefe de seguridad
Entrevista a los PPL
Verificacion --}}
