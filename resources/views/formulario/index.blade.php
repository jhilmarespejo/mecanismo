@extends('layouts.app')
@section('title', 'Formularios')

@section('content')

<div class="container mt-3 p-4 bg-white">
    <h1 class="mb-2 text-center text-primary">Lista de Formularios</h1>
    @include('layouts.breadcrumbs', $breadcrumbs)
    
    <!-- Formulario de búsqueda -->
    <div class="mb-3 row">
        <div class="col-md-8">
            <input type="text" id="titulo" class="form-control" placeholder="Buscar por título del formulario">
        </div>
        <div class="col-md-4">
            <a class="btn btn-success w-100" href="{{route('formulario.nuevo')}}"><i class="bi bi-plus-circle"></i> Crear nuevo formulario</a>
        </div>
    </div>

    <!-- Contenedor de formularios, que se actualizará dinámicamente -->
    <div id="formularios-list" class="row">
        @if (count($formularios) == 0)
            <div class="col-md-12 mb-3 ">
                <div class="card mb-3 alert alert-warning">
                    <div class="card-body text-dark">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle"></i> No hay formularios disponibles
                        </h5>
                    </div>
                </div>
            </div>
            
        @else
            @foreach($formularios as $formulario)
                <div class="col-md-4 mb-3">
                    <div class="card mb-3" style="height: 100%;">
                        <div class="card-body text-dark" style="overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                            <!-- Texto ajustado al espacio del div -->
                            <h5 class="card-title">
                                {{ $formulario->FRM_titulo }}
                            </h5>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a class="btn btn-light" href="{{ route('formulario.verFormularioCreado', $formulario->FRM_id) }}">
                                <i class="bi bi-eye"></i> Ver formulario
                            </a>
                            <a class="btn btn-primary" href="{{ route('formulario.editar', $formulario->FRM_id) }}">
                                <i class="bi bi-pencil-square"></i> Editar formulario
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    
</div>

<script>
    $(document).ready(function() {
        // Detectar la escritura del usuario en el campo de búsqueda
        $('#titulo').on('keyup', function() {
            var titulo = $(this).val();

            // Enviar la solicitud AJAX para obtener los formularios filtrados
            $.ajax({
                url: '{{ route('formularios.filtrar') }}',  // Cambia la ruta a la nueva
                method: 'GET',
                data: { titulo: titulo },
                success: function(response) {
                    // Actualizar solo el contenedor de la lista de formularios
                    $('#formularios-list').html(response);
                }
            });
        });
    });
</script>

@endsection