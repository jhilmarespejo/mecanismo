/**
 * Script para la edición de formularios
 */
$(document).ready(function() {
    // Verifica que jQuery esté disponible
    if (typeof jQuery === 'undefined') {
        console.error('jQuery no está cargado. Verifica tus dependencias.');
        return;
    }

    // Inicializar variables globales
    let contadorPreguntasNuevas = $('.card[id^="card_pregunta_existente_"]').length;
    let contadorSecciones = 0;
    let contadorSubsecciones = {};
    let nivelActual = '';
    let preguntasEliminar = [];

    // Inicializar sortable con jQuery UI
    $('#contenedor_pregunta_seleccionada').sortable({
        placeholder: "ui-state-highlight",
        handle: ".drag-handle",
        start: function(event, ui) {
            ui.item.css("border", "2px solid #007bff");
            ui.placeholder.height(ui.item.height());
        },
        stop: function(event, ui) {
            ui.item.css("border", "1px solid #dee2e6");
            ui.item.effect("highlight", { color: "#d4edda" }, 1000);
            reenumerarPreguntasVisuales();
            
            // Importante: actualizar el campo hidden después de reordenar
            ensamblarDatos();
        },
        update: function(event, ui) {
            reenumerarPreguntasVisuales();
            // También se puede llamar aquí, pero es preferible en stop para evitar duplicados
        }
    });
    // Evento para reenumerar preguntas visuales
    function reenumerarPreguntasVisuales() {
        let contador = 0;
        $('#contenedor_pregunta_seleccionada .card').each(function() {
            // Verifica si no es una sección o subsección
            let tipoRespuesta = $(this).find('input[name="tipoRespuesta[]"]').val();
            if (tipoRespuesta !== 'Sección' && tipoRespuesta !== 'Subsección') {
                contador++;
                $(this).find('.numero-pregunta').text(contador);
            }
        });
    }

    // Eventos para los botones principales
    $('#nueva_pregunta').on('click', function() {
        agregarNuevaPregunta();
    });

    $('#nueva_seccion').on('click', function() {
        contadorSecciones++;
        nivelActual = `${contadorSecciones}`;
        contadorSubsecciones[nivelActual] = 0;
        agregarSeccion(nivelActual, 'Sección');
    });

    $('#nueva_subseccion').on('click', function() {
        if (!nivelActual) {
            alert('Primero debes crear una sección.');
            return;
        }
        contadorSubsecciones[nivelActual]++;
        var nivelSubseccion = `${nivelActual}.${contadorSubsecciones[nivelActual]}`;
        agregarSeccion(nivelSubseccion, 'Subsección');
    });

    // Eventos delegados
    $(document).on('click', '.eliminar-pregunta-existente', function() {
        var rbfId = $(this).data('rbf-id');
        var bcpId = $(this).data('bcp-id');
        
        if (confirm('¿Estás seguro de que deseas eliminar esta pregunta?')) {
            preguntasEliminar.push(rbfId);
            $('#preguntasEliminar').val(JSON.stringify(preguntasEliminar));
            
            $(`#card_pregunta_existente_${bcpId}`).fadeOut(300, function() {
                $(this).remove();
                reenumerarPreguntasVisuales();
            });
        }
    });

    $(document).on('click', '.eliminar-pregunta', function() {
        var idPregunta = $(this).data('id');
        
        if (confirm('¿Estás seguro de que deseas eliminar esta pregunta?')) {
            $(`#card_pregunta_${idPregunta}`).fadeOut(300, function() {
                $(this).remove();
                reenumerarPreguntasVisuales();
            });
        }
    });

    $(document).on('click', '.eliminar-seccion', function() {
        var id = $(this).data('id').toString();
        var escapedId = id.replace(/\./g, '\\.');
        
        if (confirm(`¿Seguro que deseas eliminar la sección ${id}?`)) {
            $(`#seccion_${escapedId}`).remove();
            actualizarContadoresSecciones();
        }
    });

    $(document).on('click', '.btn-tipo-respuesta', function() {
        var id = $(this).attr('id');
        var tipoRespuesta = id.split('_')[1];
        var contadorId = id.split('_')[2];
        
        var icono = $(this).children('i').clone();
        $(`#icono_pregunta_${contadorId}`).empty().append(icono);
        
        if (tipoRespuesta === 'varias' || tipoRespuesta === 'unica') {
            crearPreguntaConOpciones(contadorId, tipoRespuesta);
        } else {
            crearPreguntaEspecifica(contadorId, tipoRespuesta);
        }
    });

    $(document).on('click', '.btn-add-opcion', function() {
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

    $(document).on('click', '.btn-remove-opcion', function() {
        $(this).closest('.input-group').remove();
    });

    $(document).on('click', '.btn-add-complemento', function() {
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

    $(document).on('click', '.btn-remove-complemento', function() {
        $(this).closest('.input-group').remove();
    });

    // Validación del formulario
    $('#formularioEditar').on('submit', function(e) {
        ensamblarDatos();
        if (!validarFormulario()) {
            e.preventDefault();
        } else {
            ensamblarDatos();
        }
    });

    // Funciones auxiliares
    function agregarNuevaPregunta() {
        contadorPreguntasNuevas++;
        var pregunta = `
            <div class="card mb-2 ms-4" id="card_pregunta_${contadorPreguntasNuevas}">
                <div class="card-header d-flex align-items-center">
                    <span class="numero-pregunta me-2 d-flex justify-content-center align-items-center rounded-circle border shadow-sm drag-handle"
                        style="width: 40px; height: 40px; font-size: 18px; font-weight: bold; background-color: #f8f9fa; cursor: grab;">
                        ${contadorPreguntasNuevas}
                    </span>
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
                    <button type="button" class="btn btn-warning btn-sm eliminar-pregunta" 
                            id="eliminar_pregunta_${contadorPreguntasNuevas}" data-id="${contadorPreguntasNuevas}">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </div>
            </div>`;
        $('#contenedor_pregunta_seleccionada').append(pregunta);
        reenumerarPreguntasVisuales();
    }

    function agregarSeccion(nivel, tipo) {
        contadorPreguntasNuevas++;
        var seccionHtml = `
            <div class="card mb-1 bg-light shadow-sm" id="seccion_${nivel}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <input type="text" class="form-control fw-bold text-primary border-0 bg-light" 
                        name="pregunta[]" 
                        value="${nivel}. ${tipo} ..."  
                        placeholder="Escribe el nombre aquí..." 
                        style="font-size: 1.2rem;">
                    
                    <button type="button" class="btn btn-danger btn-sm eliminar-seccion" data-id="${nivel}">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </div>
                
                <input type="hidden" name="tipoRespuesta[]" value="${tipo}">
                <input type="hidden" name="RBF_orden[]" value="${contadorPreguntasNuevas}">
            </div>
        `;
        $('#contenedor_pregunta_seleccionada').append(seccionHtml);
    }

    function crearPreguntaConOpciones(contadorId, tipoRespuesta) {
        var tipoRespuestaTexto = tipoRespuesta === 'varias' ? 'Casilla verificación' : 'Lista desplegable';
        
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
            <input type="hidden" name="RBF_orden[]" value="${contadorId}">
        `;
        $(`#card_pregunta_${contadorId} .card-body`).html(opcionesHtml);
    }

    function crearPreguntaEspecifica(contadorId, tipoRespuesta) {
        let inputHtml = '';
        let tipoRespuestaTexto = '';
        
        switch (tipoRespuesta) {
            case 'texto':
                tipoRespuestaTexto = 'Respuesta corta';
                inputHtml = `
                    <label class="form-label fw-bold mb-2 text-primary text-start">Respuesta de texto</label>
                    <input type="text" class="form-control" name="respuesta_${contadorId}" 
                            placeholder="En este espacio se ingresará la respuesta de texto" disabled />
                `;
                break;
                
            case 'numero':
                tipoRespuestaTexto = 'Numeral';
                inputHtml = `
                    <label class="form-label fw-bold mb-2 text-primary text-start">Respuesta numérica</label>
                    <input type="number" class="form-control" name="respuesta_${contadorId}" 
                            placeholder="En este espacio se ingresará las respuestas numéricas" disabled />
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
            
            <!-- Campos ocultos -->
            <input type="hidden" id="tipoRespuesta_${contadorId}" name="tipoRespuesta[]" value="${tipoRespuestaTexto}">
            <input type="hidden" name="RBF_orden[]" value="${contadorId}">
        `;
        
        $(`#card_pregunta_${contadorId} .card-body`).html(inputHtml);
    }

    function actualizarContadoresSecciones() {
        contadorSecciones = 0;
        contadorSubsecciones = {};
        
        $('#contenedor_pregunta_seleccionada .card').each(function() {
            var id = $(this).attr('id');
            if (id && id.startsWith('seccion_')) {
                id = id.replace('seccion_', '');
                if (id.indexOf('.') === -1) {
                    contadorSecciones++;
                    contadorSubsecciones[contadorSecciones] = 0;
                } else {
                    var partes = id.split('.');
                    var seccion = partes[0];
                    if (contadorSubsecciones[seccion] !== undefined) {
                        contadorSubsecciones[seccion]++;
                    }
                }
            }
        });
    }

    function validarFormulario() {
        let valido = true;
        let titulo = $('#titulo_formulario');
        
        if (!titulo.val() || !titulo.val().trim()) {
            titulo.addClass('is-invalid');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    text: 'El título del formulario no puede estar vacío.',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert('El título del formulario no puede estar vacío.');
            }
            valido = false;
            return false;
        } else {
            titulo.removeClass('is-invalid');
        }
        
        // Validar que las preguntas tengan textos completos
        let preguntasInvalidas = false;
        $('#contenedor_pregunta_seleccionada .card').each(function() {
            if ($(this).attr('id') && $(this).attr('id').startsWith('card_pregunta_')) {
                let inputPregunta = $(this).find('input.pregunta');
                
                // Verificar que inputPregunta es un objeto jQuery válido con elementos
                if (inputPregunta.length) {
                    // Ahora podemos acceder al valor con seguridad
                    if (!inputPregunta.val() || !inputPregunta.val().trim()) {
                        inputPregunta.addClass('is-invalid');
                        preguntasInvalidas = true;
                    } else {
                        inputPregunta.removeClass('is-invalid');
                    }
                }
                
                // Validar opciones si existen
                $(this).find('input[name^="opciones_"]').each(function() {
                    // También verificar que este elemento existe y tiene método val()
                    if ($(this).length && (!$(this).val() || !$(this).val().trim())) {
                        $(this).addClass('is-invalid');
                        preguntasInvalidas = true;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
            }
        });
        
        if (preguntasInvalidas) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    text: 'Hay preguntas u opciones incompletas. Por favor, revisa el formulario.',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert('Hay preguntas u opciones incompletas. Por favor, revisa el formulario.');
            }
            valido = false;
        }
        
        return valido;
    }

    function ensamblarDatos() {
        let arrayPreguntas = [];
        let preguntasExistentes = [];
        
        // Depuración - verificar qué elementos hay en el DOM
        console.log("Elementos en el contenedor:", $('#contenedor_pregunta_seleccionada .card').length);
        
        // Procesar todas las preguntas en el orden del DOM
        $('#contenedor_pregunta_seleccionada .card').each(function(index) {
            let elemId = $(this).attr('id');
            console.log("Procesando elemento:", elemId, "índice:", index);
            
            if (!elemId) {
                console.log("Elemento sin ID - ignorado");
                return;
            }
            
            // Procesar preguntas existentes
            if (elemId.startsWith('card_pregunta_existente_')) {
                let bcpId = $(this).data('bcp-id');
                let rbfId = $(this).data('rbf-id');
                
                console.log("Pregunta existente encontrada:", bcpId, rbfId);
                
                // Si no está en la lista de preguntas a eliminar, la agregamos
                if (!preguntasEliminar.includes(rbfId)) {
                    let obj = {
                        BCP_id: bcpId,
                        RBF_id: rbfId,
                        RBF_orden: index + 1
                    };
                    preguntasExistentes.push(obj);
                    console.log("Pregunta existente agregada:", obj);
                } else {
                    console.log("Pregunta marcada para eliminar - ignorada:", rbfId);
                }
            }
            // Procesar preguntas nuevas
            else if (elemId.startsWith('card_pregunta_')) {
                console.log("Nueva pregunta encontrada:", elemId);
                
                let idPregunta = elemId.split('_')[2];
                let obj = {};
                
                // Texto de la pregunta
                let textoPregunta = $(this).find('input.pregunta').val() || "";
                let numeroPregunta = $(this).find('.numero-pregunta').text() || "";
                obj.BCP_pregunta = `${numeroPregunta}. ${textoPregunta.trim()}`;
                
                // Tipo de respuesta
                obj.BCP_tipoRespuesta = $(this).find('input[name="tipoRespuesta[]"]').val() || "";
                
                // Orden
                obj.RBF_orden = index + 1;
                
                // Opciones
                let opciones = {};
                $(this).find('input[name^="opciones_"]').each(function(idx) {
                    let valorOpcion = $(this).val();
                    if (valorOpcion && valorOpcion.trim() !== '') {
                        opciones[idx.toString()] = valorOpcion.trim();
                    }
                });
                obj.BCP_opciones = Object.keys(opciones).length > 0 ? JSON.stringify(opciones) : null;
                
                // Complemento
                obj.BCP_complemento = $(this).find('input[name^="BCP_complemento_"]').val() || null;
                
                // Verificar que la pregunta tiene al menos texto y tipo
                if (obj.BCP_pregunta && obj.BCP_tipoRespuesta) {
                    arrayPreguntas.push(obj);
                    console.log("Nueva pregunta agregada:", obj);
                } else {
                    console.log("Pregunta incompleta - ignorada:", obj);
                }
            }
            // Procesar secciones o subsecciones si existen
            else if (elemId.startsWith('seccion_') || elemId.startsWith('card_grupo_') || elemId.startsWith('card_subgrupo_')) {
                console.log("Sección o subsección encontrada:", elemId);
                
                let obj = {};
                let textoElemento = $(this).find('input').val() || "";
                let tipoRespuesta = "";
                
                if (elemId.startsWith('seccion_') || elemId.startsWith('card_grupo_')) {
                    tipoRespuesta = 'Sección';
                } else {
                    tipoRespuesta = 'Subsección';
                }
                
                obj.BCP_pregunta = textoElemento.trim();
                obj.BCP_tipoRespuesta = tipoRespuesta;
                obj.RBF_orden = index + 1;
                obj.BCP_opciones = null;
                obj.BCP_complemento = null;
                
                if (obj.BCP_pregunta) {
                    arrayPreguntas.push(obj);
                    console.log("Sección/subsección agregada:", obj);
                } else {
                    console.log("Sección/subsección incompleta - ignorada:", obj);
                }
            }
        });
        
        // Combinar preguntas existentes y nuevas
        let todasLasPreguntas = [...preguntasExistentes, ...arrayPreguntas];
        
        console.log("JSON final a enviar:", todasLasPreguntas);
        
        // Guardar en campo oculto
        $('#listaPreguntasJSON').val(JSON.stringify(todasLasPreguntas));
        
        // Actualizar campo para preguntas a eliminar
        $('#preguntasEliminar').val(JSON.stringify(preguntasEliminar));
    }
});