<!-- Modal SEXO -->
@if ($tipo == 'Lista sexo')
    <div class="modal fade" id="listaSexoModal" tabindex="-1" aria-labelledby="listaSexoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="listaSexoModalLabel">{{ $parametro }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class=" p-2 text-center modal-title fs-4 border-bottom anio-actual">
                    Año: <strong id="anio">{{ $gestion }}</strong>
                </p>

                <div class="modal-body">
                    @php
                        // Decodificar la respuesta JSON si existe
                        $respuestas = json_decode($pregunta['HIN_respuesta'] ?? '{}', true);
                        $femenino = $respuestas['femenino'] ?? '';
                        $masculino = $respuestas['masculino'] ?? '';
                    @endphp
                    <form id="listaSexoForm" method="POST">
                        @csrf
                            
                            <div class="mb-3">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Femenino: 
                                        <input type="number" name="femenino" class="form-control w-25 lista-sexo" placeholder="0"id="femenino" value="{{ $femenino }}">
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Masculino:
                                        <input type="number" name="masculino" class="form-control w-25 lista-sexo" placeholder="0"id="masculino" value="{{ $masculino }}">
                                    </li>
                                </ul>
                            </div>
                            <div class="mb-2 mt-2">
                                <label for="adicional" class="ms-2"><i class="bi bi-info-circle-fill text-warning fs-5 text-shadow"></i> Información complementaria:</label>
                                <div class=" px-4">
                                <input type="text" name="informacion_complementaria" class="form-control box-shadow" id="adicional_{{ $pregunta['IND_id'] }}" value="{{ $pregunta['HIN_informacion_complementaria'] ?? '' }}">
                                </div>
                            </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="guardarListaSexo" class="btn btn-primary" data-id="{{ $pregunta['IND_id'] }}">Guardar datos</button>
                </div>
            </div>
        </div>
    </div>

    <script>
         $('#guardarListaSexo').off('click').click(function() {
        var id = $(this).data('id');
        var form = $('#listaSexoForm');
        var anyoConsulta = $('#anio_consulta').val();
        
        console.log(anyoConsulta); 

        var informacionComplementaria = form.find('[name="informacion_complementaria"]').val();
        
        let sexoData = {};
        let valid = true;
        
        $('.lista-sexo').each(function() {
            let id = $(this).attr('id');
            let numero = $(this).val();
            
            if (numero === "" || numero === null) {
                valid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
                sexoData[id] = numero;
            }
        });

        if (!valid) {
            Swal.fire({
                title: 'Error!',
                text: 'Completa todos los campos antes de guardar',
                icon: 'error',
                confirmButtonText: 'Entendido',
            });
            return;
        }

        $('#overlay').show();
        
         // Obtener el token CSRF desde el input hidden
        var csrfToken = $('input[name="_token"]').val();
        var formData = new FormData();
        formData.append('respuesta', JSON.stringify(sexoData));
        formData.append('anio_consulta', anyoConsulta);
        formData.append('informacion_complementaria', informacionComplementaria);
        formData.append('FK_IND_id', id);
        formData.append('_token', csrfToken);
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/indicadores/guardar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#listaSexoModal').modal('hide');
                Swal.fire({
                    title: 'Éxito!',
                    text: 'Datos guardados correctamente',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
                $('#overlay').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Error al guardar los datos',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                $('#overlay').hide();
            }
        });
    });
    
    </script>
@endif

<!-- Modal CENTROS PENITENCIARIOS -->
@if ($tipo == 'centros penitenciarios')
 
    <div class="modal fade" id="centrosModal" id="centrosModal_{{ $pregunta['IND_id'] }}" tabindex="-1" aria-labelledby="centrosModalLabel_{{ $pregunta['IND_id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="centrosModalLabel">{{ $parametro }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class=" p-2 text-center modal-title fs-4 border-bottom anio-actual">
                    Año: <strong id="anio">{{ $gestion }}</strong>
                </p>
                <div class="modal-body">
                    <form id="centrosForm_{{ $pregunta['IND_id'] }}">
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

                        <div class="mb-2 mt-2">
                            <label for="adicional_{{ $pregunta['IND_id'] }}" class="ms-2">
                                <i class="bi bi-info-circle-fill text-warning fs-5 text-shadow"></i> Informacion complementaria:
                            </label>
                            <div class="px-4">
                                <input type="text" name="informacion_complementaria" class="form-control box-shadow" id="adicional_{{ $pregunta['IND_id'] }}" value="{{ $pregunta['HIN_informacion_complementaria'] ?? '' }}">
                            </div>
                        </div>
                    </form>
                </div>
               
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="guardarListaCentros_{{ $pregunta['IND_id'] }}" class="btn btn-primary">Guardar datos</button>
                </div>
            
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function () {
    
            $(`#guardarListaCentros_{{ $pregunta['IND_id'] }}`).off('click').on('click', function() {
                const preguntaId = "{{ $pregunta['IND_id'] }}";
                const form = $(`#centrosForm_${preguntaId}`);
                const anyoConsulta = $('#anio_consulta').val();

                const informacionComplementaria = form.find('[name="informacion_complementaria"]').val();
                
                let centrosData = {};
                let valid = true;
        
                $(`.centro-numero_${preguntaId}`).removeClass('is-invalid').each(function() {
                    let centro = $(this).data('centro');
                    let numero = $(this).val().trim();
                    
                    if (numero === "" || numero === null || isNaN(numero)) {
                        valid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        centrosData[centro] = numero;
                    }
                });

                if (!valid) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Completa todos los campos antes de guardar',
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });                    return;
                }

                $('#overlay').show();
                
                // var csrfToken = $('input[name="_token"]').val();
                // var formData = new FormData();
                // formData.append('respuesta', JSON.stringify(centrosData));
                // formData.append('anio_consulta', anyoConsulta);
                // formData.append('informacion_complementaria', informacionComplementaria);
                // formData.append('FK_IND_id', id);
                // formData.append('_token', csrfToken);'
                // 

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
                    url: '/indicadores/guardar',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // $('#centrosModal').modal('hide');
                        $(`#centrosModal_${preguntaId}`).modal('hide');
                        Swal.fire({
                            title: 'Éxito!',
                            text: 'Datos guardados correctamente',
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        });
                        $('#overlay').hide();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error al guardar los datos',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                        $('#overlay').hide();
                    }
                });
            });
        });
    </script>
@endif

<!-- Modal Lista delitos -->
@if ($tipo == 'Lista delitos')
    <div class="modal fade" id="listaSexoModal" tabindex="-1" aria-labelledby="listaSexoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="listaSexoModalLabel">{{ $parametro }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class=" p-2 text-center modal-title fs-4 border-bottom anio-actual">
                    Año: <strong id="anio">{{ $gestion }}</strong>
                </p>

                <div class="modal-body">
                    {{-- @php
                        // Decodificar la respuesta JSON si existe
                        $respuestas = json_decode($pregunta['HIN_respuesta'] ?? '{}', true);
                        $femenino = $respuestas['femenino'] ?? '';
                        $masculino = $respuestas['masculino'] ?? '';
                    @endphp --}}
                    <form id="listaSexoForm" method="POST">
                        @csrf
                            
                            <div class="mb-3">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Delito 1: 
                                        <input type="number" name="delito1" class="form-control w-25 lista-sexo" placeholder="0"id="delito1" value="{{ $delito1 }}">
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Delito 2:
                                        <input type="number" name="Delito2" class="form-control w-25 lista-sexo" placeholder="0"id="Delito2" value="{{ $Delito2 }}">
                                    </li>
                                </ul>
                            </div>
                            <div class="mb-2 mt-2">
                                <label for="adicional" class="ms-2"><i class="bi bi-info-circle-fill text-warning fs-5 text-shadow"></i> Información complementaria:</label>
                                <div class=" px-4">
                                <input type="text" name="informacion_complementaria" class="form-control box-shadow" id="adicional_{{ $pregunta['IND_id'] }}" value="{{ $pregunta['HIN_informacion_complementaria'] ?? '' }}">
                                </div>
                            </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="guardarListaSexo" class="btn btn-primary" data-id="{{ $pregunta['IND_id'] }}">Guardar datos</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#guardarListaDelitos').off('click').click(function() {
        var id = "{{ $pregunta['IND_id'] }}";
        var form = $('#listaDelitosForm');
        var anyoConsulta = $('#anio_consulta').val();
        var informacionComplementaria = form.find('[name="informacion_complementaria"]').val();
        
        let delitosData = {};
        let valid = true;
        
        $('.lista-delitos').each(function() {
            let id = $(this).attr('id');
            let numero = $(this).val();
            
            if (numero === "" || numero === null) {
                valid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
                delitosData[id] = numero;
            }
        });

        if (!valid) {
            Swal.fire({
                title: 'Error!',
                text: 'Completa todos los campos antes de guardar',
                icon: 'error',
                confirmButtonText: 'Entendido',
            });
            return;
        }

        $('#overlay').show();
        
        var csrfToken = $('input[name="_token"]').val();
        var formData = new FormData();
        formData.append('respuesta', JSON.stringify(delitosData));
        formData.append('anio_consulta', anyoConsulta);
        formData.append('informacion_complementaria', informacionComplementaria);
        formData.append('FK_IND_id', id);
        formData.append('_token', csrfToken);
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/indicadores/guardar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#listaSexoModal').modal('hide');
                Swal.fire({
                    title: 'Éxito!',
                    text: 'Datos guardados correctamente',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
                $('#overlay').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Error al guardar los datos',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                $('#overlay').hide();
            }
        });
    });
            
        });

       
    
    </script>
@endif
