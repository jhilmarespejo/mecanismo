{{-- FORMULARIO PARA CREAR NUEVO CUESTIONARIO formulario-nuevo.php  --}}
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
                
                <!-- Contenedor principal para elementos del formulario (secciones, subsecciones y preguntas) -->
                <!-- IMPORTANTE: Este contenedor es sortable para permitir reordenación por drag & drop -->
                <div id="contenedor_pregunta_seleccionada" class="sortable mb-3">
                    <!-- Input oculto para configurar tipo de formulario (siempre múltiple aplicación) -->
                    <input type="hidden" name="FRM_tipo" id="FRM_tipo_si" value="N">
                    <!-- Las preguntas dinámicas se agregarán aquí mediante JavaScript -->
                </div>
                
                <!-- Botón para guardar el formulario -->
                <hr>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <!-- Botón para guardar formulario -->
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-save"></i> Guardar Formulario
                    </button>
                
                    <!-- Botones para agregar elementos del formulario -->
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
                
                <!-- Input oculto que contendrá el JSON final de todas las preguntas, secciones y subsecciones -->
                <input type="hidden" name="listaPreguntasJSON" id="listaPreguntasJSON">
            
            </form>
        </div>
    </div>
    
</div>

<script>
    
    $(document).ready(function () {
        
        /* ========================================================================
         * VALIDACIÓN Y ENVÍO DEL FORMULARIO PRINCIPAL
         * ======================================================================== */
        
        // Interceptar el envío del formulario para validar antes de enviar
        $('#formularioPrincipal').on('submit', function(e) {
            if (!validarFormulario()) {
                e.preventDefault(); // Detiene el envío del formulario si hay errores
            } else {
                ensamblarDatos(); // Solo ensambla datos si el formulario es válido
            }
        });
        
        /* ========================================================================
         * VARIABLES GLOBALES PARA MANEJO DE CONTADORES
         * ======================================================================== */
        
        //Variables globales para el manejo de numeración y jerarquía
        var contadorPreguntasNuevas = 0;      // Contador para preguntas
        var contadorSecciones = 0;           // Contador para secciones
        var contadorSubsecciones = {};       // Contador de subsecciones por sección
        var nivelActual = '';               // Mantiene el nivel actual de sección
        
        /* ========================================================================
         * EVENT HANDLERS PARA BOTONES PRINCIPALES
         * ======================================================================== */
        
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

        /* ========================================================================
         * FUNCIONES PARA CREAR SECCIONES Y SUBSECCIONES
         * ======================================================================== */

        /**
         * Función para agregar Sección o Subsección
         * @param {string} nivel - Nivel jerárquico (ej: "1", "1.1", "1.2")
         * @param {string} tipo - Tipo de elemento ("Sección" o "Subsección")
         */
        function agregarSeccion(nivel, tipo) {
            var claseSubseccion = (tipo === 'Subsección') ? 'ms-3' : '';
            
            var seccionHtml = `
                    <div class="card mb-1 bg-light shadow-sm ${claseSubseccion}" id="seccion_${nivel}">
                        <div class="card-header d-flex justify-content-between align-items-center ">
                            <!-- Input editable para el nombre (aquí va tu "1.1", etc.) -->
                            <input type="text" class="form-control fw-bold text-primary border-0 bg-light py-0 w-75" 
                                name="pregunta[]" 
                                value="${nivel}. ${tipo} ..."  
                                placeholder="Escribe el nombre aquí..." 
                                style="font-size: 1.2rem;">
                            
                            <!-- Botón para eliminar -->
                            <button type="button" class="btn btn-danger btn-sm eliminar-seccion ms-3" data-id="${nivel}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                        
                        <!-- Inputs ocultos para identificar el tipo y orden -->
                        <input type="hidden" name="tipoRespuesta[]" value="${tipo}">
                        <input type="hidden" name="RBF_orden[]" value="${contadorPreguntasNuevas}">
                    </div>
                `;
            
            $('#contenedor_pregunta_seleccionada').append(seccionHtml);
        }
        
        /* ========================================================================
         * EVENT HANDLERS PARA ELIMINACIÓN DE ELEMENTOS
         * ======================================================================== */
        
        // Evento para ELIMINAR SECCIÓN o SUBSECCIÓN con confirmación
        $(document).on('click', '.eliminar-seccion', function () {
            var id = $(this).data('id').toString();
            var escapedId = id.replace(/\./g, '\\.');

            Swal.fire({
                title: `¿Estás seguro?`,
                text: `Vas a eliminar esta sección. Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#seccion_${escapedId}`).remove();
                    actualizarContadoresSecciones();
                }
            });
        });

        /**
         * Función para actualizar contadores después de eliminar secciones/subsecciones
         * Recalcula los contadores basándose en los elementos DOM existentes
         */
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

        /* ========================================================================
         * CONFIGURACIÓN DE SORTABLE (Drag & Drop)
         * ======================================================================== */

        // Habilitar drag & drop para reordenar elementos del formulario
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
        
        /**
         * Función para reenumerar preguntas después del drag & drop
         * Solo numera las preguntas, no las secciones ni subsecciones
         */
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
            
        /* ========================================================================
         * FUNCIONES PARA CREAR PREGUNTAS
         * ======================================================================== */

        /**
         * Función principal para agregar una nueva pregunta
         * Crea la estructura base con los botones de tipo de respuesta
         */
        function agregarNuevaPregunta() {
            contadorPreguntasNuevas++;
            var pregunta = `
                <div class="card mb-2 ms-5" id="card_pregunta_${contadorPreguntasNuevas}">
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
                    <div class="card-body py-0">
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
                            <button type="button" class="col btn btn-outline-danger eliminar-pregunta " style=""
                                    id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                    
                </div>`;
            $('#contenedor_pregunta_seleccionada').append(pregunta);
            reenumerarPreguntasVisuales();
        }

        /* ========================================================================
         * EVENT HANDLERS PARA OPCIONES DINÁMICAS
         * ======================================================================== */

        // Agregar una nueva opción dentro de una pregunta de múltiple opción
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

        // Eliminar una opción específica
        $(document).on('click', '.btn-remove-opcion', function () {
            $(this).closest('.input-group').remove();
        });
      
        /* ========================================================================
         * EVENT HANDLER PRINCIPAL PARA SELECCIÓN DE TIPO DE RESPUESTA
         * ======================================================================== */

        /**
         * EVENTO CRÍTICO: Maneja la selección del tipo de respuesta
         * MODIFICACIÓN IMPORTANTE: Se mantiene el botón "Eliminar" después de la selección
         */
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
        
        // Evento para aceptar preguntas (funcionalidad futura)
        $(document).on('click', '.aceptar-pregunta', function () {
            var id = $(this).data('id');
            console.log("Pregunta aceptada con ID:", id);
        });
    });
    
    /* ========================================================================
     * FUNCIONES PARA CREAR DIFERENTES TIPOS DE PREGUNTAS
     * ======================================================================== */

    /**
     * Función para crear múltiples opciones (checkboxes o radio buttons)
     * MODIFICACIÓN CRÍTICA: Preserva el botón eliminar después de generar el contenido
     * @param {number} contadorId - ID único del elemento
     * @param {string} tipoRespuesta - Tipo de respuesta ('varias' o 'unica')
     * @param {number} contadorPreguntasNuevas - Contador global de preguntas
     */
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
        
        // *** MODIFICACIÓN CRÍTICA: Preservar el botón eliminar ***
        // Guardar el botón eliminar antes de reemplazar el contenido
        var botonEliminar = $(`#eliminar_pregunta_${contadorId}`).closest('.btn-group');
        
        // Aplicar el nuevo contenido
        $(`#card_pregunta_${contadorId} .card-body`).html(opcionesHtml);
        
        // Volver a agregar el botón eliminar al final
        $(`#card_pregunta_${contadorId} .card-body`).append('<hr class="my-2">').append(botonEliminar);
    }

    /* ========================================================================
     * EVENT HANDLERS PARA COMPLEMENTOS
     * ======================================================================== */

    // Evento para agregar el complemento a una pregunta
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
        
    /**
     * Función para crear preguntas específicas (texto o número)
     * MODIFICACIÓN CRÍTICA: Preserva el botón eliminar después de generar el contenido
     * @param {number} contadorId - ID único del elemento
     * @param {string} tipoRespuesta - Tipo de respuesta ('texto' o 'numero')
     * @param {number} contadorPreguntasNuevas - Contador global de preguntas
     */
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
        
        // *** MODIFICACIÓN CRÍTICA: Preservar el botón eliminar ***
        // Guardar el botón eliminar antes de reemplazar el contenido
        var botonEliminar = $(`#eliminar_pregunta_${contadorId}`).closest('.btn-group');
        
        // Aplicar el nuevo contenido
        $(`#card_pregunta_${contadorId} .card-body`).html(inputHtml);
        
        // Volver a agregar el botón eliminar al final
        $(`#card_pregunta_${contadorId} .card-body`).append('<hr class="my-2">').append(botonEliminar);
    }

    /* ========================================================================
     * EVENT HANDLERS PARA ELIMINACIÓN DE PREGUNTAS
     * ======================================================================== */

    /**
     * Evento para eliminar una pregunta completa con confirmación
     * Incluye SweetAlert para mejor UX
     */
    $(document).on('click', '.eliminar-pregunta', function () {
            var idPregunta = $(this).data('id');
            var numeroPregunta = $(`#card_pregunta_${idPregunta} .numero-pregunta`).text().trim();

            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a eliminar la pregunta número ${numeroPregunta}. Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#card_pregunta_${idPregunta}`).fadeOut(300, function () {
                        $(this).remove();
                        reenumerarPreguntasVisuales();
                    });
                    Swal.fire('Eliminado', `La pregunta número ${numeroPregunta} ha sido eliminada.`, 'success');
                }
            });
        });

    /* ========================================================================
     * FUNCIONES DE ENSAMBLADO Y VALIDACIÓN FINAL
     * ======================================================================== */

    /**
     * Función para ensamblar todos los datos en formato JSON antes del envío
     * Recorre todos los elementos del DOM y construye el array final
     */
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
    
    /**
     * Función de validación completa del formulario antes del envío
     * Valida título, preguntas mínimas, campos obligatorios y opciones
     * Incluye mensajes de error específicos para cada campo
     * @returns {boolean} - true si el formulario es válido, false en caso contrario
     */
    function validarFormulario() {
        let valido = true;
        let totalPreguntas = 0; // Contador para el total de preguntas
        let erroresEncontrados = []; // Array para acumular errores
        
        // Limpiar mensajes de error anteriores
        $('.error-message').remove();
        $('.is-invalid').removeClass('is-invalid');
        
        // Validar el título del formulario
        let titulo = $('#titulo_formulario');
        if (!titulo.val().trim()) {
            titulo.addClass('is-invalid');
            titulo.closest('.mb-4').addClass('is-invalid');
            
            // Agregar mensaje de error específico para el título
            if (!titulo.next('.error-message').length) {
                titulo.after('<div class="error-message text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>El título del formulario es obligatorio</div>');
            }
            
            erroresEncontrados.push('Título del formulario faltante');
            valido = false;
        } else {
            titulo.removeClass('is-invalid');
            titulo.closest('.mb-4').removeClass('is-invalid');
            titulo.next('.error-message').remove();
        }
        
        // Validar preguntas (conteo y completitud)
        $('#contenedor_pregunta_seleccionada .card').each(function(index) {
            let cardElement = $(this);
            let tipoRespuesta = cardElement.find('input[name="tipoRespuesta[]"]').val();
            let numeroElemento = index + 1;
            let tipoElemento = tipoRespuesta || 'Elemento';
            
            // No contar secciones y subsecciones como preguntas
            if (tipoRespuesta !== 'Sección' && tipoRespuesta !== 'Subsección') {
                totalPreguntas++;
            }

            // Validar texto de la pregunta/sección/subsección
            let inputPregunta = cardElement.find('input[name="pregunta[]"]');
            if (!inputPregunta.val().trim()) {
                inputPregunta.addClass('is-invalid');
                cardElement.addClass('is-invalid');
                
                // Agregar mensaje específico según el tipo de elemento
                if (!inputPregunta.next('.error-message').length) {
                    let mensajeError = '';
                    if (tipoRespuesta === 'Sección') {
                        mensajeError = `<div class="error-message text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>El nombre de la sección es obligatorio</div>`;
                    } else if (tipoRespuesta === 'Subsección') {
                        mensajeError = `<div class="error-message text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>El nombre de la subsección es obligatorio</div>`;
                    } else {
                        mensajeError = `<div class="error-message text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>El texto de la pregunta es obligatorio</div>`;
                    }
                    inputPregunta.after(mensajeError);
                }
                
                erroresEncontrados.push(`${tipoElemento} ${numeroElemento}: Texto faltante`);
                valido = false;
            } else {
                inputPregunta.removeClass('is-invalid');
                inputPregunta.next('.error-message').remove();
            }

            // Validar opciones si existen (solo para preguntas con opciones)
            let opcionesContainer = cardElement.find('input[name^="opciones_"]');
            if (opcionesContainer.length > 0) {
                let opcionesVacias = 0;
                
                opcionesContainer.each(function() {
                    let opcionInput = $(this);
                    if (!opcionInput.val().trim()) {
                        opcionInput.addClass('is-invalid');
                        
                        // Agregar mensaje de error para opción vacía
                        if (!opcionInput.closest('.input-group').find('.error-message').length) {
                            opcionInput.closest('.input-group').after('<div class="error-message text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>Esta opción no puede estar vacía</div>');
                        }
                        
                        opcionesVacias++;
                        valido = false;
                    } else {
                        opcionInput.removeClass('is-invalid');
                        opcionInput.closest('.input-group').next('.error-message').remove();
                    }
                });
                
                if (opcionesVacias > 0) {
                    erroresEncontrados.push(`Pregunta ${totalPreguntas}: ${opcionesVacias} opción(es) vacía(s)`);
                }
            }
            
            // Validar complementos si existen
            let complementoInput = cardElement.find('input[name^="BCP_complemento_"]');
            if (complementoInput.length && !complementoInput.val().trim()) {
                complementoInput.addClass('is-invalid');
                
                // Agregar mensaje de error para complemento vacío
                if (!complementoInput.next('.error-message').length) {
                    complementoInput.parent.after('<div class="error-message text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>El complemento no puede estar vacío</div>');
                }
                
                erroresEncontrados.push(`Pregunta ${totalPreguntas}: Complemento vacío`);
                valido = false;
            } else if (complementoInput.length) {
                complementoInput.removeClass('is-invalid');
                complementoInput.next('.error-message').remove();
            }
        });
        
        // Verificar si hay al menos 2 preguntas (mínimo requerido)
        if (totalPreguntas < 2) {
            erroresEncontrados.push('Mínimo 2 preguntas requeridas');
            valido = false;
        }
        
        // Mostrar resumen de errores si hay alguno
        if (!valido) {
            let mensajeResumen = `Se encontraron ${erroresEncontrados.length} error(es):\n\n`;
            erroresEncontrados.slice(0, 5).forEach((error, index) => {
                mensajeResumen += `${index + 1}. ${error}\n`;
            });
            
            if (erroresEncontrados.length > 5) {
                mensajeResumen += `\n... y ${erroresEncontrados.length - 5} error(es) más.`;
            }
            
            mensajeResumen += '\n\nRevise los campos marcados en rojo y corrija los errores antes de continuar.';
            
            Swal.fire({
                icon: 'error',
                title: 'Errores en el formulario',
                text: mensajeResumen,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            }).then(() => {
                // Scroll al primer error encontrado
                const primerError = $('.is-invalid').first();
                if (primerError.length) {
                    $('html, body').animate({
                        scrollTop: primerError.offset().top - 100
                    }, 500);
                    primerError.focus();
                }
            });
        }
        
        return valido; // Retorna false si hay errores
    }

    /* ========================================================================
     * ESTILOS CSS DINÁMICOS PARA VALIDACIÓN
     * ======================================================================== */

    // Agregar estilos CSS necesarios para la validación visual
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
        /* Estilos para mensajes de error */
        .error-message {
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            animation: fadeInError 0.3s ease-in-out;
        }
        .error-message i {
            flex-shrink: 0;
        }
        @keyframes fadeInError {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* Estilo mejorado para inputs inválidos */
        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4 1.4-1.4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        /* Estilos adicionales para mejorar la experiencia visual */
        .btn-group .btn {
            transition: all 0.2s ease;
        }
        .btn-group .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .sortable .card {
            transition: transform 0.2s ease;
        }
        .sortable .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        /* Indicador visual para campos obligatorios */
        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        /* Estilo para el resaltado de errores */
        .card.is-invalid {
            position: relative;
        }
        .card.is-invalid::before {
            content: '!';
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            z-index: 10;
        }
    `;
    document.head.appendChild(style);
</script>
@endsection