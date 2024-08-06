
@extends('layouts.app')
@section('title', 'Nuevo Lugar de detención')


@section('content')

    <div class="container mt-5">
        <h2 class="mb-4">Crear Nuevo Establecimiento</h2>
        <form id="create-establecimiento-form" action="{{ route('establecimientos.almacenar') }}" method="POST">
            @csrf
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Información del Establecimiento</h4>
                </div>


                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label for="FK_TES_id" class="form-label">Tipo de Establecimiento</label>
                        <select class="form-select @error('establecimiento.FK_TES_id') is-invalid @enderror" id="FK_TES_id" name="establecimiento[FK_TES_id]" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->TES_id }}" {{ old('establecimiento.FK_TES_id') == $tipo->TES_id ? 'selected' : '' }}>{{ $tipo->TES_tipo }}</option>
                            @endforeach
                        </select>
                        @error('establecimiento.FK_TES_id')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="EST_nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control @error('establecimiento.EST_nombre') is-invalid @enderror" id="EST_nombre" name="establecimiento[EST_nombre]" value="{{ old('establecimiento.EST_nombre') }}" required>

                        @error('establecimiento.EST_nombre')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>


                    <div class="col-md-6 mb-3">
                        <label for="EST_departamento" class="form-label">Departamento</label>

                        <select class="form-control @error('establecimiento.EST_departamento') is-invalid @enderror" id="establecimiento.EST_departamento" name="establecimiento[EST_departamento]">
                            <option value="">Seleccione</option>
                            <option value="La Paz" {{ old('establecimiento.EST_departamento') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                            <option value="Cochabamba" {{ old('establecimiento.EST_departamento') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                            <option value="Santa Cruz" {{ old('establecimiento.EST_departamento') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                            <option value="Oruro" {{ old('establecimiento.EST_departamento') == 'Oruro' ? 'selected' : '' }}>Oruro</option>
                            <option value="Potosí" {{ old('establecimiento.EST_departamento') == 'Potosí' ? 'selected' : '' }}>Potosí</option>
                            <option value="Chuquisaca" {{ old('establecimiento.EST_departamento') == 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca</option>
                            <option value="Tarija" {{ old('establecimiento.EST_departamento') == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                            <option value="Beni" {{ old('establecimiento.EST_departamento') == 'Beni' ? 'selected' : '' }}>Beni</option>
                            <option value="Pando" {{ old('establecimiento.EST_departamento') == 'Pando' ? 'selected' : '' }}>Pando</option>
                        </select>




                         @error('establecimiento.EST_nombre')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EST_municipio" class="form-label">Municipio</label>
                        <input type="text" class="form-control @error('establecimiento.EST_municipio') is-invalid @enderror" id="EST_municipio" name="establecimiento[EST_municipio]" value="{{ old('establecimiento.EST_municipio') }}" required>

                         @error('establecimiento.EST_municipio')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EST_direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control @error('establecimiento.EST_direccion') is-invalid @enderror" id="EST_direccion" name="establecimiento[EST_direccion]" value="{{ old('establecimiento.EST_direccion') }}" required>

                         @error('establecimiento.EST_direccion')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EST_telefono_contacto" class="form-label">Teléfono de Contacto</label>
                        <input type="text" class="form-control @error('establecimiento.EST_telefono_contacto') is-invalid @enderror" id="EST_telefono_contacto" name="establecimiento[EST_telefono_contacto]" value="{{ old('establecimiento.EST_telefono_contacto') }}">

                         @error('establecimiento.EST_telefono_contacto')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EST_anyo_funcionamiento" class="form-label">Año de Funcionamiento</label>
                        <input type="text" class="form-control @error('establecimiento.EST_anyo_funcionamiento') is-invalid @enderror" id="EST_anyo_funcionamiento" name="establecimiento[EST_anyo_funcionamiento]" value="{{ old('establecimiento.EST_anyo_funcionamiento') }}">

                         @error('establecimiento.EST_anyo_funcionamiento')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EST_capacidad_creacion" class="form-label">Capacidad de Creación</label>
                        <input type="text" class="form-control @error('establecimiento.EST_capacidad_creacion') is-invalid @enderror" id="EST_capacidad_creacion" name="establecimiento[EST_capacidad_creacion]" value="{{ old('establecimiento.EST_capacidad_creacion') }}">

                        @error('establecimiento.EST_capacidad_creacion')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h4>Información Adicional del Establecimiento</h4>
                </div>
                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_policias_varones" class="form-label">Cantidad de Policías Varones</label>
                        <input type="text" class="form-control @error('info.EINF_cantidad_policias_varones') is-invalid @enderror" id="EINF_cantidad_policias_varones" name="info[EINF_cantidad_policias_varones]" value="{{ old('info.EINF_cantidad_policias_varones') }}">
                        @error('info.EINF_cantidad_policias_varones')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_policias_mujeres" class="form-label">Cantidad de Policías Mujeres</label>
                        <input type="text" class="form-control @error('info.EINF_cantidad_policias_mujeres') is-invalid @enderror" id="EINF_cantidad_policias_mujeres" name="info[EINF_cantidad_policias_mujeres]" value="{{ old('info.EINF_cantidad_policias_mujeres') }}">
                        @error('info.EINF_cantidad_policias_mujeres')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_celdas_varones" class="form-label">Cantidad de Celdas para Varones</label>
                        <input type="text" class="form-control @error('info.EINF_cantidad_celdas_varones') is-invalid @enderror" id="EINF_cantidad_celdas_varones" name="info[EINF_cantidad_celdas_varones]" value="{{ old('info.EINF_cantidad_celdas_varones') }}">
                        @error('info.EINF_cantidad_celdas_varones')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_celdas_mujeres" class="form-label">Cantidad de Celdas para Mujeres</label>
                        <input type="text" class="form-control @error('info.EINF_cantidad_celdas_mujeres') is-invalid @enderror" id="EINF_cantidad_celdas_mujeres" name="info[EINF_cantidad_celdas_mujeres]" value="{{ old('info.EINF_cantidad_celdas_mujeres') }}">
                        @error('info.EINF_cantidad_celdas_mujeres')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_normativa_interna" class="form-label">Normativa Interna</label>
                        <select class="form-control @error('info.EINF_normativa_interna') is-invalid @enderror" id="info.EINF_normativa_interna" name="info[EINF_normativa_interna]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ old('info.EINF_normativa_interna') == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ old('info.EINF_normativa_interna') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('info.EINF_normativa_interna')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_formato_registro_aprehendidos" class="form-label">Tipo de Registro de persona aprehendidos</label>

                        <select class="form-control @error('info.EINF_formato_registro_aprehendidos') is-invalid @enderror" id="info.EINF_formato_registro_aprehendidos" name="info[EINF_formato_registro_aprehendidos]">
                            <option value="">Seleccione</option>
                            <option value="Manual" {{ old('info.EINF_formato_registro_aprehendidos') == 'Manual' ? 'selected' : '' }}>Manual</option>
                            <option value="Digital" {{ old('info.EINF_formato_registro_aprehendidos') == 'Digital' ? 'selected' : '' }}>Digital</option>
                        </select>

                        @error('info.EINF_formato_registro_aprehendidos')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_cantidad_actual_internos" class="form-label">Cantidad Actual de Internos</label>
                        <input type="text" class="form-control @error('info.EINF_cantidad_actual_internos') is-invalid @enderror" id="EINF_cantidad_actual_internos" name="info[EINF_cantidad_actual_internos]" value="{{ old('info.EINF_cantidad_actual_internos') }}">
                        @error('info.EINF_cantidad_actual_internos')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_poblacion_atendida" class="form-label">Población Atendida</label>
                        <input type="text" class="form-control @error('info.EINF_poblacion_atendida') is-invalid @enderror" id="EINF_poblacion_atendida" name="info[EINF_poblacion_atendida]" value="{{ old('info.EINF_poblacion_atendida') }}">
                        @error('info.EINF_poblacion_atendida')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_rangos_edad_poblacion" class="form-label">Rangos de Edad de la Población</label>
                        <input type="text" class="form-control @error('info.EINF_rangos_edad_poblacion') is-invalid @enderror" id="EINF_rangos_edad_poblacion" name="info[EINF_rangos_edad_poblacion]" value="{{ old('info.EINF_rangos_edad_poblacion') }}">
                        @error('info.EINF_rangos_edad_poblacion')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_tipo_entidad" class="form-label">Tipo de Entidad</label>
                        <select class="form-control @error('info.EINF_tipo_entidad') is-invalid @enderror" id="info.EINF_tipo_entidad" name="info[EINF_tipo_entidad]">
                            <option value="">Seleccione</option>
                            <option value="Público" {{ old('info.EINF_tipo_entidad') == 'Público' ? 'selected' : '' }}>Público</option>
                            <option value="Privado" {{ old('info.EINF_tipo_entidad') == 'Privado' ? 'selected' : '' }}>Privado</option>
                        </select>
                        @error('info.EINF_tipo_entidad')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_tipo_administracion" class="form-label">Tipo de Administración</label>
                        <input type="text" class="form-control @error('info.EINF_tipo_administracion') is-invalid @enderror" id="EINF_tipo_administracion" name="info[EINF_tipo_administracion]" value="{{ old('info.EINF_tipo_administracion') }}">
                        @error('info.EINF_tipo_administracion')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_banyo_ppl" class="form-label">Baño PPL</label>
                        <select class="form-control @error('info.EINF_banyo_ppl') is-invalid @enderror" id="info.EINF_banyo_ppl" name="info[EINF_banyo_ppl]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ old('info.EINF_banyo_ppl') == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ old('info.EINF_banyo_ppl') == 'No' ? 'selected' : '' }}>No</option>
                        </select>


                        @error('info.EINF_banyo_ppl')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_telefono_ppl" class="form-label">Teléfono PPL</label>
                        <select class="form-control @error('info.EINF_telefono_ppl') is-invalid @enderror" id="info.EINF_telefono_ppl" name="info[EINF_telefono_ppl]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ old('info.EINF_telefono_ppl') == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ old('info.EINF_telefono_ppl') == 'No' ? 'selected' : '' }}>No</option>
                        </select>

                        @error('info.EINF_telefono_ppl')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_camaras_vigilancia" class="form-label">Cámaras de Vigilancia</label>
                        <select class="form-control @error('info.EINF_camaras_vigilancia') is-invalid @enderror" id="info.EINF_camaras_vigilancia" name="info[EINF_camaras_vigilancia]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ old('info.EINF_camaras_vigilancia') == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ old('info.EINF_camaras_vigilancia') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('info.EINF_camaras_vigilancia')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_ambientes_visita" class="form-label">Ambientes de Visita</label>
                        <select class="form-control @error('info.EINF_ambientes_visita') is-invalid @enderror" id="info.EINF_ambientes_visita" name="info[EINF_ambientes_visita]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ old('info.EINF_ambientes_visita') == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ old('info.EINF_ambientes_visita') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('info.EINF_ambientes_visita')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_informacion_ddhh" class="form-label">Información visible sobre DDHH de PPls</label>
                        <select class="form-control @error('info.EINF_informacion_ddhh') is-invalid @enderror" id="info.EINF_informacion_ddhh" name="info[EINF_informacion_ddhh]">
                            <option value="">Seleccione</option>
                            <option value="Si" {{ old('info.EINF_informacion_ddhh') == 'Si' ? 'selected' : '' }}>Sí</option>
                            <option value="No" {{ old('info.EINF_informacion_ddhh') == 'No' ? 'selected' : '' }}>No</option>
                        </select>

                        @error('info.EINF_informacion_ddhh')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_observaciones" class="form-label">Observaciones</label>
                        <input type="text" class="form-control @error('info.EINF_observaciones') is-invalid @enderror" id="EINF_observaciones" name="info[EINF_observaciones]" value="{{ old('info.EINF_observaciones') }}">
                        @error('info.EINF_observaciones')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EINF_gestion" class="form-label">Gestión</label>
                        <input type="text" class="form-control @error('info.EINF_gestion') is-invalid @enderror" id="EINF_gestion" name="info[EINF_gestion]" value="{{ old('info.EINF_gestion') }}">
                        @error('info.EINF_gestion')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h4>Personal del Establecimiento</h4>
                </div>
                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label for="EPER_nombre_responsable" class="form-label">Nombre del Responsable o Comandante</label>
                        <input type="text" class="form-control @error('personal.EPER_nombre_responsable') is-invalid @enderror" id="EPER_nombre_responsable" name="personal[EPER_nombre_responsable]" value="{{ old('personal.EPER_nombre_responsable') }}">

                        @error('personal.EPER_nombre_responsable')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EPER_grado_profesion" class="form-label">Profesión o Grado</label>
                        <input type="text" class="form-control @error('personal.EPER_grado_profesion') is-invalid @enderror" id="EPER_grado_profesion" name="personal[EPER_grado_profesion]" value="{{ old('personal.EPER_grado_profesion') }}">

                        @error('personal.EPER_grado_profesion')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EPER_fecha_incorporacion" class="form-label">Fecha de Incorporación</label>
                        <input type="date" class="form-control @error('personal.EPER_fecha_incorporacion') is-invalid @enderror" id="EPER_fecha_incorporacion" name="personal[EPER_fecha_incorporacion]" value="{{ old('personal.EPER_fecha_incorporacion') }}">

                        @error('personal.EPER_fecha_incorporacion')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EPER_experiencia" class="form-label">Experiencia</label>
                        <input type="text" class="form-control @error('personal.EPER_experiencia') is-invalid @enderror" id="EPER_experiencia" name="personal[EPER_experiencia]" value="{{ old('personal.EPER_experiencia') }}">

                        @error('personal.EPER_experiencia')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EPER_telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control @error('personal.EPER_telefono') is-invalid @enderror" id="EPER_telefono" name="personal[EPER_telefono]" value="{{ old('personal.EPER_telefono') }}">

                        @error('personal.EPER_telefono')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EPER_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('personal.EPER_email') is-invalid @enderror" id="EPER_email" name="personal[EPER_email]" value="{{ old('personal.EPER_email') }}">

                        @error('personal.EPER_email')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="EPER_gestion" class="form-label">Gestión</label>
                        <input type="text" class="form-control @error('personal.EPER_gestion') is-invalid @enderror" id="EPER_gestion" name="personal[EPER_gestion]" value="{{ (old('personal.EPER_gestion'))? old('personal.EPER_gestion'): date('Y')  }}">

                        @error('personal.EPER_gestion')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>

                </div>
            </div>

            <button type="button" id="submit-btn" class="btn btn-primary">Guardar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        document.getElementById('submit-btn').addEventListener('click', function () {
            swal({
                title: "¿Estás seguro?",
                text: "Verifica que toda la información es correcta antes de enviar.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willSubmit) => {
                if (willSubmit) {
                    document.getElementById('create-establecimiento-form').submit();
                }
            });
        });
    </script>
@endsection
