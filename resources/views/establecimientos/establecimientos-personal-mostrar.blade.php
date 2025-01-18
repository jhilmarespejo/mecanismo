@extends('layouts.app')
@section('title', 'Información sobre el personal')

@section('content')

<div class="container mt-3 p-4 bg-white">
    @include('layouts.breadcrumbs', $breadcrumbs)
    <h1 class="mb-3 text-center text-secondary">Información sobre el personal</h1>
    <h2 class="mb-2 text-center text-primary">{{ $infoPersonal['EST_nombre'] }}</h2>
    <h5 class="mb-2 text-center text-secondary">{{ $infoPersonal['TES_tipo'] }}</h5>

    <div class="row m-4 p-3 " style="background-color: #cfe2ff;">
        <label for="colFormLabelLg" class="col-sm-8 col-form-label col-form-label-lg">Información de la gestión.</label>
        <div class="col-sm-4">
            <select class="form-select form-select-lg" id="anyo_consulta" name="anyo_consulta">
                <option value="2024" {{ ( $infoPersonal['EPER_gestion'] == '2024' || $gestion == '2024') ? 'selected' : '' }}>2024</option>
                <option value="2025" {{ ( $infoPersonal['EPER_gestion'] == '2025' || $gestion == '2025') ? 'selected' : '' }}>2025</option>
                <option value="2026" {{ ( $infoPersonal['EPER_gestion'] == '2026' || $gestion == '2026') ? 'selected' : '' }}>2026</option>
                <option value="2027" {{ ( $infoPersonal['EPER_gestion'] == '2027' || $gestion == '2027') ? 'selected' : '' }}>2027</option>
                <option value="2028" {{ ( $infoPersonal['EPER_gestion'] == '2028' || $gestion == '2028') ? 'selected' : '' }}>2028</option>
            </select>
        </div>
    </div>

    <form id="actualizar-personal-form" method="POST">
        <input type="hidden" name="personal[FK_EST_id]" value="{{ $infoPersonal['EST_id'] }}">
        <div class="card mb-3">
            <div class="card-body row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre del Responsable o Comandante</label>
                    <input disabled type="text" class="form-control" id="EPER_nombre_responsable" name="personal[EPER_nombre_responsable]" value="{{ $infoPersonal['EPER_nombre_responsable'] }}">
                    <small class="text-danger" id="error-EPER_nombre_responsable"></small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Profesión o Grado</label>
                    <input disabled type="text" class="form-control" id="EPER_grado_profesion" name="personal[EPER_grado_profesion]" value="{{ $infoPersonal['EPER_grado_profesion'] }}">
                    <small class="text-danger" id="error-EPER_grado_profesion"></small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de incorporación</label>
                    <input disabled type="text" class="form-control" id="EPER_fecha_incorporacion" name="personal[EPER_fecha_incorporacion]" value="{{ $infoPersonal['EPER_fecha_incorporacion'] }}">
                    <small class="text-danger" id="error-EPER_fecha_incorporacion"></small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Experiencia previa</label>
                    <input disabled type="text" class="form-control" id="EPER_experiencia" name="personal[EPER_experiencia]" value="{{ $infoPersonal['EPER_experiencia'] }}">
                    <small class="text-danger" id="error-EPER_experiencia"></small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input disabled type="text" class="form-control" id="EPER_telefono" name="personal[EPER_telefono]" value="{{ $infoPersonal['EPER_telefono'] }}">
                    <small class="text-danger" id="error-EPER_telefono"></small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input disabled type="text" class="form-control " id="EPER_email" name="personal[EPER_email]" value="{{ $infoPersonal['EPER_email'] }}">
                    <small class="text-danger" id="error-EPER_email"></small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Gestión</label>
                    <input disabled type="text" class="form-control" id="EPER_gestion" name="personal[EPER_gestion]" value="{{ $infoPersonal['EPER_gestion'] }}">
                    <small class="text-danger" id="error-EPER_gestion"></small>
                </div>
            </div>
        </div>
        
        {{-- <button type="button" class="btn btn-primary" id="editBtn">Editar la información actual</button> --}}
        @if($infoPersonal['EPER_gestion'] == date('Y'))
            <button type="button" class="btn btn-primary" id="editBtn">Editar la información actual</button>
        @endif

        <button type="button" class="btn btn-success" id="updateBtn" style="display: none;">Actualizar Información</button>
    </form>

    <script>
        $(document).ready(function() {
            $('#editBtn').click(function() {
                $('input, select').prop('disabled', false);
                $('#editBtn').hide();
                $('#updateBtn').show();
            });

            $('#updateBtn').click(function() {
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
                        let formData = $('#actualizar-personal-form').serialize();
                        $.ajax({
                            method: 'POST',
                            async: true,
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                            url: "{{ route('establecimientos.personalActualizar') }}",
                            data: formData,
                            success: function(response) {
                                Swal.fire(
                                    'Actualizado!',
                                    'La información ha sido actualizada.',
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                });
                                $('#updateBtn').hide();
                                $('#editBtn').show();
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
                                            let inputKey = key.replace('personal.', '');
                                            $('#' + inputKey).addClass('is-invalid');//
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
                window.location.href = "{{ route('establecimientos.personalMostrar', ['EST_id' => $infoPersonal['EST_id']]) }}" + "?gestion=" + selectedYear;
            });
        });
    </script>

</div>
@endsection
