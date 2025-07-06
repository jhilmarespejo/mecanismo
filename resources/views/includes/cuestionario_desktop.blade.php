@php
    $elemento = $elementos;
@endphp

<style>
    .seccion-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        margin: 20px 0 10px 0;
        border-radius: 8px;
        font-size: 1.2em;
        font-weight: bold;
        text-align: left;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .subseccion-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 12px;
        margin: 15px 0 8px 25px;
        border-radius: 6px;
        font-size: 1.1em;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .pregunta-container {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .pregunta-container.respondida {
        border-left-color: #28a745;
    }

    .pregunta-container.sin-responder {
        border-left-color: #ffc107;
    }

    .pregunta-container.en-foco {
        border-left-color: #007bff;
        background-color: #f8f9fa;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .notificacion-guardado {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 250px;
    }

    .progress-sidebar {
        position: fixed;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 60px;
        z-index: 1040;
    }

    @media (max-width: 768px) {
        .progress-sidebar {
            display: none;
        }
    }
</style>

<!-- Notificaciones flotantes -->
<div id="notificaciones-container" class="notificacion-guardado"></div>

<!-- Barra de progreso lateral -->
<div class="progress-sidebar d-none d-md-block">
    <div class="card">
        <div class="card-body text-center p-2">
            <small class="text-muted">Progreso</small>
            <div class="progress mt-2" style="height: 120px; width: 20px;">
                <div id="progress-bar-vertical" class="progress-bar bg-success" role="progressbar" 
                     style="width: 100%; height: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <small id="progress-text" class="text-muted mt-2">0/0</small>
        </div>
    </div>
</div>

<div id="frm_cuestionario">
    @php 
        $c = 1; 
        $pregunta_numero = 1;
        $total_preguntas = collect($elemento)->whereNotIn('BCP_tipoRespuesta', ['Sección', 'Subsección', 'Seccion', 'Subseccion', 'Etiqueta'])->count();
    @endphp
    
    <ul class="list-unstyled" id="q">
        @foreach($elemento as $k => $item)
            @if(in_array($item['BCP_tipoRespuesta'], ['Sección', 'Seccion']))
                {{-- SECCIÓN --}}
                <li class="seccion-header" data-tipo="seccion">
                    <i class="bi bi-folder-fill me-2"></i>
                    {{ $item['BCP_pregunta'] }}
                </li>
            @elseif(in_array($item['BCP_tipoRespuesta'], ['Subsección', 'Subseccion']))
                {{-- SUBSECCIÓN --}}
                <li class="subseccion-header" data-tipo="subseccion">
                    <i class="bi bi-folder2-open me-2"></i>
                    {{ $item['BCP_pregunta'] }}
                </li>
            @elseif($item['BCP_tipoRespuesta'] == 'Etiqueta')
                {{-- ETIQUETA INFORMATIVA --}}
                <li class="my-3">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ $item['BCP_pregunta'] }}
                    </div>
                </li>
            @else
                {{-- PREGUNTA NORMAL --}}
                <li class="pregunta-container border-bottom py-3 hover p-2 elementos ms-5" 
                    data-pregunta-id="{{ $pregunta_numero }}" 
                    data-rbf-id="{{ $item['RBF_id'] }}"
                    data-bcp-id="{{ $item['BCP_id'] }}"
                    id="pregunta_{{ $pregunta_numero }}">
                    
                    <div class="row">
                        <div class="col-sm-5 col-preguntas-sc">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-primary me-2 mt-1">{{ $pregunta_numero }}</span>
                                <div>
                                    {{ $item['BCP_pregunta'] }}
                                    @if($item['BCP_aclaracion'])
                                        <small class="text-muted d-block mt-1">
                                            <i class="bi bi-lightbulb"></i> {{ $item['BCP_aclaracion'] }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-7 col-respuestas-sc">
                            <form method="POST" enctype="multipart/form-data" 
                                  id="frm_{{ $item['RBF_id'] }}" 
                                  class="frm-respuesta" 
                                  data-pregunta-numero="{{ $pregunta_numero }}"> 
                                @csrf
                                
                                @php
                                    $opcionesSC = json_decode($item['BCP_opciones'], true);
                                    $respuestasSC = json_decode($item['RES_respuesta'], true);
                                    if ($respuestasSC === null) { $respuestasSC = []; }
                                @endphp
                                
                                @if(is_array($opcionesSC))
                                    <input type="hidden" class="salto" value="{{ $item['RBF_salto_FK_BCP_id'] }}">
                                    <div class="{{ ($item['BCP_tipoRespuesta'] == 'Casilla verificación') ? 'group-check' : 'group-radio' }}">
                                        @foreach($opcionesSC as $opcion)
                                            @if($item['BCP_tipoRespuesta'] == 'Casilla verificación')
                                                <div class="col-auto d-flex mb-2 text-start">
                                                    <input {{ in_array($opcion, $respuestasSC) ? 'checked' : '' }} 
                                                           type='checkbox' name="RES_respuesta[]" value="{{ $opcion }}" 
                                                           class="me-2"> {{ $opcion }}
                                                </div>
                                            @elseif(in_array($item['BCP_tipoRespuesta'], ['Afirmación', 'Lista desplegable']))
                                                <div class="col-auto d-flex mb-2 text-start">
                                                    <input {{ ($item['RES_respuesta'] == $opcion) ? 'checked' : '' }} 
                                                           type='radio' name="RES_respuesta" value="{{ $opcion }}" 
                                                           class="me-2"> {{ $opcion }}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if($item['BCP_tipoRespuesta'] == 'Numeral')
                                    <div class="row p-2">
                                        <div class="col-auto">
                                            <input class="form-control resp" type='number' min="0" 
                                                   name="RES_respuesta" value="{{ $item['RES_respuesta'] }}">
                                        </div>
                                        <span class="col-1 marca"></span>
                                    </div>
                                @endif
                                
                                @if($item['BCP_tipoRespuesta'] == 'Respuesta corta')
                                    <div class='row p-2'>
                                        <div class="col">
                                            <input class="form-control resp" type='text' 
                                                   name="RES_respuesta" value="{{ $item['RES_respuesta'] }}">
                                        </div>
                                        <span class="col-1 marca"></span>
                                    </div>
                                @endif
                                
                                @if($item['BCP_tipoRespuesta'] == 'Respuesta larga')
                                    <div class='row p-2'>
                                        <div class="col">
                                            <textarea name="RES_respuesta" class="form-control resp" 
                                                      rows="3">{{ $item['RES_respuesta'] }}</textarea>
                                        </div>
                                        <span class="col-1 marca"></span>
                                    </div>
                                @endif
                                
                                @if($item['BCP_complemento'])
                                    <div class="row complemento px-3 py-1">
                                        <div class="col">
                                            <label class="form-label small">{{ $item['BCP_complemento'] }}</label>
                                            <input type="text" name='RES_complemento' 
                                                   class="form-control form-control-sm"
                                                   value="{{ $item['RES_complemento'] }}">
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item['BCP_adjunto'])
                                    <div class="row complemento px-3 py-1">
                                        <div class="col">
                                            <label class="form-label small">{{ $item['BCP_adjunto'] }}</label>
                                            <input type="file" 
                                                   accept="image/*,video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" 
                                                   class="form-control form-control-sm archivo-{{ $item['RBF_id'] }}" 
                                                   name='RES_adjunto[]' multiple>
                                            <input type="hidden" name="ARC_descripcion" value="{{ $item['BCP_pregunta'] }}">
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Campos ocultos -->
                                <input type="hidden" name="RES_tipoRespuesta" value="{{ $item['BCP_tipoRespuesta'] }}">
                                <input type="hidden" name="RES_complementoRespuesta" value="{{ $item['BCP_complemento'] }}">
                                <input type="hidden" name="FK_RBF_id" value="{{ $item['RBF_id'] }}">
                                <input type="hidden" name="FK_AGF_id" value="{{ $item['AGF_id'] }}">
                            </form>
                        </div>
                    </div>
                </li>
                @php $pregunta_numero++; @endphp
            @endif
            @php $c++; @endphp
        @endforeach
    </ul>
    
    <div class="row m-2 d-flex">
        <div class="col-auto">
            <button class="btn btn-primary btn-lg" id="btn_confirmacion">
                <i class="bi bi-check-circle me-2"></i>Confirmar datos
            </button>
        </div>
        <div class="col">
            <div class="alert alert-warning d-none" id="msg_vacios">
                <i class="bi bi-exclamation-triangle"></i> ¡Existen campos sin responder!
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const totalPreguntas = {{ $total_preguntas }};
    let preguntasRespondidas = 0;
    
    // Inicializar estado de preguntas
    actualizarEstadoPreguntas();
    actualizarProgreso();
    
    // Navegación automática con saltos
    $("#q div.group-radio").change(function (e) {
        var salto = jQuery.parseJSON($(this).siblings('.salto').val() || '{}');
        var resultado = $(this).find("input[name='RES_respuesta']:checked").val();
        
        if (salto && Object.keys(salto).length > 0) {
            jQuery.each(salto, function(key, value) {
                if (String(key) == String(resultado) && String(resultado) != 'Finalizar cuestionario') {
                    $('html,body').animate({
                        scrollTop: ($("#BCP_id_" + value).offset().top) - 150
                    }, 'slow');
                } else if (String(resultado) == 'Finalizar cuestionario') {
                    $('html,body').animate({
                        scrollTop: ($("#btn_confirmacion").offset().top)
                    }, 'slow');
                }
            });
        }
    });

    // Indicador visual de pregunta en foco
    $('.frm-respuesta').on('focus', 'input, textarea, select', function() {
        $('.pregunta-container').removeClass('en-foco');
        $(this).closest('.pregunta-container').addClass('en-foco');
    });

    

    // Botón confirmar con validación
    $("#btn_confirmacion").click(function(e) {
        if (validarFormulario()) {
            confirmarCuestionario({{ $FRM_id }});
        }
    });

    // Objeto para rastrear solicitudes en curso
    const requestsInProgress = {};

    // Guardado automático mejorado con protección contra duplicados
    $(".frm-respuesta").on('focusout', 'input, textarea, select', function(e) {
        let $form = $(this).closest('.frm-respuesta');
        let id = $form.attr('id').replace(/[^0-9]/g,'');
        let preguntaNumero = $form.data('pregunta-numero');
        
        // Usar una clave única para identificar la pregunta en curso
        const requestKey = `${id}_${preguntaNumero}`;
        
        // Si ya hay una solicitud en curso para esta pregunta, no hacer nada
        if (requestsInProgress[requestKey]) {
            return;
        }
        
        guardarRespuesta(id, preguntaNumero, $form, requestKey);
    });
    
    // Función para guardar respuestas 
    function guardarRespuesta(id, preguntaNumero, $form, requestKey) {
        let formData = new FormData($form[0]);
        
        // Marcar que hay una solicitud en curso para esta pregunta
        requestsInProgress[requestKey] = true;
        
        $.ajax({
            async: true,
            url: '/cuestionario/guardarRespuestasCuestionario',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                mostrarNotificacion('info', `Guardando respuesta ${preguntaNumero}...`, 'loading');
                $("form :input").prop("disabled", true);
            
            },
            success: function(response) {
                if (response.status === 'success' || response.status === 'updated') {
                    $form.css("border", "");
                    $form.find(".mensaje-error").remove();

                    mostrarNotificacion('success', response.message, 'check');
                    marcarPreguntaRespondida($form.closest('.pregunta-container'));
                    actualizarProgreso();
                }
            },
            error: function(xhr) {
                let message = 'Error al guardar la respuesta';
                
                // Limpieza previa
                $form.css("border", "");
                $form.find(".mensaje-error").remove();

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    
                    if (errors.RES_respuesta) {
                        $form.css("border", "2px solid red");

                        // 🔽 Insertar mensaje debajo del input RES_respuesta
                        const $input = $form.find('[name="RES_respuesta"]');
                        if ($input.length) {
                            $(`<div class="mensaje-error" style="color: red; font-size: 14px; margin-top: 4px;">${errors.RES_respuesta[0]}</div>`)
                                .insertAfter($input);
                        } else {
                            // Si no se encuentra el input, lo muestra debajo del form
                            $form.append(`
                                <div class="mensaje-error" style="color: red; margin-top: 8px;">
                                    ${errors.RES_respuesta[0]}
                                </div>
                            `);
                        }

                        message = errors.RES_respuesta[0];
                    }
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                
                mostrarNotificacion('error', message, 'x-circle');
            },

            complete: function() {
                $("form :input").prop("disabled", false);
                delete requestsInProgress[requestKey];
            }
        
        });
    }

    function mostrarNotificacion(tipo, mensaje, icono) {
        const colores = {
            success: 'success',
            error: 'danger',
            info: 'info',
            warning: 'warning'
        };

        const iconos = {
            check: 'bi-check-circle',
            loading: 'bi-arrow-clockwise spin',
            'x-circle': 'bi-x-circle',
            info: 'bi-info-circle'
        };

        const html = `
            <div class="alert alert-${colores[tipo]} alert-dismissible fade show" role="alert">
                <i class="bi ${iconos[icono]} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $('#notificaciones-container').html(html);
        
        // Auto-dismiss después de 3 segundos (excepto errores)
        if (tipo !== 'error') {
            setTimeout(() => {
                $('#notificaciones-container .alert').alert('close');
            }, 3000);
        }
    }

    function actualizarEstadoPreguntas() {
        $('.pregunta-container').each(function() {
            const $container = $(this);
            const $form = $container.find('.frm-respuesta');
            
            if (tieneRespuesta($form)) {
                marcarPreguntaRespondida($container);
            } else {
                marcarPreguntaSinResponder($container);
            }
        });
    }

    function tieneRespuesta($form) {
        let tieneRespuesta = false;
        
        // Verificar inputs de texto y number
        $form.find('input.resp, textarea.resp').each(function() {
            if ($(this).val().trim() !== '') {
                tieneRespuesta = true;
            }
        });
        
        // Verificar radio buttons
        if ($form.find('input[type="radio"]:checked').length > 0) {
            tieneRespuesta = true;
        }
        
        // Verificar checkboxes
        if ($form.find('input[type="checkbox"]:checked').length > 0) {
            tieneRespuesta = true;
        }
        
        return tieneRespuesta;
    }

    function marcarPreguntaRespondida($container) {
        $container.removeClass('sin-responder').addClass('respondida');
    }

    function marcarPreguntaSinResponder($container) {
        $container.removeClass('respondida').addClass('sin-responder');
    }

    function actualizarProgreso() {
        preguntasRespondidas = $('.pregunta-container.respondida').length;
        const porcentaje = Math.round((preguntasRespondidas / totalPreguntas) * 100);
        
        $('#progress-bar-vertical').css('height', porcentaje + '%');
        $('#progress-text').text(`${preguntasRespondidas}/${totalPreguntas}`);
    }

    function validarFormulario() {
        const preguntasSinResponder = $('.pregunta-container.sin-responder').length;
        
        if (preguntasSinResponder > 0) {
            $('#msg_vacios').removeClass('d-none');
            $('html,body').animate({
                scrollTop: $('.pregunta-container.sin-responder').first().offset().top - 150
            }, 'slow');
            return false;
        }
        
        $('#msg_vacios').addClass('d-none');
        return true;
    }

    function confirmarCuestionario(FRM_id) {
        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: '/cuestionario/confirmaCuestionario',
            type: 'POST',
            data: {estado: 'completado', FRM_id: FRM_id},
            beforeSend: function() {
                $('#btn_confirmacion').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Confirmando...');
            },
            success: function(data) {
                Swal.fire({
                    title: '¡Cuestionario completado!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.history.back();
                    }
                });
            },
            error: function() {
                mostrarNotificacion('error', 'Error al confirmar el cuestionario', 'x-circle');
                $('#btn_confirmacion').prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Confirmar datos');
            }
        });
    }
});

// CSS para animación de loading
// .spin {
//     animation: spin 1s linear infinite;
// }

// @keyframes spin {
//     from { transform: rotate(0deg); }
//     to { transform: rotate(360deg); }
// }
</script>