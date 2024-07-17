{{-- categorias/index --}}
@extends('layouts.app')
@section('title', 'Asesoramiento')
<style>
    /* Cambiar el color de la flecha del acordeón */
    .accordion-button::after {
        filter: invert(100%) sepia(100%) saturate(0%) hue-rotate(104deg) brightness(100%) contrast(100%);
    }
</style>
@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3 ">
            <h2 class="text-primary">Módulo de asesoría </h2>
            <a href="/asesoramientos/create" class="btn btn-primary btn-lg text-shadow box-shadow">Nueva actividad de asesoramiento</a>
        </div>
        @if (count($asesoramientos)>0)
        <div class="accordion" id="accordionMandato">
            @php $a = 0; @endphp
            @foreach ($asesoramientos as $mandato => $asesoramiento)
                <div class="accordion-item mt-2 box-shadow">
                    <h2 class="accordion-header" id="heading{{$a}}">
                    <button class="fs-5 accordion-button bg-success text-white d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{$a}}" aria-expanded="false" aria-controls="collapse_{{$a}}">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        {{$mandato}}
                        {{-- @dump(key($asesoramiento)) --}}
                    </button>
                    </h2>
                    <div id="collapse_{{$a}}" class="accordion-collapse collapse" aria-labelledby="heading{{$a}}" data-bs-parent="#accordionMandato">
                        <div class="accordion-body">
                            <p class="alert alert-info mb-2"> {{key($asesoramiento)}}</p>
                            <div class="accordion-body">
                                <fieldset id="archivos_0" class="hover border-start border-top px-2 mx-2" style="">
                                    <legend class="fs-6 float-none w-auto p-2">Actividades de asesoramiento <i class="fs-4 bi bi-activity text'primary"></i></legend>
                                    @foreach ($asesoramiento[key($asesoramiento)] as $actividad)
                                        <div class="border ms-2 mt-2 p-2 rounded">
                                            {{$actividad->ASE_actividad}}
                                            <hr>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><p>Fecha: {{$actividad->ASE_fecha_actividad}}</p></small>
                                                <div>
                                                    <a href="/asesoramientos/{{ $actividad->ASE_id }}/edit" class="btn btn-warning btn-sm">Modificar</a>
                                                    <form id="deleteForm-{{ $actividad->ASE_id }}" action="/asesoramientos/{{ $actividad->ASE_id }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <span  onclick="xxx('{{ $actividad->ASE_id }}')" class="btn btn-danger btn-sm">Eliminar</span>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>

                                    @endforeach
                                </fieldset>
                            </div>
                            </div>
                    </div>
                </div>

                @php $a++; @endphp
            @endforeach
        </div>
        @else
        <p class="alert alert-warning fs-5">
            Aún no se registraron actividades de asesoría!
        </p>
        @endif

    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        function xxx(id) {
            console.log(id);
            swal({
                title: "¿Estás seguro?",
                text: "Una vez eliminado, no podrás recuperar este registro",
                icon: "warning",
                buttons: ["Cancelar", "Eliminar"],
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    // Enviar el formulario de eliminación
                    document.getElementById('deleteForm-' + id).submit();
                }
            });
        }
    </script>



@endsection

