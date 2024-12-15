
@extends('layouts.app')
@section('title', 'indicadores')

@section('content')

<div class="container">

    {{-- <a href="{{ route('indicadores.create') }}" class="btn btn-primary mb-3">Crear Nuevo Indicador</a> --}}
    <h1 class="text-center">Módulo de Indicadores</h1>
    <h3 class="text-center">Actualización de datos</h3>

    <div class="d-flex align-items-start border">
        <div class="col-4 border-end overflow-auto" id="v-pills-tab" style="max-height: 550px; direction: rtl;">
            <div class="nav flex-column nav-pills me-3" role="tablist" aria-orientation="vertical">
                <h3 class="text-center p-2 text-primary my-2">Categorías</h3>
                @php $i=0; @endphp
                @foreach ($categorias as $indic => $indicador)
                    <button class="border-bottom border-top border-star text-start nav-link mb-1 box-shadow " id="v-pills-{{$i}}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{$i}}" type="button" role="tab" aria-controls="v-pills-{{$i}}" aria-selected="false">
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

                    <div class="tab-pane fade " id="v-pills-{{$j}}" role="tabpanel" aria-labelledby="v-pills-{{$j}}-tab">
                        <h3 class="text-center p-2 text-primary">Indicadores</h3>
                        <div class="accordion" id="accordionIndicadores">
                            @php $a=0; @endphp
                            @foreach ($indicadores as $indic => $indicador)
                                <h2 class="accordion-header mt-1 rounded" id="heading_{{$a}}_{{$j}}">
                                    <button class="accordion-button bg-info bg-gradient text-dark border p-2 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{$a}}_{{$j}}" aria-expanded="false" aria-controls="collapse_{{$a}}_{{$j}}">
                                        <span class="bg-light rounded-circle p-3 box-shadow"><i class="bi bi-bar-chart-line-fill"></i></span>
                                        <span class="ms-3"><b>{{$indicador[0]['IND_numero']}}</b> {{$indic}}</span>
                                    </button>
                                </h2>

                                    <div id="collapse_{{$a}}_{{$j}}" class="accordion-collapse collapse bg-light border-start border-top" aria-labelledby="heading_{{$a}}_{{$j}}" data-bs-parent="#accordionIndicadores">
                                        <div class="accordion-body">

                                            {{-- mensaje --}}
                                            <div class="alert alert-info box-shadow d-flex align-items-center">
                                                <span class="rounded rounded-circle bg-light p-0 m-1 me-3"><i class="bi bi-chat-left-text fs-3"></i></span>
                                                <small class="text-muted mx-2"><i>Fuente de informacion:</i></small>{{$indicador[0]['IND_fuente_informacion']}}
                                            </div>
                                            <div class="ms-3 p-1 border-start">
                                                @foreach ($indicador as $p => $pregunta)
                                                    <form method="POST" enctype="multipart/form-data" id="formularioIndicadores_{{ $pregunta['IND_id'] }}" class="formularioIndicadores">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="options_{{$pregunta['IND_id']}}" class="form-label">
                                                                <b class="text-muted">{{$pregunta['IND_codigo_pregunta']}}</b>. {{$pregunta['IND_pregunta']}}
                                                            </label>

                                                            @php
                                                            // dump($pregunta['HIN_respuesta']);
                                                                $opciones = json_decode($pregunta['IND_opciones'], true);
                                                                if ( $pregunta['IND_tipo_repuesta'] == 'Lista desplegable' ) {
                                                                    echo '<div class="form-check">';
                                                                    echo '<label>Opciones:</label>';
                                                                    foreach ($opciones as $key => $value) {
                                                                        echo '<div class="form-check">';

                                                                        echo '<input class="form-check-input" type="radio" name="respuesta" id="option' . $key . '" value="' . $value . '"';
                                                                        echo ($pregunta['HIN_respuesta'] == $value) ? ' checked' : '';
                                                                        echo '>';

                                                                        echo '<label class="form-check-label" for="option' . $key . '">' . $value . '</label>';
                                                                        echo '</div>';
                                                                    }
                                                                    echo '</div>';
                                                                } elseif ($pregunta['IND_tipo_repuesta'] == 'Casilla verificacion') {
                                                                    echo '<div class="form-check">';
                                                                    echo '<label class="form-check-label">Opciones:</label>';
                                                                    foreach ($opciones as $key => $value) {
                                                                        echo '<div class="form-check">';
                                                                        echo '<input class="form-check-input" type="checkbox" name="respuesta[]" id="option' . $key . '" value="' . $value . '">';
                                                                        echo '<label class="form-check-label" for="option' . $key . '">' . $value . '</label>';
                                                                        echo '</div>';
                                                                    }
                                                                    echo '</div>';
                                                                } elseif ($pregunta['IND_tipo_repuesta'] == 'Texto') {
                                                                    echo '<input type="text" name="respuesta" class="form-control box-shadow" id="respuesta_' . $pregunta['IND_id'] . '" value="' . (isset($pregunta['HIN_respuesta']) ? $pregunta['HIN_respuesta'] : '') . '">';
                                                                } elseif ($pregunta['IND_tipo_repuesta'] == 'Numero') {
                                                                    echo '<input type="number" name="respuesta" class="form-control box-shadow" id="respuesta_' . $pregunta['IND_id'] . '" value="' . (isset($pregunta['HIN_respuesta']) ? $pregunta['HIN_respuesta'] : '') . '">';
                                                                }
                                                            @endphp
                                                        </div>

                                                        <input type="hidden" name="FK_IND_id" value="{{ $pregunta['IND_id'] }}">
                                                        <button type="button" class="btn btn-primary btn-sm guardarIndicadores" data-id="{{ $pregunta['IND_id'] }}">Actualizar</button>
                                                        <div id="mensajeConfirmacion_{{ $pregunta['IND_id'] }}" class="mt-3 p-1 alert alert-success d-none"></div>
                                                        <hr>
                                                    </form>
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


@endsection

@section('js')
<script>
     $(document).ready(function() {
        $('.guardarIndicadores').click(function() {
            var id = $(this).data('id');
            var formData = $('#formularioIndicadores_' + id).serialize();
            console.log(formData);

            $.ajax({
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: '{{ route("guardar.indicadores") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#mensajeConfirmacion_' + id).removeClass('d-none').html('Datos guardados correctamente.');
                },
                error: function(xhr, status, error) {
                    console.error('Error al guardar los datos:', error);
                    $('#mensajeConfirmacion_' + id).removeClass('d-none').addClass('alert-danger').html('Error al guardar los datos.');
                }
            });
        });
    });
</script>

@endsection
