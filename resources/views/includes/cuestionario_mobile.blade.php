@php
    // Filtrar solo preguntas normales para el carrusel
    $preguntas_normales = collect($elementos)->filter(function($item) {
        return !in_array($item['BCP_tipoRespuesta'], ['Sección', 'Subsección', 'Seccion', 'Subseccion', 'Etiqueta']);
    })->values()->all();
    
    $total_preguntas = count($preguntas_normales);
@endphp

<style>
.seccion-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
    margin-bottom: 10px;
    display: inline-block;
}

.subseccion-badge {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 500;
    margin-bottom: 8px;
    display: inline-block;
}

.card-pregunta {
    min-height: 400px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.progress-container {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 10px;
    margin: 10px 0;
}

.btn-navigation {
    min-width: 100px;
    font-weight: 600;
}

.estado-respuesta {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 1.2em;
}

.toast-container {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1100;
}

/* Estilos para campos obligatorios */
.border-danger {
    border: 2px solid #dc3545 !important;
    transition: border 0.3s ease;
}

.card-pregunta.campo-requerido {
    animation: shake 0.5s ease-in-out;
    border: 2px solid #dc3545;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}
</style>

<!-- Contenedor para notificaciones tipo toast -->
<div class="toast-container">
    <div id="toast-guardado" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-check-circle text-success me-2"></i>
            <strong class="me-auto">Guardado</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            Respuesta guardada correctamente
        </div>
    </div>
    
    <div id="toast-error" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-x-circle text-danger me-2"></i>
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            Error al guardar la respuesta
        </div>
    </div>
    
</div>

<div id="carousel_preguntas" class="carousel slide" data-bs-interval="false">
    <div class="carousel-inner">
        @foreach($preguntas_normales as $k => $elemento)
            <div class="carousel-item {{ ($k==0) ? 'active' : '' }}" id="card_{{ $k+1 }}">
                <input type="hidden" class="salto" value="{{ $elemento['RBF_salto_FK_BCP_id'] }}">
                
                <!-- Estado de respuesta -->
                <div class="estado-respuesta">
                    <i class="bi bi-circle text-muted" id="estado_{{ $k+1 }}"></i>
                </div>
                
                <div class="card border mb-3 card-pregunta RBF_id_{{ $elemento['RBF_id'] }}" 
                     id="BCP_id_{{ $elemento['BCP_id'] }}">
                    
                    <div class="card-header">
                        @php
                            // Buscar la sección y subsección más cercana hacia atrás
                            $seccion_actual = null;
                            $subseccion_actual = null;
                            
                            for ($i = array_search($elemento, $elementos); $i >= 0; $i--) {
                                if (in_array($elementos[$i]['BCP_tipoRespuesta'], ['Sección', 'Seccion'])) {
                                    $seccion_actual = $elementos[$i]['BCP_pregunta'];
                                    break;
                                } elseif (in_array($elementos[$i]['BCP_tipoRespuesta'], ['Subsección', 'Subseccion'])) {
                                    if (!$subseccion_actual) {
                                        $subseccion_actual = $elementos[$i]['BCP_pregunta'];
                                    }
                                }
                            }
                        @endphp
                        
                        @if($seccion_actual)
                            <div class="seccion-badge">
                                <i class="bi bi-folder-fill me-1"></i>{{ $seccion_actual }}
                            </div>
                        @endif
                        
                        @if($subseccion_actual)
                            <div class="subseccion-badge">
                                <i class="bi bi-folder2-open me-1"></i>{{ $subseccion_actual }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <span class="badge bg-primary me-2 mt-1 fs-6">{{ $k+1 }}</span>
                            <p class="card-title fs-5 mb-0">{{ $elemento['BCP_pregunta'] }}</p>
                        </div>
                        
                        @if($elemento['BCP_aclaracion'])
                            <div class="alert alert-light small mb-3">
                                <i class="bi bi-lightbulb text-warning me-1"></i>
                                {{ $elemento['BCP_aclaracion'] }}
                            </div>
                        @endif
                        
                        <!-- FORMULARIO DE RESPUESTA -->
                        <form method="POST" enctype="multipart/form-data" 
                              id="frm_{{ $elemento['RBF_id'] }}" 
                              class="frm-respuesta" 
                              data-pregunta-numero="{{ $k+1 }}"> 
                            @csrf
                            
                            @php
                                $opcionesSC = json_decode($elemento['BCP_opciones'], true);
                                $respuestasSC = json_decode($elemento['RES_respuesta'], true);
                                if ($respuestasSC === null) { $respuestasSC = []; }
                            @endphp
                            
                            @if(is_array($opcionesSC))
                                <div class="{{ ($elemento['BCP_tipoRespuesta'] == 'Casilla verificación') ? 'group-check' : 'group-radio' }}">
                                    @foreach($opcionesSC as $opcion)
                                        @if($elemento['BCP_tipoRespuesta'] == 'Casilla verificación')
                                            <div class="form-check mb-2">
                                                <input {{ in_array($opcion, $respuestasSC) ? 'checked' : '' }} 
                                                       type='checkbox' name="RES_respuesta[]" value="{{ $opcion }}" 
                                                       class="form-check-input" id="check_{{ $k }}_{{ $loop->index }}">
                                                <label class="form-check-label" for="check_{{ $k }}_{{ $loop->index }}">
                                                    {{ $opcion }}
                                                </label>
                                            </div>
                                        @elseif(in_array($elemento['BCP_tipoRespuesta'], ['Afirmación', 'Lista desplegable']))
                                            <div class="form-check mb-2">
                                                <input {{ ($elemento['RES_respuesta'] == $opcion) ? 'checked' : '' }} 
                                                       type='radio' name="RES_respuesta" value="{{ $opcion }}" 
                                                       class="form-check-input" id="radio_{{ $k }}_{{ $loop->index }}">
                                                <label class="form-check-label" for="radio_{{ $k }}_{{ $loop->index }}">
                                                    {{ $opcion }}
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            
                            @if($elemento['BCP_tipoRespuesta'] == 'Numeral')
                                <div class="mb-3">
                                    <input class="form-control resp" type='number' min="0" 
                                           name="RES_respuesta" value="{{ $elemento['RES_respuesta'] }}"
                                           placeholder="Ingrese un número">
                                </div>
                            @endif
                            
                            @if($elemento['BCP_tipoRespuesta'] == 'Respuesta corta')
                                <div class="mb-3">
                                    <input class="form-control resp" type='text' 
                                           name="RES_respuesta" value="{{ $elemento['RES_respuesta'] }}"
                                           placeholder="Escriba su respuesta">
                                </div>
                            @endif
                            
                            @if($elemento['BCP_tipoRespuesta'] == 'Respuesta larga')
                                <div class="mb-3">
                                    <textarea name="RES_respuesta" class="form-control resp" 
                                              rows="4" placeholder="Escriba su respuesta detallada">{{ trim($elemento['RES_respuesta']) }}</textarea>
                                </div>
                            @endif
                            
                            @if($elemento['BCP_complemento'])
                                <div class="mb-3">
                                    <label class="form-label small text-muted">{{ $elemento['BCP_complemento'] }}</label>
                                    <input type="text" name='RES_complemento' 
                                           class="form-control form-control-sm"
                                           value="{{ $elemento['RES_complemento'] }}">
                                </div>
                            @endif
                            
                            @if($elemento['BCP_adjunto'])
                                <div class="mb-3">
                                    <label class="form-label small text-muted">{{ $elemento['BCP_adjunto'] }}</label>
                                    <input type="file" 
                                           accept="image/*,video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" 
                                           class="form-control form-control-sm archivo-{{ $elemento['RBF_id'] }}" 
                                           name='RES_adjunto[]' multiple>
                                    <input type="hidden" name="ARC_descripcion" value="{{ $elemento['BCP_pregunta'] }}">
                                </div>
                            @endif
                            
                            <!-- Campos ocultos -->
                            <input type="hidden" name="RES_tipoRespuesta" value="{{ $elemento['BCP_tipoRespuesta'] }}">
                            <input type="hidden" name="RES_complementoRespuesta" value="{{ $elemento['BCP_complemento'] }}">
                            <input type="hidden" name="FK_RBF_id" value="{{ $elemento['RBF_id'] }}">
                            <input type="hidden" name="FK_AGF_id" value="{{ $elemento['AGF_id'] }}">
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        
        {{-- Pantalla final --}}
        <div class="carousel-item" id="card_{{ $total_preguntas + 1 }}">
            <div class="card text-white bg-success mb-3 card-pregunta">
                <div class="card-header fs-4">
                    <i class="bi bi-check-circle me-2"></i>Fin del cuestionario
                </div>
                <div class="card-body">
                    <h5 class="card-title">¡Cuestionario completado!</h5>
                    <div class="mb-3">
                        <p>Por favor revise los siguientes puntos:</p>
                        <ul>
                            <li>Asegúrese de que la información es correcta y confiable</li>
                            <li>Verifique las preguntas importantes</li>
                            <li>Todas las preguntas han sido respondidas</li>
                        </ul>
                    </div>
                    <div class="alert alert-light text-dark">
                        <strong>Resumen:</strong> 
                        <span id="resumen-respuestas">-</span> de {{ $total_preguntas }} preguntas respondidas
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de progreso -->
    <div class="progress-container">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-primary" id="conteo">1/{{ $total_preguntas + 1 }}</span>
            <small class="text-muted">Progreso del cuestionario</small>
        </div>
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                 id="pb_preguntas" role="progressbar" 
                 style="width: {{ round(1/($total_preguntas + 1) * 100) }}%"></div>
        </div>
    </div>

    <!-- Controles de navegación -->
    <div class="container mt-3 px-0">
        <div class="row g-2">
            <div class="col">
                <button class="btn btn-outline-primary btn-navigation w-100" id="btn_anterior">
                    <i class="bi bi-chevron-left me-1"></i>Anterior
                </button>
            </div>
            <div class="col">
                <button class="btn btn-primary btn-navigation w-100" id="btn_siguiente">
                    Siguiente<i class="bi bi-chevron-right ms-1"></i>
                </button>
            </div>
        </div>
        
        <button class="btn btn-secondary w-100 mt-2 d-none" id="btn_cargando" disabled>
            <span class="spinner-border spinner-border-sm me-2"></span>Guardando...
        </button>
        
        <button class="btn btn-success w-100 mt-2 d-none" id="btn_fin">
            <i class="bi bi-check-circle me-2"></i>Confirmar y finalizar
        </button>
    </div>
</div>

<script>
$(document).ready(function() {
    const totalPreguntas = {{ $total_preguntas }};
    let preguntaActual = 1;
    let preguntasRespondidas = new Set();
    
    // Inicializar estado
    inicializarEstado();
    
    // Navegación
    $("#btn_anterior, #btn_siguiente").click(function(e) {
        navegarPregunta($(this));
    });
    
    // Botón finalizar
    $("#btn_fin").click(function(e) {
        confirmarCuestionario({{ $elementos[0]['FRM_id'] ?? 0 }});
    });
    
    // Detectar cambios en respuestas
    $('.frm-respuesta').on('change', 'input, textarea, select', function() {
        actualizarEstadoRespuesta();
    });

    function inicializarEstado() {
        // Deshabilitar botón anterior en la primera pregunta
        actualizarBotonesNavegacion();
        
        // Verificar respuestas existentes
        $('.frm-respuesta').each(function() {
            if (tieneRespuesta($(this))) {
                const numero = $(this).data('pregunta-numero');
                preguntasRespondidas.add(numero);
                actualizarIconoEstado(numero, 'respondida');
            }
        });
        
        actualizarResumen();
    }

    function navegarPregunta($boton) {
        const direccion = $boton.attr('id');
        
        if (direccion === "btn_anterior") {
            preguntaActual--;
            $("#carousel_preguntas").carousel("prev");
            actualizarProgreso();
            actualizarBotonesNavegacion();
        } else if (direccion === "btn_siguiente") {
            const $formActual = $(`#card_${preguntaActual} .frm-respuesta`);
            
            // CAMBIO: Validar si la pregunta actual necesita respuesta obligatoria
            if (esRespuestaObligatoria($formActual) && !tieneRespuesta($formActual)) {
                // Mostrar notificación de error y NO avanzar
                mostrarToast('error', 'Debe responder esta pregunta antes de continuar');
                
                // Efecto visual adicional
                mostrarNotificacionCampoObligatorio();
                
                return; // CLAVE: No ejecuta avanzarPregunta()
            } else {
                avanzarPregunta($formActual);
            }
        }
    }

    function avanzarPregunta($form) {
        guardarRespuesta($form, () => {
            preguntaActual++;
            $("#carousel_preguntas").carousel("next");
            actualizarProgreso();
            actualizarBotonesNavegacion();
            
            // Manejar saltos condicionales
            manejarSaltosCondicionales();
        });
    }

    function guardarRespuesta($form, callback) {
        const formData = new FormData($form[0]);
        const preguntaNumero = $form.data('pregunta-numero');
        
        $.ajax({
            async: true,
            url: '/cuestionario/guardarRespuestasCuestionario',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                mostrarCargando(true);
            },
            success: function(response) {
                if (response.status === 'success') {
                    mostrarToast('guardado', response.message);
                    
                    if (tieneRespuesta($form)) {
                        preguntasRespondidas.add(preguntaNumero);
                        actualizarIconoEstado(preguntaNumero, 'respondida');
                    } else {
                        preguntasRespondidas.delete(preguntaNumero);
                        actualizarIconoEstado(preguntaNumero, 'sin-respuesta');
                    }
                    
                    actualizarResumen();
                } else if (response.status === 'skip') {
                    // Elemento informativo, continuar sin guardar
                }
                
                if (callback) callback();
            },
            error: function(xhr) {
                let mensaje = 'Error al guardar la respuesta';
                if (xhr.responseJSON?.message) {
                    mensaje = xhr.responseJSON.message;
                }
                mostrarToast('error', mensaje);
                
                if (callback) callback(); // Continuar incluso con error
            },
            complete: function() {
                mostrarCargando(false);
            }
        });
    }

    function tieneRespuesta($form) {
        let tiene = false;
        
        // Verificar inputs de texto y number
        $form.find('input.resp, textarea.resp').each(function() {
            if ($(this).val().trim() !== '') {
                tiene = true;
                return false; // break
            }
        });
        
        // Verificar radio buttons
        if ($form.find('input[type="radio"]:checked').length > 0) {
            tiene = true;
        }
        
        // Verificar checkboxes
        if ($form.find('input[type="checkbox"]:checked').length > 0) {
            tiene = true;
        }
        
        return tiene;
    }

    function esRespuestaObligatoria($form) {
        // CAMBIO: Ahora todas las preguntas son obligatorias
        return true;
    }

    function mostrarNotificacionCampoObligatorio() {
        // Agregar efecto visual al card actual para indicar que falta respuesta
        const $cardActual = $(`#card_${preguntaActual}`);
        $cardActual.addClass('border-danger');
        
        // Remover el efecto después de 3 segundos
        setTimeout(() => {
            $cardActual.removeClass('border-danger');
        }, 3000);
    }

    function actualizarIconoEstado(preguntaNumero, estado) {
        const $icono = $(`#estado_${preguntaNumero}`);
        
        switch (estado) {
            case 'respondida':
                $icono.removeClass('bi-circle text-muted bi-exclamation-circle text-warning')
                      .addClass('bi-check-circle text-success');
                break;
            case 'sin-respuesta':
                $icono.removeClass('bi-check-circle text-success bi-exclamation-circle text-warning')
                      .addClass('bi-circle text-muted');
                break;
            case 'advertencia':
                $icono.removeClass('bi-circle text-muted bi-check-circle text-success')
                      .addClass('bi-exclamation-circle text-warning');
                break;
        }
    }

    function actualizarProgreso() {
        const progreso = Math.round((preguntaActual / (totalPreguntas + 1)) * 100);
        $("#pb_preguntas").css("width", progreso + "%");
        $("#conteo").text(`${preguntaActual}/${totalPreguntas + 1}`);
    }

    function actualizarBotonesNavegacion() {
        // Botón anterior
        if (preguntaActual === 1) {
            $("#btn_anterior").prop('disabled', true);
        } else {
            $("#btn_anterior").prop('disabled', false);
        }
        
        // Botón siguiente/finalizar
        if (preguntaActual === totalPreguntas + 1) {
            $("#btn_siguiente").addClass('d-none');
            $("#btn_fin").removeClass('d-none');
        } else {
            $("#btn_siguiente").removeClass('d-none');
            $("#btn_fin").addClass('d-none');
        }
    }

    function actualizarResumen() {
        $("#resumen-respuestas").text(preguntasRespondidas.size);
    }

    function mostrarCargando(mostrar) {
        if (mostrar) {
            $("#btn_siguiente, #btn_anterior").addClass('d-none');
            $("#btn_cargando").removeClass('d-none');
        } else {
            $("#btn_cargando").addClass('d-none');
            $("#btn_siguiente, #btn_anterior").removeClass('d-none');
            actualizarBotonesNavegacion();
        }
    }

    function mostrarToast(tipo, mensaje) {
        const $toast = $(`#toast-${tipo}`);
        $toast.find('.toast-body').text(mensaje);
        
        const toast = new bootstrap.Toast($toast[0]);
        toast.show();
    }

    function manejarSaltosCondicionales() {
        const $cardActual = $(`#card_${preguntaActual - 1}`);
        const salto = $cardActual.find('.salto').val();
        
        if (salto) {
            try {
                const saltosObj = JSON.parse(salto);
                const $formAnterior = $cardActual.find('.frm-respuesta');
                const respuestaSeleccionada = $formAnterior.find("input[name='RES_respuesta']:checked").val();
                
                if (saltosObj[respuestaSeleccionada]) {
                    // Implementar lógica de salto si es necesario
                    console.log('Salto detectado:', respuestaSeleccionada, '->', saltosObj[respuestaSeleccionada]);
                }
            } catch (e) {
                console.log('Error procesando saltos:', e);
            }
        }
    }

    function confirmarCuestionario(FRM_id) {
        Swal.fire({
            title: '¿Confirmar cuestionario?',
            text: `Has respondido ${preguntasRespondidas.size} de ${totalPreguntas} preguntas.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Revisar respuestas'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    async: true,
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    url: '/cuestionario/confirmaCuestionario',
                    type: 'POST',
                    data: {estado: 'completado', FRM_id: FRM_id},
                    beforeSend: function() {
                        $("#btn_fin").prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Confirmando...');
                    },
                    success: function(data) {
                        Swal.fire({
                            title: '¡Cuestionario completado!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.history.back();
                        });
                    },
                    error: function() {
                        mostrarToast('error', 'Error al confirmar el cuestionario');
                        $("#btn_fin").prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Confirmar y finalizar');
                    }
                });
            }
        });
    }

    function actualizarEstadoRespuesta() {
        const $formActual = $(`#card_${preguntaActual} .frm-respuesta`);
        const preguntaNumero = preguntaActual;
        
        if (tieneRespuesta($formActual)) {
            preguntasRespondidas.add(preguntaNumero);
            actualizarIconoEstado(preguntaNumero, 'respondida');
        } else {
            preguntasRespondidas.delete(preguntaNumero);
            actualizarIconoEstado(preguntaNumero, 'sin-respuesta');
        }
        
        actualizarResumen();
    }
});
</script>