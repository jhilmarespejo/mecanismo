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
                    <div id="contenedor_pregunta_seleccionada" class="sortable">
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
                    <div class="card-body bg-light overflow-scroll" id="contenedor_lista_preguntas">
                        <!-- Tarjetas dinámicas se agregarán aquí -->
                    </div>
            </div>
        </div>
    </div>
</div>





<script>
    $(document).ready(function() {
        var contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
 = 1000;

        $('#nueva_pregunta').click(function() {
            nueva_pregunta();
        });

        // Hacer que el contenedor de preguntas sea ordenable
        $('#contenedor_pregunta_seleccionada').sortable({
            placeholder: "ui-state-highlight",
            start: function(event, ui) {
                ui.item.css("border", "2px solid #007bff"); // Cambiar el borde del elemento arrastrado
                ui.placeholder.height(ui.item.height()); // Ajustar la altura del marcador de posición
                ui.placeholder.css("background-color", "#f0f8ff"); // Color de fondo del marcador de posición
            },
            stop: function(event, ui) {
                ui.item.css("border", "1px solid #dee2e6"); // Restablecer el borde del elemento
                ui.item.effect("highlight", { color: "#d4edda" }, 1000); // Animación de resaltado al soltar
            }
        });

        function nueva_pregunta(){
            contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
++;
            var pregunta = `
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}">
                        <div class="input-group mb-3">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}" placeholder="Inserte la pregunta" name="pregunta[]" />
                        </div>
                        <div class="btn-group text-dark d-flex tipo-respuesta" >
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}"><i class="bi bi-paperclip"></i> Archivo </div>
                        </div>

                    </div>
                    <div class="card-footer text-end">
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}" data-id="${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Aceptar...
                        </span>
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}" data-id="${contadorPreguntasNuevas        var contadorPreguntasNuevas = 1000;
            contadorPreguntasNuevas++;
                <div class="card mb-3" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header" id="card_header_${contadorPreguntasNuevas}">
                            <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                            <input class="form-control pregunta col" type="text" list="preguntas" id="pregunta_${contadorPreguntasNuevas}" placeholder="Inserte la pregunta" name="pregunta[]" />
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}"><i class="bi bi-list-check"></i> Varias opciones </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}"><i class="bi bi-ui-radios-grid"></i> Única opción </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}"><i class="bi bi-chat-left-text"></i> Texto </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}"><i class="bi bi-123"></i> Numérico </div>
                            <div class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_archivo_${contadorPreguntasNuevas}"><i class="bi bi-paperclip"></i> Archivo </div>
                        <span class="btn btn-success btn-sm aceptar-pregunta Xd-none box-shadow" id="btn_aceptar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <span class="btn btn-warning btn-sm eliminar-pregunta box-shadow" id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}"><i class="bi bi-trash"></i>Eliminar</span>
}"><i class="bi bi-trash"></i>Eliminar</span>
                    </div>
                </div>
            `;
            // $('#contenedor_lista_preguntas').append(listaPreguntas);
            $('#contenedor_pregunta_seleccionada').append(pregunta);


            //hacer un entpara el boton aceotar, que valide input vacio y eliminar todo lo demas

        }

        $(document).on('click', '.btn-tipo-respuesta', function() {
            var id = $(this).attr('id');
            var tipoRespuesta = id.split('_')[1];
            var contadorId = id.split('_')[2];
            var icono = $(this).children('i').clone();
            $('#icono_pregunta_'+contadorId).empty();
            $('#icono_pregunta_'+contadorId).append(icono);
            switch (tipoRespuesta) {
                case 'varias':
                    console.log(tipoRespuesta);
                    break;
                case 'unica':
                    console.log(tipoRespuesta);
                    break;
                default:
                    console.log('Tipo de respuesta no definido');
            }
        });

        $(document).on('click', '.aceptar-pregunta', function() {
            var id = $(this).data('id');
            console.log(id);
            // $('#card_header_' + id).parent().fadeOut(500, function() {
            //     $(this).remove();
            // });
            // $('#btn_aceptar_pregunta_' + id).fadeOut(500, function() {
            //     $(this).remove();
            // });
            // $('#btn_eliminar_bloque_' + id).fadeOut(500, function() {
            //     $(this).remove();
            // });
            // $('#eliminar_pregunta_' + id).removeClass('d-none').hide().fadeIn(500);
            // // $('#eliminar_pregunta_' + id).removeClass('d-none').hide().fadeIn(500);

        });


    });

    </script>




@endsection

