@extends('layouts.app')
@section('title', 'Formularios')

@section('content')
<style>
    .sortable-list {
        list-style-type: none;
        padding: 0;
    }
    .card {
        margin-bottom: 10px;
    }
</style>
@php
    $TES_tipo = session('TES_tipo');
    $EST_nombre = session('EST_nombre');
    $EST_id = session('EST_id');
@endphp
@mobile
<div class="container-fluid row border-top border-bottom p-3">
    <div class="col">
        <a class="text-decoration-none" href="/visita/historial/{{ $EST_id }}" >
            <i class="bi bi-house-gear-fill"></i> Historial
        </a>
    </div>
    <div class="col">
        <a class="text-decoration-none" href="/recomendaciones/{{ $EST_id }}" >
            <i class="bi bi-chat-right-dots"></i> Recomendaciones
        </a>
    </div>
</div>
@endmobile
@desktop
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
        <ul class="navbar-nav" id="nav_2">
            <li class="nav-item p-1 px-3">
                <a class="text-decoration-none" href="/visita/historial/{{ $EST_id }}" >
                    <i class="bi bi-house-gear-fill"></i> Historial
                </a>
            </li>
            {{-- <li class="nav-item p-1 px-3" id="btn_imprimir">
                <a class="text-decoration-none" href="/cuestionario/imprimir/{{ $FRM_id }}" >
                    <i class="bi bi-printer"></i> Vista para imprimir formulario</span>
                </a>
            </li> --}}
            {{-- <li class="nav-item p-1 px-3">
                <a class="text-decoration-none" href="/cuestionario/responder/{{ $FRM_id }}" >
                    <i class="bi bi-ui-checks-grid"></i> Responder cuestionario
                </a>
            </li> --}}
            <li class="nav-item p-1 px-3">
                <a class="text-decoration-none" href="/recomendaciones/{{ $EST_id }}" >
                    <i class="bi bi-chat-right-dots"></i> Recomendaciones
                </a>
            </li>
        </ul>
    </div>
</nav>
@enddesktop
<style>
    .list-group-item:hover {
        background: rgb(233, 248, 255);
        cursor: pointer;
    }
    .sticky-button {
            position: sticky;
            top: 70px; /* Ajusta este valor si tu navbar tiene una altura diferente */
            z-index: 1000;
        }
        .overflow-scroll {
            max-height: 80vh; /* Ajusta este valor según la altura que necesites */
            /* overflow-y: auto; */
        }
</style>

<div class="container mt-2">
    <h1 class="mb-4">{{ $nuevo_formulario }}</h1>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="container border p-3">
                <form id="formulario" method="POST" action="{{ route('formulario.store') }}">
                    @csrf
                    <input type="hidden" name="FRM_titulo" value="{{ $nuevo_formulario }}">
                    <div id="contenedor_pregunta_seleccionada">
                        <!-- Tarjetas dinámicas se agregarán aquí -->
                    </div>
                    <button type="submit" class="btn btn-success">Guardar Formulario</button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top: 70px;">
                <div class="sticky-button text-center mb-3">
                    <span class="btn btn-lg btn-primary box-shadow" id="nueva_pregunta">
                        <i class="bi bi-plus-circle"></i> Nueva pregunta
                    </span>
                </div>
                {{-- <div class="card text-dark bg-light mb-3"> --}}
                    <div class="card-body bg-light overflow-scroll" id="contenedor_lista_preguntas">
                        <!-- Tarjetas dinámicas se agregarán aquí -->
                    </div>
                {{-- </div> --}}
            </div>
        </div>
    </div>
</div>

<script>
     $(document).ready(function() {
        var contadorPreguntas = 0;
        $('#nueva_pregunta').click(function() {
            //si el spinner no existe en el dom se ejecuta la funcion nueva_pregunta
            if ( $('.spinner').length === 0 && $('.aceptar_pregunta').length === 0 ) {
                nueva_pregunta();
            }else{
                event.preventDefault();
            }
        });

        function nueva_pregunta(){
            contadorPreguntas++;
            var listaPreguntas = `
                <div class="card mb-3" id="card_pregunta_${contadorPreguntas}">
                    <div class="card-header" id="card_header_${contadorPreguntas}">
                        <div class="mb-3">
                            <label for="pregunta_${contadorPreguntas}" class="form-label">Seleccione una Pregunta</label>
                            <input class="form-control pregunta" type="text" list="preguntas" id="pregunta_${contadorPreguntas}" name="pregunta" />
                            <ul class="list-group" id="preguntas_${contadorPreguntas}"> <!--preguntas sugeridas--> </ul>
                        </div>
                    </div>
                </div>
            `;
            var preguntaSeleccionada = `
                <div class="card mb-3" id="card_sugerencia_${contadorPreguntas}">
                    <div class="card-body">
                        <div id="datos_pregunta_${contadorPreguntas}"></div>
                        <div class="text-center mt-3">
                            <div class="spinner" id="spinner_${contadorPreguntas}" data-id="${contadorPreguntas}">
                                <div class="spinner-border text-info" role="status" style="animation-duration: 2s;"></div>
                                <span >Seleccione una pregunta...</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <span class="btn btn-success btn-sm aceptar-pregunta d-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntas}" data-id="${contadorPreguntas}">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Aceptar...
                        </span>
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntas}" data-id="${contadorPreguntas}"><i class="bi bi-trash"></i>Eliminar</span>
                    </div>
                </div>
            `;
            $('#contenedor_lista_preguntas').append(listaPreguntas);
            $('#contenedor_pregunta_seleccionada').append(preguntaSeleccionada);

            // Evento para mostrar sugerencias de preguntas al escribir en el input de pregunta
            $('#pregunta_' + contadorPreguntas).on('input', function() {
                var inputId = $(this).attr('id'); // id completo
                var id = inputId.match(/\d+/)[0]; // parte numerica del id
                var inputVal = $(this).val();

                if (inputVal.length > 3) {
                    $.ajax({
                        async: true,
                        url: '/formulario/buscarPregunta',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        type: 'post',
                        data: { pregunta: inputVal }, // Datos a enviar
                        dataType: 'json', // Tipo de datos esperados
                        success: function(response) {
                            $('#preguntas_' + id).empty(); // Limpiar div antes de agregar nuevos elementos
                            $.each(response, function(index, pregunta) {
                                var listItem = $('<li class="m-0 p-1 list-group-item">').text(pregunta.BCP_pregunta);
                                // Adjuntamos los datos adicionales de la pregunta a través de un atributo de datos personalizado
                                listItem.data('pregunta-completa', pregunta); // Almacena todos los datos de la pregunta
                                $('#preguntas_' + id).append(listItem);
                            });

                            // Manejador de eventos click para cada elemento de la lista
                            $('#preguntas_' + id + ' li').click(function() {
                                // Acceder a los datos adicionales de la pregunta asociada al elemento de la lista
                                var preguntaCompleta = $(this).data('pregunta-completa');
                                // console.log(preguntaCompleta);
                                // Mostrar los datos adicionales en otro div
                                mostrarDatosPregunta(id, preguntaCompleta);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error en la solicitud AJAX:', error);
                        }
                    });

                    function mostrarDatosPregunta(id, preguntaCompleta) {
                        // Limpiar el contenido previo del div
                        $('#datos_pregunta_' + id).empty();

                        // Crear un div para mostrar los datos de la pregunta
                        var datosHTML = '<div class="form-section">';
                        datosHTML += '<h5 class="card-header">' + preguntaCompleta.BCP_pregunta + '</h5>';
                        datosHTML += '<ul class="list-group list-group-flush">';
                        datosHTML += '<input type="hidden" name="BCP_id_[]' + id + '" value="' + preguntaCompleta.BCP_id + '">';
                        var opciones = JSON.parse(preguntaCompleta.BCP_opciones);

                        if (preguntaCompleta.BCP_tipoRespuesta === "Afirmación" || preguntaCompleta.BCP_tipoRespuesta === "Lista desplegable") {
                            datosHTML += '<li class="list-group-item">';
                            $.each(opciones, function (clave, opcion) {
                                datosHTML += '<div class="form-check">';
                                datosHTML += '<input class="form-check-input" type="radio" name="pregunta_' + id + '" value="' + opcion + '">';
                                datosHTML += '<label class="form-check-label">' + opcion + '</label>';
                                datosHTML += '</div>';
                            });
                            datosHTML += '</li>';
                        } else if (preguntaCompleta.BCP_tipoRespuesta === "Casilla verificación") {
                            datosHTML += '<li class="list-group-item">';
                            $.each(opciones, function (clave, opcion) {
                                datosHTML += '<div class="form-check">';
                                datosHTML += '<input class="form-check-input" type="checkbox" name="pregunta_' + id + '" value="' + opcion + '">';
                                datosHTML += '<label class="form-check-label">' + opcion + '</label>';
                                datosHTML += '</div>';
                            });
                            datosHTML += '</li>';
                        } else if (preguntaCompleta.BCP_tipoRespuesta === "Numeral") {
                            datosHTML += '<li class="list-group-item">';
                            datosHTML += '<input class="form-control" type="number" name="pregunta_' + id + '">';
                            datosHTML += '</li>';
                        } else if (preguntaCompleta.BCP_tipoRespuesta === "Respuesta corta") {
                            datosHTML += '<li class="list-group-item">';
                            datosHTML += '<input class="form-control" type="text" name="pregunta_' + id + '">';
                            datosHTML += '</li>';
                        } else if (preguntaCompleta.BCP_tipoRespuesta === "Respuesta larga") {
                            datosHTML += '<li class="list-group-item">';
                            datosHTML += '<textarea class="form-control" name="pregunta_' + id + '"></textarea>';
                            datosHTML += '</li>';
                        }

                        // Verificar si hay complemento y agregarlo
                        if (preguntaCompleta.BCP_complemento) {
                            datosHTML += '<li class="list-group-item">';
                            datosHTML += '<label for="complemento_' + id + '">' + preguntaCompleta.BCP_complemento + '</label>';
                            datosHTML += '<input class="form-control" type="text" name="complemento_' + id + '">';
                            datosHTML += '</li>';
                        }

                        datosHTML += '</ul>'; // Cerrar la lista
                        datosHTML += '</div>'; // Cerrar el div de la sección de la pregunta

                        // Mostrar los datos en el div #datos_pregunta_
                        $('#datos_pregunta_' + id).html(datosHTML);
                        $('#btn_aceptar_pregunta_' + id).removeClass('d-none').hide().fadeIn(500);
                        $('.spinner').remove().fadeIn(500);
                    }

                }
            });


            $(document).on('click', '#btn_aceptar_pregunta_' + contadorPreguntas, function() {
                var id = $(this).data('id');
                $('#card_header_' + id).parent().fadeOut(500, function() {
                    $(this).remove();
                });
                $('#btn_aceptar_pregunta_' + id).fadeOut(500, function() {
                    $(this).remove();
                });
                $('#btn_eliminar_bloque_' + id).fadeOut(500, function() {
                    $(this).remove();
                });
                $('#eliminar_pregunta_' + id).removeClass('d-none').hide().fadeIn(500);
                // $('#eliminar_pregunta_' + id).removeClass('d-none').hide().fadeIn(500);

            });

            // ELIMINAR la pregunta seleccionada
            $(document).on('click', '#eliminar_pregunta_' + contadorPreguntas, function() {
                var id = $(this).data('id');
                $('#card_sugerencia_' + id).fadeOut(500, function() {
                    $(this).remove();
                });
                $('#card_pregunta_' + id).fadeOut(500, function() {
                    $(this).remove();
                });

            });


        }




    });

    </script>




@endsection

