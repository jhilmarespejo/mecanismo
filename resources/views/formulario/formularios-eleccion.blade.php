@extends('layouts.app')
@section('title', 'Formularios')

@section('content')
@php
    $TES_tipo = session('TES_tipo');
    $EST_nombre = session('EST_nombre');
    $EST_id= session('EST_id');
@endphp

<div class="text-center">
    <h2 class="text-primary fs-2">Formularios</h2>
</div>

<div class="container row p-0">
    <div class="col-md">
        <div class="card mb-3">
            <div class="card-header bg-primary text-light text-shadow">NUEVO FORMULARIO</div>
            <div class="card-body">
                <div class="my-3">
                    <label class="form-label"><b>Visita:</b></label>
                    <input type="text" class="form-control" disabled value="{{ $VIS_tipo }}">
                </div>
                <div class="my-3">
                    <label class="form-label"><b>Nombre del establecimiento:</b></label>
                    <input type="text" class="form-control" disabled value="{{ $EST_nombre }}">
                </div>
                <div class="my-3">
                    <label class="form-label"><b>Tipo de establecimiento:</b></label>
                    <input type="text" class="form-control" disabled value="{{ $TES_tipo }}">
                </div>
                <form method="POST" id="formulario-asignar" action="{{ route('formulario.asignar') }}">
                    @csrf
                    <input type="hidden" name="TES_tipo" value="{{ $TES_tipo }}">
                    <input type="hidden" name="EST_nombre" value="{{ $EST_nombre }}">
                    <input type="hidden" name="EST_id" value="{{ $EST_id }}">
                    <input type="hidden" name="VIS_id" value="{{ $VIS_id }}">
                    <div class="my-3">
                        <label class="form-label"><b>Seleccione una opción:</b></label>
                        <select class="form-select" name="opcion" id="opcion">
                            <option value="" selected>...</option>
                            <option value="nuevo">Crear nuevo formulario</option>
                            <option value="asignar">Asignar formulario a esta visita</option>
                        </select>
                        @error('opcion')
                            <i class="bi bi-info-circle text-danger"> </i><small class="text-danger">{{ $message }}</small>
                        @enderror
                        <br>
                        @error('FRM_id')
                            <i class="bi bi-info-circle text-danger"> </i><small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="my-3 d-none" id="formularios">
                        <label class="form-label"><b>Seleccione un formulario:</b></label>
                        <select class="form-select" name="FRM_id">
                            <option value="" selected>...</option>
                            @foreach ($formularios as $formulario)
                                <option value="{{ $formulario->FRM_id }}">{{ $formulario->FRM_titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-center my-3">
                        <button id="btn-submit" class="btn btn-success btn-lg text-shadow box-shadow" type="button">Aceptar</button>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#opcion').change(function (e) {
            e.preventDefault();
            if ($(this).val() == 'anterior' || $(this).val() == 'asignar') {
                $('#formularios').removeClass('d-none').hide().fadeIn(600);
            } else if ($(this).val() == 'nuevo') {
                $('#formularios').addClass('d-none').hide().fadeIn(800);
                Swal.fire({
                    title: '¿Desea continuar?',
                    text: 'Será redirigido a una nueva ventana donde podrá crear un nuevo formulario.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/formulario/nuevo'; // Redirigir a la ruta
                    }
                });
            }
        });

        // Interceptar el envío del formulario
        $('#btn-submit').click(function (e) {
            e.preventDefault();

            // Obtener datos seleccionados
            let visita = $('input[value="{{ $VIS_tipo }}"]').val();
            let establecimiento = $('input[value="{{ $EST_nombre }}"]').val();
            let tipoEstablecimiento = $('input[value="{{ $TES_tipo }}"]').val();
            let formularioSeleccionado = $('select[name="FRM_id"] option:selected').text();

            // Verificar que se haya seleccionado un formulario en caso de asignar
            if ($('#opcion').val() === 'asignar' && $('select[name="FRM_id"]').val() === '') {
                Swal.fire({
                    title: 'Error',
                    text: 'Debe seleccionar un formulario antes de continuar.',
                    icon: 'error',
                });
                return;
            }

            // Mostrar alerta de confirmación
            Swal.fire({
                title: '¿Desea continuar?',
                html: `El formulario "<strong>${formularioSeleccionado}</strong>" será asignado a: <br> "<strong>${visita}</strong>",<br>
           Lugar de detención: "<strong>${establecimiento}</strong>" <br>Tipo: "<strong>${tipoEstablecimiento}</strong>".`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#formulario-asignar').submit(); // Enviar el formulario
                }
            });
        });
    });
</script>

@endsection
