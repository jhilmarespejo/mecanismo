@extends('layouts.app')
@section('title', 'Establecimientos')


@section('content')
<style>
    /* Custom styling */

    .dataTables_wrapper .top {
        display: flex;
        justify-content: space-between;
    }
    .dataTables_info{
        padding-top: 0.3em !important;
    }

    table.modal-body tbody tr td{
        padding-top: 0px !important;
        padding-left: 10px !important;
    }
</style>
<div class="container mt-3 p-4 bg-white">
    @include('layouts.breadcrumbs', $breadcrumbs)

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <div class="row sm-m-2 m-2">
        <div class="col-sm-6 ">@include('establecimientos.estabs-departamento')</div>
        <div class="col-sm-6 ">@include('establecimientos.estabs-tipo')</div>
    </div>
    <a class="btn btn-primary mb-3" href="/establecimientos/crear"><i class="bi bi-clipboard-plus"></i> Añadir Nuevo lugar de detención    </a>
    <h1 class="mb-2 text-center text-primary">Lugares de detención</h1>
     <!-- Mostrar mensajes de éxito o error -->
     @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="mb-2">
        <select id="filter-tipo" class="form-select">
            <option value="" disabled selected>Seleccione una opcion</option>
            <option value="">Todos los tipos</option>
            @foreach ($tipo_establecimientos as $tipo)
                <option value="{{ $tipo->TES_id }}">{{ $tipo->TES_tipo }}</option>
            @endforeach
        </select>
    </div>
    <h3 class="mb-3">{{  ($TES_id && $establecimientos)? $establecimientos[0]['TES_tipo']:'' }}</h3>
    {{-- <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearModal">Añadir Establecimiento</button> --}}

    <table id="establecimientos-table" class="table table-striped">
        <thead>
            <tr>
                <th>Cod</th>
                <th>Nombre</th>
                <th>Departamento</th>
                <th>Municipio</th>
                <th>Teléfono de Contacto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($establecimientos as $establecimiento)
                <tr>
                    <td>{{ $establecimiento['EST_id'] }}</td>
                    <td>{{ $establecimiento['EST_nombre'] }}</td>
                    <td>{{ $establecimiento['EST_departamento'] }}</td>
                    <td>{{ $establecimiento['EST_municipio'] }}</td>
                    <td>{{ $establecimiento['EST_telefono_contacto'] }}</td>
                    <td>
                        {{-- <span class="btn btn-success box-shadow btn-sm text-light text-shadow" data-bs-toggle="modal" data-bs-target="#detalleModal" data-id="{{ $establecimiento['EST_id'] }}">Detalles</span> --}}
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle box-shadow text-shadow" data-bs-toggle="dropdown" aria-expanded="false">
                              Opciones
                            </button>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#detalleModal" data-id="{{ $establecimiento['EST_id'] }}"><i class="bi bi-info-circle"></i> Detalles</a></li>
                              <li><a class="dropdown-item" href="/establecimientos/infoMostrar/{{ $establecimiento['EST_id'] }}"><i class="bi bi-paperclip"></i> Información adicional</a></li>
                              <li><a class="dropdown-item" href="/establecimientos/personalMostrar/{{ $establecimiento['EST_id'] }}"><i class="bi bi-paperclip"></i> Información sobre el personal</a></li>
                            </ul>
                          </div>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

</div>

<!-- Modal de detalle -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleModalLabel">Detalle del Lugar de detencion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="">
                <table class="modal-body table table-hover table-responsive">
                </table>
                <!-- Aquí se mostrará la información del establecimiento -->
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#establecimientos-table').DataTable({
            "columnDefs": [
                {
                    "targets": [0], // Índice de la columna que deseas ocultar
                    "visible": false, // Hacer la columna no visible
                }
            ],
            "order": [[0, "desc"]],
            "dom": '<"top"ilf>rt<"bottom"p><"clear">',

            "language": {
                //"info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "info": "<b>_TOTAL_</b> resultados",
                "lengthMenu": "Mostrar _MENU_ elementos",
                "search": "Buscar:"
            }
        });

        $('#filter-tipo').change(function() {
            var TES_id = $(this).val();
            window.location.href = "{{ route('establecimientos.index') }}?TES_id=" + TES_id;

        });

        $('#detalleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            $.ajax({
                url: "{{ url('/establecimientos/mostrar') }}/" + id,
                method: 'GET',
                success: function(data) {
                    var modal = $('#detalleModal');
                    var content = `
                    <tbody>
                        <tr>
                            <td class="col-4">Nombre: </td>
                            <td>${ (data.establecimiento.EST_nombre)? data.establecimiento.EST_nombre:""}</td>
                        </tr>
                        <tr>
                            <td class="col-4">Departamento: </td>
                            <td>${ (data.establecimiento.EST_departamento)? data.establecimiento.EST_departamento:""}</td>
                        </tr>
                        <tr>
                            <td class="col-4">Municipio: </td>
                            <td>${ (data.establecimiento.EST_municipio)? data.establecimiento.EST_municipio:""}</td>
                        </tr>
                        <tr>
                            <td class="col-4">Dirección: </td>
                            <td>${ (data.establecimiento.EST_direccion)? data.establecimiento.EST_direccion:""}</td>
                        </tr>
                        <tr>
                            <td class="col-4">Teléfono de contacto: </td>
                            <td>${ (data.establecimiento.EST_telefono_contacto)? data.establecimiento.EST_telefono_contacto:""}</td>
                        </tr>
                        <tr>
                            <td class="col-4">Año de funcionamiento : </td>
                            <td>${ (data.establecimiento.EST_anyo_funcionamiento)? data.establecimiento.EST_anyo_funcionamiento:""}</td>
                        </tr>
                        <tr>
                            <td class="col-4">Capacidad - creación: </td>
                            <td>${ (data.establecimiento.EST_capacidad_creacion)? data.establecimiento.EST_capacidad_creacion:""}</td>
                        </tr>
                    </tbody>

                    <tr>
                        <td colspan="2"><h5>Información Adicional</h5></td>
                    </tr>  `;

                    if (data.info) {
                        content += `
                        <tbody>
                            <tr>
                                <td>Gestión:</td>
                                <td>${(data.info.EINF_gestion)? data.info.EINF_gestion:"" }</td>
                            </tr>
                            <tr>
                                <td>Cantidad de policías varones:</td>
                                <td>${(data.info.EINF_cantidad_policias_varones)? data.info.EINF_cantidad_policias_varones:"" }</td>
                            </tr>
                            <tr>
                                <td>Cantidad de policías mujeres:</td>
                                <td>${(data.info.EINF_cantidad_policias_mujeres)? data.info.EINF_cantidad_policias_mujeres:"" }</td>
                            </tr>
                            <tr>
                                <td>Cantidad de celdas para varones:</td>
                                <td>${(data.info.EINF_cantidad_celdas_varones)? data.info.EINF_cantidad_celdas_varones:"" }</td>
                            </tr>
                            <tr>
                                <td>Cantidad de celdas para mujeres:</td>
                                <td>${(data.info.EINF_cantidad_celdas_mujeres)? data.info.EINF_cantidad_celdas_mujeres:"" }</td>
                            </tr>
                            <tr>
                                <td>Normativa interna:</td>
                                <td>${(data.info.EINF_normativa_interna)? data.info.EINF_normativa_interna:"" }</td>
                            </tr>
                            <tr>
                                <td>Formato de registro para aprehendidos:</td>
                                <td>${(data.info.EINF_formato_registro_aprehendidos)? data.info.EINF_formato_registro_aprehendidos:"" }</td>
                            </tr>
                            <tr>
                                <td>Cantidad actual internos:</td>
                                <td>${(data.info.EINF_cantidad_actual_internos)? data.info.EINF_cantidad_actual_internos:"" }</td>
                            </tr>
                            <tr>
                                <td>Población atendida:</td>
                                <td>${(data.info.EINF_poblacion_atendida)? data.info.EINF_poblacion_atendida:"" }</td>
                            </tr>
                            <tr>
                                <td>Rangos de edad de la población:</td>
                                <td>${(data.info.EINF_rangos_edad_poblacion)? data.info.EINF_rangos_edad_poblacion:"" }</td>
                            </tr>
                            <tr>
                                <td>Tipo de entidad:</td>
                                <td>${(data.info.EINF_tipo_entidad)? data.info.EINF_tipo_entidad:"" }</td>
                            </tr>
                            <tr>
                                <td>Tipo de administración:</td>
                                <td>${(data.info.EINF_tipo_administracion)? data.info.EINF_tipo_administracion:"" }</td>
                            </tr>
                            <tr>
                                <td>Existe baño para PPLs:</td>
                                <td>${(data.info.EINF_banyo_ppl)? data.info.EINF_banyo_ppl:"" }</td>
                            </tr>
                            <tr>
                                <td>Existe teléfono para PPLs:</td>
                                <td>${(data.info.EINF_telefono_ppl)? data.info.EINF_telefono_ppl:"" }</td>
                            </tr>
                            <tr>
                                <td>Existen cámaras de vigilancia:</td>
                                <td>${(data.info.EINF_camaras_vigilancia)? data.info.EINF_camaras_vigilancia:"" }</td>
                            </tr>
                            <tr>
                                <td>Existe ambientes para visita</td>
                                <td>${(data.info.EINF_ambientes_visita)? data.info.EINF_ambientes_visita:"" }</td>
                            </tr>
                            <tr>
                                <td>Existe información visible sobre DDHH:</td>
                                <td>${(data.info.EINF_informacion_ddhh)? data.info.EINF_informacion_ddhh:"" }</td>
                            </tr>
                            <tr>
                                <td>Observaciones:</td>
                                <td>${(data.info.EINF_observaciones)? data.info.EINF_observaciones:"" }</td>
                            </tr>

                        </tbody>

                                    `;
                    }

                    if (data.personal.length > 0) {
                        content += `
                        <tr>
                            <td colspan="2"><h5>Información sobre el personal</h5></td>
                        </tr> `;
                        data.personal.forEach(person => {
                            content += `
                            <tbody>
                                <tr>
                                    <td>Gestión:</td>
                                    <td>${(person.EPER_gestion)? person.EPER_gestion:"" }</td>
                                </tr>
                                <tr>
                                    <td>Nombre del Responsable o Comandante:</td>
                                    <td>${(person.EPER_nombre_responsable)? person.EPER_nombre_responsable:"" }</td>
                                </tr>
                                <tr>
                                    <td>Profesión o Grado:</td>
                                    <td>${(person.EPER_grado_profesion)? person.EPER_grado_profesion:"" }</td>
                                </tr>
                                <tr>
                                    <td>Fecha de incorporación:</td>
                                    <td>${(person.EPER_fecha_incorporacion)? person.EPER_fecha_incorporacion:"" }</td>
                                </tr>
                                <tr>
                                    <td>Experiencia previa:</td>
                                    <td>${(person.EPER_experiencia)? person.EPER_experiencia:"" }</td>
                                </tr>
                                <tr>
                                    <td>Teléfono:</td>
                                    <td>${(person.EPER_telefono)? person.EPER_telefono:"" }</td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td>${(person.EPER_email)? person.EPER_email:"" }</td>
                                </tr>
                            </tbody>
                            `;
                        });
                    }

                    modal.find('.modal-body').html(content);
                }

            });
        });
    });
</script>

@endsection

