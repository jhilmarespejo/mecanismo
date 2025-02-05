{{-- FORMULARIO PARA CREAR NUEVO CUESTIONARIO --}}
@extends('layouts.app')
@section('title', 'Formularios')

@section('content')

@php
    $TES_tipo = session('TES_tipo');
    $EST_nombre = session('EST_nombre');
    $EST_id = session('EST_id');
@endphp

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
    .is-invalid {
        border-color: #dc3545; /* Rojo de Bootstrap para elementos inválidos */
    }
</style>
<div class="container mt-3 p-4 bg-white">
      <!-- Mensaje de éxito -->
      @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>¡Éxito!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Mensaje de error -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif  
    <!-- Sección única para el formulario -->
    <div class="col-lg-12 mb-4">
        <!-- Contenedor principal -->
        <div class="p-4 position-relative">
            <!-- Botón para agregar nueva pregunta (pegajoso en la esquina superior derecha) -->
            {{-- <div class="position-absolute top-0 end-0 mt-3 me-3">
                
            </div> --}}
            
            <!-- Título del formulario -->
            @include('layouts.breadcrumbs', $breadcrumbs)
            <h3 class="text-primary fw-bold mb-4 text-center">Crear nuevo formulario</h3>
            
            <form id="formularioPrincipal" method="POST" action="{{ route('formulario.store') }}">
                @csrf

                <!-- Campo para el título del formulario -->
                <div class="mb-4 text-center">
                    <label for="titulo_formulario" class=" form-label fw-bold">Título del formulario</label>
                    <input 
                        type="text" 
                        class="form-control form-control-lg shadow-sm text-center text-uppercase" 
                        id="titulo_formulario" 
                        name="FRM_titulo" 
                        placeholder="ESCRIBA EL TÍTULO DEL FORMULARIO AQUI..."  
                        value=""
                    />
                </div>
                
                
                

                <!-- Preguntas dinámicas -->
                <div id="contenedor_pregunta_seleccionada" class="sortable mb-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold">¿Este formulario se aplicará más de una vez?</label>
                        <div class="d-flex gap-3">
                            <input type="radio" class="btn-check" name="FRM_tipo" id="FRM_tipo_si" value="N" autocomplete="off">
                            <label class="btn btn-outline-success" for="FRM_tipo_si">Sí</label>
                    
                            <input type="radio" class="btn-check" name="FRM_tipo" id="FRM_tipo_no" value="" autocomplete="off">
                            <label class="btn btn-outline-danger" for="FRM_tipo_no">No</label>
                        </div>
                    </div>
                    <!-- Las preguntas dinámicas se agregarán aquí -->
                </div>
                <div id="contenedor_secciones" class="sortable mb-3"></div>
                <div id="contenedor_preguntas" class="sortable mb-3"></div>
                
                <!-- Botón para guardar el formulario -->
                <hr>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <!-- Botón para guardar formulario -->
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-save"></i> Guardar Formulario
                    </button>
                
                    <!-- Botón para agregar nueva pregunta -->
                    {{-- <button type="button"  class="btn btn-primary btn-lg box-shadow" id="nueva_pregunta">
                        <i class="bi bi-plus-circle"></i> Nueva pregunta
                    </button> --}}
                    
                    <!-- Botones para agregar elementos -->
                    <div class="d-flex gap-3">
                        <!-- Nueva Sección -->
                        <button type="button" class="btn btn-secondary btn-lg box-shadow" id="nueva_seccion">
                            <i class="bi bi-folder-plus"></i> Nueva Sección
                        </button>

                        <!-- Nueva Subsección -->
                        <button type="button" class="btn btn-secondary btn-lg box-shadow" id="nueva_subseccion">
                            <i class="bi bi-folder-symlink"></i> Nueva Subsección
                        </button>

                        <!-- Nueva Pregunta -->
                        <button type="button" class="btn btn-primary btn-lg box-shadow" id="nueva_pregunta">
                            <i class="bi bi-plus-circle"></i> Nueva Pregunta
                        </button>
                    </div>
                </div>
                <input type="hidden" name="listaPreguntasJSON" id="listaPreguntasJSON">
            
            </form>
        </div>
    </div>
    
</div>


<script>
    
    $(document).ready(function () {
        

        $('#formularioPrincipal').on('submit', function(e) {
            if (!validarFormulario()) {
                e.preventDefault(); // Detiene el envío del formulario si hay errores
            } else {
                ensamblarDatos(); // Solo ensambla datos si el formulario es válido
            }
        });
        
        //Variables globales
        var contadorPreguntasNuevas = 0;      // Contador para preguntas
        var contadorSecciones = 0;           // Contador para secciones
        var contadorSubsecciones = {};       // Contador de subsecciones por sección
        var nivelActual = '';   
        
        // Evento para agregar una NUEVA PREGUNTA
        $('#nueva_pregunta').click(function () {
            agregarNuevaPregunta(); 
        });

        // Evento para agregar una NUEVA SECCIÓN
        $('#nueva_seccion').click(function () {
            contadorSecciones++; 
            nivelActual = `${contadorSecciones}`; 
            contadorSubsecciones[nivelActual] = 0; 
            agregarSeccion(nivelActual, `Sección`, 'Sección');
        });

        // Evento para agregar una NUEVA SUBSECCIÓN
        $('#nueva_subseccion').click(function () {
            if (!nivelActual) {
                alert('Primero debes crear una sección.');
                return;
            }
            contadorSubsecciones[nivelActual]++;
            var nivelSubseccion = `${nivelActual}.${contadorSubsecciones[nivelActual]}`;
            agregarSeccion(nivelSubseccion, `Subsección`, 'Subsección');
        });

        // Función para agregar Sección o Subsección
        function agregarSeccion(nivel, tipo) {
            var seccionHtml = `
                <div class="card mb-1 bg-light shadow-sm" id="seccion_${nivel}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Input editable para el nombre (aquí va tu "1.1", etc.) -->
                        <input type="text" class="form-control fw-bold text-primary border-0 bg-light" 
                            name="pregunta[]" 
                            value="${nivel}. ${tipo} ..."  
                            placeholder="Escribe el nombre aquí..." 
                            style="font-size: 1.2rem;">
                        
                        <!-- Botón para eliminar -->
                        <button type="button" class="btn btn-danger btn-sm eliminar-seccion" data-id="${nivel}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                    
                    <!-- Inputs ocultos -->
                    <input type="hidden" name="tipoRespuesta[]" value="${tipo}">
                    <!-- AQUI pone lo que sea (nivel), luego se sobreescribe con número correlativo -->
                    <input type="hidden" name="RBF_orden[]" value="${contadorPreguntasNuevas}">
                </div>
            `;
            $('#contenedor_pregunta_seleccionada').append(seccionHtml);

            // Opcional: forzar la actualización para reenumerar de inmediato
            //actualizarOrdenEnDOM();
        }
        
        // Evento para ELIMINAR SECCIÓN o SUBSECCIÓN
        $(document).on('click', '.eliminar-seccion', function () {
            var id = $(this).data('id').toString();
            var escapedId = id.replace(/\./g, '\\.');
            
            if (confirm(`¿Seguro que deseas eliminar la sección ${id}?`)) {
                $(`#seccion_${escapedId}`).remove();
            }
            actualizarContadoresSecciones();
        });

        // Función para actualizar contadores después de eliminar
        function actualizarContadoresSecciones() {
            contadorSecciones = 0;      
            contadorSubsecciones = {}; 
            
            $('#contenedor_pregunta_seleccionada .card').each(function () {
                var id = $(this).attr('id').replace('seccion_', '');
                if (id.indexOf('.') === -1) { // Es una sección
                    contadorSecciones++;
                    contadorSubsecciones[contadorSecciones] = 0; 
                } else { // Subsección
                    var partes = id.split('.');
                    var seccion = partes[0];
                    if (contadorSubsecciones[seccion] !== undefined) {
                        contadorSubsecciones[seccion]++;
                    }
                }
            });
        }

        // *** ACTIVA EL SORTABLE PARA TODO EL .card ***
        $('#contenedor_pregunta_seleccionada').sortable({
            placeholder: "ui-state-highlight",
            start: function (event, ui) {
                ui.item.css("border", "2px solid #007bff");
                ui.placeholder.height(ui.item.height());
                ui.placeholder.css("background-color", "#f0f8ff");
            },
            stop: function (event, ui) {
                ui.item.css("border", "1px solid #dee2e6");
                ui.item.effect("highlight", { color: "#d4edda" }, 1000);
                reenumerarPreguntasVisuales();
            },
            update: function(event, ui) {
                reenumerarPreguntasVisuales(); // Asegura actualización inmediata
            }
        });
        

        // Función mejorada para reenumerar preguntas
        function reenumerarPreguntasVisuales() {
            let contadorPreguntas = 0;
            $('#contenedor_pregunta_seleccionada .card').each(function() {
                // Verifica si no es una sección o subsección
                if (!$(this).find('input[name="tipoRespuesta[]"][value="Sección"]').length && 
                    !$(this).find('input[name="tipoRespuesta[]"][value="Subsección"]').length) {
                    contadorPreguntas++;
                    $(this).find('.numero-pregunta').text(contadorPreguntas);
                }
            });
        }
        
        // *** FUNCION QUE SOBRESCRIBE RBF_orden[] con un número simple 1, 2, 3, ...
        // function actualizarOrdenEnDOM() {
        //     $('#contenedor_pregunta_seleccionada .card').each(function(index) {
        //         $(this).find('input[name="RBF_orden[]"]').val(index + 1);
        //     });
        // }
            
        // Función para agregar una nueva pregunta
        function agregarNuevaPregunta() {
            contadorPreguntasNuevas++;
            var pregunta = `
                <div class="card mb-2 ms-4" id="card_pregunta_${contadorPreguntasNuevas}">
                    <div class="card-header d-flex align-items-center">
                        <span class="numero-pregunta me-2 d-flex justify-content-center align-items-center rounded-circle border shadow-sm"
                            name="RBF_orden[]"
                            style="width: 40px; height: 40px; font-size: 18px; font-weight: bold; cursor: grab; background-color: #f8f9fa;"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Arrastra para mover">${contadorPreguntasNuevas}</span>
                        <div class="input-group-text fs-5" id="icono_pregunta_${contadorPreguntasNuevas}"></div>
                        <input 
                            class="form-control pregunta col"
                            type="text"
                            id="pregunta_${contadorPreguntasNuevas}"
                            placeholder="Inserte la pregunta"
                            name="pregunta[]" 
                        />
                    </div>
                    <div class="card-body">
                        <!-- Botones de tipo respuesta -->
                        <div class="btn-group d-flex tipo-respuesta">
                            <button type="button" class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_varias_${contadorPreguntasNuevas}">
                                <i class="bi bi-list-check"></i> Varias opciones
                            </button>
                            <button type="button" class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_unica_${contadorPreguntasNuevas}">
                                <i class="bi bi-ui-radios-grid"></i> Única opción
                            </button>
                            <button type="button" class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_texto_${contadorPreguntasNuevas}">
                                <i class="bi bi-chat-left-text"></i> Texto
                            </button>
                            <button type="button" class="col btn btn-outline-secondary btn-tipo-respuesta" id="btn_numero_${contadorPreguntasNuevas}">
                                <i class="bi bi-123"></i> Numérico
                            </button>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-warning btn-sm eliminar-pregunta box-shadow" 
                                id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>`;
            $('#contenedor_pregunta_seleccionada').append(pregunta);
            reenumerarPreguntasVisuales();
        }

        // Agregar una nueva opción dentro de una pregunta
        $(document).on('click', '.btn-add-opcion', function () {
            var id = $(this).data('id');
            var numOpciones = $(`#opciones_${id} .input-group`).length + 1;
            var nuevaOpcion = `
                <div class="input-group mb-2">
                    <input type="text" class="form-control" placeholder="Opción ${numOpciones}" name="opciones_${id}[]">
                    <button type="button" class="btn btn-outline-danger btn-remove-opcion">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>`;
            $(`#opciones_${id}`).append(nuevaOpcion);
        });

        // Eliminar una opción
        $(document).on('click', '.btn-remove-opcion', function () {
            $(this).closest('.input-group').remove();
        });
      
        // Evento para seleccionar el tipo de respuesta
        $(document).on('click', '.btn-tipo-respuesta', function () {
            var id = $(this).attr('id');
            var tipoRespuesta = id.split('_')[1];
            var contadorId = id.split('_')[2];

            var ultimaPregunta = $('#contenedor_pregunta_seleccionada .card:last');
            var contadorPreguntasNuevas = parseInt(ultimaPregunta.attr('id').split('_')[2]);
            var icono = $(this).children('i').clone();
            $('#icono_pregunta_' + contadorId).empty().append(icono);

            // Crear campos adicionales según el tipo
            if (tipoRespuesta === 'varias' || tipoRespuesta === 'unica') {
                crearPreguntaConOpciones(contadorId, tipoRespuesta, contadorPreguntasNuevas);
            } else {
                crearPreguntaEspecifica(contadorId, tipoRespuesta, contadorPreguntasNuevas);
            }
        });
        
        // Evento para aceptar preguntas (ejemplo)
        $(document).on('click', '.aceptar-pregunta', function () {
            var id = $(this).data('id');
            console.log("Pregunta aceptada con ID:", id);
        });
    });
    
    // Función para crear múltiples opciones (checkboxes)
    function crearPreguntaConOpciones(contadorId, tipoRespuesta, contadorPreguntasNuevas) {
        var tipoRespuestaTexto = '';
        if (tipoRespuesta === 'varias') {
            tipoRespuestaTexto = 'Casilla verificación';
        } else if (tipoRespuesta === 'unica') {
            tipoRespuestaTexto = 'Lista desplegable';
        } else {
            tipoRespuestaTexto = tipoRespuesta.charAt(0).toUpperCase() + tipoRespuesta.slice(1);
        }
        var opcionesHtml = `
            <div id="opciones_${contadorId}">
                <label class="form-label fw-bold mb-2 text-primary text-start">
                    ${tipoRespuesta === 'unica' ? 'Respuesta con selección única' : 'Respuesta con selección múltiple'} 
                </label>
                
                <div class="input-group mb-2">
                    <input type="text" class="form-control" placeholder="Opción 1" name="opciones_${contadorId}[]">
                    <button type="button" class="btn btn-outline-success btn-add-opcion" data-id="${contadorId}">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                </div>
            </div> 

            <hr class="my-3">

            <div id="complemento_container_${contadorId}" class="mt-3">
                <div class="text-end">
                    <button type="button" class="btn btn-outline-primary btn-sm btn-add-complemento" data-id="${contadorId}">
                        <i class="bi bi-plus-square"></i> Agregar complemento a la respuesta
                    </button>
                </div>
            </div> 
            
            <!-- Campos ocultos -->
            <input type="hidden" id="tipoRespuesta_${contadorId}" name="tipoRespuesta[]" value="${tipoRespuestaTexto}">
            <input type="hidden" name="RBF_orden[]" value="${contadorPreguntasNuevas}">
        `;
        $(`#card_pregunta_${contadorId} .card-body`).html(opcionesHtml);
    }

    // Evento para agregar el complemento
    $(document).on('click', '.btn-add-complemento', function () {
        var id = $(this).data('id'); 
        var complementoContainer = $(`#complemento_container_${id}`);

        if (complementoContainer.children('.input-group').length === 0) {
            var complementoHtml = `
                <div class="input-group mt-2">
                    <span class="input-group-text">Complemento:</span>
                    <input type="text" class="form-control" placeholder="Añadir complemento" name="BCP_complemento_${id}">
                    <button type="button" class="btn btn-outline-danger btn-remove-complemento">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            `;
            complementoContainer.append(complementoHtml); 
        }
    });

    // Evento para eliminar el complemento
    $(document).on('click', '.btn-remove-complemento', function () {
        $(this).closest('.input-group').remove();
    });
        
    function crearPreguntaEspecifica(contadorId, tipoRespuesta, contadorPreguntasNuevas) {
        let inputHtml = '';

        switch (tipoRespuesta) {
            case 'texto':
                inputHtml = `
                    <label class="form-label fw-bold mb-2 text-primary text-start">Respuesta de texto</label>
                    <input type="text" class="form-control" name="respuesta_${contadorId}" 
                           placeholder="En este espacio se ingresará la respuesta de texto" disabled />
                    <input type="hidden" id="tipoRespuesta_${contadorId}" name="tipoRespuesta[]" value="Respuesta corta" />
                    <input type="hidden" name="RBF_orden[]" value="${contadorPreguntasNuevas}">
                `;
                break;

            case 'numero':
                inputHtml = `
                    <label class="form-label fw-bold mb-2 text-primary text-start">Respuesta numérica</label>
                    <input type="number" class="form-control" name="respuesta_${contadorId}" 
                           placeholder="En este espacio se ingresará las respuestas numéricas" disabled />
                    <input type="hidden" id="tipoRespuesta_${contadorId}" name="tipoRespuesta[]" value="Numeral" />
                    <input type="hidden" name="RBF_orden[]" value="${contadorPreguntasNuevas}">
                `;
                break;

            default:
                console.log("Tipo de respuesta no definido");
                return;
        }

        inputHtml += `
            <hr>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-outline-primary btn-sm btn-add-complemento" data-id="${contadorId}">
                    <i class="bi bi-plus-square"></i> Agregar complemento
                </button>
            </div>
            <div id="complemento_container_${contadorId}" class="mt-3"></div>
        `;
        $(`#card_pregunta_${contadorId} .card-body`).html(inputHtml);
    }

    // Evento para eliminar una pregunta completa
    $(document).on('click', '.eliminar-pregunta', function () {
        var idPregunta = $(this).data('id');
        var numeroPregunta = $(`#card_pregunta_${idPregunta} .numero-pregunta`).text().trim();
        
        if (confirm(`¿Estás seguro de que deseas eliminar la pregunta número ${numeroPregunta}?`)) {
            $(`#card_pregunta_${idPregunta}`).fadeOut(300, function () {
                $(this).remove();
            });
        }
        reenumerarPreguntasVisuales();
    });
    function ensamblarDatos() {
        let arrayPreguntas = [];
        
        $('#contenedor_pregunta_seleccionada .card').each(function(index) {
            let obj = {};
            
            // Obtener el texto de la pregunta o sección/subsección
            let textoPregunta = $(this).find('input[name="pregunta[]"]').val().trim();

            // Obtener el tipo de respuesta
            let tipoRespuesta = $(this).find('input[name="tipoRespuesta[]"]').val().trim();
            
            // Obtener el número de pregunta visible (solo para preguntas normales)
            let numeroPregunta = $(this).find('.numero-pregunta').text().trim();

            // Concatenar número solo si NO es sección o subsección
            if (tipoRespuesta.toLowerCase() === 'sección' || tipoRespuesta.toLowerCase() === 'subsección') {
                obj.BCP_pregunta = textoPregunta; // Dejar solo el texto
            } else {
                obj.BCP_pregunta = `${numeroPregunta}. ${textoPregunta}`; // Agregar número
            }

            // Obtener el orden (número correlativo)
            obj.RBF_orden = index + 1; // Orden correlativo (1, 2, 3, ...)

            // Construir las opciones en formato JSON
            let opciones = {};
            $(this).find('input[name^="opciones_"]').each(function(idx) {
                let valorOpcion = $(this).val();
                if (valorOpcion.trim() !== '') {
                    opciones[idx.toString()] = valorOpcion.trim();
                }
            });
            obj.BCP_opciones = JSON.stringify(opciones); // Opciones como JSON
            if (Object.keys(opciones).length === 0) {
                obj.BCP_opciones = null; // Si no hay opciones, guardar como null
            }

            // Obtener complemento (si existe)
            obj.BCP_complemento = $(this).find('input[name^="BCP_complemento_"]').val() || null;

            // Obtener tipo de respuesta
            obj.BCP_tipoRespuesta = tipoRespuesta;
            
            // Agregar objeto al array de preguntas
            arrayPreguntas.push(obj);
        });
        
        // Guardar el JSON ensamblado en un input oculto para enviarlo al servidor
        $('#listaPreguntasJSON').val(JSON.stringify(arrayPreguntas));
        
        console.log(arrayPreguntas); // Para depurar el resultado en consola
    }
    
    function validarFormulario() {
        let valido = true;
        let totalPreguntas = 0; // Contador para el total de preguntas
        
        // Validar el título del formulario
        let titulo = $('#titulo_formulario');
        if (!titulo.val().trim()) {
            titulo.addClass('is-invalid');
            titulo.closest('.mb-4').addClass('is-invalid'); // Marca el contenedor
            Swal.fire({
                icon: 'error',
                text: 'El título del formulario no puede estar vacío.',
                confirmButtonColor: '#3085d6'
            });
            valido = false;
            return false;
        } else {
            titulo.removeClass('is-invalid');
            titulo.closest('.mb-4').removeClass('is-invalid');
        }
        
        // Validar tipo de formulario
        if (!$("input[name='FRM_tipo']:checked").length) {
            $("input[name='FRM_tipo']").closest('.d-flex').addClass('is-invalid');
            valido = false;
            Swal.fire({
                icon: 'error',
                text: 'Debe seleccionar si el formulario se aplicará más de una vez.',
                confirmButtonColor: '#3085d6'
            });
            return false;
        } else {
            $("input[name='FRM_tipo']").closest('.d-flex').removeClass('is-invalid');
        }

        // Validar preguntas
        // $('#contenedor_pregunta_seleccionada .card').each(function() {
        //     totalPreguntas++; // Incrementar por cada pregunta encontrada
        //     let mensajeError = ''; // Variable para almacenar el mensaje de error específico

        //     // Verificar si el input de la pregunta está vacío
        //     let inputPregunta = $(this).find('input[name="pregunta[]"]');
        //     if (!inputPregunta.val().trim()) {
        //         inputPregunta.addClass('is-invalid');
        //         valido = false;
        //         mensajeError = 'Debe completar las preguntas';
        //     } else {
        //         inputPregunta.removeClass('is-invalid');
        //     }
            
        //     // Verificar opciones, si existen
        //     $(this).find('input[name^="opciones_"]').each(function() {
        //         if (!$(this).val().trim()) {
        //             $(this).addClass('is-invalid');
        //             valido = false;
        //             mensajeError = 'Debe completar las opciones de la pregunta.';
        //         } else {
        //             $(this).removeClass('is-invalid');
        //         }
        //     });

        //     // Verificar complementos, si existen
        //     $(this).find('input[name^="BCP_complemento_"]').each(function() {
        //         if (!$(this).val().trim()) {
        //             $(this).addClass('is-invalid');
        //             valido = false;
        //             mensajeError = 'Debe completar el/los complementos de la pregunta.';

        //         } else {
        //             $(this).removeClass('is-invalid');
        //         }
        //     });
        //     if (!$("input[name^='FRM_tipo']:checked").val()) {
        //         $(this).addClass('is-invalid');
        //         valido = false;
        //         mensajeError = 'Debe seleccionar si el formulario se aplicará más de una vez.';
        //      event.preventDefault();
        //     }else{
        //         $(this).removeClass('is-invalid');
        //     }
            
        //     if(!valido){
        //         Swal.fire({
        //             icon: 'error',
        //             text: mensajeError,
        //             confirmButtonColor: '#3085d6'
        //         });
        //     }
        // });
        
        // Validar preguntas
        $('#contenedor_pregunta_seleccionada .card').each(function() {
            let cardElement = $(this);
            let tipoRespuesta = cardElement.find('input[name="tipoRespuesta[]"]').val();
            
            // No contar secciones y subsecciones como preguntas
            if (tipoRespuesta !== 'Sección' && tipoRespuesta !== 'Subsección') {
                totalPreguntas++;
            }

            // Validar texto de la pregunta
            let inputPregunta = cardElement.find('input[name="pregunta[]"]');
            if (!inputPregunta.val().trim()) {
                inputPregunta.addClass('is-invalid');
                cardElement.addClass('is-invalid'); // Marca toda la tarjeta
                valido = false;
            } else {
                inputPregunta.removeClass('is-invalid');
                cardElement.removeClass('is-invalid');
            }

            // Validar opciones si existen
            let opcionesContainer = cardElement.find('.input-group');
            opcionesContainer.each(function() {
                let opcionInput = $(this).find('input[type="text"]');
                if (opcionInput.length && !opcionInput.val().trim()) {
                    opcionInput.addClass('is-invalid');
                    $(this).addClass('is-invalid'); // Marca el contenedor de la opción
                    valido = false;
                } else {
                    opcionInput.removeClass('is-invalid');
                    $(this).removeClass('is-invalid');
                }
            });

            // Validar complementos
            let complementoInput = cardElement.find('input[name^="BCP_complemento_"]');
            if (complementoInput.length && !complementoInput.val().trim()) {
                complementoInput.addClass('is-invalid');
                complementoInput.closest('.input-group').addClass('is-invalid');
                valido = false;
            } else if (complementoInput.length) {
                complementoInput.removeClass('is-invalid');
                complementoInput.closest('.input-group').removeClass('is-invalid');
            }
        });
        // Verificar si hay al menos 3 preguntas
        if (totalPreguntas < 2) {
            Swal.fire({
                icon: 'error',
                text: 'Debe agregar al menos 2 preguntas al formulario.',
                confirmButtonColor: '#3085d6'
            });
            valido = false;
        }
        return valido; // Retorna false si hay errores
    }

    // Agregar estilos CSS necesarios
    const style = document.createElement('style');
    style.textContent = `
        .card.is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
        }
        .input-group.is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
        }
        .d-flex.is-invalid {
            padding: 0.375rem;
            border: 1px solid #dc3545;
            border-radius: 0.25rem;
        }
    `;
    document.head.appendChild(style);
</script>
@endsection

