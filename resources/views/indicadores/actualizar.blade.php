
@extends('layouts.app')
@section('title', 'Actualizacion - indicadores')

@section('content')
<div class="container mt-3 p-4 bg-white">
    @include('layouts.breadcrumbs', $breadcrumbs)
    <h1 class="text-center">Módulo de Indicadores</h1>
    <h3 class="text-center">Actualización de datos</h3>
    <div class="row m-4 p-3 " style="background-color: #cfe2ff;">
        <label for="colFormLabelLg" class="col-sm-8 col-form-label col-form-label-lg">Gestión:</label>
        <div class="col-sm-4 text-start">
            <select class=" form-select form-select-lg" id="anio_consulta" name="anio_consulta">
                <option value="2024" {{ ( $gestion == '2024') ? 'selected' : '' }}>2024</option>
                <option value="2025" {{ ( $gestion == '2025') ? 'selected' : '' }}>2025</option>
                <option value="2026" {{ ( $gestion == '2026') ? 'selected' : '' }}>2026</option>
                <option value="2027" {{ ( $gestion == '2027') ? 'selected' : '' }}>2027</option>
                <option value="2028" {{ ( $gestion == '2028') ? 'selected' : '' }}>2028</option>
            </select>
        </div>
    </div>
    {{-- @dump($categorias) --}}

    <div class="d-flex align-items-start border">
        <div class="col-4 border-end overflow-auto" id="v-pills-tab" style="max-height: 550px; direction: rtl;">
            <div class="nav flex-column nav-pills me-3" role="tablist" aria-orientation="vertical">
                <h3 class="text-center p-2 text-primary my-2">Categorías</h3>
                @php $i=0; @endphp
                @foreach ($categorias as $indic => $indicador)
                    <button class="border-bottom border-top border-start text-start nav-link mb-1 box-shadow {{ $loop->first ? 'active' : '' }}"  id="v-pills-{{$loop->index}}-tab"  data-bs-toggle="pill"  data-bs-target="#v-pills-{{$loop->index}}"  type="button"  role="tab"  aria-controls="v-pills-{{$loop->index}}>
                        <span class="ms-3">{{$indic}} </span>

                        {{-- <span class="ms-3">{{$indic}}. {{$indicador[0]['IND_numero']}}  </span> --}}
                    </button>
                @php $i++; @endphp
                @endforeach
            </div>
        </div>
        
        <div class="col-8 p-0 d-flex ">

            <div class="tab-content" id="v-pills-tabContent">
                
                @php $j=0; @endphp
                @foreach ($categorias as $indic => $indicadores)

                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                        id="v-pills-{{$loop->index}}" 
                        role="tabpanel" 
                        aria-labelledby="v-pills-{{$loop->index}}-tab">
                        <h3 class="text-center p-2 text-primary">Indicadores</h3>
                        <div class="accordion" id="accordionIndicadores">
                            @php $a=0; @endphp
                            @foreach ($indicadores as $index => $indicador)
                                <h2 class="accordion-header mt-1 rounded" id="heading_{{$a}}_{{$j}}">
                                    <button class="accordion-button bg-info bg-gradient text-dark border p-2 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{$a}}_{{$j}}" aria-expanded="false" aria-controls="collapse_{{$a}}_{{$j}}">
                                        <span class="bg-light rounded-circle p-3 box-shadow"><i class="bi bi-bar-chart-line-fill"></i></span>
                                        <span class="ms-3"><b>{{$indicador[0]['IND_numero']}}</b> {{$index}}</span>
                                    </button>
                                </h2>

                                    <div id="collapse_{{$a}}_{{$j}}" class="accordion-collapse collapse bg-light border-start border-top" aria-labelledby="heading_{{$a}}_{{$j}}" data-bs-parent="#accordionIndicadores">
                                        <div class="accordion-body">
                                            
                                            {{-- mensaje --}}
                                            <div class="alert alert-info box-shadow d-flex align-items-center">
                                                <span class="rounded rounded-circle bg-light p-0 m-1 me-3"><i class="bi bi-chat-left-text fs-3"></i></span>
                                                <small class="text-muted mx-2"><i>Fuente:</i></small>{{$indicador[0]['IND_fuente_informacion']}}
                                            </div>
                                            <div class="ms-3 p-1 border-start ">
                                                @foreach ($indicador as $p => $pregunta)
                                                    <form method="POST" enctype="multipart/form-data" id="formularioIndicadores_{{ $pregunta['IND_id'] }}" class="formularioIndicadores">
                                                        @csrf
                                                          
                                                        <div class="mb-3 ">
                                                            <label for="options_{{$pregunta['IND_id']}}" class="form-label">
                                                                {{$pregunta['IND_parametro']}}
                                                            </label>
                                                            @php
                                                                $opciones = json_decode($pregunta['IND_opciones'], true);
                                                            @endphp
                                                            
                                                            @if ($pregunta['IND_tipo_repuesta'] == 'Lista desplegable')
                                                                <div class="form-check">
                                                                    <label>Opciones:</label>
                                                                    @foreach ($opciones as $key => $value)
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" 
                                                                                type="radio" 
                                                                                name="respuesta" 
                                                                                id="option{{ $key }}" 
                                                                                value="{{ $value }}" 
                                                                                {{ $pregunta['HIN_respuesta'] == $value ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="option{{ $key }}">{{ $value }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @elseif ($pregunta['IND_tipo_repuesta'] == 'Casilla verificacion')
                                                                <div class="form-check">
                                                                    <label class="form-check-label">Opciones:</label>
                                                                    @foreach ($opciones as $key => $value)
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" 
                                                                                type="checkbox" 
                                                                                name="respuesta[]" 
                                                                                id="option{{ $key }}" 
                                                                                value="{{ $value }}">
                                                                            <label class="form-check-label" for="option{{ $key }}">{{ $value }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @elseif ($pregunta['IND_tipo_repuesta'] == 'Texto')
                                                                <input type="text" 
                                                                    name="respuesta" 
                                                                    class="form-control box-shadow" 
                                                                    id="respuesta_{{ $pregunta['IND_id'] }}" 
                                                                    value="{{ $pregunta['HIN_respuesta'] ?? '' }}">

                                                            @elseif ($pregunta['IND_tipo_repuesta'] == 'Numeral')
                                                                <input type="number" 
                                                                    name="respuesta" 
                                                                    class="form-control box-shadow" 
                                                                    id="respuesta_{{ $pregunta['IND_id'] }}" 
                                                                    value="{{ $pregunta['HIN_respuesta'] ?? '' }}">
                                                            @endif
                                                            
                                                            {{--Si es una lista de centros penitenciarios, no se muestra información complementaria --}}
                                                            @if ($pregunta['IND_tipo_repuesta'] == 'Lista desplegable' || $pregunta['IND_tipo_repuesta'] == 'Casilla verificacion' || $pregunta['IND_tipo_repuesta'] == 'Numeral' ||  $pregunta['IND_tipo_repuesta'] == 'Texto')

                                                                <div class="mb-2 mt-2">
                                                                    <label for="adicional" class="ms-2"><i class="bi bi-info-circle-fill text-warning fs-5 text-shadow"></i> Informacion complementaria:</label>
                                                                    <div class=" px-4">
                                                                    <input type="text" name="informacion_complementaria" class="form-control box-shadow" id="adicional_{{ $pregunta['IND_id'] }}" value="{{ $pregunta['HIN_informacion_complementaria'] ?? '' }}">
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="FK_IND_id" value="{{ $pregunta['IND_id'] }}">
                                                            
                                                                <button type="button" class="btn btn-primary btn-sm guardarIndicadores box-shadow" data-id="{{ $pregunta['IND_id'] }}"><i class="bi bi-check2-circle"></i> Actualizar datos para el año {{ $gestion }}</button>
                                                            @endif
                                                            
                                                        </div>
                                                        
                                                        <div id="mensajeConfirmacion_{{ $pregunta['IND_id'] }}" class="mt-3 alert alert-success d-none p-1"></div>
                                                        <div id="errorMessage_{{ $pregunta['IND_id'] }}" class="alert alert-danger alert-dismissible fade show d-none p-2 mt-2" role="alert">
                                                            <strong><i class="bi bi-exclamation-triangle"></i></strong> Inserte una respuesta
                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                        </div>
                                                        <hr>
                                                    </form>
                                                    @if ($pregunta['IND_tipo_repuesta'] == 'Lista centros penitenciarios')
                                                        <br>
                                                        <button type="button" class="btn btn-success text-shadow" data-bs-toggle="modal" data-bs-target="#centrosModal_{{ $pregunta['IND_id'] }}">
                                                            <i class="bi bi-building"></i> Insertar datos por centro - {{ $gestion }}
                                                        </button> <br>
                                                        @include('indicadores.listas_modal', ['parametro' => $pregunta['IND_parametro'], 'tipo' => 'centros penitenciarios'])

                                                    @elseif ($pregunta['IND_tipo_repuesta'] == 'Lista sexo')
                                                        <br>
                                                        <button type="button" class="btn btn-success text-shadow" data-bs-toggle="modal" data-bs-target="#listaSexoModal_{{ $pregunta['IND_id'] }}">
                                                            <i class="bi bi-people"></i> Insertar datos por sexo - {{ $gestion }}
                                                        </button> <br>
                                                        @include('indicadores.listas_modal', ['parametro' => $pregunta['IND_parametro'], 'tipo' => 'Lista sexo'])

                                                    @elseif ($pregunta['IND_tipo_repuesta'] == 'Lista delitos')
                                                        <br>
                                                        <button type="button" class="btn btn-warning text-shadow" data-bs-toggle="modal" data-bs-target="#listaDelitosModal_{{ $pregunta['IND_id'] }}">
                                                            <i class="bi bi-exclamation-triangle"></i> Insertar datos por delitos - {{ $gestion }}
                                                        </button> <br>
                                                        @include('indicadores.listas_modal', ['parametro' => $pregunta['IND_parametro'], 'tipo' => 'Lista delitos'])

                                                    @elseif ($pregunta['IND_tipo_repuesta'] == 'Lista departamentos')
                                                        <br>
                                                        <button type="button" class="btn btn-info text-shadow" data-bs-toggle="modal" data-bs-target="#listaDepartamentosModal_{{ $pregunta['IND_id'] }}">
                                                            <i class="bi bi-geo-alt"></i> Insertar datos por departamentos - {{ $gestion }}
                                                        </button> <br>
                                                        @include('indicadores.listas_modal', ['parametro' => $pregunta['IND_parametro'], 'tipo' => 'Lista departamentos'])
                                                    @endif


                                                @endforeach
                                                
                                                <div id="mensajeConfirmacion" class="mt-3 alert alert-success d-none"></div>
                                            </div>
                                        </div>
                                    </div>
                              @php $a++; @endphp
                            @endforeach
                        </div>

                    </div>
                @php $j++; @endphp
                @endforeach
            </div>
        </div>
    
    </div>
    
    <div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index:9999;"> <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:white;"> <span>Un momento por favor...</span> </div> </div>
@endsection

@section('js')

<script>
    let isSubmitting = false;
    
    $(document).ready(function() {
        // Función para actualizar el texto de todos los botones
        function actualizarTextoBotones(anio) {
            $('.guardarIndicadores').each(function() {
                $(this).html('<i class="bi bi-check2-circle"></i> Actualizar datos para el año ' + anio);
            });
        }

        // Actualizar botones al cargar la página
        var anioInicial = $('#anio_consulta').val();
        var anioConsulta = anioInicial; // Variable global para usar en AJAX
        actualizarTextoBotones(anioInicial);

        // *** ÚNICO EVENT LISTENER PARA EL SELECT DE AÑO ***
        $('#anio_consulta').off('change').on('change', function() {
            // Prevenir múltiples ejecuciones
            if (isSubmitting) {
                console.log('Ya se está procesando un cambio de año');
                return false;
            }
            
            isSubmitting = true;
            let selectedYear = $(this).val();
            
            console.log('Cambiando a año:', selectedYear);
            
            // Mostrar overlay inmediatamente
            $('#overlay').show();
            
            // Actualizar texto de botones
            actualizarTextoBotones(selectedYear);
            
            // Deshabilitar el select temporalmente
            $(this).prop('disabled', true);
            
            // Pequeño delay para asegurar que el overlay se muestre
            setTimeout(function() {
                // Redirigir
                window.location.href = "{{ route('indicadores.actualizar') }}" + "?gestion=" + selectedYear;
            }, 100);
        });

        // Event listener para guardar indicadores
        $('.guardarIndicadores').off('click').on('click', function() {
            if (isSubmitting) {
                console.log('Sistema ocupado, esperando...');
                return false;
            }
            
            var id = $(this).data('id');
            var form = $('#formularioIndicadores_' + id);
            var respuestaInput = form.find('[name="respuesta"]');
            var informacionComplementaria = form.find('[name="informacion_complementaria"]').val();
            var respuesta;
        
            // Función para mostrar mensajes de error
            function mostrarError(id, mensaje) {
                var errorDiv = $('#errorMessage_' + id);
                errorDiv.html('<strong><i class="bi bi-exclamation-triangle"></i></strong> ' + mensaje)
                    .removeClass('d-none')
                    .fadeIn();
                    
                setTimeout(function() {
                    errorDiv.fadeOut();
                }, 3000);
            }

            // Obtener el valor según el tipo de input
            if (respuestaInput.length > 0) {
                var inputType = respuestaInput.first().attr('type');
                
                switch(inputType) {
                    case 'radio':
                        respuesta = form.find('[name="respuesta"]:checked').val();
                        break;
                        
                    case 'checkbox':
                        var checkboxValues = [];
                        form.find('[name="respuesta"]:checked').each(function() {
                            checkboxValues.push($(this).val());
                        });
                        respuesta = checkboxValues.length > 0 ? checkboxValues : null;
                        break;
                        
                    case 'number':
                        respuesta = respuestaInput.val();
                        if (respuesta !== "" && !$.isNumeric(respuesta)) {
                            mostrarError(id, 'Por favor, ingrese un valor numérico válido');
                            return;
                        }
                        break;
                        
                    case 'text':
                    case 'textarea':
                    default:
                        respuesta = respuestaInput.val();
                        break;
                }
            }

            // Validación general
            if (inputType === 'checkbox') {
                if (!respuesta || respuesta.length === 0) {
                    mostrarError(id, 'Por favor, seleccione al menos una opción');
                    return;
                }
            } else {
                if (typeof respuesta === "undefined" || respuesta === "" || respuesta === null) {
                    mostrarError(id, 'Por favor, complete este campo');
                    return;
                }
            }

            // Mostrar overlay
            $('#overlay').show();
            
            var formData = form.serialize();
            formData += '&anio_consulta=' + encodeURIComponent(anioConsulta);

            $.ajax({
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: '{{ route("indicadores.guardar") }}',
                type: 'POST',
                data: formData,
                timeout: 30000, // 30 segundos timeout
                success: function(response) {
                    $('#mensajeConfirmacion_' + id).removeClass('d-none').html('<i class="bi bi-check2-circle"></i> Datos guardados correctamente.');
                    setTimeout(function() {
                        $('#mensajeConfirmacion_' + id).addClass('d-none').html('');
                    }, 3000);
                    $('#overlay').hide();
                },
                error: function(xhr, status, error) {
                    console.error('Error al guardar los datos:', error);
                    $('#mensajeConfirmacion_' + id).removeClass('d-none').addClass('alert-danger').html('<strong>Error:</strong> ' + (xhr.responseJSON?.message || 'Error al guardar los datos.'));
                    $('#overlay').hide();
                }
            });
        });

        // Función para permitir desmarcar radio buttons
       // Manejar cada formulario de indicadores por separado
        $('.formularioIndicadores').each(function() {
            const $form = $(this);
            const $radioButtons = $form.find('input[type="radio"][name="respuesta"]');
            const indicadorId = $form.attr('id').split('_')[1]; // Extraer ID del indicador
            
            // Solo procesar si tiene radio buttons
            if ($radioButtons.length === 0) return;
            
            // Crear contenedor para el botón "Quitar selección" si no existe
            let $clearContainer = $form.find('.clear-selection-container');
            if ($clearContainer.length === 0) {
                $clearContainer = $('<div class="clear-selection-container mt-2 mb-2" style="display: none;"></div>');
                $radioButtons.last().closest('.form-check').after($clearContainer);
            }
            
            // Crear botón "Quitar selección"
            const $clearButton = $('<button type="button" class="btn btn-sm btn-outline-secondary">' +
                                '<i class="bi bi-x-circle me-1"></i>Quitar selección</button>');
            $clearContainer.empty().append($clearButton);
            
            // Función para mostrar/ocultar el botón "Quitar selección"
            function toggleClearButton() {
                const hasSelection = $radioButtons.is(':checked');
                $clearContainer.toggle(hasSelection);
            }
            
            // Función para limpiar selección
            function clearSelection() {
                $radioButtons.prop('checked', false);
                toggleClearButton();
            }
            
            // Event listeners
            $radioButtons.off('click.clearSelection').on('click.clearSelection', function(event) {
                const $this = $(this);
                const currentValue = $this.val();
                
                // Si ya estaba seleccionado, desmarcarlo
                if ($this.data('was-checked')) {
                    clearSelection();
                    event.preventDefault();
                    return false;
                }
                
                // Actualizar estado de todos los radio buttons del grupo
                $radioButtons.each(function() {
                    $(this).data('was-checked', this === $this[0]);
                });
                
                toggleClearButton();
            });
            
            // Evento del botón "Quitar selección"
            $clearButton.off('click').on('click', function() {
                clearSelection();
            });
            
            // Inicializar estado
            $radioButtons.each(function() {
                $(this).data('was-checked', this.checked);
            });
            
            // Mostrar botón si ya hay selección inicial
            toggleClearButton();
        });


    });

    // Resetear flags cuando la página termine de cargar
    $(window).on('load', function() {
        isSubmitting = false;
        $('#overlay').hide();
        $('#anio_consulta').prop('disabled', false);
        console.log('Página cargada completamente');
    });

    // Manejo de errores de navegación
    $(window).on('beforeunload', function() {
        $('#overlay').show();
    });

    // Detectar si el usuario regresa con el botón atrás
    $(window).on('pageshow', function(event) {
        if (event.originalEvent.persisted) {
            isSubmitting = false;
            $('#overlay').hide();
            $('#anio_consulta').prop('disabled', false);
        }
    });



    // Función para actualizar los años en todos los botones dinámicamente
        function actualizarAniosEnBotones(anio) {
            // Actualizar botones de listas
            $('button[data-bs-toggle="modal"]').each(function() {
                let textoBoton = $(this).html();
                // Reemplazar cualquier año existente (2024-2030) con el nuevo año
                textoBoton = textoBoton.replace(/- \d{4}/g, '- ' + anio);
                $(this).html(textoBoton);
            });
            
            // Actualizar años en modales abiertos
            $('.anio-actual strong').text(anio);
        }

        // Llamar la función cuando cambie el año
        $('#anio_consulta').on('change', function() {
            let selectedYear = $(this).val();
            actualizarAniosEnBotones(selectedYear);
        });

        // Inicializar al cargar la página
        $(document).ready(function() {
            let anioInicial = $('#anio_consulta').val();
            actualizarAniosEnBotones(anioInicial);
        });
</script>





@endsection
