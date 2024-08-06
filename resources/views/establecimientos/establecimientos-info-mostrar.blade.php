@extends('layouts.app')
@section('title', 'Información adicional')


@section('content')

<div class="container mt-3 p-4 bg-white">
    @include('layouts.breadcrumbs', $breadcrumbs)
        <h1 class="mb-3 text-center text-secondary">Información adicional</h1>
        <h2 class="mb-2 text-center text-primary">{{ $infoAdicional['EST_nombre'] }}</h2>
        <h5 class="mb-2 text-center text-secondary">{{ $infoAdicional['TES_tipo'] }}</h5>





        <div class="row m-4 p-3 " style="background-color: #cfe2ff;">
            <label for="colFormLabelLg" class="col-sm-8 col-form-label col-form-label-lg">Información de la gestión.</label>
            <div class="col-sm-4">
                <select class=" form-select form-select-lg" id="anyo_consulta" name="anyo_consulta">
                    <option value="2024" {{ ( $infoAdicional['EINF_gestion'] == '2024' || $gestion == '2024') ? 'selected' : '' }}>2024</option>
                    <option value="2025" {{ ( $infoAdicional['EINF_gestion'] == '2025' || $gestion == '2025') ? 'selected' : '' }}>2025</option>
                    <option value="2026" {{ ( $infoAdicional['EINF_gestion'] == '2026' || $gestion == '2026') ? 'selected' : '' }}>2026</option>
                </select>
            </div>
        </div>

        <form id="actualizar-establecimiento-form"  method="POST">
            <input type="hidden" name="info[FK_EST_id]" value="{{ $infoAdicional['EST_id'] }}">
            <div class="card mb-3">
                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_policias_varones" class="form-label">Cantidad de Policías Varones</label>
                        <input disabled type="text" class="form-control" id="EINF_cantidad_policias_varones" name="info[EINF_cantidad_policias_varones]" value="{{ $infoAdicional['EINF_cantidad_policias_varones'] }}">
                        <small class="text-danger" id="error-EINF_cantidad_policias_varones"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_policias_mujeres" class="form-label">Cantidad de Policías Mujeres</label>
                        <input disabled type="text" class="form-control" id="EINF_cantidad_policias_mujeres" name="info[EINF_cantidad_policias_mujeres]" value="{{ $infoAdicional['EINF_cantidad_policias_mujeres'] }}">
                        <small class="text-danger" id="error-EINF_cantidad_policias_mujeres"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_celdas_varones" class="form-label">Cantidad de Celdas para Varones</label>
                        <input disabled type="text" class="form-control" id="EINF_cantidad_celdas_varones" name="info[EINF_cantidad_celdas_varones]" value="{{ $infoAdicional['EINF_cantidad_celdas_varones'] }}">
                        <small class="text-danger" id="error-EINF_cantidad_celdas_varones"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_celdas_mujeres" class="form-label">Cantidad de Celdas para Mujeres</label>
                        <input disabled type="text" class="form-control" id="EINF_cantidad_celdas_mujeres" name="info[EINF_cantidad_celdas_mujeres]" value="{{ $infoAdicional['EINF_cantidad_celdas_mujeres'] }}">
                        <small class="text-danger" id="error-EINF_cantidad_celdas_mujeres"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_normativa_interna" class="form-label">Normativa Interna</label>
                        <select disabled class="form-control" id="EINF_normativa_interna" name="info[EINF_normativa_interna]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ $infoAdicional['EINF_normativa_interna'] == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ $infoAdicional['EINF_normativa_interna'] == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        <small class="text-danger" id="error-EINF_normativa_interna"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_formato_registro_aprehendidos" class="form-label">Tipo de Registro de persona aprehendidos</label>

                        <select disabled class="form-control" id="EINF_formato_registro_aprehendidos" name="info[EINF_formato_registro_aprehendidos]">
                            <option value="">Seleccione</option>
                            <option value="Manual" {{ $infoAdicional['EINF_formato_registro_aprehendidos'] == 'Manual' ? 'selected' : '' }}>Manual</option>
                            <option value="Digital" {{ $infoAdicional['EINF_formato_registro_aprehendidos'] == 'Digital' ? 'selected' : '' }}>Digital</option>
                        </select>

                        <small class="text-danger" id="error-EINF_formato_registro_aprehendidos"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_actual_internos" class="form-label">Cantidad Actual de Internos</label>
                        <input disabled type="text" class="form-control" id="EINF_cantidad_actual_internos" name="info[EINF_cantidad_actual_internos]" value="{{ $infoAdicional['EINF_cantidad_actual_internos'] }}">
                        <small class="text-danger" id="error-EINF_cantidad_actual_internos"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_poblacion_atendida" class="form-label">Población Atendida</label>
                        <input disabled type="text" class="form-control" id="EINF_poblacion_atendida" name="info[EINF_poblacion_atendida]" value="{{ $infoAdicional['EINF_poblacion_atendida'] }}">
                        <small class="text-danger" id="error-EINF_poblacion_atendida"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_rangos_edad_poblacion" class="form-label">Rangos de Edad de la Población</label>
                        <input disabled type="text" class="form-control" id="EINF_rangos_edad_poblacion" name="info[EINF_rangos_edad_poblacion]" value="{{ $infoAdicional['EINF_rangos_edad_poblacion'] }}">
                        <small class="text-danger" id="error-EINF_rangos_edad_poblacion"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_tipo_entidad" class="form-label">Tipo de Entidad</label>
                        <select disabled class="form-control" id="EINF_tipo_entidad" name="info[EINF_tipo_entidad]">
                            <option value="">Seleccione</option>
                            <option value="Público" {{ $infoAdicional['EINF_tipo_entidad'] == 'Público' ? 'selected' : '' }}>Público</option>
                            <option value="Privado" {{ $infoAdicional['EINF_tipo_entidad'] == 'Privado' ? 'selected' : '' }}>Privado</option>
                        </select>
                        <small class="text-danger" id="error-EINF_tipo_entidad"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_tipo_administracion" class="form-label">Tipo de Administración</label>
                        <input disabled type="text" class="form-control" id="EINF_tipo_administracion" name="info[EINF_tipo_administracion]" value="{{ $infoAdicional['EINF_tipo_administracion'] }}">
                        <small class="text-danger" id="error-EINF_tipo_administracion"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_banyo_ppl" class="form-label">Baño PPL</label>
                        <select disabled class="form-control" id="EINF_banyo_ppl" name="info[EINF_banyo_ppl]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ $infoAdicional['EINF_banyo_ppl'] == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ $infoAdicional['EINF_banyo_ppl'] == 'No' ? 'selected' : '' }}>No</option>
                        </select>


                        <small class="text-danger" id="error-EINF_banyo_ppl"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_telefono_ppl" class="form-label">Teléfono PPL</label>
                        <select disabled class="form-control" id="EINF_telefono_ppl" name="info[EINF_telefono_ppl]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ $infoAdicional['EINF_telefono_ppl'] == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ $infoAdicional['EINF_telefono_ppl'] == 'No' ? 'selected' : '' }}>No</option>
                        </select>

                        <small class="text-danger" id="error-EINF_telefono_ppl"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_camaras_vigilancia" class="form-label">Cámaras de Vigilancia</label>
                        <select disabled class="form-control" id="EINF_camaras_vigilancia" name="info[EINF_camaras_vigilancia]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ $infoAdicional['EINF_camaras_vigilancia'] == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ $infoAdicional['EINF_camaras_vigilancia'] == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        <small class="text-danger" id="error-EINF_camaras_vigilancia"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_ambientes_visita" class="form-label">Ambientes de Visita</label>
                        <select disabled class="form-control" id="EINF_ambientes_visita" name="info[EINF_ambientes_visita]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ $infoAdicional['EINF_ambientes_visita'] == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ $infoAdicional['EINF_ambientes_visita'] == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        <small class="text-danger" id="error-EINF_ambientes_visita"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_informacion_ddhh" class="form-label">Información visible sobre DDHH de PPls</label>
                        <select disabled class="form-control" id="EINF_informacion_ddhh" name="info[EINF_informacion_ddhh]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ $infoAdicional['EINF_informacion_ddhh'] == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ $infoAdicional['EINF_informacion_ddhh'] == 'No' ? 'selected' : '' }}>No</option>
                        </select>

                        <small class="text-danger" id="error-EINF_informacion_ddhh"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_observaciones" class="form-label">Observaciones</label>
                        <input disabled type="text" class="form-control" id="EINF_observaciones" name="info[EINF_observaciones]" value="{{ $infoAdicional['EINF_observaciones'] }}">
                        <small class="text-danger" id="error-EINF_observaciones"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_gestion" class="form-label">Gestión</label>
                        <input disabled type="text" class="form-control" id="EINF_gestion" name="info[EINF_gestion]" value="{{ $infoAdicional['EINF_gestion'] }}">
                        <small class="text-danger" id="error-EINF_gestion"></small>
                    </div>

                </div>
            </div>
                <span class="btn btn-primary" id="edit_info_btn">Editar la información actual</span>

            @if($infoAdicional['EINF_gestion'] == date('Y'))
                {{-- <button type="button" class="btn btn-primary" id="edit_info_btn">Editar la información actual</button> --}}
            @endif

            <button type="button" class="btn btn-success" id="update_info_btn" style="display: none;">Actualizar Información</button>
        </form>



</div>
<script>
    $(document).ready(function() {
        $('#edit_info_btn').click(function() {
            $('input, select').prop('disabled', false);
            $('#edit_info_btn').hide();
            $('#update_info_btn').show();
        });

        $('#update_info_btn').click(function() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Confirmar para actualizar la información!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = $('#actualizar-establecimiento-form').serialize();
                    $.ajax({
                        method: 'POST',
                        async: true,
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        url: "{{ route('establecimientos.infoActualizar') }}",
                        data: formData,
                        success: function(response) {
                            Swal.fire(
                                'Actualizado!',
                                'La información ha sido actualizada.',
                                'success'
                            ).then(() => {
                                // Recargar la página después de confirmar
                                window.location.reload();
                            });
                            $('#update_info_btn').hide();
                            $('#edit_info_btn').show();
                            $('input').prop('disabled', true);
                        },
                        error: function(xhr) {
                            if(xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                // Limpiar errores previos
                                $('input').removeClass('is-invalid');
                                $('small.text-danger').text('');
                                // Mostrar errores nuevos
                                for (let key in errors) {
                                    if (errors.hasOwnProperty(key)) {
                                        let inputKey = key.replace('info.', '');
                                        $('#' + inputKey).addClass('is-invalid');
                                        $('#error-' + inputKey).text(errors[key][0]);
                                    }
                                }
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Hubo un problema al actualizar la información.',
                                    'error'
                                ).then(() => {
                                    //window.location.reload();
                                });
                            }
                        }
                    });
                }
            });
        });

        $('#anyo_consulta').change(function() {
            let selectedYear = $(this).val();
            window.location.href = "{{ route('establecimientos.infoMostrar', ['EST_id' => $infoAdicional['EST_id']]) }}" + "?gestion=" + selectedYear;
        });
    });
</script>

@endsection

