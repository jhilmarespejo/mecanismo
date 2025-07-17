@if(Auth::user()->rol == 'Administrador' )
    <button type="button" class="my-2 btn btn-primary text-light text-shadow box-shadow" data-bs-toggle="modal" data-bs-target="#nuevaVisita">
        <i class="bi bi-clipboard-plus"></i> Nueva Visita
    </button>

    <!-- Modal -->
    <div class="modal fade" id="nuevaVisita" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Nueva Visita</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="Post" id="frm_visita_nueva" action="/visita/guardarNuevaVisita">
                {{-- @csrf --}}
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label" >Establecimiento</label>
                        <input class="form-control" list="establecimientos" value="{{$EST_nombre}}" disabled>
                        <input type="hidden" name="FK_EST_id" id="FK_EST_id" value="{{$EST_id}}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tipo de visita</label>
                        <select class="form-select" name="VIS_tipo">
                            <option value="" selected>Seleccione</option>
                            <option value="Visita en profundidad">Visita en profundidad</option>
                            <option value="Visita Temática">Visita Temática</option>
                            <option value="Visita de seguimiento">Visita de seguimiento</option>
                            <option value="Visita reactiva">Visita reactiva</option>
                            <option value="Visita Ad hoc">Visita Ad hoc</option>
                        </select>
                        <small class="text-danger" id="VIS_tipo_err"></small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Título</label>
                        <input class="form-control" type="text" name="VIS_titulo">
                        <small class="text-danger" id="VIS_titulo_err"></small>
                    </div>

                    {{-- <div class="mb-4">
                        <label class="form-label">Fecha/s de ingreso</label>
                        <input class="form-control" type="date" name="VIS_fechas" max="100" min="0">
                        <small class="text-danger" id="VIS_fechas_err"></small>
                    </div> --}}
                    <div class="mb-4">
                        <div class="row">
                            <!-- Fecha de inicio -->
                            <div class="col-md-6">
                                <label class="form-label">Fecha de inicio de visita <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="VIS_fechas" id="VIS_fechas" required>
                                <small class="text-danger" id="VIS_fechas_err"></small>
                            </div>
                            
                            <!-- Fecha de fin -->
                            <div class="col-md-6">
                                <label class="form-label">Fecha de fin de visita <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="VIS_fecha_fin" id="VIS_fecha_fin" required>
                                <small class="text-danger" id="VIS_fecha_fin_err"></small>
                            </div>
                        </div>
                        
                        <!-- Mensaje informativo debajo de ambas fechas -->
                        <div class="row mt-2">
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> 
                                    La fecha de fin debe ser igual o posterior a la fecha de inicio. 
                                    Para visitas de un solo día, seleccione la misma fecha.
                                </small>
                            </div>
                        </div>
                    </div>

                
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-formulario-cancelar">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btn-visita-nueva">Guardar</button>
                </div>
            </form>

        </div>
        </div>
    </div>

    <script type="text/javascript">

        $(document).ready( function () {
            $("#btn-visita-nueva").click(function(e){
                    e.preventDefault();
                $('small').empty();
                    $.ajax({
                        async: true,
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        url: $('#frm_visita_nueva').attr("action"),
                        type: 'post',
                        data: $('#frm_visita_nueva').serialize(),
                        beforeSend: function () { },
                        success: function (data, response) {
                            jQuery.each(data.errors, function( key, value ){
                                $('#'+key+'_err').append( value );
                            });
                            if(!data.errors){
                                Swal.fire({
                                    text: 'Agregado correctamente',
                                    icon: 'success',
                                    confirmButtonText: 'ok!'
                                    }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload()
                                    }
                                })
                            }
                        },
                        error: function(response){ }
                    });
            });
        });



        
    // Validación de fechas 
    document.addEventListener('DOMContentLoaded', function() {
        const fechaInicio = document.getElementById('VIS_fechas');
        const fechaFin = document.getElementById('VIS_fecha_fin');
        const errorInicio = document.getElementById('VIS_fechas_err');
        const errorFin = document.getElementById('VIS_fecha_fin_err');
        
        // Establecer fecha mínima como hoy
        const today = new Date().toISOString().split('T')[0];
        fechaInicio.min = today;
        
        function validarRangoFechas() {
            // Limpiar errores previos
            errorInicio.textContent = '';
            errorFin.textContent = '';
            
            if (fechaInicio.value && fechaFin.value) {
                const inicio = new Date(fechaInicio.value);
                const fin = new Date(fechaFin.value);
                
                if (inicio > fin) {
                    errorInicio.textContent = 'La fecha de inicio debe ser menor o igual a la fecha de fin';
                    errorFin.textContent = 'La fecha de fin debe ser mayor o igual a la fecha de inicio';
                    return false;
                }
            }
            return true;
        }
        
        // Cuando cambia la fecha de inicio
        fechaInicio.addEventListener('change', function() {
            // Establecer la fecha mínima del campo fin
            fechaFin.min = this.value;
            
            // Si no hay fecha fin seleccionada, establecer la misma fecha
            if (!fechaFin.value) {
                fechaFin.value = this.value;
            }
            
            validarRangoFechas();
        });
        
        // Validación cuando cambia la fecha fin
        fechaFin.addEventListener('change', function() {
            validarRangoFechas();
        });
        
        // Validación al enviar el formulario
        const form = fechaInicio.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validarRangoFechas()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
    </script>


@endif


