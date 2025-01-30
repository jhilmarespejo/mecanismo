{{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}



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
                    Año: 
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
        var anyoConsulta = $('#anyo_consulta').val();
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
        formData.append('anyo_consulta', anyoConsulta);
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
    <div class="modal fade" id="centrosModal" tabindex="-1" aria-labelledby="centrosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="centrosModalLabel">{{ $parametro }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class=" p-2 text-center modal-title fs-4 border-bottom anio-actual">
                    Año: {{ $pregunta['HIN_gestion'] }}
                </p>
                <div class="modal-body">
                    <form id="centrosForm">
                        @foreach ($centrosPenitenciarios as $departamento => $centros)
                            <div class="mb-3">
                                <h6 class="fw-bold">{{ $departamento }}</h6>
                                <ul class="list-group">
                                    @foreach ($centros as $centro)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{$centro->EST_nombre }}
                                            <input 
                                                type="number" 
                                                name="numero[{{ $centro->EST_nombre }}]" 
                                                class="form-control w-25 centro-numero" 
                                                data-centro="{{ $centro->EST_nombre }}" 
                                                placeholder="0"
                                                id="{{ $centro->EST_id }}"
                                            >
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach

                        <div class="mb-2 mt-2">
                            <label for="adicional" class="ms-2"><i class="bi bi-info-circle-fill text-warning fs-5 text-shadow"></i> Informacion complementaria:</label>
                            <div class=" px-4">
                            <input type="text" name="informacion_complementaria" class="form-control box-shadow" id="adicional_{{ $pregunta['IND_id'] }}" value="{{ $pregunta['HIN_informacion_complementaria'] ?? '' }}">
                            </div>
                        </div>
                    </form>
                </div>
               
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="guardarCentros" class="btn btn-primary">Guardar datos</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.anio-actual').text('Año: '+$('#anio_consulta').val());
            $('#guardarCentros').off('click').click(function () {
                let centrosData = {}; // Objeto para almacenar los datos agrupados por departamento
                let valid = true; // Validación para asegurarnos que todos los campos estén llenos
    
                // Recorrer cada input
                $('.centro-numero').each(function () {
                    let centroId = $(this).attr('id'); // ID del centro
                    let centroNombre = $(this).data('centro'); // Nombre del centro
                    let numero = $(this).val(); // Valor ingresado
                    let departamento = $(this).closest('.mb-3').find('h6').text(); // Nombre del departamento
                    
                    // Validar que el número no esté vacío
                   /* if (numero === "" || numero === null) {
                        valid = false;
                        $(this).addClass('is-invalid'); // Resaltar el campo vacío
                    } else {
                        $(this).removeClass('is-invalid'); // Quitar la clase de error si es válido
                    }*/
    
                    // Si el departamento no existe en el objeto, lo inicializamos
                    if (!centrosData[departamento]) {
                        centrosData[departamento] = [];
                    }
    
                    // Agregar el centro con su ID y número al departamento correspondiente
                    centrosData[departamento].push({
                        EST_id: centroId,
                        EST_nombre: centroNombre,
                        numero: numero
                    });
                });
    
                // Validar que todos los campos estén llenos antes de continuar
                // if (!valid) {
                //     alert('Por favor, completa todos los campos antes de guardar.');
                //     return;
                // }
    
                // Convertir los datos a JSON
                let jsonData = JSON.stringify(centrosData);
                console.log(jsonData);
    
                // Aquí puedes enviar los datos al servidor con AJAX
                /*
                $.ajax({
                    url: '/ruta/del/servidor',
                    method: 'POST',
                    contentType: 'application/json',
                    data: jsonData,
                    success: function (response) {
                        alert('Datos guardados correctamente.');
                    },
                    error: function (error) {
                        alert('Ocurrió un error al guardar los datos.');
                    }
                });
                */
            });
        });
    </script>
@endif

<!-- Modal Lista delitos -->
@if ($tipo == 'Lista sexo')
    <div class="modal fade" id="listaSexoModal" tabindex="-1" aria-labelledby="listaSexoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="listaSexoModalLabel">{{ $parametro }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class=" p-2 text-center modal-title fs-4 border-bottom anio-actual">
                    Año: 
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
        var anyoConsulta = $('#anyo_consulta').val();
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
        formData.append('anyo_consulta', anyoConsulta);
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
