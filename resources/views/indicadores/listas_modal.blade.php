<!-- Modal SEXO -->
@if ($tipo == 'Lista sexo')
    <div class="modal fade" id="listaSexoModal_{{ $pregunta['IND_id'] }}" tabindex="-1" aria-labelledby="listaSexoModalLabel_{{ $pregunta['IND_id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" id="listaSexoModalLabel_{{ $pregunta['IND_id'] }}">
                        <i class="bi bi-people me-2"></i>{{ $parametro }}
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="p-2 text-center fs-4 border-bottom bg-light">
                    <strong>Año: {{ $gestion }}</strong>
                </div>

                <div class="modal-body">
                    @php
                        $respuestas = json_decode($pregunta['HIN_respuesta'] ?? '{}', true);
                    @endphp
                    
                    <form id="listaSexoForm_{{ $pregunta['IND_id'] }}" method="POST">
                        @csrf
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Instrucciones:</strong> Ingrese la cantidad para cada categoría. Use 0 si no hay registros.
                        </div>
                        
                        <div class="mb-3">
                            @foreach($sexo as $key => $label)
                                <div class="mb-3 row">
                                    <label for="{{ $key }}_{{ $pregunta['IND_id'] }}" class="col-sm-2 col-form-label">
                                        {{ $label }}:
                                    </label>
                                    <div class="col-sm-10">
                                        <input 
                                            type="number" 
                                            name="{{ $key }}" 
                                            class="form-control lista-sexo-{{ $pregunta['IND_id'] }}" 
                                            {{-- placeholder="Ingrese cantidad (mínimo 0)"  --}}
                                            id="{{ $key }}_{{ $pregunta['IND_id'] }}" 
                                            value="{{ $respuestas[$key] ?? '' }}"
                                            min="0"
                                            max="999999"
                                            required
                                        >
                                    </div>
                                    <div class="invalid-feedback" id="error_{{ $key }}_{{ $pregunta['IND_id'] }}">
                                        Este campo es obligatorio y debe ser un número mayor o igual a 0
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mb-3">
                            <label for="adicional_sexo_{{ $pregunta['IND_id'] }}" class="form-label">
                                <i class="bi bi-info-circle-fill text-warning me-2"></i>Información complementaria:
                            </label>
                            <textarea 
                                name="informacion_complementaria" 
                                class="form-control" 
                                id="adicional_sexo_{{ $pregunta['IND_id'] }}" 
                                rows="3"
                                placeholder="Información adicional (opcional)"
                            >{{ $pregunta['HIN_informacion_complementaria'] ?? '' }}</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Cancelar
                    </button>
                    <button type="button" id="guardarListaSexo_{{ $pregunta['IND_id'] }}" class="btn btn-primary" data-id="{{ $pregunta['IND_id'] }}">
                        <i class="bi bi-save me-2"></i>Guardar datos por sexo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#guardarListaSexo_{{ $pregunta['IND_id'] }}').off('click').on('click', function() {
                var id = {{ $pregunta['IND_id'] }};
                var form = $('#listaSexoForm_' + id);
                var anyoConsulta = $('#anio_consulta').val();
                var informacionComplementaria = form.find('[name="informacion_complementaria"]').val();
                
                let sexoData = {};
                let valid = true;
                let errores = [];
                
                $('.lista-sexo-' + id).each(function() {
                    let input = $(this);
                    let fieldName = input.attr('name');
                    let numero = input.val().trim();
                    
                    input.removeClass('is-invalid');
                    $('#error_' + fieldName + '_' + id).hide();
                    
                    if (numero === "" || numero === null || numero === undefined) {
                        valid = false;
                        input.addClass('is-invalid');
                        $('#error_' + fieldName + '_' + id).show();
                        errores.push(fieldName + ': Campo vacío');
                    } else if (isNaN(numero) || parseInt(numero) < 0) {
                        valid = false;
                        input.addClass('is-invalid');
                        $('#error_' + fieldName + '_' + id).text('Debe ser un número válido mayor o igual a 0').show();
                        errores.push(fieldName + ': Número inválido');
                    } else {
                        sexoData[fieldName] = parseInt(numero);
                    }
                });

                if (!valid) {
                    Swal.fire({
                        title: 'Errores de Validación',
                        html: '<ul style="text-align: left;">' + 
                              errores.map(error => '<li>' + error + '</li>').join('') + 
                              '</ul>',
                        icon: 'error',
                        confirmButtonText: 'Corregir'
                    });
                    return;
                }

                $('#overlay').show();
                
                var formData = new FormData();
                formData.append('respuesta', JSON.stringify(sexoData));
                formData.append('anio_consulta', anyoConsulta);
                formData.append('informacion_complementaria', informacionComplementaria);
                formData.append('FK_IND_id', id);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("indicadores.guardar") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 30000,
                    success: function(response) {
                        $('#listaSexoModal_' + id).modal('hide');
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Datos por sexo guardados correctamente para el año ' + anyoConsulta,
                            icon: 'success',
                            confirmButtonText: 'Continuar'
                        });
                        $('#overlay').hide();
                    },
                    error: function(xhr, status, error) {
                        let errorMsg = 'Error desconocido al guardar los datos por sexo';
                        
                        try {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        
                        Swal.fire({
                            title: 'Error al Guardar',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonText: 'Entendido'
                        });
                        $('#overlay').hide();
                    }
                });
            });
        });
    </script>
@endif

<!-- Modal CENTROS PENITENCIARIOS -->
@if ($tipo == 'centros penitenciarios')
    <div class="modal fade" id="centrosModal_{{ $pregunta['IND_id'] }}" tabindex="-1" aria-labelledby="centrosModalLabel_{{ $pregunta['IND_id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title" id="centrosModalLabel_{{ $pregunta['IND_id'] }}">
                        <i class="bi bi-building me-2"></i>{{ $parametro }}
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="p-2 text-center fs-4 border-bottom bg-light">
                    <strong>Año: {{ $gestion }}</strong>
                </div>
                <div class="modal-body">
                    @php
                        $respuestas = json_decode($pregunta['HIN_respuesta'] ?? '{}', true);
                    @endphp
                    
                    <form id="centrosForm_{{ $pregunta['IND_id'] }}">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Instrucciones:</strong> Complete la información para cada centro penitenciario.
                        </div>
                       
                        @foreach ($centrosPenitenciarios as $departamento => $centros)
                            <div class="mb-3">
                                <h6 class="fw-bold">{{ $departamento }}</h6>
                                <ul class="list-group">
                                    @foreach ($centros as $centro)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{$centro->EST_nombre }}
                                            <input 
                                                type="number" 
                                                name="numero[{{ $centro->EST_id }}]" 
                                                class="form-control w-25 centro-numero_{{ $pregunta['IND_id'] }}" 
                                                data-centro="{{ $centro->EST_nombre }}"
                                                data-pregunta="{{ $pregunta['IND_id'] }}"
                                                placeholder="0"
                                                id="{{ $centro->EST_id }}"
                                                min="0"
                                                max="999999"
                                                maxlength="6"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                value="{{ json_decode( $pregunta['HIN_respuesta'], true)[$centro->EST_nombre] ?? '' }}"
                                            >
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach

                        <div class="mb-3">
                            <label for="adicional_centros_{{ $pregunta['IND_id'] }}" class="form-label">
                                <i class="bi bi-info-circle-fill text-warning me-2"></i>Información complementaria:
                            </label>
                            <textarea 
                                name="informacion_complementaria" 
                                class="form-control" 
                                id="adicional_centros_{{ $pregunta['IND_id'] }}" 
                                rows="3"
                                placeholder="Información adicional (opcional)"
                            >{{ $pregunta['HIN_informacion_complementaria'] ?? '' }}</textarea>
                        </div>
                    </form>
                </div>
               
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Cancelar
                    </button>
                    <button type="button" id="guardarListaCentros_{{ $pregunta['IND_id'] }}" class="btn btn-success">
                        <i class="bi bi-save me-2"></i>Guardar datos por centro
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            $('#guardarListaCentros_{{ $pregunta['IND_id'] }}').off('click').on('click', function() {
                const preguntaId = {{ $pregunta['IND_id'] }};
                const form = $('#centrosForm_' + preguntaId);
                const anyoConsulta = $('#anio_consulta').val();
                const informacionComplementaria = form.find('[name="informacion_complementaria"]').val();
                
                let centrosData = {};
                let valid = true;
                let errores = [];
        
                $('.centro-numero-' + preguntaId).each(function() {
                    let input = $(this);
                    let centro = input.data('centro');
                    let numero = input.val().trim();
                    
                    input.removeClass('is-invalid');
                    
                    if (numero === "" || numero === null) {
                        valid = false;
                        input.addClass('is-invalid');
                        errores.push(centro + ': Campo vacío');
                    } else if (isNaN(numero) || parseInt(numero) < 0) {
                        valid = false;
                        input.addClass('is-invalid');
                        errores.push(centro + ': Número inválido');
                    } else {
                        centrosData[centro] = parseInt(numero);
                    }
                });

                if (!valid) {
                    Swal.fire({
                        title: 'Errores de Validación',
                        html: '<ul style="text-align: left; max-height: 300px; overflow-y: auto;">' + 
                              errores.map(error => '<li>' + error + '</li>').join('') + 
                              '</ul>',
                        icon: 'error',
                        confirmButtonText: 'Corregir',
                        width: '600px'
                    });
                    return;
                }

                $('#overlay').show();

                var formData = new FormData();
                formData.append('respuesta', JSON.stringify(centrosData));
                formData.append('anio_consulta', anyoConsulta);
                formData.append('informacion_complementaria', informacionComplementaria);
                formData.append('FK_IND_id', preguntaId);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("indicadores.guardar") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 30000,
                    success: function(response) {
                        $('#centrosModal_' + preguntaId).modal('hide');
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Datos de centros penitenciarios guardados correctamente para el año ' + anyoConsulta,
                            icon: 'success',
                            confirmButtonText: 'Continuar'
                        });
                        $('#overlay').hide();
                    },
                    error: function(xhr, status, error) {
                        let errorMsg = 'Error al guardar los datos de centros';
                        
                        try {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        
                        Swal.fire({
                            title: 'Error al Guardar',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonText: 'Entendido'
                        });
                        $('#overlay').hide();
                    }
                });
            });
        });
    </script>
@endif

<!-- Modal LISTA DELITOS -->
@if ($tipo == 'Lista delitos')
    <div class="modal fade" id="listaDelitosModal_{{ $pregunta['IND_id'] }}" tabindex="-1" aria-labelledby="listaDelitosModalLabel_{{ $pregunta['IND_id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h4 class="modal-title" id="listaDelitosModalLabel_{{ $pregunta['IND_id'] }}">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ $parametro }}
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="p-2 text-center fs-4 border-bottom bg-light">
                    <strong>Año: {{ $gestion }}</strong>
                </div>

                <div class="modal-body">
                    @php
                        $respuestas = json_decode($pregunta['HIN_respuesta'] ?? '{}', true);
                    @endphp
                    
                    <form id="listaDelitosForm_{{ $pregunta['IND_id'] }}">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Instrucciones:</strong> Ingrese el número de casos para cada tipo de delito.
                        </div>

                        {{-- <div class="mb-3">
                            @foreach($sexo as $key => $label)
                                <div class="mb-3 row">
                                    <label for="{{ $key }}_{{ $pregunta['IND_id'] }}" class="col-sm-2 col-form-label">
                                        {{ $label }}:
                                    </label>
                                    <div class="col-sm-10">
                                        <input 
                                            type="number" 
                                            name="{{ $key }}" 
                                            class="form-control lista-sexo-{{ $pregunta['IND_id'] }}" 
                                            id="{{ $key }}_{{ $pregunta['IND_id'] }}" 
                                            value="{{ $respuestas[$key] ?? '' }}"
                                            min="0"
                                            max="999999"
                                            required
                                        >
                                    </div>
                                    <div class="invalid-feedback" id="error_{{ $key }}_{{ $pregunta['IND_id'] }}">
                                        Este campo es obligatorio y debe ser un número mayor o igual a 0
                                    </div>
                                </div>
                            @endforeach
                        </div> --}}
                        
                        
                        <div class="mb-3">
                            @foreach($delitos as $key => $delito)
                                <div class="row align-items-center mb-3">
                                    <label 
                                        for="delito_{{ $key }}_{{ $pregunta['IND_id'] }}" 
                                        class="col-12 col-md-6 col-form-label"
                                    >
                                        {{ $delito }}:
                                    </label>
                                    <div class="col-12 col-md-6">
                                        <input 
                                            type="number" 
                                            name="{{ $key }}" 
                                            class="form-control delito-numero-{{ $pregunta['IND_id'] }}" 
                                            data-delito="{{ $delito }}"
                                            data-key="{{ $key }}"
                                            id="delito_{{ $key }}_{{ $pregunta['IND_id'] }}" 
                                            value="{{ $respuestas[$key] ?? '' }}"
                                            min="0"
                                            max="999999"
                                            required
                                        >
                                        <div class="invalid-feedback" id="error_{{ $key }}_{{ $pregunta['IND_id'] }}">
                                            Este campo es obligatorio y debe ser un número mayor o igual a 0
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        

                        <div class="mb-3">
                            <label for="adicional_delitos_{{ $pregunta['IND_id'] }}" class="form-label">
                                <i class="bi bi-info-circle-fill text-warning me-2"></i>Información complementaria:
                            </label>
                            <textarea 
                                name="informacion_complementaria" 
                                class="form-control" 
                                id="adicional_delitos_{{ $pregunta['IND_id'] }}" 
                                rows="3"
                                placeholder="Información adicional sobre los datos (opcional)"
                            >{{ $pregunta['HIN_informacion_complementaria'] ?? '' }}</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Cancelar
                    </button>
                    <button type="button" id="guardarListaDelitos_{{ $pregunta['IND_id'] }}" class="btn btn-warning">
                        <i class="bi bi-save me-2"></i>Guardar datos de delitos
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            $('#guardarListaDelitos_{{ $pregunta['IND_id'] }}').off('click').on('click', function() {
                const preguntaId = {{ $pregunta['IND_id'] }};
                const form = $('#listaDelitosForm_' + preguntaId);
                const anyoConsulta = $('#anio_consulta').val();
                const informacionComplementaria = form.find('[name="informacion_complementaria"]').val();
                
                let delitosData = {};
                let valid = true;
                let errores = [];
        
                $('.delito-numero-' + preguntaId).each(function() {
                    let input = $(this);
                    let delitoKey = input.attr('name');
                    let delitoNombre = input.data('delito');
                    let numero = input.val().trim();
                    
                    input.removeClass('is-invalid');
                    $('#error_' + delitoKey + '_' + preguntaId).hide();
                    
                    if (numero === "" || numero === null || numero === undefined) {
                        valid = false;
                        input.addClass('is-invalid');
                        $('#error_' + delitoKey + '_' + preguntaId).show();
                        errores.push(delitoNombre + ': Campo vacío');
                    } else if (isNaN(numero)) {
                        valid = false;
                        input.addClass('is-invalid');
                        $('#error_' + delitoKey + '_' + preguntaId).text('Debe ser un número válido').show();
                        errores.push(delitoNombre + ': No es un número válido');
                    } else if (parseInt(numero) < 0) {
                        valid = false;
                        input.addClass('is-invalid');
                        $('#error_' + delitoKey + '_' + preguntaId).text('No puede ser menor a 0').show();
                        errores.push(delitoNombre + ': No puede ser negativo');
                    } else {
                        delitosData[delitoKey] = parseInt(numero);
                    }
                });

                if (!valid) {
                    Swal.fire({
                        title: 'Errores de Validación',
                        html: '<ul style="text-align: left; max-height: 300px; overflow-y: auto;">' + 
                              errores.map(error => '<li>' + error + '</li>').join('') + 
                              '</ul>',
                        icon: 'error',
                        confirmButtonText: 'Corregir',
                        width: '600px'
                    });
                    return;
                }

                $('#overlay').show();

                var formData = new FormData();
                formData.append('respuesta', JSON.stringify(delitosData));
                formData.append('anio_consulta', anyoConsulta);
                formData.append('informacion_complementaria', informacionComplementaria);
                formData.append('FK_IND_id', preguntaId);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("indicadores.guardar") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 30000,
                    success: function(response) {
                        $('#listaDelitosModal_' + preguntaId).modal('hide');
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Datos de delitos guardados correctamente para el año ' + anyoConsulta,
                            icon: 'success',
                            confirmButtonText: 'Continuar'
                       });
                       $('#overlay').hide();
                   },
                   error: function(xhr, status, error) {
                       let errorMsg = 'Error desconocido al guardar los datos de delitos';
                       
                       try {
                           if (xhr.responseJSON) {
                               if (xhr.responseJSON.error) {
                                   errorMsg = xhr.responseJSON.error;
                               } else if (xhr.responseJSON.message) {
                                   errorMsg = xhr.responseJSON.message;
                               }
                           }
                       } catch (e) {
                           console.error('Error parsing response:', e);
                       }
                       
                       Swal.fire({
                           title: 'Error al Guardar',
                           html: `
                               <div style="text-align: left;">
                                   <p><strong>Tipo:</strong> Datos de delitos</p>
                                   <p><strong>Año:</strong> ${anyoConsulta}</p>
                                   <p><strong>Error:</strong> ${errorMsg}</p>
                               </div>
                           `,
                           icon: 'error',
                           confirmButtonText: 'Entendido'
                       });
                       $('#overlay').hide();
                   }
               });
           });
       });
   </script>
@endif

<!-- Modal LISTA DEPARTAMENTOS -->
@if ($tipo == 'Lista departamentos')
   <div class="modal fade" id="listaDepartamentosModal_{{ $pregunta['IND_id'] }}" tabindex="-1" aria-labelledby="listaDepartamentosModalLabel_{{ $pregunta['IND_id'] }}" aria-hidden="true">
       <div class="modal-dialog modal-dialog-scrollable modal-lg">
           <div class="modal-content">
               <div class="modal-header bg-info text-white">
                   <h4 class="modal-title" id="listaDepartamentosModalLabel_{{ $pregunta['IND_id'] }}">
                       <i class="bi bi-geo-alt me-2"></i>{{ $parametro }}
                   </h4>
                   <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="p-2 text-center fs-4 border-bottom bg-light">
                   <strong>Año: {{ $gestion }}</strong>
               </div>

               <div class="modal-body">
                   @php
                       $respuestas = json_decode($pregunta['HIN_respuesta'] ?? '{}', true);
                   @endphp
                   
                   <form id="listaDepartamentosForm_{{ $pregunta['IND_id'] }}">
                       <div class="alert alert-info">
                           <i class="bi bi-info-circle me-2"></i>
                           <strong>Instrucciones:</strong> Complete los datos para cada departamento.
                       </div>
                       
                       <div class="mb-3">
                           <div class="row">
                               @foreach($departamentos as $key => $departamento)
                                   <div class="col-md-6 mb-3">
                                       <div class="card">
                                           <div class="card-body">
                                               <label for="depto_{{ $key }}_{{ $pregunta['IND_id'] }}" class="form-label fw-bold">
                                                   {{ $departamento }}:
                                               </label>
                                               <input 
                                                   type="number" 
                                                   name="{{ $key }}" 
                                                   class="form-control departamento-numero-{{ $pregunta['IND_id'] }}" 
                                                   data-departamento="{{ $departamento }}"
                                                   data-key="{{ $key }}"
                                                   placeholder="Ingrese cantidad (mínimo 0)" 
                                                   id="depto_{{ $key }}_{{ $pregunta['IND_id'] }}" 
                                                   value="{{ $respuestas[$key] ?? '' }}"
                                                   min="0"
                                                   max="999999"
                                                   required
                                               >
                                               <div class="invalid-feedback" id="error_{{ $key }}_{{ $pregunta['IND_id'] }}">
                                                   Este campo es obligatorio y debe ser un número mayor o igual a 0
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               @endforeach
                           </div>
                       </div>

                       <div class="mb-3">
                           <label for="adicional_departamentos_{{ $pregunta['IND_id'] }}" class="form-label">
                               <i class="bi bi-info-circle-fill text-warning me-2"></i>Información complementaria:
                           </label>
                           <textarea 
                               name="informacion_complementaria" 
                               class="form-control" 
                               id="adicional_departamentos_{{ $pregunta['IND_id'] }}" 
                               rows="3"
                               placeholder="Información adicional sobre los datos (opcional)"
                           >{{ $pregunta['HIN_informacion_complementaria'] ?? '' }}</textarea>
                       </div>
                   </form>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                       <i class="bi bi-x-lg me-2"></i>Cancelar
                   </button>
                   <button type="button" id="guardarListaDepartamentos_{{ $pregunta['IND_id'] }}" class="btn btn-info">
                       <i class="bi bi-save me-2"></i>Guardar datos por departamentos
                   </button>
               </div>
           </div>
       </div>
   </div>
   
   <script>
       $(document).ready(function() {
           $('#guardarListaDepartamentos_{{ $pregunta['IND_id'] }}').off('click').on('click', function() {
               const preguntaId = {{ $pregunta['IND_id'] }};
               const form = $('#listaDepartamentosForm_' + preguntaId);
               const anyoConsulta = $('#anio_consulta').val();
               const informacionComplementaria = form.find('[name="informacion_complementaria"]').val();
               
               let departamentosData = {};
               let valid = true;
               let errores = [];
       
               $('.departamento-numero-' + preguntaId).each(function() {
                   let input = $(this);
                   let deptoKey = input.attr('name');
                   let deptoNombre = input.data('departamento');
                   let numero = input.val().trim();
                   
                   input.removeClass('is-invalid');
                   $('#error_' + deptoKey + '_' + preguntaId).hide();
                   
                   if (numero === "" || numero === null || numero === undefined) {
                       valid = false;
                       input.addClass('is-invalid');
                       $('#error_' + deptoKey + '_' + preguntaId).show();
                       errores.push(deptoNombre + ': Campo vacío');
                   } else if (isNaN(numero)) {
                       valid = false;
                       input.addClass('is-invalid');
                       $('#error_' + deptoKey + '_' + preguntaId).text('Debe ser un número válido').show();
                       errores.push(deptoNombre + ': No es un número válido');
                   } else if (parseInt(numero) < 0) {
                       valid = false;
                       input.addClass('is-invalid');
                       $('#error_' + deptoKey + '_' + preguntaId).text('No puede ser menor a 0').show();
                       errores.push(deptoNombre + ': No puede ser negativo');
                   } else {
                       departamentosData[deptoKey] = parseInt(numero);
                   }
               });

               if (!valid) {
                   Swal.fire({
                       title: 'Errores de Validación',
                       html: '<ul style="text-align: left; max-height: 300px; overflow-y: auto;">' + 
                             errores.map(error => '<li>' + error + '</li>').join('') + 
                             '</ul>',
                       icon: 'error',
                       confirmButtonText: 'Corregir',
                       width: '600px'
                   });
                   return;
               }

               $('#overlay').show();

               var formData = new FormData();
               formData.append('respuesta', JSON.stringify(departamentosData));
               formData.append('anio_consulta', anyoConsulta);
               formData.append('informacion_complementaria', informacionComplementaria);
               formData.append('FK_IND_id', preguntaId);
               formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
               
               $.ajax({
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },
                   url: '{{ route("indicadores.guardar") }}',
                   type: 'POST',
                   data: formData,
                   processData: false,
                   contentType: false,
                   timeout: 30000,
                   success: function(response) {
                       $('#listaDepartamentosModal_' + preguntaId).modal('hide');
                       Swal.fire({
                           title: '¡Éxito!',
                           text: 'Datos por departamentos guardados correctamente para el año ' + anyoConsulta,
                           icon: 'success',
                           confirmButtonText: 'Continuar'
                       });
                       $('#overlay').hide();
                   },
                   error: function(xhr, status, error) {
                       let errorMsg = 'Error desconocido al guardar los datos por departamentos';
                       
                       try {
                           if (xhr.responseJSON) {
                               if (xhr.responseJSON.error) {
                                   errorMsg = xhr.responseJSON.error;
                               } else if (xhr.responseJSON.message) {
                                   errorMsg = xhr.responseJSON.message;
                               }
                           }
                       } catch (e) {
                           console.error('Error parsing response departamentos:', e);
                       }
                       
                       Swal.fire({
                           title: 'Error al Guardar',
                           html: `
                               <div style="text-align: left;">
                                   <p><strong>Tipo:</strong> Datos por departamentos</p>
                                   <p><strong>Año:</strong> ${anyoConsulta}</p>
                                   <p><strong>Error:</strong> ${errorMsg}</p>
                               </div>
                           `,
                           icon: 'error',
                           confirmButtonText: 'Entendido'
                       });
                       $('#overlay').hide();
                   }
               });
           });
       });
   </script>
@endif